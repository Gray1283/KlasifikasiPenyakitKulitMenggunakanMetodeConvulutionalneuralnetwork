@extends('layouts.admin')

@section('pageTitle', 'Dashboard Admin')

@section('content')
<div class="space-y-6">

    {{-- ================= STAT CARDS ================= --}}
    <div class="grid grid-cols-3 gap-4">

        <div class="bg-white rounded-2xl px-5 py-4 border border-gray-100 flex items-center gap-4">
            <div class="bg-green-100 text-green-700 p-3 rounded-lg">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Total User</p>
                <p class="text-2xl font-semibold" style="color:#146135;">{{ $totalUser ?? 0 }}</p>
                @if(isset($userBaruMingguIni))
                    <p class="text-[11px] text-green-600 mt-0.5">+{{ $userBaruMingguIni }} minggu ini</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl px-5 py-4 border border-gray-100 flex items-center gap-4">
            <div class="bg-teal-100 text-teal-700 p-3 rounded-lg">
                <i class="fas fa-notes-medical"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Total Deteksi</p>
                <p class="text-2xl font-semibold" style="color:#146135;">{{ $totalHasilCnn ?? 0 }}</p>
                @if(isset($deteksiHariIni))
                    <p class="text-[11px] text-teal-600 mt-0.5">{{ $deteksiHariIni }} hari ini</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl px-5 py-4 border border-gray-100 flex items-center gap-4">
            <div class="bg-amber-100 text-amber-600 p-3 rounded-lg">
                <i class="fas fa-brain"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Akurasi Model</p>
                <p class="text-2xl font-semibold" style="color:#146135;">{{ isset($akurasiModel) ? round($akurasiModel, 1).'%' : '-' }}</p>
                @if(isset($trainingTerakhir))
                    <p class="text-[11px] text-gray-400 mt-0.5">Training {{ $trainingTerakhir }}</p>
                @endif
            </div>
        </div>

    </div>

    <div class="grid grid-cols-3 gap-6">

        {{-- ================= CHART DISTRIBUSI PENYAKIT ================= --}}
        <div class="col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm font-medium text-gray-600 mb-4">Distribusi Penyakit Terdeteksi</p>
            @if(isset($distribusiPenyakit) && count($distribusiPenyakit) > 0)
                <canvas id="chartPenyakit" height="140"></canvas>
            @else
                <div class="py-12 text-center">
                    <i class="fas fa-chart-simple text-3xl text-gray-200 mb-3 block"></i>
                    <p class="text-gray-400 text-sm">Belum ada data deteksi untuk ditampilkan.</p>
                </div>
            @endif
        </div>

        {{-- ================= STATUS MODEL AI ================= --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-50">
                <p class="text-sm font-medium text-gray-600">Status Model AI</p>
            </div>
            <div class="p-5 flex flex-col gap-4">

                @if($namaModelAktif ?? false)
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">Model Aktif</span>
                    <span class="text-sm font-semibold text-gray-700">{{ $namaModelAktif }}</span>
                </div>
                @endif

                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">Server Flask</span>
                    @if(($flaskOnline ?? false))
                        <span class="flex items-center gap-1.5 text-xs font-medium text-green-700">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> Online
                        </span>
                    @else
                        <span class="flex items-center gap-1.5 text-xs font-medium text-red-500">
                            <span class="w-2 h-2 rounded-full bg-red-400"></span> Offline
                        </span>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">Akurasi Model</span>
                    <span class="text-sm font-semibold text-gray-700">{{ isset($akurasiModel) ? round($akurasiModel, 1).'%' : '-' }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">Training Terakhir</span>
                    <span class="text-sm font-semibold text-gray-700">{{ $trainingTerakhir ?? '-' }}</span>
                </div>

                <div class="pt-2 border-t border-gray-50 flex flex-col gap-2">
                    <a href="{{ route('admin.training.index') }}"
                       class="flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-white transition-colors hover:opacity-90"
                       style="background:#146135;">
                        <i class="fas fa-microchip"></i> Jalankan Training
                    </a>
                    <a href="{{ route('admin.evaluasi.index') }}"
                       class="flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-green-700 border border-green-200 hover:bg-green-50 transition-colors">
                        <i class="fas fa-chart-pie"></i> Evaluasi Model
                    </a>
                </div>

            </div>
        </div>

    </div>

    <div class="grid grid-cols-3 gap-6">

        {{-- ================= TREN DETEKSI 7 HARI ================= --}}
        <div class="col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm font-medium text-gray-600 mb-4">Tren Deteksi — 7 Hari Terakhir</p>
            @if(isset($trenMingguan) && count($trenMingguan) > 0)
                <canvas id="chartTren" height="120"></canvas>
            @else
                <div class="py-10 text-center">
                    <i class="fas fa-chart-line text-3xl text-gray-200 mb-3 block"></i>
                    <p class="text-gray-400 text-sm">Belum ada data tren untuk ditampilkan.</p>
                </div>
            @endif
        </div>

        {{-- ================= AKTIVITAS TERBARU ================= --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                <p class="text-sm font-medium text-gray-600">Aktivitas Terbaru</p>
                <a href="{{ route('admin.riwayat_kesehatan.index') }}" class="text-xs text-green-700 hover:underline">
                    Lihat semua
                </a>
            </div>

            @if(isset($riwayatTerbaru) && count($riwayatTerbaru) > 0)
                <div class="divide-y divide-gray-50">
                    @foreach($riwayatTerbaru as $item)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-700">{{ $item->gambarKulit->user->name ?? '-' }}</p>
                            <p class="text-[11px] text-gray-400">{{ $item->hasil_prediksi ?? '-' }}</p>
                        </div>
                        <span class="text-[11px] text-gray-400">{{ $item->tanggal_prediksi?->diffForHumans() ?? '-' }}</span>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="px-5 py-10 text-center">
                    <i class="fas fa-clock-rotate-left text-3xl text-gray-200 mb-3 block"></i>
                    <p class="text-gray-400 text-sm">Belum ada aktivitas deteksi terbaru.</p>
                </div>
            @endif
        </div>

    </div>

</div>

@if((isset($distribusiPenyakit) && count($distribusiPenyakit) > 0) || (isset($trenMingguan) && count($trenMingguan) > 0))
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    @if(isset($distribusiPenyakit) && count($distribusiPenyakit) > 0)
    new Chart(document.getElementById('chartPenyakit'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($distribusiPenyakit)) !!},
            datasets: [{
                label: 'Jumlah Deteksi',
                data: {!! json_encode(array_values($distribusiPenyakit)) !!},
                backgroundColor: '#146135',
                borderRadius: 8,
                maxBarThickness: 40
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });
    @endif

    @if(isset($trenMingguan) && count($trenMingguan) > 0)
    new Chart(document.getElementById('chartTren'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($trenMingguan)) !!},
            datasets: [{
                label: 'Jumlah Deteksi',
                data: {!! json_encode(array_values($trenMingguan)) !!},
                borderColor: '#146135',
                backgroundColor: 'rgba(20,97,53,0.08)',
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#146135'
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });
    @endif

});
</script>
@endpush
@endif
@endsection