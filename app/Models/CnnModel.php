<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CnnModel extends Model
{
    protected $table      = 'model_cnn';
    protected $primaryKey = 'id_model';

    protected $fillable = [
        'nama_model',
        'arsitektur',
        'epoch',
        'akurasi_training',
        'tanggal_training',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif'     => 'boolean',
        'akurasi_training' => 'float',
        'tanggal_training' => 'date',
    ];

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', 1);
    }

    // Helper: set model ini jadi satu-satunya yang aktif
    public function jadikanAktif(): void
    {
        static::query()->update(['status_aktif' => 0]);
        $this->update(['status_aktif' => 1]);
    }

    public function getNamaTampilanAttribute(): string
    {
        $nama = $this->nama_model;

        if ($nama === 'ham10000_best.pth') {
            return 'ResNet50 (Baseline)';
        }

        if (preg_match('/resnet50_v(\d+)_acc([\d.]+)\.pth/', $nama, $m)) {
            return "ResNet50 v{$m[1]}";
        }

        if (preg_match('/model_(\d{8})_(\d{6})_acc([\d.]+)\.pth/', $nama, $m)) {
            $tanggal = substr($m[1], 4, 2) . '/' . substr($m[1], 6, 2);
            return "ResNet50 ({$tanggal})";
        }

        return $this->arsitektur ?: $nama;
    }
}