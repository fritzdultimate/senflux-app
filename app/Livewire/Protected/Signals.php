<?php

namespace App\Livewire\Protected;

use App\Enums\PlanType;
use App\Models\Signal;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Signals')]
class Signals extends Component
{
    
    public function mount() {
        Auth::user()->onboarding->markStep('explored_signals');
    }
    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function userPlan(): ?PlanType
    {
        $plan = $this->user->subscription_plan;
        if (!$plan) return null;
        return $plan instanceof PlanType ? $plan : PlanType::from($plan);
    }

    #[Computed]
    public function allSignals()
    {
        return Signal::active()
            ->with('trackedAsset')
            ->latest()
            ->take(30)
            ->get();
    }

    #[Computed]
    public function visibleSignals()
    {
        return $this->allSignals->filter(fn (Signal $s) => $s->isVisibleTo($this->user))->values();
    }

    #[Computed]
    public function lockedCount(): int
    {
        return $this->allSignals->count() - $this->visibleSignals->count();
    }

    #[Computed]
    public function highestLockedPlan(): ?PlanType
    {
        $locked = $this->allSignals->filter(fn (Signal $s) => !$s->isVisibleTo($this->user));
        if ($locked->isEmpty()) return null;

        return $locked->pluck('min_plan')->filter()->sortByDesc(fn ($p) => $p->order())->first();
    }

    #[Poll(30000)]
    public function refresh(): void
    {
        unset($this->allSignals, $this->visibleSignals, $this->lockedCount, $this->highestLockedPlan);
    }

    public function render()
    {
        return view('livewire.protected.signals');
    }
}
