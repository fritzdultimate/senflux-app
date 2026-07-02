<?php

namespace App\Jobs;

use App\Models\Formation;
use App\Services\MarketData\FormationMarketDataSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncFormationMarketData implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(public ?Formation $formation = null) {}

    public function handle(FormationMarketDataSyncService  $service): void {

        if ($this->formation) {
            $service->syncOne($this->formation);
            \Log::info("Synced market data for 1 formation at " . now());
            return;
        }

        $count = $service->syncAll();
        \Log::info("Synced market data for {$count} formation(s) at " . now());

    }
}
