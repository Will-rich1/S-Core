<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Untuk testing, kita hardcode user
        // Nanti bisa diganti dengan database
        $testEmail = 'admin1@itbss.ac.id';
        $testPassword = 'password';

        if ($request->email === $testEmail && $request->password === $testPassword) {
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => 1,
                        'name' => 'Manda Aprikasari',
                        'email' => $request->email,
                        'role' => 'student'
                    ],
                    'token' => 'dummy-token-' . time()
                ]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Incorrect email address or password'
        ], 401);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ], 200);
    }
}
