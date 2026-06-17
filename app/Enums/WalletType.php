<?php

namespace App\Enums;

// ─── WalletType ───────────────────────────────────────────────────────────────
enum WalletType: string {
    case MAIN     = 'main';
    case REFERRAL = 'referral';
    case RANK     = 'rank';

    public function label(): string {
        return match($this) {
            self::MAIN     => 'Main Wallet',
            self::REFERRAL => 'Referral Wallet',
            self::RANK     => 'Rank Bonus Wallet',
        };
    }

    public function description(): string {
        return match($this) {
            self::MAIN     => 'Daily earnings and principal',
            self::REFERRAL => 'Referral commissions',
            self::RANK     => 'Rank advancement bonuses',
        };
    }

    public function icon(): string {
        return match($this) {
            self::MAIN     => 'heroicon-o-banknotes',
            self::REFERRAL => 'heroicon-o-users',
            self::RANK     => 'heroicon-o-trophy',
        };
    }
}