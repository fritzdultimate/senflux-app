<?php

namespace App\Models;

use App\Enums\RankLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RankAdvancement extends Model
{
    protected $fillable = [
        'user_id',
        'from_rank',
        'to_rank',
        'bonus_amount',
        'wallet_transaction_id',
        'achieved_at',
    ];

    protected function casts(): array
    {
        return [
            'bonus_amount' => 'decimal:2',
            'achieved_at'  => 'datetime',
            'from_rank'    => RankLevel::class,
            'to_rank'      => RankLevel::class,
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    public function leadershipMatches(): HasMany
    {
        return $this->hasMany(LeadershipMatchBonus::class);
    }
}