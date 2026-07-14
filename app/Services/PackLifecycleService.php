<?php

namespace App\Services;

use App\Enums\PackSlotStatus;
use App\Enums\PackSubscriptionStatus;
use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\PackSlot;
use App\Models\PackSubscription;
use App\Models\PackTier;
use Illuminate\Support\Facades\DB;

/**
 * Same TransactionType caveat as PackPurchaseService — this file also
 * uses PACK_CAPITAL_RETURN and PACK_COMPOUND_RESTAKE, plus reuses the
 * existing FEE case for the 8% early-exit fee (rather than inventing a
 * separate case, since FEE already exists and the description field
 * carries the specific reason).
 */
class PackLifecycleService
{
    public function __construct(
        private WalletService $wallet,
        private ReferralBonusService $referralBonus,
    ) {}

    /**
     * Scheduler-called sweep: ACTIVE subscriptions whose maturity has
     * passed move into the 7-day renewal window. The window is anchored
     * to the true maturity moment (matures_at + 7 days), not to whenever
     * this sweep happens to run, so cron timing imprecision never shifts
     * the deadline a user actually sees.
     */
    public function openRenewalWindowsForMatured(): int {
        $matured = PackSubscription::where('status', PackSubscriptionStatus::ACTIVE->value)
            ->where('matures_at', '<=', now())
            ->get();

        foreach ($matured as $subscription) {
            $subscription->update([
                'status' => PackSubscriptionStatus::IN_RENEWAL_WINDOW,
                'renewal_window_ends_at' => $subscription->matures_at->copy()->addDays(7),
            ]);
        }

        return $matured->count();
    }

    /**
     * Scheduler-called sweep: renewal windows that closed without the
     * user making a choice. Every funded slot's capital returns to the
     * wallet (full amount, no fee — this is the "clean" exit point, fee
     * only applies to manual early exit), slots close, subscription
     * becomes EXPIRED.
     */
    public function closeExpiredRenewalWindows(): int {
        $expired = PackSubscription::where('status', PackSubscriptionStatus::IN_RENEWAL_WINDOW->value)
            ->where('renewal_window_ends_at', '<=', now())
            ->get();

        foreach ($expired as $subscription) {
            DB::transaction(function () use ($subscription) {
                foreach ($subscription->slots()->where('status', PackSlotStatus::FUNDED->value)->get() as $slot) {
                    $this->returnSlotCapital($slot, TransactionType::PACK_CAPITAL_RETURN, 'Pack matured, renewal window passed unrenewed');
                }

                $subscription->update(['status' => PackSubscriptionStatus::EXPIRED]);
            });
        }

        return $expired->count();
    }

    /**
     * User chooses Withdraw during the renewal window — same capital
     * return as the auto-expiry sweep, just user-initiated and marked
     * CLOSED rather than EXPIRED.
     */
    public function withdraw(PackSubscription $subscription): PackSubscription {
        $this->guardInRenewalWindow($subscription);

        return DB::transaction(function () use ($subscription) {
            foreach ($subscription->slots()->where('status', PackSlotStatus::FUNDED->value)->get() as $slot) {
                $this->returnSlotCapital($slot, TransactionType::PACK_CAPITAL_RETURN, 'Withdrawn at renewal window');
            }

            $subscription->update(['status' => PackSubscriptionStatus::CLOSED]);

            return $subscription->fresh('slots');
        });
    }

    /**
     * User chooses Continue — capital rolls into a new subscription cycle
     * of the SAME tier. Profit already paid out daily stays in the wallet
     * as free cash; only the principal carries forward. No wallet
     * transaction for the rolled principal — it never re-enters
     * wallet.balance, it just moves from the old slot's row to the new
     * slot's row directly.
     */
    public function continueCycle(PackSubscription $old): PackSubscription {
        return $this->renewInto($old, $old->packTier, compound: false);
    }

    /**
     * User chooses Auto-Compound — same as Continue, but also pulls
     * already-paid-out profit back out of the wallet and stakes it
     * alongside the rolled principal in the new cycle.
     */
    public function autoCompound(PackSubscription $old): PackSubscription {
        return $this->renewInto($old, $old->packTier, compound: true);
    }

