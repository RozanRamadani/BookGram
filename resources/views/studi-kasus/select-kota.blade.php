@extends('layouts.app')

@section('title', 'Studi Kasus - Select Kota')

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">Studi Kasus - Select Kota</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('studi-kasus.table-html') }}" class="btn btn-outline-primary btn-sm">Table HTML</a>
                <a href="{{ route('studi-kasus.table-datatables') }}" class="btn btn-outline-primary btn-sm">DataTables</a>
                <a href="{{ route('studi-kasus.select-kota') }}" class="btn btn-primary btn-sm">Select Kota</a>
            </div>
        </div>
    </div>

    <div class="col-lg-6 grid-margin">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="mb-3">Select 1</h5>

                <form id="formKota1" class="mb-3" novalidate>
                    <label for="inputKota1" class="form-label">Kota</label>
                    <div class="d-flex gap-2">
                        <input type="text" id="inputKota1" class="form-control" required>
                        <button type="button" id="btnTambahKota1" class="btn btn-success">
                            <span class="label">Tambahkan</span>
                        </button>
                    </div>
                </form>

                <div class="mb-3">
                    <label for="selectKota1" class="form-label">Select Kota</label>
                    <select id="selectKota1" class="form-select">
                        <option value="">-- pilih kota --</option>
                    </select>
                </div>

                <div class="alert alert-info mb-0">
                    Kota terpilih: <strong id="hasilKota1">-</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 grid-margin">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="mb-3">Select 2 (Dua Select)</h5>

                <form id="formKota2" class="mb-3" novalidate>
                    <label for="inputKota2" class="form-label">Kota</label>
                    <div class="d-flex gap-2">
                        <input type="text" id="inputKota2" class="form-control" required>
                        <button type="button" id="btnTambahKota2" class="btn btn-success">
                            <span class="label">Tambahkan</span>
                        </button>
                    </div>
                </form>

                <div class="mb-3">
                    <label for="selectKota2A" class="form-label">Select Kota A</label>
                    <select id="selectKota2A" class="form-select">
                        <option value="">-- pilih kota --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="selectKota2B" class="form-label">Select Kota B</label>
                    <select id="selectKota2B" class="form-select">
                        <option value="">-- pilih kota --</option>
                    </select>
                </div>

                <div class="alert alert-info mb-0">
                    Kota terpilih A: <strong id="hasilKota2A">-</strong><br>
                    Kota terpilih B: <strong id="hasilKota2B">-</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-scripts')
<script>
    (function () {
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
                label.textContent = label.dataset.original || 'Tambahkan';
            }
        }

        function withFakeProcess(button, callback) {
            setButtonLoading(button, true);
            setTimeout(function () {
                callback();
                setButtonLoading(button, false);
            }, 600);
        }

        const formKota1 = document.getElementById('formKota1');
        const inputKota1 = document.getElementById('inputKota1');
        const btnKota1 = document.getElementById('btnTambahKota1');
        const selectKota1 = document.getElementById('selectKota1');
        const hasilKota1 = document.getElementById('hasilKota1');

        btnKota1.addEventListener('click', function () {
            if (!formKota1.reportValidity()) {
                return;
            }

            withFakeProcess(btnKota1, function () {
                const kota = inputKota1.value.trim();
                const option = new Option(kota, kota);
                selectKota1.add(option);
                inputKota1.value = '';
                inputKota1.focus();
            });
        });

        selectKota1.addEventListener('change', function () {
            hasilKota1.textContent = selectKota1.value || '-';
        });

        const formKota2 = document.getElementById('formKota2');
        const inputKota2 = document.getElementById('inputKota2');
        const btnKota2 = document.getElementById('btnTambahKota2');
        const selectKota2A = document.getElementById('selectKota2A');
        const selectKota2B = document.getElementById('selectKota2B');
        const hasilKota2A = document.getElementById('hasilKota2A');
        const hasilKota2B = document.getElementById('hasilKota2B');

        btnKota2.addEventListener('click', function () {
            if (!formKota2.reportValidity()) {
                return;
            }

            withFakeProcess(btnKota2, function () {
                const kota = inputKota2.value.trim();
                selectKota2A.add(new Option(kota, kota));
                selectKota2B.add(new Option(kota, kota));
                inputKota2.value = '';
                inputKota2.focus();
            });
        });

        selectKota2A.addEventListener('change', function () {
            hasilKota2A.textContent = selectKota2A.value || '-';
        });

        selectKota2B.addEventListener('change', function () {
            hasilKota2B.textContent = selectKota2B.value || '-';
        });
    })();
</script>
@endpush
