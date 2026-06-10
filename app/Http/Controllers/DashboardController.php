<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (!in_array(auth()->user()->role, ['admin', 'kasir'])) {
            abort(403, 'Akses dashboard hanya untuk Admin/Kasir.');
        }

        $totalCustomers = Customer::count();

        // Belum ada modul transaksi
        $todayTransactions = 0;

        // Belum ada modul order
        $activeOrders = 0;

        // Belum ada modul pembayaran/invoice
        $monthlyIncome = 0;

        $weeklyRevenue = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::today()->subDays($i);

            $count = Customer::whereDate(
                'created_at',
                $date
            )->count();

            $weeklyRevenue[] = [
                'day' => $date->translatedFormat('D'),
                'value' => $count,
            ];
        }

        $stats = [
            'total_customers' => $totalCustomers,
            'today_transactions' => $todayTransactions,
            'active_orders' => $activeOrders,
            'monthly_income' => $monthlyIncome,
        ];

        return view(
            'dashboard.index',
            compact('stats', 'weeklyRevenue')
        );
    }
}
