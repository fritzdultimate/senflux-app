<?php

namespace App\Models;

use App\Enums\BotDeploymentStatus;
use App\Enums\MarketFormationState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketFormationStateModel extends Model
{
    protected $table = 'market_formation_states';

    protected $fillable = [
        'state',
        'ecosystem',
        'bot_status',
        'active_wallets',
        'liquidity_score',
        'participation_score',
        'formation_score',
        'earnings_multiplier',
        'notes',
        'set_by',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'liquidity_score'     => 'decimal:2',
            'participation_score' => 'decimal:2',
            'formation_score'     => 'decimal:2',
            'earnings_multiplier' => 'decimal:4',
            'is_current'          => 'boolean',
            'state' => MarketFormationState::class,
            'bot_status' => BotDeploymentStatus::class,
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function setter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    // ── Scopes & helpers ─────────────────────────────────────────────────────

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public static function getCurrent(): ?self
    {
        return static::where('is_current', true)->latest()->first();
    }

    /**
     * Set this as the current state, deactivating the previous one.
     */
    public static function setAsCurrentState(
        MarketFormationState $state,
        BotDeploymentStatus $botStatus = BotDeploymentStatus::DEPLOYED,
        float $multiplier = 1.0,
        ?int $setBy = null,
        ?string $notes = null,
    ): self {
        // Deactivate existing current
        static::where('is_current', true)->update(['is_current' => false]);

        return static::create([
            'state'               => $state->value,
            'bot_status'          => $botStatus->value,
            'earnings_multiplier' => $multiplier,
            'is_current'          => true,
            'set_by'              => $setBy,
            'notes'               => $notes,
        ]);
    }
}