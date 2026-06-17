<?php

namespace App\Http\Controllers;

use App\Models\LaundryOrder;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $cleanSearch = preg_replace('/[^0-9]/', '', $search);

        $orders = \App\Models\LaundryOrder::with(['customer.user', 'service'])
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->when($search, function ($query) use ($search, $cleanSearch) {
                $query->where(function ($q) use ($search, $cleanSearch) {
                    $q->whereHas('customer.user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
                    if ($cleanSearch !== '') {
                        $q->orWhere('id', (int) $cleanSearch);
                    }
                });
            })
            ->latest()
            ->paginate(10)->appends(request()->query());

        // KEMBALIKAN VARIABEL INI: Dibutuhkan untuk opsi dropdown ubah status
        $statusOptions = [
            'diterima' => 'Diterima',
            'dicuci' => 'Sedang Dicuci',
            'dijemur' => 'Sedang Dijemur',
            'disetrika' => 'Sedang Disetrika',
            'siap_diambil' => 'Siap Diambil / Diantar',
            'selesai' => 'Selesai (Sudah Diserahkan)'
        ];

        return view('tracking.index', compact('orders', 'search', 'statusOptions'));
    }

    public function updateStatus(Request $request, LaundryOrder $order)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:diterima,dicuci,dijemur,disetrika,siap_diambil,selesai',
            ],
        ]);

        // 1. Update status cucian di database
        $order->update([
            'status' => $validated['status'],
        ]);

        // 2. Logika Pengiriman WhatsApp Otomatis
        $waSent = false; // Penanda awal (dianggap belum terkirim)

        if ($validated['status'] === 'siap_diambil') {
            $customer = $order->customer;

            // Pastikan data pelanggan dan nomor HP tersedia
            if ($customer && $customer->phone) {
                $phone = $customer->phone;
                $name = $customer->user->name ?? 'Pelanggan';
                $orderId = 'ORD-' . str_pad($order->id, 3, '0', STR_PAD_LEFT);
                $total = number_format($order->total_price, 0, ',', '.');

                // Susun isi pesan WhatsApp lengkap
                $message = "Halo *$name*!\n\nCucian Anda dengan No. Order *$orderId* sudah *SIAP DIAMBIL* di outlet kami.\n\nTotal Tagihan: Rp $total\n\nTerima kasih telah mempercayakan cucian Anda kepada kami!";

                try {
                    // Tembak API Fonnte
                    $response = Http::withHeaders([
                        'Authorization' => env('FONNTE_TOKEN')
                    ])->post('https://api.fonnte.com/send', [
                        'target' => $phone,
                        'message' => $message,
                        'countryCode' => '62',
                    ]);

                    $responseData = $response->json();
                    
                    // Cek apakah respons dari server Fonnte sukses
                    if ($response->successful() && isset($responseData['status']) && $responseData['status']) {
                        $waSent = true; // Tandai bahwa WA sukses terkirim!
                        
                        NotificationLog::create([
                            'laundry_order_id' => $order->id,
                            'customer_id' => $customer->id,
                            'channel' => 'whatsapp',
                            'recipient' => $phone,
                            'message' => $message,
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]);
                    } else {
                        // Jika server merespon tapi ada masalah (misal nomor tidak valid)
                        NotificationLog::create([
                            'laundry_order_id' => $order->id,
                            'customer_id' => $customer->id,
                            'channel' => 'whatsapp',
                            'recipient' => $phone,
                            'message' => $message,
                            'status' => 'failed',
                            'error_message' => $responseData['reason'] ?? 'Unknown API Error',
                        ]);
                    }

                } catch (\Exception $e) {
                    // Jika gagal total (misal internet putus atau server Fonnte down)
                    NotificationLog::create([
                        'laundry_order_id' => $order->id,
                        'customer_id' => $customer->id,
                        'channel' => 'whatsapp',
                        'recipient' => $phone,
                        'message' => $message,
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }
        }

        // ==========================================
        // 3. PENGECEKAN NOTIFIKASI UNTUK KASIR
        // ==========================================
        
        // Jika statusnya Siap Diambil TAPI WA gagal terkirim (atau pelanggan tidak punya no HP)
        if ($validated['status'] === 'siap_diambil' && !$waSent) {
            return redirect()
                ->route('tracking.index')
                ->with('info', 'Status diperbarui, NAMUN pesan WA GAGAL terkirim.');
        }

        // Jika berhasil semua (atau untuk perubahan status selain "siap_diambil")
        return redirect()
            ->route('tracking.index')
            ->with('success', 'Status cucian berhasil diperbarui.');
    }

}
