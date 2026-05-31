<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penyakit extends Model
{
    protected $table      = 'penyakit';
    protected $primaryKey = 'id_penyakit';
    protected $fillable   = ['nama_penyakit', 'deskripsi', 'rekomendasi', 'saran_tambahan'];
}