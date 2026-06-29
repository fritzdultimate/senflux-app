<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlotEarning extends Model
{
    protected $fillable = [
        'pack_slot_id', 'user_id', 'formation_id', 'wallet_transaction_id',
        'amount', 'base_rate_applied', 'formation_state', 'formation_multiplier',
        'earned_date', 'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:8',
            'base_rate_applied' => 'decimal:6',
            'formation_multiplier' => 'decimal:4',
            'earned_date' => 'date',
            'processed_at' => 'datetime',
        ];
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(PackSlot::class, 'pack_slot_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }
}
