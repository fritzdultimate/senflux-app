<?php
// app/Http/Controllers/FormationShareController.php

namespace App\Http\Controllers;

use App\Services\MarketData\FormationAutoDetectionService;

class RunFormationAutoDetection extends Controller {
    public function run(FormationAutoDetectionService $service) {
        $result = $service->runCycle();
        \Log::info("Detected {$result['created']} new, updated {$result['updated']} — " . now());
    }
}