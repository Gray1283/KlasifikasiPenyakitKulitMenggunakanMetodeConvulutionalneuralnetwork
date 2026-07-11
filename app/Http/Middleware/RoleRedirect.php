<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleRedirect
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 🛠 ADMIN tidak boleh masuk halaman user (optional strict)
        if ($user->role === 'admin') {
            if ($request->is('dashboard', 'deteksi*', 'riwayat-kesehatan*')) {
                return redirect()->route('admin.dashboard');
            }
        }

        // 👤 USER tidak boleh masuk admin
        if ($user->role === 'user') {
            if ($request->is('admin*')) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}