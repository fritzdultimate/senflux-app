<?php

namespace App\Services;

use App\Enums\PackSlotStatus;
use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\PackSlot;
use App\Models\SlotEarning;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Each FUNDED + deployed slot earns on its OWN rolling clock — never a
 * shared calendar-day cutoff. Every payout schedules the slot's next
 * eligible moment at (24h + random jitter) from now, so slots naturally
 * spread out instead of every slot in the system firing in the same
 * 5-min window forever. A cron controller can safely poll this every
 * 5 minutes (or any interval shorter than the jitter window) — it just
 * picks up whichever slots have crossed their own next_earning_at.
 *
 * Gating lives entirely on PackSlot.next_earning_at, not on SlotEarning
 * date rows, so "one payout per calendar day" no longer applies — a
 * slot's interval is always >= 24h, could be a bit more, never less.
 */
class DailySlotEarningsService {
    public function __construct(private WalletService $wallet) {}

    /**
     * Entry point for the cron controller. Finds every deployed slot
     * whose next_earning_at has passed (or was never set) and pays it.
     */
    public function processEligibleSlots(): int {
        $processed = 0;

        PackSlot::where('status', PackSlotStatus::FUNDED->value)
            ->whereNotNull('formation_id')
            ->where(function ($q) {
                $q->whereNull('next_earning_at')
                  ->orWhere('next_earning_at', '<=', now());
            })
            ->with(['formation', 'subscription.packTier'])
            ->chunkById(100, function ($slots) use (&$processed) {
                foreach ($slots as $slot) {
                    if ($this->processSlotEarning($slot)) {
                        $processed++;
                    }
                }
            });

        return $processed;
    }

    /**
     * Backward-compat alias — keep the old public method name working
     * if anything else in the codebase (or an existing cron entry)
     * still calls it directly.
     */
    public function processAllFundedSlots(): void {
        $this->processEligibleSlots();
    }

    public function processSlotEarning(PackSlot $slot): ?SlotEarning {
        // Per-slot lock — the 5-min controller hit could theoretically
        // overlap a slow prior run (Hostinger has no queue to serialize
        // this naturally), so guard against double-paying the same slot.
        $lock = Cache::lock("slot-earning:{$slot->id}", 30);

        if (!$lock->get()) {
            return null;
        }

        try {
            $slot->refresh();

            if ($slot->next_earning_at !== null && $slot->next_earning_at->isFuture()) {
                return null;
            }

            $formation = $slot->formation;
            if (!$formation) {
                return null;
            }

            $tier = $slot->subscription->packTier;
            $baseRate = $tier->baselineDailyRate();
            $multiplier = $formation->state->earningsMultiplier();
            $principal = (float) $slot->capital_amount;

            $earning = round($principal * $baseRate * $multiplier, 8);
            $nextEarningAt = $this->nextEarningTime();
            $today = now()->toDateString();

            if ($earning <= 0) {
                $record = SlotEarning::create([
                    'pack_slot_id' => $slot->id,
                    'user_id' => $slot->subscription->user_id,
                    'formation_id' => $formation->id,
                    'amount' => 0,
                    'base_rate_applied' => $baseRate,
                    'formation_state' => $formation->state->value,
                    'formation_multiplier' => $multiplier,
                    'earned_date' => $today,
                    'processed_at' => now(),
                ]);

                $slot->update(['next_earning_at' => $nextEarningAt]);

                return $record;
            }

            return DB::transaction(function () use ($slot, $formation, $today, $earning, $baseRate, $multiplier, $nextEarningAt) {
                $tx = $this->wallet->credit(
                    user: $slot->subscription->user,
                    walletType: WalletType::MAIN,
                    amount: $earning,
                    type: TransactionType::PACK_SLOT_EARNING,
                    description: "Daily earning — slot #{$slot->slot_number} ({$formation->token_symbol}) — {$today}",
                    referenceId: $slot->id,
                    referenceType: PackSlot::class,
                    meta: ['date' => $today, 'formation' => $formation->token_symbol, 'rate' => $baseRate, 'multiplier' => $multiplier],
                );

                $record = SlotEarning::create([
                    'pack_slot_id' => $slot->id,
                    'user_id' => $slot->subscription->user_id,
                    'formation_id' => $formation->id,
                    'wallet_transaction_id' => $tx->id,
                    'amount' => $earning,
                    'base_rate_applied' => $baseRate,
                    'formation_state' => $formation->state->value,
                    'formation_multiplier' => $multiplier,
                    'earned_date' => $today,
                    'processed_at' => now(),
                ]);

                $slot->update(['next_earning_at' => $nextEarningAt]);
                $slot->increment('realized_profit', $earning);

                return $record;
            });
        } finally {
            $lock->release();
        }
    }

    /**
     * 24h floor, plus a random jitter on top — never less than 24h,
     * randomly a bit more, so slots drift apart over time instead of
     * clustering on whatever moment they happened to first deploy.
     */
    private function nextEarningTime(): \Carbon\CarbonInterface {
        $minHours = config('packs.earning_min_interval_hours', 24);
        $jitterMaxMinutes = config('packs.earning_jitter_max_minutes', 180);

        return now()
            ->addHours($minHours)
            ->addMinutes(random_int(0, max(0, $jitterMaxMinutes)));
    }
}