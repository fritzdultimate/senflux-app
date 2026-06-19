<?php

namespace App\Enums;

enum TradeStatus: string
{
    case OPEN   = 'open';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN   => 'Open',
            self::CLOSED => 'Closed',
        };
    }
}
