<?php

namespace App\Livewire\Concerns;

use App\Services\TwoFactorService;
use Illuminate\Support\Facades\Auth;

/**
 * Adds a session-scoped "step-up" re-authentication gate to a Livewire
 * component for sensitive actions (withdrawal confirm, disabling 2FA, etc).
 *
 * A user with confirmed 2FA must enter a fresh TOTP/recovery code before the
 * gated action runs; once verified, a short trusted window (10 minutes)
 * avoids re-prompting on every subsequent sensitive action in the same
 * session.
 */
trait RequiresStepUp {
    public bool $stepUpRequired = false;
    public string $stepUpCode = '';
    public string $stepUpError = '';

    /** Session key is per-user so switching accounts can't inherit trust. */
    private function stepUpSessionKey(): string {
        return 'stepup_verified_until_' . Auth::id();
    }

    public function stepUpSatisfied(): bool {
        $user = Auth::user();

        // No confirmed 2FA on the account — nothing to step up from.
        if (! $user || ! $user->two_factor_enable || ! $user->two_factor_confirmed_at) {
            return true;
        }

        $until = session($this->stepUpSessionKey());

        return $until && now()->lt($until);
    }

    public function markStepUpVerified(): void {
        session([$this->stepUpSessionKey() => now()->addMinutes(1)]);
        $this->stepUpRequired = false;
        $this->stepUpCode = '';
        $this->stepUpError = '';
    }

    /**
     * Call this from the gated action before doing anything else. Returns
     * true if the caller may proceed immediately; if false, the component
     * should render the step-up code prompt (via $stepUpRequired) instead.
     */
    protected function ensureStepUp(): bool {
        if ($this->stepUpSatisfied()) {
            return true;
        }

        $this->stepUpRequired = true;

        return false;
    }

    /**
     * Bound to the step-up prompt's "Verify" button.
     */
    public function verifyStepUp(TwoFactorService $service): void {
        $user = Auth::user();
        $this->stepUpError = '';

        if (! $this->stepUpCode) {
            $this->stepUpError = 'Enter the 6-digit code from your authenticator app.';
            return;
        }

        if (! $service->verifyCode($user, $this->stepUpCode) && ! $service->verifyRecoveryCode($user, $this->stepUpCode)) {
            $this->stepUpError = 'Invalid code. Please try again.';
            return;
        }

        $this->markStepUpVerified();
    }

    public function cancelStepUp(): void {
        $this->stepUpRequired = false;
        $this->stepUpCode = '';
        $this->stepUpError = '';
    }
}
