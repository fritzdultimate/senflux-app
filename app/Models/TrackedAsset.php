<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrackedAsset extends Model {
    protected $fillable = [
        'symbol',
        'name',
        'network',
        'current_price',
        'price_change_24h',
        'price_updated_at',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'current_price'     => 'decimal:8',
            'price_change_24h'  => 'decimal:4',
            'price_updated_at'  => 'datetime',
            'is_active'         => 'boolean',
            'sort_order'        => 'integer',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────

    public function liveTrades(): HasMany
    {
        return $this->hasMany(LiveTrade::class);
    }

    public function signals(): HasMany {
        return $this->hasMany(Signal::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────

    public function getIsPriceStaleAttribute(): bool
    {
        return !$this->price_updated_at || $this->price_updated_at->diffInMinutes(now()) > 10;
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
