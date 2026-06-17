<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'point_value_rupiah',
                'label' => 'Nilai Rupiah per 1 Poin',
                'value' => '100',
                'unit' => 'rupiah',
                'description' => 'Nilai potongan harga untuk setiap 1 poin yang ditukarkan pelanggan.',
            ],
            [
                'key' => 'point_earn_nominal',
                'label' => 'Nominal Transaksi per 1 Poin',
                'value' => '10000',
                'unit' => 'rupiah',
                'description' => 'Pelanggan mendapat 1 poin setiap mencapai nominal transaksi ini.',
            ],
            [
                'key' => 'pickup_fee',
                'label' => 'Tarif Jemput Cucian',
                'value' => '0',
                'unit' => 'rupiah',
                'description' => 'Biaya penjemputan cucian dari alamat pelanggan.',
            ],
            [
                'key' => 'delivery_fee_per_km',
                'label' => 'Tarif Antar per Kilometer',
                'value' => '2000',
                'unit' => 'rupiah/km',
                'description' => 'Biaya pengantaran per kilometer jika jarak melebihi batas gratis.',
            ],
            [
                'key' => 'free_delivery_distance_km',
                'label' => 'Batas Jarak Gratis Antar',
                'value' => '3',
                'unit' => 'km',
                'description' => 'Jarak maksimal pengantaran gratis.',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
