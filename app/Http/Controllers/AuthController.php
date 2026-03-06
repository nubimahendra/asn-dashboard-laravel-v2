<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // Rate limiting: max 5 attempts per minute per IP
        $key = 'login_attempts_' . $request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return back()->with('error', "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.");
        }

        if (Auth::attempt($credentials)) {
            \Illuminate\Support\Facades\RateLimiter::clear($key);
            $request->session()->regenerate();

            // Clear NIP to force re-verification for new chat session
            $request->user()->update(['nip' => null]);

            return redirect()->intended('/')->with('success', 'Login berhasil!');
        }

        \Illuminate\Support\Facades\RateLimiter::hit($key, 60); // 60 second decay

        return back()->with('error', 'Login gagal! Periksa email dan password.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Logout berhasil!');
    }
}
