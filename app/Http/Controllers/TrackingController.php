<?php

namespace App\Http\Controllers;

use App\Models\LaundryOrder;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $query = LaundryOrder::with(['customer.user', 'service', 'cashier'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        $statuses = [
            'diterima',
            'dicuci',
            'dijemur',
            'disetrika',
            'siap_diambil',
            'selesai',
            'dibatalkan',
        ];

        return view('tracking.index', compact('orders', 'statuses'));
    }

    public function updateStatus(Request $request, LaundryOrder $order)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:diterima,dicuci,dijemur,disetrika,siap_diambil,selesai,dibatalkan',
            ],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($order, $validated) {
            $order->update([
                'status' => $validated['status'],
            ]);

            OrderStatusHistory::create([
                'laundry_order_id' => $order->id,
                'user_id' => Auth::id(),
                'status' => $validated['status'],
                'note' => $validated['note'] ?? 'Status cucian diperbarui.',
            ]);
        });

        return redirect()
            ->route('tracking.index')
            ->with('success', 'Status cucian berhasil diperbarui.');
    }
}
