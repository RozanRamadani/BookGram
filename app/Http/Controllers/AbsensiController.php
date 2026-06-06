<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index()
    {
        return view('absensi.scanner');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'serial' => 'required'
        ]);

        $mahasiswa = Mahasiswa::where('serial_nfc', $request->serial)->first();

        if ($mahasiswa) {
            $today = Carbon::now()->toDateString();

            $sudahAbsen = Absensi::where('mahasiswa_id', $mahasiswa->id)
                                 ->where('tanggal', $today)
                                 ->exists();

            if (!$sudahAbsen) {
                Absensi::create([
                    'mahasiswa_id' => $mahasiswa->id,
                    'tanggal' => $today,
                    'jam' => Carbon::now()->toTimeString(),
                    'status' => 'Hadir'
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Absensi berhasil',
                    'data' => [
                        'nama' => $mahasiswa->nama,
                        'nim' => $mahasiswa->nim,
                        'serial' => $mahasiswa->serial_nfc
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sudah absen hari ini',
                    'data' => [
                        'nama' => $mahasiswa->nama,
                        'nim' => $mahasiswa->nim,
                        'serial' => $mahasiswa->serial_nfc
                    ]
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Kartu tidak terdaftar'
            ], 404);
        }
    }
}
