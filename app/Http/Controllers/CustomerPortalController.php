<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class CustomerPortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $customer = $user->customer;

        $stats = [
            'active_orders' => 0,
            'completed_orders' => 0,
            'points' => $customer->points_balance ?? 0,
        ];

        return view(
            'portal.dashboard',
            compact('customer', 'stats')
        );
    }
}