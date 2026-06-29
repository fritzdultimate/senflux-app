<?php

namespace App\Enums;

enum PackSlotStatus: string {
    case EMPTY  = 'empty';
    case FUNDED = 'funded';
    case CLOSED = 'closed';

    public function label(): string {
        return match($this) {
            self::EMPTY  => 'Empty',
            self::FUNDED => 'Funded',
            self::CLOSED => 'Closed',
        };
    }
}