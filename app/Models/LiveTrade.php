<?php

namespace App\Models;

use App\Enums\TradeStatus;
use App\Enums\TradeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveTrade extends Model
{
    protected $fillable = [
        'tracked_asset_id',
        'type',
        'entry_price',
        'current_price',
        'exit_price',
        'status',
        'pnl_amount',
        'pnl_percent',
        'opened_at',
        'closed_at',
        'created_by',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (LiveTrade $trade) {
            $trade->created_by ??= auth()->id();
            $trade->status ??= TradeStatus::OPEN->value;
            // Mirror entry price as the initial current price until the
            // price sync job (or a manual edit) updates it.
            $trade->current_price ??= $trade->entry_price;
        });
    }

    protected function casts(): array {
        return [
            'entry_price'   => 'decimal:8',
            'current_price' => 'decimal:8',
            'exit_price'    => 'decimal:8',
            'pnl_amount'    => 'decimal:2',
            'pnl_percent'   => 'decimal:4',
            'opened_at'     => 'datetime',
            'closed_at'     => 'datetime',
            'type'          => TradeType::class,
            'status'        => TradeStatus::class,
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────

    public function trackedAsset(): BelongsTo
    {
        return $this->belongsTo(TrackedAsset::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Business logic ───────────────────────────────────────────────────

    /**
     * Recompute P&L from current_price (or exit_price if closed).
     */
    public function recomputePnl(): void
    {
        $referencePrice = $this->status === TradeStatus::CLOSED
            ? $this->exit_price
            : $this->current_price;

        if (!$referencePrice || !$this->entry_price) return;

        $entry = (float) $this->entry_price;
        $ref   = (float) $referencePrice;

        $direction = $this->type === TradeType::LONG ? 1 : -1;
        $pctChange = (($ref - $entry) / $entry) * 100 * $direction;

        $this->pnl_percent = round($pctChange, 4);
        // Dollar P&L is illustrative only since there's no real position size tracked yet.
        $this->pnl_amount  = round(($pctChange / 100) * $entry, 2);
    }

    public function close(float $exitPrice): void {
        $this->exit_price = $exitPrice;
        $this->current_price = $exitPrice;
        $this->status = TradeStatus::CLOSED;
        $this->closed_at = now();
        $this->recomputePnl();
        $this->save();
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', TradeStatus::OPEN->value);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', TradeStatus::CLOSED->value);
    }
}
