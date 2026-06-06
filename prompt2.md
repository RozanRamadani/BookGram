# Context

Saya sedang mengerjakan studi kasus Geolocation Kunjungan Toko menggunakan Laravel 11.

Saat ini saya sudah memiliki:

- CRUD Data Toko
- Halaman Kunjungan Toko
- Fitur Geolocation
- Fitur Scan Barcode menggunakan html5-qrcode

Tabel toko memiliki field:

- id
- nama_toko
- alamat
- latitude
- longitude
- accuracy
- radius
- barcode

Contoh nilai barcode:

TOKO-G7H8I9
TOKO-P7Q8R9
TOKO-M4N5O6

Saat ini barcode hanya berupa string yang ditampilkan di tabel.

Saya ingin menambahkan fitur QR Code agar kode toko dapat dicetak dan dipindai oleh sales saat melakukan kunjungan.

---

# Requirement

Gunakan package:

simplesoftwareio/simple-qrcode

atau package QR Code Laravel yang kompatibel dengan Laravel 11.

---

# Fitur yang Dibutuhkan

## 1. Generate QR Code

Setiap toko memiliki QR Code berdasarkan field:

barcode

Contoh:

TOKO-G7H8I9

QR Code harus menyimpan value:

TOKO-G7H8I9

---

## 2. Tombol QR Code

Tambahkan tombol:

[ QR Code ]

pada kolom aksi Data Toko.

Saat tombol diklik:

munculkan modal Bootstrap.

---

## 3. Modal QR Code

Modal menampilkan:

- Nama Toko
- Kode Barcode
- QR Code ukuran 250px

Contoh:

Nama Toko:
Toko Berkah Abadi

Kode:
TOKO-G7H8I9

[ QR CODE ]

---

## 4. Download QR

Tambahkan tombol:

Download PNG

atau

Download PDF

agar QR Code bisa dicetak dan ditempel di toko.

---

## 5. Integrasi Dengan Scanner

Saat sales membuka menu Kunjungan Toko:

Klik:

Mulai Scan Barcode

Scanner menggunakan html5-qrcode.

Ketika QR Code dipindai:

hasil scan berupa:

TOKO-G7H8I9

Kemudian frontend mengirim request:

GET /api/toko/TOKO-G7H8I9

Backend mencari toko berdasarkan field barcode.

Jika ditemukan:

kembalikan:

- nama toko
- alamat
- latitude
- longitude
- accuracy
- radius

---

## 6. Response JSON

Contoh:

{
  "success": true,
  "data": {
    "nama_toko": "Toko Berkah Abadi",
    "barcode": "TOKO-G7H8I9",
    "latitude": -6.235142,
    "longitude": 106.822084,
    "accuracy": 20,
    "radius": 100
  }
}

---

## 7. Flow Lengkap

Admin input toko
↓
Generate QR Code
↓
Cetak QR Code
↓
Tempel di toko
↓
Sales datang ke toko
↓
Scan QR Code
↓
Dapat kode toko
↓
Ambil data toko dari database
↓
Ambil lokasi GPS sales
↓
Hitung jarak menggunakan Haversine
↓
Tentukan DITERIMA atau DITOLAK

---

# Coding Style

Gunakan:

- Laravel 11
- Bootstrap 5
- SweetAlert2
- Clean Controller
- Route Model Binding jika memungkinkan
- Responsive UI

Berikan kode lengkap:

- Route
- Controller
- Blade
- Modal Bootstrap
- QR Code Generator
- Download PDF
- Integrasi html5-qrcode