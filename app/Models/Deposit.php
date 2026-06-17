<?php

namespace App\Models;

use App\Enums\DepositStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deposit extends Model {
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'plan_config_id',
        'amount_usd',
        'crypto_currency',
        'crypto_amount',
        'actually_paid',
        'actually_paid_usd',
        'status',
        'nowpayments_id',
        'nowpayments_order_id',
        'payment_url',
        'pay_address',
        'network',
        'confirmations',
        'required_confirmations',
        'daily_rate',
        'total_earnings',
        'last_earnings_at',
        'activated_at',
        'expires_at',
        'ipn_received_at',
        'meta',
    ];

    protected function casts(): array {
        return [
            'amount_usd'         => 'decimal:2',
            'crypto_amount'      => 'decimal:8',
            'actually_paid'      => 'decimal:8',
            'actually_paid_usd'  => 'decimal:2',
            'daily_rate'         => 'decimal:4',
            'total_earnings'     => 'decimal:8',
            'last_earnings_at'   => 'datetime',
            'activated_at'       => 'datetime',
            'expires_at'         => 'datetime',
            'ipn_received_at'    => 'datetime',
            'meta'               => 'array',
            'status'             => DepositStatus::class,
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

    public function earnings(): HasMany
    {
        return $this->hasMany(DepositEarning::class);
    }

    public function referralBonuses(): HasMany
    {
        return $this->hasMany(ReferralBonus::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIsActiveAttribute(): bool
    {
        return $this->status === DepositStatus::ACTIVE;
    }

    public function getIsTerminalAttribute(): bool
    {
        return $this->status->isTerminal();
    }

    public function getIsEarningAttribute(): bool
    {
        return $this->status->isEarning();
    }

    public function getEffectiveAmountAttribute(): float
    {
        return (float) ($this->actually_paid_usd ?? $this->amount_usd);
    }

    public function getDailyEarningEstimateAttribute(): float
    {
        return (float) $this->effective_amount * (float) ($this->daily_rate ?? 0);
    }

    public function getConfirmationProgressAttribute(): int
    {
        if (!$this->required_confirmations) return 0;
        return min(100, (int) round(($this->confirmations / $this->required_confirmations) * 100));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast()
            && in_array($this->status, [DepositStatus::WAITING, DepositStatus::PENDING]);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', DepositStatus::ACTIVE->value);
    }

    public function scopePending($query) {
        return $query->whereIn('status', [
            DepositStatus::PENDING->value,
            DepositStatus::WAITING->value,
            DepositStatus::CONFIRMING->value,
        ]);
    }

    public function scopeEarning($query) {
        return $query->where('status', DepositStatus::ACTIVE->value)
                     ->whereNotNull('daily_rate');
    }

    public function scopeForUser($query, int $userId) {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, DepositStatus $status)
    {
        return $query->where('status', $status->value);
    }
}