<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'laundry_order_id',
        'type',
        'points',
        'description',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function laundryOrder()
    {
        return $this->belongsTo(LaundryOrder::class);
    }
}
