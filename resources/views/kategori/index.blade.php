@extends('layouts.app')

@section('title', 'Data Kategori')

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Data Kategori</h4>
                    <a href="{{ route('kategori.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Tambah Kategori
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Kategori</th>
                                <th width="100" class="text-center">Jumlah Buku</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kategoris as $key => $kategori)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $kategori->nama }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $kategori->bukus_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn btn-warning btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data kategori</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
