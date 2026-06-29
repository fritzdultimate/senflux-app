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
    ];

    protected function casts(): array
    {
        return [
            'state' => FormationState::class,
            'is_active' => 'boolean',
            'detected_at' => 'datetime',
            'state_changed_at' => 'datetime',
        ];
    }

    public function setter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    public function deployedSlots(): HasMany
    {
        return $this->hasMany(PackSlot::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAcceptingDeployments($query)
    {
        return $query->where('is_active', true)->where('state', FormationState::ACTIVE->value);
    }

    /**
     * "18 mins ago" style — anchored to the last state change, not
     * creation, so a formation that's been tracked for days but just
     * flipped to ACTIVE an hour ago correctly reads as "Active · 1 hour
     * ago," not "3 days ago."
     */
    public function detectedAgo(): string
    {
        return ($this->state_changed_at ?? $this->detected_at ?? $this->created_at)->diffForHumans();
    }
}
