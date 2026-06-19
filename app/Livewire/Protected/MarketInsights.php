<?php

namespace App\Livewire\Protected;

use App\Models\AssetPriceHistory;
use App\Models\TrackedAsset;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Market Insights')]
class MarketInsights extends Component
{
    #[Url]
    public ?int $assetId = null;

    #[Url]
    public string $range = '7'; // days: 1, 7, 30

    #[Computed]
    public function assets()
    {
        return TrackedAsset::active()->get();
    }

    #[Computed]
    public function selectedAsset(): ?TrackedAsset
    {
        if ($this->assetId) {
            $found = $this->assets->firstWhere('id', $this->assetId);
            if ($found) return $found;
        }

        return $this->assets->first();
    }

    public function selectAsset(int $id): void
    {
        $this->assetId = $id;
    }

    public function setRange(string $range): void
    {
        $this->range = $range;
    }

    #[Computed]
    public function priceHistory()
    {
        if (!$this->selectedAsset) return collect();

        $days = (int) $this->range;

        return AssetPriceHistory::forAsset($this->selectedAsset->id)
            ->since(now()->subDays($days))
            ->orderBy('recorded_at')
            ->get();
    }

    #[Computed]
    public function chartPoints(): array
    {
        return $this->priceHistory->map(fn ($h) => [
            'x'     => $h->recorded_at->timestamp,
            'price' => (float) $h->price,
            'label' => $h->recorded_at->format('M j, g:ia'),
        ])->toArray();
    }

    #[Computed]
    public function formationTrend()
    {
        return DB::table('market_formation_states')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->reverse()
            ->values();
    }

    #[Computed]
    public function priceChangeInRange(): ?float
    {
        $points = $this->priceHistory;
        if ($points->count() < 2) return null;

        $first = (float) $points->first()->price;
        $last  = (float) $points->last()->price;

        if ($first <= 0) return null;

        return round((($last - $first) / $first) * 100, 2);
    }

    public function render()
    {
        return view('livewire.protected.market-insights');
    }
}
