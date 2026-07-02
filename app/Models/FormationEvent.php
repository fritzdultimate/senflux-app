<?php

namespace App\Models;

use App\Enums\FormationEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormationEvent extends Model {
    public const UPDATED_AT = null;

    protected $fillable = ['formation_id', 'type', 'message', 'meta'];

    protected function casts(): array {
        return [
            'type' => FormationEventType::class,
            'meta' => 'array',
        ];
    }

    public function formation(): BelongsTo {
        return $this->belongsTo(Formation::class);
    }

    public function scopeRecent($query, int $limit = 20) {
        return $query->latest('created_at')->limit($limit);
    }
}