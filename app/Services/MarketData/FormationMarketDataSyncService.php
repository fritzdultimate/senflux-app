<?php

namespace App\Services\MarketData;

use App\Models\Formation;
use App\Models\FormationLiquiditySnapshot;
use Illuminate\Support\Facades\Log;

class FormationMarketDataSyncService
{
    public function __construct(
        private DexScreenerService $dexScreener,
        private LiquidityMigrationScorer $migrationScorer,
    ) {}

    public function syncAll(): int
    {
        $synced = 0;

        Formation::active()
            ->whereNotNull('mint_address')
            ->chunkById(50, function ($formations) use (&$synced) {
                foreach ($formations as $formation) {
                    if ($this->syncOne($formation)) {
                        $synced++;
                    }
                }
            });

        return $synced;
    }

    public function syncOne(Formation $formation): bool
    {
        $data = $this->dexScreener->summarize($formation->mint_address);

        if (!$data) {
            Log::warning('Formation market data sync failed', ['formation_id' => $formation->id, 'mint' => $formation->mint_address]);
            return false;
        }

        $formation->update([
            'dex' => $data['dex'],
            'pair_address' => $data['pair_address'],
            'pair_url' => $data['pair_url'],
            'price_usd' => $data['price_usd'],
            'liquidity_usd' => $data['liquidity_usd'],
            'volume_24h' => $data['volume_24h'],
            'buys_24h' => $data['buys_24h'],
            'sells_24h' => $data['sells_24h'],
            'price_change_24h' => $data['price_change_24h'],
            'market_data_synced_at' => now(),
        ]);

        FormationLiquiditySnapshot::create([
            'formation_id' => $formation->id,
            'liquidity_usd' => $data['liquidity_usd'],
        ]);

        $score = $this->migrationScorer->score($formation);

        dd($score);

        if ($score !== null) {
            $formation->update(['liquidity_migration' => $score]);
        }

        return true;
    }
}