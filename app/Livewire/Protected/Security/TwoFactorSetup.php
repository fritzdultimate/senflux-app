<?php

namespace App\Livewire\Protected\Security;

use App\Livewire\Concerns\RequiresStepUp;
use App\Services\TwoFactorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Security — Two-Factor Authentication')]
class TwoFactorSetup extends Component
{
    use RequiresStepUp;

    // ── Enrollment (in progress, unconfirmed) ───────────────────────────
    public ?string $pendingSecret = null;
    public string $confirmCode = '';

    // ── Recovery codes shown once, right after (re)generation ───────────
    public array $recoveryCodes = [];
    public bool $recoveryCodesAcknowledged = false;

    // ── Disable flow ─────────────────────────────────────────────────
    public bool $confirmingDisable = false;
    public string $disablePassword = '';

    public string $errorMessage = '';
    public string $successMessage = '';

    #[Computed]
    public function user() {
        return Auth::user();
    }

    #[Computed]
    public function isEnabled(): bool {
        return (bool) ($this->user->two_factor_enable && $this->user->two_factor_confirmed_at);
    }

    #[Computed]
    public function remainingRecoveryCodes(): int {
        if (! $this->isEnabled) return 0;
        return app(TwoFactorService::class)->remainingRecoveryCodeCount($this->user);
    }

    public function beginEnrollment(TwoFactorService $service): void {
        $this->errorMessage = '';
        $this->pendingSecret = $service->generateSecret();
        $this->confirmCode = '';
    }

    public function cancelEnrollment(): void {
        $this->pendingSecret = null;
        $this->confirmCode = '';
        $this->errorMessage = '';
    }

    #[Computed]
    public function pendingOtpAuthUrl(): ?string {
        if (! $this->pendingSecret) return null;
        return app(TwoFactorService::class)->otpAuthUrl($this->user, $this->pendingSecret);
    }

    public function confirmEnrollment(TwoFactorService $service): void {
        $this->errorMessage = '';

        $this->validate([
            'confirmCode' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ], [], ['confirmCode' => 'code']);

        try {
            $this->recoveryCodes = $service->confirmAndEnable($this->user, $this->pendingSecret, $this->confirmCode);
            $this->recoveryCodesAcknowledged = false;
            $this->pendingSecret = null;
            $this->confirmCode = '';
            unset($this->user, $this->isEnabled, $this->remainingRecoveryCodes);
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function acknowledgeRecoveryCodes(): void {
        $this->recoveryCodesAcknowledged = true;
        $this->recoveryCodes = [];
        $this->successMessage = 'Two-factor authentication is now enabled on your account.';
    }

    public function regenerateRecoveryCodes(TwoFactorService $service): void {
        $this->errorMessage = '';

        if (! $this->ensureStepUp()) {
            return;
        }

        try {
            $this->recoveryCodes = $service->regenerateRecoveryCodes($this->user);
            $this->recoveryCodesAcknowledged = false;
            unset($this->remainingRecoveryCodes);
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function requestDisable(): void {
        $this->confirmingDisable = true;
        $this->disablePassword = '';
        $this->errorMessage = '';
    }

    public function cancelDisable(): void {
        $this->confirmingDisable = false;
        $this->disablePassword = '';
    }

    public function disable(TwoFactorService $service): void {
        $this->errorMessage = '';

        $this->validate(['disablePassword' => 'required|string']);

        if (! Hash::check($this->disablePassword, $this->user->password)) {
            $this->addError('disablePassword', 'Incorrect password.');
            return;
        }

        if (! $this->ensureStepUp()) {
            return;
        }

        $service->disable($this->user);

        $this->confirmingDisable = false;
        $this->disablePassword = '';
        $this->successMessage = 'Two-factor authentication has been disabled.';
        unset($this->user, $this->isEnabled, $this->remainingRecoveryCodes);
    }

    public function render() {
        return view('livewire.protected.security.two-factor-setup');
    }
}
