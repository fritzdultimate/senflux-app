<?php

namespace App\Livewire\Protected;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Terminal — Senflux')]
class Terminal extends Component {

    public function mount() {
        Auth::user()->onboarding->markStep('viewed_terminal');
    }
    public function render(): \Illuminate\View\View {
        return view('livewire.protected.terminal');
    }
}
