@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Kategori Buku</h4>
                <p class="card-description">Form untuk menambahkan kategori buku baru</p>

                <form action="{{ route('kategori.store') }}" method="POST" class="forms-sample">
                    @csrf

                    <div class="form-group">
                        <label for="nama">Nama Kategori</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                               id="nama" name="nama" value="{{ old('nama') }}"
                               placeholder="Masukkan nama kategori (contoh: Novel, Biografi, Komik)">
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary me-2">Simpan</button>
                    <a href="{{ route('kategori.index') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
