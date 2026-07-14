<?php

namespace App\Enums;

enum FormationState: string {
    case IDLE      = 'idle';
    case EARLY     = 'early';
    case BUILDING  = 'building';
    case ACTIVE    = 'active';
    case MATURE    = 'mature';
    case WEAKENING = 'weakening';

    public function label(): string {
        return match($this) {
            self::IDLE      => 'Idle',
            self::EARLY     => 'Early',
            self::BUILDING  => 'Building',
            self::ACTIVE    => 'Active',
            self::MATURE    => 'Mature',
            self::WEAKENING => 'Weakening',
        };
    }

    public function description(): string {
        return match($this) {
            self::IDLE      => 'Very little meaningful activity.',
            self::EARLY     => 'Initial capital participation detected.',
            self::BUILDING  => 'Liquidity and participation continue strengthening.',
            self::ACTIVE    => 'Formation validated. Eligible for deployment.',
            self::MATURE    => 'Broader market participation increasing. Monitoring for deterioration.',
            self::WEAKENING => 'Capital concentration declining. Exposure reduction initiated.',
        };
    }

    public function color(): string {
        return match($this) {
            self::IDLE      => '#6b7280',
            self::EARLY     => '#06b6d4',
            self::BUILDING  => '#a855f7',
            self::ACTIVE    => '#22c55e',
            self::MATURE    => '#f59e0b',
            self::WEAKENING => '#ef4444', 
        };
    }

   
    public function earningsMultiplier(): float {
        return match($this) {
            self::IDLE      => 0.0,  // not deployable — see Formation::isDeployable()
            self::EARLY     => 0.5,
            self::BUILDING  => 0.75,
            self::ACTIVE    => 1.0,
            self::MATURE    => 0.85,
            self::WEAKENING => 0.4,
        };
    }

    /**
     * Per the PDF: "Eligible For Deployment" only applies once a
     * formation reaches ACTIVE. MATURE and WEAKENING are existing
     * deployments being monitored/unwound, not states a NEW slot should
     * be matched into.
     */
    public function acceptsNewDeployments(): bool {
        return $this === self::ACTIVE;
    }

    public function priority(): int {
        return match($this) {
            self::ACTIVE    => 0,
            self::BUILDING  => 1,
            self::MATURE    => 2,
            self::EARLY     => 3,
            self::WEAKENING => 4,
            self::IDLE      => 5,
        };
    }
}