    /**
     * User chooses Upgrade — rolls into a new subscription on a HIGHER
     * tier. Each old funded slot's capital (plus profit if $compound) must
     * meet the new tier's minimum per-slot — this does not auto-top-up;
     * it throws and asks the user to fund the difference separately
     * first, since silently pulling extra money from the wallet on an
     * upgrade is exactly the kind of surprising money movement worth
     * avoiding.
     */
    public function upgrade(PackSubscription $old, PackTier $newTier, bool $compound = false): PackSubscription {
        if ($newTier->price <= $old->packTier->price) {
            throw new \DomainException('Upgrade target must be a higher tier than the current pack.');
        }

        return $this->renewInto($old, $newTier, $compound);
    }

    /**
     * Manual undeployment, available any time a slot is funded (not just
     * during the renewal window) — 8% fee, charged as a separate FEE
     * transaction so the ledger shows "capital returned" and "fee
     * charged" as two legible lines rather than one opaque net figure.
     */
    public function earlyExit(PackSlot $slot): PackSlot {
        if ($slot->status !== PackSlotStatus::FUNDED) {
            throw new \DomainException('Only a funded slot can be exited early.');
        }

        return DB::transaction(function () use ($slot) {
            $capital = (float) $slot->capital_amount;
            $fee = round($capital * 0.08, 2);

            $returnTransaction = $this->wallet->credit(
                user: $slot->subscription->user,
                walletType: WalletType::MAIN,
                amount: $capital,
                type: TransactionType::PACK_CAPITAL_RETURN,
                description: "Early exit — capital returned from slot #{$slot->slot_number}",
                referenceType: PackSlot::class,
                referenceId: $slot->id,
            );

            $this->wallet->debit(
                user: $slot->subscription->user,
                walletType: WalletType::MAIN,
                amount: $fee,
                type: TransactionType::FEE,
                description: "8% early exit fee — slot #{$slot->slot_number}",
                referenceType: PackSlot::class,
                referenceId: $slot->id,
            );

            $slot->update([
                'status' => PackSlotStatus::CLOSED,
                'closed_at' => now(),
                'close_transaction_id' => $returnTransaction->id,
                'early_exit_fee_charged' => $fee,
                'was_early_exit' => true,
            ]);

            $this->referralBonus->cancelPendingForSlot($slot);

            return $slot->fresh();
        });
    }

    // ---------------------------------------------------------------------

    private function renewInto(PackSubscription $old, PackTier $newTier, bool $compound): PackSubscription {
        $this->guardInRenewalWindow($old);

        return DB::transaction(function () use ($old, $newTier, $compound) {
            $new = PackSubscription::create([
                'user_id' => $old->user_id,
                'pack_tier_id' => $newTier->id,
                'status' => PackSubscriptionStatus::ACTIVE,
                'price_paid' => 0, // renewal — no fresh access fee charged
                'purchased_at' => now(),
                'matures_at' => now()->addDays($newTier->duration_days),
                'renewed_from_subscription_id' => $old->id,
            ]);

            $oldFundedSlots = $old->slots()->where('status', PackSlotStatus::FUNDED->value)->get();

            foreach ($oldFundedSlots as $index => $oldSlot) {
                $newCapital = (float) $oldSlot->capital_amount;
                $fundTransactionId = null;

                if ($compound && (float) $oldSlot->realized_profit > 0) {
                    $profitAmount = (float) $oldSlot->realized_profit;

                    $compoundTransaction = $this->wallet->debit(
                        user: $old->user,
                        walletType: WalletType::MAIN,
                        amount: $profitAmount,
                        type: TransactionType::PACK_COMPOUND_RESTAKE,
                        description: "Auto-compound — restaking profit from slot #{$oldSlot->slot_number}",
                        referenceType: PackSlot::class,
                        referenceId: $oldSlot->id,
                    );

                    $newCapital += $profitAmount;
                    $fundTransactionId = $compoundTransaction->id;
                }

                if (!$newTier->isCapitalWithinBounds($newCapital)) {
                    throw new \DomainException(
                        "Rolled capital (\${$newCapital}) for slot #{$oldSlot->slot_number} doesn't meet "
                        ."{$newTier->name}'s per-slot minimum of \${$newTier->min_capital_per_slot}. "
                        .'Fund the difference into your wallet before renewing into this tier.'
                    );
                }

                $newSlotNumber = $index + 1;

                if ($newSlotNumber > $newTier->slot_count) {
                    throw new \DomainException("{$newTier->name} only has {$newTier->slot_count} slots — not enough to hold all funded slots from the previous pack.");
                }

                PackSlot::create([
                    'pack_subscription_id' => $new->id,
                    'slot_number' => $newSlotNumber,
                    'status' => PackSlotStatus::FUNDED,
                    'capital_amount' => $newCapital,
                    'funded_at' => now(),
                    'fund_transaction_id' => $fundTransactionId,
                ]);

                $oldSlot->update([
                    'status' => PackSlotStatus::CLOSED,
                    'closed_at' => now(),
                ]);
            }

            // Any remaining slots in the new tier beyond what carried over stay empty for fresh funding.
            for ($i = $oldFundedSlots->count() + 1; $i <= $newTier->slot_count; $i++) {
                PackSlot::create([
                    'pack_subscription_id' => $new->id,
                    'slot_number' => $i,
                    'status' => PackSlotStatus::EMPTY,
                ]);
            }

            $old->update([
                'status' => PackSubscriptionStatus::RENEWED,
                'renewed_into_subscription_id' => $new->id,
            ]);

            return $new->fresh('slots');
        });
    }

