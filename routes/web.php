<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeteksiController;
use App\Http\Controllers\RiwayatKesehatanController;


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

    // Deteksi
Route::get('/deteksi',          [DeteksiController::class, 'index'])->name('deteksi.index');
Route::post('/deteksi/store',   [DeteksiController::class, 'store'])->name('deteksi.store');
Route::get('/deteksi/hasil/{id}',[DeteksiController::class, 'hasil'])->name('deteksi.hasil');
// Riwayat Kesehatan
Route::get('/riwayat-kesehatan', [RiwayatKesehatanController::class, 'index'])->name('riwayat_kesehatan.index');

});
Route::get('/hasil-test', function () {
    return view('deteksi.hasil', [
        'gambar'        => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=600',
        'ukuran'        => '2.1 MB',
        'waktu'         => now()->format('H:i'),
        'nama_penyakit' => 'Tinea Corporis',
        'confidence'    => 94,
        'deskripsi'     => 'Tinea corporis (kurap badan) adalah infeksi jamur superfisial.',
        'rekomendasi'   => ['Gunakan krim antijamur', 'Jaga kebersihan area'],
        'saran'         => null,
    ]);
});

Route::get('/test-ml', function () {
    $service  = new \App\Services\MLService();
    $isHealthy = $service->isHealthy();
    return response()->json([
        'connected' => $isHealthy
    ]);
});
Route::post('/test-predict', function (\Illuminate\Http\Request $request) {
    $request->validate(['image' => 'required|image']);
    
    $service = new \App\Services\MLService();
    $result  = $service->predictImage($request->file('image'));
    
    return response()->json($result);
});