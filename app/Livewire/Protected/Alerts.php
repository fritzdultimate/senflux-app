<?php

namespace App\Livewire\Protected;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Alerts')]
class Alerts extends Component
{
    // ── Toggle states ─────────────────────────────────────────────────────
    public bool $deposit_confirmed   = true;
    public bool $deposit_activated   = true;
    public bool $daily_earning_summary = false;
    public bool $withdrawal_approved = true;
    public bool $withdrawal_paid     = true;
    public bool $withdrawal_rejected = true;
    public bool $referral_bonus      = false;
    public bool $rank_achieved       = true;
    public bool $leadership_match    = false;
    public bool $subscription_expiring = true;
    public bool $security_alerts     = true; // login from new device, password change — always recommended on

    public string $savedFlash = '';

    public function mount(): void {
        $prefs = Auth::user()->notification_preferences ?? [];

        foreach ($this->defaultKeys() as $key) {
            if (array_key_exists($key, $prefs)) {
                $this->{$key} = (bool) $prefs[$key];
            }
        }
    }

    private function defaultKeys(): array
    {
        return [
            'deposit_confirmed',
            'deposit_activated',
            'daily_earning_summary',
            'withdrawal_approved',
            'withdrawal_paid',
            'withdrawal_rejected',
            'referral_bonus',
            'rank_achieved',
            'leadership_match',
            'subscription_expiring',
            'security_alerts',
        ];
    }

    public function save(): void
    {
        $prefs = collect($this->defaultKeys())
            ->mapWithKeys(fn($key) => [$key => $this->{$key}])
            ->toArray();

        $user = Auth::user();
        $user->update(['notification_preferences' => $prefs]);

        ActivityLog::record(
            action:      'updated_notification_preferences',
            description: 'Updated email alert preferences',
            subject:     $user,
        );

        $this->savedFlash = 'Preferences saved.';
    }

    public function clearFlash(): void
    {
        $this->savedFlash = '';
    }

    public function render()
    {
        return view('livewire.protected.alerts');
    }
}
