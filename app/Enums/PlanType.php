<?php

namespace App\Enums;

enum PlanType: string {
    case CORE = 'core';
    case PRO  = 'pro';
    case APEX = 'apex';

    public function label(): string {
        return match($this) {
            self::CORE => 'Core',
            self::PRO  => 'Pro',
            self::APEX => 'Apex',
        };
    }

    public function dailyRateMax(): float {
        return match($this) {
            self::CORE => 0.006,
            self::PRO  => 0.009,
            self::APEX => 0.013,
        };
    }

    public function monthlyPrice(): float {
        return match($this) {
            self::CORE => 20.00,
            self::PRO  => 50.00,
            self::APEX => 100.00,
        };
    }

    public function isPopular(): bool {
        return $this === self::PRO;
    }
}
