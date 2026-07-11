<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeteksiController;
use App\Http\Controllers\RiwayatKesehatanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RiwayatKesehatanController as AdminRiwayatKesehatanController;
use App\Http\Controllers\Admin\DatasetController;
use App\Http\Controllers\Admin\TrainingController;
use App\Http\Controllers\Admin\AugmentasiController;
use App\Http\Controllers\Admin\ModelController;
use App\Http\Controllers\Admin\EvaluasiController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

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

    // Deteksi
    Route::get('/deteksi',           [DeteksiController::class, 'index'])->name('deteksi.index');
    Route::post('/deteksi/store',    [DeteksiController::class, 'store'])->name('deteksi.store');
    Route::get('/deteksi/hasil/{id}',[DeteksiController::class, 'hasil'])->name('deteksi.hasil');

    // Riwayat Kesehatan User
    Route::get('/riwayat-kesehatan', [RiwayatKesehatanController::class, 'index'])
        ->name('riwayat_kesehatan.index');

    // Pengaturan
    Route::get('/pengaturan', function () {
        return redirect()->route('dashboard');
    })->name('pengaturan');

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
    $service   = new \App\Services\MLService();
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

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users',         [UserController::class, 'index'])->name('users');
    Route::post('/users/store',  [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}',    [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Riwayat Kesehatan Admin
    Route::get('/riwayat-kesehatan',         [AdminRiwayatKesehatanController::class, 'index'])->name('riwayat_kesehatan.index');
    Route::get('/riwayat-kesehatan/export',  [AdminRiwayatKesehatanController::class, 'export'])->name('riwayat_kesehatan.export');
    Route::get('/riwayat-kesehatan/{id}',    [AdminRiwayatKesehatanController::class, 'show'])->name('riwayat_kesehatan.show');
    Route::delete('/riwayat-kesehatan/{id}', [AdminRiwayatKesehatanController::class, 'destroy'])->name('riwayat_kesehatan.destroy');

     // Dataset
    Route::get('dataset',                        [DatasetController::class, 'index'])->name('dataset.index');
    Route::post('dataset/upload-zip',            [DatasetController::class, 'uploadZip'])->name('dataset.upload-zip');
    Route::get('dataset/{id}/lihat',             [DatasetController::class, 'lihat'])->name('dataset.lihat');
    Route::get('dataset/{id}/gambar/{nama}',     [DatasetController::class, 'serveGambar'])->name('dataset.gambar');
    Route::delete('dataset/{id}/gambar/{nama}',  [DatasetController::class, 'hapusGambar'])->name('dataset.hapus-gambar');
    Route::delete('dataset/{id}/hapus-semua',    [DatasetController::class, 'hapusSemua'])->name('dataset.hapus-semua');
        // Training
    Route::get('training',         [TrainingController::class, 'index'])->name('training.index');
    Route::post('training/start',  [TrainingController::class, 'start'])->name('training.start');
    Route::get('training/status',  [TrainingController::class, 'status'])->name('training.status');
    Route::post('training/stop',   [TrainingController::class, 'stop'])->name('training.stop');
    
    // Augmentasi
    Route::get('/augmentasi', [AugmentasiController::class, 'index'])->name('augmentasi.index');
    Route::post('/augmentasi/process', [AugmentasiController::class, 'process'])->name('augmentasi.process');

        // Model CNN
    Route::get('/model', [ModelController::class, 'index'])->name('model.index');
    Route::post('/model/switch', [ModelController::class, 'switch'])->name('model.switch');

    Route::get('/evaluasi',      [EvaluasiController::class, 'index'])->name('evaluasi.index');
    Route::post('/evaluasi/run', [EvaluasiController::class, 'run'])->name('evaluasi.run');
 
    });