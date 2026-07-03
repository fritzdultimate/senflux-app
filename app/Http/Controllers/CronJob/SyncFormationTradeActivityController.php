<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Controller;
use App\Services\MarketData\FormationTradeActivitySyncService;

class  SyncFormationTradeActivityController extends Controller {
    public function run(FormationTradeActivitySyncService $service) {
        $count = $service->syncAll();
        \Log::info("Recorded {$count} new trade activity signature(s) — " . now());
    }
}