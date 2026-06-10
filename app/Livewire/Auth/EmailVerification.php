<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Verify Your Email — Senflux')]

class EmailVerification extends Component {
    public bool $resent = false;

    public function mount(): void {
        if (Auth::user()?->hasVerifiedEmail()) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function resend(): void {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirect(route('dashboard'), navigate: true);
            return;
        }

        $key = 'verification.resend.' . $user->id;

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('resend', "Too many attempts. Try again in {$seconds} seconds.");
            return;
        }

        RateLimiter::hit($key, 60);

        $user->sendEmailVerificationNotification();
        $this->resent = true;

        $this->js("setTimeout(() => { \$wire.set('resent', false) }, 4000)");
    }

    public function logout(): void {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('login'), navigate: true);
    }

    public function render(): \Illuminate\View\View {
        return view('livewire.auth.email-verification');
    }
}