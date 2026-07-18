<?php

namespace App\Console\Commands;

use App\Services\DailySlotEarningsService;
use Illuminate\Console\Command;

class ProcessDailySlotEarnings extends Command {
    protected $signature = 'slot:daily-earnings';
    protected $description = 'Process daily earnings for all eligible funded slots';

    public function handle(DailySlotEarningsService $earningService): int {
        $processed = $earningService->processEligibleSlots();
        $this->info("Processed {$processed} slot(s)");
        \Log::info("Processed {$processed} slot earnings — " . now());
        return self::SUCCESS;
    }
}