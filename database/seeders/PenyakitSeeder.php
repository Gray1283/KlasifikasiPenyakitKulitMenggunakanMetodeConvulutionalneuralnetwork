<?php

namespace Database\Seeders;

use App\Models\Penyakit;
use Illuminate\Database\Seeder;

class PenyakitSeeder extends Seeder
{
    /**
     * Mengisi/memperbarui kode_label untuk 7 kelas dataset HAM10000.
     * Menggunakan updateOrCreate berdasarkan kode_label, jadi aman
     * dijalankan berkali-kali (tidak akan duplikat).
     */
    public function run(): void
    {
        $data = [
            [
                'kode_label'    => 'akiec',
                'nama_penyakit' => 'Actinic Keratoses & Intraepithelial Carcinoma (Keratosis Aktinik)',
            ],
            [
                'kode_label'    => 'bcc',
                'nama_penyakit' => 'Basal Cell Carcinoma (Karsinoma Sel Basal)',
            ],
            [
                'kode_label'    => 'bkl',
                'nama_penyakit' => 'Benign Keratosis-like Lesions (Keratosis Jinak)',
            ],
            [
                'kode_label'    => 'df',
                'nama_penyakit' => 'Dermatofibroma',
            ],
            [
                'kode_label'    => 'mel',
                'nama_penyakit' => 'Melanoma',
            ],
            [
                'kode_label'    => 'nv',
                'nama_penyakit' => 'Melanocytic Nevi (Nevus/Tahi Lalat)',
            ],
            [
                'kode_label'    => 'vasc',
                'nama_penyakit' => 'Vascular Lesions (Lesi Vaskular)',
            ],
        ];

        foreach ($data as $item) {
            Penyakit::updateOrCreate(
                ['kode_label' => $item['kode_label']],
                $item
            );
        }

        $this->command->info('Berhasil mengisi kode_label untuk ' . count($data) . ' kelas penyakit.');
    }
}