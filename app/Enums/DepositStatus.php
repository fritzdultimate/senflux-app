<?php namespace App\Enums;

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