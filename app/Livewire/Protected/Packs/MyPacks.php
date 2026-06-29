<?php

namespace App\Livewire\Protected\Packs;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.protected')]
class MyPacks extends Component
{
    #[Computed]
    public function subscriptions()
    {
        return Auth::user()->packSubscriptions()
            ->with(['packTier', 'slots'])
            ->latest('purchased_at')
            ->get();
    }

    public function render()
    {
        return view('livewire.protected.packs.my-packs');
    }
}
