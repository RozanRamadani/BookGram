@extends('layouts.app')

@section('title', 'Riwayat Kunjungan')

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-history text-primary"></i> Riwayat Kunjungan Saya
                        </h4>
                        <p class="card-description mt-1">
                            Log kunjungan toko oleh <strong>{{ Auth::user()->name }}</strong>
                        </p>
                    </div>
                    <a href="{{ route('kunjungan.index') }}" class="btn btn-primary btn-icon-text">
                        <i class="mdi mdi-map-marker-radius btn-icon-prepend"></i> Kunjungan Baru
                    </a>
                </div>

                {{-- Summary Cards --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-white">Total Kunjungan</h6>
                                        <h3 class="mb-0 text-white">{{ $kunjungans->count() }}</h3>
                                    </div>
                                    <i class="mdi mdi-map-marker-multiple" style="font-size: 2.5rem; opacity: 0.7;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-white">Valid</h6>
                                        <h3 class="mb-0 text-white">{{ $kunjungans->where('status', 'valid')->count() }}</h3>
                                    </div>
                                    <i class="mdi mdi-check-circle" style="font-size: 2.5rem; opacity: 0.7;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-white">Invalid</h6>
                                        <h3 class="mb-0 text-white">{{ $kunjungans->where('status', 'invalid')->count() }}</h3>
                                    </div>
                                    <i class="mdi mdi-close-circle" style="font-size: 2.5rem; opacity: 0.7;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="riwayatTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Waktu</th>
                                <th>Toko</th>
                                <th>Jarak (m)</th>
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
                                    <td><strong>{{ $k->toko->nama_toko ?? '-' }}</strong></td>
                                    <td>
                                        <span class="{{ $k->gps_valid ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($k->jarak_meter, 1) }}m
                                        </span>
                                    </td>
                                    <td>
                                        @if($k->gps_valid)
                                            <span class="badge badge-success"><i class="mdi mdi-check"></i> Valid</span>
                                        @else
                                            <span class="badge badge-danger"><i class="mdi mdi-close"></i> Invalid</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($k->barcode_valid)
                                            <span class="badge badge-success"><i class="mdi mdi-check"></i> Valid</span>
                                        @else
                                            <span class="badge badge-danger"><i class="mdi mdi-close"></i> Invalid</span>
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
                                        <small class="text-muted">{{ $k->catatan ?? '-' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="mdi mdi-clipboard-text-off" style="font-size: 2rem;"></i>
                                        <p class="mt-2">Belum ada riwayat kunjungan.</p>
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
