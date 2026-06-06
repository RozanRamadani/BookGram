@extends('layouts.app')

@section('title', 'Cetak Tag Harga')

@push('plugin-styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .label-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 4px;
        max-width: 340px;
        margin: 0 auto;
    }
    .label-cell {
        border: 2px solid #dee2e6;
        border-radius: 4px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        color: #6c757d;
        cursor: default;
        transition: background .15s;
    }
    .label-cell.selected  { background: #0d6efd; color: #fff; border-color: #0d6efd; }
    .label-cell.filled    { background: #198754; color: #fff; border-color: #198754; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Cetak Tag Harga — Kertas TnJ No. 108</h4>
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left"></i> Kembali
                    </a>
                </div>

                <form id="printForm" action="{{ route('barang.print-pdf') }}" method="POST" target="_blank">
                    @csrf

                    {{-- Starting position --}}
                    <div class="row mb-4 align-items-start">
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title">Posisi Awal Cetak</h6>
                                    <p class="text-muted small mb-3">
                                        Kertas TnJ 108 memiliki <strong>5 kolom × 8 baris = 40 label</strong>.
                                        Tentukan posisi label pertama (X = kolom, Y = baris).
                                    </p>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <label class="form-label fw-semibold">X (Kolom 1-5)</label>
                                            <input type="number" id="start_x" name="start_x"
                                                   class="form-control" min="1" max="5" value="1" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-semibold">Y (Baris 1-8)</label>
                                            <input type="number" id="start_y" name="start_y"
                                                   class="form-control" min="1" max="8" value="1" required>
                                        </div>
                                    </div>
                                    {{-- Preview grid --}}
                                    <p class="small text-muted mb-1">Preview posisi (<span class="text-primary">biru</span> = awal, <span class="text-success">hijau</span> = terisi):</p>
                                    <div class="label-grid" id="gridPreview">
                                        @for($r = 1; $r <= 8; $r++)
                                            @for($c = 1; $c <= 5; $c++)
                                                <div class="label-cell" data-row="{{ $r }}" data-col="{{ $c }}">
                                                    {{ ($r-1)*5 + $c }}
                                                </div>
                                            @endfor
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="card-title mb-0">Pilih Barang yang Dicetak</h6>
                                        <div>
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="checkAll">Pilih Semua</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm ms-1" id="uncheckAll">Hapus Pilihan</button>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-2">
                                        Dipilih: <strong id="selectedCount">0</strong> barang.
                                        Slot tersedia: <strong id="availableSlots">0</strong>
                                    </p>

                                    <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
                                        <table id="tabelPilih" class="table table-sm table-hover">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th width="40"><input type="checkbox" id="masterCheck"></th>
                                                    <th>ID Barang</th>
                                                    <th>Nama</th>
                                                    <th>Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($barangs as $barang)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="ids[]"
                                                               value="{{ $barang->id_barang }}"
                                                               class="item-check">
                                                    </td>
                                                    <td><span class="badge bg-dark">{{ $barang->id_barang }}</span></td>
                                                    <td>{{ $barang->nama }}</td>
                                                    <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success" id="btnCetak" disabled>
                            <i class="mdi mdi-file-pdf-box"></i> Generate PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#tabelPilih').DataTable({ paging: false, info: false, searching: true });

    function updateGrid() {
        var sx = parseInt($('#start_x').val()) || 1;
        var sy = parseInt($('#start_y').val()) || 1;
        var startSlot = (sy - 1) * 5 + (sx - 1); // 0-based
        var count = $('.item-check:checked').length;
        var available = 40 - startSlot;

        $('#selectedCount').text(count);
        $('#availableSlots').text(available);
        $('#btnCetak').prop('disabled', count === 0);

        // Redraw grid
        $('#gridPreview .label-cell').removeClass('selected filled');
        for (var i = startSlot; i < 40; i++) {
            var offset = i - startSlot;
            var cell = $('#gridPreview .label-cell').eq(i);
            if (offset === 0) {
                cell.addClass('selected');
            } else if (offset < count) {
                cell.addClass('filled');
            }
        }
    }

    $('#start_x, #start_y').on('input', updateGrid);
    $(document).on('change', '.item-check', function () {
        var allChecked = $('.item-check').length === $('.item-check:checked').length;
        $('#masterCheck').prop('checked', allChecked);
        updateGrid();
    });

    $('#masterCheck, #checkAll').on('click', function () {
        $('.item-check').prop('checked', true);
        $('#masterCheck').prop('checked', true);
        updateGrid();
    });

    $('#uncheckAll').on('click', function () {
        $('.item-check').prop('checked', false);
        $('#masterCheck').prop('checked', false);
        updateGrid();
    });

    updateGrid();
});
</script>
@endpush
