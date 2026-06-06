@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0">Data Mahasiswa</h5>
                    <a href="{{ route('mahasiswa.create') }}" class="btn btn-light btn-sm">Tambah Data</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Serial NFC</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mahasiswas as $mhs)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $mhs->nim }}</td>
                                    <td>{{ $mhs->nama }}</td>
                                    <td>
                                        @if($mhs->serial_nfc)
                                            <span class="badge bg-success">{{ $mhs->serial_nfc }}</span>
                                        @else
                                            <button class="btn btn-sm btn-warning btn-register-nfc" data-id="{{ $mhs->id }}" data-nama="{{ $mhs->nama }}">
                                                <i class="fas fa-wifi"></i> Daftar NFC
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('mahasiswa.edit', $mhs->id) }}" class="btn btn-sm btn-info text-white">Edit</a>
                                        <form action="{{ route('mahasiswa.destroy', $mhs->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Data mahasiswa tidak tersedia</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Scan NFC -->
<div class="modal fade" id="scanNfcModal" tabindex="-1" aria-labelledby="scanNfcModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="scanNfcModalLabel">Daftar Kartu NFC</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center py-4">
        <h6 id="modalMahasiswaName" class="mb-3"></h6>
        <input type="hidden" id="modalMahasiswaId">

        <div id="modalNfcStatus" class="mb-4">
            <span class="badge bg-secondary p-2">Siap memindai...</span>
        </div>

        <div id="modalNfcAnimation" style="display: none;">
            <div class="spinner-grow text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Silakan tempelkan kartu NFC ke bagian belakang HP Anda...</p>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnMulaiScanNfcModal">Mulai Scan</button>
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
        const scanModal = new bootstrap.Modal(document.getElementById('scanNfcModal'));
        const btnRegister = document.querySelectorAll('.btn-register-nfc');
        const btnMulaiScan = document.getElementById('btnMulaiScanNfcModal');
        const modalMahasiswaName = document.getElementById('modalMahasiswaName');
        const modalMahasiswaId = document.getElementById('modalMahasiswaId');
        const modalNfcStatus = document.getElementById('modalNfcStatus');
        const modalNfcAnimation = document.getElementById('modalNfcAnimation');

        let ndef = null;
        let ctrl = null;

        btnRegister.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');

                modalMahasiswaId.value = id;
                modalMahasiswaName.textContent = 'Mendaftarkan kartu untuk: ' + nama;

                resetModalUI();
                scanModal.show();
            });
        });

        document.getElementById('scanNfcModal').addEventListener('hidden.bs.modal', function () {
            if(ctrl) ctrl.abort();
        });

        btnMulaiScan.addEventListener('click', async function() {
            if (!('NDEFReader' in window)) {
                Swal.fire('Error', 'Browser tidak mendukung Web NFC', 'error');
                return;
            }

            try {
                ndef = new NDEFReader();
                ctrl = new AbortController();

                btnMulaiScan.disabled = true;
                btnMulaiScan.textContent = 'Membaca...';
                modalNfcAnimation.style.display = 'block';
                modalNfcStatus.innerHTML = '<span class="badge bg-warning text-dark p-2">Membaca Kartu NFC...</span>';

                await ndef.scan({ signal: ctrl.signal });

                ndef.addEventListener('reading', async ({ serialNumber }) => {
                    ctrl.abort(); // Stop scanning once read

                    try {
                        const mhsId = modalMahasiswaId.value;

                        Swal.fire({title: 'Menyimpan...', allowOutsideClick: false});
                        Swal.showLoading();

                        const res = await axios.post('/register-kartu', {
                            mahasiswa_id: mhsId,
                            serial_nfc: serialNumber
                        }, {
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });

                        scanModal.hide();
                        Swal.fire('Berhasil', res.data.message, 'success').then(() => {
                            window.location.reload();
                        });

                    } catch (err) {
                        let msg = 'Gagal menyimpan kartu.';
                        if(err.response && err.response.data && err.response.data.message) {
                            msg = err.response.data.message;
                        }
                        Swal.fire('Error', msg, 'error');
                        resetModalUI();
                    }
                }, { once: true });

            } catch (err) {
                Swal.fire('Error', 'Gagal memulai scanner NFC. Pastikan NFC aktif.', 'error');
                resetModalUI();
            }
        });

        function resetModalUI() {
            btnMulaiScan.disabled = false;
            btnMulaiScan.textContent = 'Mulai Scan';
            modalNfcAnimation.style.display = 'none';
            modalNfcStatus.innerHTML = '<span class="badge bg-secondary p-2">Siap memindai...</span>';
        }
    });
</script>
@endpush
