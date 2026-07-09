<?php
// app/Services/MarketData/BirdeyeService.php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BirdeyeService {
    private const BASE_URL = 'https://public-api.birdeye.so';

    public function isConfigured(): bool {
        return filled(config('services.birdeye.key'));
    }

    /**
     * Returns holder count from Birdeye's token overview. Returns null
     * (not 0) when unconfigured or on failure — a missing key should
     * never masquerade as "zero wallets."
     */
    public function fetchHolderCount(string $mintAddress): ?int {
        if (!$this->isConfigured()) {
            return null;
        }

        $response = Http::timeout(10)
            ->withHeaders(['X-API-KEY' => config('services.birdeye.key'), 'x-chain' => 'solana'])
            ->get(self::BASE_URL . '/defi/token_overview', ['address' => $mintAddress]);

        if ($response->failed()) {
            Log::warning('Birdeye fetch failed', ['mint' => $mintAddress, 'status' => $response->status()]);
            return null;
        }

        return $response->json('data.holder');
    }

    public function traderStats(string $mintAddress): ?array {
        if (!$this->isConfigured()) {
            return null;
        }

        try {

            $response = Http::withHeaders([
                'X-API-KEY' => config('services.birdeye.key'),
                'x-chain'   => 'solana',
            ])->get(self::BASE_URL . '/defi/v3/token/trade-data/single', [
                'address' => $mintAddress,
            ]);

            if (!$response->ok()) {
                // dd('not successful');
                return null;
            }

            $d = $response->json('data') ?? [];

            if (empty($d)) {
                return null;
            }

            return [
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
            ];
        } catch(\Throwable $e) {
            Log::warning('Birdeye traderStats failed', [
                'mint' => $mintAddress,
                'error' => $e->getMessage()
            ]);

            return $this->defaultTraderStats();
        }
    }

    private function defaultTraderStats() {
        return [
            'active_wallets' => null,
            'holders' => null,
            'unique_wallets_24h' =>  null,
            'unique_wallets_24h_change_pct' => null,
            'volume_buy_24h_usd' => null,
            'volume_sell_24h_usd' => null,
            'birdeye_synced_at' => null,
        ];
    }
}