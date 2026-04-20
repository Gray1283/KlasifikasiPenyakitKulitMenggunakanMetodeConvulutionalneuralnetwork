<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_klasifikasi', function (Blueprint $table) {
            $table->id('id_hasil');
            $table->foreignId('id_gambar')->constrained('gambar_kulit', 'id_gambar')->onDelete('cascade');
            $table->foreignId('id_model')->constrained('model_cnn', 'id_model')->onDelete('cascade');
            $table->foreignId('id_penyakit')->constrained('penyakit', 'id_penyakit')->onDelete('cascade');
            $table->float('tingkat_akurasi')->nullable();
            $table->string('hasil_prediksi')->nullable();
            $table->timestamp('tanggal_prediksi')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_klasifikasi');
    }
};