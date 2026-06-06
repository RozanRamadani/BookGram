<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Find or create user
            $user = User::where('email', $googleUser->getEmail())
                ->orWhere('id_google', $googleUser->getId())
                ->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'id_google' => $googleUser->getId(),
                    'password' => bcrypt(Str::random(16)), // Random password
                ]);

            } else {
                // Update Google ID if not set
                if (!$user->id_google) {
                    $user->id_google = $googleUser->getId();
                    $user->save();
                }
            }

            // Skip OTP if it's the admin
            if ($user->email === 'admin@mail.com') {
                \Illuminate\Support\Facades\Auth::login($user);
                return redirect()->intended('/home');
            }

            // Generate OTP
            $otp = $this->generateOTP();
            $user->otp = $otp;
            $user->save();

            // Send OTP via email
            $this->sendOTPEmail($user, $otp);

            // Store user ID in session for OTP verification
            session(['otp_user_id' => $user->id]);

            return redirect()->route('otp.verify')->with('success', 'Kode OTP telah dikirim ke email Anda.');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan saat login dengan Google: ' . $e->getMessage());
        }
    }

    private function generateOTP()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function sendOTPEmail($user, $otp)
    {
        Mail::raw("Kode OTP Anda adalah: {$otp}\n\nKode ini berlaku untuk satu kali login.", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Kode OTP Login');
        });;
    }
}
