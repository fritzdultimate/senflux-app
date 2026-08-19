<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Two-Factor Authentication — Senflux')]
class TwoFactor extends Component {
    public string $code = '';
    public bool $useRecovery  = false;
    public string $recoveryCode = '';

    public function mount(): void {
        if (! session()->has('2fa_user_id')) {
            $this->redirect(route('login'), navigate: true);
        }
    }

    protected function getUser(): ?User {
        return User::find(session('2fa_user_id'));
    }

    public function verify(TwoFactorService $service): void {
        $user = $this->getUser();

        if (! $user) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        if ($this->useRecovery) {
            $this->verifyRecovery($user, $service);
        } else {
            $this->verifyTotp($user, $service);
        }
    }

    private function verifyTotp(User $user, TwoFactorService $service): void {
        $this->validate(['code' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/']]);

        if (! $service->verifyCode($user, $this->code)) {
            $this->addError('code', 'Invalid authentication code. Please try again.');
            return;
        }

        $this->completeLogin($user);
    }

    private function verifyRecovery(User $user, TwoFactorService $service): void {
        $this->validate(['recoveryCode' => ['required', 'string']]);

        if (! $service->verifyRecoveryCode($user, $this->recoveryCode)) {
            $this->addError('recoveryCode', 'Invalid recovery code.');
            return;
        }

        $this->completeLogin($user);
    }

    private function completeLogin(User $user): void {
        session()->forget('2fa_user_id');

        Auth::login($user);
        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render(): \Illuminate\View\View {
        return view('livewire.auth.two-factor');
    }
}