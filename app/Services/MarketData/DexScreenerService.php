<?php
// app/Services/MarketData/DexScreenerService.php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DexScreenerService
{
    private const BASE_URL = 'https://api.dexscreener.com';

    /**
     * Well-known Solana mint addresses for this test — not user input,
     * so no need to validate/sanitize these.
     */
    public const TOKENS = [
        'WIF'  => 'EKpQGSJtjMFqKZ9KQanSqYXRcF8fBopzLHYxdM65zcjm',
        'BONK' => 'DezXAZ8z7PnrnRJjz3wXBoRgixCa6xjnB7YaB1pPB263',
    ];

    /**
     * Returns every liquidity pool this token trades in, sorted by
     * liquidity descending — the first result is the "main" pool most
     * dashboards would treat as canonical.
     */
    public function fetchPairs(string $mintAddress): array {
        $response = Http::timeout(10)
            ->retry(2, 500)
            ->get(self::BASE_URL . "/tokens/v1/solana/{$mintAddress}");

        if ($response->failed()) {
            Log::warning('DexScreener fetch failed', ['mint' => $mintAddress, 'status' => $response->status()]);
            return [];
        }

        $pairs = $response->json() ?? [];

        usort($pairs, fn ($a, $b) => ($b['liquidity']['usd'] ?? 0) <=> ($a['liquidity']['usd'] ?? 0));

        return $pairs;
    }

    /**
     * Just the top (most liquid) pool — this is what a "current price /
     * current liquidity" widget would actually display.
     */
    public function fetchTopPair(string $mintAddress): ?array {
        return $this->fetchPairs($mintAddress)[0] ?? null;
    }

    /**
     * Flattens DexScreener's raw shape into exactly the fields a
     * formation-style card would want to show. This is the translation
     * layer between "what the API gives us" and "what our UI needs" —
     * keep it here so nothing downstream touches raw API shape.
     */
    public function summarize(string $mintAddress): ?array {
        $pair = $this->fetchTopPair($mintAddress);

        if (!$pair) {
            return null;
        }

        // dd($pair);

        return [
            'symbol' => $pair['baseToken']['symbol'] ?? null,
            'name' => $pair['baseToken']['name'] ?? null,
            'price_usd' => (float) ($pair['priceUsd'] ?? 0),
            'liquidity_usd' => (float) ($pair['liquidity']['usd'] ?? 0),
            'volume_24h' => (float) ($pair['volume']['h24'] ?? 0),
            'buys_24h' => (int) ($pair['txns']['h24']['buys'] ?? 0),
            'sells_24h' => (int) ($pair['txns']['h24']['sells'] ?? 0),
            'price_change_24h' => (float) ($pair['priceChange']['h24'] ?? 0),
            'dex' => $pair['dexId'] ?? null,
            'pair_address' => $pair['pairAddress'] ?? null,
            'pair_url' => $pair['url'] ?? null,
            'fetched_at' => now()->toDateTimeString(),

            'price_change_5m' => $pair['priceChange']['m5'] ?? null,
            'price_change_1h' => $pair['priceChange']['h1'] ?? null,
            'price_change_6h' => $pair['priceChange']['h6'] ?? null,

            'volume_5m' => $pair['volume']['m5'] ?? null,
            'volume_1h' => $pair['volume']['h1'] ?? null,
            'volume_6h' => $pair['volume']['h6'] ?? null,

            'buys_5m'  => $pair['txns']['m5']['buys']  ?? null,
            'sells_5m' => $pair['txns']['m5']['sells'] ?? null,
            'buys_1h'  => $pair['txns']['h1']['buys']  ?? null,
            'sells_1h' => $pair['txns']['h1']['sells'] ?? null,
            'buys_6h'  => $pair['txns']['h6']['buys']  ?? null,
            'sells_6h' => $pair['txns']['h6']['sells'] ?? null,

            'fdv'        => $pair['fdv'] ?? null,
            'market_cap' => $pair['marketCap'] ?? null,

            'image_url' => $pair['info']['imageUrl'] ?? null,
            'header' => $pair['info']['header'] ?? null,
            'open_graph' => $pair['info']['openGraph'] ?? null,
        ];
    }
}