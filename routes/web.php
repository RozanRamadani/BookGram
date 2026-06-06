<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\StudiKasusController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\PDFController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Redirect root ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes (Login, Register, Reset Password, dll)
// Menggunakan middleware 'guest' untuk mencegah user yang sudah login mengakses halaman ini
Auth::routes();

// Google OAuth Routes
Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// OTP Verification Routes
Route::get('auth/verify-otp', [OTPController::class, 'showVerifyForm'])->name('otp.verify');
Route::post('auth/verify-otp', [OTPController::class, 'verify'])->name('otp.verify.submit');

// PDF Generation Routes
Route::get('pdf/certificate', [PDFController::class, 'generateCertificate'])->name('pdf.certificate');
Route::get('pdf/invitation', [PDFController::class, 'generateInvitation'])->name('pdf.invitation');

Route::middleware(['auth'])->group(function () {
    // Dashboard / Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Studi Kasus JavaScript dan jQuery
    Route::view('/studi-kasus/table-html', 'studi-kasus.table-html')->name('studi-kasus.table-html');
    Route::view('/studi-kasus/table-datatables', 'studi-kasus.table-datatables')->name('studi-kasus.table-datatables');
    Route::view('/studi-kasus/select-kota', 'studi-kasus.select-kota')->name('studi-kasus.select-kota');
    Route::get('/studi-kasus/wilayah/jquery', [StudiKasusController::class, 'wilayahJquery'])->name('studi-kasus.wilayah-jquery');
    Route::get('/studi-kasus/wilayah/axios', [StudiKasusController::class, 'wilayahAxios'])->name('studi-kasus.wilayah-axios');
    Route::get('/studi-kasus/pos/jquery', [StudiKasusController::class, 'posJquery'])->name('studi-kasus.pos-jquery');
    Route::get('/studi-kasus/pos/axios', [StudiKasusController::class, 'posAxios'])->name('studi-kasus.pos-axios');

    // Endpoint Ajax/Axios untuk data wilayah
    Route::get('/studi-kasus/api/wilayah/provinsi', [StudiKasusController::class, 'getProvinsi'])->name('studi-kasus.api.provinsi');
    Route::get('/studi-kasus/api/wilayah/kota/{provinsiId}', [StudiKasusController::class, 'getKota'])->name('studi-kasus.api.kota');
    Route::get('/studi-kasus/api/wilayah/kecamatan/{kotaId}', [StudiKasusController::class, 'getKecamatan'])->name('studi-kasus.api.kecamatan');
    Route::get('/studi-kasus/api/wilayah/kelurahan/{kecamatanId}', [StudiKasusController::class, 'getKelurahan'])->name('studi-kasus.api.kelurahan');

    // Endpoint Ajax/Axios untuk POS
    Route::get('/studi-kasus/api/pos/barang/{kode}', [StudiKasusController::class, 'findBarang'])->name('studi-kasus.api.pos.find-barang');
    Route::post('/studi-kasus/api/pos/checkout', [StudiKasusController::class, 'checkout'])->name('studi-kasus.api.pos.checkout');

    // Kategori Management
    Route::resource('kategori', KategoriController::class);

    // Buku Management
    Route::resource('buku', BukuController::class);

    // Barang Management
    Route::get('barang/print-form', [BarangController::class, 'printForm'])->name('barang.print-form');
    Route::post('barang/print-pdf', [BarangController::class, 'printPdf'])->name('barang.print-pdf');
    Route::resource('barang', BarangController::class);

    // Scanner
    Route::get('/scanner', [\App\Http\Controllers\ScannerController::class, 'index'])->name('scanner.index');
    Route::get('/api/scanner/barang/{id}', [\App\Http\Controllers\ScannerController::class, 'getBarang'])->name('api.scanner.barang');

    // NFC Absensi
    Route::get('/absensi', [\App\Http\Controllers\AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/scan', [\App\Http\Controllers\AbsensiController::class, 'scan'])->name('absensi.scan');
    Route::resource('mahasiswa', \App\Http\Controllers\MahasiswaController::class);
    Route::post('/register-kartu', [\App\Http\Controllers\MahasiswaController::class, 'registerKartu'])->name('mahasiswa.register-kartu');

    // ════════════════════════════════════════════
    // Kunjungan Toko — Geolocation + Barcode
    // ════════════════════════════════════════════

    // Manajemen Toko (Admin CRUD)
    Route::resource('toko', TokoController::class);
    Route::get('toko/{toko}/barcode', [TokoController::class, 'printBarcode'])->name('toko.barcode');
    Route::get('toko/{toko}/qrcode', [TokoController::class, 'qrcode'])->name('toko.qrcode');
    Route::get('toko/{toko}/qrcode/download-pdf', [TokoController::class, 'downloadQrPdf'])->name('toko.qrcode.download-pdf');

    // API: Cari toko berdasarkan kode barcode (digunakan oleh scanner QR)
    Route::get('/api/toko/{kode_barcode}', [TokoController::class, 'findByBarcode'])->name('api.toko.find-by-barcode');

    // Kunjungan Toko (Sales)
    Route::get('/kunjungan', [KunjunganController::class, 'index'])->name('kunjungan.index');
    Route::post('/kunjungan/verifikasi', [KunjunganController::class, 'verifikasi'])->name('kunjungan.verifikasi');
    Route::get('/kunjungan/riwayat', [KunjunganController::class, 'riwayat'])->name('kunjungan.riwayat');
    Route::get('/kunjungan/riwayat-admin', [KunjunganController::class, 'riwayatAdmin'])->name('kunjungan.riwayat-admin');

    // API Endpoint — Cari toko terdekat berdasarkan GPS
    Route::get('/api/kunjungan/toko-terdekat', [KunjunganController::class, 'getTokoTerdekat'])->name('api.kunjungan.toko-terdekat');
});
