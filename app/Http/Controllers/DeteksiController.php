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

        $lastCheckup = HasilKlasifikasi::whereHas('gambarKulit', fn($q) => $q->where('id_user', $userId))
            ->with(['penyakit', 'gambarKulit'])
            ->latest('tanggal_prediksi')
            ->first();

        $totalCheckups = HasilKlasifikasi::whereHas('gambarKulit', fn($q) => $q->where('id_user', $userId))->count();
        $normalCount   = HasilKlasifikasi::whereHas('gambarKulit', fn($q) => $q->where('id_user', $userId))->where('tingkat_akurasi', '>=', 80)->count();
        $abnormalCount = HasilKlasifikasi::whereHas('gambarKulit', fn($q) => $q->where('id_user', $userId))->where('tingkat_akurasi', '<', 80)->count();

        return view('user.deteksi.index', compact('lastCheckup', 'totalCheckups', 'normalCount', 'abnormalCount'));
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

    // Simpan gambar ke DB
    try {
        $gambar = GambarKulit::create([
            'id_user'        => Auth::id(),
            'nama_file'      => $imagePath,
            'format_file'    => $file->getClientOriginalExtension(),
            'ukuran_file'    => $file->getSize(),
            'tanggal_upload' => now(),
        ]);
    } catch (\Exception $e) {
        Log::error('Gagal menyimpan gambar: ' . $e->getMessage());
        Storage::disk('public')->delete($imagePath);
        return back()->withErrors(['image' => 'Gagal menyimpan gambar. Silakan coba lagi.']);
    }

    // ── DUMMY ML RESULT (hapus setelah AI siap) ──────────────
    $labelRaw   = 'Tinea Corporis';
    $confidence = 94.00;
    // ─────────────────────────────────────────────────────────

    // Simpan hasil ke DB
    $penyakit = Penyakit::firstOrCreate(
        ['nama_penyakit' => $labelRaw],
        [
            'deskripsi'       => 'Tinea corporis (kurap badan) adalah infeksi jamur superfisial yang menyerang lapisan kulit luar. Ditandai dengan bercak merah melingkar, bersisik di tepinya, dan terasa gatal.',
            'rekomendasi'     => "Gunakan krim antijamur topikal 2x sehari\nJaga area tetap bersih dan kering\nHindari berbagi handuk\nPeriksa dokter jika tidak membaik dalam 2 minggu",
            'saran_tambahan'  => 'Hindari menggaruk area yang terinfeksi untuk mencegah penyebaran.',
        ]
    );

    $modelCnn = null;

    try {
        $hasil = HasilKlasifikasi::create([
            'id_gambar'        => $gambar->id_gambar,
            'id_model'         => $modelCnn?->id_model,
            'id_penyakit'      => $penyakit->id_penyakit,
            'tingkat_akurasi'  => $confidence,
            'hasil_prediksi'   => $labelRaw,
            'tanggal_prediksi' => now(),
        ]);
    } catch (\Exception $e) {
        Log::error('Gagal menyimpan hasil: ' . $e->getMessage());
        return back()->withErrors(['image' => 'Gagal menyimpan hasil deteksi. Silakan coba lagi.']);
    }

    return redirect()->route('deteksi.hasil', ['id' => $hasil->id_hasil]);
}

    public function hasil($id)
    {
        $hasil = HasilKlasifikasi::with(['penyakit', 'gambarKulit'])
            ->where('id_hasil', $id)
            ->whereHas('gambarKulit', fn($q) => $q->where('id_user', Auth::id()))
            ->firstOrFail();

        $penyakit   = $hasil->penyakit;
        $gambarKulit = $hasil->gambarKulit;

        return view('user.deteksi.hasil', [
            'nama_penyakit' => $penyakit->nama_penyakit ?? 'Tidak Diketahui',
            'confidence'    => $hasil->tingkat_akurasi,
            'deskripsi'     => $penyakit->deskripsi     ?? '-',
            'rekomendasi'   => $penyakit->rekomendasi
                                ? (is_array($penyakit->rekomendasi)
                                    ? $penyakit->rekomendasi
                                    : explode("\n", $penyakit->rekomendasi))
                                : [],
            'saran'         => $penyakit->saran_tambahan ?? null,
            'gambar'        => Storage::url($gambarKulit->nama_file),
            'ukuran'        => number_format($gambarKulit->ukuran_file / 1024 / 1024, 1) . ' MB',
            'waktu'         => $hasil->tanggal_prediksi->format('H:i'),
        ]);
    }
}

