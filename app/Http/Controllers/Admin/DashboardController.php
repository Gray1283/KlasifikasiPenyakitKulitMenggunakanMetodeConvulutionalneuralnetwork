<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HasilKlasifikasi;
use App\Models\CnnModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== Statistik utama =====
        $totalUser         = User::count();
        $userBaruMingguIni = User::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        $totalHasilCnn  = HasilKlasifikasi::count();
        $deteksiHariIni = HasilKlasifikasi::whereDate('tanggal_prediksi', Carbon::today())->count();

        // ===== Distribusi penyakit (untuk bar chart) =====
        // hasil: ['Eksim' => 12, 'Psoriasis' => 5, ...]
        $distribusiPenyakit = HasilKlasifikasi::selectRaw('hasil_prediksi, COUNT(*) as jumlah')
                                ->whereNotNull('hasil_prediksi')
                                ->groupBy('hasil_prediksi')
                                ->pluck('jumlah', 'hasil_prediksi')
                                ->toArray();

        // ===== Tren deteksi 7 hari terakhir (untuk line chart) =====
        $trenMingguan = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i);
            $label   = $tanggal->translatedFormat('d M');
            $trenMingguan[$label] = HasilKlasifikasi::whereDate('tanggal_prediksi', $tanggal->toDateString())->count();
        }

        // ===== Aktivitas terbaru (5 hasil klasifikasi terakhir) =====
        $riwayatTerbaru = HasilKlasifikasi::with('gambarKulit.user')
                            ->latest('tanggal_prediksi')
                            ->take(5)
                            ->get();

        // ===== Status server Flask (opsional, aman kalau gagal) =====
        $flaskOnline = false;
        try {
            $response = Http::timeout(2)->get('http://127.0.0.1:5000/health');
            $flaskOnline = $response->successful();
        } catch (\Exception $e) {
            $flaskOnline = false;
        }

        // ===== Info model CNN yang sedang aktif =====
        $modelAktif = CnnModel::aktif()->first();

        $namaModelAktif   = $modelAktif?->nama_tampilan;
        $akurasiModel     = $modelAktif?->akurasi_training !== null ? $modelAktif->akurasi_training : null;
        $trainingTerakhir = $modelAktif?->tanggal_training?->diffForHumans();

        return view('admin.dashboard', compact(
            'totalUser',
            'userBaruMingguIni',
            'totalHasilCnn',
            'deteksiHariIni',
            'distribusiPenyakit',
            'trenMingguan',
            'riwayatTerbaru',
            'flaskOnline',
            'namaModelAktif',
            'akurasiModel',
            'trainingTerakhir'
        ));
    }
}