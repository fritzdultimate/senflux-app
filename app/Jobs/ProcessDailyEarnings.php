<?php

namespace App\Jobs;

use App\Services\DailySlotEarningsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Same job class/name as before — so whatever already schedules this
 * (Console scheduler) doesn't need to change — but the body now calls
 * DailySlotEarningsService::processAllFundedSlots() instead of the
 * deleted EarningsEngineService::processAllActiveDeposits().
 */
class ProcessDailyEarnings implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function handle(DailySlotEarningsService $engine): void {
        $engine->processAllFundedSlots();
    }
}
