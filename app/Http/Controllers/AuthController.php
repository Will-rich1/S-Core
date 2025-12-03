<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // --- FUNGSI 1: MENAMPILKAN FORM LOGIN (INI YANG HILANG TADI) ---
    public function showLoginForm(Request $request)
    {
        $request->session()->regenerateToken();
        return view('login');
    }

    // --- FUNGSI 2: MEMPROSES LOGIN (DENGAN JEBAKAN DIAGNOSA) ---
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Cek apakah Auth Berhasil di Database?
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // --- FUNGSI 3: LOGOUT ---
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}