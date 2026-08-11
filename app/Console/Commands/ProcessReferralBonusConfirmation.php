<?php

namespace App\Console\Commands;

use App\Services\ReferralBonusService;
use Illuminate\Console\Command;

class ProcessReferralBonusConfirmation extends Command {
    protected $signature = 'bonus:confirmation';
    protected $description = 'Process referral bonus confirmation';

    public function handle(ReferralBonusService $bonusService): int {
        $processedSlot = $bonusService->confirmAllExpiredPendingSlots();
        $processedPurchase = $bonusService->confirmAllExpiredPending();

        \Log::info("Confirmed {$processedSlot} slot funding referral bonus(s) — " . now());
        \Log::info("Confirmed {$processedPurchase} pack purchase referral bonus(s) — " . now());

        $this->info("Confirmed {$processedPurchase} pack purchase referral bonus(s)");
        $this->info("Confirmed {$processedSlot} slot funding referral bonus(s)");
        return self::SUCCESS;
    }
}