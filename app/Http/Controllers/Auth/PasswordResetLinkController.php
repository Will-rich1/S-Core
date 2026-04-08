<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

class PasswordResetLinkController extends Controller
{
    /**
     * Show forgot-password form.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link to student email.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                Rule::exists('users', 'email')->where(function ($query) {
                    return $query->where('role', 'student');
                }),
            ],
        ], [
            'email.exists' => 'Email mahasiswa tidak ditemukan.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => __($status),
        ]);
    }
}
