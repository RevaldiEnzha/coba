<?php

namespace App\Http\Controllers;

use App\Models\LaundryOrder;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TrackingController extends Controller
{
    public function index()
    {
        $orders = LaundryOrder::with(['customer.user', 'service'])
            ->latest()
            ->get();

        $statusOptions = [
            'diterima' => 'Masuk',
            'dicuci' => 'Sedang Dicuci',
            'dijemur' => 'Pengeringan',
            'disetrika' => 'Setrika',
            'siap_diambil' => 'Siap Diambil',
            'selesai' => 'Selesai',
        ];

        return view('tracking.index', compact('orders', 'statusOptions'));
    }

    public function updateStatus(Request $request, LaundryOrder $order)
    {
        $statusOptions = [
            'diterima',
            'dicuci',
            'dijemur',
            'disetrika',
            'siap_diambil',
            'selesai',
        ];

        $validated = $request->validate([
            'status' => ['required', Rule::in($statusOptions)],
        ]);

        DB::transaction(function () use ($order, $validated) {
            $order->update([
                'status' => $validated['status'],
            ]);

            OrderStatusHistory::create([
                'laundry_order_id' => $order->id,
                'user_id' => Auth::id(),
                'status' => $validated['status'],
                'note' => 'Status order diperbarui melalui halaman Order Tracking.',
            ]);
        });

        return redirect()
            ->route('tracking.index')
            ->with('success', 'Status order berhasil diperbarui.');
    }
}
