<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gambar_kulit', function (Blueprint $table) {
            $table->id('id_gambar');
            $table->unsignedBigInteger('id_user');
            $table->string('nama_file');
            $table->string('format_file')->nullable();
            $table->unsignedBigInteger('ukuran_file')->nullable();
            $table->timestamp('tanggal_upload')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gambar_kulit');
    }
};