<?php
// app/Models/FormationLiquiditySnapshot.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormationLiquiditySnapshot extends Model {
    public const UPDATED_AT = null;

    protected $fillable = ['formation_id', 'liquidity_usd'];

    protected function casts(): array
    {
        return ['liquidity_usd' => 'decimal:2'];
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }
}