<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\RankAdvancementService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckRankAdvancement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 600;

    public function handle(RankAdvancementService $service): void
    {
        // Process all active users in chunks
        User::where('is_active', true)
            ->chunkById(50, function ($users) use ($service) {
                foreach ($users as $user) {
                    $service->checkAndAdvance($user);
                }
            });
    }
}
