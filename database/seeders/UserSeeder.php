<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => '123123123',
            'role' => 'admin',
        ]);

        // Customer / Pelanggan
        $pelanggan = User::create([
            'name' => 'Pelanggan',
            'username' => 'pelanggan',
            'email' => 'pelanggan@gmail.com',
            'password' => '123123123',
            'role' => 'pelanggan',
        ]);

        Customer::create([
            'user_id' => $pelanggan->id,
            'phone' => '081234567890',
            'address' => 'Jl. Pelanggan',
            'points_balance' => 0,
        ]);
    }
}
