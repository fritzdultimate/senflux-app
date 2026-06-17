<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    // ── Static helpers ────────────────────────────────────────────────────────

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) return $default;
        return static::cast($setting->value, $setting->type);
    }

    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => (string) $value]);
    }

    public static function isEnabled(string $key): bool
    {
        return (bool) static::get($key, false);
    }

    private static function cast(mixed $value, string $type): mixed
    {
        return match($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'json'    => json_decode($value, true),
            default   => $value,
        };
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeInGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
