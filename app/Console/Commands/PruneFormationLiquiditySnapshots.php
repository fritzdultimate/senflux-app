<?php

namespace App\Console\Commands;

use App\Models\FormationLiquiditySnapshot;
use Illuminate\Console\Command;

class PruneFormationLiquiditySnapshots extends Command {
    protected $signature = 'formation:snapshot:prune {--days=14}';
    protected $description = 'Delete formation liquidity snapshots older than N days';

    public function handle(): int {
        $days = (int) $this->option('days');
        $deleted = FormationLiquiditySnapshot::where('created_at', '<', now()->subDays($days))->delete();
        $this->info("Pruned {$deleted} snapshot(s) older than {$days} days");
        return self::SUCCESS;
    }
}