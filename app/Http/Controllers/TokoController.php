<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class TokoController extends Controller
{
    /**
     * Display a listing of all toko.
     */
    public function index()
    {
        $tokos = Toko::orderBy('nama_toko')->get();
        return view('toko.index', compact('tokos'));
    }

    /**
     * Show the form for creating a new toko.
     */
    public function create()
    {
        $kodeBarcode = Toko::generateKodeBarcode();
        return view('toko.create', compact('kodeBarcode'));
    }

    /**
     * Store a newly created toko in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_toko'    => 'required|string|max:100',
            'alamat'       => 'required|string',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'kode_barcode' => 'required|string|max:50|unique:toko,kode_barcode',
            'radius_meter' => 'required|integer|min:10|max:1000',
        ]);

        Toko::create($validated);

        return redirect()->route('toko.index')
            ->with('success', 'Data toko berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified toko.
     */
    public function edit(Toko $toko)
    {
        return view('toko.edit', compact('toko'));
    }

    /**
     * Update the specified toko in storage.
     */
    public function update(Request $request, Toko $toko)
    {
        $validated = $request->validate([
            'nama_toko'    => 'required|string|max:100',
            'alamat'       => 'required|string',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'kode_barcode' => 'required|string|max:50|unique:toko,kode_barcode,' . $toko->id,
            'radius_meter' => 'required|integer|min:10|max:1000',
        ]);

        $toko->update($validated);

        return redirect()->route('toko.index')
            ->with('success', 'Data toko berhasil diperbarui!');
    }

    /**
     * Remove the specified toko from storage.
     */
    public function destroy(Toko $toko)
    {
        $toko->delete();

        return redirect()->route('toko.index')
            ->with('success', 'Data toko berhasil dihapus!');
    }

    /**
     * Show the detail of a toko (JSON for API usage).
     */
    public function show(Toko $toko)
    {
        return response()->json([
            'status' => 'success',
            'data'   => $toko,
        ]);
    }

    /**
     * Generate QR Code data for a toko (AJAX endpoint for modal).
     * Returns JSON with SVG QR code and toko info.
     */
    public function qrcode(Toko $toko)
    {
        $qrcodeSvg = QrCode::format('svg')
            ->size(250)
            ->errorCorrection('H')
            ->margin(1)
            ->generate($toko->kode_barcode);

        return response()->json([
            'status'      => 'success',
            'nama_toko'   => $toko->nama_toko,
            'kode_barcode' => $toko->kode_barcode,
            'alamat'      => $toko->alamat,
            'qrcode_svg'  => $qrcodeSvg,
        ]);
    }

    /**
     * Download QR Code as PDF for printing.
     */
    public function downloadQrPdf(Toko $toko)
    {
        $qrcodeSvg = QrCode::format('svg')
            ->size(250)
            ->errorCorrection('H')
            ->margin(1)
            ->generate($toko->kode_barcode);

        $qrcodeBase64 = base64_encode($qrcodeSvg);

        $pdf = Pdf::loadView('toko.qrcode-pdf', [
            'toko'          => $toko,
            'qrcodeBase64'  => $qrcodeBase64,
        ]);

        $pdf->setPaper('A6', 'portrait');

        $filename = 'QRCode_' . str_replace(' ', '_', $toko->nama_toko) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * API: Find toko by kode_barcode string.
     * Used by the scanner on the Kunjungan page after scanning a QR code.
     */
    public function findByBarcode(string $kode_barcode)
    {
        $toko = Toko::where('kode_barcode', $kode_barcode)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan untuk barcode: ' . $kode_barcode,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'nama_toko'   => $toko->nama_toko,
                'barcode'     => $toko->kode_barcode,
                'alamat'      => $toko->alamat,
                'latitude'    => $toko->latitude,
                'longitude'   => $toko->longitude,
                'accuracy'    => $toko->accuracy ?? null,
                'radius'      => $toko->radius_meter,
            ],
        ]);
    }
}
