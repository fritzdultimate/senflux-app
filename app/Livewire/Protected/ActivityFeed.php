<?php

namespace App\Livewire\Protected;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.protected')]
#[Title('Activity Log')]
class ActivityFeed extends Component
{
    use WithPagination;

    #[Url]
    public string $filter = 'all';

    public function mount(): void
    {
        Auth::user()->forceFill(['last_activity_seen_at' => now()])->save();
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function categories(): array
    {
        return [
            'all' => 'All',
            'financial' => 'Financial',
            'security' => 'Security',
            'compliance' => 'Compliance',
            'account' => 'Account',
        ];
    }

    public function setFilter(string $filter): void
    {
        $this->filter = in_array($filter, array_keys($this->categories), true) ? $filter : 'all';
        $this->resetPage();
    }

    #[Computed]
    public function activity()
    {
        $query = ActivityLog::query()
            ->visibleTo($this->user->id)
            ->latest();

        if ($this->filter !== 'all') {
            $query->inCategory($this->filter);
        }

        return $query->paginate(20);
    }

    public function render()
    {
        return view('livewire.protected.activity-feed');
    }
}
