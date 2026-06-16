<?php namespace App\Enums;

// ─── PlanInterval ─────────────────────────────────────────────────────────────
enum PlanInterval: string {
    case MONTHLY   = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY    = 'yearly';

    public function label(): string {
        return match($this) {
            self::MONTHLY   => 'Monthly',
            self::QUARTERLY => 'Quarterly',
            self::YEARLY    => 'Yearly',
        };
    }

    public function months(): int {
        return match($this) {
            self::MONTHLY   => 1,
            self::QUARTERLY => 3,
            self::YEARLY    => 12,
        };
    }
}

// ─── DepositStatus ────────────────────────────────────────────────────────────
enum DepositStatus: string {
    case PENDING    = 'pending';
    case WAITING    = 'waiting';
    case CONFIRMING = 'confirming';
    case CONFIRMED  = 'confirmed';
    case ACTIVE     = 'active';
    case FINISHED   = 'finished';
    case FAILED     = 'failed';
    case EXPIRED    = 'expired';
    case REFUNDED   = 'refunded';

    public function label(): string {
        return match($this) {
            self::PENDING    => 'Pending',
            self::WAITING    => 'Awaiting Payment',
            self::CONFIRMING => 'Confirming',
            self::CONFIRMED  => 'Confirmed',
            self::ACTIVE     => 'Active',
            self::FINISHED   => 'Finished',
            self::FAILED     => 'Failed',
            self::EXPIRED    => 'Expired',
            self::REFUNDED   => 'Refunded',
        };
    }

    public function color(): string {
        return match($this) {
            self::PENDING    => 'yellow',
            self::WAITING    => 'blue',
            self::CONFIRMING => 'indigo',
            self::CONFIRMED  => 'cyan',
            self::ACTIVE     => 'green',
            self::FINISHED   => 'gray',
            self::FAILED     => 'red',
            self::EXPIRED    => 'orange',
            self::REFUNDED   => 'pink',
        };
    }

    public function isEarning(): bool {
        return $this === self::ACTIVE;
    }

    public function isTerminal(): bool {
        return in_array($this, [self::FINISHED, self::FAILED, self::EXPIRED, self::REFUNDED]);
    }

    /** Map NowPayments payment_status to our DepositStatus */
    public static function fromNowPayments(string $npStatus): self {
        return match($npStatus) {
            'waiting'             => self::WAITING,
            'confirming'          => self::CONFIRMING,
            'confirmed', 'sending', 'partially_paid' => self::CONFIRMING,
            'finished'            => self::CONFIRMED,
            'failed'              => self::FAILED,
            'refunded'            => self::REFUNDED,
            'expired'             => self::EXPIRED,
            default               => self::PENDING,
        };
    }
}

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

// ─── MarketFormationState ─────────────────────────────────────────────────────
enum MarketFormationState: string {
    case IDLE      = 'idle';
    case EARLY     = 'early';
    case BUILDING  = 'building';
    case ACTIVE    = 'active';
    case WEAKENING = 'weakening';

    public function label(): string {
        return match($this) {
            self::IDLE      => 'Idle',
            self::EARLY     => 'Early',
            self::BUILDING  => 'Building',
            self::ACTIVE    => 'Active',
            self::WEAKENING => 'Weakening',
        };
    }

    public function description(): string {
        return match($this) {
            self::IDLE      => 'Minimal Meaningful Participation',
            self::EARLY     => 'Initial Participation Beginning To Emerge',
            self::BUILDING  => 'Participation Density Increasing Consistently',
            self::ACTIVE    => 'Sustained Formation Confirmed',
            self::WEAKENING => 'Participation Beginning To Fade',
        };
    }

    /** Rate multiplier applied to daily earnings */
    public function earningsMultiplier(): float {
        return match($this) {
            self::IDLE      => 0.5,
            self::EARLY     => 0.75,
            self::BUILDING  => 0.9,
            self::ACTIVE    => 1.0,
            self::WEAKENING => 0.6,
        };
    }

    public function color(): string {
        return match($this) {
            self::IDLE      => '#6b7280',
            self::EARLY     => '#06b6d4',
            self::BUILDING  => '#f59e0b',
            self::ACTIVE    => '#22c55e',
            self::WEAKENING => '#ef4444',
        };
    }
}

// ─── BotDeploymentStatus ──────────────────────────────────────────────────────
enum BotDeploymentStatus: string {
    case STANDBY    = 'standby';
    case DETECTING  = 'detecting';
    case VALIDATING = 'validating';
    case DEPLOYED   = 'deployed';
    case REDUCING   = 'reducing';
    case PAUSED     = 'paused';

    public function label(): string {
        return match($this) {
            self::STANDBY    => 'Standby',
            self::DETECTING  => 'Detecting',
            self::VALIDATING => 'Validating',
            self::DEPLOYED   => 'Deployed',
            self::REDUCING   => 'Reducing',
            self::PAUSED     => 'Paused',
        };
    }

    public function isActive(): bool {
        return in_array($this, [self::DETECTING, self::VALIDATING, self::DEPLOYED]);
    }
}
