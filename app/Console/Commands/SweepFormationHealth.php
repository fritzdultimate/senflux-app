<?php

namespace App\Console\Commands;

use App\Services\FormationHealthMonitorService;
use Illuminate\Console\Command;

class SweepFormationHealth extends Command {
    protected $signature = 'formation:health:sweep';
    protected $description = 'Sweep formations for health monitoring';

    public function handle(FormationHealthMonitorService $healthService): int {
        $healthService->sweep();
        $this->info('Health sweep complete');
        return self::SUCCESS;
    }
}