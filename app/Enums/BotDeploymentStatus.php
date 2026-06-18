<?php

namespace App\Enums;

enum BotDeploymentStatus: string {
    case STANDBY    = 'standby';
    case DETECTING  = 'detecting';
    case VALIDATING = 'validating';
    case DEPLOYED   = 'deployed';
    case REDUCING   = 'reducing';
    case PAUSED     = 'paused';

    public function label(): string {
        return match($this) {
            self::STANDBY    => 'Standby',
            self::DETECTING  => 'Detecting',
            self::VALIDATING => 'Validating',
            self::DEPLOYED   => 'Deployed',
            self::REDUCING   => 'Reducing',
            self::PAUSED     => 'Paused',
        };
    }

    public function isActive(): bool {
        return in_array($this, [self::DETECTING, self::VALIDATING, self::DEPLOYED]);
    }
}