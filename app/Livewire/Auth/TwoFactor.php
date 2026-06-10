<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

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

    public function verify(): void {
        $user = $this->getUser();

        if (! $user) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        if ($this->useRecovery) {
            $this->verifyRecovery($user);
        } else {
            $this->verifyTotp($user);
        }
    }

    private function verifyTotp(User $user): void {
        $this->validate(['code' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/']]);

        $google2fa = new Google2FA();
        $secret  = decrypt($user->two_factor_secret);

        if (! $google2fa->verifyKey($secret, $this->code)) {
            $this->addError('code', 'Invalid authentication code. Please try again.');
            return;
        }

        $this->completeLogin($user);
    }

    private function verifyRecovery(User $user): void {
        $this->validate(['recoveryCode' => ['required', 'string']]);

        $codes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        $matched = collect($codes)->first(
            fn($c) => Hash::check($this->recoveryCode, $c)
        );

        if (! $matched) {
            $this->addError('recoveryCode', 'Invalid recovery code.');
            return;
        }

        // Burn used recovery code
        $remaining = collect($codes)
            ->reject(fn($c) => Hash::check($this->recoveryCode, $c))
            ->values()
            ->all();

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($remaining)),
        ])->save();

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