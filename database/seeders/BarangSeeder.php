<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nama' => 'Buku Tulis A5 48 Hlm', 'harga' => 5000],
            ['nama' => 'Pensil 2B Faber', 'harga' => 3500],
            ['nama' => 'Pulpen Hitam Pilot', 'harga' => 7500],
            ['nama' => 'Penggaris 30cm', 'harga' => 6000],
            ['nama' => 'Spidol Boardmarker', 'harga' => 12000],
            ['nama' => 'Stapler Kecil', 'harga' => 25000],
            ['nama' => 'Isi Stapler No.10', 'harga' => 4000],
            ['nama' => 'Kertas HVS A4 80gr', 'harga' => 55000],
            ['nama' => 'Map Plastik Bening', 'harga' => 3000],
            ['nama' => 'Gunting Kecil', 'harga' => 9500],
            ['nama' => 'Lem Kertas UHU', 'harga' => 8000],
            ['nama' => 'Tip-Ex Koreksi', 'harga' => 5500],
        ];

        foreach ($items as $item) {
            DB::statement(
                'INSERT INTO barang (id_barang, nama, harga, timestamp) VALUES (?, ?, ?, NOW())',
                ['', $item['nama'], $item['harga']]
            );
        }
    }
}
