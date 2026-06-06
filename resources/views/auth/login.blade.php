@extends('layouts.auth')

@section('title', 'Masuk - ' . config('app.name'))

@section('content')
<div>
    <h1 class="auth-heading">Selamat datang kembali</h1>
    <p class="auth-subheading">Masuk ke akun Anda untuk melanjutkan</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="mdi mdi-alert-circle-outline"></i>&nbsp;{{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-form-group">
            <label for="email">Email</label>
            <input id="email" type="email"
                class="form-control @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}"
                placeholder="nama@email.com"
                required autocomplete="email" autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-form-group">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <label for="password">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                @endif
            </div>
            <input id="password" type="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password"
                placeholder="••••••••"
                required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-row">
            <div class="form-check" style="margin:0;">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
        </div>

        <button type="submit" class="btn-auth">Masuk</button>
    </form>

    <div class="auth-divider">atau lanjutkan dengan</div>

    <a href="{{ route('auth.google') }}" class="btn-google">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="18" height="18">
            <path fill="#EA4335" d="M24 9.5c3.14 0 5.95 1.08 8.17 2.84l6.1-6.1C34.55 3.1 29.6 1 24 1 14.82 1 6.97 6.54 3.28 14.44l7.1 5.52C12.18 13.67 17.63 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.56 24.5c0-1.6-.14-3.13-.4-4.6H24v8.7h12.68c-.55 2.94-2.22 5.43-4.72 7.1l7.18 5.58C43.42 37.4 46.56 31.4 46.56 24.5z"/>
            <path fill="#FBBC05" d="M10.38 28.04A14.55 14.55 0 0 1 9.5 24c0-1.41.2-2.78.55-4.08l-7.1-5.52A22.98 22.98 0 0 0 1 24c0 3.73.9 7.26 2.48 10.4l6.9-6.36z"/>
            <path fill="#34A853" d="M24 47c6.48 0 11.93-2.14 15.9-5.82l-7.17-5.57C30.56 37.5 27.47 38.5 24 38.5c-6.37 0-11.82-4.17-13.62-9.96l-6.9 6.36C7.07 42.54 14.88 47 24 47z"/>
        </svg>
        Google
    </a>

    <p class="auth-footer-text">
        Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
    </p>
</div>
@endsection
