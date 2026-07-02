<?php

namespace Database\Seeders;

use App\Enums\FormationState;
use App\Models\Formation;
use Illuminate\Database\Seeder;

class FormationSeeder extends Seeder
{
    public function run(): void
    {
        $formations = [
            ['WIF', 'dogwifhat', FormationState::ACTIVE, 92, 'High', 82, 71, 91, 85, now()->subHours(3)],
            ['BONK', 'bonk', FormationState::BUILDING, 72, 'High', 68, 74, 79, 66, now()->subMinutes(45)],
            ['POPCAT', 'popcat', FormationState::EARLY, 58, 'Moderate', 41, 38, 55, 60, now()->subMinutes(18)],
            ['JTO', 'jito', FormationState::WEAKENING, 34, 'Low', 22, 19, 30, 41, now()->subHours(6)],
            ['PYTH', 'Pyth Network', FormationState::IDLE, 21, 'Low', 12, 8, 15, 20, now()->subHours(12)],
        ];

        foreach ($formations as [$symbol, $name, $state, $score, $confidence, $cap, $liq, $part, $wallet, $detected]) {
            Formation::create([
                'token_symbol' => $symbol,
                'token_name' => $name,
                'ecosystem' => 'Solana',
                'state' => $state,
                'score' => $score,
                'confidence' => $confidence,
                'capital_concentration' => $cap,
                'liquidity_migration' => $liq,
                'participation_growth' => $part,
                'wallet_quality' => $wallet,
                'detected_at' => $detected,
                'state_changed_at' => $detected,
                'is_active' => true,
            ]);
        }
    }
}