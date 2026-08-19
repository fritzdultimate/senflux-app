<?php

namespace App\Livewire\Protected;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ActivityBell extends Component
{
    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function recentActivity()
    {
        return ActivityLog::query()
            ->visibleTo($this->user->id)
            ->latest()
            ->take(8)
            ->get();
    }

    #[Computed]
    public function hasUnseen(): bool
    {
        $lastSeen = $this->user->last_activity_seen_at;

        if (!$lastSeen) {
            return $this->recentActivity->isNotEmpty();
        }

        return $this->recentActivity->contains(
            fn (ActivityLog $log) => $log->created_at->greaterThan($lastSeen)
        );
    }

    /** Called when the dropdown is opened — marks everything visible as seen. */
    public function markSeen(): void
    {
        if (!$this->hasUnseen) {
            return;
        }

        $this->user->forceFill(['last_activity_seen_at' => now()])->save();

        unset($this->hasUnseen);
    }

    #[On('activity-logged')]
    public function refreshActivity(): void
    {
        unset($this->recentActivity, $this->hasUnseen);
    }

    public function render()
    {
        return view('livewire.protected.activity-bell');
    }
}
