<?php

namespace App\Enums;

enum SignalType: string
{
    case BUY   = 'buy';
    case SELL  = 'sell';
    case WATCH = 'watch';

    public function label(): string
    {
        return match ($this) {
            self::BUY   => 'Buy',
            self::SELL  => 'Sell',
            self::WATCH => 'Watch',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BUY   => '#22c55e',
            self::SELL  => '#ef4444',
            self::WATCH => '#f59e0b',
        };
    }
}
