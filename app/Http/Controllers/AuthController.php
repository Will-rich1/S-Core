<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Tambahkan ini untuk hashing password

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // Jika user sudah login, langsung lempar ke dashboard masing-masing
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect sesuai role
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Jika request dari AJAX/fetch, kembalikan JSON response
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil',
                'redirect' => '/login'
            ], 200);
        }
        
        return redirect('/login');
    }

    // --- TAMBAHAN: FITUR GANTI PASSWORD ---
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password', // Validasi password lama
            'new_password' => 'required|min:8|confirmed',      // Validasi password baru
        ]);

        // Update password di database
        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}