@extends('layouts.app')

@section('title', 'Studi Kasus Wilayah - jQuery Ajax')

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">AJAX Wilayah (jQuery)</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('studi-kasus.wilayah-jquery') }}" class="btn btn-primary btn-sm">jQuery</a>
                        <a href="{{ route('studi-kasus.wilayah-axios') }}" class="btn btn-outline-primary btn-sm">Axios</a>
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
<script>
    $(function () {
        const $provinsi = $('#provinsi');
        const $kota = $('#kota');
        const $kecamatan = $('#kecamatan');
        const $kelurahan = $('#kelurahan');

        function setOptions($el, placeholder, rows) {
            $el.empty().append(`<option value="">${placeholder}</option>`);
            rows.forEach(function (row) {
                $el.append(`<option value="${row.id}">${row.nama}</option>`);
            });
        }

        function resetKota() {
            setOptions($kota, 'Pilih Kota', []);
            $kota.prop('disabled', true);
        }

        function resetKecamatan() {
            setOptions($kecamatan, 'Pilih Kecamatan', []);
            $kecamatan.prop('disabled', true);
        }

        function resetKelurahan() {
            setOptions($kelurahan, 'Pilih Kelurahan', []);
            $kelurahan.prop('disabled', true);
        }

        function loadProvinsi() {
            $.ajax({
                url: '{{ route('studi-kasus.api.provinsi') }}',
                type: 'GET',
                success: function (res) {
                    setOptions($provinsi, 'Pilih Provinsi', res.data || []);
                }
            });
        }

        loadProvinsi();

        $provinsi.on('change', function () {
            const id = $(this).val();
            resetKota();
            resetKecamatan();
            resetKelurahan();

            if (!id) {
                return;
            }

            $.ajax({
                url: '/studi-kasus/api/wilayah/kota/' + id,
                type: 'GET',
                success: function (res) {
                    setOptions($kota, 'Pilih Kota', res.data || []);
                    $kota.prop('disabled', false);
                }
            });
        });

        $kota.on('change', function () {
            const id = $(this).val();
            resetKecamatan();
            resetKelurahan();

            if (!id) {
                return;
            }

            $.ajax({
                url: '/studi-kasus/api/wilayah/kecamatan/' + id,
                type: 'GET',
                success: function (res) {
                    setOptions($kecamatan, 'Pilih Kecamatan', res.data || []);
                    $kecamatan.prop('disabled', false);
                }
            });
        });

        $kecamatan.on('change', function () {
            const id = $(this).val();
            resetKelurahan();

            if (!id) {
                return;
            }

            $.ajax({
                url: '/studi-kasus/api/wilayah/kelurahan/' + id,
                type: 'GET',
                success: function (res) {
                    setOptions($kelurahan, 'Pilih Kelurahan', res.data || []);
                    $kelurahan.prop('disabled', false);
                }
            });
        });
    });
</script>
@endpush
