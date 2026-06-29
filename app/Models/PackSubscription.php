<?php

namespace App\Models;

use App\Enums\PackSubscriptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackSubscription extends Model {
    protected $fillable = [
        'user_id', 'pack_tier_id', 'status', 'price_paid',
        'purchased_at', 'matures_at', 'renewal_window_ends_at',
        'purchase_transaction_id', 'refunded_at', 'refund_transaction_id',
        'renewed_into_subscription_id', 'renewed_from_subscription_id',
    ];

    protected function casts(): array {
        return [
            'status' => PackSubscriptionStatus::class,
            'price_paid' => 'decimal:2',
            'purchased_at' => 'datetime',
            'matures_at' => 'datetime',
            'renewal_window_ends_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function packTier(): BelongsTo {
        return $this->belongsTo(PackTier::class);
    }

    public function slots(): HasMany {
        return $this->hasMany(PackSlot::class)->orderBy('slot_number');
    }

    public function purchaseTransaction(): BelongsTo {
        return $this->belongsTo(WalletTransaction::class, 'purchase_transaction_id');
    }

    public function refundTransaction(): BelongsTo {
        return $this->belongsTo(WalletTransaction::class, 'refund_transaction_id');
    }

    public function renewedInto(): BelongsTo {
        return $this->belongsTo(self::class, 'renewed_into_subscription_id');
    }

    public function renewedFrom(): BelongsTo {
        return $this->belongsTo(self::class, 'renewed_from_subscription_id');
    }

    public function isInRenewalWindow(): bool {
        return $this->status === PackSubscriptionStatus::IN_RENEWAL_WINDOW;
    }

    public function isPastRenewalWindow(): bool {
        return $this->renewal_window_ends_at !== null && $this->renewal_window_ends_at->isPast();
    }

    /**
     * Eligible for the 3-day no-questions refund only if literally zero
     * slots have ever been funded — the moment one slot gets capital,
     * "I haven't used this" stops being true, even if day 1 of 3.
     */
    public function isEligibleForRefund(): bool {
        if ($this->status !== PackSubscriptionStatus::ACTIVE) {
            return false;
        }

        if ($this->purchased_at->diffInDays(now()) > 3) {
            return false;
        }

        return $this->slots()->whereNotNull('funded_at')->doesntExist();
    }
}