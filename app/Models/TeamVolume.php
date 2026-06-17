<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamVolume extends Model
{
    protected $fillable = [
        'user_id',
        'level_1', 'level_2', 'level_3', 'level_4',
        'level_5', 'level_6', 'level_7', 'level_8',
        'raw_total',
        'weighted_total',
        'last_computed_at',
    ];

    protected function casts(): array
    {
        return [
            'level_1'          => 'decimal:2',
            'level_2'          => 'decimal:2',
            'level_3'          => 'decimal:2',
            'level_4'          => 'decimal:2',
            'level_5'          => 'decimal:2',
            'level_6'          => 'decimal:2',
            'level_7'          => 'decimal:2',
            'level_8'          => 'decimal:2',
            'raw_total'        => 'decimal:2',
            'weighted_total'   => 'decimal:2',
            'last_computed_at' => 'datetime',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getLevelVolume(int $level): float
    {
        return (float) ($this->{"level_{$level}"} ?? 0);
    }

    public function getIsStaleAttribute(): bool
    {
        return !$this->last_computed_at || $this->last_computed_at->diffInHours(now()) > 6;
    }
}