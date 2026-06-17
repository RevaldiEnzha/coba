<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $cleanSearch = preg_replace('/[^0-9]/', '', $search);

        $invoices = \App\Models\Invoice::with(['laundryOrder.customer.user'])
            ->when($search, function ($query) use ($search, $cleanSearch) {
                $query->where(function ($q) use ($search, $cleanSearch) {
                    $q->where('invoice_code', 'like', "%{$search}%")
                      ->orWhereHas('laundryOrder.customer.user', function ($u) use ($search) {
                          $u->where('name', 'like', "%{$search}%");
                      });
                    if ($cleanSearch !== '') {
                        $q->orWhere('laundry_order_id', (int) $cleanSearch);
                    }
                });
            })
            ->latest()
            ->paginate(10)->appends(request()->query());

        // KEMBALIKAN VARIABEL INI: Dibutuhkan untuk memunculkan struk (modal) setelah bayar
        // Biasanya diambil dari Session setelah fungsi bayar berhasil
        $paidInvoice = session('paidInvoice') ?? null;

        return view('payments.index', compact('invoices', 'search', 'paidInvoice'));
    }

    public function process(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'method' => ['required', 'in:cash,qris,transfer'],
            'points_used' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($invoice->status === 'paid') {
            return redirect()
                ->route('payments.index')
                ->with('success', 'Invoice ini sudah lunas.');
        }

        DB::transaction(function () use ($invoice, $validated) {
            $invoice->load('laundryOrder.customer');

            $order = $invoice->laundryOrder;
            $customer = $order->customer;

            $pointsUsed = (int) ($validated['points_used'] ?? 0);

            /*
             * Aturan sementara:
             * 1 poin = Rp 100 diskon.
             * Nanti bisa dipindah ke tabel settings/konfigurasi.
             */
            $pointValue = 100;
            $availablePoints = $customer->points_balance ?? 0;
            $validPointsUsed = min($pointsUsed, $availablePoints);

            $pointDiscount = $validPointsUsed * $pointValue;
            $baseTotal = $invoice->subtotal + $invoice->delivery_fee;
            $finalTotal = max(0, $baseTotal - $pointDiscount);

            $invoice->update([
                'point_discount' => $pointDiscount,
                'total_amount' => $finalTotal,
                'status' => 'paid',
            ]);

            Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => Auth::id(),
                'method' => $validated['method'],
                'amount_paid' => $finalTotal,
                'change_amount' => 0,
                'paid_at' => now(),
            ]);

            $order->update([
                'payment_status' => 'dibayar',
            ]);

            if ($validPointsUsed > 0) {
                $customer->decrement('points_balance', $validPointsUsed);

                PointTransaction::create([
                    'customer_id' => $customer->id,
                    'laundry_order_id' => $order->id,
                    'type' => 'redeem',
                    'points' => $validPointsUsed,
                    'description' => 'Penukaran poin untuk pembayaran invoice ' . $invoice->invoice_code,
                ]);
            }

            /*
             * Aturan sementara:
             * pelanggan mendapat 1 poin setiap Rp 10.000 pembayaran.
             */
            $earnedPoints = (int) floor($finalTotal / 10000);

            if ($earnedPoints > 0) {
                $customer->increment('points_balance', $earnedPoints);

                PointTransaction::create([
                    'customer_id' => $customer->id,
                    'laundry_order_id' => $order->id,
                    'type' => 'earn',
                    'points' => $earnedPoints,
                    'description' => 'Poin dari pembayaran invoice ' . $invoice->invoice_code,
                ]);
            }
        });

        return redirect()
            ->route('payments.index', ['paid' => $invoice->id])
            ->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }
}
