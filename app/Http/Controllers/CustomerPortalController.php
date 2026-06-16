<?php

namespace App\Http\Controllers;

use App\Models\LaundryOrder;
use Illuminate\Support\Facades\Auth;

class CustomerPortalController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $customer = $user->customer;

        if (!$customer) {
            abort(403, 'Data pelanggan tidak ditemukan.');
        }

        $activeOrders = LaundryOrder::with(['service', 'invoice'])
            ->where('customer_id', $customer->id)
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->latest()
            ->get();

        $completedOrders = LaundryOrder::with(['service', 'invoice'])
            ->where('customer_id', $customer->id)
            ->where('status', 'selesai')
            ->latest()
            ->get();

        $recentOrders = LaundryOrder::with(['service', 'invoice'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('portal.dashboard', compact(
            'customer',
            'activeOrders',
            'completedOrders',
            'recentOrders'
        ));
    }

    public function show(LaundryOrder $order)
    {
        $user = Auth::user();
        $customer = $user->customer;

        if (!$customer || $order->customer_id !== $customer->id) {
            abort(403, 'Anda tidak memiliki akses ke order ini.');
        }

        $order->load(['service', 'invoice', 'statusHistories']);

        return view('portal.show', compact('order', 'customer'));
    }
}
