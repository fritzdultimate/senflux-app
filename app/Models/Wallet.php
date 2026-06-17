<?php

namespace App\Models;

use App\Enums\WalletType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'balance',
        'locked_balance',
        'currency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'balance'        => 'decimal:8',
            'locked_balance' => 'decimal:8',
            'is_active'      => 'boolean',
            'type'           => WalletType::class,
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getAvailableBalanceAttribute(): float
    {
        return max(0, (float) $this->balance - (float) $this->locked_balance);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return number_format((float) $this->balance, 2);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, WalletType $type)
    {
        return $query->where('type', $type->value);
    }
}
