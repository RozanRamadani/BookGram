@extends('layouts.auth')

@section('title', 'Daftar - ' . config('app.name'))

@section('content')
<div>
    <h1 class="auth-heading">Buat akun baru</h1>
    <p class="auth-subheading">Isi formulir di bawah untuk mendaftar</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="mdi mdi-alert-circle-outline"></i>&nbsp;{{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-form-group">
            <label for="name">Nama Lengkap</label>
            <input id="name" type="text"
                class="form-control @error('name') is-invalid @enderror"
                name="name" value="{{ old('name') }}"
                placeholder="Nama lengkap Anda"
                required autocomplete="name" autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-form-group">
            <label for="email">Email</label>
            <input id="email" type="email"
                class="form-control @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}"
                placeholder="nama@email.com"
                required autocomplete="email">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-form-group">
            <label for="password">Password</label>
            <input id="password" type="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password"
                placeholder="Minimal 8 karakter"
                required autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-form-group">
            <label for="password-confirm">Konfirmasi Password</label>
            <input id="password-confirm" type="password"
                class="form-control"
                name="password_confirmation"
                placeholder="Ulangi password"
                required autocomplete="new-password">
        </div>

        <button type="submit" class="btn-auth" style="margin-top:0.5rem;">Buat Akun</button>
    </form>

    <p class="auth-footer-text">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </p>
</div>
@endsection
