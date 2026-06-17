<?php

namespace App\Models;

use App\Enums\PlanType;
use App\Enums\PlanInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_config_id',
        'interval',
        'amount_paid',
        'starts_at',
        'expires_at',
        'deposit_id',
        'nowpayments_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'starts_at'   => 'datetime',
            'expires_at'  => 'datetime',
            'interval'    => PlanInterval::class,
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function planConfig(): BelongsTo
    {
        return $this->belongsTo(PlanConfig::class);
    }

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, (int) now()->diffInDays($this->expires_at, false));
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(fn($q) =>
            $q->where('status', 'expired')->orWhere('expires_at', '<=', now())
        );
    }
}