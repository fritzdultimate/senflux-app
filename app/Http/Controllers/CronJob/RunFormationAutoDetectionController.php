<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\FormationLiquiditySnapshot;
use App\Services\MarketData\FormationAutoDetectionService;
use Illuminate\Http\Request;

class  RunFormationAutoDetectionController extends Controller {
    public function run(FormationAutoDetectionService $service) {
        $result = $service->runCycle();
        \Log::info("Detected {$result['created']} new, updated {$result['updated']} — " . now());
    }

    public function snapshot() {
        Formation::query()->chunk(200, function ($formations) {
            foreach ($formations as $formation) {
                FormationLiquiditySnapshot::create([
                    'formation_id' => $formation->id,
                    'liquidity_usd' => $formation->liquidity_usd,
                ]);
            }
        });
    }

    public function pruneSnapshot() {
        FormationLiquiditySnapshot::where('created_at', '<', now()->subDays(14))->delete();
    }
}