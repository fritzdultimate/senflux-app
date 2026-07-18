<?php

namespace App\Enums;

enum FormationEventType: string {
    case DETECTED = 'detected';
    case STATE_CHANGED = 'state_changed';
    case CAPITAL_CONCENTRATION = 'capital_concentration_detected';
    case WALLET_CLUSTER = 'wallet_cluster_identified';
    case LIQUIDITY_INCREASING = 'liquidity_increasing';
    case DEPLOYMENT_INITIATED = 'deployment_initiated';
    case EXPOSURE_REDUCED = 'exposure_reduced';
    case MATURED = 'matured';

    /**
     * Default message templates. FormationEventLogger can override with
     * a custom message when the caller has more specific context (e.g.
     * "14 wallets" instead of just "Wallet cluster identified").
     */
    public function defaultMessage(): string {
        return match ($this) {
            self::DETECTED              => 'New formation detected',
            self::STATE_CHANGED         => 'Formation state changed',
            self::CAPITAL_CONCENTRATION => 'New capital concentration detected',
            self::WALLET_CLUSTER        => 'Wallet cluster identified',
            self::LIQUIDITY_INCREASING  => 'Liquidity increasing',
            self::DEPLOYMENT_INITIATED  => 'Deployment initiated',
            self::EXPOSURE_REDUCED      => 'Exposure reduced',
            self::MATURED => 'Reached sustained maturity',
        };
    }

    public function category(): string {
        return match ($this) {
            self::DETECTED => 'detected',
            self::STATE_CHANGED => 'state_change',
            self::CAPITAL_CONCENTRATION, self::WALLET_CLUSTER, self::LIQUIDITY_INCREASING => 'signal',
            self::DEPLOYMENT_INITIATED, self::EXPOSURE_REDUCED => 'deployment',
            self::MATURED => 'sustained',
        };
    }
}