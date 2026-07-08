<?php

namespace App\Services\MarketData;

use App\Enums\FormationEventType;
use App\Enums\FormationState;
use App\Models\Formation;
use App\Models\FormationWatchlistItem;
use App\Services\FormationEventLogger;

class FormationAutoDetectionService {
    /** Minimum real liquidity before we consider a token "detected" at all — filters out dead/rug pools. */
    private const MIN_DETECTABLE_LIQUIDITY_USD = 5000;

    /** A score drop this large auto-flips state to WEAKENING regardless of band, so a formation doesn't sit labeled ACTIVE while visibly dying. */
    private const WEAKENING_SCORE_DROP_THRESHOLD = 15;

    /** % liquidity growth since last sync that's worth a ticker event. */
    private const LIQUIDITY_SURGE_THRESHOLD_PCT = 10;

    public function __construct(
        private DexScreenerService $dexScreener,
        private FormationScoringService $scorer,
        private LiquidityMigrationScorer $migrationScorer,
        private FormationEventLogger $eventLogger,
    ) {}

    public function runCycle(): array {
        $created = 0;
        $updated = 0;

        FormationWatchlistItem::active()->chunkById(50, function ($items) use (&$created, &$updated) {
            foreach ($items as $item) {
                $data = $this->dexScreener->summarize($item->mint_address);

                if (!$data) {
                    continue;
                }

                if ($item->formation_id === null) {
                    if ($this->tryDetect($item, $data)) {
                        $created++;
                    }
                    continue;
                }

                if ($this->updateFormation($item->formation, $data)) {
                    $updated++;
                }
            }
        });

        return compact('created', 'updated');
    }

    private function tryDetect(FormationWatchlistItem $item, array $data): bool {
        if ((float) $data['liquidity_usd'] < self::MIN_DETECTABLE_LIQUIDITY_USD) {
            return false;
        }

        $score = $this->scorer->score($data, null);

        $formation = Formation::create([
            'token_name' => $data['name'] ?? $item->token_symbol,
            'token_symbol' => $data['symbol'] ?? $item->token_symbol,
            'ecosystem' => $item->ecosystem,
            'sector' => $item->sector,
            'state' => $this->stateFromScore($score),
            'score' => $score,
            'confidence' => $score >= 60 ? 'High' : ($score >= 35 ? 'Moderate' : 'Low'),
            'capital_concentration' => 50, 
            'wallet_quality' => 50, 
            'participation_growth' => 50, // neutral placeholders until Birdeye
            'liquidity_migration' => 50,
            'detected_at' => now(), 
            'state_changed_at' => now(),
            'is_active' => true, 
            'auto_managed' => true,
            'mint_address' => $item->mint_address,
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

            'price_change_5m' => $data['price_change_5m'],
            'price_change_1h' => $data['price_change_1h'],
            'price_change_6h' => $data['price_change_6h'],
            'volume_5m' => $data['volume_5m'],
            'volume_1h' => $data['volume_1h'],
            'volume_6h' => $data['volume_6h'],
            'buys_5m' => $data['buys_5m'],
            'sells_5m' => $data['sells_5m'],
            'buys_1h' => $data['buys_1h'],
            'sells_1h' => $data['sells_1h'],
            'buys_6h' => $data['buys_6h'],
            'sells_6h' => $data['sells_6h'],
            'fdv' => $data['fdv'],
            'market_cap' => $data['market_cap'],
            'image_url' => $data['image_url'],
            'header' => $data['header'],
            'open_graph' => $data['open_graph'],
        ]);

        $item->update(['formation_id' => $formation->id]);

        return true;
    }

    private function updateFormation(Formation $formation, array $data): bool {
        if (!$formation->auto_managed) {
            return false; // hands-off — admin owns this one
        }

        $previousLiquidity = (float) $formation->liquidity_usd;
        $previousScore = (int) $formation->score;

        $liquidityMigration = $this->migrationScorer->score($formation) ?? $formation->liquidity_migration;
        $score = $this->scorer->score($data, $liquidityMigration);

        $newState = $this->stateFromScore($score);
        if ($previousScore - $score >= self::WEAKENING_SCORE_DROP_THRESHOLD) {
            $newState = FormationState::WEAKENING;
        }

        $formation->update([
            'previous_score' => $previousScore,
            'score' => $score,
            'state' => $newState,
            'confidence' => $score >= 60 ? 'High' : ($score >= 35 ? 'Moderate' : 'Low'),
            'liquidity_migration' => $liquidityMigration,
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

            'price_change_5m' => $data['price_change_5m'],
            'price_change_1h' => $data['price_change_1h'],
            'price_change_6h' => $data['price_change_6h'],
            'volume_5m' => $data['volume_5m'],
            'volume_1h' => $data['volume_1h'],
            'volume_6h' => $data['volume_6h'],
            'buys_5m' => $data['buys_5m'],
            'sells_5m' => $data['sells_5m'],
            'buys_1h' => $data['buys_1h'],
            'sells_1h' => $data['sells_1h'],
            'buys_6h' => $data['buys_6h'],
            'sells_6h' => $data['sells_6h'],
            'fdv' => $data['fdv'],
            'market_cap' => $data['market_cap'],
            'image_url' => $data['image_url'],
            'header' => $data['header'],
            'open_graph' => $data['open_graph'],
        ]);

       // update birdeye value 
        $birdeyeData = app(BirdeyeService::class)->traderStats($formation->mint_address);
        // dd($birdeyeData['active_wallets']);
        $formation->update([
            'active_wallets' => $birdeyeData['active_wallets'],
            // 'holders' => $birdeyeData['holders'] ?? $formation->holders,
            // 'unique_wallets_24h' => $birdeyeData['unique_wallets_24h'] ?? null,
            // 'unique_wallets_24h_change_pct' => $birdeyeData['unique_wallets_24h_change_pct'] ?? null,
            // 'volume_buy_24h_usd' => $birdeyeData['volume_buy_24h_usd'] ?? null,
            // 'volume_sell_24h_usd' => $birdeyeData['volume_sell_24h_usd'] ?? null,
            // 'birdeye_synced_at' => $birdeyeData ? now() : null,
        ]);

        // dd($birdEye);

        if ($newState === FormationState::WEAKENING && $formation->getOriginal('state') !== FormationState::WEAKENING->value) {
            $this->eventLogger->log($formation, FormationEventType::EXPOSURE_REDUCED, "Exposure reduction initiated — {$formation->token_symbol}");
        }

        if ($previousLiquidity > 0) {
            $liquidityGrowthPct = (($data['liquidity_usd'] - $previousLiquidity) / $previousLiquidity) * 100;
            if ($liquidityGrowthPct >= self::LIQUIDITY_SURGE_THRESHOLD_PCT) {
                $this->eventLogger->log($formation, FormationEventType::LIQUIDITY_INCREASING, "Liquidity up " . round($liquidityGrowthPct) . "% — {$formation->token_symbol}");
            }
        }

        return true;
    }

    /**
     * Band thresholds are our own calibration, not derived from anything
     * authoritative — same caveat as FormationScoringService. MATURE is
     * intentionally never auto-assigned here; "broader participation,
     * monitoring for deterioration" is a judgment call the docs describe
     * as needing human read on market context, not a pure score band.
     */
    private function stateFromScore(int $score): FormationState {
        return match (true) {
            $score >= 65 => FormationState::ACTIVE,
            $score >= 40 => FormationState::BUILDING,
            $score >= 20 => FormationState::EARLY,
            default => FormationState::IDLE,
        };
    }
}