    private function returnSlotCapital(PackSlot $slot, TransactionType $type, string $reason): void {
        $transaction = $this->wallet->credit(
            user: $slot->subscription->user,
            walletType: WalletType::MAIN,
            amount: (float) $slot->capital_amount,
            type: $type,
            description: $reason." — slot #{$slot->slot_number}",
            referenceType: PackSlot::class,
            referenceId: $slot->id,
        );

        $slot->update([
            'status' => PackSlotStatus::CLOSED,
            'closed_at' => now(),
            'close_transaction_id' => $transaction->id,
        ]);
    }

    private function guardInRenewalWindow(PackSubscription $subscription): void {
        if ($subscription->status !== PackSubscriptionStatus::IN_RENEWAL_WINDOW) {
            throw new \DomainException(
                "This pack isn't in its renewal window (status: {$subscription->status->value})."
            );
        }
    }

     public function upgradeNow(PackSubscription $subscription, PackTier $newTier): PackSubscription {
        if (!$subscription->isEligibleForRealtimeUpgrade()) {
            throw new \DomainException(
                "Only an active pack can be upgraded in real time (status: {$subscription->status->value})."
            );
        }
 
        if ($newTier->price <= $subscription->packTier->price) {
            throw new \DomainException('Upgrade target must be a higher tier than your current pack.');
        }
 
        $fundedSlots = $subscription->slots()->where('status', PackSlotStatus::FUNDED->value)->get();
 
        foreach ($fundedSlots as $slot) {
            if (!$newTier->isCapitalWithinBounds((float) $slot->capital_amount)) {
                throw new \DomainException(
                    "Slot #{$slot->slot_number}'s deployed capital (\${$slot->capital_amount}) falls outside "
                    ."{$newTier->name}'s per-slot bounds (\${$newTier->min_capital_per_slot}"
                    .($newTier->max_capital_per_slot ? "–\${$newTier->max_capital_per_slot}" : '+')
                    .'). Contact support before upgrading this slot.'
                );
            }
        }
 
        return DB::transaction(function () use ($subscription, $newTier) {
            $oldTier = $subscription->packTier;
            $cost = $subscription->estimateUpgradeCost($newTier);
 
            $transaction = null;
            if ($cost > 0) {
                $transaction = $this->wallet->debitRespectingLock(
                    user: $subscription->user,
                    walletType: WalletType::MAIN,
                    amount: $cost,
                    type: TransactionType::PACK_UPGRADE_FEE,
                    description: "Real-time upgrade — {$oldTier->name} → {$newTier->name} ({$subscription->remainingDays()} days remaining)",
                    referenceType: PackSubscription::class,
                    referenceId: $subscription->id,
                );
            }
 
            $subscription->update([
                'pack_tier_id' => $newTier->id,
                'upgraded_from_tier_id' => $oldTier->id,
                'upgraded_at' => now(),
                'upgrade_transaction_id' => $transaction?->id,
            ]);

            $subscription->update([
                'pack_tier_id' => $newTier->id,
                'upgraded_from_tier_id' => $oldTier->id,
                'upgraded_at' => now(),
                'upgrade_transaction_id' => $transaction?->id,
                'matures_at' => now()->addDays($newTier->duration_days),
            ]);
 
            
            $existingSlotCount = $subscription->slots()->count();
            for ($i = $existingSlotCount + 1; $i <= $newTier->slot_count; $i++) {
                PackSlot::create([
                    'pack_subscription_id' => $subscription->id,
                    'slot_number' => $i,
                    'status' => PackSlotStatus::EMPTY,
                ]);
            }
 
            return $subscription->fresh('slots');
        });
    }
}
