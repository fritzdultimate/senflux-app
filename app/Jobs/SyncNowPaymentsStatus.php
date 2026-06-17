<?php

namespace App\Jobs;

use App\Models\Deposit;
use App\Services\DepositService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncNowPaymentsStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function handle(DepositService $service): void
    {
        // Sync all pending/confirming deposits
        Deposit::whereIn('status', ['waiting', 'confirming'])
            ->whereNotNull('nowpayments_id')
            ->chunkById(50, function ($deposits) use ($service) {
                foreach ($deposits as $deposit) {
                    try {
                        $service->syncStatus($deposit);
                    } catch (\Exception $e) {
                        \Log::error("Failed to sync deposit #{$deposit->id}: " . $e->getMessage());
                    }
                }
            });
    }
}
