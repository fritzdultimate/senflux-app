<?php

namespace App\Livewire\Protected\Formations;

use App\Models\Formation;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.protected')]
class FormationFeed extends Component
{
    #[Computed]
    public function formations()
    {
        return Formation::active()->orderByDesc('score')->get();
    }

    public function render()
    {
        return view('livewire.protected.formations.formation-feed');
    }
}
