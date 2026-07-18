<?php

namespace App\Console\Commands;

use App\Services\SlotAutoDeploymentService;
use Illuminate\Console\Command;

class DeployEligibleSlots extends Command {
    protected $signature = 'slot:auto-deploy';
    protected $description = 'Deploy all eligible pack slots';

    public function handle(SlotAutoDeploymentService $deployer): int {
        $deployer->deployEligibleSlots();
        $this->info('Eligible slots deployed');
        return self::SUCCESS;
    }
}