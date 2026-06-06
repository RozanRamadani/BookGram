@extends('layouts.app')

@section('title', 'Edit Toko')

@push('custom-styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 400px; border-radius: 8px; border: 2px solid #e8ecf1; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Toko</h4>
                <p class="card-description">Ubah data toko <strong>{{ $toko->nama_toko }}</strong></p>

                <form action="{{ route('toko.update', $toko) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_toko">Nama Toko <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_toko') is-invalid @enderror"
                                       id="nama_toko" name="nama_toko"
                                       value="{{ old('nama_toko', $toko->nama_toko) }}" required>
                                @error('nama_toko')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode_barcode">Kode Barcode <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-barcode"></i></span>
                                    </div>
                                    <input type="text" class="form-control @error('kode_barcode') is-invalid @enderror"
                                           id="kode_barcode" name="kode_barcode"
                                           value="{{ old('kode_barcode', $toko->kode_barcode) }}" readonly>
                                    @error('kode_barcode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Kode barcode tidak dapat diubah</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror"
                                  id="alamat" name="alamat" rows="2" required>{{ old('alamat', $toko->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="latitude">Latitude <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror"
                                       id="latitude" name="latitude"
                                       value="{{ old('latitude', $toko->latitude) }}" required>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="longitude">Longitude <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror"
                                       id="longitude" name="longitude"
                                       value="{{ old('longitude', $toko->longitude) }}" required>
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="radius_meter">Radius Toleransi (meter) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('radius_meter') is-invalid @enderror"
                                       id="radius_meter" name="radius_meter"
                                       value="{{ old('radius_meter', $toko->radius_meter) }}"
                                       min="10" max="1000" required>
                                @error('radius_meter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Lokasi di Peta <small class="text-muted">(klik pada peta untuk ubah koordinat)</small></label>
                        <div id="map"></div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-icon-text mr-2">
                            <i class="mdi mdi-content-save btn-icon-prepend"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('toko.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tokoLat = {{ $toko->latitude }};
    const tokoLng = {{ $toko->longitude }};
    const tokoRadius = {{ $toko->radius_meter }};

    const map = L.map('map').setView([tokoLat, tokoLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;
    let circle = null;

    // Place initial marker
    placeMarker(tokoLat, tokoLng);

    // Click on map to update coordinates
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);

        placeMarker(lat, lng);
    });

    // Update circle when radius changes
    document.getElementById('radius_meter').addEventListener('input', function() {
        if (marker) {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            placeMarker(lat, lng);
        }
    });

    function placeMarker(lat, lng) {
        const radius = parseInt(document.getElementById('radius_meter').value) || 100;

        if (marker) {
            map.removeLayer(marker);
            map.removeLayer(circle);
        }

        marker = L.marker([lat, lng]).addTo(map)
            .bindPopup('{{ $toko->nama_toko }}').openPopup();

        circle = L.circle([lat, lng], {
            color: '#6c63ff',
            fillColor: '#6c63ff',
            fillOpacity: 0.15,
            radius: radius
        }).addTo(map);
    }
});
</script>
@endpush
