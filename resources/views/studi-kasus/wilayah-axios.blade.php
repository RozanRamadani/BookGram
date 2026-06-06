@extends('layouts.app')

@section('title', 'Studi Kasus Wilayah - Axios')

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">AJAX Wilayah (Axios)</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('studi-kasus.wilayah-jquery') }}" class="btn btn-outline-primary btn-sm">jQuery</a>
                        <a href="{{ route('studi-kasus.wilayah-axios') }}" class="btn btn-primary btn-sm">Axios</a>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Provinsi</label>
                        <select id="provinsi" class="form-select">
                            <option value="">Pilih Provinsi</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kota</label>
                        <select id="kota" class="form-select" disabled>
                            <option value="">Pilih Kota</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kecamatan</label>
                        <select id="kecamatan" class="form-select" disabled>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kelurahan</label>
                        <select id="kelurahan" class="form-select" disabled>
                            <option value="">Pilih Kelurahan</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    (function () {
        const provinsi = document.getElementById('provinsi');
        const kota = document.getElementById('kota');
        const kecamatan = document.getElementById('kecamatan');
        const kelurahan = document.getElementById('kelurahan');

        function setOptions(el, placeholder, rows) {
            el.innerHTML = '';
            const placeholderOpt = document.createElement('option');
            placeholderOpt.value = '';
            placeholderOpt.textContent = placeholder;
            el.appendChild(placeholderOpt);

            rows.forEach(function (row) {
                const opt = document.createElement('option');
                opt.value = row.id;
                opt.textContent = row.nama;
                el.appendChild(opt);
            });
        }

        function resetKota() {
            setOptions(kota, 'Pilih Kota', []);
            kota.disabled = true;
        }

        function resetKecamatan() {
            setOptions(kecamatan, 'Pilih Kecamatan', []);
            kecamatan.disabled = true;
        }

        function resetKelurahan() {
            setOptions(kelurahan, 'Pilih Kelurahan', []);
            kelurahan.disabled = true;
        }

        axios.get('{{ route('studi-kasus.api.provinsi') }}').then(function (res) {
            setOptions(provinsi, 'Pilih Provinsi', res.data.data || []);
        });

        provinsi.addEventListener('change', function () {
            resetKota();
            resetKecamatan();
            resetKelurahan();

            if (!provinsi.value) {
                return;
            }

            axios.get('/studi-kasus/api/wilayah/kota/' + provinsi.value).then(function (res) {
                setOptions(kota, 'Pilih Kota', res.data.data || []);
                kota.disabled = false;
            });
        });

        kota.addEventListener('change', function () {
            resetKecamatan();
            resetKelurahan();

            if (!kota.value) {
                return;
            }

            axios.get('/studi-kasus/api/wilayah/kecamatan/' + kota.value).then(function (res) {
                setOptions(kecamatan, 'Pilih Kecamatan', res.data.data || []);
                kecamatan.disabled = false;
            });
        });

        kecamatan.addEventListener('change', function () {
            resetKelurahan();

            if (!kecamatan.value) {
                return;
            }

            axios.get('/studi-kasus/api/wilayah/kelurahan/' + kecamatan.value).then(function (res) {
                setOptions(kelurahan, 'Pilih Kelurahan', res.data.data || []);
                kelurahan.disabled = false;
            });
        });
    })();
</script>
@endpush
