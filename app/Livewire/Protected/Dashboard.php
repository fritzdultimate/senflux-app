<?php

namespace App\Livewire\Protected;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Dashboard — Senflux')]
class Dashboard extends Component {
    public function render(): \Illuminate\View\View {
        return view('livewire.protected.dashboard');
    }
}
