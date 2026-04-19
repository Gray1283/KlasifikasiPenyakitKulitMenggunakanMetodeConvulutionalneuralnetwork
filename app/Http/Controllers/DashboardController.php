<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $user = Auth::user();

        // Data dummy untuk keperluan frontend
        // Nanti diganti dengan data dari model/database
        $jenisPenyakit      = null;
        $statusDeteksi      = null;
        $akurasiModel       = null;
        $levelAkurasi       = null;
        $tingkatKepercayaan = null;
        $levelKepercayaan   = null;
        $statusKlasifikasi  = null;
        $terakhirDiperbarui = null;
        $rekomendasi        = [];
        $riwayatTerbaru     = collect(); // collection kosong

        return view('user.dashboard', compact(
            'user',
            'jenisPenyakit',
            'statusDeteksi',
            'akurasiModel',
            'levelAkurasi',
            'tingkatKepercayaan',
            'levelKepercayaan',
            'statusKlasifikasi',
            'terakhirDiperbarui',
            'rekomendasi',
            'riwayatTerbaru'
        ));
    }
}