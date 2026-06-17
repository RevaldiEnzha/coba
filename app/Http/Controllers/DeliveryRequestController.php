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
    public function index(Request $request)
    {
        $search = $request->search;
        // Bersihkan teks (Misal: "JMP-005" menjadi "5" agar bisa dicari di database)
        $cleanSearch = preg_replace('/[^0-9]/', '', $search);

        // 1. Query untuk tabel JEMPUT (Batas 5 per halaman)
        $pickups = DeliveryRequest::with(['customer.user', 'service', 'laundryOrder'])
            ->where('type', 'jemput')
            ->when($search, function ($query) use ($search, $cleanSearch) {
                $query->where(function($q) use ($search, $cleanSearch) {
                    $q->where('address', 'like', "%{$search}%")
                      ->orWhereHas('customer.user', function($u) use ($search) {
                          $u->where('name', 'like', "%{$search}%");
                      });
                    if ($cleanSearch !== '') {
                        $q->orWhere('id', (int) $cleanSearch);
                    }
                });
            })
            ->latest()
            // Menggunakan nama page khusus 'pickup_page' agar tidak bentrok
            ->paginate(5, ['*'], 'pickup_page')
            ->appends(request()->query()); 

        // 2. Query untuk tabel ANTAR (Batas 5 per halaman)
        $deliveries = DeliveryRequest::with(['customer.user', 'laundryOrder'])
            ->where('type', 'antar')
            ->when($search, function ($query) use ($search, $cleanSearch) {
                $query->where(function($q) use ($search, $cleanSearch) {
                    $q->where('address', 'like', "%{$search}%")
                      ->orWhereHas('customer.user', function($u) use ($search) {
                          $u->where('name', 'like', "%{$search}%");
                      });
                    if ($cleanSearch !== '') {
                        $q->orWhere('id', (int) $cleanSearch);
                    }
                });
            })
            ->latest()
            // Menggunakan nama page khusus 'delivery_page' agar tidak bentrok
            ->paginate(5, ['*'], 'delivery_page')
            ->appends(request()->query());

        return view('delivery.index', compact('pickups', 'deliveries', 'search'));
    }

    // ... fungsi index() yang sudah ada ...

    public function store(Request $request)
    {
        // 1. Validasi Input form dan Peta
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'latitude' => 'required',
            'longitude' => 'required',
            'address_main' => 'required',
            'address_detail' => 'required',
            'scheduled_at' => 'nullable|date',
        ], [
            'service_id.required' => 'Silakan pilih jenis layanan terlebih dahulu.',
            'latitude.required' => 'Silakan tentukan titik lokasi pada peta.',
            'address_detail.required' => 'Detail patokan alamat wajib diisi.',
        ]);

        $customer = Auth::user()->customer;
        if (!$customer) {
            abort(403, 'Data pelanggan tidak ditemukan.');
        }

        // 2. Rumus Haversine: Menghitung Jarak Jemput (KM)
        $outletLat = -7.428940;
        $outletLng = 109.337930; 

        $earthRadius = 6371; 
        $latFrom = deg2rad((float) $outletLat);
        $lonFrom = deg2rad((float) $outletLng);
        $latTo = deg2rad((float) $request->latitude);
        $lonTo = deg2rad((float) $request->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        $distance = round($earthRadius * $c, 2);

        // 3. Logika Biaya Jemput
        // Contoh: Gratis 2 KM pertama, selebihnya Rp 3.000 / KM
        $fee = 0;
        if ($distance > 2) {
            $kelebihanKm = ceil($distance - 2); 
            $fee = $kelebihanKm * 3000;
        }

        // 4. Gabungkan Alamat
        $fullAddress = $request->address_main . ' (Detail: ' . $request->address_detail . ')';

        // 5. Simpan ke database
        DeliveryRequest::create([
            'customer_id' => $customer->id,
            'laundry_order_id' => null, // Dikosongkan karena order resminya belum dibuat kasir
            'service_id' => $request->service_id,
            'type' => 'jemput',
            'address' => $fullAddress,
            'distance_km' => $distance,
            'fee' => $fee,
            'status' => 'menunggu_konfirmasi',
            'note' => $request->note,
            'scheduled_at' => $request->scheduled_at,
        ]);

        return redirect()
            ->route('portal.active')
            ->with('success', "Permintaan jemput berhasil dikirim! Jarak tercatat: {$distance} KM (Biaya: Rp " . number_format($fee, 0, ',', '.') . "). Silakan siapkan cucian Anda, kurir kami akan segera datang.");
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

        // Mengubah pesannya menjadi lebih umum (karena bisa jemput atau antar)
        return redirect()
            ->route('delivery.index')
            ->with('success', 'Status permintaan (jemput/antar) berhasil diperbarui.');
    }

    public function confirm(Request $request, DeliveryRequest $deliveryRequest)
    {
        if ($deliveryRequest->laundry_order_id) {
            return redirect()
                ->route('delivery.index')
                ->with('success', 'Permintaan jemput ini sudah dibuatkan transaksi.');
        }

        if ($deliveryRequest->status !== 'selesai') {
            return back()->withErrors([
                'amount' => 'Transaksi gagal dibuat. Status penjemputan harus "Selesai Dijemput" terlebih dahulu agar cucian bisa ditimbang dengan akurat.'
            ]);
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
