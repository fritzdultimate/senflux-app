<?php

namespace App\Services\MarketData;

use App\Enums\TradeActivitySource;
use App\Models\Formation;
use App\Models\FormationTradeActivity;
use Illuminate\Support\Facades\Cache;

class FormationTradeActivitySyncService {

    private const CURSOR_CACHE_KEY = 'formation_trade_sync:last_id';
    public function __construct(
        private SolanaRpcService $rpc,
        private HeliusService $helius,
    ) {}

    public function syncOne(Formation $formation): int {
        if (!$formation->pair_address) {
            return 0;
        }

        $signatures = $this->rpc->fetchRecentSignatures($formation->pair_address, 5);
        $new = 0;

        // Only fetch signatures not already stored — avoids re-parsing
        // (and re-billing Helius credits for) the same transactions
        // every 5-minute cycle.
        $newSignatures = collect($signatures)
            ->pluck('signature')
            ->reject(fn ($sig) => FormationTradeActivity::where('tx_signature', $sig)->exists())
            ->values()
            ->all();


        if (empty($newSignatures)) {
            return 0;
        }

        // $parsedBatch = $this->helius->isConfigured()
        //     ? collect($this->helius->parseTransactions($newSignatures))->keyBy('signature')
        //     : collect();


        foreach ($signatures as $sig) {
            if (!in_array($sig['signature'], $newSignatures, true)) {
                continue;
            }

            $tx = $this->rpc->fetchTransactionDetail($sig['signature']);
            $ext = $this->rpc->extractSwapInfo($tx, $formation->mint_address);

            // $parsed = $parsedBatch->get($sig['signature']);
            // $swap = $parsed ? $this->helius->extractSwapInfo($parsed) : null;

            FormationTradeActivity::create([
                'formation_id' => $formation->id,
                'tx_signature' => $sig['signature'],
                'slot' => $sig['slot'] ?? null,
                'block_time' => isset($sig['blockTime']) ? \Carbon\Carbon::createFromTimestamp($sig['blockTime']) : null,
                'source' => TradeActivitySource::SENFLUX->value,
                'failed' => (bool) ($sig['err'] ?? false),
                'type' => $ext['type'] ?? null,
                'token_amount' => $ext['token_amount'] ?? null,
                'trader_wallet' => $ext['wallet'] ?? null,
            ]);

            $new++;
        }

        return $new;
    }

    public function syncAll(int $limit = 3): int {
        $total = 0;
        $lastId = Cache::get(self::CURSOR_CACHE_KEY, 0);

        $formations = Formation::active()
            ->whereIn('state', ['active', 'building', 'mature'])
            ->whereNotNull('pair_address')
            ->where('id', '>', $lastId)
            ->orderBy('id')
            ->limit($limit)
            ->get();

        if ($formations->count() < $limit) {
            $remaining = $limit - $formations->count();

            $wrapped = Formation::active()
                ->whereNotNull('pair_address')
                ->where('id', '<=', $lastId)
                ->orderBy('id')
                ->limit($remaining)
                ->get();

            $formations = $formations->concat($wrapped);
        }

        if ($formations->isEmpty()) {
            Cache::forget(self::CURSOR_CACHE_KEY);
            return 0;
        }

        foreach ($formations as $formation) {
            $total += $this->syncOne($formation);
        }

        Cache::put(self::CURSOR_CACHE_KEY, $formations->last()->id, now()->addDays(7));

        // dd($lastId);

        return $total;
    }
}