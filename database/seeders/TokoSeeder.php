<?php

namespace Database\Seeders;

use App\Models\Toko;
use Illuminate\Database\Seeder;

class TokoSeeder extends Seeder
{
    /**
     * Seed data toko dummy untuk testing kunjungan.
     * Koordinat berada di area Jakarta dan sekitarnya.
     */
    public function run(): void
    {
        $tokos = [
            [
                'nama_toko'    => 'Toko Makmur Jaya',
                'alamat'       => 'Jl. Sudirman No. 45, Jakarta Pusat',
                'latitude'     => -6.20876400,
                'longitude'    => 106.84560000,
                'kode_barcode' => 'TOKO-A1B2C3',
                'radius_meter' => 100,
            ],
            [
                'nama_toko'    => 'Toko Sumber Rezeki',
                'alamat'       => 'Jl. Thamrin No. 12, Jakarta Pusat',
                'latitude'     => -6.19517500,
                'longitude'    => 106.82317100,
                'kode_barcode' => 'TOKO-D4E5F6',
                'radius_meter' => 150,
            ],
            [
                'nama_toko'    => 'Toko Berkah Abadi',
                'alamat'       => 'Jl. Gatot Subroto No. 78, Jakarta Selatan',
                'latitude'     => -6.23514200,
                'longitude'    => 106.82208400,
                'kode_barcode' => 'TOKO-G7H8I9',
                'radius_meter' => 100,
            ],
            [
                'nama_toko'    => 'Toko Sejahtera',
                'alamat'       => 'Jl. Kemang Raya No. 33, Jakarta Selatan',
                'latitude'     => -6.26080900,
                'longitude'    => 106.81320600,
                'kode_barcode' => 'TOKO-J1K2L3',
                'radius_meter' => 120,
            ],
            [
                'nama_toko'    => 'Toko Maju Bersama',
                'alamat'       => 'Jl. Kelapa Gading No. 56, Jakarta Utara',
                'latitude'     => -6.15750000,
                'longitude'    => 106.90830000,
                'kode_barcode' => 'TOKO-M4N5O6',
                'radius_meter' => 200,
            ],
            [
                'nama_toko'    => 'Toko Cahaya Mart',
                'alamat'       => 'Jl. Margonda Raya No. 88, Depok',
                'latitude'     => -6.37010500,
                'longitude'    => 106.83111000,
                'kode_barcode' => 'TOKO-P7Q8R9',
                'radius_meter' => 100,
            ],
            [
                'nama_toko'    => 'Toko Sentosa',
                'alamat'       => 'Jl. Raya Bogor Km 26, Jakarta Timur',
                'latitude'     => -6.33590900,
                'longitude'    => 106.86590000,
                'kode_barcode' => 'TOKO-S1T2U3',
                'radius_meter' => 150,
            ],
            [
                'nama_toko'    => 'Toko Prima',
                'alamat'       => 'Jl. Daan Mogot No. 100, Tangerang',
                'latitude'     => -6.16870000,
                'longitude'    => 106.63580000,
                'kode_barcode' => 'TOKO-V4W5X6',
                'radius_meter' => 100,
            ],
        ];

        foreach ($tokos as $toko) {
            Toko::updateOrCreate(
                ['kode_barcode' => $toko['kode_barcode']],
                $toko
            );
        }

        $this->command->info('✅ ' . count($tokos) . ' data toko berhasil di-seed!');
    }
}
