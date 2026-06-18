<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalSetting extends Model
{
    protected $fillable = [
        'min_amount',
        'max_amount',
        'fee_type',
        'fee_value',
        'processing_days',
        'allowed_networks',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'min_amount'       => 'decimal:2',
            'max_amount'       => 'decimal:2',
            'fee_value'        => 'decimal:4',
            'processing_days'  => 'integer',
            'allowed_networks' => 'array',
            'is_enabled'       => 'boolean',
        ];
    }

    /**
     * There's only ever one row of withdrawal settings — this is the
     * standard accessor for that singleton pattern.
     */
    public static function current(): self
    {
        return static::first() ?? static::create([
            'min_amount'       => 10,
            'max_amount'       => 50000,
            'fee_type'         => 'percentage',
            'fee_value'        => 0,
            'processing_days'  => 1,
            'allowed_networks' => ['sol', 'bsc', 'eth', 'trc20'],
            'is_enabled'       => true,
        ]);
    }

    public function calculateFee(float $amount): float
    {
        return $this->fee_type === 'percentage'
            ? round($amount * (float) $this->fee_value, 8)
            : (float) $this->fee_value;
    }
}
