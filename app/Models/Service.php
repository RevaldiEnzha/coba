<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'type',
        'price',
        'estimated_hours',
        'is_active',
    ];

    public function orders()
    {
        return $this->hasMany(LaundryOrder::class);
    }
}
