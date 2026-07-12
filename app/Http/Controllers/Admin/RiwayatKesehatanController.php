<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilKlasifikasi;
use App\Models\Penyakit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatKesehatanController extends Controller
{
    public function index(Request $request)
    {
        $query = HasilKlasifikasi::with(['gambar.user', 'penyakit', 'model'])
            ->latest('tanggal_prediksi');

        if ($request->filled('penyakit')) {
            $query->where('id_penyakit', $request->penyakit);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('gambar.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $riwayat = $query->paginate(15)->withQueryString();

        $totalHasil = HasilKlasifikasi::count();

        $totalUser = DB::table('gambar_kulit')
            ->join('hasil_klasifikasi', 'gambar_kulit.id_gambar', '=', 'hasil_klasifikasi.id_gambar')
            ->distinct('gambar_kulit.id_user')
            ->count('gambar_kulit.id_user');

        $rataAkurasi     = number_format(HasilKlasifikasi::avg('tingkat_akurasi') ?? 0, 1);
        $prediksiHariIni = HasilKlasifikasi::whereDate('tanggal_prediksi', today())->count();
        $penyakitList    = Penyakit::all();

        $penyakitTerbanyak = HasilKlasifikasi::select('id_penyakit', DB::raw('count(*) as total'))
            ->groupBy('id_penyakit')
            ->orderByDesc('total')
            ->with('penyakit')
            ->first();

        return view('admin.m_riwayat_kesehatan.index', compact(
            'riwayat',
            'totalHasil',
            'totalUser',
            'rataAkurasi',
            'prediksiHariIni',
            'penyakitList',
            'penyakitTerbanyak' // 
        ));
    }

    public function show(int $id)
    {
        $item = HasilKlasifikasi::with(['gambar.user', 'penyakit', 'model'])
            ->findOrFail($id);

        return response()->json([
            'id_hasil'         => $item->id_hasil,
            'user_name'        => $item->gambar?->user?->name        ?? '-',
            'user_email'       => $item->gambar?->user?->email       ?? '-',
            'nama_penyakit'    => $item->penyakit?->nama_penyakit    ?? '-',
            'nama_model'       => $item->model?->nama_tampilan       ?? '-',
            'tingkat_akurasi'  => $item->tingkat_akurasi,
            'hasil_prediksi'   => $item->hasil_prediksi,
            'tanggal_prediksi' => $item->tanggal_prediksi
                ? Carbon::parse($item->tanggal_prediksi)->format('d M Y, H:i')
                : '-',
            'path_gambar'      => $item->gambar?->nama_file ?? null,
        ]);
    }

    public function destroy(int $id)
    {
        $item = HasilKlasifikasi::findOrFail($id);
        $item->delete();

        return redirect()
            ->route('admin.riwayat_kesehatan.index')
            ->with('success', 'Data berhasil dihapus.');
    }

    public function export()
    {
        $data = HasilKlasifikasi::with(['gambar.user', 'penyakit', 'model'])
            ->latest('tanggal_prediksi')
            ->get();

        $filename = 'riwayat_kesehatan_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'No', 'Nama User', 'Email', 'Penyakit', 'Model',
                'Akurasi (%)', 'Hasil Prediksi', 'Tanggal Prediksi'
            ]);

            foreach ($data as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->gambar?->user?->name        ?? '-',
                    $item->gambar?->user?->email       ?? '-',
                    $item->penyakit?->nama_penyakit    ?? '-',
                    // Pakai nama_tampilan supaya CSV juga rapi dibaca,
                    // bukan nama file teknis mentah
                    $item->model?->nama_tampilan       ?? '-',
                    $item->tingkat_akurasi             ?? 0,
                    $item->hasil_prediksi              ?? '-',
                    $item->tanggal_prediksi            ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}