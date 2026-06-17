<?php

namespace App\Services;

use App\Enums\DepositStatus;
use App\Models\Deposit;
use App\Models\PlanConfig;
use App\Models\User;
use App\Jobs\ProcessReferralBonus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepositService
{
    /** Hours a pending deposit can be cancelled by the user */
    public const CANCEL_GRACE_HOURS = 2;

    public function __construct(
        private NowPaymentsService $nowPayments,
        private WalletService $wallet,
    ) {}

    /**
     * Returns the user's currently pending/waiting/confirming deposit, if any.
     */
    public function getPendingDeposit(User $user): ?Deposit
    {
        return $user->deposits()
            ->whereIn('status', [
                DepositStatus::PENDING->value,
                DepositStatus::WAITING->value,
                DepositStatus::CONFIRMING->value,
            ])
            ->with('planConfig')
            ->latest()
            ->first();
    }

    /**
     * Create a new deposit and NowPayments invoice.
     * Throws if the user already has a pending deposit.
     */
    public function createInvoice(
        User $user,
        PlanConfig $plan,
        float $amountUsd,
        string $cryptoCurrency = 'sol',
    ): Deposit {
        if ($this->getPendingDeposit($user)) {
            throw new \RuntimeException('PENDING_EXISTS');
        }

        return DB::transaction(function () use ($user, $plan, $amountUsd, $cryptoCurrency) {
            $deposit = Deposit::create([
                'user_id'         => $user->id,
                'plan_config_id'  => $plan->id,
                'amount_usd'      => $amountUsd,
                'crypto_currency' => $cryptoCurrency,
                'status'          => DepositStatus::PENDING->value,
            ]);

            $npResponse = $this->nowPayments->createPayment(
                priceAmount:      $amountUsd,
                priceCurrency:    'usd',
                payCurrency:      $cryptoCurrency,
                orderId:          "SENFLUX-{$deposit->id}",
                orderDescription: "Senflux {$plan->label} Plan Deposit",
                successUrl:       route('dashboard.deposit.track', $deposit),
                cancelUrl:        route('dashboard.deposit.create'),
            );

            $deposit->update([
                'nowpayments_id'       => $npResponse['payment_id'],
                'nowpayments_order_id' => $npResponse['order_id'] ?? null,
                'pay_address'          => $npResponse['pay_address'] ?? null,
                'crypto_amount'        => $npResponse['pay_amount'] ?? null,
                'network'              => $npResponse['network'] ?? null,
                'payment_url'          => $npResponse['invoice_url'] ?? null,
                'expires_at'           => isset($npResponse['expiration_estimate_date'])
                                          ? now()->parse($npResponse['expiration_estimate_date'])
                                          : now()->addHours(24),
                'status'               => DepositStatus::WAITING->value,
            ]);

            return $deposit->fresh();
        });
    }

    /**
     * User-initiated cancel of a pending deposit, within grace period only.
     */
    public function cancelPending(Deposit $deposit, User $user): void {
        abort_if($deposit->user_id !== $user->id, 403);

        if (!in_array($deposit->status, [DepositStatus::PENDING->value, DepositStatus::WAITING->value])) {
            throw new \RuntimeException('NOT_CANCELLABLE');
        }

        if ($deposit->created_at->diffInHours(now()) > self::CANCEL_GRACE_HOURS) {
            throw new \RuntimeException('GRACE_PERIOD_EXPIRED');
        }

        $deposit->update(['status' => DepositStatus::FAILED->value]);

        Log::info('Deposit cancelled by user', [
            'deposit_id' => $deposit->id,
            'user_id'    => $user->id,
        ]);
    }

    public function canBeCancelled(Deposit $deposit): bool
    {
        return in_array($deposit->status, [DepositStatus::PENDING->value, DepositStatus::WAITING->value])
            && $deposit->created_at->diffInHours(now()) <= self::CANCEL_GRACE_HOURS;
    }

    /**
     * Handle NowPayments IPN update. Idempotent.
     */
    public function handleIpnUpdate(array $ipnData): void
    {
        $paymentId = $ipnData['payment_id'] ?? null;
        if (!$paymentId) return;

        $deposit = Deposit::where('nowpayments_id', $paymentId)->first();
        if (!$deposit) {
            Log::warning("NowPayments IPN: deposit not found for payment_id {$paymentId}");
            return;
        }

        if (DepositStatus::from($deposit->status->value)->isTerminal()) return;

        $newStatus = DepositStatus::fromNowPayments($ipnData['payment_status'] ?? '');

        $deposit->update([
            'status'                  => $newStatus->value,
            'actually_paid'           => $ipnData['actually_paid'] ?? null,
            'actually_paid_usd'       => $ipnData['actually_paid_fiat'] ?? null,
            'confirmations'           => $ipnData['confirmations_count'] ?? 0,
            'required_confirmations'  => $ipnData['required_confirmations'] ?? 0,
            'ipn_received_at'         => now(),
        ]);

        if ($newStatus === DepositStatus::CONFIRMED) {
            $this->activate($deposit);
        }
    }

    public function activate(Deposit $deposit): void
    {
        if ($deposit->status === DepositStatus::ACTIVE->value) return;

        DB::transaction(function () use ($deposit) {
            $plan = $deposit->planConfig;

            $deposit->update([
                'status'       => DepositStatus::ACTIVE->value,
                'daily_rate'   => $plan->daily_rate_max,
                'activated_at' => now(),
            ]);

            ProcessReferralBonus::dispatch($deposit);
        });
    }

    public function syncStatus(Deposit $deposit): Deposit
    {
        if (DepositStatus::from($deposit->status->value)->isTerminal()) {
            return $deposit;
        }

        $npData = $this->nowPayments->getPaymentStatus($deposit->nowpayments_id);
        $this->handleIpnUpdate($npData);

        return $deposit->fresh();
    }

    /**
     * Expire abandoned pending/waiting deposits. Called by scheduler.
     */
    public function expireStale(): void
    {
        Deposit::whereIn('status', [DepositStatus::PENDING->value, DepositStatus::WAITING->value])
            ->where('created_at', '<=', now()->subHours(24))
            ->update(['status' => DepositStatus::EXPIRED->value]);
    }

    /**
     * Paginated history for a user — used by the inline history panel.
     */
    public function getHistoryForUser(User $user, int $perPage = 8)
    {
        return $user->deposits()
            ->with('planConfig')
            ->latest()
            ->paginate($perPage);
    }
}
