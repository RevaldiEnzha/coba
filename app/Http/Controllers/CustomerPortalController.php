<?php

namespace App\Http\Controllers;

use App\Models\LaundryOrder;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

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

        $services = Service::where('is_active', true)->get();

        return view('portal.dashboard', compact(
            'customer',
            'activeOrders',
            'completedOrders',
            'recentOrders',
            'services'
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

    public function active()
    {
        $customer = Auth::user()->customer;
        $activeOrders = LaundryOrder::with(['service', 'invoice'])
            ->where('customer_id', $customer->id)
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->latest()
            ->get();

        return view('portal.active', compact('activeOrders'));
    }

    public function history()
    {
        $customer = Auth::user()->customer;
        $completedOrders = LaundryOrder::with(['service', 'invoice'])
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->latest()
            ->get();

        return view('portal.history', compact('completedOrders'));
    }

    public function points()
    {
        $customer = Auth::user()->customer;
        
        // Menggunakan nama kelas absolut untuk menghindari error Namespace Not Found
        $pointTransactions = \App\Models\PointTransaction::where('customer_id', $customer->id)
            ->latest()
            ->get();

        return view('portal.points', compact('customer', 'pointTransactions'));
    }

    public function account()
    {
        $user = Auth::user();
        $customer = $user->customer;

        return view('portal.account', compact('user', 'customer'));
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        // Validasi input form
        $request->validate([
            'password_sekarang' => 'required',
            'password_baru' => 'nullable|min:8|confirmed',
        ], [
            'password_sekarang.required' => 'Password saat ini wajib diisi untuk verifikasi keamanan.',
            'password_baru.min' => 'Password baru minimal harus terdiri dari 8 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Verifikasi apakah password lama yang dimasukkan sudah benar
        if (!Hash::check($request->password_sekarang, $user->password)) {
            return back()->withErrors(['password_sekarang' => 'Password saat ini yang Anda masukkan salah.']);
        }

        // Jika user mengisi field password baru, lakukan pembaruan data
        if ($request->filled('password_baru')) {
            $user->password = Hash::make($request->password_baru);
            $user->save();
            return back()->with('success', 'Password akun Anda berhasil diperbarui!');
        }

        return back()->with('info', 'Tidak ada perubahan data password dilakukan.');
    }
}