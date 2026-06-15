<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::create([
            'name' => 'Pelanggan',
            'username' => 'pelanggan',
            'email' => 'pelanggan@gmail.com',
            'password' => '123123123',
            'role' => 'pelanggan',
        ]);
    }
}
