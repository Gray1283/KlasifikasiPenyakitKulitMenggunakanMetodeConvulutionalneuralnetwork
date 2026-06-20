<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CnnModel extends Model
{
    protected $table = 'model_cnn';
    protected $primaryKey = 'id_model';
    protected $fillable   = ['nama_model', 'versi', 'status_aktif', 'deskripsi'];
}