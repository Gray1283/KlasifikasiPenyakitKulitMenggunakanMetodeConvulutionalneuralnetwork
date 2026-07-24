@extends('layouts.navbar')

@section('title', 'Dashboard')

@php $pageTitle = 'Dashboard'; @endphp

@section('content')
@php
    /*
     * Data contoh (dummy) — dipakai HANYA sebagai fallback kalau controller
     * belum mengirim variabel ini, supaya tampilan dashboard tidak kosong
     * saat development/demo. Kalau controller sudah kirim data asli hasil
     * klasifikasi CNN, blok ini otomatis diabaikan.
     */
    $statusKlasifikasi   ??= 'Selesai Dianalisis';
    $terakhirDiperbarui  ??= now()->translatedFormat('d F Y');

    $jenisPenyakit  ??= 'Melanocytic Nevus (nv)';
    $statusDeteksi  ??= 'Terdeteksi';

    $akurasiModel ??= 78;
    $levelAkurasi ??= 'Tinggi';

    $tingkatKepercayaan ??= 92;
    $levelKepercayaan   ??= 'Ideal';

    $rekomendasi ??= [
        'Lesi tampak jinak, namun tetap pantau perubahan bentuk atau warna.',
        'Lakukan pemeriksaan ulang jika muncul rasa gatal atau perubahan ukuran.',
        'Konsultasikan ke dokter kulit untuk konfirmasi diagnosis lebih lanjut.',
    ];
