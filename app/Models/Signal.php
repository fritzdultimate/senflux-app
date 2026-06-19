<?php

namespace App\Models;

use App\Enums\PlanType;
use App\Enums\SignalType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Signal extends Model
{
    protected $fillable = [
        'tracked_asset_id',
        'signal_type',
        'confidence_score',
        'note',
        'min_plan',
        'expires_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'confidence_score' => 'decimal:2',
            'expires_at'       => 'datetime',
            'signal_type'      => SignalType::class,
            'min_plan'         => PlanType::class,
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────

    public function trackedAsset(): BelongsTo
    {
        return $this->belongsTo(TrackedAsset::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Access control ───────────────────────────────────────────────────

    /**
     * Does the given user's subscription plan meet this signal's minimum?
     * No min_plan set = visible to everyone (including no subscription).
     */
    public function isVisibleTo(?User $user): bool
    {
        if (!$this->min_plan) return true;
        if (!$user || !$user->subscription_plan) return false;

        $userPlan = $user->subscription_plan instanceof PlanType
            ? $user->subscription_plan
            : PlanType::from($user->subscription_plan);

        return $userPlan->order() >= $this->min_plan->order();
    }

    // ── Accessors ─────────────────────────────────────────────────────────

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where(fn ($q) =>
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now())
        );
    }
}
