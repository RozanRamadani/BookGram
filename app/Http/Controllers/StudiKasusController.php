<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudiKasusController extends Controller
{
    public function wilayahJquery()
    {
        return view('studi-kasus.wilayah-jquery');
    }

    public function wilayahAxios()
    {
        return view('studi-kasus.wilayah-axios');
    }

    public function posJquery()
    {
        return view('studi-kasus.pos-jquery');
    }

    public function posAxios()
    {
        return view('studi-kasus.pos-axios');
    }

    public function getProvinsi()
    {
        $rows = $this->readWilayahCsv('provinces.csv');
        $data = array_map(function (array $row) {
            return [
                'id' => $row['id'] ?? '',
                'nama' => $row['name'] ?? '',
            ];
        }, $rows);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Data provinsi berhasil diambil',
            'data' => $data,
        ]);
    }

    public function getKota(string $provinsiId)
    {
        $rows = $this->readWilayahCsv('regencies.csv');
        $data = [];

        foreach ($rows as $row) {
            if (($row['province_id'] ?? null) !== $provinsiId) {
                continue;
            }

            $data[] = [
                'id' => $row['id'] ?? '',
                'nama' => $row['name'] ?? '',
            ];
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Data kota berhasil diambil',
            'data' => $data,
        ]);
    }

    public function getKecamatan(string $kotaId)
    {
        $rows = $this->readWilayahCsv('districts.csv');
        $data = [];

        foreach ($rows as $row) {
            if (($row['regency_id'] ?? null) !== $kotaId) {
                continue;
            }

            $data[] = [
                'id' => $row['id'] ?? '',
                'nama' => $row['name'] ?? '',
            ];
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Data kecamatan berhasil diambil',
            'data' => $data,
        ]);
    }

    public function getKelurahan(string $kecamatanId)
    {
        $rows = $this->readWilayahCsv('villages.csv');
        $data = [];

        foreach ($rows as $row) {
            if (($row['district_id'] ?? null) !== $kecamatanId) {
                continue;
            }

            $data[] = [
                'id' => $row['id'] ?? '',
                'nama' => $row['name'] ?? '',
            ];
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Data kelurahan berhasil diambil',
            'data' => $data,
        ]);
    }

    private function readWilayahCsv(string $fileName): array
    {
        static $cache = [];

        if (isset($cache[$fileName])) {
            return $cache[$fileName];
        }

        $path = storage_path('app/wilayah/' . $fileName);
        if (!is_file($path)) {
            return [];
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return [];
        }

        $header = fgetcsv($handle, 0, ';');
        if ($header === false) {
            fclose($handle);
            return [];
        }

        $rows = [];

        while (($line = fgetcsv($handle, 0, ';')) !== false) {
            if ($line === [null] || count($line) === 0) {
                continue;
            }

            if (count($line) < count($header)) {
                $line = array_pad($line, count($header), null);
            }

            $rows[] = array_combine($header, array_slice($line, 0, count($header)));
        }

        fclose($handle);
        $cache[$fileName] = $rows;

        return $rows;
    }

    public function findBarang(string $kode)
    {
        $barang = Barang::find($kode);

        if (!$barang) {
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Barang tidak ditemukan',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Barang ditemukan',
            'data' => [
                'id_barang' => $barang->id_barang,
                'nama' => $barang->nama,
                'harga' => (int) $barang->harga,
            ],
        ]);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.kode' => 'required|string|exists:barang,id_barang',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        $items = $validated['items'];
        $codes = collect($items)->pluck('kode')->values();

        $barangMap = Barang::whereIn('id_barang', $codes)->get()->keyBy('id_barang');

        $detailRows = [];
        $grandTotal = 0;

        foreach ($items as $item) {
            $barang = $barangMap->get($item['kode']);

            if (!$barang) {
                return response()->json([
                    'status' => 'error',
                    'code' => 422,
                    'message' => 'Ada barang yang tidak valid',
                    'data' => null,
                ], 422);
            }

            $jumlah = (int) $item['jumlah'];
            $harga = (int) $barang->harga;
            $subtotal = $harga * $jumlah;

            $detailRows[] = [
                'id_barang' => $barang->id_barang,
                'jumlah' => $jumlah,
                'subtotal' => $subtotal,
            ];

            $grandTotal += $subtotal;
        }

        DB::transaction(function () use ($detailRows, $grandTotal) {
            $penjualan = Penjualan::create([
                'total' => $grandTotal,
            ]);

            foreach ($detailRows as $detail) {
                PenjualanDetail::create([
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_barang' => $detail['id_barang'],
                    'jumlah' => $detail['jumlah'],
                    'subtotal' => $detail['subtotal'],
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Transaksi berhasil disimpan',
            'data' => [
                'total' => $grandTotal,
            ],
        ]);
    }
}
