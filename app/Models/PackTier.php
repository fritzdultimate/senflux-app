<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackTier extends Model
{
    protected $fillable = [
        'key', 'name', 'price', 'duration_days', 'slot_count',
        'min_capital_per_slot', 'max_capital_per_slot',
        'historical_outcome_min', 'historical_outcome_max',
        'features', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'min_capital_per_slot' => 'decimal:2',
            'max_capital_per_slot' => 'decimal:2',
            'historical_outcome_min' => 'decimal:2',
            'historical_outcome_max' => 'decimal:2',
            'features' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(PackSubscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Validates a proposed per-slot capital amount against this tier's
     * bounds. Null max means no upper bound (Dominion: "$25,000 and above").
     */
    public function isCapitalWithinBounds(float $amount): bool {
        if ($amount < (float) $this->min_capital_per_slot) {
            return false;
        }

        if ($this->max_capital_per_slot !== null && $amount > (float) $this->max_capital_per_slot) {
            return false;
        }

        return true;
    }

    
    public function baselineDailyRate(): float {
        if (!$this->historical_outcome_min || !$this->historical_outcome_max || !$this->duration_days) {
            return 0.0;
        }

        $midpointPercent = ((float) $this->historical_outcome_min + (float) $this->historical_outcome_max) / 2;

        return ($midpointPercent / 100) / $this->duration_days;
    }
}
