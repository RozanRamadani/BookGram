@extends('layouts.app')

@section('title', 'Kunjungan Toko')

@push('custom-styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 450px;
        border-radius: 10px;
        border: 2px solid #e8ecf1;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }
    #reader {
        width: 100%;
        border-radius: 8px;
        overflow: hidden;
    }
    .gps-status {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 0.9em;
    }
    .gps-status.searching {
        background: #fff3cd;
        color: #856404;
    }
    .gps-status.locked {
        background: #d4edda;
        color: #155724;
    }
    .gps-status.error {
        background: #f8d7da;
        color: #721c24;
    }
    .pulse-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        animation: pulse 1.5s infinite;
    }
    .pulse-dot.orange { background: #ffc107; }
    .pulse-dot.green { background: #28a745; }
    .pulse-dot.red { background: #dc3545; }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(0,0,0,0.3); }
        70% { box-shadow: 0 0 0 8px rgba(0,0,0,0); }
        100% { box-shadow: 0 0 0 0 rgba(0,0,0,0); }
    }
    .step-card {
        border-left: 4px solid #6c63ff;
        transition: all 0.3s ease;
    }
    .step-card.active {
        border-left-color: #28a745;
        background: #f8fff9;
    }
    .step-card.completed {
        border-left-color: #28a745;
        opacity: 0.7;
    }
    .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #6c63ff;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9em;
    }
    .step-card.completed .step-number { background: #28a745; }
    .verification-result {
        border-radius: 10px;
        padding: 20px;
        margin-top: 15px;
    }
</style>
@endpush

@section('content')
<div class="row">
    {{-- LEFT COLUMN — Map --}}
    <div class="col-lg-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-map-marker-radius text-primary"></i> Kunjungan Toko
                        </h4>
                        <p class="card-description mt-1">Verifikasi kehadiran sales dengan GPS & Barcode</p>
                    </div>
                    <a href="{{ route('kunjungan.riwayat') }}" class="btn btn-outline-primary btn-sm">
                        <i class="mdi mdi-history"></i> Riwayat
                    </a>
                </div>

                {{-- GPS Status --}}
                <div id="gpsStatus" class="gps-status searching mb-3">
                    <div class="pulse-dot orange"></div>
                    <span>Mencari sinyal GPS...</span>
                </div>

                {{-- Map --}}
                <div id="map"></div>

                <div class="mt-3">
                    <small class="text-muted">
                        <i class="mdi mdi-information-outline"></i>
                        <span class="text-primary">Biru</span> = Posisi Anda |
                        <span class="text-danger">Merah</span> = Toko |
                        <span style="color:#6c63ff">Lingkaran</span> = Radius verifikasi
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN — Steps --}}
    <div class="col-lg-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="mdi mdi-clipboard-check text-success"></i> Langkah Verifikasi
                </h4>

                {{-- Step 1: GPS Lock --}}
                <div class="step-card p-3 mb-3 rounded" id="step1">
                    <div class="d-flex align-items-center mb-2">
                        <div class="step-number mr-2">1</div>
                        <strong>Lock Posisi GPS</strong>
                    </div>
                    <p class="text-muted mb-1" style="font-size:0.85em;">
                        Pastikan GPS aktif. Sistem akan otomatis mendeteksi posisi Anda.
                    </p>
                    <div id="gpsInfo" class="d-none">
                        <small class="text-success">
                            <i class="mdi mdi-check-circle"></i>
                            Lat: <span id="displayLat">-</span>,
                            Lng: <span id="displayLng">-</span>
                            (akurasi: <span id="displayAcc">-</span>m)
                        </small>
                    </div>
                </div>

                {{-- Step 2: Scan Barcode --}}
                <div class="step-card p-3 mb-3 rounded" id="step2">
                    <div class="d-flex align-items-center mb-2">
                        <div class="step-number mr-2">2</div>
                        <strong>Scan Barcode Toko</strong>
                    </div>
                    <p class="text-muted mb-2" style="font-size:0.85em;">
                        Pindai barcode/QR code yang tertempel di toko.
                    </p>

                    <div id="reader"></div>

                    <div class="mt-2">
                        <button class="btn btn-primary btn-block btn-icon-text" id="startScanBtn" disabled>
                            <i class="mdi mdi-barcode-scan btn-icon-prepend"></i> Mulai Scan Barcode
                        </button>
                        <button class="btn btn-secondary btn-block btn-icon-text d-none" id="stopScanBtn">
                            <i class="mdi mdi-stop btn-icon-prepend"></i> Stop Scanner
                        </button>
                    </div>

                    <div id="scanResult" class="d-none mt-2">
                        <div class="alert alert-info py-2 mb-0">
                            <small>
                                <i class="mdi mdi-barcode"></i>
                                Barcode: <strong id="scannedCode">-</strong>
                            </small>
                        </div>
                    </div>

                    {{-- Toko Info Card (shown after scan + API lookup) --}}
                    <div id="tokoInfoCard" class="d-none mt-3">
                        <div class="card border-left-success" style="border-left: 4px solid #28a745;">
                            <div class="card-body py-3 px-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="mdi mdi-store text-success mr-2" style="font-size: 1.4em;"></i>
                                    <strong id="tokoInfoNama" style="font-size: 1em;"></strong>
                                </div>
                                <table class="table table-sm table-borderless mb-0" style="font-size: 0.85em;">
                                    <tr>
                                        <td class="text-muted" style="width:90px;">Alamat</td>
                                        <td id="tokoInfoAlamat">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Latitude</td>
                                        <td id="tokoInfoLat">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Longitude</td>
                                        <td id="tokoInfoLng">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Radius</td>
                                        <td><span class="badge badge-info" id="tokoInfoRadius">-</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Loading indicator for API lookup --}}
                    <div id="tokoInfoLoading" class="d-none mt-2 text-center">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <small class="text-muted ml-2">Mencari data toko...</small>
                    </div>

                    {{-- Error message --}}
                    <div id="tokoInfoError" class="d-none mt-2">
                        <div class="alert alert-danger py-2 mb-0">
                            <small>
                                <i class="mdi mdi-alert-circle"></i>
                                <span id="tokoInfoErrorMsg">Toko tidak ditemukan</span>
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Step 3: Catatan (Opsional) --}}
                <div class="step-card p-3 mb-3 rounded" id="step3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="step-number mr-2">3</div>
                        <strong>Catatan (Opsional)</strong>
                    </div>
                    <textarea class="form-control" id="catatan" rows="2"
                              placeholder="Tambahkan catatan kunjungan..." maxlength="500"></textarea>
                </div>

                {{-- Submit Button --}}
                <button class="btn btn-success btn-block btn-lg btn-icon-text" id="verifikasiBtn" disabled>
                    <i class="mdi mdi-check-decagram btn-icon-prepend"></i> Kirim Verifikasi Kunjungan
                </button>

                {{-- Verification Result --}}
                <div id="verificationResult" class="d-none"></div>
            </div>
        </div>
    </div>
