<?php

namespace App\Livewire\Protected\Packs;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.protected')]
class MyPacks extends Component
{
    private const OPEN_STATUSES = ['active', 'in_renewal_window'];

    #[Computed]
    public function subscriptions()
    {
        return Auth::user()->packSubscriptions()
            ->with(['packTier', 'slots'])
            ->latest('purchased_at')
            ->get();
    }

    #[Computed]
    public function activePackCount(): int
    {
        return $this->subscriptions
            ->filter(fn ($sub) => in_array($sub->status->value, self::OPEN_STATUSES, true))
            ->count();
    }

    #[Computed]
    public function totalEarningActive(): float
    {
        return (float) $this->subscriptions
            ->filter(fn ($sub) => in_array($sub->status->value, self::OPEN_STATUSES, true))
            ->flatMap(fn ($sub) => $sub->slots)
            ->sum('realized_profit');
    }

    public function refresh(): void
    {
        unset($this->subscriptions, $this->activePackCount, $this->totalEarningActive);
    }

    public function render()
    {
        return view('livewire.protected.packs.my-packs');
    }
}