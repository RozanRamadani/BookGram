@extends('layouts.app')

@section('title', 'Riwayat Kunjungan (Admin)')

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-clipboard-check-multiple text-primary"></i> Riwayat Kunjungan — Semua Sales
                        </h4>
                        <p class="card-description mt-1">Data kunjungan seluruh sales ke toko</p>
                    </div>
                </div>

                {{-- Summary Cards --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-white">Total</h6>
                                        <h3 class="mb-0 text-white">{{ $kunjungans->count() }}</h3>
                                    </div>
                                    <i class="mdi mdi-map-marker-multiple" style="font-size: 2rem; opacity: 0.7;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-white">Valid</h6>
                                        <h3 class="mb-0 text-white">{{ $kunjungans->where('status', 'valid')->count() }}</h3>
                                    </div>
                                    <i class="mdi mdi-check-circle" style="font-size: 2rem; opacity: 0.7;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-white">Invalid</h6>
                                        <h3 class="mb-0 text-white">{{ $kunjungans->where('status', 'invalid')->count() }}</h3>
                                    </div>
                                    <i class="mdi mdi-close-circle" style="font-size: 2rem; opacity: 0.7;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-white">Hari Ini</h6>
                                        <h3 class="mb-0 text-white">{{ $kunjungans->where('waktu_kunjungan', '>=', today())->count() }}</h3>
                                    </div>
                                    <i class="mdi mdi-calendar-today" style="font-size: 2rem; opacity: 0.7;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="riwayatAdminTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Waktu</th>
                                <th>Sales</th>
                                <th>Toko</th>
                                <th>Jarak</th>
                                <th>GPS</th>
                                <th>Barcode</th>
                                <th>Status</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kunjungans as $index => $k)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <small>{{ $k->waktu_kunjungan->format('d/m/Y') }}</small><br>
                                        <small class="text-muted">{{ $k->waktu_kunjungan->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $k->user->name ?? '-' }}</strong><br>
                                        <small class="text-muted">{{ $k->user->email ?? '-' }}</small>
                                    </td>
                                    <td><strong>{{ $k->toko->nama_toko ?? '-' }}</strong></td>
                                    <td>
                                        <span class="{{ $k->gps_valid ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($k->jarak_meter, 1) }}m
                                        </span>
                                    </td>
                                    <td>
                                        @if($k->gps_valid)
                                            <span class="badge badge-success"><i class="mdi mdi-check"></i></span>
                                        @else
                                            <span class="badge badge-danger"><i class="mdi mdi-close"></i></span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($k->barcode_valid)
                                            <span class="badge badge-success"><i class="mdi mdi-check"></i></span>
                                        @else
                                            <span class="badge badge-danger"><i class="mdi mdi-close"></i></span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($k->status === 'valid')
                                            <span class="badge badge-success" style="font-size: 0.85em;">
                                                <i class="mdi mdi-check-decagram"></i> VALID
                                            </span>
                                        @else
                                            <span class="badge badge-danger" style="font-size: 0.85em;">
                                                <i class="mdi mdi-alert-circle"></i> INVALID
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($k->catatan, 30) ?? '-' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="mdi mdi-clipboard-text-off" style="font-size: 2rem;"></i>
                                        <p class="mt-2">Belum ada data kunjungan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
