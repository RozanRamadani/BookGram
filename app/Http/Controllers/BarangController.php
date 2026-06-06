<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─── CRUD ────────────────────────────────────────────────────────────────

    public function index()
    {
        $barangs = Barang::orderBy('timestamp', 'desc')->get();
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        // Use raw insert so the trigger sets id_barang automatically.
        DB::statement(
            'INSERT INTO barang (id_barang, nama, harga, timestamp) VALUES (?, ?, ?, NOW())',
            ['', $validated['nama'], $validated['harga']]
        );

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($validated);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diupdate!');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }

    // ─── PDF Tag Harga ───────────────────────────────────────────────────────

    /**
     * Show the print selection form.
     */
    public function printForm()
    {
        $barangs = Barang::orderBy('timestamp', 'desc')->get();
        return view('barang.print_form', compact('barangs'));
    }

    /**
     * Generate TnJ no. 108 label PDF.
     *
     * Sheet layout: 5 columns × 8 rows = 40 labels per sheet.
     * User supplies:
     *   - ids[]      : selected barang ids
     *   - start_x    : starting column (1-5)
     *   - start_y    : starting row    (1-8)
     */
    public function printPdf(Request $request)
    {
        $request->validate([
            'ids'     => 'required|array|min:1',
            'ids.*'   => 'exists:barang,id_barang',
            'start_x' => 'required|integer|min:1|max:5',
            'start_y' => 'required|integer|min:1|max:8',
        ]);

        $selected = Barang::whereIn('id_barang', $request->ids)->get();

        // Build the 5×8 grid (40 slots per page).
        // start_x and start_y are 1-based.
        $startCol  = (int) $request->start_x;
        $startRow  = (int) $request->start_y;
        $startSlot = ($startRow - 1) * 5 + ($startCol - 1); // 0-based index in the sheet

        // Each "page" has 40 slots.  We offset the first page.
        $labels = $selected->values(); // re-index
        $count  = $labels->count();

        // Build pages array: each page is an array of 40 cells (null = empty)
        $pages = [];
        $labelIndex = 0;
        $pageOffset = $startSlot; // empty slots before the first label

        while ($labelIndex < $count) {
            $page = array_fill(0, 40, null);
            $slot = $pageOffset;

            while ($slot < 40 && $labelIndex < $count) {
                $page[$slot] = $labels[$labelIndex];
                $slot++;
                $labelIndex++;
            }

            $pages[] = $page;
            $pageOffset = 0; // subsequent pages start at slot 0
        }

        $pdf = Pdf::loadView('barang.pdf_label', compact('pages'))
            ->setOptions([
                'dpi'           => 150,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
            ]);

        return $pdf->stream('tag_harga.pdf');
    }
}
