@extends('layouts.app')

@section('title', 'Data Barang')

@push('plugin-styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Data Barang</h4>
                    <div>
                        <a href="{{ route('barang.print-form') }}" class="btn btn-success btn-sm me-1">
                            <i class="mdi mdi-printer"></i> Cetak Label
                        </a>
                        <a href="{{ route('barang.create') }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-plus"></i> Tambah Barang
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabelBarang" class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>ID Barang</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Waktu Input</th>
                                <th width="140" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barangs as $key => $barang)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><span class="badge bg-dark">{{ $barang->id_barang }}</span></td>
                                    <td>{{ $barang->nama }}</td>
                                    <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($barang->timestamp)->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('barang.edit', $barang->id_barang) }}" class="btn btn-warning btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('barang.destroy', $barang->id_barang) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#tabelBarang').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            order: [[0, 'asc']],
            responsive: true
        });
    });
</script>
@endpush
