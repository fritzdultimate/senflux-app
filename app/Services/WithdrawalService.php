<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Enums\WithdrawalStatus;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\WithdrawalSetting;
use Illuminate\Support\Facades\DB;

class WithdrawalService
{
    public function __construct(private WalletService $wallet) {}

    /**
     * Create a pending withdrawal request. Locks balance immediately.
     */
    public function create(
        User   $user,
        WalletType $walletType,
        float  $amount,
        string $walletAddress,
        string $network,
        string $cryptoCurrency,
    ): Withdrawal {
        $settings = $this->getSettings();

        if (!$settings->is_enabled) {
            throw new \RuntimeException('Withdrawals are currently disabled.');
        }

        if ($amount < (float) $settings->min_amount) {
            throw new \RuntimeException("Minimum withdrawal is \${$settings->min_amount}.");
        }

        if ($amount > (float) $settings->max_amount) {
            throw new \RuntimeException("Maximum withdrawal is \${$settings->max_amount}.");
        }

        $available = $this->wallet->availableBalance($user, $walletType);
        if ($available < $amount) {
            throw new \RuntimeException("Insufficient balance. Available: \${$available}.");
        }

        // Validate network is allowed
        $allowed = json_decode($settings->allowed_networks ?? '[]', true);
        if (!empty($allowed) && !in_array($network, $allowed)) {
            throw new \RuntimeException("Network '{$network}' is not supported.");
        }

        // Calculate fee
        $fee = $this->calculateFee($amount, $settings);
        $netAmount = $amount - $fee;

        return DB::transaction(function () use (
            $user, $walletType, $amount, $fee, $netAmount,
            $walletAddress, $network, $cryptoCurrency
        ) {
            $walletModel = $user->wallets()->where('type', $walletType->value)->firstOrFail();

            // Lock the balance so it can't be double-spent
            $this->wallet->lockBalance($walletModel, $amount);

            return Withdrawal::create([
                'user_id'          => $user->id,
                'wallet_id'        => $walletModel->id,
                'amount'           => $amount,
                'fee'              => $fee,
                'net_amount'       => $netAmount,
                'wallet_address'   => $walletAddress,
                'network'          => $network,
                'crypto_currency'  => $cryptoCurrency,
                'status'           => WithdrawalStatus::PENDING->value,
            ]);
        });
    }

    /**
     * Admin approves a withdrawal.
     */
    public function approve(Withdrawal $withdrawal, int $adminId, string $note = ''): void
    {
        if ($withdrawal->status !== WithdrawalStatus::PENDING->value) {
            throw new \RuntimeException('Only pending withdrawals can be approved.');
        }

        $withdrawal->update([
            'status'      => WithdrawalStatus::APPROVED->value,
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_note'  => $note ?: null,
        ]);
    }

    /**
     * Admin rejects a withdrawal. Releases locked balance.
     */
    public function reject(Withdrawal $withdrawal, int $adminId, string $reason): void
    {
        if ($withdrawal->status !== WithdrawalStatus::PENDING->value) {
            throw new \RuntimeException('Only pending withdrawals can be rejected.');
        }

        DB::transaction(function () use ($withdrawal, $adminId, $reason) {
            $walletModel = $withdrawal->wallet;
            $this->wallet->releaseLockedBalance($walletModel, (float) $withdrawal->amount);

            $withdrawal->update([
                'status'      => WithdrawalStatus::REJECTED->value,
                'reviewed_by' => $adminId,
                'reviewed_at' => now(),
                'admin_note'  => $reason,
            ]);
        });
    }

    /**
     * Admin marks a withdrawal as paid. Debits the wallet and releases lock.
     */
    public function markPaid(Withdrawal $withdrawal, int $adminId, string $txHash = ''): void
    {
        if ($withdrawal->status !== WithdrawalStatus::APPROVED->value) {
            throw new \RuntimeException('Only approved withdrawals can be marked as paid.');
        }

        DB::transaction(function () use ($withdrawal, $adminId, $txHash) {
            $user        = $withdrawal->user;
            $walletModel = $withdrawal->wallet;
            $walletType  = WalletType::from($walletModel->type);

            // Release the lock first, then debit
            $this->wallet->releaseLockedBalance($walletModel, (float) $withdrawal->amount);

            $tx = $this->wallet->debit(
                user:          $user,
                walletType:    $walletType,
                amount:        (float) $withdrawal->amount,
                type:          TransactionType::WITHDRAWAL,
                description:   "Withdrawal #{$withdrawal->id} — {$withdrawal->crypto_currency} on {$withdrawal->network}",
                referenceId:   $withdrawal->id,
                referenceType: Withdrawal::class,
            );

            $withdrawal->update([
                'status'                => WithdrawalStatus::PAID->value,
                'reviewed_by'           => $adminId,
                'paid_at'               => now(),
                'tx_hash'               => $txHash ?: null,
                'wallet_transaction_id' => $tx->id,
            ]);
        });
    }

    /**
     * User cancels their own pending withdrawal (within grace period).
     */
    public function cancel(Withdrawal $withdrawal, User $user): void
    {
        if ($withdrawal->user_id !== $user->id) {
            throw new \RuntimeException('Unauthorized.');
        }

        if ($withdrawal->status !== WithdrawalStatus::PENDING->value) {
            throw new \RuntimeException('Only pending withdrawals can be cancelled.');
        }

        // Allow cancellation only within 30 minutes
        if ($withdrawal->created_at->diffInMinutes(now()) > 30) {
            throw new \RuntimeException('Cancellation window has closed (30 minutes).');
        }

        DB::transaction(function () use ($withdrawal) {
            $this->wallet->releaseLockedBalance($withdrawal->wallet, (float) $withdrawal->amount);
            $withdrawal->update(['status' => WithdrawalStatus::REJECTED->value]);
        });
    }

    public function getSettings(): object
    {
        return DB::table('withdrawal_settings')->first()
            ?? (object) [
                'is_enabled'      => false,
                'min_amount'      => 10,
                'max_amount'      => 50000,
                'fee_type'        => 'percentage',
                'fee_value'       => 0,
                'processing_days' => 1,
                'allowed_networks'=> '["sol","bsc","eth","trc20"]',
            ];
    }

    private function calculateFee(float $amount, object $settings): float
    {
        if ((float) $settings->fee_value <= 0) return 0;

        return match ($settings->fee_type) {
            'percentage' => round($amount * ((float) $settings->fee_value / 100), 8),
            'flat'       => (float) $settings->fee_value,
            default      => 0,
        };
    }
}
