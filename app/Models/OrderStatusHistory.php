<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $fillable = [
        'laundry_order_id',
        'user_id',
        'status',
        'note',
    ];

    public function laundryOrder()
    {
        return $this->belongsTo(LaundryOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
