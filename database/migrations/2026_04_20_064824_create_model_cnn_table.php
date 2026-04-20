<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_cnn', function (Blueprint $table) {
            $table->id('id_model');
            $table->string('nama_model');
            $table->string('arsitektur')->nullable();
            $table->integer('epoch')->nullable();
            $table->float('akurasi_training')->nullable();
            $table->date('tanggal_training')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_cnn');
    }
};