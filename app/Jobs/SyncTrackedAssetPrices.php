<?php

namespace App\Jobs;

use App\Models\LiveTrade;
use App\Models\TrackedAsset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Syncs current_price on all active tracked assets, then recomputes
 * P&L on every open live trade tied to that asset.
 *
 * NOTE: Price source is not wired yet. fetchPrice() currently returns
 * null (no-op) so this job is safe to schedule now and wire to a real
 * feed (CoinGecko, Binance, etc.) later without touching the rest of
 * the pipeline below it.
 */
class SyncTrackedAssetPrices implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void {
        TrackedAsset::active()->each(function (TrackedAsset $asset) {
            $priceData = $this->fetchPrice($asset->symbol);

            if ($priceData === null) {
                // No feed wired yet — skip silently rather than corrupt data with nulls.
                return;
            }

            $asset->update([
                'current_price'    => $priceData['price'],
                'price_change_24h' => $priceData['change_24h'] ?? null,
                'price_updated_at' => now(),
            ]);

            $this->syncOpenTrades($asset);
        });
    }

    /**
     * Fetch the current price for a symbol from an external feed.
     *
     * STUB: returns null until a real price provider is wired in.
     * Expected return shape once implemented:
     *   ['price' => float, 'change_24h' => float|null]
     */
    private function fetchPrice(string $symbol): ?array
    {
        // TODO: wire to CoinGecko / Binance / preferred provider.
        // Example shape for future implementation:
        // $response = Http::get("https://api.example.com/price/{$symbol}");
        // return ['price' => $response['price'], 'change_24h' => $response['change_24h']];

        return null;
    }

    private function syncOpenTrades(TrackedAsset $asset): void {
        LiveTrade::open()
            ->where('tracked_asset_id', $asset->id)
            ->each(function (LiveTrade $trade) use ($asset) {
                $trade->current_price = $asset->current_price;
                $trade->recomputePnl();
                $trade->save();
            });
    }
}
