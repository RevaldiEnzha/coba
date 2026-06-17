<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRequest extends Model
{
    protected $fillable = [
        'customer_id',
        'laundry_order_id',
        'service_id',
        'type',
        'address',
        'distance_km',
        'fee',
        'status',
        'note',
        'scheduled_at'
    ];
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function laundryOrder()
    {
        return $this->belongsTo(LaundryOrder::class);
    }
}
