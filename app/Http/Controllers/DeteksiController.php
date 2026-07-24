<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
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

    // ============================================================
    // INDEX
    // ============================================================
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

    // ============================================================
    // STORE
    // ============================================================
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

        // ── Simpan gambar ke DB ──────────────────────────────────
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

        // ── Panggil ML API ───────────────────────────────────────
        $mlResult = $this->mlService->predictImage($file);

        if (!$mlResult['success']) {
            Log::error('ML API error: ' . ($mlResult['error'] ?? 'unknown'));
            Storage::disk('public')->delete($imagePath);
            GambarKulit::destroy($gambar->id_gambar);

            // Deteksi error "bukan gambar kulit" dari ML API
            $errorMsg = $mlResult['error'] ?? '';
            $isSkinError = str_contains(strtolower($errorMsg), 'tidak terdeteksi sebagai gambar kulit')
                        || str_contains(strtolower($errorMsg), 'is_skin')
                        || ($mlResult['data']['is_skin'] ?? true) === false;

            if ($isSkinError) {
                return back()->withErrors([
                    'image' => 'tidak terdeteksi sebagai gambar kulit',
                ]);
            }

            return back()->withErrors(['image' => 'Gagal menganalisis gambar. Silakan coba lagi.']);
        }

        $labelRaw    = $mlResult['data']['predicted_class'];
        $confidence  = $mlResult['data']['confidence'];
        $deskripsiAI = $mlResult['data']['description'];

        // ── Map label ke nama penyakit ───────────────────────────
        $namaPenyakit = $this->mapLabel($labelRaw);

        // ── Simpan atau update data penyakit ─────────────────────
        // updateOrCreate dipakai (bukan firstOrCreate) supaya gejala_umum
        // dan penanganan selalu ikut ter-update setiap ada prediksi baru,
        // bukan cuma sekali di awal saat baris pertama kali dibuat.
        $penyakit = Penyakit::updateOrCreate(
            ['nama_penyakit' => $namaPenyakit],
            [
                'deskripsi'   => $deskripsiAI,
                'gejala_umum' => $this->getGejalByLabel($labelRaw),
                'penanganan'  => $this->getRekomendasiByLabel($labelRaw),
            ]
        );

        // ── Ambil model CNN aktif jika ada ──────────────────────
        try {
            $modelCnn = CnnModel::latest()->first();
        } catch (\Exception $e) {
            $modelCnn = null;
        }

        // ── Simpan hasil klasifikasi ─────────────────────────────
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

    // ============================================================
    // HASIL
    // ============================================================
    public function hasil($id)
    {
        $hasil = HasilKlasifikasi::with(['penyakit', 'gambarKulit'])
            ->where('id_hasil', $id)
            ->whereHas('gambarKulit', fn($q) => $q->where('id_user', Auth::id()))
            ->firstOrFail();

        $penyakit    = $hasil->penyakit;
        $gambarKulit = $hasil->gambarKulit;

        return view('user.deteksi.hasil', [
            'nama_penyakit'  => $penyakit->nama_penyakit ?? 'Tidak Diketahui',
            'label_raw'      => $hasil->hasil_prediksi,
            'confidence'     => $hasil->tingkat_akurasi,
            'deskripsi'      => $penyakit->deskripsi  ?? '-',
            'gejala'         => $penyakit->gejala_umum ?? '-',
            'rekomendasi'    => $penyakit->penanganan
                                ? (is_array($penyakit->penanganan)
                                    ? $penyakit->penanganan
                                    : explode("\n", $penyakit->penanganan))
                                : [],
            'saran'          => null,
            'gambar'         => Storage::url($gambarKulit->nama_file),
            'ukuran'         => number_format($gambarKulit->ukuran_file / 1024 / 1024, 1) . ' MB',
            'waktu'          => $hasil->tanggal_prediksi->format('H:i'),
            'training_stats' => $this->getTrainingStats(),
        ]);
    }

    // ============================================================
    // HELPER - Baca statistik training dari JSON (real-time)
    // ============================================================
    private function getTrainingStats(): ?array
    {
        $jsonPath = storage_path('app/ml/training_stats.json');

        if (!File::exists($jsonPath)) {
            return null;
        }

        try {
            $data = json_decode(File::get($jsonPath), true);
            return $data ?: null;
        } catch (\Exception $e) {
            Log::warning('Gagal baca training_stats.json: ' . $e->getMessage());
            return null;
        }
    }

    // ============================================================
    // HELPER - Map label ke nama penyakit
    // ============================================================
    private function mapLabel(string $label): string
    {
        return match(strtolower($label)) {
            'mel'   => 'Melanoma',
            'nv'    => 'Melanocytic Nevi (Tahi Lalat)',
            'bcc'   => 'Basal Cell Carcinoma',
            'akiec' => 'Actinic Keratoses',
            'bkl'   => 'Benign Keratosis',
            'df'    => 'Dermatofibroma',
            'vasc'  => 'Vascular Lesions',
            default => ucfirst($label),
        };
    }

    // ============================================================
    // HELPER - Gejala per label
    // ============================================================
    private function getGejalByLabel(string $label): string
    {
        return match(strtolower($label)) {
            'mel'   => 'Bercak kulit berubah warna, tepi tidak rata, ukuran membesar, bisa berdarah',
            'nv'    => 'Bercak coklat atau hitam, tepi rata, ukuran stabil, tidak nyeri',
            'bcc'   => 'Benjolan kecil berkilat, luka yang tidak sembuh, bercak merah bersisik',
            'akiec' => 'Bercak kasar bersisik, kemerahan, terasa gatal atau perih saat disentuh',
            'bkl'   => 'Pertumbuhan kulit berwarna coklat atau hitam, permukaan kasar dan bersisik',
            'df'    => 'Benjolan keras kecil di bawah kulit, berwarna kecoklatan, tidak nyeri',
            'vasc'  => 'Bercak merah atau ungu di kulit, pembuluh darah terlihat jelas di permukaan',
            default => 'Perubahan pada kulit yang perlu diperiksa lebih lanjut',
        };
    }

    // ============================================================
    // HELPER - Rekomendasi Tindakan per label
    // ============================================================
    private function getRekomendasiByLabel(string $label): string
    {
        return match(strtolower($label)) {
            'mel' => implode("\n", [
                'Segera konsultasi ke dokter spesialis kulit atau onkologi — berpotensi ganas (melanoma)',
                'Jangan menunda pemeriksaan lanjutan seperti biopsi atau dermoskopi',
                'Hindari paparan sinar matahari langsung dan gunakan tabir surya SPF 50+',
                'Informasikan riwayat keluarga terkait kanker kulit kepada dokter',
                'Pantau tanda ABCDE: Asimetri, Border tidak rata, Color tidak merata, Diameter >6mm, Evolving (berubah)',
            ]),

            'nv' => implode("\n", [
                'Umumnya jinak (tahi lalat biasa), namun tetap perlu dipantau',
                'Periksa rutin setiap 6-12 bulan terutama jika memiliki banyak tahi lalat',
                'Gunakan tabir surya untuk mencegah perubahan akibat paparan UV',
                'Konsultasi ke dokter jika terjadi perubahan bentuk, warna, atau ukuran secara tiba-tiba',
            ]),

            'bcc' => implode("\n", [
                'Periksakan diri ke dokter kulit sesegera mungkin, BCC memerlukan penanganan medis',
                'Hindari paparan sinar UV berlebih dan gunakan pelindung fisik (topi, baju lengan panjang)',
                'Jangan mencoba mengobati sendiri dengan obat topikal tanpa resep dokter',
                'Pantau perubahan ukuran, warna, atau bentuk lesi secara berkala',
            ]),

            'akiec' => implode("\n", [
                'Segera konsultasi ke dokter spesialis kulit untuk pemeriksaan lebih lanjut',
                'Hindari paparan sinar matahari langsung dan gunakan tabir surya SPF 30+ setiap hari',
                'Jangan menggaruk atau mengelupas area kulit yang bersisik',
                'Lakukan biopsi jika direkomendasikan dokter untuk memastikan diagnosis',
            ]),

            'bkl' => implode("\n", [
                'Umumnya jinak, namun tetap disarankan konsultasi untuk memastikan diagnosis',
                'Hindari menggaruk atau mengelupas area yang terkena agar tidak iritasi atau infeksi',
                'Gunakan pelembap kulit untuk mengurangi rasa gatal jika ada',
                'Konsultasikan ke dokter bila muncul perubahan mendadak pada tekstur atau warna',
            ]),

            'df' => implode("\n", [
                'Umumnya jinak dan tidak berbahaya, namun tetap perlu dipantau',
                'Konsultasi ke dokter kulit jika lesi terasa nyeri, membesar, atau berubah warna',
                'Hindari trauma berulang (gesekan, tekanan) pada area yang terkena',
                'Jangan mencoba memencet atau menghilangkan sendiri',
            ]),

            'vasc' => implode("\n", [
                'Umumnya berupa kelainan pembuluh darah jinak, namun tetap perlu evaluasi medis',
                'Konsultasi ke dokter kulit jika lesi berdarah, membesar, atau menimbulkan nyeri',
                'Hindari trauma fisik pada area yang terkena',
                'Pantau perubahan warna atau ukuran secara berkala',
            ]),

            default => implode("\n", [
                'Konsultasikan hasil ini kepada dokter spesialis kulit untuk diagnosis yang akurat',
                'Jangan mengambil keputusan pengobatan hanya berdasarkan hasil deteksi otomatis ini',
            ]),
        };
    }
}