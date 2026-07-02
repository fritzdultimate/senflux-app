<?php

namespace App\Services\MarketData;

use App\Models\Formation;
use App\Models\FormationLiquiditySnapshot;

class LiquidityMigrationScorer {
 
    public function score(Formation $formation): ?int {
        $baseline = FormationLiquiditySnapshot::where('formation_id', $formation->id)
            ->where('created_at', '<=', now()->subHours(20))
            ->orderByDesc('created_at')
            ->first();

        if (!$baseline || (float) $baseline->liquidity_usd <= 0) {
            return null;
        }

        $past = (float) $baseline->liquidity_usd;
        $current = (float) $formation->liquidity_usd;

        $pctChange = (($current - $past) / $past) * 100;

        return (int) max(0, min(100, round(50 + ($pctChange / 2))));
    }
}