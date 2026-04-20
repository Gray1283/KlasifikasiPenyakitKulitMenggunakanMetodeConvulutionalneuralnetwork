<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\HasilKlasifikasi;
use App\Models\GambarKulit;

class RiwayatKesehatanController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $riwayat = HasilKlasifikasi::whereHas('gambarKulit', function($q) use ($userId) {
                $q->where('id_user', $userId);
            })
            ->with(['penyakit', 'gambarKulit'])
            ->orderBy('tanggal_prediksi', 'desc')
            ->get()
            ->map(function($item) {
                return (object)[
                    'id'             => $item->id_hasil,
                    'label_penyakit' => $item->hasil_prediksi,
                    'confidence'     => $item->tingkat_akurasi,
                    'tingkat_resiko' => $this->mapRisiko($item->tingkat_akurasi),
                    'rekomendasi'    => $item->penyakit?->penanganan,
                    'perlu_konsul'   => $item->tingkat_akurasi >= 70 ? 'Ya' : 'Tidak',
                    'gambar_path'    => $item->gambarKulit?->nama_file,
                    'created_at'     => $item->tanggal_prediksi,
                    'penyakit'       => $item->penyakit,
                ];
            });

        return view('user.riwayat_kesehatan.index', compact('riwayat'));
    }

    private function mapRisiko($confidence): string
    {
        if ($confidence >= 80) return 'Tinggi';
        if ($confidence >= 60) return 'Sedang';
        return 'Rendah';
    }
}