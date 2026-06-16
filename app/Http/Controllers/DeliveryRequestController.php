<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryRequestController extends Controller
{
    public function index()
    {
        $requests = DeliveryRequest::with('customer.user')
            ->where('type', 'jemput')
            ->latest()
            ->get();

        return view('delivery.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $customer = $user->customer;

        if (!$customer) {
            abort(403, 'Data pelanggan tidak ditemukan.');
        }

        $validated = $request->validate([
            'address' => ['required', 'string'],
            'scheduled_at' => ['required', 'date'],
        ]);

        DeliveryRequest::create([
            'customer_id' => $customer->id,
            'laundry_order_id' => null,
            'type' => 'jemput',
            'address' => $validated['address'],
            'distance_km' => 0,
            'fee' => 0,
            'status' => 'menunggu_konfirmasi',
            'scheduled_at' => $validated['scheduled_at'],
        ]);

        return redirect()
            ->route('portal.dashboard')
            ->with('success', 'Permintaan jemput cucian berhasil dikirim.');
    }

    public function updateStatus(Request $request, DeliveryRequest $deliveryRequest)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:menunggu_konfirmasi,diproses,selesai,dibatalkan',
            ],
        ]);

        $deliveryRequest->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('delivery.index')
            ->with('success', 'Status permintaan jemput berhasil diperbarui.');
    }
}
