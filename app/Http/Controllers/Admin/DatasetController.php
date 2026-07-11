<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penyakit;
use App\Services\MLService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DatasetController extends Controller
{
    protected string $datasetPath = 'C:/Users/ASUS/DermaAI/dataset';

    public function __construct(protected MLService $ml) {}

    public function index()
    {
        // Ambil jumlah gambar dari Flask (sumber kebenaran)
        $flaskInfo = $this->ml->getDatasetInfo();
        $flaskData = $flaskInfo['success'] ? ($flaskInfo['data']['dataset'] ?? []) : [];

        $penyakit = Penyakit::all()->map(function ($item) use ($flaskData) {
            $item->jumlah_gambar = $flaskData[$item->kode_label] ?? $this->countGambarLokal($item->kode_label);
            return $item;
        });

        $totalGambar = $penyakit->sum('jumlah_gambar');
        $totalKelas  = $penyakit->count();
        $terbanyak   = $penyakit->sortByDesc('jumlah_gambar')->first();
        $tersedikit  = $penyakit->sortBy('jumlah_gambar')->first();
        $flaskOnline = $flaskInfo['success'];

        return view('admin.dataset.index', compact(
            'penyakit', 'totalGambar', 'totalKelas',
            'terbanyak', 'tersedikit', 'flaskOnline'
        ));
    }

    // ── Upload ZIP (kirim ke Flask) ──────────────────────────────

    public function uploadZip(Request $request)
    {
        $request->validate([
            'label' => 'required|in:mel,nv,bcc,akiec,bkl,df,vasc',
            'file'  => 'required|file|mimes:zip|max:512000', // maks 500MB
        ]);

        $penyakit = Penyakit::where('kode_label', $request->label)->first();

        $result = $this->ml->uploadZipDataset($request->label, $request->file('file'));

        if ($result['success']) {
            $data = $result['data'];
            return response()->json([
                'success' => true,
                'message' => "{$data['extracted']} gambar berhasil diekstrak ke kelas " .
                             ($penyakit?->nama_penyakit ?? $request->label) .
                             ($data['skipped'] > 0 ? " ({$data['skipped']} file non-gambar dilewati)" : ''),
                'data'    => $data,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? 'Upload gagal',
        ], 422);
    }

    // ── Upload gambar satuan (tetap ada sebagai fallback) ────────

    public function upload(Request $request, $id)
    {
        $request->validate([
            'gambar.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $penyakit = Penyakit::findOrFail($id);

        if (!$penyakit->kode_label) {
            return back()->with('error', 'Kode label penyakit ini belum diset.');
        }

        $folder = $this->datasetPath . '/' . $penyakit->kode_label;

        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        $uploaded = 0;
        foreach ($request->file('gambar') as $file) {
            $nama = $penyakit->kode_label . '_' . time() . '_' . $uploaded . '.' . $file->getClientOriginalExtension();
            $file->move($folder, $nama);
            $uploaded++;
        }

        return back()->with('success', "{$uploaded} gambar berhasil diupload ke {$penyakit->nama_penyakit}.");
    }

    // ── Lihat gambar per kelas ───────────────────────────────────

    public function lihat($id)
    {
        $penyakit = Penyakit::findOrFail($id);
        $folder   = $this->datasetPath . '/' . $penyakit->kode_label;
        $gambar   = collect();

        if ($penyakit->kode_label && File::exists($folder)) {
            $gambar = collect(File::files($folder))
                ->filter(fn($f) => in_array(strtolower($f->getExtension()), ['jpg', 'jpeg', 'png']))
                ->map(fn($f) => [
                    'name' => $f->getFilename(),
                    'path' => route('admin.dataset.gambar', [$penyakit->id_penyakit, $f->getFilename()]),
                    'size' => round($f->getSize() / 1024, 1) . ' KB',
                ])
                ->values();
        }

        return view('admin.dataset.lihat', compact('penyakit', 'gambar'));
    }

    public function hapusGambar($id, $nama)
    {
        $penyakit = Penyakit::findOrFail($id);
        $nama     = basename($nama);
        $path     = $this->datasetPath . '/' . $penyakit->kode_label . '/' . $nama;

        if (File::exists($path)) {
            File::delete($path);
            return back()->with('success', 'Gambar berhasil dihapus.');
        }

        return back()->with('error', 'Gambar tidak ditemukan.');
    }

    // ── Hapus SEMUA gambar dalam satu kelas ──────────────────────

    public function hapusSemua($id)
    {
        $penyakit = Penyakit::findOrFail($id);

        if (!$penyakit->kode_label) {
            return back()->with('error', 'Kode label penyakit ini belum diset.');
        }

        $folder = $this->datasetPath . '/' . $penyakit->kode_label;

        if (!File::exists($folder)) {
            return back()->with('error', 'Folder dataset tidak ditemukan.');
        }

        $files = collect(File::files($folder))
            ->filter(fn($f) => in_array(strtolower($f->getExtension()), ['jpg', 'jpeg', 'png']));

        $jumlah = $files->count();

        foreach ($files as $file) {
            File::delete($file->getPathname());
        }

        return redirect()
            ->route('admin.dataset.index')
            ->with('success', "{$jumlah} gambar pada kelas {$penyakit->nama_penyakit} berhasil dihapus.");
    }

    public function serveGambar($id, $nama)
    {
        $penyakit = Penyakit::findOrFail($id);
        $nama     = basename($nama);
        $path     = $this->datasetPath . '/' . $penyakit->kode_label . '/' . $nama;

        if (!File::exists($path)) abort(404);

        return response()->file($path, ['Content-Type' => mime_content_type($path)]);
    }

    // ── Helper ───────────────────────────────────────────────────

    private function countGambarLokal(?string $kodeLabel): int
    {
        if (!$kodeLabel) return 0;
        $folder = $this->datasetPath . '/' . $kodeLabel;
        if (!File::exists($folder)) return 0;

        return collect(File::files($folder))
            ->filter(fn($f) => in_array(strtolower($f->getExtension()), ['jpg', 'jpeg', 'png']))
            ->count();
    }
}