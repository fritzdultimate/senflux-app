<?php namespace App\Enums;

// ─── WithdrawalStatus ─────────────────────────────────────────────────────────
enum WithdrawalStatus: string {
    case PENDING  = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case PAID     = 'paid';

    public function label(): string {
        return match($this) {
            self::PENDING  => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::PAID     => 'Paid',
        };
    }

    public function color(): string {
        return match($this) {
            self::PENDING  => 'yellow',
            self::APPROVED => 'blue',
            self::REJECTED => 'red',
            self::PAID     => 'green',
        };
    }
}

// ─── TransactionType ──────────────────────────────────────────────────────────
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

// ─── RankLevel ────────────────────────────────────────────────────────────────
enum RankLevel: string {
    case NONE      = 'none';
    case GENESIS   = 'genesis';
    case PIONEER   = 'pioneer';
    case VANGUARD  = 'vanguard';
    case PRIME     = 'prime';
    case EMPIRE    = 'empire';
    case HORIZON   = 'horizon';
    case MONARCH   = 'monarch';
    case APEX      = 'apex';
    case CROWN     = 'crown';
    case SOVEREIGN = 'sovereign';

    public function label(): string {
        return match($this) {
            self::NONE      => 'Unranked',
            self::GENESIS   => 'Genesis',
            self::PIONEER   => 'Pioneer',
            self::VANGUARD  => 'Vanguard',
            self::PRIME     => 'Prime',
            self::EMPIRE    => 'Empire',
            self::HORIZON   => 'Horizon',
            self::MONARCH   => 'Monarch',
            self::APEX      => 'Apex',
            self::CROWN     => 'Crown',
            self::SOVEREIGN => 'Sovereign',
        };
    }

    public function order(): int {
        return match($this) {
            self::NONE      => 0,
            self::GENESIS   => 1,
            self::PIONEER   => 2,
            self::VANGUARD  => 3,
            self::PRIME     => 4,
            self::EMPIRE    => 5,
            self::HORIZON   => 6,
            self::MONARCH   => 7,
            self::APEX      => 8,
            self::CROWN     => 9,
            self::SOVEREIGN => 10,
        };
    }

    public function cashBonus(): float {
        return match($this) {
            self::NONE      => 0,
            self::GENESIS   => 150,
            self::PIONEER   => 500,
            self::VANGUARD  => 1500,
            self::PRIME     => 5000,
            self::EMPIRE    => 10000,
            self::HORIZON   => 50000,
            self::MONARCH   => 100000,
            self::APEX      => 250000,
            self::CROWN     => 500000,
            self::SOVEREIGN => 1000000,
        };
    }

    public function teamVolumeRequired(): float {
        return match($this) {
            self::NONE      => 0,
            self::GENESIS   => 5000,
            self::PIONEER   => 15000,
            self::VANGUARD  => 50000,
            self::PRIME     => 150000,
            self::EMPIRE    => 500000,
            self::HORIZON   => 1000000,
            self::MONARCH   => 2500000,
            self::APEX      => 5000000,
            self::CROWN     => 10000000,
            self::SOVEREIGN => 25000000,
        };
    }

    public function personalDepositRequired(): float {
        return match($this) {
            self::NONE      => 0,
            self::GENESIS   => 500,
            self::PIONEER   => 1000,
            self::VANGUARD  => 2000,
            self::PRIME     => 5000,
            self::EMPIRE    => 10000,
            self::HORIZON   => 15000,
            self::MONARCH   => 25000,
            self::APEX      => 50000,
            self::CROWN     => 100000,
            self::SOVEREIGN => 150000,
        };
    }

    public function directReferralsRequired(): int {
        return match($this) {
            self::NONE      => 0,
            self::GENESIS   => 2,
            self::PIONEER   => 3,
            self::VANGUARD  => 5,
            self::PRIME     => 6,
            self::EMPIRE    => 8,
            self::HORIZON   => 10,
            self::MONARCH   => 12,
            self::APEX      => 15,
            self::CROWN     => 20,
            self::SOVEREIGN => 25,
        };
    }

    public function next(): ?self {
        $cases = self::cases();
        $current = $this->order();
        foreach ($cases as $case) {
            if ($case->order() === $current + 1) return $case;
        }
        return null;
    }

    public function color(): string {
        return match($this) {
            self::NONE      => '#6b7280',
            self::GENESIS   => '#22c55e',
            self::PIONEER   => '#06b6d4',
            self::VANGUARD  => '#f59e0b',
            self::PRIME     => '#f97316',
            self::EMPIRE    => '#ef4444',
            self::HORIZON   => '#8b5cf6',
            self::MONARCH   => '#ec4899',
            self::APEX      => '#6366f1',
            self::CROWN     => '#fbbf24',
            self::SOVEREIGN => '#9B7DFF',
        };
    }
}
