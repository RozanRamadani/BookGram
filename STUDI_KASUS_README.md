# Purple Laravel - Dokumentasi Studi Kasus

## Daftar Isi
1. [Studi Kasus 1: Google OAuth & OTP Authentication](#studi-kasus-1)
2. [Studi Kasus 2: PDF Generator](#studi-kasus-2)
3. [Setup & Konfigurasi](#setup-konfigurasi)
4. [Testing](#testing)

---

## Studi Kasus 1: Google OAuth & OTP Authentication

### Fitur yang Diimplementasikan

#### A. Database Schema
✅ Menambahkan kolom `id_google` (varchar 256) pada tabel users  
✅ Menambahkan kolom `otp` (varchar 6) pada tabel users  
✅ Kolom `email` sudah ada di tabel users

**Migration File:** `database/migrations/2026_02_19_054200_add_google_id_and_otp_to_users_table.php`

#### B. Google OAuth Login
✅ Implementasi login menggunakan Google Account  
✅ Auto-create user baru dari Google account  
✅ Menyimpan `id_google` untuk user yang login via Google  

**Files:**
- Controller: `app/Http/Controllers/Auth/GoogleAuthController.php`
- Config: `config/services.php` (Google credentials)
- Routes: `auth/google` dan `auth/google/callback`

#### C. OTP 2-Factor Authentication
✅ Generate random 6-digit OTP setelah login berhasil  
✅ Menyimpan OTP ke database  
✅ Mengirim OTP ke email user  
✅ Halaman verifikasi OTP dengan input khusus 6 karakter  
✅ Verifikasi OTP sebelum membuat sesi login  

**Files:**
- Controller: `app/Http/Controllers/Auth/OTPController.php`
- View: `resources/views/auth/verify-otp.blade.php`
- Modified: `app/Http/Controllers/Auth/LoginController.php`

### Alur Login yang Diimplementasikan

#### 1. Login Regular (Email/Password):
```
User input email & password 
→ Authentikasi berhasil 
→ User logout sementara 
→ Generate OTP (6 digit)
→ Simpan OTP ke database
→ Kirim OTP ke email
→ Redirect ke halaman verifikasi OTP
→ User input OTP
→ Verifikasi OTP
→ Jika valid: Login berhasil, redirect ke /home
→ Jika invalid: Error message
```

#### 2. Login Google OAuth:
```
User klik "Login dengan Google"
→ Redirect ke Google OAuth
→ User pilih akun Google
→ Google callback ke aplikasi
→ Cek apakah user sudah ada (by email atau id_google)
→ Jika belum ada: Create user baru
→ Generate OTP (6 digit)
→ Simpan OTP ke database
→ Kirim OTP ke email
→ Redirect ke halaman verifikasi OTP
→ User input OTP
→ Verifikasi OTP
→ Jika valid: Login berhasil, redirect ke /home
→ Jika invalid: Error message
```

---

## Studi Kasus 2: PDF Generator

### Fitur yang Diimplementasikan

#### A. Sertifikat PDF (Landscape A4)
✅ Format Landscape  
✅ Ukuran kertas A4  
✅ Design mirip sertifikat dengan border dekoratif  
✅ Area untuk nama penerima  
✅ Informasi event/kegiatan  
✅ Area tanda tangan  

**Files:**
- Controller: `app/Http/Controllers/PDFController.php` (method: `generateCertificate`)
- View: `resources/views/pdf/certificate.blade.php`
- Route: `/pdf/certificate`

#### B. Undangan PDF (Portrait A4 dengan Header)
✅ Format Portrait  
✅ Ukuran kertas A4  
✅ Header resmi (Logo, Nama Institusi, Alamat)  
✅ Nomor surat, lampiran, perihal  
✅ Daftar penerima undangan  
✅ Isi undangan  
✅ Detail acara (waktu, tempat, agenda)  
✅ Area tanda tangan  

**Files:**
- Controller: `app/Http/Controllers/PDFController.php` (method: `generateInvitation`)
- View: `resources/views/pdf/invitation.blade.php`
- Route: `/pdf/invitation`

---

## Setup & Konfigurasi

### 1. Install Dependencies
Sudah terinstall:
- Laravel Socialite (untuk Google OAuth)
- Laravel DomPDF (untuk generate PDF)

### 2. Konfigurasi Google OAuth

#### A. Membuat Google OAuth Credentials
1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru atau pilih project yang ada
3. Enable "Google+ API"
4. Buat OAuth 2.0 Credentials:
   - Application type: Web application
   - Authorized redirect URIs: `http://localhost/auth/google/callback` (atau sesuai APP_URL)
5. Copy Client ID dan Client Secret

#### B. Update File .env
```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

### 3. Konfigurasi Email (untuk OTP)

Update `.env` untuk email settings. Contoh menggunakan Gmail:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Catatan:** Untuk Gmail, gunakan "App Password", bukan password biasa:
1. Aktifkan 2-Step Verification di akun Google
2. Buat App Password di https://myaccount.google.com/apppasswords

### 4. Run Migration
```bash
php artisan migrate
```

### 5. Clear Cache (Optional)
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## Testing

### 1. Test Google OAuth Login
1. Buka halaman login: `http://localhost/login`
2. Klik tombol "Login dengan Google"
3. Pilih akun Google Anda
4. Setelah redirect, cek apakah OTP dikirim ke email
5. Input kode OTP di halaman verifikasi
6. Setelah verifikasi berhasil, harus redirect ke `/home`

### 2. Test Regular Login dengan OTP
1. Buka halaman login
2. Input email dan password user yang sudah terdaftar
3. Setelah authentikasi berhasil, cek email untuk kode OTP
4. Input kode OTP di halaman verifikasi
5. Setelah verifikasi berhasil, harus redirect ke `/home`

### 3. Test PDF Certificate
1. Login ke aplikasi
2. Di dashboard, klik "Download Sertifikat (Landscape A4)"
3. Atau akses langsung: `http://localhost/pdf/certificate`
4. PDF harus terdownload dengan format landscape

### 4. Test PDF Invitation
1. Login ke aplikasi
2. Di dashboard, klik "Download Undangan (Portrait A4)"
3. Atau akses langsung: `http://localhost/pdf/invitation`
4. PDF harus terdownload dengan format portrait dan header

---

## Routes

### Authentication Routes
- `GET /login` - Halaman login
- `POST /login` - Process login
- `GET /register` - Halaman register
- `POST /register` - Process register
- `GET /auth/google` - Redirect ke Google OAuth
- `GET /auth/google/callback` - Google OAuth callback
- `GET /auth/verify-otp` - Halaman verifikasi OTP
- `POST /auth/verify-otp` - Process verifikasi OTP

### PDF Routes
- `GET /pdf/certificate` - Generate & download sertifikat (landscape)
- `GET /pdf/invitation` - Generate & download undangan (portrait)

### Protected Routes (Auth Required)
- `GET /home` - Dashboard
- `RESOURCE /kategori` - Kategori CRUD
- `RESOURCE /buku` - Buku CRUD

---

## Troubleshooting

### OTP Tidak Terkirim
1. Cek konfigurasi email di `.env`
2. Cek email spam/junk folder
3. Test email configuration: `php artisan tinker` → `Mail::raw('Test', function($m) { $m->to('your@email.com')->subject('Test'); });`

### Google OAuth Error
1. Pastikan Google Client ID dan Secret sudah benar
2. Pastikan redirect URI di Google Console sama dengan yang di `.env`
3. Pastikan Google+ API sudah di-enable

### PDF Tidak Generate
1. Pastikan DomPDF sudah terinstall
2. Check permission folder `storage/`
3. Check log di `storage/logs/laravel.log`

---

## File Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── Auth/
│       │   ├── GoogleAuthController.php    # Google OAuth handler
│       │   ├── LoginController.php         # Modified dengan OTP
│       │   └── OTPController.php           # OTP verification
│       └── PDFController.php               # PDF generator
└── Models/
    └── User.php                            # Updated fillable fields

config/
└── services.php                            # Google OAuth config

database/
└── migrations/
    └── 2026_02_19_054200_add_google_id_and_otp_to_users_table.php

resources/
└── views/
    ├── auth/
    │   ├── login.blade.php                 # Added Google login button
    │   └── verify-otp.blade.php            # OTP verification page
    ├── pdf/
    │   ├── certificate.blade.php           # Sertifikat template
    │   └── invitation.blade.php            # Undangan template
    └── home.blade.php                      # Added PDF links & status

routes/
└── web.php                                 # All routes defined here
```

---

## Catatan Penting

1. **Security:** Password user yang login via Google akan di-generate random dan di-hash
2. **OTP Expiry:** Saat ini OTP tidak memiliki expiry time. Untuk production, sebaiknya tambahkan field `otp_expires_at`
3. **Rate Limiting:** Pertimbangkan untuk menambahkan rate limiting pada OTP verification untuk mencegah brute force
4. **Session Management:** OTP verification menggunakan session untuk menyimpan user_id sementara
5. **PDF Customization:** Template PDF dapat dikustomisasi di `/resources/views/pdf/`

---

## Credits

- Laravel Framework
- Laravel Socialite (Google OAuth)
- Laravel DomPDF (PDF Generation)

---

**Developed for Study Case Assignment**
**Date:** February 19, 2026
