<?php

namespace App\Console\Commands;

use App\Services\MarketData\BirdeyeSyncService;
use Illuminate\Console\Command;

class SyncBirdeyeData extends Command {
    protected $signature = 'birdeye:sync {--batch=1}';
    protected $description = 'Sync Birdeye trader stats for the least-recently-synced formation(s)';

    public function handle(BirdeyeSyncService $service): int {
        $result = $service->syncNext((int) $this->option('batch'));
        $this->info("synced={$result['synced']} errors={$result['errors']}");
        \Log::info('birdeye:sync batch', $result);
        return self::SUCCESS;
    }
}