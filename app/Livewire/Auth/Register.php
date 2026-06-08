<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Create Your Account — Senflux')]
class Register extends Component {
    public function render(): \Illuminate\View\View {
        return view('livewire.auth.register');
    }
}
