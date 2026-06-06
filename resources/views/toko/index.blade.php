@extends('layouts.app')

@section('title', 'Data Toko')

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-0">Data Toko</h4>
                        <p class="card-description mt-1">Kelola data toko yang harus dikunjungi sales</p>
                    </div>
                    <a href="{{ route('toko.create') }}" class="btn btn-primary btn-icon-text">
                        <i class="mdi mdi-plus-circle btn-icon-prepend"></i> Tambah Toko
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="tokoTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Toko</th>
                                <th>Alamat</th>
                                <th>Koordinat</th>
                                <th>Kode Barcode</th>
                                <th>Radius</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokos as $index => $toko)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $toko->nama_toko }}</strong></td>
                                    <td>{{ Str::limit($toko->alamat, 40) }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $toko->latitude }}, {{ $toko->longitude }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-dark" style="font-size: 0.85em;">
                                            <i class="mdi mdi-barcode"></i> {{ $toko->kode_barcode }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $toko->radius_meter }}m</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info btn-icon-text btn-qrcode"
                                                data-id="{{ $toko->id }}"
                                                data-url="{{ route('toko.qrcode', $toko) }}"
                                                data-pdf-url="{{ route('toko.qrcode.download-pdf', $toko) }}">
                                            <i class="mdi mdi-qrcode btn-icon-prepend"></i> QR Code
                                        </button>
                                        <a href="{{ route('toko.edit', $toko) }}" class="btn btn-sm btn-warning btn-icon-text">
                                            <i class="mdi mdi-pencil btn-icon-prepend"></i> Edit
                                        </a>
                                        <form action="{{ route('toko.destroy', $toko) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus toko ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger btn-icon-text">
                                                <i class="mdi mdi-delete btn-icon-prepend"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="mdi mdi-store-off" style="font-size: 2rem;"></i>
                                        <p class="mt-2">Belum ada data toko. Klik "Tambah Toko" untuk memulai.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal QR Code --}}
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                <h5 class="modal-title text-white" id="qrCodeModalLabel">
                    <i class="mdi mdi-qrcode"></i> QR Code Toko
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                {{-- Loading spinner --}}
                <div id="qrLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2">Generating QR Code...</p>
                </div>

                {{-- QR Content (hidden until loaded) --}}
                <div id="qrContent" class="d-none">
                    <h5 class="mb-1" id="modalNamaToko" style="font-weight: 700; color: #333;"></h5>
                    <p class="text-muted mb-3" id="modalAlamat" style="font-size: 0.85em;"></p>

                    <div id="qrCodeContainer" class="d-inline-block p-3 mb-3"
                         style="background: #fff; border: 2px solid #e8ecf1; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                        {{-- SVG QR Code will be inserted here --}}
                    </div>

                    <div class="mb-3">
                        <span class="badge badge-dark px-3 py-2" style="font-size: 1em; letter-spacing: 1px;">
                            <i class="mdi mdi-barcode"></i>
                            <span id="modalKodeBarcode"></span>
                        </span>
                    </div>

                    {{-- Download Buttons --}}
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-success btn-icon-text" id="downloadPngBtn">
                            <i class="mdi mdi-download btn-icon-prepend"></i> Download PNG
                        </button>
                        <a href="#" class="btn btn-danger btn-icon-text" id="downloadPdfBtn" target="_blank">
                            <i class="mdi mdi-file-pdf btn-icon-prepend"></i> Download PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Hidden canvas for PNG conversion --}}
<canvas id="qrCanvas" style="display: none;"></canvas>
@endsection

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qrModal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
    const qrLoading    = document.getElementById('qrLoading');
    const qrContent    = document.getElementById('qrContent');
    const qrContainer  = document.getElementById('qrCodeContainer');
    const modalNama    = document.getElementById('modalNamaToko');
    const modalAlamat  = document.getElementById('modalAlamat');
    const modalKode    = document.getElementById('modalKodeBarcode');
    const downloadPng  = document.getElementById('downloadPngBtn');
    const downloadPdf  = document.getElementById('downloadPdfBtn');
    const canvas       = document.getElementById('qrCanvas');

    let currentPdfUrl = '';
    let currentNamaToko = '';

    // Attach click event to all QR Code buttons
    document.querySelectorAll('.btn-qrcode').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const ajaxUrl = this.dataset.url;
            const pdfUrl  = this.dataset.pdfUrl;

            // Reset modal state
            qrLoading.classList.remove('d-none');
            qrContent.classList.add('d-none');
            qrContainer.innerHTML = '';

            currentPdfUrl = pdfUrl;
            downloadPdf.href = pdfUrl;

            // Show modal
            qrModal.show();

            // Fetch QR Code via AJAX
            fetch(ajaxUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    modalNama.textContent   = data.nama_toko;
                    modalAlamat.textContent = data.alamat;
                    modalKode.textContent   = data.kode_barcode;
                    currentNamaToko         = data.nama_toko;

                    // Insert SVG QR Code
                    qrContainer.innerHTML = data.qrcode_svg;

                    // Style the SVG
                    const svgEl = qrContainer.querySelector('svg');
                    if (svgEl) {
                        svgEl.setAttribute('width', '250');
                        svgEl.setAttribute('height', '250');
                    }

                    qrLoading.classList.add('d-none');
                    qrContent.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error loading QR Code:', error);
                qrLoading.innerHTML = '<p class="text-danger"><i class="mdi mdi-alert-circle"></i> Gagal memuat QR Code</p>';
            });
        });
    });

    // Download PNG
    downloadPng.addEventListener('click', function() {
        const svgEl = qrContainer.querySelector('svg');
        if (!svgEl) return;

        const svgData = new XMLSerializer().serializeToString(svgEl);
        const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);

        const img = new Image();
        img.onload = function() {
            canvas.width  = 300;
            canvas.height = 300;
            const ctx = canvas.getContext('2d');

            // White background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Center QR Code
            const offsetX = (canvas.width - 250) / 2;
            const offsetY = (canvas.height - 250) / 2;
            ctx.drawImage(img, offsetX, offsetY, 250, 250);

            // Download
            const pngUrl = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = 'QRCode_' + currentNamaToko.replace(/\s+/g, '_') + '.png';
            link.href = pngUrl;
            link.click();

            URL.revokeObjectURL(url);
        };
        img.src = url;
    });
});
</script>
@endpush
