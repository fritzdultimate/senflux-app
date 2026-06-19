<?php

namespace App\Livewire\Protected;

use App\Models\LiveTrade;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Live Trades')]
class LiveTrades extends Component
{
    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function openTrades()
    {
        return LiveTrade::open()
            ->with('trackedAsset')
            ->latest('opened_at')
            ->get();
    }

    #[Computed]
    public function closedTrades()
    {
        return LiveTrade::closed()
            ->with('trackedAsset')
            ->latest('closed_at')
            ->take(15)
            ->get();
    }

    #[Computed]
    public function openCount(): int
    {
        return $this->openTrades->count();
    }

    #[Computed]
    public function totalOpenPnl(): float
    {
        return (float) $this->openTrades->sum('pnl_amount');
    }

    #[Computed]
    public function winRate(): float
    {
        $closed = $this->closedTrades;
        if ($closed->isEmpty()) return 0;

        $wins = $closed->where('pnl_amount', '>', 0)->count();
        return round(($wins / $closed->count()) * 100, 1);
    }

    #[Poll(8000)]
    public function refresh(): void
    {
        unset($this->openTrades, $this->closedTrades, $this->openCount, $this->totalOpenPnl, $this->winRate);
    }

    public function render()
    {
        return view('livewire.protected.live-trades');
    }
}
