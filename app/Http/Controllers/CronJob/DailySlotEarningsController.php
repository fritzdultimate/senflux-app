<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Controller;
use App\Services\DailySlotEarningsService;

class DailySlotEarningsController extends Controller {
    public function process(DailySlotEarningsService $earningService) {
        // $earningService->processAllFundedSlots();

        $processed = $earningService->processEligibleSlots();
 
        return response()->json(['processed' => $processed]);
    }
}