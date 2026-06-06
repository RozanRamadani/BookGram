<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OTPController extends Controller
{
    public function showVerifyForm()
    {
        // Check if user ID is in session
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        // Check if user ID is in session
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login')->with('error', 'Sesi telah berakhir. Silakan login kembali.');
        }

        $userId = session('otp_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        // Verify OTP
        if ($user->otp === $request->otp) {
            // Clear OTP
            $user->otp = null;
            $user->save();

            // Clear session
            session()->forget('otp_user_id');

            // Login user
            Auth::login($user);

            return redirect()->intended('/home')->with('success', 'Login berhasil!');
        } else {
            return back()->with('error', 'Kode OTP tidak valid.');
        }
    }
}
