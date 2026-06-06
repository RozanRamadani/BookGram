@extends('layouts.app')

@section('title', 'Tambah Toko')

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
                <h4 class="card-title">Tambah Toko Baru</h4>
                <p class="card-description">Isi data toko dan pilih lokasi pada peta</p>

                <form action="{{ route('toko.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_toko">Nama Toko <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_toko') is-invalid @enderror"
                                       id="nama_toko" name="nama_toko" value="{{ old('nama_toko') }}"
                                       placeholder="Contoh: Toko Makmur Jaya" required>
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
                                           value="{{ old('kode_barcode', $kodeBarcode) }}" readonly>
                                    @error('kode_barcode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Kode otomatis di-generate oleh sistem</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror"
                                  id="alamat" name="alamat" rows="2"
                                  placeholder="Jl. Contoh No. 123, Kota ..." required>{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="latitude">Latitude <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror"
                                       id="latitude" name="latitude" value="{{ old('latitude') }}"
                                       placeholder="-6.xxxxxxxx" required>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="longitude">Longitude <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror"
                                       id="longitude" name="longitude" value="{{ old('longitude') }}"
                                       placeholder="106.xxxxxxxx" required>
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="radius_meter">Radius Toleransi (meter) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('radius_meter') is-invalid @enderror"
                                       id="radius_meter" name="radius_meter" value="{{ old('radius_meter', 100) }}"
                                       min="10" max="1000" required>
                                @error('radius_meter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Pilih Lokasi di Peta <small class="text-muted">(klik pada peta untuk set koordinat)</small></label>
                        <div id="map"></div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-icon-text mr-2">
                            <i class="mdi mdi-content-save btn-icon-prepend"></i> Simpan
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
    // Default center: Jakarta
    const defaultLat = {{ old('latitude', '-6.2088') }};
    const defaultLng = {{ old('longitude', '106.8456') }};

    const map = L.map('map').setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;
    let circle = null;

    // If old values exist, place marker
    const oldLat = document.getElementById('latitude').value;
    const oldLng = document.getElementById('longitude').value;
    if (oldLat && oldLng) {
        placeMarker(parseFloat(oldLat), parseFloat(oldLng));
        map.setView([parseFloat(oldLat), parseFloat(oldLng)], 16);
    }

    // Click on map to set coordinates
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
            .bindPopup('Lokasi Toko').openPopup();

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
