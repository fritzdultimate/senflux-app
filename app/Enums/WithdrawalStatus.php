<?php 

namespace App\Enums;


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
