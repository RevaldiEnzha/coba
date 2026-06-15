<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'user_id',
        'method',
        'amount_paid',
        'change_amount',
        'paid_at',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
