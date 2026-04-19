<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('user.landingpage');
})->name('landingpage');

// Auth Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
    ->name('forgot.password');

/*
|--------------------------------------------------------------------------
| USER ROUTES (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role.redirect'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])
        ->name('dashboard');

    // Placeholder DeteksiAI — belum dibuat, redirect ke dashboard dulu
    Route::get('/deteksi', function () {
        return redirect()->route('dashboard');
    })->name('deteksi.index');

    Route::get('/deteksi/create', function () {
        return redirect()->route('dashboard');
    })->name('deteksi.create');

    // Placeholder Riwayat Kesehatan — belum dibuat, redirect ke dashboard dulu
    Route::get('/riwayat-kesehatan', function () {
        return redirect()->route('dashboard');
    })->name('riwayat_kesehatan.index');

    // Placeholder Pengaturan — belum dibuat
    Route::get('/pengaturan', function () {
        return redirect()->route('dashboard');
    })->name('pengaturan');

});