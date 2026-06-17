<?php

namespace App\Models;

use App\Enums\MarketFormationState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepositEarning extends Model
{
    protected $fillable = [
        'deposit_id',
        'user_id',
        'wallet_transaction_id',
        'amount',
        'rate_applied',
        'formation_state',
        'formation_multiplier',
        'earned_date',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'               => 'decimal:8',
            'rate_applied'         => 'decimal:4',
            'formation_multiplier' => 'decimal:4',
            'earned_date'          => 'date',
            'processed_at'         => 'datetime',
            'formation_state'      => MarketFormationState::class,
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForDate($query, string $date)
    {
        return $query->where('earned_date', $date);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('earned_date', $year)
                     ->whereMonth('earned_date', $month);
    }
}

