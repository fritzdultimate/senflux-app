<?php

namespace App\Services\MarketData;

use App\Models\Formation;
use Illuminate\Support\Facades\Log;

class BirdeyeSyncService {
    public function __construct(
        private BirdeyeService $birdeye,
        private FormationScoringService $scorer,
    ) {}

    public function syncNext(int $batchSize = 1): array {
        $synced = 0;
        $errors = 0;
        $formationId = null;
        $formationName = null;

        $formations = Formation::query()
            ->whereNotNull('mint_address')
            ->orderByRaw('birdeye_synced_at IS NOT NULL, birdeye_synced_at ASC')
            ->limit($batchSize)
            ->get();

        foreach ($formations as $formation) {
            try {
                $birdeyeData = $this->birdeye->traderStats($formation->mint_address);

                $walletQuality = $this->scorer->walletParticipationScore($birdeyeData);
                $capitalConcentration = $this->scorer->capitalConcentrationScore($birdeyeData);

                $formation->update([
                    'active_wallets' => $birdeyeData['active_wallets'] ?? $formation->active_wallets,
                    'holders' => $birdeyeData['holders'] ?? $formation->holders,
                    'unique_wallets_24h' => $birdeyeData['unique_wallets_24h'] ?? null,
                    'unique_wallets_24h_change_pct' => $birdeyeData['unique_wallets_24h_change_pct'] ?? null,
                    'volume_buy_24h_usd' => $birdeyeData['volume_buy_24h_usd'] ?? null,
                    'volume_sell_24h_usd' => $birdeyeData['volume_sell_24h_usd'] ?? null,
                    'wallet_quality' => $walletQuality,
                    'capital_concentration' => $capitalConcentration,
                    'birdeye_synced_at' => now(),
                ]);

                $synced++;
                $formationId = $formation->id;
                $formation->token_name;
            } catch (\Throwable $e) {
                $errors++;
                Log::warning('BirdeyeSyncService: formation sync failed', [
                    'formation_id' => $formation->id,
                    'mint' => $formation->mint_address,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return compact('synced', 'errors', 'formationName', 'formationId');
    }
}