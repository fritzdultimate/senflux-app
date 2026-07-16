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
        'upgraded_from_tier_id', 'upgraded_at', 'upgrade_transaction_id',
    ];

    protected function casts(): array {
        return [
            'status' => PackSubscriptionStatus::class,
            'price_paid' => 'decimal:2',
            'purchased_at' => 'datetime',
            'matures_at' => 'datetime',
            'renewal_window_ends_at' => 'datetime',
            'refunded_at' => 'datetime',
            'upgraded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function packTier(): BelongsTo {
        return $this->belongsTo(PackTier::class);
    }

    public function upgradedFromTier(): BelongsTo {
        return $this->belongsTo(PackTier::class, 'upgraded_from_tier_id');
    }

    public function slots(): HasMany {
        return $this->hasMany(PackSlot::class)->orderBy('slot_number');
    }

    public function purchaseTransaction(): BelongsTo {
        return $this->belongsTo(WalletTransaction::class, 'purchase_transaction_id');
    }

    public function upgradeTransaction(): BelongsTo {
        return $this->belongsTo(WalletTransaction::class, 'upgrade_transaction_id');
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

    public function isEligibleForRefund(): bool {
        if ($this->status !== PackSubscriptionStatus::ACTIVE) {
            return false;
        }

        if ($this->purchased_at->diffInDays(now()) > 3) {
            return false;
        }

        return $this->slots()->whereNotNull('funded_at')->doesntExist();
    }

    public function isEligibleForRealtimeUpgrade(): bool {
        return $this->status === PackSubscriptionStatus::ACTIVE;
    }
 

    public function estimateUpgradeCost(PackTier $newTier): float {
        $totalDays = max(1, (int) $this->packTier->duration_days);
        $remainingDays = $this->remainingDays();
        $fraction = min(1.0, $remainingDays / $totalDays);

        $fraction = $fraction === 0.0 ? ($this->remainingDays() > 0 ? $fraction : 0.0) : $fraction;
 
        $unusedCredit = round((float) $this->packTier->price * $fraction, 2);
 
        return max(0, round((float) $newTier->price - $unusedCredit, 2));
    }
 
    public function remainingDays(): int {
        return max(0, (int) round(now()->diffInDays($this->matures_at, false)));
    }
}