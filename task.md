# MODUL PRAKTIKUM — Web NFC API & Laravel Integration

Sumber: Modul Web NFC API Laravel Integration

---

# Modul 11 — Web NFC API

---

# 01. PENGANTAR WEB NFC API

## 1.1 Apa itu Web NFC API?

Web NFC API adalah antarmuka JavaScript standar yang memungkinkan halaman web membaca dan menulis tag NFC (Near Field Communication) secara langsung dari browser tanpa memerlukan aplikasi native.

API ini merupakan bagian dari inisiatif Project Fugu milik Google, yaitu program perluasan kemampuan web platform agar setara dengan native app.

NFC (Near Field Communication) adalah teknologi komunikasi nirkabel jarak sangat pendek (≤ 4 cm) yang bekerja pada frekuensi 13,56 MHz.

Teknologi ini umum digunakan pada:

- kartu akses
- kartu identitas
- pembayaran contactless
- asset tracking

---

## Catatan Penting — Kompatibilitas Browser

Web NFC API hanya berfungsi pada:

✅ Android Chrome versi 89 ke atas

Tidak didukung pada:

❌ iOS Safari  
❌ Firefox  
❌ Browser desktop tanpa hardware NFC

Solusi praktikum:

Gunakan smartphone Android sebagai perangkat scanner.

---

# 1.2 Cara Kerja Web NFC API

Web NFC API bekerja menggunakan:

```text
NDEFReader
```

(NFC Data Exchange Format Reader)

---

## Flow Dasar

| Langkah | Aksi | Keterangan |
|---|---|---|
| 1 | User klik tombol | Wajib dari user gesture |
| 2 | Browser meminta izin NFC | Popup permission Android |
| 3 | `NDEFReader.scan()` dipanggil | NFC aktif |
| 4 | Kartu ditempel ke HP | ≤ 4 cm |
| 5 | Event `reading` terpanggil | Data tag tersedia |
| 6 | JavaScript memproses data | Bisa kirim ke Laravel |
| 7 | Backend merespons | Simpan DB / validasi |

---

# 1.3 Struktur Data Tag NFC (NDEF)

Saat tag dibaca:

```javascript
{
  serialNumber: "04:AB:CD:EF:12:34:56",

  message: {
    records: [
      {
        recordType: "text",
        mediaType: "text/plain",
        data: ArrayBuffer,
        encoding: "UTF-8",
        lang: "en"
      }
    ]
  }
}
```

---

## Penjelasan

### serialNumber

ID unik kartu NFC.

---

### message.records

Isi data NFC.

Bisa berupa:

- text
- url
- mime
- dll

---

# 1.4 Persyaratan Teknis

| Syarat | Detail | Alasan |
|---|---|---|
| HTTPS / localhost | URL aman | Security browser |
| User Gesture | Harus klik user | Privacy |
| Permission NFC | User harus grant | Security |
| Android Chrome ≥ 89 | Minimum support | API implementation |
| Hardware NFC | HP harus ada NFC | Wajib |

---

# 02. DEBUGGING & REMOTE INSPECTION

Karena NFC harus dites di HP fisik, debugging berbeda dengan web biasa.

---

# 2.1 Metode 1 — Chrome Remote Debugging via USB

Arsitektur:

```text
HP Android
   ↓ USB
Laptop Developer
```

---

## Langkah Setup HP

1. Buka:

```text
Settings → About Phone
```

2. Tap Build Number 7 kali

3. Aktifkan:

```text
Developer Options
→ USB Debugging
```

4. Hubungkan HP ke laptop via USB

5. Izinkan popup:

```text
Allow USB debugging
```

6. Buka website Laravel di Chrome HP

---

## Langkah Setup Laptop

Buka:

```text
chrome://inspect/#devices
```

Pastikan:

```text
Discover USB devices
```

aktif.

---

## Fitur yang Bisa Digunakan

✅ Console  
✅ Network  
✅ Breakpoint  
✅ Inspect DOM  
✅ LocalStorage  
✅ Debugging realtime

---

# 2.2 Metode 2 — Wireless via ngrok + Port Forwarding

Metode ini digunakan tanpa kabel USB.

---

# Opsi A — ngrok HTTPS Tunnel

