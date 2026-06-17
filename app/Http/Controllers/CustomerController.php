<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index(Request $request)
{
        $search = $request->search;

        $customers = Customer::with('user')
            ->whereHas('user', function ($query) {
                $query->where('role', 'pelanggan');
            })
            ->when($search, function ($query) use ($search) {
                $cleanSearch = preg_replace('/[^0-9]/', '', $search);

                $query->where(function ($q) use ($search, $cleanSearch) {
                    $q->where('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");

                    if ($cleanSearch !== '') {
                        $q->orWhere('id', (int) $cleanSearch);
                    }

                    $q->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->get();

        return view('customers.index', compact('customers', 'search'));
    }
    public function create()
    {
         return redirect()->route('customers.index');
    }

    public function edit(Customer $customer)
    {
        // PERBAIKAN: Tampilkan halaman edit, jangan di-redirect!
        return view('customers.edit', compact('customer'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'phone' => ['required', 'string', 'max:20', 'unique:customers,phone'],
        'address' => ['required', 'string'],
        '_mode' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $username = $this->generateUniqueUsername($validated['name']);
            $email = $this->generateUniqueEmail($username);

            $user = User::create([
                'name' => $validated['name'],
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('pelanggan123'),
                'role' => 'pelanggan',
            ]);

            Customer::create([
                'user_id' => $user->id,
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'points_balance' => 0,
            ]);
        });

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }



    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $customer->user_id,
            'phone'    => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'address'  => 'required|string',
            'password' => 'nullable|min:8',
        ], [
            'username.unique' => 'Username ini sudah dipakai oleh pengguna lain.',
            'phone.unique'    => 'Nomor WhatsApp ini sudah terdaftar di sistem.',
        ]);

        // Simpan ke tabel users
        $customer->user->name = $request->name;
        $customer->user->username = $request->username;
        if ($request->filled('password')) {
            $customer->user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $customer->user->save();

        // Simpan ke tabel customers
        $customer->update([
            'phone'   => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Data pelanggan berhasil diperbarui!');
    }
}
