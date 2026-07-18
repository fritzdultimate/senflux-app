<?php

namespace App\Console\Commands;

use App\Services\PackLifecycleService;
use Illuminate\Console\Command;

class OpenPackRenewalWindows extends Command {
    protected $signature = 'pack-lifecycle:open-renewal-windows';
    protected $description = 'Open renewal windows for matured pack subscriptions';

    public function handle(PackLifecycleService $service): int {
        $service->openRenewalWindowsForMatured();
        $this->info('Renewal windows opened');
        return self::SUCCESS;
    }
}