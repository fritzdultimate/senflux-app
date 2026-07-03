<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormationWatchlistItem extends Model {
    protected $fillable = ['mint_address', 'token_symbol', 'sector', 'ecosystem', 'is_active', 'formation_id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUndetected($query)
    {
        return $query->whereNull('formation_id');
    }
}