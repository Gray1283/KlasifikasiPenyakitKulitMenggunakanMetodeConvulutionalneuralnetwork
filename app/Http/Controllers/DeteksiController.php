<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\GambarKulit;
use App\Models\HasilKlasifikasi;
use App\Models\Penyakit;
use App\Models\CnnModel;
use App\Services\MLService;

class DeteksiController extends Controller
{
    protected $mlService;

    public function __construct(MLService $mlService)
    {
        $this->mlService = $mlService;
    }

    public function index()
    {
        $userId = Auth::id();

        $lastCheckup = HasilKlasifikasi::whereHas('gambarKulit', function($q) use ($userId) {
                $q->where('id_user', $userId);
            })
            ->with(['penyakit', 'gambarKulit'])
            ->latest('tanggal_prediksi')
            ->first();

        $totalCheckups = HasilKlasifikasi::whereHas('gambarKulit', function($q) use ($userId) {
                $q->where('id_user', $userId);
            })->count();

        $normalCount = HasilKlasifikasi::whereHas('gambarKulit', function($q) use ($userId) {
                $q->where('id_user', $userId);
            })
            ->where('tingkat_akurasi', '>=', 80)
            ->count();

        $abnormalCount = HasilKlasifikasi::whereHas('gambarKulit', function($q) use ($userId) {
                $q->where('id_user', $userId);
            })
            ->where('tingkat_akurasi', '<', 80)
            ->count();

        return view('user.deteksi.index', compact(
            'lastCheckup',
            'totalCheckups',
            'normalCount',
            'abnormalCount'
        ));
    }

    public function create()
    {
        return view('user.deteksi.form');
    }

    public function store(Request $request)
    {
        $request->validate(
            ['image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120'],
            [
                'image.required' => 'Gambar wajib diunggah.',
                'image.image'    => 'File harus berupa gambar.',
                'image.mimes'    => 'Format gambar harus JPG, PNG, atau WEBP.',
                'image.max'      => 'Ukuran gambar maksimal 5 MB.',
            ]
        );

        $file      = $request->file('image');
        $imagePath = $file->store('deteksi_kulit', 'public');

        try {
            $gambar = GambarKulit::create([
                'id_user'        => Auth::id(),
                'nama_file'      => $imagePath,
                'format_file'    => $file->getClientOriginalExtension(),
                'ukuran_file'    => $file->getSize(),
                'tanggal_upload' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan gambar kulit: ' . $e->getMessage());
            Storage::disk('public')->delete($imagePath);
            return back()->with('error', 'Gagal menyimpan gambar. Silakan coba lagi.');
        }

        try {
            $mlResult = $this->mlService->predictImage($file);

            if (!$mlResult['success']) {
                Log::warning('ML API gagal', ['error' => $mlResult['error'] ?? 'Unknown']);
                Storage::disk('public')->delete($imagePath);
                $gambar->delete();
                return back()->with('error', 'Gagal menghubungi layanan AI. Silakan coba lagi.');
            }

            $prediction = $mlResult['data'];
            $labelRaw   = $prediction['label']     ?? 'Tidak Diketahui';
            $confidence = $prediction['confidence'] ?? 0;
            $allScores  = $prediction['all_scores'] ?? [];

        } catch (\Exception $e) {
            Log::error('MLService Error: ' . $e->getMessage());
            Storage::disk('public')->delete($imagePath);
            $gambar->delete();
            return back()->with('error', 'Terjadi kesalahan saat memproses gambar.');
        }

        $penyakit = Penyakit::firstOrCreate(['nama_penyakit' => $labelRaw]);
        $modelCnn = CnnModel::where('status_aktif', true)->latest()->first();

        try {
            $hasil = HasilKlasifikasi::create([
                'id_gambar'        => $gambar->id_gambar,
                'id_model'         => $modelCnn?->id_model,
                'id_penyakit'      => $penyakit->id_penyakit,
                'tingkat_akurasi'  => round($confidence * 100, 2),
                'hasil_prediksi'   => $labelRaw,
                'tanggal_prediksi' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan hasil klasifikasi: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan hasil deteksi. Silakan coba lagi.');
        }

        $hasilData = [
            'id_hasil'   => $hasil->id_hasil,
            'label'      => $labelRaw,
            'confidence' => round($confidence * 100, 2),
            'all_scores' => $allScores,
            'image_url'  => Storage::url($imagePath),
            'penyakit'   => $penyakit,
        ];

        return view('user.deteksi.hasil', compact('hasilData'));
    }
}