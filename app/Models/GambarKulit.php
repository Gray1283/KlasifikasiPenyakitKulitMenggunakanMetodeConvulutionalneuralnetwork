<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GambarKulit extends Model
{
    use HasFactory;

    protected $table = 'gambar_kulit';
    protected $primaryKey = 'id_gambar';

    protected $fillable = [
        'id_user',
        'nama_file',
        'format_file',
        'ukuran_file',
        'tanggal_upload',
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function hasilKlasifikasi()
    {
        return $this->hasOne(HasilKlasifikasi::class, 'id_gambar', 'id_gambar');
    }
}