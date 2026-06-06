<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Override the authenticated method to add OTP verification
     */
    protected function authenticated(Request $request, $user)
    {
        // Skip OTP for admin email
        if ($user->email === 'admin@mail.com') {
            return redirect()->intended($this->redirectPath());
        }

        // Generate OTP
        $otp = $this->generateOTP();
        $user->otp = $otp;
        $user->save();

        // Send OTP via email
        $this->sendOTPEmail($user, $otp);

        // Store OTP user ID in session FIRST
        $request->session()->put('otp_user_id', $user->id);

        // Remove auth from session without invalidating/regenerating session
        // (avoids CSRF token being destroyed which causes 419)
        $request->session()->forget('password_hash_web');
        $request->session()->forget('auth.password_confirmed_at');
        $request->session()->forget(Auth::guard()->getName());
        Auth::guard()->forgetUser();

        return redirect()->route('otp.verify')->with('success', 'Kode OTP telah dikirim ke email Anda.');
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
        });
    }
}
