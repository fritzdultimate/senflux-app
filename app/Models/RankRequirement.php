<?php

namespace App\Models;

use App\Enums\RankLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RankRequirement extends Model {
    protected $fillable = [
        'rank',
        'label',
        'team_volume_usd',
        'personal_deposit_usd',
        'direct_referrals',
        'cash_bonus',
        'leadership_match_rate',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'team_volume_usd'      => 'decimal:2',
            'personal_deposit_usd' => 'decimal:2',
            'direct_referrals'     => 'integer',
            'cash_bonus'           => 'decimal:2',
            'leadership_match_rate'=> 'decimal:4',
            'sort_order'           => 'integer',
            'is_active'            => 'boolean',
            'rank'                 => RankLevel::class,
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function advancements(): HasMany
    {
        return $this->hasMany(RankAdvancement::class, 'to_rank', 'rank');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeForRank($query, RankLevel $rank)
    {
        return $query->where('rank', $rank->value);
    }
}

