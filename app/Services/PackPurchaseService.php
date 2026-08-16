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


class PackPurchaseService {
    public function __construct(
        private WalletService $wallet,
        private ReferralBonusService $referralBonus,
    ) {}

    
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

            // One slot per subscription now — the "3/5/10 slots" tiering is
            // gone. A subscriber deploys once, then tops up the same slot.
            PackSlot::create([
                'pack_subscription_id' => $subscription->id,
                'slot_number' => 1,
                'status' => PackSlotStatus::EMPTY,
            ]);

            ProcessPackPurchaseReferralBonus::dispatch($subscription);

            return $subscription->fresh('slots');
        });
    }

    
    public function deploySlot(PackSubscription $subscription, float $amount): PackSlot {
        $currentSlot = $subscription->slots()
            ->reorder('slot_number', 'desc')
            ->first();

        
        if (!$currentSlot) {
            $newSlot = $subscription->slots()->create([
                'slot_number' => 1,
                'status' => PackSlotStatus::EMPTY,
            ]);

            return $this->fundSlot($newSlot, $amount);
        }

        if ($currentSlot->status === PackSlotStatus::EMPTY) {
            return $this->fundSlot($currentSlot, $amount);
        }

        if ($currentSlot->status === PackSlotStatus::CLOSED) {
            $newSlot = $subscription->slots()->create([
                'slot_number' => $currentSlot->slot_number + 1,
                'status' => PackSlotStatus::EMPTY,
            ]);

            return $this->fundSlot($newSlot, $amount);
        }

        // FUNDED
        throw new \DomainException('This pack already has an active position.');
    }

    
    public function fundSlot(PackSlot $slot, float $amount): PackSlot {
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

            $slot->contributions()->create([
                'amount' => $amount,
                'type' => 'deploy',
                'wallet_transaction_id' => $transaction->id,
            ]);

            ProcessSlotFundingReferralBonus::dispatch($slot);

            return $slot->fresh();
        });
    }

    
    public function topUp(PackSlot $slot, float $amount): PackSlot {
        $subscription = $slot->subscription;
        $tier = $subscription->packTier;

        if ($subscription->status !== PackSubscriptionStatus::ACTIVE) {
            throw new \DomainException("This pack isn't open for top-ups (status: {$subscription->status->value}).");
        }

        if ($slot->status !== PackSlotStatus::FUNDED) {
            throw new \DomainException('Deploy capital before adding more.');
        }

        if ($amount <= 0) {
            throw new \DomainException('Top-up amount must be greater than zero.');
        }

        $projectedTotal = (float) $slot->capital_amount + $amount;

        if ($tier->max_capital_per_slot && $projectedTotal > (float) $tier->max_capital_per_slot) {
            $room = max(0, (float) $tier->max_capital_per_slot - (float) $slot->capital_amount);
            throw new \DomainException("This would exceed the {$tier->name} capacity of \${$tier->max_capital_per_slot}. You can add up to \${$room} more.");
        }

        return DB::transaction(function () use ($slot, $amount, $subscription) {
            $transaction = $this->wallet->debitRespectingLock(
                user: $subscription->user,
                walletType: WalletType::MAIN,
                amount: $amount,
                type: TransactionType::PACK_SLOT_TOPUP,
                description: "Added capital to slot #{$slot->slot_number} — {$subscription->packTier->name}",
                referenceType: PackSlot::class,
                referenceId: $slot->id,
            );

            $slot->increment('capital_amount', $amount);

            $slot->contributions()->create([
                'amount' => $amount,
                'type' => 'topup',
                'wallet_transaction_id' => $transaction->id,
            ]);

            return $slot->fresh();
        });
    }

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