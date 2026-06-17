<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'label',
        'value',
        'unit',
        'description',
    ];

    public static function getValue(string $key, $default = null)
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function getNumber(string $key, float|int $default = 0): float
    {
        return (float) static::getValue($key, $default);
    }
}
