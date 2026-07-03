<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Controller;
use App\Services\MarketData\FormationAutoDetectionService;

class  RunFormationAutoDetectionController extends Controller {
    public function run(FormationAutoDetectionService $service) {
        $result = $service->runCycle();
        \Log::info("Detected {$result['created']} new, updated {$result['updated']} — " . now());
    }
}