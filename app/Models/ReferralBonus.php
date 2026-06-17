<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralBonus extends Model
{
    protected $fillable = [
        'earner_id',
        'source_user_id',
        'deposit_id',
        'level',
        'rate',
        'amount',
        'wallet_transaction_id',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'level'        => 'integer',
            'rate'         => 'decimal:4',
            'amount'       => 'decimal:8',
            'processed_at' => 'datetime',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function earner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'earner_id');
    }

    public function sourceUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class);
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    public function scopeProcessed($query)
    {
        return $query->whereNotNull('processed_at');
    }
}