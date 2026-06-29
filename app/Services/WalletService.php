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
     * Spend from a wallet, automatically releasing the corresponding
     * amount of locked_balance so it doesn't go stale — without this,
     * spending locked deposit funds (e.g. buying a pack) would leave
     * locked_balance referencing money that's already gone, eventually
     * blocking legitimate withdrawals of money that should be free.
     *
     * Locked funds are spent first; only spills into already-unlocked
     * balance if locked funds run short. The portion that came from
     * locked_balance is recorded on the transaction (locked_portion) so
     * a later refund can be an exact reversal, not a flat credit.
     */
    public function debitRespectingLock(
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
            $lockedPortion = min($amount, (float) $wallet->locked_balance);

            $transaction = $this->debit(
                user: $user,
                walletType: $walletType,
                amount: $amount,
                type: $type,
                description: $description,
                referenceId: $referenceId,
                referenceType: $referenceType,
                meta: $meta,
                createdBy: $createdBy,
            );

            if ($lockedPortion > 0) {
                $this->releaseLockedBalance($wallet, $lockedPortion);
            }

            $transaction->update(['locked_portion' => $lockedPortion]);

            return $transaction->refresh();
        });
    }

    /**
     * Exactly reverses a debit previously made via debitRespectingLock()
     * — credits the full amount back, then re-locks however much of it
     * was locked originally (read from the original transaction's
     * locked_portion, not recomputed). This is what makes a refund safe:
     * money that was locked before the spend comes back locked,
     * regardless of how much time has passed or what else happened to
     * the wallet in between.
     *
     * Caller is responsible for confirming refund eligibility (e.g.
     * "zero slots funded yet") before calling this — this method only
     * handles the money movement correctly, not the business rule for
     * whether a refund is allowed.
     */
    public function reverseDebit(
        WalletTransaction $originalDebit,
        TransactionType $refundType,
        string $description = '',
        ?int $createdBy = null,
    ): WalletTransaction {
        return DB::transaction(function () use ($originalDebit, $refundType, $description, $createdBy) {
            $user = $originalDebit->user;
            $walletType = $originalDebit->wallet->type;
            $amount = (float) $originalDebit->amount;
            $lockedPortion = (float) ($originalDebit->locked_portion ?? 0);

            $refundTransaction = $this->credit(
                user: $user,
                walletType: $walletType,
                amount: $amount,
                type: $refundType,
                description: $description ?: "Refund of transaction #{$originalDebit->id}",
                referenceId: $originalDebit->id,
                referenceType: WalletTransaction::class,
                meta: ['reversed_transaction_id' => $originalDebit->id],
                createdBy: $createdBy,
            );

            if ($lockedPortion > 0) {
                $wallet = $this->getOrCreateWallet($user, $walletType);
                $this->lockBalance($wallet, $lockedPortion);
            }

            return $refundTransaction;
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