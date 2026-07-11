<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CnnModel;
use App\Services\MLService;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    public function __construct(protected MLService $ml) {}

    public function index()
    {
        // Ambil dari DB
        $models = CnnModel::orderByDesc('created_at')->get()->map(function ($m) {
            return [
                'id'               => $m->id_model,
                'name'             => $m->nama_model,
                'arsitektur'       => $m->arsitektur ?? 'ResNet50',
                'size_mb'          => '-',
                'akurasi_training' => $m->akurasi_training,
                'epoch'            => $m->epoch,
                'created_at'       => $m->tanggal_training?->format('Y-m-d') ?? $m->created_at->format('Y-m-d H:i'),
                'is_active'        => (bool) $m->status_aktif,
            ];
        })->toArray();

        // Stats tetap dari Flask
        $stats       = $this->ml->getStats();
        $flaskOnline = $this->ml->isHealthy();

        return view('admin.model.index', [
            'models'      => $models,
            'error'       => null,
            'flaskOnline' => $flaskOnline,
            'stats'       => $stats['success'] ? $stats['data'] : null,
        ]);
    }

    public function switch(Request $request)
    {
        $request->validate(['model_name' => 'required|string']);

        // Kirim ke Flask
        $result = $this->ml->switchModel($request->model_name);

        if ($result['success']) {
            // Update status_aktif di DB
            $model = CnnModel::where('nama_model', $request->model_name)->first();

            if ($model) {
                $model->jadikanAktif();
            }
        }

        return response()->json($result);
    }
}