<?php

namespace App\Services;

use App\Enums\PackSlotStatus;
use App\Enums\PackSubscriptionStatus;
use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Jobs\ProcessPackPurchaseReferralBonus;
use App\Jobs\ProcessSlotFundingReferralBonus;
use App\Models\PackSlot;
use App\Models\PackSubscription;
use App\Models\PackTier;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * NOTE ON TransactionType: this file uses TransactionType::PACK_PURCHASE,
 * PACK_SLOT_FUND, and PACK_REFUND. I haven't seen your TransactionType
 * enum's real case list — only WITHDRAWAL, FEE, REFERRAL_BONUS, RANK_BONUS,
 * LEADERSHIP_MATCH, and DEPOSIT (assumed) are confirmed. Add these three
 * cases (or rename to whatever convention already exists) before this
 * will run.
 */
class PackPurchaseService
{
    public function __construct(
        private WalletService $wallet,
        private ReferralBonusService $referralBonus,
    ) {}

    /**
     * Buy a pack tier — debits the tier price (locked funds first), creates
     * the subscription and its empty slots, and dispatches the referral
     * commission job (creates PENDING rows — see ReferralBonusService;
     * nothing pays out yet, on purpose, since this purchase is still
     * within its 3-day refund window).
     */
    public function buyPack(User $user, PackTier $tier): PackSubscription {
        return DB::transaction(function () use ($user, $tier) {
            $hasActive = PackSubscription::where('user_id', $user->id)
                ->whereIn('status', [
                    PackSubscriptionStatus::ACTIVE->value,
                    PackSubscriptionStatus::IN_RENEWAL_WINDOW->value,
                ])
                ->lockForUpdate()
                ->exists();

            if ($hasActive) {
                throw new \DomainException('You already have an active pack subscription.');
            }

            $transaction = $this->wallet->debitRespectingLock(
                user: $user,
                walletType: WalletType::MAIN,
                amount: (float) $tier->price,
                type: TransactionType::PACK_PURCHASE,
                description: "Purchased {$tier->name} pack",
                referenceType: PackTier::class,
                referenceId: $tier->id,
            );

            $subscription = PackSubscription::create([
                'user_id' => $user->id,
                'pack_tier_id' => $tier->id,
                'status' => PackSubscriptionStatus::ACTIVE,
                'price_paid' => $tier->price,
                'purchased_at' => now(),
                'matures_at' => now()->addDays($tier->duration_days),
                'purchase_transaction_id' => $transaction->id,
            ]);

            for ($i = 1; $i <= $tier->slot_count; $i++) {
                PackSlot::create([
                    'pack_subscription_id' => $subscription->id,
                    'slot_number' => $i,
                    'status' => PackSlotStatus::EMPTY,
                ]);
            }

            ProcessPackPurchaseReferralBonus::dispatch($subscription);

            return $subscription->fresh('slots');
        });
    }

    /**
     * Fund a single empty slot. Capital leaves wallet.balance entirely
     * while deployed — it isn't "locked," it's gone from the wallet and
     * tracked on the slot until the slot closes.
     */
    public function fundSlot(PackSlot $slot, float $amount): PackSlot
    {
        $subscription = $slot->subscription;
        $tier = $subscription->packTier;

        if ($subscription->status !== PackSubscriptionStatus::ACTIVE) {
            throw new \DomainException("This pack isn't open for funding (status: {$subscription->status->value}).");
        }

        if ($slot->status !== PackSlotStatus::EMPTY) {
            throw new \DomainException("Slot #{$slot->slot_number} is already {$slot->status->value}.");
        }

        if (!$tier->isCapitalWithinBounds($amount)) {
            $max = $tier->max_capital_per_slot ? "\${$tier->max_capital_per_slot}" : 'no limit';
            throw new \DomainException("Amount must be between \${$tier->min_capital_per_slot} and {$max} for {$tier->name}.");
        }

        return DB::transaction(function () use ($slot, $amount, $subscription) {
            $transaction = $this->wallet->debitRespectingLock(
                user: $subscription->user,
                walletType: WalletType::MAIN,
                amount: $amount,
                type: TransactionType::PACK_SLOT_FUND,
                description: "Funded slot #{$slot->slot_number} — {$subscription->packTier->name}",
                referenceType: PackSlot::class,
                referenceId: $slot->id,
            );

            $slot->update([
                'status' => PackSlotStatus::FUNDED,
                'capital_amount' => $amount,
                'funded_at' => now(),
                'fund_transaction_id' => $transaction->id,
            ]);

            ProcessSlotFundingReferralBonus::dispatch($slot);

            return $slot->fresh();
        });
    }

    /**
     * 3-day no-questions refund — only if zero slots have ever been
     * funded. Reverses the exact original purchase transaction (locked
     * portion comes back locked), not a flat credit. Also cancels any
     * pending referral commission tied to this purchase in the same
     * transaction — a refunded purchase should never quietly leave a
     * pending bonus sitting around to get confirmed later by the sweep.
     */
    public function refund(PackSubscription $subscription): PackSubscription {
        if (!$subscription->isEligibleForRefund()) {
            throw new \DomainException('This pack is no longer eligible for a refund.');
        }

        return DB::transaction(function () use ($subscription) {
            $refundTransaction = $this->wallet->reverseDebit(
                originalDebit: $subscription->purchaseTransaction,
                refundType: TransactionType::PACK_REFUND,
                description: "Refund of {$subscription->packTier->name} pack purchase",
            );

            $subscription->update([
                'status' => PackSubscriptionStatus::REFUNDED,
                'refunded_at' => now(),
                'refund_transaction_id' => $refundTransaction->id,
            ]);

            $this->referralBonus->cancelPendingFor($subscription);

            return $subscription->fresh();
        });
    }
}
