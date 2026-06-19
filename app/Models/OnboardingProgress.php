<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingProgress extends Model
{
    protected $fillable = [
        'user_id',
        'profile_completed',
        'explored_signals',
        'viewed_terminal',
        'connected_bot',
        'joined_telegram',
        'welcome_dismissed',
        'completed',
        'completed_at',
    ];

    protected $casts = [
        'profile_completed' => 'boolean',
        'explored_signals' => 'boolean',
        'viewed_terminal' => 'boolean',
        'connected_bot' => 'boolean',
        'joined_telegram' => 'boolean',
        'welcome_dismissed' => 'boolean',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    // ── Steps definition ──────────────────────────────────────────
    public static function steps(): array {
        return [
            'profile_completed' => [
                'label' => 'Complete your profile',
                'description' => 'Add your country and timezone so signals are relevant to you.',
                'icon' => 'user',
                'route' => 'dashboard.settings',
                'xp' => 20,
            ],
            'explored_signals' => [
                'label' => 'Browse live signals',
                'description' => 'See real-time formation intelligence from BirdEye & DexScreener.',
                'icon' => 'signal',
                'route' => 'dashboard.signals',
                'xp' => 15,
            ],
            'viewed_terminal' => [
                'label' => 'Open the terminal',
                'description' => 'Explore on-chain data, whale clusters, and wallet cohesion.',
                'icon' => 'terminal',
                'route' => 'dashboard.terminal',
                'xp' => 15,
            ],
            'connected_bot' => [
                'label' => 'Activate your trading bot',
                'description' => 'Set up your first automated bot with your preferred strategy.',
                'icon' => 'bot',
                'route' => 'dashboard.bots',
                'xp' => 30,
            ],
            'joined_telegram' => [
                'label' => 'Join the Telegram channel',
                'description' => 'Get instant alerts and signals pushed to your phone.',
                'icon' => 'telegram',
                'route' => 'settings.notifications',
                'xp' => 20,
            ],
        ];
    }

    // ── Computed progress ─────────────────────────────────────────
    public function completedSteps(): int {
        return collect(array_keys(self::steps()))
            ->filter(fn($key) => $this->{$key})
            ->count();
    }

    public function totalSteps(): int {
        return count(self::steps());
    }

    public function progressPercent(): int {
        return (int) round(($this->completedSteps() / $this->totalSteps()) * 100);
    }

    public function isFullyComplete(): bool {
        return $this->completedSteps() === $this->totalSteps();
    }

    public function markStep(string $step): void {
        if (! array_key_exists($step, self::steps())) return;
        if ($this->{$step}) return; // already done

        $this->update([$step => true]);

        if ($this->isFullyComplete()) {
            $this->update([
                'completed'    => true,
                'completed_at' => now(),
            ]);
        }
    }
}