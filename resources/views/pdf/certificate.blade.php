<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            font-family: Arial, Helvetica, sans-serif;
            background: #ffffff;
        }

        /* ── Two-column layout ── */
        .layout {
            display: table;
            width: 100%;
            height: 210mm;
        }
        .sidebar {
            display: table-cell;
            width: 72mm;
            background: #0d4f6c;
            vertical-align: top;
            position: relative;
            overflow: hidden;
        }
        .sidebar-accent {
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 160px;
            height: 160px;
            background: rgba(255,255,255,0.07);
            border-radius: 50%;
        }
        .sidebar-accent2 {
            position: absolute;
            top: -20px;
            right: -40px;
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .sidebar-inner {
            padding: 28px 20px;
            height: 100%;
            position: relative;
            z-index: 2;
        }
        .sidebar-logos {
            display: table;
            width: 100%;
            margin-bottom: 22px;
        }
        .sidebar-logos td {
            vertical-align: middle;
            text-align: center;
        }
        .logo-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 2px solid rgba(255,255,255,0.4);
            display: inline-block;
            line-height: 44px;
            text-align: center;
            font-size: 9px;
            color: rgba(255,255,255,0.8);
            font-weight: bold;
        }
        .sidebar-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.3);
            margin: 16px 0;
        }
        .sidebar-label {
            font-size: 9px;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }
        .sidebar-cert-type {
            font-size: 22px;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 3px;
            line-height: 1.2;
        }
        .sidebar-number {
            font-size: 9px;
            color: rgba(255,255,255,0.55);
            margin-top: 10px;
            word-break: break-all;
            line-height: 1.5;
        }
        .sidebar-date-block {
            margin-top: 24px;
        }
        .sidebar-date-label {
            font-size: 8px;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .sidebar-date-value {
            font-size: 11px;
            color: #ffffff;
            margin-top: 3px;
            font-weight: 600;
        }
        .bar-gold {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #c99b3a, #f5d77a, #c99b3a);
        }

        /* ── Main content ── */
        .main {
            display: table-cell;
            vertical-align: middle;
            padding: 32px 46px 32px 40px;
            position: relative;
        }
        .top-strip {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(90deg, #c99b3a, #f5d77a, #c99b3a);
        }
        .bottom-strip {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 6px;
            background: #0d4f6c;
        }
        .watermark {
            position: absolute;
            right: 50px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 180px;
            color: rgba(13,79,108,0.04);
            font-weight: 900;
            user-select: none;
            pointer-events: none;
            line-height: 1;
        }

        .given-label {
            font-size: 11px;
            color: #888;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .recipient-name {
            font-size: 38px;
            font-weight: 700;
            color: #0d4f6c;
            font-family: 'Times New Roman', Georgia, serif;
            font-style: italic;
            margin-bottom: 6px;
            line-height: 1.1;
        }
        .name-underline {
            height: 3px;
            width: 180px;
            background: linear-gradient(90deg, #c99b3a, #f5d77a);
            border-radius: 2px;
            margin-bottom: 18px;
        }
        .desc-text {
            font-size: 11px;
            color: #555;
            margin-bottom: 4px;
        }
        .role-badge {
            display: inline-block;
            background: #0d4f6c;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 6px 18px;
            border-radius: 3px;
            margin: 8px 0 14px;
        }
        .event-block {
            font-size: 10.5px;
            color: #444;
            line-height: 1.65;
            max-width: 420px;
        }
        .event-name {
            font-style: italic;
            font-weight: 700;
            color: #0d4f6c;
        }

        /* ── Signature row ── */
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 28px;
        }
        .sig-cell {
            display: table-cell;
            text-align: center;
            width: 33.33%;
            padding: 0 10px;
            vertical-align: top;
        }
        .sig-role {
            font-size: 9.5px;
            color: #555;
            font-weight: 600;
            margin-bottom: 50px;
            line-height: 1.4;
        }
        .sig-line {
            border-top: 1.5px solid #0d4f6c;
            width: 140px;
            margin: 0 auto 5px;
        }
        .sig-name {
            font-size: 10px;
            font-weight: 700;
            color: #0d4f6c;
        }
    </style>
</head>
<body>
<div class="layout">

    {{-- ── Sidebar ── --}}
    <div class="sidebar">
        <div class="sidebar-accent"></div>
        <div class="sidebar-accent2"></div>
        <div class="sidebar-inner">
            {{-- 5 logos as placeholder circles --}}
            <table class="sidebar-logos">
                <tr>
                    @foreach(['KEM','ORG','UA','FKI','PH'] as $l)
                    <td><span class="logo-circle">{{ $l }}</span></td>
                    @endforeach
                </tr>
            </table>

            <hr class="sidebar-divider">

            <div class="sidebar-label">Dokumen Resmi</div>
            <div class="sidebar-cert-type">{{ $title }}</div>
            <div class="sidebar-number">No. {{ $number }}</div>

            <div class="sidebar-date-block">
                <div class="sidebar-date-label">Tanggal Terbit</div>
                <div class="sidebar-date-value">{{ $issued_date }}</div>
            </div>
        </div>
        <div class="bar-gold"></div>
    </div>

    {{-- ── Main Content ── --}}
    <div class="main">
        <div class="top-strip"></div>
        <div class="bottom-strip"></div>
        <div class="watermark">✦</div>

        <div class="given-label">Diberikan Kepada</div>
        <div class="recipient-name">{{ $recipient_name }}</div>
        <div class="name-underline"></div>

        <div class="desc-text">Atas partisipasinya sebagai:</div>
        <div class="role-badge">{{ $role }}</div>

        <div class="event-block">
            <div>{{ $event_detail }}</div>
            <div class="event-name">"{{ $event_name }}"</div>
            <div style="margin-top:4px;">
                Yang diselenggarakan oleh <strong>{{ $organizer }}</strong>,<br>
                pada <strong>{{ $date }}</strong>.
            </div>
        </div>

        <div class="signature-section">
            <div class="sig-cell">
                <div class="sig-role">Dekan FIKIA UNAIR</div>
                <div class="sig-line"></div>
                <div class="sig-name">______________________</div>
            </div>
            <div class="sig-cell">
                <div class="sig-role">Koordinator Program Studi<br>Kesehatan Masyarakat FIKIA UNAIR</div>
                <div class="sig-line"></div>
                <div class="sig-name">______________________</div>
            </div>
            <div class="sig-cell">
                <div class="sig-role">Ketua Pelaksana</div>
                <div class="sig-line"></div>
                <div class="sig-name">______________________</div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
