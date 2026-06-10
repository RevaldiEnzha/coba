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
        return redirect()->route('customers.index');
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
        $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'phone' => ['required', 'string', 'max:20', 'unique:customers,phone,' . $customer->id],
        'address' => ['required', 'string'],
        '_mode' => ['nullable', 'string'],
        'customer_id' => ['nullable'],
        ]);

        DB::transaction(function () use ($validated, $customer) {
            $customer->user->update([
                'name' => $validated['name'],
            ]);

            $customer->update([
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);
        });

        return redirect()
            ->route('customers.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    private function generateUniqueEmail(string $username): string
    {
        $email = $username . '@customer.local';
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $email = $username . $counter . '@customer.local';
            $counter++;
        }

        return $email;
    }

    private function generateUniqueUsername(string $name): string
    {
        $base = Str::lower(Str::slug($name, ''));
        $base = $base !== '' ? $base : 'pelanggan';

        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    public function destroy(Customer $customer)
    {
        DB::transaction(function () use ($customer) {
            $user = $customer->user;

            $customer->delete();

            if ($user) {
                $user->delete();
            }
        });

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}