</div>

{{-- Audio beep --}}
<audio id="beepSound" src="{{ asset('assets/audio/beep_audio.mp3') }}"></audio>
@endsection

@push('custom-scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ═══════════════════════════════════
    // VARIABLES
    // ═══════════════════════════════════
    let currentLat = null;
    let currentLng = null;
    let scannedBarcode = null;
    let gpsLocked = false;
    let map, userMarker, userCircle;
    let html5QrCode;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // DOM elements
    const gpsStatusEl    = document.getElementById('gpsStatus');
    const gpsInfoEl      = document.getElementById('gpsInfo');
    const displayLat     = document.getElementById('displayLat');
    const displayLng     = document.getElementById('displayLng');
    const displayAcc     = document.getElementById('displayAcc');
    const startScanBtn   = document.getElementById('startScanBtn');
    const stopScanBtn    = document.getElementById('stopScanBtn');
    const scanResultEl   = document.getElementById('scanResult');
    const scannedCodeEl  = document.getElementById('scannedCode');
    const verifikasiBtn  = document.getElementById('verifikasiBtn');
    const verResultEl    = document.getElementById('verificationResult');
    const beepSound      = document.getElementById('beepSound');

    // Store data from Laravel
    const tokos = @json($tokos);

    // ═══════════════════════════════════
    // MAP INITIALIZATION
    // ═══════════════════════════════════
    map = L.map('map').setView([-6.2088, 106.8456], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Add toko markers
    const tokoIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    tokos.forEach(function(toko) {
        L.marker([toko.latitude, toko.longitude], { icon: tokoIcon })
            .addTo(map)
            .bindPopup(
                '<strong>' + toko.nama_toko + '</strong><br>' +
                '<small>' + toko.alamat + '</small><br>' +
                '<span class="badge badge-dark">' + toko.kode_barcode + '</span><br>' +
                '<small>Radius: ' + toko.radius_meter + 'm</small>'
            );

        L.circle([toko.latitude, toko.longitude], {
            color: '#6c63ff',
            fillColor: '#6c63ff',
            fillOpacity: 0.08,
            radius: toko.radius_meter
        }).addTo(map);
    });

    // ═══════════════════════════════════
    // GPS TRACKING
    // ═══════════════════════════════════
    if ('geolocation' in navigator) {
        navigator.geolocation.watchPosition(
            function(position) {
                currentLat = position.coords.latitude;
                currentLng = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                if (!gpsLocked) {
                    gpsLocked = true;
                    map.setView([currentLat, currentLng], 16);

                    // Enable scan button
                    startScanBtn.disabled = false;

                    // Update step 1 UI
                    document.getElementById('step1').classList.add('completed');

                    gpsStatusEl.className = 'gps-status locked mb-3';
                    gpsStatusEl.innerHTML = '<div class="pulse-dot green"></div><span>GPS Terkunci — Posisi ditemukan</span>';
                }

                // Update coordinates display
                gpsInfoEl.classList.remove('d-none');
                displayLat.textContent = currentLat.toFixed(8);
                displayLng.textContent = currentLng.toFixed(8);
                displayAcc.textContent = accuracy.toFixed(0);

                // Update user marker on map
                if (userMarker) {
                    userMarker.setLatLng([currentLat, currentLng]);
                    userCircle.setLatLng([currentLat, currentLng]);
                    userCircle.setRadius(accuracy);
                } else {
                    userMarker = L.circleMarker([currentLat, currentLng], {
                        radius: 8,
                        fillColor: '#007bff',
                        color: '#fff',
                        weight: 3,
                        fillOpacity: 1
                    }).addTo(map).bindPopup('Posisi Anda');

                    userCircle = L.circle([currentLat, currentLng], {
                        color: '#007bff',
                        fillColor: '#007bff',
                        fillOpacity: 0.1,
                        radius: accuracy
                    }).addTo(map);
                }
            },
            function(error) {
                gpsStatusEl.className = 'gps-status error mb-3';
                let msg = 'Gagal mendapatkan GPS';
                switch(error.code) {
                    case 1: msg = 'Izin GPS ditolak. Aktifkan GPS di pengaturan browser.'; break;
                    case 2: msg = 'GPS tidak tersedia.'; break;
                    case 3: msg = 'Timeout mendapatkan GPS.'; break;
                }
                gpsStatusEl.innerHTML = '<div class="pulse-dot red"></div><span>' + msg + '</span>';
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 5000
            }
        );
    } else {
        gpsStatusEl.className = 'gps-status error mb-3';
        gpsStatusEl.innerHTML = '<div class="pulse-dot red"></div><span>Browser tidak mendukung Geolocation</span>';
    }

    // ═══════════════════════════════════
    // BARCODE SCANNER
    // ═══════════════════════════════════
    html5QrCode = new Html5Qrcode("reader");

    function playBeep() {
        beepSound.currentTime = 0;
        beepSound.play().catch(function() {});
    }

    startScanBtn.addEventListener('click', function() {
        Html5Qrcode.getCameras().then(function(devices) {
            if (devices && devices.length) {
                // Prefer back camera
                let cameraId = devices[0].id;
                for (let i = 0; i < devices.length; i++) {
                    if (devices[i].label.toLowerCase().includes('back') ||
                        devices[i].label.toLowerCase().includes('rear') ||
                        devices[i].label.toLowerCase().includes('belakang')) {
                        cameraId = devices[i].id;
                        break;
                    }
                }

                html5QrCode.start(
                    cameraId,
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    onScanSuccess,
                    function() {} // ignore failures
                ).then(function() {
                    startScanBtn.classList.add('d-none');
                    stopScanBtn.classList.remove('d-none');
                    document.getElementById('step2').classList.add('active');
                }).catch(function(err) {
                    Swal.fire('Error', 'Gagal mengakses kamera: ' + err, 'error');
                });
            }
        }).catch(function(err) {
            Swal.fire('Error', 'Tidak ada kamera yang terdeteksi.', 'error');
        });
    });

    stopScanBtn.addEventListener('click', function() {
        stopScanner();
    });

    function stopScanner() {
        html5QrCode.stop().then(function() {
            startScanBtn.classList.remove('d-none');
            stopScanBtn.classList.add('d-none');
        }).catch(function() {});
    }

    function onScanSuccess(decodedText) {
        // Stop scanning
        stopScanner();

        // Play beep
        playBeep();

        // Store scanned barcode
        scannedBarcode = decodedText;

        // Update UI
        scanResultEl.classList.remove('d-none');
        scannedCodeEl.textContent = decodedText;
        document.getElementById('step2').classList.remove('active');
        document.getElementById('step2').classList.add('completed');

        // Lookup toko info via API
        lookupToko(decodedText);
    }

    /**
     * Lookup toko by barcode via API and display info.
     */
    function lookupToko(barcode) {
        const tokoInfoCard    = document.getElementById('tokoInfoCard');
        const tokoInfoLoading = document.getElementById('tokoInfoLoading');
        const tokoInfoError   = document.getElementById('tokoInfoError');

        // Reset state
        tokoInfoCard.classList.add('d-none');
        tokoInfoError.classList.add('d-none');
        tokoInfoLoading.classList.remove('d-none');

        // Call API: GET /api/toko/{kode_barcode}
        fetch('/api/toko/' + encodeURIComponent(barcode), {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Toko tidak ditemukan');
            }
            return response.json();
        })
        .then(function(result) {
            tokoInfoLoading.classList.add('d-none');

            if (result.success && result.data) {
                const d = result.data;
                document.getElementById('tokoInfoNama').textContent   = d.nama_toko;
                document.getElementById('tokoInfoAlamat').textContent = d.alamat || '-';
                document.getElementById('tokoInfoLat').textContent    = d.latitude;
                document.getElementById('tokoInfoLng').textContent    = d.longitude;
                document.getElementById('tokoInfoRadius').textContent = d.radius + 'm';

                tokoInfoCard.classList.remove('d-none');

                // Enable verification button
                checkCanVerify();
            } else {
                document.getElementById('tokoInfoErrorMsg').textContent = result.message || 'Toko tidak ditemukan';
                tokoInfoError.classList.remove('d-none');
            }
        })
        .catch(function(error) {
            tokoInfoLoading.classList.add('d-none');
            document.getElementById('tokoInfoErrorMsg').textContent = 'Barcode "' + barcode + '" tidak terdaftar di sistem.';
            tokoInfoError.classList.remove('d-none');
        });
    }

    function checkCanVerify() {
        if (gpsLocked && scannedBarcode) {
            verifikasiBtn.disabled = false;
        }
    }

    // ═══════════════════════════════════
    // VERIFICATION
    // ═══════════════════════════════════
    verifikasiBtn.addEventListener('click', function() {
        if (!gpsLocked || !scannedBarcode) {
            Swal.fire('Perhatian', 'Pastikan GPS sudah terkunci dan barcode sudah di-scan.', 'warning');
            return;
        }

        // Disable button & show loading
        verifikasiBtn.disabled = true;
        verifikasiBtn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span> Memproses...';

        axios.post('{{ route("kunjungan.verifikasi") }}', {
            latitude: currentLat,
            longitude: currentLng,
            kode_barcode: scannedBarcode,
            catatan: document.getElementById('catatan').value
        }, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(function(response) {
            const data = response.data;

            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Kunjungan Valid!',
                    html: '<p>' + data.message + '</p>' +
                          '<table class="table table-sm mt-2 text-left">' +
                          '<tr><td>Toko</td><td><strong>' + data.data.toko + '</strong></td></tr>' +
                          '<tr><td>Jarak</td><td>' + data.data.jarak + '</td></tr>' +
                          '<tr><td>Radius</td><td>' + data.data.radius + '</td></tr>' +
                          '<tr><td>Waktu</td><td>' + data.data.waktu + '</td></tr>' +
                          '</table>',
                    confirmButtonColor: '#28a745'
                });

                showResult('success', data);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Di Luar Radius!',
                    html: '<p>' + data.message + '</p>' +
                          '<table class="table table-sm mt-2 text-left">' +
                          '<tr><td>Toko</td><td><strong>' + data.data.toko + '</strong></td></tr>' +
                          '<tr><td>Jarak Anda</td><td class="text-danger"><strong>' + data.data.jarak + '</strong></td></tr>' +
                          '<tr><td>Radius Toko</td><td>' + data.data.radius + '</td></tr>' +
                          '</table>',
                    confirmButtonColor: '#ffc107'
                });

                showResult('warning', data);
            }
        })
        .catch(function(error) {
            const msg = error.response?.data?.message || 'Terjadi kesalahan saat memproses verifikasi.';
            Swal.fire('Error', msg, 'error');

            showResult('error', { message: msg });
        })
        .finally(function() {
            verifikasiBtn.disabled = false;
            verifikasiBtn.innerHTML = '<i class="mdi mdi-check-decagram btn-icon-prepend"></i> Kirim Verifikasi Kunjungan';

            // Reset for next scan
            scannedBarcode = null;
            scanResultEl.classList.add('d-none');
            document.getElementById('step2').classList.remove('completed');
            document.getElementById('tokoInfoCard').classList.add('d-none');
            document.getElementById('tokoInfoError').classList.add('d-none');
        });
    });

    function showResult(type, data) {
        let bgClass, iconClass, label;
        switch(type) {
            case 'success':
                bgClass = 'bg-success text-white';
                iconClass = 'mdi-check-circle';
                label = 'VALID';
                break;
            case 'warning':
                bgClass = 'bg-warning text-dark';
                iconClass = 'mdi-alert';
                label = 'DI LUAR RADIUS';
                break;
            default:
                bgClass = 'bg-danger text-white';
                iconClass = 'mdi-close-circle';
                label = 'ERROR';
        }

        verResultEl.className = 'verification-result ' + bgClass;
        verResultEl.innerHTML = '<div class="d-flex align-items-center">' +
            '<i class="mdi ' + iconClass + ' mr-2" style="font-size:1.5em;"></i>' +
            '<div><strong>' + label + '</strong><br><small>' + (data.message || '') + '</small></div>' +
            '</div>';
        verResultEl.classList.remove('d-none');
    }
});
</script>
@endpush
