# Quick Setup Instructions

## ⚠️ PENTING: Langkah-langkah Setup

Ikuti langkah-langkah berikut untuk menjalankan fitur Studi Kasus:

### 1. Setup Google OAuth Credentials

#### Cara mendapatkan Google Client ID dan Secret:

1. Buka https://console.cloud.google.com/
2. Login dengan akun Google Anda
3. Buat project baru:
   - Klik "Select a project" > "New Project"
   - Nama project: "Purple Laravel" (atau nama lain)
   - Klik "Create"

4. Enable Google+ API:
   - Di menu kiri, pilih "APIs & Services" > "Library"
   - Cari "Google+ API"
   - Klik dan pilih "Enable"

5. Buat OAuth Credentials:
   - Di menu kiri, pilih "APIs & Services" > "Credentials"
   - Klik "Create Credentials" > "OAuth client ID"
   - Jika diminta, konfigurasi OAuth consent screen:
     - User Type: External
     - App name: Purple Laravel
     - User support email: email Anda
     - Developer contact: email Anda
     - Klik Save and Continue

   - Application type: "Web application"
   - Name: "Purple Laravel Web"
   - Authorized redirect URIs: 
     ```
     http://localhost/auth/google/callback
     http://127.0.0.1:8000/auth/google/callback
     ```
   - Klik "Create"

6. Copy credentials yang muncul:
   - Client ID: Akan seperti `xxxxx.apps.googleusercontent.com`
   - Client Secret: String random

### 2. Update File .env

Buka file `.env` di root folder project, kemudian tambahkan/update:

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your_client_id_here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# Email Configuration (untuk OTP)
# Gunakan salah satu metode berikut:

# METODE 1: Gmail (Recommended untuk testing)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# METODE 2: Mailtrap (untuk development/testing tanpa kirim email real)
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.mailtrap.io
# MAIL_PORT=2525
# MAIL_USERNAME=your_mailtrap_username
# MAIL_PASSWORD=your_mailtrap_password
# MAIL_ENCRYPTION=tls
# MAIL_FROM_ADDRESS=noreply@purplelaravel.com
# MAIL_FROM_NAME="${APP_NAME}"

# METODE 3: Log (email akan tersimpan di log file, tidak dikirim)
# MAIL_MAILER=log
# MAIL_FROM_ADDRESS=noreply@purplelaravel.com
# MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Setup Gmail App Password (jika menggunakan Gmail)

Gmail tidak mengizinkan login dengan password biasa untuk aplikasi. Anda perlu membuat "App Password":

1. Buka https://myaccount.google.com/
2. Pilih "Security" di menu kiri
3. Aktifkan "2-Step Verification" jika belum aktif
4. Setelah 2-Step Verification aktif, scroll ke bawah dan cari "App passwords"
5. Klik "App passwords"
6. Select app: "Mail"
7. Select device: "Other (Custom name)" → ketik "Purple Laravel"
8. Klik "Generate"
9. Copy password yang muncul (16 karakter tanpa spasi)
10. Paste ke `MAIL_PASSWORD` di file `.env`

### 4. Clear Cache Laravel

Jalankan command berikut di terminal:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 5. Test Aplikasi

#### Test 1: Login Regular dengan OTP
```bash
# 1. Buka browser ke halaman login
http://localhost/login

# 2. Login dengan user yang sudah ada atau register dulu
# 3. Setelah login, akan redirect ke halaman OTP
# 4. Cek email untuk kode OTP (6 digit)
# 5. Masukkan kode OTP
# 6. Jika berhasil, akan redirect ke dashboard (/home)
```

#### Test 2: Login dengan Google + OTP
```bash
# 1. Buka browser ke halaman login
http://localhost/login

# 2. Klik tombol "Login dengan Google"
# 3. Pilih akun Google Anda
# 4. Setelah otorisasi, akan redirect ke halaman OTP
# 5. Cek email untuk kode OTP (6 digit)
# 6. Masukkan kode OTP
# 7. Jika berhasil, akan redirect ke dashboard
```

#### Test 3: Generate PDF Sertifikat
```bash
# Buka di browser:
http://localhost/pdf/certificate

# Atau klik tombol di dashboard setelah login
```

#### Test 4: Generate PDF Undangan
```bash
# Buka di browser:
http://localhost/pdf/invitation

# Atau klik tombol di dashboard setelah login
```

### 6. Troubleshooting

#### Problem: OTP tidak terkirim ke email

**Solusi:**
1. Cek konfigurasi MAIL di `.env`
2. Cek spam/junk folder email Anda
3. Coba test kirim email manual:
   ```bash
   php artisan tinker
   ```
   Kemudian ketik:
   ```php
   Mail::raw('Test email from Laravel', function($message) {
       $message->to('your@email.com')
               ->subject('Test Email');
   });
   ```
4. Jika pakai Gmail, pastikan sudah menggunakan App Password, bukan password biasa
5. Alternative: Gunakan `MAIL_MAILER=log` untuk testing, email akan tersimpan di `storage/logs/laravel.log`

#### Problem: Google OAuth Error "redirect_uri_mismatch"

**Solusi:**
1. Pastikan URL di Google Console sama persis dengan `GOOGLE_REDIRECT_URI` di `.env`
2. Jika menggunakan `php artisan serve`, URL-nya adalah `http://127.0.0.1:8000`
3. Tambahkan semua kemungkinan URL di Google Console:
   - `http://localhost/auth/google/callback`
   - `http://127.0.0.1:8000/auth/google/callback`
   - `http://localhost:8000/auth/google/callback`

#### Problem: PDF tidak generate

**Solusi:**
1. Pastikan folder `storage/` memiliki permission write
2. Check error di `storage/logs/laravel.log`
3. Coba clear cache: `php artisan cache:clear`

#### Problem: Error "Class 'Auth' not found" di routes

**Solusi:**
1. Sudah diperbaiki dengan menambahkan `use Illuminate\Support\Facades\Auth;` di routes/web.php
2. Jika masih error, jalankan: `php artisan route:clear`

### 7. Default Test User (jika sudah ada seeder)

Jika Anda memiliki seeder, Anda bisa test dengan user default:
- Email: test@example.com
- Password: password

Atau register user baru di `/register`

---

## Checklist Setup

- [ ] Google Cloud Console project dibuat
- [ ] Google OAuth Client ID & Secret didapat
- [ ] File .env diupdate dengan Google credentials
- [ ] Email configuration di .env disetup (Gmail/Mailtrap/Log)
- [ ] Gmail App Password dibuat (jika pakai Gmail)
- [ ] `php artisan config:clear` dijalankan
- [ ] Test login regular dengan OTP → ✅ berhasil
- [ ] Test login Google + OTP → ✅ berhasil
- [ ] Test generate PDF sertifikat → ✅ berhasil
- [ ] Test generate PDF undangan → ✅ berhasil

---

## Kontak Support

Jika mengalami kesulitan, check file `STUDI_KASUS_README.md` untuk dokumentasi lengkap.

**Happy coding! 🚀**
