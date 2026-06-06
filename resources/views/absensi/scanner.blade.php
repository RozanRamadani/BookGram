@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-id-card"></i> Sistem Absensi NFC</h4>
                </div>

                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <span id="nfcStatusBadge" class="badge bg-secondary p-2 fs-6">Belum aktif</span>
                    </div>

                    <div class="nfc-animation mb-4" id="nfcAnimation" style="display: none;">
                        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">NFC aktif, silakan tempel kartu</p>
                    </div>

                    <button id="btnStartNFC" class="btn btn-primary btn-lg">
                        <i class="fas fa-wifi"></i> Aktifkan NFC
                    </button>

                    <button id="btnStopNFC" class="btn btn-danger btn-lg" style="display: none;">
                        <i class="fas fa-stop-circle"></i> Berhenti Scan
                    </button>

                    <hr class="my-4">

                    <div id="resultArea" style="display: none;">
                        <h5 class="text-secondary">Hasil Scan Terakhir</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered mt-3 text-start">
                                <tbody>
                                    <tr>
                                        <th width="30%" class="bg-light">Nama</th>
                                        <td id="resNama">-</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">NIM</th>
                                        <td id="resNim">-</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Serial Kartu</th>
                                        <td id="resSerial">-</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Status</th>
                                        <td><span id="resStatus" class="badge">-</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnStartNFC = document.getElementById('btnStartNFC');
        const btnStopNFC = document.getElementById('btnStopNFC');
        const nfcStatusBadge = document.getElementById('nfcStatusBadge');
        const nfcAnimation = document.getElementById('nfcAnimation');
        const resultArea = document.getElementById('resultArea');

        let ndef = null;
        let ctrl = null;

        btnStartNFC.addEventListener('click', startScan);
        btnStopNFC.addEventListener('click', stopScan);

        async function startScan() {
            if (!('NDEFReader' in window)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Browser atau perangkat Anda tidak mendukung Web NFC API. Akses menggunakan Chrome for Android dengan hardware NFC.'
                });
                return;
            }

            try {
                ndef = new NDEFReader();
                ctrl = new AbortController();

                await ndef.scan({ signal: ctrl.signal });

                // Update UI
                btnStartNFC.style.display = 'none';
                btnStopNFC.style.display = 'inline-block';
                nfcStatusBadge.textContent = 'Membaca...';
                nfcStatusBadge.className = 'badge bg-success p-2 fs-6';
                nfcAnimation.style.display = 'block';

                ndef.addEventListener('reading', handleReading);
                ndef.addEventListener('readingerror', () => {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Info',
                        text: 'Terjadi kesalahan saat membaca kartu. Silakan tempelkan ulang.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                });

            } catch (error) {
                console.error("NFC Error: ", error);
                let text = "Terjadi kesalahan saat mengaktifkan NFC.";
                if(error.name === 'NotAllowedError') {
                    text = "Izin NFC ditolak oleh pengguna.";
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: text
                });
            }
        }

        function stopScan() {
            if (ctrl) {
                ctrl.abort();
            }
            // Update UI
            btnStartNFC.style.display = 'inline-block';
            btnStopNFC.style.display = 'none';
            nfcStatusBadge.textContent = 'Belum aktif';
            nfcStatusBadge.className = 'badge bg-secondary p-2 fs-6';
            nfcAnimation.style.display = 'none';
        }

        async function handleReading({ message, serialNumber }) {
            try {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Silakan tunggu',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send to backend
                const response = await axios.post('/scan', {
                    serial: serialNumber
                }, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const res = response.data;

                // Update Result UI
                resultArea.style.display = 'block';
                document.getElementById('resNama').textContent = res.data ? res.data.nama : '-';
                document.getElementById('resNim').textContent = res.data ? res.data.nim : '-';
                document.getElementById('resSerial').textContent = res.data ? res.data.serial : serialNumber;

                const resStatus = document.getElementById('resStatus');
                if (res.status === 'success') {
                    resStatus.textContent = 'Hadir';
                    resStatus.className = 'badge bg-success';
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else if (res.status === 'error' && res.data) {
                    resStatus.textContent = 'Sudah Absen';
                    resStatus.className = 'badge bg-warning text-dark';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sudah Absen',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                console.error("Scan Process Error: ", error);

                resultArea.style.display = 'block';
                document.getElementById('resNama').textContent = '-';
                document.getElementById('resNim').textContent = '-';
                document.getElementById('resSerial').textContent = serialNumber;

                const resStatus = document.getElementById('resStatus');
                resStatus.className = 'badge bg-danger';

                if (error.response && error.response.status === 404) {
                    resStatus.textContent = 'Tidak Terdaftar';
                    Swal.fire({
                        icon: 'error',
                        title: 'Perhatian',
                        text: 'Kartu tidak terdaftar dalam sistem!'
                    });
                } else {
                    resStatus.textContent = 'Error';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan koneksi server'
                    });
                }
            }
        }
    });
</script>
@endpush
