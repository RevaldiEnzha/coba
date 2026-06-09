<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'points_balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
