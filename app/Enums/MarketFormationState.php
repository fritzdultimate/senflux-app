<?php

namespace App\Enums;

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