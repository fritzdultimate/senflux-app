<?php

namespace App\Services\MarketData;

use App\Enums\TradeActivitySource;
use App\Models\Formation;
use App\Models\FormationTradeActivity;

class FormationTradeActivitySyncService {
    public function __construct(private SolanaRpcService $rpc) {}

    public function syncOne(Formation $formation): int {
        if (!$formation->pair_address) {
            return 0;
        }

        $signatures = $this->rpc->fetchRecentSignatures($formation->pair_address, 15);
        $new = 0;

        // dd($signatures);

        foreach ($signatures as $sig) {
            $created = FormationTradeActivity::firstOrCreate(
                ['tx_signature' => $sig['signature']],
                [
                    'formation_id' => $formation->id,
                    'slot' => $sig['slot'] ?? null,
                    'block_time' => isset($sig['blockTime']) ? \Carbon\Carbon::createFromTimestamp($sig['blockTime']) : null,
                    'source' => TradeActivitySource::MARKET_POOL->value,
                    'failed' => (bool) ($sig['err'] ?? false),
                ],
            );

            if ($created->wasRecentlyCreated) {
                $new++;
            }
        }

        return $new;
    }

    public function syncAll(): int {
        $total = 0;

        Formation::active()->whereNotNull('pair_address')->chunkById(50, function ($formations) use (&$total) {
            foreach ($formations as $formation) {
                $total += $this->syncOne($formation);
            }
        });

        return $total;
    }
}