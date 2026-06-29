<?php

namespace App\Enums;

enum PackSubscriptionStatus: string {
    case ACTIVE             = 'active';
    case IN_RENEWAL_WINDOW  = 'in_renewal_window';
    case RENEWED            = 'renewed';
    case CLOSED             = 'closed';
    case EXPIRED            = 'expired';
    case REFUNDED           = 'refunded';

    public function label(): string {
        return match($this) {
            self::ACTIVE            => 'Active',
            self::IN_RENEWAL_WINDOW => 'Renewal Window',
            self::RENEWED           => 'Renewed',
            self::CLOSED            => 'Closed',
            self::EXPIRED           => 'Expired',
            self::REFUNDED          => 'Refunded',
        };
    }

    public function isTerminal(): bool {
        return in_array($this, [self::CLOSED, self::EXPIRED, self::REFUNDED, self::RENEWED]);
    }
}