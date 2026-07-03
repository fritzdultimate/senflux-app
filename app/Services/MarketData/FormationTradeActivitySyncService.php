<?php

namespace App\Services\MarketData;

use App\Enums\TradeActivitySource;
use App\Models\Formation;
use App\Models\FormationTradeActivity;

class FormationTradeActivitySyncService
{
    public function __construct(
        private SolanaRpcService $rpc,
        private HeliusService $helius,
    ) {}

    public function syncOne(Formation $formation): int {
        if (!$formation->pair_address) {
            return 0;
        }

        $signatures = $this->rpc->fetchRecentSignatures($formation->pair_address, 15);
        $new = 0;

        // Only fetch signatures not already stored — avoids re-parsing
        // (and re-billing Helius credits for) the same transactions
        // every 5-minute cycle.
        $newSignatures = collect($signatures)
            ->pluck('signature')
            ->reject(fn ($sig) => FormationTradeActivity::where('tx_signature', $sig)->exists())
            ->values()
            ->all();

        if (empty($newSignatures)) {
            return 0;
        }

        $parsedBatch = $this->helius->isConfigured()
            ? collect($this->helius->parseTransactions($newSignatures))->keyBy('signature')
            : collect();

        foreach ($signatures as $sig) {
            if (!in_array($sig['signature'], $newSignatures, true)) {
                continue;
            }

            $parsed = $parsedBatch->get($sig['signature']);
            $swap = $parsed ? $this->helius->extractSwapInfo($parsed) : null;

            FormationTradeActivity::create([
                'formation_id' => $formation->id,
                'tx_signature' => $sig['signature'],
                'slot' => $sig['slot'] ?? null,
                'block_time' => isset($sig['blockTime']) ? \Carbon\Carbon::createFromTimestamp($sig['blockTime']) : null,
                'source' => TradeActivitySource::MARKET_POOL->value,
                'failed' => (bool) ($sig['err'] ?? false),
                'type' => $swap['type'] ?? null,
                'token_amount' => $swap['token_amount'] ?? null,
                'trader_wallet' => $swap['wallet'] ?? null,
            ]);

            $new++;
        }

        return $new;
    }

    public function syncAll(): int
    {
        $total = 0;

        Formation::active()->whereNotNull('pair_address')->chunkById(50, function ($formations) use (&$total) {
            foreach ($formations as $formation) {
                $total += $this->syncOne($formation);
            }
        });

        return $total;
    }
}