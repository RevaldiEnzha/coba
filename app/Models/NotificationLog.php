<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    // 1. Daftarkan kolom yang diizinkan untuk diisi secara massal (Mass Assignment)
    protected $fillable = [
        'laundry_order_id',
        'customer_id',
        'channel',
        'recipient',
        'message',
        'status',
        'error_message',
        'sent_at'
    ];

    // 2. Beri tahu Laravel bahwa kolom sent_at adalah format tanggal/waktu (DateTime)
    protected $casts = [
        'sent_at' => 'datetime',
    ];

    // 3. Relasi ke tabel LaundryOrder (Setiap log dimiliki oleh satu order)
    public function laundryOrder()
    {
        return $this->belongsTo(LaundryOrder::class);
    }

    // 4. Relasi ke tabel Customer (Setiap log ditujukan ke satu pelanggan)
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}