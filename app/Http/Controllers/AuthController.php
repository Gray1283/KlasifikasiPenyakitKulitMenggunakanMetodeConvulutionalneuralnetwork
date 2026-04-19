<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('user.login');
    }

    public function showRegister()
    {
        return view('user.register');
    }

    public function showForgotPassword()
    {
        return view('user.forgot-password');
    }

    /* =========================
       REGISTER
    ========================== */

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|min:3',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'nullable|in:pengguna,admin',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role ?? 'pengguna',
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil! Silakan login.');
    }

    /* =========================
       LOGIN
    ========================== */

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            // ✅ Redirect berdasarkan role
            if (Auth::user()->role === 'admin') {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('login', 'Selamat datang Admin!');
            }

            return redirect()
                ->route('dashboard')
                ->with('login', 'Login berhasil! Selamat datang 👋');
        }

        return back()->with('error', 'Email atau password salah!');
    }

    /* =========================
       LOGOUT
    ========================== */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('logout', 'Anda berhasil logout.');
    }
}