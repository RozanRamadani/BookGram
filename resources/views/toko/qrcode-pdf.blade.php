<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code — {{ $toko->nama_toko }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #fff;
        }
        .qr-card {
            text-align: center;
            padding: 20px;
            border: 2px dashed #ccc;
            border-radius: 12px;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }
        .qr-card h2 {
            font-size: 16px;
            color: #333;
            margin-bottom: 5px;
            word-wrap: break-word;
        }
        .qr-card .alamat {
            font-size: 10px;
            color: #777;
            margin-bottom: 12px;
        }
        .qr-card .qr-image {
            margin: 10px auto;
            display: block;
        }
        .qr-card .kode {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
            padding: 6px 16px;
            background: #f0f0f0;
            border-radius: 6px;
            display: inline-block;
            letter-spacing: 1px;
        }
        .qr-card .footer-text {
            font-size: 9px;
            color: #999;
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <div class="qr-card">
        <h2>{{ $toko->nama_toko }}</h2>
        <p class="alamat">{{ $toko->alamat }}</p>

        <div class="qr-image">
            <img src="data:image/svg+xml;base64,{{ $qrcodeBase64 }}" width="250" height="250" alt="QR Code">
        </div>

        <div class="kode">{{ $toko->kode_barcode }}</div>

        <p class="footer-text">
            Scan QR Code ini untuk verifikasi kunjungan<br>
            Radius: {{ $toko->radius_meter }} meter
        </p>
    </div>
</body>
</html>
