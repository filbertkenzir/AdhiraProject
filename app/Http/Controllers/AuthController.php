<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $credentials['login'];
        $password = $credentials['password'];

        // Determine if input is email or username
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$fieldType => $loginInput, 'password' => $password])) {
            $request->session()->regenerate();
            $user = Auth::user();

            $redirectUrl = $user->role === 'admin' ? '/admin' : '/';

            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'redirect' => $redirectUrl,
                    'user' => [
                        'name' => $user->name,
                        'role' => $user->role,
                    ]
                ]);
            }

            return redirect()->intended($redirectUrl);
        }

        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return response()->json([
                'success' => false,
                'message' => 'Email/Username atau password salah.'
            ], 422);
        }

        return back()->withErrors([
            'login' => 'Email/Username atau password salah.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil.',
                'redirect' => '/'
            ]);
        }

        return redirect('/');
    }
}
