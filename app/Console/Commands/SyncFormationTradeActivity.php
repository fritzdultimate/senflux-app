<?php

namespace App\Console\Commands;

use App\Services\MarketData\FormationTradeActivitySyncService;
use Illuminate\Console\Command;

class SyncFormationTradeActivity extends Command {
    protected $signature = 'formation:trade-activity:sync';
    protected $description = 'Sync new on-chain trade activity signatures for formations';

    public function handle(FormationTradeActivitySyncService $service): int {
        $count = $service->syncAll();
        $this->info("Recorded {$count} new trade activity signature(s)");
        \Log::info("Recorded {$count} new trade activity signature(s) — " . now());
        return self::SUCCESS;
    }
}