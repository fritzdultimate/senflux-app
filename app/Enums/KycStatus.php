<?php

namespace App\Enums;

enum KycStatus: string {
    case UNSUBMITTED = 'unsubmitted';
    case PENDING     = 'pending';
    case APPROVED    = 'approved';
    case REJECTED    = 'rejected';
    case EXPIRED     = 'expired';

    public function label(): string {
        return match($this) {
            self::UNSUBMITTED => 'Not Submitted',
            self::PENDING     => 'Under Review',
            self::APPROVED    => 'Verified',
            self::REJECTED    => 'Rejected',
            self::EXPIRED     => 'Expired',
        };
    }

    public function color(): string {
        return match($this) {
            self::UNSUBMITTED => 'gray',
            self::PENDING     => 'yellow',
            self::APPROVED    => 'green',
            self::REJECTED    => 'red',
            self::EXPIRED     => 'orange',
        };
    }
}
