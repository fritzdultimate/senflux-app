<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadershipMatchBonus extends Model
{
    protected $fillable = [
        'earner_id',
        'source_user_id',
        'rank_advancement_id',
        'rate',
        'amount',
        'wallet_transaction_id',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'rate'         => 'decimal:4',
            'amount'       => 'decimal:2',
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

    public function rankAdvancement(): BelongsTo
    {
        return $this->belongsTo(RankAdvancement::class);
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }
}