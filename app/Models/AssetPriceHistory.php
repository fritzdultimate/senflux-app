<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetPriceHistory extends Model
{
    public $timestamps = false; // recorded_at is the meaningful timestamp; created_at/updated_at unnecessary

    protected $fillable = [
        'tracked_asset_id',
        'price',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'price'       => 'decimal:8',
            'recorded_at' => 'datetime',
        ];
    }

    public function trackedAsset(): BelongsTo
    {
        return $this->belongsTo(TrackedAsset::class);
    }

    public function scopeForAsset($query, int $assetId)
    {
        return $query->where('tracked_asset_id', $assetId);
    }

    public function scopeSince($query, \DateTimeInterface $date)
    {
        return $query->where('recorded_at', '>=', $date);
    }
}
