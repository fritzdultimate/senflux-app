<?php

namespace App\Services;

use App\Enums\DepositStatus;
use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\ActivityLog;
use App\Models\Deposit;
use App\Models\User;
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
     * Create a new deposit and NowPayments invoice. No Plan involved
     * anymore — Packs replaced the subscription/plan gate entirely.
     * Deposit amount is bounded by config, not a per-plan range.
     * Throws if the user already has a pending deposit.
     */
    public function createInvoice(
        User $user,
        float $amountUsd,
        string $cryptoCurrency = 'sol',
    ): Deposit {
        if ($this->getPendingDeposit($user)) {
            throw new \RuntimeException('PENDING_EXISTS');
        }

        $min = (float) config('senflux.deposit.min_amount', 50);

        if ($amountUsd < $min) {
            throw new \RuntimeException('BELOW_MINIMUM');
        }

        return DB::transaction(function () use ($user, $amountUsd, $cryptoCurrency) {
            $deposit = Deposit::create([
                'user_id'         => $user->id,
                'amount_usd'      => $amountUsd,
                'crypto_currency' => $cryptoCurrency,
                'status'          => DepositStatus::PENDING->value,
            ]);

            $npResponse = $this->nowPayments->createPayment(
                priceAmount:      $amountUsd,
                priceCurrency:    'usd',
                payCurrency:      $cryptoCurrency,
                orderId:          "SENFLUX-{$deposit->id}",
                orderDescription: "Senflux wallet deposit #{$deposit->id}",
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

        if (!in_array($deposit->status, [DepositStatus::PENDING, DepositStatus::WAITING])) {
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
    public function handleIpnUpdate(array $ipnData): void {
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
            'status' => $newStatus->value,
            'actually_paid' => $ipnData['actually_paid'] ?? null,
            'actually_paid_usd' => $ipnData['actually_paid_fiat'] ?? null,
            'confirmations' => $ipnData['confirmations_count'] ?? 0,
            'required_confirmations' => $ipnData['required_confirmations'] ?? 0,
            'ipn_received_at'  => now(),
        ]);

        if ($newStatus === DepositStatus::CONFIRMED) {
            $this->activate($deposit);
        }
    }

    /**
     * A confirmed deposit now does exactly one thing: fund the user's main
     * wallet with the amount actually paid. It no longer starts a
     * daily-rate earning cycle and no longer triggers referral bonuses —
     * referral commission is earned on Pack purchases now (see
     * PackPurchaseService), not on the act of depositing.
     *
     * locked_balance is incremented by the same amount as balance — this
     * is what makes "can't withdraw a fresh deposit, but can spend it on
     * a pack" actually true. The lock is released the moment the funds
     * are spent (WalletService::debitRespectingLock) or, for genuinely
     * unspent deposits, would need an explicit unlock path if the
     * business ever wants deposits to become withdrawable on their own —
     * not part of this change, since the spec is "deposits fund packs,"
     * not "deposits become free cash after some delay."
     */
    public function activate(Deposit $deposit): void
    {
        if ($deposit->status->value === DepositStatus::ACTIVE->value) return;

        DB::transaction(function () use ($deposit) {
            $amount = (float) ($deposit->actually_paid_usd ?? $deposit->amount_usd);

            $deposit->update([
                'status'       => DepositStatus::ACTIVE->value,
                'activated_at' => now(),
            ]);

            $transaction = $this->wallet->credit(
                user: $deposit->user,
                walletType: WalletType::MAIN,
                amount: $amount,
                type: TransactionType::DEPOSIT,
                description: "Deposit #{$deposit->id} confirmed ({$deposit->crypto_currency})",
                referenceId: $deposit->id,
                referenceType: Deposit::class,
            );

            $mainWallet = $deposit->user->wallets()->where('type', WalletType::MAIN->value)->first();
            $this->wallet->lockBalance($mainWallet, $amount);
        });
    }

    public function syncStatus(Deposit $deposit): Deposit {
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
    public function getHistoryForUser(User $user, int $perPage = 8) {
        return $user->deposits()
            ->with('planConfig')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Same change as activate(): funds the wallet (credit + lock) instead
     * of starting a daily-rate earning cycle.
     */
    public function manualActivate(
        Deposit $deposit,
        User $admin,
        float $actuallyPaidUsd,
        ?float $actuallyPaid,
        string $reason,
    ): void {
        if ($deposit->status === DepositStatus::ACTIVE) {
            throw new \RuntimeException('ALREADY_ACTIVE');
        }

        DB::transaction(function () use ($deposit, $admin, $actuallyPaidUsd, $actuallyPaid, $reason) {
            $before = [
                'status' => $deposit->status->value,
                'actually_paid_usd' => $deposit->actually_paid_usd,
                'actually_paid' => $deposit->actually_paid,
            ];

            $deposit->update([
                'status' => DepositStatus::ACTIVE->value,
                'actually_paid_usd' => $actuallyPaidUsd,
                'actually_paid' => $actuallyPaid,
                'activated_at' => now(),
            ]);

            $transaction = $this->wallet->credit(
                user: $deposit->user,
                walletType: WalletType::MAIN,
                amount: $actuallyPaidUsd,
                type: TransactionType::DEPOSIT,
                description: "Deposit #{$deposit->id} manually activated by admin #{$admin->id}",
                referenceId: $deposit->id,
                referenceType: Deposit::class,
                createdBy: $admin->id,
            );

            $mainWallet = $deposit->user->wallets()->where('type', WalletType::MAIN->value)->first();
            $this->wallet->lockBalance($mainWallet, $actuallyPaidUsd);

            ActivityLog::record(
                action: 'deposit.manual_activation',
                userId: $admin->id,
                description: "Manually activated deposit #{$deposit->id} for user #{$deposit->user_id}. Reason: {$reason}",
                subject: $deposit,
                meta: [
                    'before' => $before,
                    'after' => [
                        'status'            => 'active',
                        'actually_paid_usd' => $actuallyPaidUsd,
                        'actually_paid'     => $actuallyPaid,
                    ],
                    'reason' => $reason,
                    'activated_by' => $admin->id,
                    'activated_by_name'=> $admin->name,
                ],
            );

            Log::info('Deposit manually activated by admin', [
                'deposit_id' => $deposit->id,
                'admin_id' => $admin->id,
                'reason' => $reason,
            ]);
        });
    }
}