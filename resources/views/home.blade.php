@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-6 col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title mb-0">Total Kategori</h6>
                    <i class="mdi mdi-format-list-bulleted icon-lg text-primary"></i>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h3 class="mb-2 mt-3">{{ $totalKategori }}</h3>
                        <p class="text-muted">Kategori Buku</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title mb-0">Total Buku</h6>
                    <i class="mdi mdi-book-open-variant icon-lg text-success"></i>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h3 class="mb-2 mt-3">{{ $totalBuku }}</h3>
                        <p class="text-muted">Koleksi Buku</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title mb-0">User</h6>
                    <i class="mdi mdi-account icon-lg text-warning"></i>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h3 class="mb-2 mt-3">{{ Auth::user()->name }}</h3>
                        <p class="text-muted">Logged in</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Generate PDF</h4>
                <p class="card-description">Download contoh PDF untuk Studi Kasus 2</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <a href="{{ route('pdf.certificate') }}" class="btn btn-primary" target="_blank">
                                <i class="mdi mdi-certificate"></i>
                                Download Sertifikat (Landscape A4)
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <a href="{{ route('pdf.invitation') }}" class="btn btn-success" target="_blank">
                                <i class="mdi mdi-email-outline"></i>
                                Download Undangan (Portrait A4)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Fitur Keamanan Login</h4>
                <p class="card-description">Status implementasi Studi Kasus 1</p>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Google OAuth Login
                        <span class="badge bg-success">✓ Aktif</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        OTP 2-Factor Authentication
                        <span class="badge bg-success">✓ Aktif</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Email OTP Verification
                        <span class="badge bg-success">✓ Aktif</span>
                    </li>
                    <li class="list-group-item">
                        <small class="text-muted">
                            <i class="mdi mdi-information"></i>
                            Setiap login akan memerlukan verifikasi OTP yang dikirim ke email Anda.
                            Anda juga dapat login menggunakan akun Google.
                        </small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Buku Terbaru</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Kategori</th>
                                <th>Ditambahkan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBukus as $buku)
                                <tr>
                                    <td><span class="badge badge-dark">{{ $buku->kode }}</span></td>
                                    <td>{{ $buku->judul }}</td>
                                    <td>{{ $buku->pengarang }}</td>
                                    <td><span class="badge badge-info">{{ $buku->kategori->nama }}</span></td>
                                    <td>{{ $buku->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data buku</td>
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
