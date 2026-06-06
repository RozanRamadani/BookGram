<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Purple Admin'))</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f5f7;
            margin: 0;
        }

        .auth-page {
            min-height: 100vh;
            display: flex;
        }

        /* Left decorative panel */
        .auth-side {
            display: none;
            width: 420px;
            flex-shrink: 0;
            background: #7B61FF;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(255,255,255,0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255,255,255,0.06) 0%, transparent 50%);
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 992px) {
            .auth-side { display: flex; flex-direction: column; justify-content: center; padding: 3rem; }
        }

        .auth-side-inner {
            color: #fff;
            position: relative;
            z-index: 2;
        }

        .auth-side-icon {
            width: 52px;
            height: 52px;
            background: rgba(255,255,255,0.15);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .auth-side-icon i { font-size: 1.6rem; color: #fff; }

        .auth-side h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .auth-side p {
            font-size: 0.9rem;
            opacity: 0.75;
            line-height: 1.7;
            margin: 0;
        }

        .auth-side-dots {
            position: absolute;
            bottom: 2.5rem;
            left: 3rem;
            display: flex;
            gap: 0.4rem;
        }

        .auth-side-dots span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,0.35);
        }

        .auth-side-dots span.active {
            background: #fff;
            width: 20px;
            border-radius: 3px;
        }

        /* Right content */
        .auth-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
        }

        .auth-box {
            width: 100%;
            max-width: 400px;
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 2.25rem;
        }

        .auth-brand-icon {
            width: 36px;
            height: 36px;
            background: #7B61FF;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-brand-icon i { font-size: 1.1rem; color: #fff; }

        .auth-brand-name {
            font-size: 1rem;
            font-weight: 700;
            color: #1a1a2e;
            letter-spacing: -0.3px;
        }

        .auth-heading {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.35rem;
            letter-spacing: -0.4px;
        }

        .auth-subheading {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 1.75rem;
        }

        .auth-form-group {
            margin-bottom: 1rem;
        }

        .auth-form-group label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.4rem;
        }

        .auth-form-group .form-control {
            width: 100%;
            height: 42px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            padding: 0 0.875rem;
            font-size: 0.875rem;
            color: #111827;
            font-family: 'Inter', sans-serif;
            background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }

        .auth-form-group .form-control::placeholder { color: #9ca3af; }

        .auth-form-group .form-control:focus {
            border-color: #7B61FF;
            box-shadow: 0 0 0 3px rgba(123, 97, 255, 0.12);
        }

        .auth-form-group .form-control.is-invalid {
            border-color: #ef4444;
        }

        .invalid-feedback {
            font-size: 0.78rem;
            color: #ef4444;
            margin-top: 0.3rem;
        }

        .auth-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .form-check-label {
            font-size: 0.8125rem;
            color: #374151;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #7B61FF;
            border-color: #7B61FF;
        }

        .forgot-link {
            font-size: 0.8rem;
            color: #7B61FF;
            font-weight: 500;
            text-decoration: none;
        }
        .forgot-link:hover { text-decoration: underline; }

        .btn-auth {
            display: block;
            width: 100%;
            height: 42px;
            background: #7B61FF;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            letter-spacing: 0.1px;
            transition: background 0.15s, transform 0.1s;
        }
        .btn-auth:hover { background: #6a50ee; color: #fff; transform: translateY(-1px); }
        .btn-auth:active { transform: translateY(0); }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.1rem 0;
            color: #9ca3af;
            font-size: 0.78rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .auth-divider::before, .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            width: 100%;
            height: 42px;
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s, border-color 0.15s;
        }
        .btn-google:hover { background: #f9fafb; border-color: #d1d5db; color: #111827; }

        .auth-footer-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.8125rem;
            color: #6b7280;
        }
        .auth-footer-text a {
            color: #7B61FF;
            font-weight: 600;
            text-decoration: none;
        }
        .auth-footer-text a:hover { text-decoration: underline; }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            color: #b91c1c;
            font-size: 0.8125rem;
            padding: 0.65rem 0.875rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="auth-page">
        <!-- Left panel -->
        <div class="auth-side">
            <div class="auth-side-inner">
                <div class="auth-side-icon">
                    <i class="mdi mdi-book-open-variant"></i>
                </div>
                <h2>Kelola Koleksi Buku dengan Mudah</h2>
                <p>Sistem manajemen perpustakaan yang modern, cepat, dan mudah digunakan untuk mengelola seluruh koleksi buku Anda.</p>
            </div>
            <div class="auth-side-dots">
                <span class="active"></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <!-- Right main -->
        <div class="auth-main">
            <div class="auth-box">
                <div class="auth-brand">
                    <div class="auth-brand-icon">
                        <i class="mdi mdi-book-open-variant"></i>
                    </div>
                    <span class="auth-brand-name">{{ config('app.name', 'Purple Admin') }}</span>
                </div>

                @yield('content')

                <p class="auth-footer-text" style="margin-top:2rem; color:#9ca3af; font-size:0.75rem;">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    @stack('custom-scripts')
</body>
</html>
