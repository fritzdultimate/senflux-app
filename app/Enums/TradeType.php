<?php

namespace App\Enums;

enum TradeType: string
{
    case LONG  = 'long';
    case SHORT = 'short';

    public function label(): string
    {
        return match ($this) {
            self::LONG  => 'Long',
            self::SHORT => 'Short',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LONG  => '#22c55e',
            self::SHORT => '#ef4444',
        };
    }
}
