<?php

namespace App\Services\MarketData;

/**
 * Converts raw DexScreener signals into a 0-100 Formation Score. This
 * weighting (30/30/25/15) is our own proposal, not a market-standard
 * formula — there is no authoritative "formation score" definition to
 * calibrate against. Treat this as a reasonable starting heuristic to
 * tune against real formations over time, not a finished model.
 *
 * Deliberately does NOT use capital_concentration or wallet_quality —
 * those need real holder data (Birdeye), which we don't have yet. This
 * formula only trusts what DexScreener actually measures.
 */
class FormationScoringService {
    public function score(array $marketData, ?float $liquidityMigrationScore = null): int {
        $liquidityScore = $this->liquidityHealthScore((float) $marketData['liquidity_usd']);
        $volumeScore = $this->volumeToLiquidityScore((float) $marketData['volume_24h'], (float) $marketData['liquidity_usd']);
        $pressureScore = $this->buySellPressureScore((int) $marketData['buys_24h'], (int) $marketData['sells_24h']);
        $momentumScore = $this->momentumScore((float) $marketData['price_change_24h']);

        // liquidity_migration substitutes for part of the liquidity weight
        // once real 24h history exists — otherwise fall back to raw size.
        $liquidityComponent = $liquidityMigrationScore ?? $liquidityScore;

        $weighted = ($liquidityComponent * 0.30) + ($volumeScore * 0.30) + ($pressureScore * 0.25) + ($momentumScore * 0.15);

        return (int) max(0, min(100, round($weighted)));
    }

    /** Raw liquidity size, log-scaled — $5k pool and $5M pool shouldn't score linearly. */
    private function liquidityHealthScore(float $liquidityUsd): int {
        if ($liquidityUsd <= 0) return 0;

        $score = (log10($liquidityUsd) - 3) * 20; // $1k≈0, $100k≈50, $10M≈100

        return (int) max(0, min(100, round($score)));
    }

    /** How much the pool is actually being traded relative to its own depth. */
    private function volumeToLiquidityScore(float $volume24h, float $liquidityUsd): int {
        if ($liquidityUsd <= 0) return 0;

        $ratio = $volume24h / $liquidityUsd;

        return (int) max(0, min(100, round($ratio * 40))); // ratio of 2.5x liquidity in daily volume = 100
    }

    /** Buy-heavy vs sell-heavy, 50 = perfectly balanced. */
    private function buySellPressureScore(int $buys, int $sells): int {
        $total = $buys + $sells;
        if ($total === 0) return 50;

        return (int) round(($buys / $total) * 100);
    }

    /** Same damping approach as LiquidityMigrationScorer for consistency. */
    private function momentumScore(float $priceChange24h): int {
        return (int) max(0, min(100, round(50 + ($priceChange24h / 2))));
    }
}