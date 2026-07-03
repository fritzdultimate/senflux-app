<?php

namespace App\Models;

use App\Enums\FormationState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formation extends Model
{
    protected $fillable = [
        'token_name', 'token_symbol', 'ecosystem', 'state', 'score', 'confidence',
        'capital_concentration', 'liquidity_migration', 'participation_growth', 'wallet_quality',
        'detected_at', 'state_changed_at', 'is_active', 'notes', 'set_by',
        'mint_address', 'dex', 'pair_address', 'pair_url', 'price_usd', 'liquidity_usd',
        'volume_24h', 'buys_24h', 'sells_24h', 'price_change_24h', 'market_data_synced_at',
    ];

    protected function casts(): array {
        return [
            'state' => FormationState::class,
            'is_active' => 'boolean',
            'detected_at' => 'datetime',
            'state_changed_at' => 'datetime',
            'price_usd' => 'decimal:10',
            'liquidity_usd' => 'decimal:2',
            'volume_24h' => 'decimal:2',
            'price_change_24h' => 'decimal:4',
            'market_data_synced_at' => 'datetime',
        ];
    }

    public function isVerifiable(): bool {
        return $this->mint_address !== null && $this->pair_url !== null;
    }

    public function marketDataFreshness(): ?string {
        return $this->market_data_synced_at?->diffForHumans();
    }

    public function setter(): BelongsTo {
        return $this->belongsTo(User::class, 'set_by');
    }

    public function deployedSlots(): HasMany {
        return $this->hasMany(PackSlot::class);
    }

    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    public function scopeAcceptingDeployments($query) {
        return $query->where('is_active', true)->where('state', FormationState::ACTIVE->value);
    }

    public function events() {
        return $this->hasMany(FormationEvent::class);
    }

    
    public function detectedAgo(): string {
        return ($this->state_changed_at ?? $this->detected_at ?? $this->created_at)->diffForHumans();
    }

    public function userDeploymentStatus(User $user): array {
        $deployedSlots = $this->deployedSlots()
            ->whereHas('subscription', fn ($q) => $q->where('user_id', $user->id))
            ->with('subscription.packTier')
            ->get();

        $eligibleSlots = $this->state->acceptsNewDeployments()
            ? PackSlot::whereHas('subscription', fn ($q) => $q->where('user_id', $user->id))
                ->where('status', \App\Enums\PackSlotStatus::FUNDED->value)
                ->whereNull('formation_id')
                ->with('subscription.packTier')
                ->get()
            : collect();

        return [
            'deployed_slots'      => $deployedSlots,
            'eligible_slots'      => $eligibleSlots,
            'has_deployed'        => $deployedSlots->isNotEmpty(),
            'can_deploy'          => $eligibleSlots->isNotEmpty(),
            'deployed_total_capital' => $deployedSlots->sum('capital_amount'),
            'deployed_total_profit'  => $deployedSlots->sum('realized_profit'),
        ];
    }

    public function deploymentSummary(): array {
        $slots = $this->deployedSlots();

        return [
            'total_slots'   => (clone $slots)->count(),
            'total_capital' => (clone $slots)->sum('capital_amount'),
        ];
    }

    public function hasComputedLiquidityMigration(): bool {
        return $this->isVerifiable()
            && FormationLiquiditySnapshot::where('formation_id', $this->id)
                ->where('created_at', '<=', now()->subHours(20))
                ->exists();
    }

    public function liquiditySnapshots(): HasMany {
        return $this->hasMany(FormationLiquiditySnapshot::class);
    }

    public function sparklineHeights(int $points = 5): array {
        $snapshots = $this->liquiditySnapshots()->latest('created_at')->limit($points)->get()->reverse()->values();

        if ($snapshots->count() < 2) {
            return array_fill(0, $points, 8);
        }

        $values = $snapshots->pluck('liquidity_usd')->map(fn ($v) => (float) $v);
        $min = $values->min();
        $range = max($values->max() - $min, 0.01);

        return $values->map(fn ($v) => 4 + (int) round((($v - $min) / $range) * 18))->toArray();
    }

    public function persistenceLevel(): string {
        return match (true) {
            $this->score >= 65 => 'STRONG',
            $this->score >= 40 => 'MODERATE',
            default => 'WEAK',
        };
    }

    public function participationLevel(): string {
        $change = abs((float) ($this->price_change_24h ?? 0));

        return match (true) {
            $change >= 50 => 'High',
            $change >= 15 => 'Moderate',
            default => 'Low',
        };
    }

    public function trendArrow(): string {
        $change = (float) ($this->price_change_24h ?? 0);

        return match (true) {
            $change > 5 => '↗',
            $change < -5 => '↓',
            default => '→',
        };
    }
}
