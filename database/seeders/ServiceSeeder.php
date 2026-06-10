<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::updateOrCreate(
            ['name' => 'Cuci Reguler'],
            [
                'type' => 'kiloan',
                'price' => 7000,
                'estimated_hours' => 48,
                'is_active' => true,
            ]
        );

        Service::updateOrCreate(
            ['name' => 'Cuci Express'],
            [
                'type' => 'kiloan',
                'price' => 12000,
                'estimated_hours' => 24,
                'is_active' => true,
            ]
        );

        Service::updateOrCreate(
            ['name' => 'Setrika'],
            [
                'type' => 'kiloan',
                'price' => 5000,
                'estimated_hours' => 24,
                'is_active' => true,
            ]
        );

        Service::updateOrCreate(
            ['name' => 'Bed Cover'],
            [
                'type' => 'satuan',
                'price' => 25000,
                'estimated_hours' => 72,
                'is_active' => true,
            ]
        );
    }
}