Karena Web NFC wajib HTTPS.

---

## Install ngrok

```bash
npm install -g ngrok
```

atau download dari:

```text
https://ngrok.com/download
```

---

## Jalankan Laravel

```bash
php artisan serve --port=8000
```

---

## Jalankan ngrok

```bash
ngrok http 8000
```

---

## Hasil

```text
https://abc123.ngrok.io
```

URL ini dibuka di Android Chrome.

---

# Opsi B — Port Forwarding

Alternatif menggunakan USB.

---

## Langkah

1. Hubungkan HP via USB

2. Buka:

```text
chrome://inspect/#devices
```

3. Aktifkan:

```text
Port forwarding
```

4. Tambahkan:

```text
8000 → localhost:8000
```

5. Buka di HP:

```text
http://localhost:8000
```

---

# 2.3 Perbandingan Metode

| Aspek | USB Debugging | ngrok |
|---|---|---|
| Koneksi | USB | Internet |
| HTTPS | Tidak wajib | Otomatis |
| Kecepatan | Sangat cepat | Bergantung internet |
| DevTools | Full | Terbatas |
| Cocok untuk | Development | Demo/testing |

---

# 03. STUDI KASUS — SISTEM ABSENSI NFC

Buat sistem absensi menggunakan:

- Laravel backend
- Web NFC API
- Browser Android

Mahasiswa membawa kartu NFC.

Petugas menempelkan kartu ke HP.

Sistem mencatat kehadiran.

---

# 3.1 Contoh Simple Code NFC

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NFC Scanner</title>
</head>
<body>

<h2>NFC Scanner</h2>

<button onclick="startScan()">
    Aktifkan NFC
</button>

<p id="status">
    Belum aktif.
</p>

<div id="hasil"></div>

<script>

async function startScan() {

    if (!('NDEFReader' in window)) {

        document.getElementById('status')
        .textContent =
        'Browser tidak mendukung Web NFC.'

        return
    }

    try {

        const ndef = new NDEFReader()

        await ndef.scan()

        document.getElementById('status')
        .textContent =
        'NFC aktif. Dekatkan kartu...'

        ndef.addEventListener(
            'reading',
            ({ serialNumber, message }) => {

                let isi = ''

                for (const record of message.records) {

                    isi += new TextDecoder()
                    .decode(record.data)
                }

                console.log('Serial:', serialNumber)
                console.log('Jumlah record:', message.records.length)
                console.log('Isi:', isi)

                document.getElementById('hasil')
                .innerHTML =

                    '<p><b>Serial:</b> '
                    + serialNumber + '</p>' +

                    '<p><b>Isi:</b> '
                    + (isi || '(kosong)')
                    + '</p>'
            }
        )

    } catch (err) {

        document.getElementById('status')
        .textContent =
        'Error: ' + err.message
    }
}

</script>

</body>
</html>
```

---

# 04. REFERENSI & EKSPLORASI LANJUTAN

## Dokumentasi Resmi

| Judul | URL |
|---|---|
| Web NFC API Specification | w3c.github.io/web-nfc |
| MDN Web NFC | developer.mozilla.org |
| Chrome Status | chromestatus.com |
| NDEFReader | developer.mozilla.org |

---

# Artikel & Tutorial

| Judul | Sumber |
|---|---|
| Interact with NFC devices | web.dev/nfc |
| What is Web NFC | web.dev/articles/nfc |
| Remote Debugging | developer.chrome.com |
| ngrok Docs | ngrok.com/docs |

---

# Tools yang Berguna

| Tool | Fungsi |
|---|---|
| NFC TagInfo | Baca detail NFC |
| NFC Tools | Read/write NFC |
| Postman | Test API Laravel |
| ngrok | HTTPS tunnel |
| Chrome DevTools | Debugging |

---

# Tips Praktikum

✅ Gunakan try-catch  
✅ Gunakan console.log()  
✅ Test di Android Chrome  
✅ Gunakan HTTPS  
✅ Coba fitur registrasi kartu  
✅ Eksplor `NDEFReader.write()`

---

# Penutup

```text
Kuasai Web NFC API hari ini,
bangun aplikasi IoT berbasis browser esok hari.
```
