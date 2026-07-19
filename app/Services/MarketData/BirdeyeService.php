<?php
// app/Services/MarketData/BirdeyeService.php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BirdeyeService {
    private const BASE_URL = 'https://public-api.birdeye.so';

    /** How long a successful result stays "last known" — used as fallback on failure. */
    private const LAST_KNOWN_TTL_MINUTES = 60 * 24; // 24h

    public function isConfigured(): bool {
        return filled(config('services.birdeye.key'));
    }

    /**
     * Fetches trader stats for one mint. No internal throttling — the
     * caller (BirdeyeSyncService, run on its own schedule) controls call
     * frequency now. This is a pure API client.
     *
     * IMPORTANT: only plain scalars/strings go into the returned array —
     * never Carbon objects — because this gets cached, and caching a
     * Carbon instance directly leads to __PHP_Incomplete_Class on
     * unserialize if the class isn't autoloaded at read time.
     */
    public function traderStats(string $mintAddress): array {
        if (!$this->isConfigured()) {
            return $this->defaultTraderStats();
        }

        try {
            $response = Http::timeout(10)->withHeaders([
                'X-API-KEY' => config('services.birdeye.key'),
                'x-chain'   => 'solana',
            ])->get(self::BASE_URL . '/defi/v3/token/trade-data/single', [
                'address' => $mintAddress,
            ]);

            if (!$response->ok()) {
                Log::warning('Birdeye traderStats non-ok response', [
                    'mint' => $mintAddress,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return $this->lastKnown($mintAddress) ?? $this->defaultTraderStats();
            }

            $d = $response->json('data') ?? [];

            $stats = [
                'active_wallets' => $d['unique_wallet_24h'] ?? null,
                'holders' => $d['holder'] ?? null,
                'markets' => $d['market'] ?? null,

                'unique_wallets_5m' => $d['unique_wallet_5m'] ?? null,
                'unique_wallets_1h' => $d['unique_wallet_1h'] ?? null,
                'unique_wallets_4h' => $d['unique_wallet_4h'] ?? null,
                'unique_wallets_24h' => $d['unique_wallet_24h'] ?? null,

                'unique_wallets_24h_change_pct' => $d['unique_wallet_24h_change_percent'] ?? null,

                'volume_buy_24h_usd' => $d['volume_buy_24h_usd'] ?? null,
                'volume_sell_24h_usd' => $d['volume_sell_24h_usd'] ?? null,

                'trade_24h' => $d['trade_24h'] ?? null,
                'trade_24h_change_pct' => $d['trade_24h_change_percent'] ?? null,

                'price_change_5m_pct' => $d['price_change_5m_percent'] ?? null,
                'price_change_1h_pct' => $d['price_change_1h_percent'] ?? null,
                'price_change_4h_pct' => $d['price_change_4h_percent'] ?? null,
                'price_change_8h_pct' => $d['price_change_8h_percent'] ?? null,
                'price_change_24h_pct' => $d['price_change_24h_percent'] ?? null,

                // Plain ISO string, NOT a Carbon object — safe to cache.
                'last_trade_at' => isset($d['last_trade_unix_time'])
                    ? \Carbon\Carbon::createFromTimestamp($d['last_trade_unix_time'])->toIso8601String()
                    : null,

                // Plain ISO string, NOT a Carbon object — safe to cache.
                'birdeye_synced_at' => now()->toIso8601String(),
            ];

            $this->rememberLastKnown($mintAddress, $stats);

            return $stats;

        } catch (\Throwable $e) {
            Log::warning('Birdeye traderStats failed', [
                'mint' => $mintAddress,
                'error' => $e->getMessage(),
            ]);

            return $this->lastKnown($mintAddress) ?? $this->defaultTraderStats();
        }
    }

    private function rememberLastKnown(string $mintAddress, array $stats): void {
        Cache::put("birdeye:last-known:{$mintAddress}", $stats, now()->addMinutes(self::LAST_KNOWN_TTL_MINUTES));
    }

    private function lastKnown(string $mintAddress): ?array {
        return Cache::get("birdeye:last-known:{$mintAddress}");
    }

    private function defaultTraderStats(): array {
        return [
            'active_wallets' => null,
            'holders' => null,
            'unique_wallets_24h' => null,
            'unique_wallets_24h_change_pct' => null,
            'volume_buy_24h_usd' => null,
            'volume_sell_24h_usd' => null,
            'birdeye_synced_at' => null,
        ];
    }
}