<?php

namespace App\Console\Commands;

use App\Models\Formation;
use App\Models\FormationLiquiditySnapshot;
use Illuminate\Console\Command;

class SnapshotFormationLiquidity extends Command {
    protected $signature = 'formation:snapshot';
    protected $description = 'Record a liquidity snapshot for every formation';

    public function handle(): int {
        $count = 0;

        Formation::query()->chunk(200, function ($formations) use (&$count) {
            foreach ($formations as $formation) {
                FormationLiquiditySnapshot::create([
                    'formation_id' => $formation->id,
                    'liquidity_usd' => $formation->liquidity_usd,
                ]);
                $count++;
            }
        });

        $this->info("Snapshotted {$count} formation(s)");
        return self::SUCCESS;
    }
}