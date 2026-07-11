<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MLService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.ml.url', env('ML_SERVICE_URL', 'http://localhost:5000'));
        $this->timeout = (int) config('services.ml.timeout', 30);
    }

    /**
     * Kirim gambar ke ML API untuk prediksi penyakit kulit (multipart)
     *
     * @param  UploadedFile  $file
     * @return array{ success: bool, data?: array, error?: string }
     */
    public function predictImage(UploadedFile $file): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->attach(
                    'image',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )
                ->post("{$this->baseUrl}/predict");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json(),
                ];
            }

            Log::warning('ML API non-2xx', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'error'   => "HTTP {$response->status()}: {$response->body()}",
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ML API connection error: ' . $e->getMessage());
            return [
                'success' => false,
                'error'   => 'Tidak dapat terhubung ke layanan AI: ' . $e->getMessage(),
            ];

        } catch (\Exception $e) {
            Log::error('ML API error: ' . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function predict(array $data): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/predict", $data);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'error' => "HTTP {$response->status()}"];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function explain(array $data): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/explain", $data);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'error' => "HTTP {$response->status()}"];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/health");
            return $response->ok();
        } catch (\Exception $e) {
            return false;
        }
    }

        public function uploadZipDataset(string $label, \Illuminate\Http\UploadedFile $file): array
    {
        try {
            $response = Http::timeout(300) // ZIP besar bisa lama
                ->attach(
                    'file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )
                ->post("{$this->baseUrl}/dataset/upload-zip", [
                    'label' => $label,
                ]);
 
            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
 
            return [
                'success' => false,
                'error'   => $response->json()['message'] ?? "HTTP {$response->status()}",
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return ['success' => false, 'error' => 'Tidak dapat terhubung ke Flask: ' . $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
 
    /**
     * Ambil info jumlah gambar per kelas dari Flask
     */
    public function getDatasetInfo(): array
    {
        try {
            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/dataset/info");
 
            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
 
            return ['success' => false, 'error' => "HTTP {$response->status()}"];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function startTraining(array $params): array
    {
        try {
            $response = Http::timeout(10)
                ->post("{$this->baseUrl}/train", $params);
 
            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            return ['success' => false, 'error' => "HTTP {$response->status()}"];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
 
    public function getTrainingStatus(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/training-status");
            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            return ['success' => false, 'error' => "HTTP {$response->status()}"];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
 
    public function stopTraining(): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/training-stop");
            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            return ['success' => false, 'error' => "HTTP {$response->status()}"];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
 
    public function getStats(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/stats");
            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            return ['success' => false, 'error' => "HTTP {$response->status()}"];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Minta Flask generate gambar augmentasi untuk 1 kelas tertentu.
     * Sumber augmentasi otomatis hanya diambil dari bagian TRAIN
     * (val/test tidak akan pernah disentuh) - logic ini sudah
     * ditangani di sisi Flask (get_split).
     */
    public function augmentClass(string $label, int $jumlah): array
    {
        try {
            $response = Http::timeout(120) // augmentasi banyak gambar bisa agak lama
                ->post("{$this->baseUrl}/augment", [
                    'label'  => $label,
                    'jumlah' => $jumlah,
                ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return [
                'success' => false,
                'error'   => $response->json()['message'] ?? "HTTP {$response->status()}",
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return ['success' => false, 'error' => 'Tidak dapat terhubung ke Flask: ' . $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Ambil daftar semua model yang tersimpan (model awal + hasil training)
     */
    public function listModels(): array
    {
        try {
            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/models");
            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            return ['success' => false, 'error' => "HTTP {$response->status()}"];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Aktifkan model tertentu (yang dipakai untuk /predict selanjutnya)
     */
    public function switchModel(string $modelName): array
    {
        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/models/switch", [
                    'model_name' => $modelName,
                ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return [
                'success' => false,
                'error'   => $response->json()['message'] ?? "HTTP {$response->status()}",
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}