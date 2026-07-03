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
}