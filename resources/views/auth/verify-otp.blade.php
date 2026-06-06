@extends('layouts.auth')

@section('title', 'Verifikasi OTP - ' . config('app.name'))

@section('content')
<div>
    {{-- Icon --}}
    <div style="width:52px; height:52px; background:#f0edff; border-radius:14px; display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
        <i class="mdi mdi-shield-key-outline" style="font-size:1.6rem; color:#7B61FF;"></i>
    </div>

    <h1 class="auth-heading">Verifikasi OTP</h1>
    <p class="auth-subheading">Kode 6 digit telah dikirim ke email Anda. Masukkan kode tersebut di bawah ini.</p>

    @if (session('success'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; color:#15803d; font-size:0.8125rem; padding:0.65rem 0.875rem; margin-bottom:1rem;">
            <i class="mdi mdi-check-circle-outline"></i>&nbsp;{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="mdi mdi-alert-circle-outline"></i>&nbsp;{{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.verify.submit') }}">
        @csrf

        <div class="auth-form-group">
            <label for="otp" style="display:block; text-align:center; margin-bottom:0.75rem;">Kode OTP</label>

            {{-- 6-box OTP input --}}
            <div id="otp-boxes" style="display:flex; gap:0.5rem; justify-content:center; margin-bottom:0.5rem;">
                @for ($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" inputmode="numeric" pattern="\d"
                    class="otp-box"
                    style="width:46px; height:52px; border:1.5px solid #e5e7eb; border-radius:10px; text-align:center; font-size:1.375rem; font-weight:700; color:#111827; font-family:'Inter',sans-serif; outline:none; transition:border-color 0.15s, box-shadow 0.15s; background:#fff;"
                    autocomplete="off">
                @endfor
            </div>

            <input type="hidden" id="otp" name="otp">

            @error('otp')
                <div class="invalid-feedback" style="display:block; text-align:center;">{{ $message }}</div>
            @enderror

            <p style="text-align:center; font-size:0.78rem; color:#9ca3af; margin-top:0.5rem;">
                Masukkan 6 digit kode yang dikirim ke email Anda
            </p>
        </div>

        <button type="submit" class="btn-auth">Verifikasi</button>
    </form>

    <p class="auth-footer-text">
        <a href="{{ route('login') }}" style="color:#6b7280; font-weight:400;">
            <i class="mdi mdi-arrow-left" style="font-size:0.8rem;"></i> Kembali ke Login
        </a>
    </p>
</div>

@push('custom-scripts')
<script>
    const boxes = document.querySelectorAll('.otp-box');
    const hiddenInput = document.getElementById('otp');

    boxes.forEach((box, idx) => {
        // Focus style
        box.addEventListener('focus', () => {
            box.style.borderColor = '#7B61FF';
            box.style.boxShadow = '0 0 0 3px rgba(123,97,255,0.12)';
            box.select();
        });
        box.addEventListener('blur', () => {
            box.style.borderColor = box.value ? '#7B61FF' : '#e5e7eb';
            box.style.boxShadow = 'none';
        });

        box.addEventListener('input', function () {
            const val = this.value.replace(/\D/g, '');
            this.value = val ? val[0] : '';
            syncHidden();
            if (val && idx < boxes.length - 1) boxes[idx + 1].focus();
        });

        box.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && idx > 0) {
                boxes[idx - 1].value = '';
                syncHidden();
                boxes[idx - 1].focus();
            }
        });

        box.addEventListener('paste', function (e) {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            text.split('').slice(0, 6).forEach((char, i) => {
                if (boxes[i]) boxes[i].value = char;
            });
            syncHidden();
            const next = Math.min(text.length, 5);
            boxes[next].focus();
        });
    });

    function syncHidden() {
        hiddenInput.value = Array.from(boxes).map(b => b.value).join('');
    }

    // Auto-submit when all 6 filled
    boxes.forEach(box => {
        box.addEventListener('input', () => {
            if (Array.from(boxes).every(b => b.value)) {
                syncHidden();
                box.closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection
