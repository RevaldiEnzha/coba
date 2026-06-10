<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'laundry_order_id',
        'invoice_code',
        'subtotal',
        'delivery_fee',
        'point_discount',
        'total_amount',
        'status',
        'issued_at',
    ];

    public function laundryOrder()
    {
        return $this->belongsTo(LaundryOrder::class);
    }
}
