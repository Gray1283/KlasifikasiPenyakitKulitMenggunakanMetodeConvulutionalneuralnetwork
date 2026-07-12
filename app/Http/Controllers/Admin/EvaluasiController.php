<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EvaluasiController extends Controller
{
   private string $flaskUrl;

public function __construct()
{
    $this->flaskUrl = config('services.ml.url');
}

    public function index()
    {
        // Ambil daftar model untuk dropdown
        try {
            $res    = Http::timeout(5)->get("{$this->flaskUrl}/evaluate/models");
            $models = $res->successful() ? $res->json('models', []) : [];
        } catch (\Exception $e) {
            $models = [];
        }

        // Ambil history evaluasi
        try {
            $res     = Http::timeout(5)->get("{$this->flaskUrl}/evaluate/history");
            $history = $res->successful() ? $res->json('history', []) : [];
        } catch (\Exception $e) {
            $history = [];
        }

        return view('admin.evaluasi.index', compact('models', 'history'));
    }

    public function run(Request $request)
    {
        $modelName = $request->input('model_name');
        $split     = $request->input('split', 'val');

        $payload = ['split' => $split];
        if ($modelName) {
            $payload['model_name'] = $modelName;
        }
        if ($split === 'test') {
            $payload['confirm_test'] = true;
        }

        try {
            $res = Http::timeout(120)->post("{$this->flaskUrl}/evaluate", $payload);
            return response()->json($res->json(), $res->status());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa terhubung ke Flask: ' . $e->getMessage(),
            ], 503);
        }
    }
}