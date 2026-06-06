@extends('layouts.app')

@section('title', 'Studi Kasus - DataTables')

@push('plugin-styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('custom-styles')
<style>
    #tableBarangDatatables tbody tr {
        cursor: pointer;
    }

    .case-muted-box {
        background: #f8f9fa;
        border: 1px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Studi Kasus - DataTables + jQuery</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('studi-kasus.table-html') }}" class="btn btn-outline-primary btn-sm">Table HTML</a>
                        <a href="{{ route('studi-kasus.table-datatables') }}" class="btn btn-primary btn-sm">DataTables</a>
                        <a href="{{ route('studi-kasus.select-kota') }}" class="btn btn-outline-primary btn-sm">Select Kota</a>
                    </div>
                </div>

                <div class="case-muted-box mb-4">
                    Klik baris tabel untuk membuka modal Ubah/Hapus.
                </div>

                <form id="formTambahBarangDt" class="mb-4" novalidate>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="namaBarangDt" class="form-label">Nama barang</label>
                            <input type="text" id="namaBarangDt" class="form-control" maxlength="50" required>
                        </div>
                        <div class="col-md-5">
                            <label for="hargaBarangDt" class="form-label">Harga barang</label>
                            <input type="number" id="hargaBarangDt" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="button" id="btnTambahDt" class="btn btn-success">
                                <span class="label">Submit</span>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tableBarangDatatables" width="100%">
                        <thead>
                            <tr>
                                <th>ID barang</th>
                                <th>Nama</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditDeleteDt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah / Hapus Data Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditBarangDt" novalidate>
                    <div class="mb-3">
                        <label for="modalIdBarangDt" class="form-label">ID barang</label>
                        <input type="text" id="modalIdBarangDt" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="modalNamaBarangDt" class="form-label">Nama barang</label>
                        <input type="text" id="modalNamaBarangDt" class="form-control" maxlength="50" required>
                    </div>

                    <div class="mb-3">
                        <label for="modalHargaBarangDt" class="form-label">Harga barang</label>
                        <input type="number" id="modalHargaBarangDt" class="form-control" min="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" id="btnHapusDt" class="btn btn-danger">
                    <span class="label">Hapus</span>
                </button>
                <button type="button" id="btnUbahDt" class="btn btn-success">
                    <span class="label">Ubah</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('plugin-scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('custom-scripts')
<script>
    $(function () {
        const formTambah = document.getElementById('formTambahBarangDt');
        const formEdit = document.getElementById('formEditBarangDt');

        const namaInput = $('#namaBarangDt');
        const hargaInput = $('#hargaBarangDt');

        const btnTambah = document.getElementById('btnTambahDt');
        const btnUbah = document.getElementById('btnUbahDt');
        const btnHapus = document.getElementById('btnHapusDt');

        const modalId = document.getElementById('modalIdBarangDt');
        const modalNama = document.getElementById('modalNamaBarangDt');
        const modalHarga = document.getElementById('modalHargaBarangDt');

        const modalInstance = new bootstrap.Modal(document.getElementById('modalEditDeleteDt'));

        let currentRow = null;
        let counter = 1;

        function buildId() {
            return 'BRG-' + String(counter++).padStart(4, '0');
        }

        function rupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        function setButtonLoading(button, loading) {
            const label = button.querySelector('.label');
            if (!label) {
                return;
            }

            if (loading) {
                button.disabled = true;
                label.dataset.original = label.textContent;
                label.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
            } else {
                button.disabled = false;
                label.textContent = label.dataset.original || 'Submit';
            }
        }

        function withFakeProcess(button, callback) {
            setButtonLoading(button, true);
            setTimeout(function () {
                callback();
                setButtonLoading(button, false);
            }, 600);
        }

        const table = $('#tableBarangDatatables').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });

        btnTambah.addEventListener('click', function () {
            if (!formTambah.reportValidity()) {
                return;
            }

            withFakeProcess(btnTambah, function () {
                table.row.add([
                    buildId(),
                    namaInput.val().trim(),
                    rupiah(hargaInput.val())
                ]).draw(false);

                formTambah.reset();
            });
        });

        $('#tableBarangDatatables tbody').on('click', 'tr', function () {
            currentRow = table.row(this);
            const data = currentRow.data();
            if (!data) {
                return;
            }

            modalId.value = data[0];
            modalNama.value = data[1];
            modalHarga.value = data[2].replace(/[^\d]/g, '');

            modalInstance.show();
        });

        btnUbah.addEventListener('click', function () {
            if (!formEdit.reportValidity()) {
                return;
            }

            withFakeProcess(btnUbah, function () {
                if (!currentRow) {
                    return;
                }

                currentRow.data([
                    modalId.value,
                    modalNama.value.trim(),
                    rupiah(modalHarga.value)
                ]).draw(false);

                modalInstance.hide();
            });
        });

        btnHapus.addEventListener('click', function () {
            withFakeProcess(btnHapus, function () {
                if (!currentRow) {
                    return;
                }

                currentRow.remove().draw(false);
                modalInstance.hide();
            });
        });
    });
</script>
@endpush
