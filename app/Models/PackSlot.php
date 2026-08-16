<?php

namespace App\Models;

use App\Enums\PackSlotStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackSlot extends Model
{
    protected $fillable = [
        'pack_subscription_id', 'slot_number', 'status', 'formation_id', 'deployed_at',
        'next_earning_at',
        'capital_amount', 'realized_profit', 'funded_at', 'closed_at',
        'fund_transaction_id', 'close_transaction_id',
        'early_exit_fee_charged', 'was_early_exit',
    ];

    protected function casts(): array {
        return [
            'status' => PackSlotStatus::class,
            'capital_amount' => 'decimal:2',
            'realized_profit' => 'decimal:2',
            'deployed_at' => 'datetime',
            'next_earning_at' => 'datetime',
            'funded_at' => 'datetime',
            'closed_at' => 'datetime',
            'early_exit_fee_charged' => 'decimal:2',
            'was_early_exit' => 'boolean',
        ];
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function deploymentStatus(): string {
        if ($this->status !== PackSlotStatus::FUNDED) {
            return 'not_applicable';
        }

        if ($this->formation_id !== null) {
            return 'already_deployed';
        }

        return Formation::acceptingDeployments()->exists() ? 'eligible_for_deployment' : 'waiting_for_qualification';
    }

    public function subscription(): BelongsTo {
        return $this->belongsTo(PackSubscription::class, 'pack_subscription_id');
    }

    public function fundTransaction(): BelongsTo {
        return $this->belongsTo(WalletTransaction::class, 'fund_transaction_id');
    }

    public function closeTransaction(): BelongsTo {
        return $this->belongsTo(WalletTransaction::class, 'close_transaction_id');
    }

    /**
     * Every deploy + top-up logged against this slot — powers the
     * Position panel's deployment-history timeline.
     */
    public function contributions(): HasMany {
        return $this->hasMany(SlotContribution::class);
    }

    public function isFunded(): bool {
        return $this->status === PackSlotStatus::FUNDED;
    }
}