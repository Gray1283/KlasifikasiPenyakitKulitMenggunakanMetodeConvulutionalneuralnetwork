<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CnnModel;
use App\Services\MLService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrainingController extends Controller
{
    public function __construct(protected MLService $ml) {}

    public function index()
    {
        $stats = null;
        $result = $this->ml->getStats();
        if ($result['success']) {
            $stats = $result['data'];
        }

        $status = null;
        $statusResult = $this->ml->getTrainingStatus();
        if ($statusResult['success']) {
            $status = $statusResult['data'];
        }

        return view('admin.training.index', compact('stats', 'status'));
    }

    public function start(Request $request)
    {
        $request->validate([
            'epochs'        => 'required|integer|min:1|max:100',
            'batch_size'    => 'required|integer|in:8,16,32,64',
            'learning_rate' => 'required|numeric|in:0.0001,0.001,0.01',
        ]);

        $result = $this->ml->startTraining([
            'epochs'        => (int) $request->epochs,
            'batch_size'    => (int) $request->batch_size,
            'learning_rate' => (float) $request->learning_rate,
        ]);

        return response()->json($result);
    }

    public function status()
    {
        $result = $this->ml->getTrainingStatus();

        if (!$result['success']) {
            return response()->json(['status' => 'error']);
        }

        $data = $result['data'];

        // Detect training selesai → auto insert ke DB
        if (($data['status'] ?? '') === 'finished' && !empty($data['model_name'])) {
            $this->simpanModelKeDB($data);
        }

        return response()->json($data);
    }

    public function stop()
    {
        $result = $this->ml->stopTraining();
        return response()->json($result);
    }

    // ── Private helper ───────────────────────────────────────────

    private function simpanModelKeDB(array $data): void
    {
        $namaModel = $data['model_name'];

        // Cegah duplikat
        if (CnnModel::where('nama_model', $namaModel)->exists()) {
            return;
        }

        try {
            // Non-aktifkan semua model lama
            CnnModel::query()->update(['status_aktif' => 0]);

            // Insert model baru, langsung aktif
            CnnModel::create([
                'nama_model'       => $namaModel,
                'arsitektur'       => 'ResNet50',
                'epoch'            => $data['total_epoch_run'] ?? null,
                'akurasi_training' => $data['overall_accuracy'] ?? null,
                'tanggal_training' => now()->toDateString(),
                'status_aktif'     => 1,
            ]);

            Log::info("[TrainingController] Model baru disimpan ke DB: {$namaModel}");

        } catch (\Exception $e) {
            Log::error("[TrainingController] Gagal simpan model ke DB: " . $e->getMessage());
        }
    }
}