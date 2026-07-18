<?php

namespace App\Console\Commands;

use App\Services\PackLifecycleService;
use Illuminate\Console\Command;

class CloseExpiredPackRenewalWindows extends Command {
    protected $signature = 'pack-lifecycle:close-expired-renewal-windows';
    protected $description = 'Close expired pack renewal windows';

    public function handle(PackLifecycleService $service): int {
        $service->closeExpiredRenewalWindows();
        $this->info('Expired renewal windows closed');
        return self::SUCCESS;
    }
}