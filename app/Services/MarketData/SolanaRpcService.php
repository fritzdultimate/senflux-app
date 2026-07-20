<?php
// app/Services/MarketData/SolanaRpcService.php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SolanaRpcService {
    /**
     * Public mainnet RPC — free, but rate-limited and explicitly not
     * meant for heavy production traffic per Solana Foundation's own
     * guidance. Fine for polling a handful of pool addresses every few
     * minutes; if this ever needs to scale to many formations polled
     * frequently, swap this URL for a dedicated RPC provider (Helius,
     * QuickNode, Triton) — same method signature, just a different
     * endpoint + optional API key in the URL.
     */
    private const RPC_URL = 'https://api.mainnet-beta.solana.com';

    /**
     * Real, signed Solana transaction signatures for this account,
     * newest first. Each one is independently verifiable at
     * solscan.io/tx/{signature} or explorer.solana.com/tx/{signature}.
     */
    public function fetchRecentSignatures(string $accountAddress, int $limit = 10): array {
        $response = Http::timeout(10)
            ->retry(2, 500)
            ->post(self::RPC_URL, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'getSignaturesForAddress',
                'params' => [$accountAddress, ['limit' => $limit]],
            ]);

        if ($response->failed() || $response->json('error')) {
            Log::warning('Solana RPC signature fetch failed', ['account' => $accountAddress, 'body' => $response->body()]);
            return [];
        }

        return $response->json('result') ?? [];
    }

    public function fetchTransactionDetail(string $signature): ?array {
        $response = Http::timeout(10)->post(self::RPC_URL, [
            'jsonrpc' => '2.0', 'id' => 1,
            'method' => 'getTransaction',
            'params' => [$signature, ['encoding' => 'jsonParsed', 'maxSupportedTransactionVersion' => 0]],
        ]);

        return $response->successful() ? $response->json('result') : null;
    }

    /**
 * Extracts swap-like info from a raw getTransaction (jsonParsed) result.
 * No Helius-style program-ID classification here — this infers "swap"
 * purely from balance deltas: if the fee payer's SOL/token balances moved
 * in opposite directions (lost one asset, gained another) in the same tx,
 * treat it as a swap. Returns null if it doesn't look like one.
 */
    public function extractSwapInfo1(array $tx, string $trackedMint): ?array {
        $meta = $tx['meta'] ?? null;
        if (!$meta || ($meta['err'] ?? null) !== null) {
            return null; // failed tx
        }

        $accountKeys = $tx['transaction']['message']['accountKeys'] ?? [];
        if (empty($accountKeys)) {
            return null;
        }

        // feePayer is always accountKeys[0]
        $feePayerEntry = $accountKeys[0];
        $feePayer = is_array($feePayerEntry) ? ($feePayerEntry['pubkey'] ?? null) : $feePayerEntry;
        if (!$feePayer) {
            return null;
        }

        $pre = collect($meta['preTokenBalances'] ?? []);
        $post = collect($meta['postTokenBalances'] ?? []);

        // Build owner+mint => amount maps, keyed by accountIndex since that's
        // the only stable join key between pre/post arrays.
        $preByIndex = $pre->keyBy('accountIndex');
        $postByIndex = $post->keyBy('accountIndex');

        $deltas = []; // mint => signed float delta, for the fee payer's own token accounts only

        foreach ($postByIndex as $idx => $postEntry) {
            if (($postEntry['owner'] ?? null) !== $feePayer) {
                continue;
            }

            $mint = $postEntry['mint'] ?? null;
            $postAmt = (float) ($postEntry['uiTokenAmount']['uiAmountString'] ?? $postEntry['uiTokenAmount']['uiAmount'] ?? 0);
            $preAmt = (float) ($preByIndex[$idx]['uiTokenAmount']['uiAmountString'] ?? $preByIndex[$idx]['uiTokenAmount']['uiAmount'] ?? 0);

            if ($mint) {
                $deltas[$mint] = ($deltas[$mint] ?? 0) + ($postAmt - $preAmt);
            }
        }

        // Also account for tokens the fee payer's wallet held pre-tx but has zero of post-tx
        // (account closed / balance emptied) — preByIndex entries with no matching post entry.
        foreach ($preByIndex as $idx => $preEntry) {
            if (($preEntry['owner'] ?? null) !== $feePayer || isset($postByIndex[$idx])) {
                continue;
            }
            $mint = $preEntry['mint'] ?? null;
            $preAmt = (float) ($preEntry['uiTokenAmount']['uiAmountString'] ?? $preEntry['uiTokenAmount']['uiAmount'] ?? 0);
            if ($mint) {
                $deltas[$mint] = ($deltas[$mint] ?? 0) - $preAmt;
            }
        }

        // Include native SOL movement (lamports) as a pseudo-mint, since many
        // swaps are TOKEN <-> SOL rather than TOKEN <-> TOKEN.
        $preBalances = $meta['preBalances'] ?? [];
        $postBalances = $meta['postBalances'] ?? [];
        if (isset($preBalances[0], $postBalances[0])) {
            $solDelta = ($postBalances[0] - $preBalances[0]) / 1e9;
            // Ignore dust-level delta (just fees), only count if meaningfully large
            if (abs($solDelta) > 0.0001) {
                $deltas['SOL'] = ($deltas['SOL'] ?? 0) + $solDelta;
            }
        }

        // Need at least one asset that decreased and one that increased to call it a swap
        $gained = collect($deltas)->filter(fn ($v) => $v > 0);
        $lost = collect($deltas)->filter(fn ($v) => $v < 0);

        if ($gained->isEmpty() || $lost->isEmpty()) {
            // dump('here 1', $deltas, $feePayer);
            return null;
        }

        // Primary = the asset with the largest positive delta (what the trader received)
        $primaryMint = $gained->keys()->sortByDesc(fn ($m) => $gained[$m])->first();

        $gained = collect($deltas)->filter(fn ($v) => $v > 0);
        $lost = collect($deltas)->filter(fn ($v) => $v < 0);

        if ($gained->isEmpty() || $lost->isEmpty()) {
            // dump('here 2');
            return null;
        }

        $gainedMint = $gained->keys()->sortByDesc(fn ($m) => $gained[$m])->first();
        $lostMint = $lost->keys()->sortBy(fn ($m) => $lost[$m])->first(); // most negative

        // Only classify if the tracked mint is actually one side of this trade
        if ($gainedMint !== $trackedMint && $lostMint !== $trackedMint) {
            // dump('here 3');
            // dd('swap debug', [
            //     'feePayer' => $feePayer,
            //     'trackedMint' => $trackedMint,
            //     'deltas' => $deltas,
            // ]);
            return null;
        }

        $direction = $gainedMint === $trackedMint ? 'buy' : 'sell';
        // $tokenAmount = $direction === 'buy' ? $gained[$gainedMint] : abs($lost[$lostMint]);
        $tokenAmount = round($direction === 'buy' ? $gained[$gainedMint] : abs($lost[$lostMint]), 9);
        $quoteMint = $direction === 'buy' ? $lostMint : $gainedMint;
        $quoteAmount = $direction === 'buy' ? abs($lost[$lostMint]) : $gained[$gainedMint];

        return [
            'type' => $direction,        // 'buy' or 'sell'
            'wallet' => $feePayer,
            'token_amount' => (float) $tokenAmount,   // qty of the tracked token, not USD
            'mint' => $trackedMint,
            'quote_mint' => $quoteMint,               // SOL, USDC, etc. — the other side
            'quote_amount' => (float) $quoteAmount,   // qty of the quote asset spent/received
            'description' => null,
        ];
    }


    public function extractSwapInfo(array $tx, string $trackedMint): ?array {
        $meta = $tx['meta'] ?? null;
        if (!$meta || ($meta['err'] ?? null) !== null) {
            return null; // failed tx
        }

        $accountKeys = $tx['transaction']['message']['accountKeys'] ?? [];
        if (empty($accountKeys)) {
            return null;
        }

        $pre = collect($meta['preTokenBalances'] ?? []);
        $post = collect($meta['postTokenBalances'] ?? []);
        $preByIndex = $pre->keyBy('accountIndex');
        $postByIndex = $post->keyBy('accountIndex');

        // Build deltas per-owner (not just feePayer), since the fee payer
        // isn't always the actual trader.
        $deltasByOwner = []; // [owner => [mint => delta]]

        foreach ($postByIndex as $idx => $postEntry) {
            $owner = $postEntry['owner'] ?? null;
            $mint = $postEntry['mint'] ?? null;
            if (!$owner || !$mint) continue;

            $postAmt = (float) ($postEntry['uiTokenAmount']['uiAmountString'] ?? $postEntry['uiTokenAmount']['uiAmount'] ?? 0);
            $preAmt = (float) ($preByIndex[$idx]['uiTokenAmount']['uiAmountString'] ?? $preByIndex[$idx]['uiTokenAmount']['uiAmount'] ?? 0);

            $deltasByOwner[$owner][$mint] = ($deltasByOwner[$owner][$mint] ?? 0) + ($postAmt - $preAmt);
        }

        foreach ($preByIndex as $idx => $preEntry) {
            $owner = $preEntry['owner'] ?? null;
            $mint = $preEntry['mint'] ?? null;
            if (!$owner || !$mint || isset($postByIndex[$idx])) continue;

            $preAmt = (float) ($preEntry['uiTokenAmount']['uiAmountString'] ?? $preEntry['uiTokenAmount']['uiAmount'] ?? 0);
            $deltasByOwner[$owner][$mint] = ($deltasByOwner[$owner][$mint] ?? 0) - $preAmt;
        }

        // Find the owner whose balance actually moved for the tracked mint —
        // that's the real trader, whether or not they're the fee payer.
        $traderWallet = null;
        foreach ($deltasByOwner as $owner => $mintDeltas) {
            if (($mintDeltas[$trackedMint] ?? 0) != 0) {
                $traderWallet = $owner;
                break;
            }
        }

        if (!$traderWallet) {
            return null; // tracked mint didn't move for anyone in this tx
        }

        $deltas = $deltasByOwner[$traderWallet];

        // Native SOL leg: check the trader's own account index, not accountKeys[0]
        $preBalances = $meta['preBalances'] ?? [];
        $postBalances = $meta['postBalances'] ?? [];
        $traderIndex = collect($accountKeys)->search(function ($k) use ($traderWallet) {
            return (is_array($k) ? ($k['pubkey'] ?? null) : $k) === $traderWallet;
        });
        if ($traderIndex !== false && isset($preBalances[$traderIndex], $postBalances[$traderIndex])) {
            $solDelta = ($postBalances[$traderIndex] - $preBalances[$traderIndex]) / 1e9;
            if (abs($solDelta) > 0.0001) {
                $deltas['SOL'] = ($deltas['SOL'] ?? 0) + $solDelta;
            }
        }

        $gained = collect($deltas)->filter(fn ($v) => $v > 0);
        $lost = collect($deltas)->filter(fn ($v) => $v < 0);

        if ($gained->isEmpty() || $lost->isEmpty()) {
            return null;
        }

        $gainedMint = $gained->keys()->sortByDesc(fn ($m) => $gained[$m])->first();
        $lostMint = $lost->keys()->sortBy(fn ($m) => $lost[$m])->first();

        if ($gainedMint !== $trackedMint && $lostMint !== $trackedMint) {
            return null;
        }

        $direction = $gainedMint === $trackedMint ? 'buy' : 'sell';
        $tokenAmount = round($direction === 'buy' ? $gained[$gainedMint] : abs($lost[$lostMint]), 9);
        $quoteMint = $direction === 'buy' ? $lostMint : $gainedMint;
        $quoteAmount = round($direction === 'buy' ? abs($lost[$lostMint]) : $gained[$gainedMint], 9);

        return [
            'type' => $direction,
            'wallet' => $traderWallet,
            'token_amount' => $tokenAmount,
            'mint' => $trackedMint,
            'quote_mint' => $quoteMint,
            'quote_amount' => $quoteAmount,
            'description' => null,
        ];
    }
}