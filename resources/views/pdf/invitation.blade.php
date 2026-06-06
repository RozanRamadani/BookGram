<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            background: #ffffff;
        }

        /* â”€â”€ Page wrapper: sidebar + content â”€â”€ */
        .page {
            display: table;
            width: 210mm;
            min-height: 297mm;
        }

        /* â”€â”€ Left sidebar â”€â”€ */
        .sidebar {
            display: table-cell;
            width: 52mm;
            background: #1a3a5c;
            vertical-align: top;
            position: relative;
            overflow: hidden;
        }
        .sb-top-bar {
            height: 8px;
            background: linear-gradient(90deg, #c99b3a, #f5d77a);
        }
        .sb-inner {
            padding: 28px 18px;
        }
        .sb-org-mark {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            border: 2px solid rgba(255,255,255,0.35);
            text-align: center;
            line-height: 54px;
            font-size: 20px;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 16px;
        }
        .sb-inst-name {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.3;
            margin-bottom: 4px;
        }
        .sb-inst-sub {
            font-size: 10px;
            color: rgba(255,255,255,0.65);
            line-height: 1.4;
            margin-bottom: 18px;
        }
        .sb-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.2);
            margin: 14px 0;
        }
        .sb-label {
            font-size: 8px;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 1.8px;
            margin-bottom: 4px;
        }
        .sb-value {
            font-size: 9.5px;
            color: rgba(255,255,255,0.85);
            line-height: 1.5;
            word-break: break-all;
        }
        .sb-contact-block {
            margin-bottom: 14px;
        }
        .sb-bottom-accent {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .sb-bottom-bar {~
            height: 6px;
            background: linear-gradient(90deg, #c99b3a, #f5d77a);
        }
        .sb-circle-deco {
            position: absolute;
            bottom: 40px;
            right: -30px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        /* â”€â”€ Main content area â”€â”€ */
        .main {
            display: table-cell;
            vertical-align: top;
            padding: 0;
        }
        .main-top-bar {
            height: 8px;
            background: #1a3a5c;
        }
        .main-content {
            padding: 28px 36px 36px 28px;
        }

        /* Document meta box */
        .meta-box {
            background: #f4f7fb;
            border-left: 4px solid #1a3a5c;
            padding: 12px 16px;
            margin-bottom: 22px;
            font-size: 10pt;
        }
        .meta-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }
        .meta-key {
            display: table-cell;
            width: 72px;
            color: #555;
            font-size: 10px;
        }
        .meta-sep {
            display: table-cell;
            width: 12px;
            color: #555;
            font-size: 10px;
        }
        .meta-val {
            display: table-cell;
            font-size: 10px;
            color: #1a1a1a;
            font-weight: 600;
        }
        .meta-date {
            float: right;
            font-size: 10px;
            color: #555;
            margin-top: -36px;
        }

        /* Subject heading */
        .subject-heading {
            font-size: 15pt;
            font-weight: 700;
            color: #1a3a5c;
            margin-bottom: 18px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e0e8f0;
        }

        /* Recipients */
        .recipients-wrap {
            margin-bottom: 18px;
        }
        .recipients-title {
            font-size: 10.5px;
            font-weight: 700;
            color: #1a3a5c;
            margin-bottom: 6px;
        }
        .recipients-list {
            margin: 0;
            padding-left: 18px;
            font-size: 10px;
            color: #333;
        }
        .recipients-list li {
            margin-bottom: 2px;
        }

        /* Body text */
        .body-text {
            font-size: 10.5px;
            color: #333;
            text-align: justify;
            line-height: 1.75;
            text-indent: 40px;
            margin-bottom: 16px;
        }

        /* Event details */
        .event-card {
            border: 1px solid #d0dce8;
            border-radius: 4px;
            padding: 14px 18px;
            margin-bottom: 16px;
            background: #fafcff;
        }
        .event-card-title {
            font-size: 9px;
            font-weight: 700;
            color: #1a3a5c;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #d0dce8;
        }
        .ev-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .ev-key {
            display: table-cell;
            width: 90px;
            font-size: 10px;
            color: #666;
        }
        .ev-sep {
            display: table-cell;
            width: 14px;
            font-size: 10px;
            color: #666;
        }
        .ev-val {
            display: table-cell;
            font-size: 10px;
            color: #1a1a1a;
            font-weight: 600;
        }

        /* Closing */
        .closing-text {
            font-size: 10.5px;
            color: #333;
            text-align: justify;
            line-height: 1.75;
            text-indent: 40px;
            margin-bottom: 30px;
        }

        /* Signature */
        .signature-wrap {
            text-align: right;
        }
        .signature-inner {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
        .sig-position {
            font-size: 10.5px;
            color: #333;
            margin-bottom: 64px;
        }
        .sig-line {
            border-top: 1.5px solid #1a3a5c;
            width: 180px;
            margin: 0 auto 5px;
        }
        .sig-name {
            font-size: 10.5px;
            font-weight: 700;
            color: #1a3a5c;
        }
        .sig-nip {
            font-size: 9.5px;
            color: #666;
            margin-top: 2px;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- â”€â”€ Left Sidebar â”€â”€ --}}
    <div class="sidebar">
        <div class="sb-top-bar"></div>
        <div class="sb-inner">
            <div class="sb-org-mark">UA</div>
            <div class="sb-inst-name">{{ $title }}</div>
            <div class="sb-inst-sub">{{ $subtitle }}</div>

            <hr class="sb-divider">

            <div class="sb-contact-block">
                <div class="sb-label">Alamat</div>
                <div class="sb-value">{{ $address }}</div>
            </div>
            <div class="sb-contact-block">
                <div class="sb-label">Website</div>
                <div class="sb-value">{{ $website }}</div>
            </div>
            <div class="sb-contact-block">
                <div class="sb-label">Email</div>
                <div class="sb-value">{{ $email }}</div>
            </div>
        </div>
        <div class="sb-circle-deco"></div>
        <div class="sb-bottom-accent">
            <div class="sb-bottom-bar"></div>
        </div>
    </div>

    {{-- â”€â”€ Main Content â”€â”€ --}}
    <div class="main">
        <div class="main-top-bar"></div>
        <div class="main-content">

            <div class="meta-box">
                <div class="meta-row">
                    <span class="meta-key">Nomor</span>
                    <span class="meta-sep">:</span>
                    <span class="meta-val">{{ $number }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-key">Lampiran</span>
                    <span class="meta-sep">:</span>
                    <span class="meta-val">{{ $attachment }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-key">Perihal</span>
                    <span class="meta-sep">:</span>
                    <span class="meta-val">{{ $subject }}</span>
                </div>
                <div class="meta-date">{{ $date }}</div>
            </div>

            <div class="subject-heading">{{ $subject }}</div>

            <div class="recipients-wrap">
                <div class="recipients-title">Yth.</div>
                <ol class="recipients-list">
                    @foreach($recipients as $recipient)
                    <li>{{ $recipient }}</li>
                    @endforeach
                </ol>
            </div>

            <div class="body-text">{{ $content }}</div>

            <div class="event-card">
                <div class="event-card-title">Detail Kegiatan</div>
                <div class="ev-row">
                    <span class="ev-key">Hari, Tanggal</span>
                    <span class="ev-sep">:</span>
                    <span class="ev-val">{{ $event_day }}</span>
                </div>
                <div class="ev-row">
                    <span class="ev-key">Waktu</span>
                    <span class="ev-sep">:</span>
                    <span class="ev-val">{{ $event_time }}</span>
                </div>
                <div class="ev-row">
                    <span class="ev-key">Tempat</span>
                    <span class="ev-sep">:</span>
                    <span class="ev-val">{{ $event_location }}</span>
                </div>
                <div class="ev-row">
                    <span class="ev-key">Agenda</span>
                    <span class="ev-sep">:</span>
                    <span class="ev-val">{{ $event_agenda }}</span>
                </div>
            </div>

            <div class="closing-text">{{ $closing }}</div>

            <div class="signature-wrap">
                <div class="signature-inner">
                    <div class="sig-position">Dekan,</div>
                    <div class="sig-line"></div>
                    <div class="sig-name">______________________</div>
                    <div class="sig-nip">NIP. ____________________</div>
                </div>
            </div>

        </div>
    </div>

</div>
</body>
</html>
