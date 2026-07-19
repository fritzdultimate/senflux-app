<?php

namespace App\Services\MarketData;


class FormationScoringService {
    public function score(array $marketData, ?float $liquidityMigrationScore = null): int {
        $liquidityScore = $this->liquidityHealthScore((float) $marketData['liquidity_usd']);
        $volumeScore = $this->volumeToLiquidityScore((float) $marketData['volume_24h'], (float) $marketData['liquidity_usd']);
        $pressureScore = $this->buySellPressureScore((int) $marketData['buys_24h'], (int) $marketData['sells_24h']);
        $momentumScore = $this->priceMomentumScore($marketData);
        $walletScore = $this->walletParticipationScore($marketData);

        // $liquidityComponent = $liquidityMigrationScore ?? $liquidityScore;
        $liquidityComponent = $liquidityScore;

        $weighted = ($liquidityComponent * 0.7)
            + ($volumeScore * 0.15)
            + ($pressureScore * 0.05)
            + ($momentumScore * 0.05)
            + ($walletScore * 0.05);

        // dd($weighted, $marketData, $momentumScore);

        return (int) max(0, min(100, round($weighted)));
    }

    /** $10k≈8, $100k≈70, ~$680k≈100 — recalibrated so real pool sizes can reach the ceiling. */
    private function liquidityHealthScore(float $liquidityUsd): int {
        if ($liquidityUsd <= 0) return 0;

        $score = (log10($liquidityUsd) - 2.5) * 35;

        return (int) max(0, min(100, round($score)));
    }

    /** How much the pool is actually being traded relative to its own depth. */
    private function volumeToLiquidityScore(float $volume24h, float $liquidityUsd): int {
        if ($liquidityUsd <= 0) return 0;

        $ratio = $volume24h / $liquidityUsd;

        return (int) max(0, min(100, round($ratio * 45))); // ratio of ~2.2x liquidity in daily volume = 100
    }

    private function buySellPressureScore(int $buys, int $sells): int {
        $total = $buys + $sells;
        if ($total === 0) return 50;

        return (int) round(($buys / $total) * 100);
    }


    private function priceMomentumScore(array $marketData): int {
        $changes = [
            '5m'  => (float) ($marketData['price_change_5m'] ?? 0),
            '1h'  => (float) ($marketData['price_change_1h'] ?? 0),
            '6h'  => (float) ($marketData['price_change_6h'] ?? 0),
            '24h' => (float) ($marketData['price_change_24h'] ?? 0),
        ];

        // Hard crash override — a severe drop on ANY single timeframe caps
        // momentum low regardless of the others. -45% in the last hour
        // isn't healthy just because 24h still looks flat.
        $worstDrop = min($changes);
        if ($worstDrop <= -40) {
            return 5;
        }
        if ($worstDrop <= -25) {
            // Linear ramp: -25% → 20, -40% → 0
            return (int) max(0, round(20 * (($worstDrop + 40) / 15)));
        }

        // Trend: longer timeframes weighted more — sustained 24h strength
        // means more than a 5-minute spike.
        $weights = ['5m' => 0.10, '1h' => 0.20, '6h' => 0.30, '24h' => 0.40];
        $weightedChange = 0.0;
        foreach ($weights as $tf => $w) {
            $weightedChange += $changes[$tf] * $w;
        }
        $trendScore = max(0, min(100, 50 + ($weightedChange / 1.5)));

        // Volatility: how much the timeframes disagree with each other.
        // +80% at 5m paired with -10% at 24h is a pump, not formation
        // strength — penalize the disagreement itself.
        $mean = array_sum($changes) / count($changes);
        $variance = array_sum(array_map(fn ($c) => ($c - $mean) ** 2, $changes)) / count($changes);
        $stdDev = sqrt($variance);
        $volatilityPenalty = min(35, $stdDev * 0.6);

        return (int) max(0, min(100, round($trendScore - $volatilityPenalty)));
    }

 
    public function walletParticipationScore(array $marketData): int {
        if (!array_key_exists('unique_wallets_24h', $marketData) || $marketData['unique_wallets_24h'] === null) {
            return 50;
        }

        $walletGrowthScore = max(0, min(100, 50 + ((float) ($marketData['unique_wallets_24h_change_pct'] ?? 0) * 2)));

        $buyVol = (float) ($marketData['volume_buy_24h_usd'] ?? 0);
        $sellVol = (float) ($marketData['volume_sell_24h_usd'] ?? 0);
        $totalVol = $buyVol + $sellVol;
        $buyPressureScore = $totalVol > 0 ? (($buyVol / $totalVol) * 100) : 50;

        return (int) round(($walletGrowthScore * 0.6) + ($buyPressureScore * 0.4));
    }


    public function capitalConcentrationScore(?array $birdeyeData): int {
        if (!$birdeyeData || empty($birdeyeData['holders']) || empty($birdeyeData['active_wallets'])) {
            return 50;
        }

        $activityRatio = $birdeyeData['active_wallets'] / max(1, $birdeyeData['holders']);

        return (int) max(0, min(100, round($activityRatio * 200)));
    }
}