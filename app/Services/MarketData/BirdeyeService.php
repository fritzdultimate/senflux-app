<?php
// app/Services/MarketData/BirdeyeService.php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BirdeyeService {
    private const BASE_URL = 'https://public-api.birdeye.so';

    /** Minimum spacing between real Birdeye API calls, system-wide. */
    private const MIN_SECONDS_BETWEEN_CALLS = 240; // 4 minutes

    /** How long a successful result stays "last known" — used as fallback while throttled. No expiry needed really, but capped so very stale data eventually ages out of use. */
    private const LAST_KNOWN_TTL_MINUTES = 60 * 24; // 24h

    private const RATE_GATE_KEY = 'birdeye:last_call_at';

    public function isConfigured(): bool {
        return filled(config('services.birdeye.key'));
    }

    public function fetchHolderCount(string $mintAddress): ?int {
        if (!$this->isConfigured()) {
            return null;
        }

        if (!$this->tryClaimRateSlot()) {
            return $this->lastKnown($mintAddress)['holders'] ?? null;
        }

        $response = Http::timeout(10)
            ->withHeaders(['X-API-KEY' => config('services.birdeye.key'), 'x-chain' => 'solana'])
            ->get(self::BASE_URL . '/defi/token_overview', ['address' => $mintAddress]);

        if ($response->failed()) {
            Log::warning('Birdeye fetch failed', ['mint' => $mintAddress, 'status' => $response->status()]);
            return $this->lastKnown($mintAddress)['holders'] ?? null;
        }

        return $response->json('data.holder');
    }

    public function traderStats(string $mintAddress): array {
        if (!$this->isConfigured()) {
            dd('not configured');
            return $this->defaultTraderStats();
        }

        dd('configured');

        

        try {
            if (!$this->tryClaimRateSlot()) {
                dd('last known', $this->lastKnown($mintAddress));
                return $this->lastKnown($mintAddress) ?? $this->defaultTraderStats();
            }

            $response = Http::timeout(10)->withHeaders([
                'X-API-KEY' => config('services.birdeye.key'),
                'x-chain'   => 'solana',
            ])->get(self::BASE_URL . '/defi/v3/token/trade-data/single', [
                'address' => $mintAddress,
            ]);

            if (!$response->ok()) {
                dd($response->body());
                Log::warning('Birdeye traderStats non-ok response', [
                    'mint' => $mintAddress,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return $this->lastKnown($mintAddress) ?? $this->defaultTraderStats();
            }

            dd('okays', $response->json('data'));

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

                'last_trade_at' => isset($d['last_trade_unix_time'])
                    ? \Carbon\Carbon::createFromTimestamp($d['last_trade_unix_time'])
                    : null,

                'birdeye_synced_at' => now(),
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

    /**
     * Atomically claims the right to make one real API call. Only one
     * caller across the whole app can succeed per MIN_SECONDS_BETWEEN_CALLS
     * window — everyone else this cycle falls back to cached/last-known
     * data instead of hitting Birdeye and tripping another 429.
     */
    private function tryClaimRateSlot(): bool {
        return Cache::lock('birdeye:rate-gate-lock', 5)->block(2, function () {
            $lastCall = Cache::get(self::RATE_GATE_KEY);

            if ($lastCall && now()->diffInSeconds($lastCall) < self::MIN_SECONDS_BETWEEN_CALLS) {
                return false;
            }

            Cache::put(self::RATE_GATE_KEY, now(), now()->addMinutes(10));
            return true;
        });
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