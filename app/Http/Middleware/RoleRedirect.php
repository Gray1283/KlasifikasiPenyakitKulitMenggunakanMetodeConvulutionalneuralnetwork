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

        // admin masuk area user
        if (
            Auth::user()->role === 'admin' &&
            $request->is('dashboard', 'kesehatan*', 'deteksi*')
        ) {
            return redirect()->route('admin.dashboard');
        }

        // user masuk area admin
        if (
            Auth::user()->role === 'pengguna' &&
            $request->is('admin*')
        ) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
