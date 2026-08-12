<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model {
    protected $fillable = [
        'provider',
        'api_key',
        'ipn_secret',
        'webhook_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'api_key' => 'encrypted',
        'ipn_secret' => 'encrypted',
    ];

    protected static function booted() {
        static::saving(function (PaymentSetting $setting) {
            if ($setting->is_active) {
                static::where('id', '!=', $setting->id)->update(['is_active' => false]);
            }
        });
    }
}
