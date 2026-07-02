<?php
// app/Services/MarketData/CachedMarketDataService.php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Cache;

class CachedMarketDataService
{
    public function __construct(private DexScreenerService $dexScreener) {}

    /**
     * 60s cache — realistic "live" feel without hammering the API on
     * every page load/poll cycle. Bump to 5-10 min once wired for real;
     * 60s is fine for testing.
     */
    public function summarize(string $mintAddress): ?array {
        return Cache::remember(
            "market-data:{$mintAddress}",
            now()->addSeconds(60),
            fn () => $this->dexScreener->summarize($mintAddress),
        );
    }

    public function summarizeAll(): array {
        $results = [];

        foreach (DexScreenerService::TOKENS as $symbol => $mint) {
            $results[$symbol] = $this->summarize($mint);
        }

        return $results;
    }
}