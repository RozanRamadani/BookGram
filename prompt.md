# Project Context: Web NFC API + Laravel Integration (Sistem Absensi NFC)

Saya sedang membuat project praktikum berbasis Laravel dengan integrasi Web NFC API. Tujuan project ini adalah membuat sistem absensi mahasiswa menggunakan kartu NFC yang dipindai melalui browser di smartphone Android.

## Latar Belakang

Web NFC API adalah API JavaScript yang memungkinkan website membaca data NFC langsung dari browser tanpa aplikasi native.

Teknologi ini digunakan untuk:

- Sistem absensi
- Kartu akses
- Contactless system
- Asset tracking
- Smart card

Project ini menggunakan:

Frontend:
- HTML
- Bootstrap
- JavaScript
- Axios atau Fetch API

Backend:
- Laravel

Database:
- MySQL

Environment:
- Android Chrome versi 89+
- HTTPS atau localhost
- Smartphone dengan hardware NFC

Catatan:

Web NFC API TIDAK berjalan pada:

- iOS Safari
- Firefox
- Desktop browser tanpa hardware NFC

Karena itu proses scanning dilakukan pada HP Android.

---

# Alur Sistem

Flow aplikasi:

Mahasiswa membawa kartu NFC
        ↓
Petugas membuka halaman absensi
        ↓
Klik tombol "Aktifkan NFC"
        ↓
Browser meminta izin NFC
        ↓
NDEFReader.scan() aktif
        ↓
Kartu NFC ditempel ke HP
        ↓
Browser membaca serial NFC
        ↓
Data dikirim ke Laravel
        ↓
Laravel mengecek database
        ↓
Data absensi disimpan
        ↓
Tampilkan notifikasi berhasil

---

# User Story

Sebagai petugas:

- Saya dapat mengaktifkan scanner NFC
- Saya dapat menempelkan kartu mahasiswa
- Sistem membaca kartu otomatis
- Sistem menyimpan absensi
- Sistem menampilkan status berhasil atau gagal

Sebagai admin:

- Saya dapat mengelola data mahasiswa
- Saya dapat mendaftarkan kartu NFC baru
- Saya dapat melihat data absensi

---

# Struktur Database

Buat desain database berikut:

Tabel Mahasiswa:

id
nim
nama
serial_nfc
created_at
updated_at

Tabel Absensi:

id
mahasiswa_id
tanggal
jam
status
created_at
updated_at

Relasi:

Mahasiswa
     |
     | one-to-many
     |
Absensi

Satu mahasiswa memiliki banyak data absensi.

---

# Routing Laravel

Buat route berikut:

GET /absensi

Halaman scanner NFC

POST /scan

Menerima data serial NFC

GET /mahasiswa

CRUD mahasiswa

POST /register-kartu

Mendaftarkan kartu NFC baru

---

# Tampilan Halaman Scanner

Halaman scanner memiliki:

Judul:

Sistem Absensi NFC

Button:

Aktifkan NFC

Status:

Belum aktif

Ketika scanner aktif:

"NFC aktif, silakan tempel kartu"

Area hasil:

Nama mahasiswa
NIM
Serial kartu
Status absensi

Gunakan Bootstrap Card.

---

# Ketentuan Implementasi NFC

Scanner hanya boleh aktif setelah user menekan tombol.

Jangan mengaktifkan scan otomatis ketika halaman load.

Gunakan:

```javascript
NDEFReader

Contoh flow:
async function startScan(){

if(!('NDEFReader' in window)){
 tampilkan browser tidak support
 return
}

const ndef=new NDEFReader()

await ndef.scan()

ndef.addEventListener(
'reading',
({serialNumber,message})=>{

kirim serialNumber ke Laravel

})

}

Pengiriman Data

Setelah kartu dibaca:

Kirim:
{
"serial":"04:AB:CD:EF"
}

Gunakan Axios:
axios.post('/scan',{
serial:serialNumber
})

Logika Backend Laravel

Controller Scan:

Terima serial kartu
Cari mahasiswa berdasarkan serial_nfc
Jika ditemukan:

cek apakah mahasiswa sudah absen hari ini

Jika belum:

simpan absensi

Jika sudah:

kirim response "sudah absen"

Jika tidak ditemukan:

kirim response "kartu tidak terdaftar"

Gunakan format JSON:

{
"status":"success",
"message":"Absensi berhasil"
}

atau:

{
"status":"error",
"message":"Kartu tidak ditemukan"
}
Error Handling

Handle kondisi:

Browser tidak support NFC
NFC HP mati
Permission ditolak
Kartu tidak ditemukan
Mahasiswa sudah absen
Gagal koneksi server

Gunakan try-catch.

UI tambahan

Gunakan:

SweetAlert2
Bootstrap
Loading spinner
Status badge
Tujuan Akhir

Saya ingin membangun sistem absensi NFC modern berbasis Laravel + Web NFC API dengan implementasi clean code, struktur MVC rapi, dan mudah dikembangkan.


Prompt ini dibuat supaya GitHub Copilot paham **konteks proyek, requirement, flow, database, frontend, backend, dan logika NFC** sehingga hasil generate kodenya lebih nyambung dan tidak asal.
