<?php
// app/Enums/TradeActivitySource.php

namespace App\Enums;

enum TradeActivitySource: string {
    case MARKET_POOL = 'market_pool'; // real public trades on the token's pool — not Senflux's own
    case SENFLUX = 'senflux';         // real Senflux-executed trades, once that exists

    public function label(): string {
        return match ($this) {
            self::MARKET_POOL => 'Public Pool Activity',
            self::SENFLUX => 'Senflux Execution',
        };
    }

    /**
     * The exact sentence that prevents this from becoming a fraud
     * problem — always rendered next to any market_pool trade so no one
     * mistakes public trading activity for Senflux's own. Never omit
     * this in any view showing MARKET_POOL rows.
     */
    public function disclosure(): ?string {
        return match ($this) {
            self::MARKET_POOL => 'Public transaction on this token\'s liquidity pool — not executed by Senflux.',
            self::SENFLUX => null,
        };
    }
}