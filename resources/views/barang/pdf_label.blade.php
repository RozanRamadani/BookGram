<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
/* ─── Page setup ─────────────────────────────────────────────────────────── */
@page {
    size: 210mm 165mm;          /* Kertas Custom 108: 210 x 165 mm */
    margin: 1.5mm 4mm;          /* Margin auto-calculated agar terpusat */
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: Arial, Helvetica, sans-serif;
    width: 202mm;
}

/* ─── Label sheet (5 cols × 8 rows = 40 labels per page) ────────────────── */
/*
    TnJ No. 108
    Sheet  : 210mm × 165mm
    Label  : 38mm wide × 18mm tall
    H gap  : 3.5mm   (between columns)
    V gap  : 2mm   (between rows)
*/

.sheet {
    width: 204mm;
    page-break-after: always;
}
.sheet:last-child {
    page-break-after: auto;
}

table.label-table {
    border-collapse: separate;
    border-spacing: 3.5mm 2mm;
    width: 204mm;
    table-layout: fixed;
}

td.label-cell {
    width: 38mm;
    height: 18mm;
    border: 0.4pt solid #999999;
    vertical-align: middle;
    text-align: center;
    padding: 1mm;
    overflow: hidden;
}

td.label-empty {
    width: 38mm;
    height: 18mm;
    border: 0.4pt dashed #dddddd;
}

/* ─── Content inside label ───────────────────────────────────────────────── */
.lbl-id {
    font-size: 5pt;
    color: #666666;
    margin-bottom: 0;
    letter-spacing: 0.5pt;
}

.lbl-nama {
    font-size: 7pt;
    font-weight: bold;
    color: #1a1a1a;
    margin-bottom: 0.5mm;
    line-height: 1.1;
    word-break: break-word;
}

.lbl-harga {
    font-size: 9pt;
    font-weight: bold;
    color: #000000;
    line-height: 1;
}

.lbl-harga-sub {
    font-size: 5pt;
    color: #444444;
}

hr.lbl-divider {
    border: none;
    border-top: 0.5pt solid #aaaaaa;
    margin: 0.5mm 0;
}
</style>
</head>
<body>

@foreach($pages as $pageIndex => $page)
<div class="sheet">
    <table class="label-table">
        @for($row = 0; $row < 8; $row++)
        <tr>
            @for($col = 0; $col < 5; $col++)
                @php $slot = $row * 5 + $col; $item = $page[$slot]; @endphp
                @if($item)
                    <td class="label-cell">
                        @php
                            $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
                            $barcode = base64_encode($generator->getBarcode($item->id_barang, $generator::TYPE_CODE_128, 2, 30));
                        @endphp
                        <img src="data:image/png;base64,{{ $barcode }}" alt="barcode" style="max-width:35mm; height:5mm; margin-bottom:1mm;">
                        <div class="lbl-id">{{ $item->id_barang }}</div>
                        <hr class="lbl-divider">
                        <div class="lbl-nama">{{ $item->nama }}</div>
                        <hr class="lbl-divider">
                        <div class="lbl-harga-sub">Harga</div>
                        <div class="lbl-harga">Rp {{ number_format($item->harga, 0, ',', '.') }}</div>
                    </td>
                @else
                    <td class="label-empty"></td>
                @endif
            @endfor
        </tr>
        @endfor
    </table>
</div>
@endforeach

</body>
</html>
