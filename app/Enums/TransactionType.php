<?php

namespace App\Enums;

enum TransactionType: string {
    case DEPOSIT          = 'deposit';
    case WITHDRAWAL       = 'withdrawal';
    case DAILY_EARNING    = 'daily_earning';
    case REFERRAL_BONUS   = 'referral_bonus';
    case RANK_BONUS       = 'rank_bonus';
    case LEADERSHIP_MATCH = 'leadership_match';
    case FEE              = 'fee';
    case ADJUSTMENT       = 'adjustment';

    public function label(): string {
        return match($this) {
            self::DEPOSIT          => 'Deposit',
            self::WITHDRAWAL       => 'Withdrawal',
            self::DAILY_EARNING    => 'Daily Earning',
            self::REFERRAL_BONUS   => 'Referral Bonus',
            self::RANK_BONUS       => 'Rank Bonus',
            self::LEADERSHIP_MATCH => 'Leadership Match',
            self::FEE              => 'Fee',
            self::ADJUSTMENT       => 'Admin Adjustment',
        };
    }

    public function isCredit(): bool {
        return in_array($this, [
            self::DEPOSIT,
            self::DAILY_EARNING,
            self::REFERRAL_BONUS,
            self::RANK_BONUS,
            self::LEADERSHIP_MATCH,
        ]);
    }
}