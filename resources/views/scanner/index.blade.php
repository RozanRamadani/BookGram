@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Scanner Barcode/QR Code</h4>
                <div id="reader" width="600px"></div>
                <div class="mt-3">
                    <button class="btn btn-primary mr-2" id="startBtn">Start Scan</button>
                    <button class="btn btn-secondary" id="stopBtn" disabled>Stop Scan</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Hasil Scan</h4>
                <div id="result" class="alert alert-info d-none"></div>
                <div id="loading" class="text-center d-none">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="itemDetail" class="d-none">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>ID / Kode Barang</th>
                                <td id="resKode"></td>
                            </tr>
                            <tr>
                                <th>Nama Barang</th>
                                <td id="resNama"></td>
                            </tr>
                            <tr>
                                <th>Harga Barang</th>
                                <td id="resHarga"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Audio untuk beep --}}
<audio id="beepSound" src="{{ asset('assets/audio/beep_audio.mp3') }}"></audio>
@endsection

@push('custom-scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const html5QrCode = new Html5Qrcode("reader");
        const startBtn = document.getElementById("startBtn");
        const stopBtn = document.getElementById("stopBtn");
        const resultDiv = document.getElementById("result");
        const itemDetail = document.getElementById("itemDetail");
        const resKode = document.getElementById("resKode");
        const resNama = document.getElementById("resNama");
        const resHarga = document.getElementById("resHarga");
        const loading = document.getElementById("loading");
        const beepSound = document.getElementById("beepSound");

        function playBeep() {
            beepSound.currentTime = 0;
            beepSound.play().catch(e => console.log('Audio error:', e));
        }

        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        startBtn.addEventListener("click", () => {
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    const cameraId = devices[0].id;
                    html5QrCode.start(cameraId, config, onScanSuccess, onScanFailure)
                        .then(() => {
                            startBtn.disabled = true;
                            stopBtn.disabled = false;
                            resultDiv.classList.add("d-none");
                            itemDetail.classList.add("d-none");
                        })
                        .catch(err => {
                            console.error("Error starting camera", err);
                            alert("Gagal mengakses kamera.");
                        });
                }
            }).catch(err => {
                console.error("Error getting cameras", err);
                alert("Tidak ada kamera yang terdeteksi.");
            });
        });

        stopBtn.addEventListener("click", () => {
            stopScan();
        });

        function stopScan() {
            html5QrCode.stop().then(() => {
                startBtn.disabled = false;
                stopBtn.disabled = true;
            }).catch(err => {
                console.error("Failed to stop scanning.", err);
            });
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Stop scanning immediately
            stopScan();

            // Play short beep
            playBeep();

            // Show result text temporarily
            resultDiv.textContent = `Scanned Kode: ${decodedText}`;
            resultDiv.classList.remove("d-none");
            resultDiv.classList.replace("alert-danger", "alert-info");

            // Fetch data
            loading.classList.remove("d-none");
            itemDetail.classList.add("d-none");

            fetch(`/api/scanner/barang/${encodeURIComponent(decodedText)}`)
                .then(response => response.json())
                .then(data => {
                    loading.classList.add("d-none");
                    if (data.status === 'success') {
                        resKode.textContent = data.data.id_barang;
                        resNama.textContent = data.data.nama;

                        // Format harga ke Rupiah
                        const formatter = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        });
                        resHarga.textContent = formatter.format(data.data.harga);

                        itemDetail.classList.remove("d-none");
                    } else {
                        resultDiv.textContent = `Error: ${data.message}`;
                        resultDiv.classList.replace("alert-info", "alert-danger");
                    }
                })
                .catch(error => {
                    loading.classList.add("d-none");
                    resultDiv.textContent = 'Terjadi kesalahan saat mengambil data barang.';
                    resultDiv.classList.replace("alert-info", "alert-danger");
                    console.error('Error fetching data:', error);
                });
        }

        function onScanFailure(error) {
            // Ignore scan failures (happens continuously until scan succeeds)
        }
    });
</script>
@endpush
