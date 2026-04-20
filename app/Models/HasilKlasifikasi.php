<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilKlasifikasi extends Model
{
    use HasFactory;

    protected $table = 'hasil_klasifikasi';
    protected $primaryKey = 'id_hasil';

    protected $fillable = [
        'id_gambar',
        'id_model',
        'id_penyakit',
        'tingkat_akurasi',
        'hasil_prediksi',
        'tanggal_prediksi',
    ];

    protected $casts = [
        'tanggal_prediksi' => 'datetime',
        'tingkat_akurasi'  => 'float',
    ];

    public function gambarKulit()
    {
        return $this->belongsTo(\App\Models\GambarKulit::class, 'id_gambar', 'id_gambar');
    }

    public function penyakit()
    {
        return $this->belongsTo(\App\Models\Penyakit::class, 'id_penyakit', 'id_penyakit');
    }

    public function cnnModel()
    {
        return $this->belongsTo(\App\Models\CnnModel::class, 'id_model', 'id_model');
    }
}