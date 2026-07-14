<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Controller;
use App\Services\FormationHealthMonitorService;
use App\Services\SlotAutoDeploymentService;

class SlotAutoDeploymentController extends Controller {
    public function sweep(FormationHealthMonitorService $healthService) {
        $healthService->sweep();
    }

    public function deployEligible(SlotAutoDeploymentService $deployer) {
        $deployer->deployEligibleSlots();
    }
}