<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LaundryOrder;
use App\Models\Payment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalCustomers = Customer::whereHas('user', function ($query) {
            $query->where('role', 'pelanggan');
        })->count();

        $todayTransactions = LaundryOrder::whereDate('created_at', $today)->count();

        $activeOrders = LaundryOrder::whereNotIn('status', [
            'selesai',
            'dibatalkan',
        ])->count();

        $monthlyIncome = Payment::whereNotNull('paid_at')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount_paid');

        $weeklyRevenue = $this->getWeeklyRevenue();

        $stats = [
            'total_customers' => number_format($totalCustomers, 0, ',', '.'),
            'today_transactions' => number_format($todayTransactions, 0, ',', '.'),
            'active_orders' => number_format($activeOrders, 0, ',', '.'),
            'monthly_income' => 'Rp ' . number_format($monthlyIncome, 0, ',', '.'),
        ];

        return view('dashboard.index', compact('stats', 'weeklyRevenue'));
    }

    private function getWeeklyRevenue(): array
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $period = CarbonPeriod::create($startOfWeek, '1 day', $endOfWeek);

        $weeklyRevenue = [];

        foreach ($period as $date) {
            $income = Payment::whereNotNull('paid_at')
                ->whereDate('paid_at', $date)
                ->sum('amount_paid');

            $weeklyRevenue[] = [
                'day' => $this->dayName($date),
                'value' => $income,
            ];
        }

        return $weeklyRevenue;
    }

    private function dayName(Carbon $date): string
    {
        return match ($date->format('D')) {
            'Mon' => 'Sen',
            'Tue' => 'Sel',
            'Wed' => 'Rab',
            'Thu' => 'Kam',
            'Fri' => 'Jum',
            'Sat' => 'Sab',
            'Sun' => 'Min',
            default => $date->format('D'),
        };
    }
}
