<?php

namespace App\Models;

use App\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_id',
        'amount',
        'fee',
        'net_amount',
        'wallet_address',
        'network',
        'crypto_currency',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
        'paid_at',
        'tx_hash',
        'wallet_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:8',
            'fee'         => 'decimal:8',
            'net_amount'  => 'decimal:8',
            'reviewed_at' => 'datetime',
            'paid_at'     => 'datetime',
            'status'      => WithdrawalStatus::class,
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIsPendingAttribute(): bool
    {
        return $this->status === WithdrawalStatus::PENDING;
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', WithdrawalStatus::PENDING->value);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', WithdrawalStatus::APPROVED->value);
    }
}