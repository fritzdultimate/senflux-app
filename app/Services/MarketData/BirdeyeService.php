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
}