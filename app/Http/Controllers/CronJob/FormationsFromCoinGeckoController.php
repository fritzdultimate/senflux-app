<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Controller;
use App\Models\FormationWatchlistItem;
use App\Services\MarketData\DexScreenerService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FormationsFromCoinGeckoController extends Controller
{
    protected array $sectorCategoryMap = [
        'ai-agents' => 'ai_agents',
        'depin' => 'depin',
        'gaming' => 'gaming',
        'real-world-assets-rwa' => 'rwa',
        'non-fungible-tokens-nft' => 'nft',
        'decentralized-finance-defi' => 'defi',
        'infrastructure' => 'infrastructure',
        'solana-meme-coins' => 'memecoins',
    ];

    protected const PER_PAGE = 10;
    protected const CURSOR_TTL_MINUTES = 10;
    protected const MAX_PAGES = 50; // ~500 coins ceiling — raise if your Solana-ecosystem list is bigger

    public function run(DexScreenerService $dexScreener) {
        if (Cache::get('coingecko_seed:done')) {
            return response()->json(['status' => 'done', 'message' => 'Seed already complete. Hit /cron/coingecko/reset to restart.']);
        }

        // Prevents two overlapping requests (e.g. a slow page reload + a poll)
        // from racing on the same cursor and double-advancing it.
        $lock = Cache::lock('coingecko_seed:lock', 25);
        if (!$lock->get()) {
            return response()->json(['status' => 'busy', 'message' => 'Another request is already processing this batch.']);
        }

        try {
            return $this->processBatch($dexScreener);
        } finally {
            $lock->release();
        }
    }

    public function reset() {
        Cache::forget('coingecko_seed:done');
        Cache::forget('coingecko_seed:page');

        return response()->json(['status' => 'reset']);
    }

    protected function processBatch(DexScreenerService $dexScreener) {
        $page = Cache::get('coingecko_seed:page', 1);

        if ($page > self::MAX_PAGES) {
            Cache::put('coingecko_seed:done', true, now()->addDays(30));
            return response()->json(['status' => 'done', 'message' => 'Reached max pages.']);
        }

        $response = Http::timeout(15)->get('https://api.coingecko.com/api/v3/coins/markets', [
            'vs_currency' => 'usd',
            'category' => 'solana-ecosystem',
            'per_page' => self::PER_PAGE,
            'page' => $page,
            'order' => 'market_cap_desc',
        ]);

        if ($response->status() === 429) {
            // Don't advance the cursor — next request retries this same page.
            return response()->json(['status' => 'rate_limited', 'page' => $page]);
        }

        $coins = $response->json();

        if (!is_array($coins) || !array_is_list($coins)) {
            Log::warning('CoinGecko seed: unexpected markets response shape', ['body' => $coins]);
            return response()->json(['status' => 'error', 'page' => $page], 502);
        }

        if (empty($coins)) {
            Cache::put('coingecko_seed:done', true, now()->addDays(30));
            return response()->json(['status' => 'done', 'message' => 'No more coins.']);
        }

        $seeded = 0;
        $results = [];

        foreach ($coins as $coin) {
            if (!isset($coin['id'])) {
                continue;
            }

            $detailResponse = Http::timeout(10)->get("https://api.coingecko.com/api/v3/coins/{$coin['id']}");

            if ($detailResponse->status() === 429) {
                Log::info('CoinGecko 429 mid-batch on coin detail', ['coin' => $coin['id'], 'page' => $page]);
                continue; // skip this coin, still finish the rest of the batch
            }

            $detail = $detailResponse->json();
            $mint = $detail['platforms']['solana'] ?? null;

            if (!$mint) {
                continue;
            }

            $categories = $detail['categories'] ?? [];
            $sector = null;
            foreach ($this->sectorCategoryMap as $cgCategory => $ourSector) {
                if (in_array($cgCategory, $categories, true)) {
                    $sector = $ourSector;
                    break;
                }
            }
            $sector ??= 'infrastructure';

            $pair = $dexScreener->summarize($mint);
            if (!$pair || ($pair['liquidity_usd'] ?? 0) < 1000) {
                continue;
            }

            FormationWatchlistItem::updateOrCreate(
                ['mint_address' => $mint],
                ['sector' => $sector, 'token_symbol' => strtoupper($coin['symbol'])]
            );

            $seeded++;
            $results[] = ['symbol' => $coin['symbol'], 'sector' => $sector, 'mint' => $mint];
        }

        // Cursor for next hit — 10 min TTL as requested. If nobody hits this
        // route again within 10 minutes, the next request restarts at page 1
        // rather than resuming; that's the tradeoff of a short TTL vs losing
        // progress on a stalled process.
        Cache::put('coingecko_seed:page', $page + 1, now()->addMinutes(self::CURSOR_TTL_MINUTES));

        return response()->json([
            'status' => 'ok',
            'page' => $page,
            'seeded_this_batch' => $seeded,
            'next_page' => $page + 1,
            'results' => $results,
        ]);
    }
}