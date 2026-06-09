<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        if (!in_array(auth()->user()->role, ['admin', 'kasir'])) {
            abort(403, 'Akses dashboard hanya untuk Admin/Kasir.');
        }

        $stats = [
            'total_customers' => '1,234',
            'today_transactions' => '42',
            'active_orders' => '87',
            'monthly_income' => 'Rp 45,2M',
        ];

        $weeklyRevenue = [
            ['day' => 'Sen', 'value' => 2400],
            ['day' => 'Sel', 'value' => 1400],
            ['day' => 'Rab', 'value' => 10000],
            ['day' => 'Kam', 'value' => 3900],
            ['day' => 'Jum', 'value' => 4800],
            ['day' => 'Sab', 'value' => 3800],
            ['day' => 'Min', 'value' => 4300],
        ];

        return view('dashboard.index', compact('stats', 'weeklyRevenue'));
    }
}
