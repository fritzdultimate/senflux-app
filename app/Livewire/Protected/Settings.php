<?php

namespace App\Livewire\Protected;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Settings')]
class Settings extends Component
{
    // ── Profile fields ────────────────────────────────────────────────────
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $country = '';
    public string $timezone = 'UTC';

    // ── Password fields ───────────────────────────────────────────────────
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // ── 2FA ───────────────────────────────────────────────────────────────
    public bool $two_factor_enabled = false;

    public string $profileFlash = '';
    public string $passwordFlash = '';
    public string $passwordError = '';
    public string $twoFactorFlash = '';

    public array $timezones = [
        'UTC', 'America/New_York', 'America/Chicago', 'America/Denver', 'America/Los_Angeles',
        'Europe/London', 'Europe/Paris', 'Europe/Berlin', 'Africa/Lagos', 'Africa/Cairo',
        'Asia/Dubai', 'Asia/Kolkata', 'Asia/Singapore', 'Asia/Tokyo', 'Asia/Shanghai',
        'Australia/Sydney',
    ];

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone_number ?? '';
        $this->country = $user->country ?? '';
        $this->timezone = $user->timezone ?? 'UTC';
        $this->two_factor_enabled = (bool) $user->two_factor_enabled;
    }

    public function saveProfile(): void {
        $user = Auth::user();

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'country' => 'nullable|string|max:80',
            'timezone' => 'required|string|in:' . implode(',', $this->timezones),
        ]);

        $emailChanged = $this->email !== $user->email;

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone ?: null,
            'country' => $this->country ?: null,
            'timezone' => $this->timezone,
        ]);

        if ($emailChanged) {
            $user->forceFill(['email_verified_at' => null])->save();
            $user->sendEmailVerificationNotification();
        }

        ActivityLog::record(
            action: 'updated_profile',
            description: 'Updated profile information',
            subject: $user,
        );

        $this->profileFlash = $emailChanged
            ? 'Profile updated. Please verify your new email address.'
            : 'Profile updated successfully.';
    }

    public function updatePassword(): void {
        $this->passwordError = '';
        $user = Auth::user();

        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($this->current_password, $user->password)) {
            $this->passwordError = 'Current password is incorrect.';
            return;
        }

        $user->update(['password' => Hash::make($this->new_password)]);

        ActivityLog::record(
            action: 'changed_password',
            description: 'Password changed',
            subject: $user,
        );

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->passwordFlash = 'Password updated successfully.';
    }

    public function toggleTwoFactor(): void {
        $user = Auth::user();
        $user->update(['two_factor_enable' => !$user->two_factor_enable]);
        $this->two_factor_enabled = (bool) $user->two_factor_enable;

        ActivityLog::record(
            action: $this->two_factor_enabled ? 'enabled_2fa' : 'disabled_2fa',
            description: $this->two_factor_enabled ? 'Enabled two-factor authentication' : 'Disabled two-factor authentication',
            subject: $user,
        );

        $this->twoFactorFlash = $this->two_factor_enabled
            ? 'Two-factor authentication enabled.'
            : 'Two-factor authentication disabled.';
    }

    public function clearFlashes(): void {
        $this->profileFlash  = '';
        $this->passwordFlash = '';
        $this->twoFactorFlash = '';
    }

    public function render() {
        return view('livewire.protected.settings');
    }
}
