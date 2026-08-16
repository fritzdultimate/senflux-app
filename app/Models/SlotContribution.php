<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlotContribution extends Model
{
    protected $fillable = [
        'pack_slot_id', 'amount', 'type', 'wallet_transaction_id',
    ];

    protected function casts(): array {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function slot(): BelongsTo {
        return $this->belongsTo(PackSlot::class, 'pack_slot_id');
    }

    public function walletTransaction(): BelongsTo {
        return $this->belongsTo(WalletTransaction::class);
    }
}