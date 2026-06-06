<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * Fitur Login Implementation:
     * - Check user authentication status menggunakan session
     * - Redirect ke login page jika belum terautentikasi
     * - Menyimpan user data di session setelah login berhasil
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check jika user belum login (session tidak ada)
        if (!Auth::check()) {
            // Simpan URL yang diminta untuk redirect setelah login
            session(['url.intended' => $request->url()]);

            // Redirect ke halaman login
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Simpan informasi user di session untuk akses cepat
        // Session otomatis dihandle oleh Laravel Auth
        session([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'user_email' => Auth::user()->email,
        ]);

        return $next($request);
    }
}
