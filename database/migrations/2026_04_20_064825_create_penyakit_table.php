<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyakit', function (Blueprint $table) {
            $table->id('id_penyakit');
            $table->string('nama_penyakit');
            $table->text('deskripsi')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->text('saran_tambahan')->nullable();
            $table->string('kode_label', 10)->nullable()
                  ->comment('Kode label HAM10000: mel, nv, bcc, akiec, bkl, df, vasc');
            $table->timestamps();
        });

        $mapping = [
            'Melanoma'               => 'mel',
            'Melanocytic Nevi'       => 'nv',
            'Melanocytic Nevi (Tahi Lalat)' => 'nv',
            'Basal Cell Carcinoma'   => 'bcc',
            'Actinic Keratoses'      => 'akiec',
            'Benign Keratosis'       => 'bkl',
            'Dermatofibroma'         => 'df',
            'Vascular Lesions'       => 'vasc',
        ];
        foreach ($mapping as $nama => $kode) {
            DB::table('penyakit')
                ->where('nama_penyakit', $nama)
                ->update(['kode_label' => $kode]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('penyakit');
    }
};