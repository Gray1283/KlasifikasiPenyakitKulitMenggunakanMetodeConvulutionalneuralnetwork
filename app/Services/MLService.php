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
        $this->baseUrl = config('services.ml.url', env('ML_API_URL', 'http://localhost:8000'));
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
                    'image',                        // nama field di API
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )
                ->post("{$this->baseUrl}/predict/skin");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json(),
                ];
            }

            Log::warning('ML API skin predict non-2xx response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'error'   => "HTTP {$response->status()}: {$response->body()}",
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ML API connection error (skin): ' . $e->getMessage());

            return [
                'success' => false,
                'error'   => 'Tidak dapat terhubung ke layanan AI: ' . $e->getMessage(),
            ];

        } catch (\Exception $e) {
            Log::error('ML API unexpected error (skin): ' . $e->getMessage());

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    // ---------------------------------------------------------------
    // Method lama untuk deteksi diabetes (tetap dipertahankan)
    // ---------------------------------------------------------------

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
}