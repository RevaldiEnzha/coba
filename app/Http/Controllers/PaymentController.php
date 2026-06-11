<?php

namespace App\Http\Controllers;

use App\Models\LaundryOrder;

class PaymentController extends Controller
{
    public function index()
    {
        $orders = LaundryOrder::with([
            'customer.user',
            'invoice'
        ])
        ->where('payment_status','belum_bayar')
        ->latest()
        ->get();

        return view(
            'payments.index',
            compact('orders')
        );
    }
}