<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KunjunganController extends Controller
{
    /**
     * Halaman utama kunjungan toko — peta + scanner.
     */
    public function index()
    {
        $tokos = Toko::all();
        return view('kunjungan.index', compact('tokos'));
    }

    /**
     * API: Cari toko terdekat berdasarkan posisi GPS sales.
     */
    public function getTokoTerdekat(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $lat = $request->latitude;
        $lng = $request->longitude;

        $tokos = Toko::all()->map(function ($toko) use ($lat, $lng) {
            $jarak = $this->haversine($lat, $lng, $toko->latitude, $toko->longitude);
            $toko->jarak_meter = round($jarak, 2);
            return $toko;
        })->sortBy('jarak_meter')->values();

        return response()->json([
            'status' => 'success',
            'data'   => $tokos,
        ]);
    }

    /**
     * API: Proses verifikasi kunjungan — dual verification (GPS + Barcode).
     */
    public function verifikasi(Request $request)
    {
        $validated = $request->validate([
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'kode_barcode'  => 'required|string|max:50',
            'catatan'       => 'nullable|string|max:500',
        ]);

        // 1. Cari toko berdasarkan kode barcode
        $toko = Toko::where('kode_barcode', $validated['kode_barcode'])->first();

        if (!$toko) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Barcode tidak terdaftar! Pastikan Anda memindai barcode yang benar.',
                'data'    => null,
            ], 404);
        }

        // 2. Hitung jarak GPS (Haversine formula)
        $jarak = $this->haversine(
            $validated['latitude'],
            $validated['longitude'],
            $toko->latitude,
            $toko->longitude
        );

        // 3. Validasi GPS — apakah dalam radius
        $gpsValid     = $jarak <= $toko->radius_meter;
        $barcodeValid = true; // barcode cocok (toko ditemukan)

        // 4. Status akhir — kedua verifikasi harus valid
        $status = ($gpsValid && $barcodeValid) ? 'valid' : 'invalid';

        // 5. Simpan log kunjungan
        $kunjungan = Kunjungan::create([
            'user_id'          => Auth::id(),
            'toko_id'          => $toko->id,
            'latitude'         => $validated['latitude'],
            'longitude'        => $validated['longitude'],
            'jarak_meter'      => round($jarak, 2),
            'barcode_scanned'  => $validated['kode_barcode'],
            'gps_valid'        => $gpsValid,
            'barcode_valid'    => $barcodeValid,
            'status'           => $status,
            'catatan'          => $validated['catatan'] ?? null,
            'waktu_kunjungan'  => now(),
        ]);

        // 6. Kirim response
        if ($status === 'valid') {
            return response()->json([
                'status'  => 'success',
                'message' => 'Kunjungan berhasil diverifikasi! ✅',
                'data'    => [
                    'toko'        => $toko->nama_toko,
                    'jarak'       => round($jarak, 2) . ' meter',
                    'radius'      => $toko->radius_meter . ' meter',
                    'gps_valid'   => $gpsValid,
                    'barcode_valid' => $barcodeValid,
                    'waktu'       => $kunjungan->waktu_kunjungan->format('d/m/Y H:i:s'),
                ],
            ]);
        }

        // GPS di luar radius
        return response()->json([
            'status'  => 'warning',
            'message' => "Anda di luar radius toko \"{$toko->nama_toko}\"! Jarak Anda: " . round($jarak, 2) . " meter (radius: {$toko->radius_meter} meter).",
            'data'    => [
                'toko'        => $toko->nama_toko,
                'jarak'       => round($jarak, 2) . ' meter',
                'radius'      => $toko->radius_meter . ' meter',
                'gps_valid'   => $gpsValid,
                'barcode_valid' => $barcodeValid,
                'waktu'       => $kunjungan->waktu_kunjungan->format('d/m/Y H:i:s'),
            ],
        ], 200);
    }

    /**
     * Halaman riwayat kunjungan untuk sales (user yang login).
     */
    public function riwayat()
    {
        $kunjungans = Kunjungan::with('toko')
            ->where('user_id', Auth::id())
            ->orderByDesc('waktu_kunjungan')
            ->get();

        return view('kunjungan.riwayat', compact('kunjungans'));
    }

    /**
     * Halaman riwayat seluruh kunjungan (admin view).
     */
    public function riwayatAdmin()
    {
        $kunjungans = Kunjungan::with(['toko', 'user'])
            ->orderByDesc('waktu_kunjungan')
            ->get();

        return view('kunjungan.riwayat-admin', compact('kunjungans'));
    }

    /**
     * Haversine Formula — menghitung jarak antara 2 koordinat GPS dalam meter.
     *
     * @param float $lat1 Latitude titik 1
     * @param float $lon1 Longitude titik 1
     * @param float $lat2 Latitude titik 2
     * @param float $lon2 Longitude titik 2
     * @return float Jarak dalam meter
     */
    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R    = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c;
    }
}
