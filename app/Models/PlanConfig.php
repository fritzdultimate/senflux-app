<?php

namespace App\Models;

use App\Enums\PlanType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanConfig extends Model {
    protected $fillable = [
        'plan',
        'label',
        'monthly_price',
        'quarterly_price',
        'yearly_price',
        'daily_rate_min',
        'daily_rate_max',
        'min_deposit',
        'max_deposit',
        'features',
        'is_active',
        'is_popular',
        'sort_order',
    ];

    protected function casts(): array {
        return [
            'monthly_price'   => 'decimal:2',
            'quarterly_price' => 'decimal:2',
            'yearly_price'    => 'decimal:2',
            'daily_rate_min'  => 'decimal:4',
            'daily_rate_max'  => 'decimal:4',
            'min_deposit'     => 'decimal:2',
            'max_deposit'     => 'decimal:2',
            'features'        => 'array',
            'is_active'       => 'boolean',
            'is_popular'      => 'boolean',
            'plan'            => PlanType::class,
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function deposits(): HasMany {
        return $this->hasMany(Deposit::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getDailyRatePercentAttribute(): string
    {
        return number_format((float) $this->daily_rate_max * 100, 1) . '%';
    }

    public function getMonthlyEstimateAttribute(): float
    {
        return (float) $this->daily_rate_max * 30;
    }

    public function getPriceForInterval(string $interval): float {
        return match($interval) {
            'monthly'   => (float) $this->monthly_price,
            'quarterly' => (float) $this->quarterly_price,
            'yearly'    => (float) $this->yearly_price,
            default     => (float) $this->monthly_price,
        };
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query) {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}

// ─────────────────────────────────────────────────────────────────────────────
