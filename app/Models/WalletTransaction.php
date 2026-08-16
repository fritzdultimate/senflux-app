<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
        'meta',
        'created_by',
        'locked_portion '
    ];

    protected function casts(): array {
        return [
            'amount' => 'decimal:8',
            'locked_portion' => 'decimal:8',
            'balance_before' => 'decimal:8',
            'balance_after' => 'decimal:8',
            'meta' => 'array',
            'type' => TransactionType::class,
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function wallet(): BelongsTo {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Polymorphic source (Deposit, Withdrawal, RankAdvancement, etc.) */
    public function reference(): MorphTo {
        return $this->morphTo('reference');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIsDebitAttribute(): bool {
        return in_array($this->type, [
            TransactionType::WITHDRAWAL, 
            TransactionType::FEE,
            TransactionType::PACK_PURCHASE,
            TransactionType::PACK_SLOT_FUND,
            TransactionType::PACK_COMPOUND_RESTAKE,
            TransactionType::PACK_SLOT_TOPUP,

        ]);
    }

    public function getSignedAmountAttribute(): string {
        return $this->is_debit
            ? '-' . number_format((float) $this->amount, 2)
            : '+' . number_format((float) $this->amount, 2);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeOfType($query, TransactionType $type)
    {
        return $query->where('type', $type->value);
    }

    public function scopeCredits($query)
    {
        return $query->whereNotIn('type', [TransactionType::WITHDRAWAL->value, TransactionType::FEE->value]);
    }

    public function scopeDebits($query)
    {
        return $query->whereIn('type', [TransactionType::WITHDRAWAL->value, TransactionType::FEE->value]);
    }
}