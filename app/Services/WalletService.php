<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Credit a user's wallet. Always goes through ledger.
     */
    public function credit(
        User $user,
        WalletType $walletType,
        float $amount,
        TransactionType $type,
        string $description = '',
        ?int $referenceId = null,
        ?string $referenceType = null,
        array $meta = [],
        ?int $createdBy = null,
    ): WalletTransaction {
        return DB::transaction(function () use (
            $user, $walletType, $amount, $type,
            $description, $referenceId, $referenceType, $meta, $createdBy
        ) {
            $wallet = $this->getOrCreateWallet($user, $walletType);

            $wallet = Wallet::lockForUpdate()->find($wallet->id);

            $balanceBefore = (float) $wallet->balance;
            $balanceAfter  = $balanceBefore + $amount;

            $wallet->balance = $balanceAfter;
            $wallet->save();

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => $type->value,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description,
                'meta' => $meta ?: null,
                'created_by' => $createdBy,
            ]);
        });
    }

    /**
     * Debit a user's wallet. Throws if insufficient balance.
     */
    public function debit(
        User $user,
        WalletType $walletType,
        float $amount,
        TransactionType $type,
        string $description = '',
        ?int $referenceId = null,
        ?string $referenceType = null,
        array $meta = [],
        ?int $createdBy = null,
    ): WalletTransaction {
        return DB::transaction(function () use (
            $user, $walletType, $amount, $type,
            $description, $referenceId, $referenceType, $meta, $createdBy
        ) {
            $wallet = Wallet::lockForUpdate()
                ->where('user_id', $user->id)
                ->where('type', $walletType->value)
                ->firstOrFail();

            $balanceBefore = (float) $wallet->balance;

            if ($balanceBefore < $amount) {
                throw new \RuntimeException(
                    "Insufficient balance. Available: {$balanceBefore}, Requested: {$amount}"
                );
            }

            $balanceAfter = $balanceBefore - $amount;

            $wallet->balance = $balanceAfter;
            $wallet->save();

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => $type->value,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description,
                'meta' => $meta ?: null,
                'created_by' => $createdBy,
            ]);
        });
    }

    /**
     * Lock balance for a pending withdrawal.
     */
    public function lockBalance(Wallet $wallet, float $amount): void {
        DB::transaction(function () use ($wallet, $amount) {
            $wallet = Wallet::lockForUpdate()->find($wallet->id);

            $available = (float) $wallet->balance - (float) $wallet->locked_balance;

            if ($available < $amount) {
                throw new \RuntimeException("Insufficient available balance to lock.");
            }

            $wallet->locked_balance = (float) $wallet->locked_balance + $amount;
            $wallet->save();
        });
    }

    /**
     * Release locked balance (on withdrawal rejection or cancellation).
     */
    public function releaseLockedBalance(Wallet $wallet, float $amount): void {
        DB::transaction(function () use ($wallet, $amount) {
            $wallet = Wallet::lockForUpdate()->find($wallet->id);
            $wallet->locked_balance = max(0, (float) $wallet->locked_balance - $amount);
            $wallet->save();
        });
    }

    /**
     * Get available (unlocked) balance.
     */
    public function availableBalance(User $user, WalletType $walletType): float {
        $wallet = $user->wallets()->where('type', $walletType->value)->first();
        if (!$wallet) return 0.0;
        return max(0, (float) $wallet->balance - (float) $wallet->locked_balance);
    }

    /**
     * Initialize all wallets for a new user.
     */
    public function initializeUserWallets(User $user): void {
        foreach (WalletType::cases() as $type) {
            $this->getOrCreateWallet($user, $type);
        }
    }

    private function getOrCreateWallet(User $user, WalletType $walletType): Wallet {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id, 'type' => $walletType->value],
            ['balance' => 0, 'locked_balance' => 0, 'currency' => 'USD', 'is_active' => true]
        );
    }
}
