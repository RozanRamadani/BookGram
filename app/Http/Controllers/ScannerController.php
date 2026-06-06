<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index()
    {
        return view('scanner.index');
    }

    public function getBarang($id)
    {
        // Find by id_barang
        $barang = Barang::where('id_barang', $id)->first();

        if (!$barang) {
            return response()->json([
                'status' => 'error',
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id_barang' => $barang->id_barang,
                'nama' => $barang->nama,
                'harga' => $barang->harga,
            ]
        ]);
    }
}
