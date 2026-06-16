<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'daily');

        if (!in_array($period, ['daily', 'weekly', 'monthly'])) {
            $period = 'daily';
        }

        [$startDate, $endDate, $periodLabel] = $this->resolveDateRange($request, $period);

        $payments = Payment::with('invoice.laundryOrder.customer.user')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ])
            ->get();

        $rows = $this->buildReportRows($payments, $period, $startDate, $endDate);

        $totalIncome = collect($rows)->sum('income');

        return view('reports.index', compact(
            'period',
            'periodLabel',
            'startDate',
            'endDate',
            'rows',
            'totalIncome'
        ));
    }

    private function resolveDateRange(Request $request, string $period): array
    {
        $from = $request->filled('from_date')
            ? Carbon::parse($request->from_date)
            : null;

        $to = $request->filled('to_date')
            ? Carbon::parse($request->to_date)
            : null;

        if ($period === 'daily') {
            $startDate = $from ?? now()->subDays(6);
            $endDate = $to ?? now();

            return [
                $startDate->startOfDay(),
                $endDate->endOfDay(),
                'Harian',
            ];
        }

        if ($period === 'weekly') {
            $startDate = $from ?? now()->startOfMonth();
            $endDate = $to ?? now()->endOfMonth();

            return [
                $startDate->startOfDay(),
                $endDate->endOfDay(),
                'Mingguan',
            ];
        }

        $startDate = $from
            ? $from->copy()->startOfMonth()
            : now()->subMonths(4)->startOfMonth();

        $endDate = $to
            ? $to->copy()->endOfMonth()
            : now()->endOfMonth();

        return [
            $startDate->startOfDay(),
            $endDate->endOfDay(),
            'Bulanan',
        ];
    }

    private function buildReportRows($payments, string $period, Carbon $startDate, Carbon $endDate): array
    {
        if ($period === 'daily') {
            $days = CarbonPeriod::create($startDate->copy()->startOfDay(), '1 day', $endDate->copy()->startOfDay());

            $rows = [];

            foreach ($days as $day) {
                $income = $payments
                    ->filter(fn ($payment) => Carbon::parse($payment->paid_at)->isSameDay($day))
                    ->sum('amount_paid');

                $rows[] = [
                    'label' => $day->format('d M'),
                    'income' => $income,
                ];
            }

            return $rows;
        }

        if ($period === 'weekly') {
            $rows = [];
            $weekNumber = 1;
            $cursor = $startDate->copy()->startOfDay();

            while ($cursor->lte($endDate)) {
                $weekStart = $cursor->copy();
                $weekEnd = $cursor->copy()->addDays(6)->endOfDay();

                if ($weekEnd->gt($endDate)) {
                    $weekEnd = $endDate->copy();
                }

                $income = $payments
                    ->filter(function ($payment) use ($weekStart, $weekEnd) {
                        $paidAt = Carbon::parse($payment->paid_at);

                        return $paidAt->between($weekStart, $weekEnd);
                    })
                    ->sum('amount_paid');

                $rows[] = [
                    'label' => 'Minggu ' . $weekNumber,
                    'income' => $income,
                ];

                $cursor->addDays(7);
                $weekNumber++;
            }

            return $rows;
        }

        $rows = [];
        $cursor = $startDate->copy()->startOfMonth();

        while ($cursor->lte($endDate)) {
            $month = $cursor->copy();

            $income = $payments
                ->filter(fn ($payment) => Carbon::parse($payment->paid_at)->isSameMonth($month))
                ->sum('amount_paid');

            $rows[] = [
                'label' => $month->format('M y'),
                'income' => $income,
            ];

            $cursor->addMonth();
        }

        return $rows;
    }
}
