<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Reset Password — Senflux')]
class ForgotPassword extends Component {
    public string $email  = '';
    public bool $sent = false;

    protected function rules(): array {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function updated(string $field): void {
        $this->validateOnly($field);
    }

    public function sendLink(): void {
        $this->validate();

        // Always show success — never reveal whether email exists (anti-enumeration)
        Password::sendResetLink(['email' => $this->email]);

        $this->sent = true;
    }

    public function render(): \Illuminate\View\View {
        return view('livewire.auth.forgot-password');
    }
}