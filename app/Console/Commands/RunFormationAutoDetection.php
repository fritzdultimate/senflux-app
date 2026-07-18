<?php

namespace App\Console\Commands;

use App\Services\MarketData\FormationAutoDetectionService;
use Illuminate\Console\Command;

class RunFormationAutoDetection extends Command {
    protected $signature = 'formation:detect {--batch=25}';
    protected $description = 'Process one resumable batch of the formation watchlist';

    public function handle(FormationAutoDetectionService $service): int {
        $result = $service->runCycle((int) $this->option('batch'));

        $this->info("created={$result['created']} updated={$result['updated']} errors={$result['errors']} cursor={$result['next_cursor']}");
        \Log::info('formation:detect batch', $result);

        return self::SUCCESS;
    }
}