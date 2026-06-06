@extends('layouts.app')

@section('title', 'Studi Kasus - Table HTML')

@push('custom-styles')
<style>
    .case-row-clickable {
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
                    <h4 class="card-title mb-0">Studi Kasus - Tabel HTML Biasa</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('studi-kasus.table-html') }}" class="btn btn-primary btn-sm">Table HTML</a>
                        <a href="{{ route('studi-kasus.table-datatables') }}" class="btn btn-outline-primary btn-sm">DataTables</a>
                        <a href="{{ route('studi-kasus.select-kota') }}" class="btn btn-outline-primary btn-sm">Select Kota</a>
                    </div>
                </div>

                <div class="case-muted-box mb-4">
                    Klik baris tabel untuk membuka modal Ubah/Hapus.
                </div>

                <form id="formTambahBarang" class="mb-4" novalidate>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="namaBarang" class="form-label">Nama barang</label>
                            <input type="text" id="namaBarang" class="form-control" maxlength="50" required>
                        </div>
                        <div class="col-md-5">
                            <label for="hargaBarang" class="form-label">Harga barang</label>
                            <input type="number" id="hargaBarang" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="button" id="btnTambah" class="btn btn-success">
                                <span class="label">Submit</span>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tableBarangHtml">
                        <thead>
                            <tr>
                                <th>ID barang</th>
                                <th>Nama</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyBarang"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah / Hapus Data Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditBarang" novalidate>
                    <input type="hidden" id="rowIndexAktif">

                    <div class="mb-3">
                        <label for="modalIdBarang" class="form-label">ID barang</label>
                        <input type="text" id="modalIdBarang" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="modalNamaBarang" class="form-label">Nama barang</label>
                        <input type="text" id="modalNamaBarang" class="form-control" maxlength="50" required>
                    </div>

                    <div class="mb-3">
                        <label for="modalHargaBarang" class="form-label">Harga barang</label>
                        <input type="number" id="modalHargaBarang" class="form-control" min="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" id="btnHapus" class="btn btn-danger">
                    <span class="label">Hapus</span>
                </button>
                <button type="button" id="btnUbah" class="btn btn-success">
                    <span class="label">Ubah</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-scripts')
<script>
    (function () {
        const formTambah = document.getElementById('formTambahBarang');
        const formEdit = document.getElementById('formEditBarang');
        const tbody = document.getElementById('tbodyBarang');

        const namaBarang = document.getElementById('namaBarang');
        const hargaBarang = document.getElementById('hargaBarang');
        const btnTambah = document.getElementById('btnTambah');

        const rowIndexAktif = document.getElementById('rowIndexAktif');
        const modalIdBarang = document.getElementById('modalIdBarang');
        const modalNamaBarang = document.getElementById('modalNamaBarang');
        const modalHargaBarang = document.getElementById('modalHargaBarang');
        const btnHapus = document.getElementById('btnHapus');
        const btnUbah = document.getElementById('btnUbah');

        const modalElement = document.getElementById('modalEditDelete');
        const modalInstance = new bootstrap.Modal(modalElement);

        let counter = 1;
        const stateRows = [];

        function formatRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        function buildId() {
            return 'BRG-' + String(counter++).padStart(4, '0');
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

        function renderTable() {
            tbody.innerHTML = '';

            stateRows.forEach(function (row, idx) {
                const tr = document.createElement('tr');
                tr.classList.add('case-row-clickable');
                tr.dataset.rowIndex = idx;
                tr.innerHTML = `
                    <td>${row.id}</td>
                    <td>${row.nama}</td>
                    <td>${formatRupiah(row.harga)}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        btnTambah.addEventListener('click', function () {
            if (!formTambah.reportValidity()) {
                return;
            }

            withFakeProcess(btnTambah, function () {
                stateRows.push({
                    id: buildId(),
                    nama: namaBarang.value.trim(),
                    harga: Number(hargaBarang.value)
                });

                formTambah.reset();
                renderTable();
            });
        });

        tbody.addEventListener('click', function (event) {
            const tr = event.target.closest('tr');
            if (!tr) {
                return;
            }

            const rowIndex = Number(tr.dataset.rowIndex);
            const row = stateRows[rowIndex];
            if (!row) {
                return;
            }

            rowIndexAktif.value = rowIndex;
            modalIdBarang.value = row.id;
            modalNamaBarang.value = row.nama;
            modalHargaBarang.value = row.harga;

            modalInstance.show();
        });

        btnUbah.addEventListener('click', function () {
            if (!formEdit.reportValidity()) {
                return;
            }

            withFakeProcess(btnUbah, function () {
                const idx = Number(rowIndexAktif.value);
                if (!Number.isInteger(idx) || !stateRows[idx]) {
                    return;
                }

                stateRows[idx].nama = modalNamaBarang.value.trim();
                stateRows[idx].harga = Number(modalHargaBarang.value);

                renderTable();
                modalInstance.hide();
            });
        });

        btnHapus.addEventListener('click', function () {
            withFakeProcess(btnHapus, function () {
                const idx = Number(rowIndexAktif.value);
                if (!Number.isInteger(idx) || !stateRows[idx]) {
                    return;
                }

                stateRows.splice(idx, 1);
                renderTable();
                modalInstance.hide();
            });
        });
    })();
</script>
@endpush
