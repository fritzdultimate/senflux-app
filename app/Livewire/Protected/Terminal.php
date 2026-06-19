<?php

namespace App\Livewire\Protected;

use App\Enums\PlanType;
use App\Models\LiveTrade;
use App\Models\Signal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Terminal')]
class Terminal extends Component
{
    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function formation()
    {
        return DB::table('market_formation_states')
            ->where('is_current', true)
            ->first();
    }

    /**
     * Unified feed — open trades, recently closed trades, and visible signals,
     * merged into one reverse-chronological stream.
     */
    #[Computed]
    public function feed()
    {
        $trades = LiveTrade::with('trackedAsset')
            ->latest('opened_at')
            ->take(15)
            ->get()
            ->map(fn (LiveTrade $t) => [
                'kind'      => 'trade',
                'timestamp' => $t->status->value === 'closed' ? $t->closed_at : $t->opened_at,
                'data'      => $t,
            ]);

        $signals = Signal::active()
            ->with('trackedAsset')
            ->latest()
            ->take(15)
            ->get()
            ->filter(fn (Signal $s) => $s->isVisibleTo($this->user))
            ->map(fn (Signal $s) => [
                'kind'      => 'signal',
                'timestamp' => $s->created_at,
                'data'      => $s,
            ]);

        return $trades->concat($signals)
            ->sortByDesc('timestamp')
            ->take(20)
            ->values();
    }

    #[Computed]
    public function openTradeCount(): int
    {
        return LiveTrade::open()->count();
    }

    #[Computed]
    public function activeSignalCount(): int
    {
        return Signal::active()
            ->get()
            ->filter(fn (Signal $s) => $s->isVisibleTo($this->user))
            ->count();
    }

    #[Poll(6000)]
    public function refresh(): void
    {
        unset($this->formation, $this->feed, $this->openTradeCount, $this->activeSignalCount);
    }

    public function render()
    {
        return view('livewire.protected.terminal');
    }
}
