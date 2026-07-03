<?php
// app/Services/MarketData/HeliusService.php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HeliusService
{
    public function isConfigured(): bool {
        return filled(config('services.helius.key'));
    }

    /**
     * Parses up to 100 signatures per call into human-readable form —
     * batch these rather than one-at-a-time where possible. Returns []
     * (not null) on failure/unconfigured so callers can safely loop.
     */
    public function parseTransactions(array $signatures): array {
        if (!$this->isConfigured() || empty($signatures)) {
            return [];
        }

        $response = Http::timeout(15)
            ->post('https://api-mainnet.helius-rpc.com/v0/transactions?api-key=' . config('services.helius.key'), [
                'transactions' => array_values($signatures),
            ]);

        if ($response->failed()) {
            Log::warning('Helius parse failed', ['status' => $response->status(), 'body' => $response->body()]);
            return [];
        }

        return $response->json() ?? [];
    }

    /**
     * Extracts exactly what a formation's trade feed needs from one
     * Helius-parsed transaction. Returns null for anything that isn't
     * actually a swap (LP deposits, transfers, etc. get filtered here).
     */
    public function extractSwapInfo(array $parsedTx): ?array {
        if (($parsedTx['type'] ?? null) !== 'SWAP') {
            return null;
        }

        $transfers = $parsedTx['tokenTransfers'] ?? [];
        if (count($transfers) < 1) {
            return null;
        }

        // First transfer is conventionally what the trader received —
        // good enough signal for buy/sell direction without needing to
        // know which mint is "the" token vs SOL/USDC on the other side.
        $primary = $transfers[0];

        return [
            'type' => 'swap',
            'wallet' => $parsedTx['feePayer'] ?? null,
            'token_amount' => (float) ($primary['tokenAmount'] ?? 0),
            'mint' => $primary['mint'] ?? null,
            'description' => $parsedTx['description'] ?? null,
        ];
    }
}