@extends('layouts.app')

@section('title', 'Edit Buku')

@section('content')
<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Buku</h4>
                <p class="card-description">Form untuk mengubah data buku</p>

                <form action="{{ route('buku.update', $buku->id) }}" method="POST" class="forms-sample">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="kategori_id">Kategori</label>
                        <select class="form-control @error('kategori_id') is-invalid @enderror"
                                id="kategori_id" name="kategori_id">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}"
                                    {{ old('kategori_id', $buku->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kode">Kode Buku</label>
                        <input type="text" class="form-control @error('kode') is-invalid @enderror"
                               id="kode" name="kode" value="{{ old('kode', $buku->kode) }}"
                               placeholder="Contoh: NV-01, BO-01, dst">
                        @error('kode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="judul">Judul Buku</label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror"
                               id="judul" name="judul" value="{{ old('judul', $buku->judul) }}"
                               placeholder="Masukkan judul buku">
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pengarang">Pengarang</label>
                        <input type="text" class="form-control @error('pengarang') is-invalid @enderror"
                               id="pengarang" name="pengarang" value="{{ old('pengarang', $buku->pengarang) }}"
                               placeholder="Masukkan nama pengarang">
                        @error('pengarang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary me-2">Update</button>
                    <a href="{{ route('buku.index') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
