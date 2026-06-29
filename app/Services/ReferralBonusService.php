<?php

namespace App\Services;

use App\Enums\ReferralBonusStatus;
use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\Deposit;
use App\Models\PackSubscription;
use App\Models\ReferralBonus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReferralBonusService
{
    // Referral bonus rates per level (8 levels)
    private const RATES = [
        1 => 0.08,
        2 => 0.04,
        3 => 0.025,
        4 => 0.02,
        5 => 0.015,
        6 => 0.0125,
        7 => 0.01,
        8 => 0.0075,
    ];

    public function __construct(private WalletService $wallet) {}

    /**
     * UNCHANGED — kept exactly as it was. Deposits no longer dispatch this
     * (see DepositService::activate()), so this is effectively dead going
     * forward, but left in place rather than deleted in case anything else
     * still calls it directly, and so historical deposit-sourced
     * ReferralBonus rows keep a working relation to follow.
     */
    public function processForDeposit(Deposit $deposit): void {
        $depositor  = $deposit->user;
        $amount     = (float) $deposit->actually_paid_usd ?? (float) $deposit->amount_usd;
        $currentUser = $depositor;

        for ($level = 1; $level <= 8; $level++) {
            $upline = $this->getDirectUpline($currentUser);
            if (!$upline) break;

            $rate   = self::RATES[$level];
            $bonus  = round($amount * $rate, 8);

            if ($bonus > 0) {
                DB::transaction(function () use ($upline, $depositor, $deposit, $level, $rate, $bonus) {
                    $tx = $this->wallet->credit(
                        user: $upline,
                        walletType: WalletType::REFERRAL,
                        amount: $bonus,
                        type: TransactionType::REFERRAL_BONUS,
                        description: "Level {$level} referral bonus from deposit #{$deposit->id}",
                        referenceId: $deposit->id,
                        referenceType: Deposit::class,
                        meta: ['level' => $level, 'rate' => $rate, 'from_user' => $depositor->id],
                    );

                    ReferralBonus::create([
                        'earner_id' => $upline->id,
                        'source_user_id' => $depositor->id,
                        'deposit_id' => $deposit->id,
                        'status' => ReferralBonusStatus::CONFIRMED,
                        'level' => $level,
                        'rate' => $rate,
                        'amount' => $bonus,
                        'wallet_transaction_id' => $tx->id,
                        'processed_at' => now(),
                    ]);
                });
            }

            $currentUser = $upline;
        }
    }

    /**
     * Pack-purchase equivalent of processForDeposit() — but creates
     * PENDING rows with NO wallet credit yet, rather than crediting
     * instantly. The commission only becomes real money once the 3-day
     * refund window closes without a refund (confirmPendingFor()) — see
     * the class doc on why an instant payout here would be a clawback
     * problem waiting to happen.
     *
     * Commission base is price_paid (the access fee), not slot capital —
     * see the note in PackPurchaseService. Flagged for the client to
     * confirm; trivial to extend to slot-funding later if they want that.
     */
    public function processForPackPurchase(PackSubscription $subscription): void {
        $buyer = $subscription->user;
        $amount = (float) $subscription->price_paid;

        if ($amount <= 0) {
            return; //
        }

        $currentUser = $buyer;

        for ($level = 1; $level <= 8; $level++) {
            $upline = $this->getDirectUpline($currentUser);
            if (!$upline) break;

            $rate  = self::RATES[$level];
            $bonus = round($amount * $rate, 8);

            if ($bonus > 0) {
                ReferralBonus::create([
                    'earner_id' => $upline->id,
                    'source_user_id' => $buyer->id,
                    'pack_subscription_id' => $subscription->id,
                    'status' => ReferralBonusStatus::PENDING,
                    'level' => $level,
                    'rate' => $rate,
                    'amount' => $bonus,
                ]);
            }

            $currentUser = $upline;
        }
    }

    /**
     * Confirms every pending bonus tied to a pack purchase — credits each
     * upline's wallet for real and marks processed_at. Call this once the
     * purchase's 3-day refund window has genuinely closed unrefunded
     * (see confirmAllExpiredPending() for the scheduled sweep version).
     */
    public function confirmPendingFor(PackSubscription $subscription): void {
        $pending = ReferralBonus::where('pack_subscription_id', $subscription->id)
            ->where('status', ReferralBonusStatus::PENDING->value)
            ->get();

        foreach ($pending as $bonus) {
            DB::transaction(function () use ($bonus, $subscription) {
                $tx = $this->wallet->credit(
                    user: $bonus->earner,
                    walletType: WalletType::REFERRAL,
                    amount: (float) $bonus->amount,
                    type: TransactionType::REFERRAL_BONUS,
                    description: "Level {$bonus->level} referral bonus — {$subscription->packTier->name} pack purchase",
                    referenceId: $subscription->id,
                    referenceType: PackSubscription::class,
                    meta: ['level' => $bonus->level, 'rate' => (float) $bonus->rate, 'from_user' => $bonus->source_user_id],
                );

                $bonus->update([
                    'status' => ReferralBonusStatus::CONFIRMED,
                    'wallet_transaction_id' => $tx->id,
                    'processed_at' => now(),
                ]);
            });
        }
    }

    /**
     * Called from PackPurchaseService::refund() — cancels every pending
     * bonus tied to that purchase. No wallet movement, since nothing was
     * ever credited for a row that never left PENDING.
     */
    public function cancelPendingFor(PackSubscription $subscription): void {
        ReferralBonus::where('pack_subscription_id', $subscription->id)
            ->where('status', ReferralBonusStatus::PENDING->value)
            ->update(['status' => ReferralBonusStatus::CANCELLED->value]);
    }

    /**
     * Scheduler-called sweep — confirms every pending bonus whose
     * source purchase is more than 3 days old and was never refunded.
     * This is what actually pays referrers in the common case (nobody
     * refunds); confirmPendingFor() is also called directly wherever
     * it's more convenient to tie into an existing scheduler entry
     * rather than adding a new cron line for this alone.
     */
    public function confirmAllExpiredPending(): int {
        $subscriptionIds = ReferralBonus::where('status', ReferralBonusStatus::PENDING->value)
            ->whereHas('packSubscription', fn ($q) => $q->where('purchased_at', '<=', now()->subDays(3)))
            ->pluck('pack_subscription_id')
            ->unique();

        foreach ($subscriptionIds as $id) {
            $subscription = PackSubscription::find($id);
            if ($subscription) {
                $this->confirmPendingFor($subscription);
            }
        }

        return $subscriptionIds->count();
    }

    private function getDirectUpline(User $user): ?User {
        return $user->referrer;
    }
}