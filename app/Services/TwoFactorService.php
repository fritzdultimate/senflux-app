<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\TwoFactorDisabledNotification;
use App\Notifications\TwoFactorEnabledNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorService {
    private Google2FA $google2fa;

    public function __construct() {
        $this->google2fa = new Google2FA();
    }

    public function generateSecret(): string {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Standard otpauth:// URI — rendered into a QR code client-side.
     */
    public function otpAuthUrl(User $user, string $secret): string {
        return $this->google2fa->getQRCodeUrl(
            config('app.name', 'Senflux'),
            $user->email,
            $secret,
        );
    }

    public function verifyKeyAgainstSecret(string $secret, string $code): bool {
        return (bool) $this->google2fa->verifyKey($secret, $code);
    }

    /**
     * Confirm an enrollment: verify the code against the *unconfirmed* secret,
     * then persist everything and generate recovery codes.
     *
     * @return string[] plaintext recovery codes — shown to the user exactly once
     */
    public function confirmAndEnable(User $user, string $secret, string $code): array {
        if (! $this->verifyKeyAgainstSecret($secret, $code)) {
            throw new \RuntimeException('Invalid authentication code. Please try again.');
        }

        $plainCodes = $this->generateRecoveryCodes();
        $hashed = array_map(fn (string $c) => Hash::make($c), $plainCodes);

        $user->forceFill([
            'two_factor_secret'         => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($hashed)),
            'two_factor_confirmed_at'   => now(),
            'two_factor_enable'         => true,
        ])->save();

        $user->notify(new TwoFactorEnabledNotification());

        ActivityLog::record(
            action: 'enabled_2fa',
            description: 'Enabled two-factor authentication',
            subject: $user,
        );

        return $plainCodes;
    }

    public function verifyCode(User $user, string $code): bool {
        if (! $user->two_factor_secret) {
            return false;
        }

        return $this->verifyKeyAgainstSecret(decrypt($user->two_factor_secret), $code);
    }

    /**
     * Verify + burn a recovery code. Returns true if it matched.
     */
    public function verifyRecoveryCode(User $user, string $code): bool {
        if (! $user->two_factor_recovery_codes) {
            return false;
        }

        $codes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?: [];

        $matched = collect($codes)->first(fn ($c) => Hash::check($code, $c));

        if (! $matched) {
            return false;
        }

        $remaining = collect($codes)
            ->reject(fn ($c) => Hash::check($code, $c))
            ->values()
            ->all();

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($remaining)),
        ])->save();

        return true;
    }

    public function remainingRecoveryCodeCount(User $user): int {
        if (! $user->two_factor_recovery_codes) {
            return 0;
        }

        $codes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?: [];

        return count($codes);
    }

    /**
     * @return string[] plaintext codes, shown once
     */
    public function regenerateRecoveryCodes(User $user): array {
        if (! $user->two_factor_enable) {
            throw new \RuntimeException('Two-factor authentication is not enabled.');
        }

        $plainCodes = $this->generateRecoveryCodes();
        $hashed = array_map(fn (string $c) => Hash::make($c), $plainCodes);

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($hashed)),
        ])->save();

        ActivityLog::record(
            action: 'regenerated_2fa_recovery_codes',
            description: 'Regenerated two-factor recovery codes',
            subject: $user,
        );

        return $plainCodes;
    }

    public function disable(User $user): void {
        $user->forceFill([
            'two_factor_enable'         => false,
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ])->save();

        $user->notify(new TwoFactorDisabledNotification());

        ActivityLog::record(
            action: 'disabled_2fa',
            description: 'Disabled two-factor authentication',
            subject: $user,
        );
    }

    /** @return string[] */
    private function generateRecoveryCodes(int $count = 10): array {
        return collect(range(1, $count))
            ->map(fn () => strtoupper(Str::random(4) . '-' . Str::random(4)))
            ->all();
    }
}
