<?php

namespace App\Services;

use App\Models\PlanConfig;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /** Hours a pending subscription can be cancelled by the user */
    public const CANCEL_GRACE_HOURS = 2;

    public function __construct(
        private NowPaymentsService $nowPayments,
    ) {}

    /**
     * Returns the user's currently pending/waiting subscription, if any.
     * Used to enforce the "one pending at a time" rule.
     */
    public function getPendingSubscription(User $user): ?Subscription
    {
        return $user->subscriptions()
            ->whereIn('status', ['pending', 'waiting'])
            ->with('planConfig')
            ->latest()
            ->first();
    }

    /**
     * Create a NowPayments invoice for a subscription.
     * Throws if the user already has a pending subscription.
     */
    public function createInvoice(User $user, PlanConfig $plan, string $interval): array {
        if ($this->getPendingSubscription($user)) {
            throw new \RuntimeException('PENDING_EXISTS');
        }

        $amount = $plan->getPriceForInterval($interval);

        if ($amount <= 0) {
            throw new \RuntimeException("Invalid price for plan {$plan->plan->value} / {$interval}");
        }

        return DB::transaction(function () use ($user, $plan, $interval, $amount) {

            $subscription = Subscription::create([
                'user_id'        => $user->id,
                'plan_config_id' => $plan->id,
                'interval'       => $interval,
                'amount_paid'    => $amount,
                'starts_at'      => now(),
                'expires_at'     => now()->addMonths($this->intervalMonths($interval)),
                'status'         => 'pending',
            ]);

            $npResponse = $this->nowPayments->createPayment(
                priceAmount:      $amount,
                priceCurrency:    'usd',
                payCurrency:      'sol',
                orderId:          "SUB-{$subscription->id}",
                orderDescription: "Senflux {$plan->label} — " . ucfirst($interval),
                successUrl:       route('dashboard.subscription.track', $subscription),
                cancelUrl:        route('dashboard.subscribe'),
            );

            $subscription->update([
                'nowpayments_id' => $npResponse['payment_id'],
                'status'         => 'waiting',
            ]);

            Log::info('Subscription invoice created', [
                'user_id'         => $user->id,
                'subscription_id' => $subscription->id,
                'plan'            => $plan->plan->value,
                'interval'        => $interval,
                'amount'          => $amount,
            ]);

            return [
                'subscription_id' => $subscription->id,
                'pay_address'     => $npResponse['pay_address']    ?? null,
                'crypto_amount'   => $npResponse['pay_amount']     ?? null,
                'crypto_currency' => $npResponse['pay_currency']   ?? 'sol',
                'amount_usd'      => $amount,
                'payment_url'     => $npResponse['invoice_url']    ?? null,
                'expires_at'      => $npResponse['expiration_estimate_date'] ?? now()->addDay()->toIso8601String(),
            ];
        });
    }

    /**
     * User-initiated cancel of a pending subscription, within grace period only.
     */
    public function cancelPending(Subscription $subscription, User $user): void
    {
        abort_if($subscription->user_id !== $user->id, 403);

        if (!in_array($subscription->status, ['pending', 'waiting'])) {
            throw new \RuntimeException('NOT_CANCELLABLE');
        }

        if ($subscription->created_at->diffInHours(now()) > self::CANCEL_GRACE_HOURS) {
            throw new \RuntimeException('GRACE_PERIOD_EXPIRED');
        }

        $subscription->update(['status' => 'cancelled']);

        Log::info('Subscription cancelled by user', [
            'subscription_id' => $subscription->id,
            'user_id'         => $user->id,
        ]);
    }

    public function canBeCancelled(Subscription $subscription): bool
    {
        return in_array($subscription->status, ['pending', 'waiting'])
            && $subscription->created_at->diffInHours(now()) <= self::CANCEL_GRACE_HOURS;
    }

    /**
     * Activate a subscription after payment is confirmed.
     */
    public function activate(Subscription $subscription): void
    {
        if ($subscription->status === 'active') return;

        DB::transaction(function () use ($subscription) {
            $user   = $subscription->user;
            $months = $this->intervalMonths($subscription->interval->value);

            $existing = $user->subscriptions()
                ->where('status', 'active')
                ->where('id', '!=', $subscription->id)
                ->where('expires_at', '>', now())
                ->latest('expires_at')
                ->first();

            $startsAt  = $existing ? $existing->expires_at : now();
            $expiresAt = $startsAt->copy()->addMonths($months);

            $subscription->update([
                'status'     => 'active',
                'starts_at'  => $startsAt,
                'expires_at' => $expiresAt,
            ]);

            $user->update([
                'subscription_plan'       => $subscription->planConfig->plan->value,
                'subscription_expires_at' => $expiresAt,
            ]);

            Log::info('Subscription activated', [
                'user_id'         => $user->id,
                'subscription_id' => $subscription->id,
                'expires_at'      => $expiresAt,
            ]);
        });
    }

    /**
     * Expire subscriptions past their end date, and abandon stale pending invoices.
     * Called by scheduler.
     */
    public function expireStale(): void {
        // Active subscriptions past expiry
        Subscription::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->chunkById(100, function ($subscriptions) {
                foreach ($subscriptions as $sub) {
                    $sub->update(['status' => 'expired']);

                    $hasActive = $sub->user
                        ->subscriptions()
                        ->where('id', '!=', $sub->id)
                        ->active()
                        ->exists();

                    if (!$hasActive) {
                        $sub->user->update([
                            'subscription_plan'       => null,
                            'subscription_expires_at' => null,
                        ]);
                    }
                }
            });

        // Pending/waiting invoices abandoned for 24h+ — expire so the slot frees up
        Subscription::whereIn('status', ['pending', 'waiting'])
            ->where('created_at', '<=', now()->subHours(24))
            ->update(['status' => 'expired']);
    }

    public function handleIpnUpdate(array $ipnData): void
    {
        $paymentId = $ipnData['payment_id'] ?? null;
        if (!$paymentId) return;

        $subscription = Subscription::where('nowpayments_id', $paymentId)->first();
        if (!$subscription) {
            Log::warning("Subscription IPN: no subscription found for payment_id {$paymentId}");
            return;
        }

        if ($subscription->status === 'active') return;

        $npStatus = $ipnData['payment_status'] ?? '';

        if ($npStatus === 'finished') {
            $this->activate($subscription);
        } elseif (in_array($npStatus, ['failed', 'expired', 'refunded'])) {
            $subscription->update(['status' => 'cancelled']);
        }
    }

    /**
     * Paginated history for a user — used by the inline history panel.
     */
    public function getHistoryForUser(User $user, int $perPage = 8)
    {
        return $user->subscriptions()
            ->with('planConfig')
            ->latest()
            ->paginate($perPage);
    }

    private function intervalMonths(string $interval): int
    {
        return match($interval) {
            'monthly'   => 1,
            'quarterly' => 3,
            'yearly'    => 12,
            default     => 1,
        };
    }
}