@endphp
<div class="space-y-6">

    {{-- Greeting --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900">Halo, {{ $user->name }}</h2>
        <p class="text-gray-500 text-sm mt-1">
            Pantau kondisi kulit Anda secara cepat melalui analisis gambar.
        </p>
    </div>

    {{-- Kartu Hasil Klasifikasi CNN --}}
    <div class="bg-green-50 border border-green-100 rounded-xl p-5">
        <div class="flex items-start gap-4 mb-5">
            <div class="shrink-0 w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-brain text-green-600 text-2xl" aria-hidden="true"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">Hasil Klasifikasi CNN Terkini</h3>
                <p class="text-sm text-gray-600 mt-0.5">
                    Status:
                    <span class="font-semibold text-gray-800">
                        {{ $statusKlasifikasi ?? 'Belum ada pemeriksaan' }}
                    </span>
                </p>
                <p class="text-sm text-gray-400 mt-0.5">
                    Terakhir diperbarui:
                    {{ $terakhirDiperbarui ?? now()->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>

        {{-- Tombol sementara sampai halaman deteksi dibuat --}}
        <button type="button" data-belum-tersedia
            class="inline-block bg-[#1a5c2e] hover:bg-[#154d26] text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors cursor-pointer">
            Lihat Detail Pemeriksaan
        </button>
    </div>

    {{-- Baris Statistik --}}
    @php
        // Hitung sekali di atas biar markup di bawah tidak penuh logic match()
        $warnaLevelAkurasi = match($levelAkurasi ?? null) {
            'Tinggi' => 'text-green-600',
            'Sedang' => 'text-yellow-500',
            default => 'text-red-500',
        };
        $warnaLevelKepercayaan = match($levelKepercayaan ?? null) {
            'Ideal' => 'text-green-600',
            'Cukup' => 'text-yellow-500',
            default => 'text-red-500',
        };
        $warnaStatusDeteksi = ($statusDeteksi ?? null) === 'Terdeteksi' ? 'text-orange-500' : 'text-green-600';
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Jenis Penyakit --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-xs text-gray-500 font-semibold mb-3">Jenis Penyakit</p>
            <p class="text-base font-bold text-gray-900 mb-2 min-h-[1.5rem]">
                {{ $jenisPenyakit ?? '-' }}
            </p>
            @if($statusDeteksi ?? null)
                <span class="text-xs font-semibold {{ $warnaStatusDeteksi }}">
                    {{ $statusDeteksi }}
                </span>
            @else
                <span class="text-xs text-gray-400">-</span>
            @endif
        </div>

        {{-- Akurasi Model --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-xs text-gray-500 font-semibold mb-3">Akurasi Model</p>
            <p class="text-base font-bold text-gray-900 mb-2 min-h-[1.5rem]">
                {{ $akurasiModel !== null ? $akurasiModel . ' %' : '-' }}
            </p>
            @if($levelAkurasi ?? null)
                <span class="text-xs font-semibold {{ $warnaLevelAkurasi }}">
                    {{ $levelAkurasi }}
                </span>
            @else
                <span class="text-xs text-gray-400">-</span>
            @endif
        </div>

        {{-- Tingkat Kepercayaan --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-xs text-gray-500 font-semibold mb-3">Tingkat Kepercayaan</p>
            <p class="text-base font-bold text-gray-900 mb-2 min-h-[1.5rem]">
                {{ $tingkatKepercayaan !== null ? $tingkatKepercayaan . ' %' : '-' }}
            </p>
            @if($levelKepercayaan ?? null)
                <span class="text-xs font-semibold {{ $warnaLevelKepercayaan }}">
                    {{ $levelKepercayaan }}
                </span>
            @else
                <span class="text-xs text-gray-400">-</span>
            @endif
        </div>

        {{-- Rekomendasi Tindakan --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-semibold mb-3">Rekomendasi Tindakan</p>
            @if(!empty($rekomendasi))
                <ul class="space-y-1.5">
                    @foreach($rekomendasi as $item)
                        <li class="text-xs text-gray-700 leading-relaxed flex items-start gap-1.5">
                            <span class="text-green-500 shrink-0 mt-0.5" aria-hidden="true">•</span>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-xs text-gray-400">Lakukan pemeriksaan untuk mendapatkan rekomendasi.</p>
            @endif
        </div>
    </div>

    {{-- Riwayat Terbaru --}}
    @if(($riwayatTerbaru ?? null) && $riwayatTerbaru->count() > 0)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Riwayat Pemeriksaan Terbaru</h3>
                <button type="button" data-belum-tersedia
                    class="text-xs text-green-600 hover:underline font-medium">
                    Lihat semua
                </button>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($riwayatTerbaru as $riwayat)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $riwayat->jenis_penyakit ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ optional($riwayat->created_at)->translatedFormat('d M Y') ?? '-' }}
                            </p>
                        </div>
                        <span class="text-xs px-3 py-1 rounded-full font-semibold
                            {{ ($riwayat->status ?? '') === 'Terdeteksi'
                                ? 'bg-orange-50 text-orange-600'
                                : 'bg-green-50 text-green-700' }}">
                            {{ $riwayat->status ?? '-' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        {{-- Empty state --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-magnifying-glass text-gray-400 text-xl" aria-hidden="true"></i>
            </div>
            <p class="text-sm font-semibold text-gray-700 mb-1">Belum Ada Riwayat Pemeriksaan</p>
            <p class="text-xs text-gray-400 mb-4">Halaman deteksi sedang dalam pengembangan</p>
            <button type="button" data-belum-tersedia
                class="inline-block bg-[#1a5c2e] hover:bg-[#154d26] text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                Mulai Pemeriksaan
            </button>
        </div>
    @endif

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('login'))
            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil!',
                text: 'Selamat datang, {{ $user->name }}',
                showConfirmButton: false,
                timer: 2000
            });
        @endif

        // Satu event listener untuk semua tombol "belum tersedia",
        // menggantikan banyak atribut onclick inline.
        document.querySelectorAll('[data-belum-tersedia]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                Swal.fire({
                    icon: 'info',
                    title: 'Segera Hadir',
                    text: 'Halaman ini sedang dalam pengembangan.',
                    confirmButtonColor: '#1a5c2e',
                    confirmButtonText: 'Oke'
                });
            });
        });
    });
</script>
@endpush

@endsection