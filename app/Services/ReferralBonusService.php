<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\Deposit;
use App\Models\Referral;
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
     * Walk 8 levels of upline and credit referral bonuses.
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

    private function getDirectUpline(User $user): ?User {
        return $user->referredBy ?? null;
    }
}