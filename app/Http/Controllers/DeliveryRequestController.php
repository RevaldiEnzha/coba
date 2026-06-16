<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRequest;
use App\Models\Invoice;
use App\Models\LaundryOrder;
use App\Models\OrderStatusHistory;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryRequestController extends Controller
{
    public function index()
    {
        $requests = DeliveryRequest::with(['customer.user', 'service', 'laundryOrder'])
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
            'service_id' => ['required', 'exists:services,id'],
            'address' => ['required', 'string'],
            'note' => ['nullable', 'string', 'max:500'],
            'scheduled_at' => ['required', 'date'],
        ]);

        DeliveryRequest::create([
            'customer_id' => $customer->id,
            'service_id' => $validated['service_id'],
            'laundry_order_id' => null,
            'type' => 'jemput',
            'address' => $validated['address'],
            'note' => $validated['note'] ?? null,
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

    public function confirm(Request $request, DeliveryRequest $deliveryRequest)
    {
        if ($deliveryRequest->laundry_order_id) {
            return redirect()
                ->route('delivery.index')
                ->with('success', 'Permintaan jemput ini sudah dibuatkan transaksi.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.1'],
        ]);

        $deliveryRequest->load(['customer', 'service']);

        $service = $deliveryRequest->service;

        if (!$service) {
            return back()->withErrors([
                'service_id' => 'Layanan pada permintaan jemput tidak ditemukan.',
            ]);
        }

        $amount = (float) $validated['amount'];

        $weight = $service->type === 'kiloan' ? $amount : null;
        $quantity = $service->type === 'satuan' ? (int) $amount : null;

        $subtotal = $amount * $service->price;
        $deliveryFee = $deliveryRequest->fee ?? 0;
        $discount = 0;
        $total = $subtotal + $deliveryFee - $discount;

        DB::transaction(function () use (
            $deliveryRequest,
            $service,
            $weight,
            $quantity,
            $subtotal,
            $deliveryFee,
            $discount,
            $total
        ) {
            $order = LaundryOrder::create([
                'order_code' => 'ORD-' . now()->format('YmdHis'),
                'customer_id' => $deliveryRequest->customer_id,
                'service_id' => $service->id,
                'cashier_id' => Auth::id(),
                'weight' => $weight,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => $discount,
                'total_price' => $total,
                'order_source' => 'portal',
                'delivery_option' => 'ambil_sendiri',
                'status' => 'diterima',
                'payment_status' => 'belum_bayar',
            ]);

            Invoice::create([
                'laundry_order_id' => $order->id,
                'invoice_code' => 'INV-' . now()->format('YmdHis'),
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'point_discount' => $discount,
                'total_amount' => $total,
                'status' => 'unpaid',
                'issued_at' => now(),
            ]);

            OrderStatusHistory::create([
                'laundry_order_id' => $order->id,
                'user_id' => Auth::id(),
                'status' => 'diterima',
                'note' => 'Transaksi dibuat dari permintaan jemput pelanggan.',
            ]);

            $deliveryRequest->update([
                'laundry_order_id' => $order->id,
                'status' => 'diproses',
            ]);
        });

        return redirect()
            ->route('delivery.index')
            ->with('success', 'Permintaan jemput berhasil dikonfirmasi menjadi transaksi resmi.');
    }
}
