<?php

namespace App\Models;

use App\Enums\TradeActivitySource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormationTradeActivity extends Model {
    protected $fillable = ['formation_id', 'tx_signature', 'slot', 'block_time', 'source', 'failed'];

    protected function casts(): array {
        return [
            'block_time' => 'datetime',
            'source' => TradeActivitySource::class,
            'failed' => 'boolean',
        ];
    }

    public function formation(): BelongsTo {
        return $this->belongsTo(Formation::class);
    }

    public function explorerUrl(): string {
        return "https://solscan.io/tx/{$this->tx_signature}";
    }
}