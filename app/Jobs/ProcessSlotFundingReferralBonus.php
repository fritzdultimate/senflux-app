<?php

namespace App\Jobs;

use App\Models\PackSlot;
use App\Services\ReferralBonusService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSlotFundingReferralBonus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public PackSlot $slot) {}

    public function handle(ReferralBonusService $service): void
    {
        $service->processForSlotFunding($this->slot);
    }
}
