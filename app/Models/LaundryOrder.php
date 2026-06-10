<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaundryOrder extends Model
{
    protected $fillable = [
        'order_code',
        'customer_id',
        'service_id',
        'cashier_id',
        'weight',
        'quantity',
        'subtotal',
        'delivery_fee',
        'discount',
        'total_price',
        'order_source',
        'delivery_option',
        'status',
        'payment_status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}
