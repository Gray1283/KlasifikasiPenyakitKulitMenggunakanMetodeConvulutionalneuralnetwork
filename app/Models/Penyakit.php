<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyakit extends Model
{
    use HasFactory;

    protected $table = 'penyakit';
    protected $primaryKey = 'id_penyakit';

    protected $fillable = [
        'nama_penyakit',
        'deskripsi',
        'gejala_umum',
        'penanganan',
    ];

    public function hasilKlasifikasi()
    {
        return $this->hasMany(HasilKlasifikasi::class, 'id_penyakit', 'id_penyakit');
    }
}