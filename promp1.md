# PROJECT SPECIFICATION
# Geolocation + Barcode Verification System
# Laravel 11

Saya sedang mengembangkan fitur "Kunjungan Toko" pada aplikasi Laravel yang sudah berjalan.

Project ini menggunakan:

- Laravel 11
- PHP 8+
- MySQL
- Bootstrap 5
- Axios
- SweetAlert2
- DataTables
- Barcode Scanner HTML5
- Browser Geolocation API

Tujuan fitur ini adalah memverifikasi bahwa sales benar-benar datang ke lokasi toko yang ditugaskan.

---

# LATAR BELAKANG BISNIS

Pemilik distributor ingin memastikan bahwa sales benar-benar mengunjungi toko.

Masalah:

GPS tidak selalu akurat.

Oleh karena itu sistem harus mempertimbangkan:

- latitude
- longitude
- accuracy

baik dari lokasi toko maupun lokasi sales.

---

# FLOW SISTEM

## Tahap 1

Admin menginput data toko.

Data toko:

- barcode
- nama_toko
- latitude
- longitude
- accuracy

Data ini disimpan ke database.

---

## Tahap 2

Sales membuka menu:

"Kunjungan Toko"

---

## Tahap 3

Sales melakukan scan barcode toko.

Barcode berisi kode toko.

Setelah barcode berhasil dibaca:

ambil data toko dari database.

Tampilkan:

- nama toko
- latitude toko
- longitude toko
- accuracy toko

---

## Tahap 4

Sales menekan tombol:

"Ambil Lokasi"

Sistem menggunakan Browser Geolocation API.

---

# PERSYARATAN GEOLOCATION

Gunakan:

```javascript
navigator.geolocation.watchPosition()
```

Bukan hanya getCurrentPosition().

Gunakan konsep:

Best Accuracy Location.

Implementasikan fungsi berikut:

```javascript
async function getAccuratePosition(
    targetAccuracy = 50,
    maxWait = 20000
)
```

Tujuan:

mengambil lokasi terbaik yang memiliki accuracy paling kecil.

Jika accuracy <= targetAccuracy

maka stop.

Jika timeout:

gunakan hasil terbaik yang tersedia.

---

# DATA YANG DIAMBIL

Simpan:

- latitude
- longitude
- accuracy

lokasi sales.

---

# PERHITUNGAN JARAK

Gunakan Formula Haversine.

JANGAN menggunakan Euclidean distance.

Implementasikan function:

```javascript
function haversine(
 lat1,
 lng1,
 lat2,
 lng2
)
```

Output:

meter.

---

# RULE VALIDASI

Misal:

threshold = 300 meter

accuracy toko = 30

accuracy sales = 20

threshold efektif:

300 + 30 + 20

= 350 meter

Jika:

jarak aktual <= threshold efektif

status:

DITERIMA

Jika:

jarak aktual > threshold efektif

status:

DITOLAK

---

# DATABASE

## lokasi_toko

id
barcode
nama_toko
latitude
longitude
accuracy
created_at
updated_at

---

## kunjungan_toko

id
sales_id
barcode
latitude_sales
longitude_sales
accuracy_sales
jarak
threshold
status
created_at
updated_at

---

# HALAMAN 1
# MASTER TOKO

Gunakan DataTables.

Kolom:

Barcode
Nama Toko
Latitude
Longitude
Accuracy
Cetak Barcode

Fitur:

CRUD

---

# HALAMAN 2
# INPUT TITIK AWAL TOKO

Form:

Latitude
Longitude
Accuracy

Button:

Ambil Lokasi

Ketika diklik:

ambil koordinat terbaik menggunakan Geolocation API.

---

# HALAMAN 3
# KUNJUNGAN TOKO

Bagian:

Barcode Scanner

Gunakan:

html5-qrcode

atau

Instascan

Setelah scan:

tampilkan informasi toko.

---

Bagian kedua:

Data lokasi toko

Nama
Latitude
Longitude
Accuracy

---

Bagian ketiga:

Data lokasi sales

Latitude
Longitude
Accuracy

---

Button:

Ambil Lokasi

---

Button:

Submit Kunjungan

---

# VALIDASI

Sebelum submit:

1. barcode valid
2. lokasi berhasil diambil
3. hitung jarak
4. tentukan diterima / ditolak

---

# OUTPUT

Tampilkan:

Status:
DITERIMA atau DITOLAK

Jarak:
xxx meter

Threshold:
xxx meter

Accuracy Toko:
xxx meter

Accuracy Sales:
xxx meter

---

# SWEETALERT

Jika diterima:

"Kunjungan berhasil diverifikasi"

Jika ditolak:

"Lokasi terlalu jauh dari toko"

---

# CLEAN CODE REQUIREMENT

Pisahkan:

Controller
Service
Helper

Buat:

GeoLocationService

yang berisi:

- haversine()
- calculateThreshold()
- validateDistance()

---

Gunakan:

Repository Pattern jika diperlukan.

---

# TUJUAN AKHIR

Saya ingin sistem validasi kunjungan toko yang robust dan mempertimbangkan akurasi GPS seperti aplikasi logistik profesional.