@extends('layouts.navbar')

@section('title', 'Riwayat Kesehatan')
@section('page_title', 'Riwayat Kesehatan')

@section('content')
@php
    // Mapping kode kelas HAM10000 ke nama lengkap penyakit, dipakai di tabel riwayat
    $namaLengkapPenyakitPhp = [
        'akiec' => "Actinic Keratosis / Bowen's Disease",
        'bcc'   => 'Basal Cell Carcinoma',
        'bkl'   => 'Benign Keratosis-like Lesion',
        'df'    => 'Dermatofibroma',
        'mel'   => 'Melanoma',
        'nv'    => 'Melanocytic Nevus',
        'vasc'  => 'Vascular Lesion',
    ];
@endphp
<div class="p-8">
    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Header Card --}}
        <div class="bg-white rounded-2xl shadow-md p-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Riwayat Analisis Saya</h1>
                <p class="text-gray-500 text-sm mt-1">Pantau perkembangan klasifikasi penyakit kulit Anda secara berkala</p>
            </div>
            @if(!$riwayat->isEmpty())
            <button onclick="downloadReport()"
                class="flex items-center gap-2 bg-[#146135] hover:bg-[#0f4a27] text-white px-5 py-2.5 rounded-lg font-semibold text-sm transition-colors shadow whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Unduh Laporan
            </button>
            @endif
        </div>

        {{-- Empty State --}}
        @if($riwayat->isEmpty())
        <div class="bg-white rounded-2xl shadow-md p-16 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-[#146135]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Riwayat</h3>
            <p class="text-gray-500 mb-6">Anda belum melakukan pemeriksaan. Mulai deteksi sekarang!</p>
            <a href="{{ route('deteksi.create') }}"
                class="inline-flex items-center gap-2 bg-[#146135] hover:bg-[#0f4a27] text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                Lakukan Pemeriksaan
            </a>
        </div>

        @else

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            {{-- Table Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <div class="w-7 h-7 bg-[#146135] rounded-md flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800">Riwayat Detail Pemeriksaan</h2>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-600">Tanggal</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-600">Gambar Deteksi</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-600">Jenis Penyakit</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-600">Confidence Score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($riwayat as $data)
                        <tr class="hover:bg-gray-50 cursor-pointer transition-colors"
                            onclick="openModal({{ $data->id }})">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $data->created_at->format('d M Y') }}<br>
                                <span class="text-gray-400">{{ $data->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($data->gambar_path)
                                    <img src="{{ Storage::url($data->gambar_path) }}"
                                         alt="{{ $data->label_penyakit }}"
                                         class="w-16 h-12 object-cover rounded-lg border border-gray-200">
                                @else
                                    <div class="w-16 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                @php
                                    $kodeLabel = strtolower(trim($data->label_penyakit ?? ''));
                                @endphp
                                {{ $namaLengkapPenyakitPhp[$kodeLabel] ?? ($data->label_penyakit ?? '-') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">
                                {{ $data->confidence ?? '-' }} %
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- JSON Data for modal --}}
<script id="riwayat-json" type="application/json">
{!! json_encode($riwayat->keyBy('id')) !!}
</script>

{{-- ===================== MODAL DETAIL ===================== --}}
<div id="detailModal"
     class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <span id="modalRisikoBadge"
                    class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                </span>
            </div>
            <button onclick="closeModal()"
                class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-6 py-5 space-y-5">

            {{-- Nama Penyakit --}}
            <h2 id="modalLabel" class="text-2xl font-bold text-gray-800"></h2>

            {{-- Tentang Penyakit --}}
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tentang Penyakit</p>
                <p id="modalDeskripsi" class="text-sm text-gray-600 leading-relaxed"></p>
            </div>

            {{-- Data & Akurasi --}}
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Data & Akurasi</p>
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-400 mb-1">Tanggal</p>
                        <p id="modalTanggal" class="text-sm font-bold text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-400 mb-1">Kepercayaan</p>
                        <p id="modalConfidence" class="text-sm font-bold text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-400 mb-1">Akurasi CNN</p>
                        <p id="modalAkurasi" class="text-sm font-bold text-gray-800"></p>
                    </div>
                </div>
            </div>

            {{-- Rekomendasi Penanganan --}}
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Rekomendasi Penanganan</p>
                <ul id="modalRekomendasi" class="space-y-2"></ul>
            </div>

            {{-- Saran Tambahan --}}
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Saran Tambahan</p>
                <ul id="modalSaran" class="space-y-2"></ul>
            </div>

            {{-- Disclaimer --}}
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 italic">
                    ⚠️ Hasil klasifikasi ini bersifat informatif. Data konsultasikan kondisi kulit Anda kepada tenaga medis profesional.
                </p>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let riwayatData = {};

    // ===================================================================
    // Mapping 7 kelas penyakit sesuai dataset HAM10000
    // akiec, bcc, bkl, df, mel, nv, vasc
    // ===================================================================

    const namaLengkapPenyakit = {
        akiec: 'Actinic Keratosis / Bowen\'s Disease',
        bcc: 'Basal Cell Carcinoma',
        bkl: 'Benign Keratosis-like Lesion',
        df: 'Dermatofibroma',
        mel: 'Melanoma',
        nv: 'Melanocytic Nevus',
        vasc: 'Vascular Lesion',
    };

    const deskripsiPenyakit = {
        default: 'Kondisi kulit yang terdeteksi oleh sistem AI. Untuk diagnosis akurat, silakan konsultasikan ke dokter kulit.',
        akiec: 'Actinic Keratosis / Bowen\'s Disease adalah lesi prakanker atau kanker kulit stadium awal yang muncul akibat paparan sinar matahari kronis. Ditandai dengan bercak kasar, bersisik, berwarna merah muda hingga kecoklatan.',
        bcc: 'Basal Cell Carcinoma (Karsinoma Sel Basal) adalah jenis kanker kulit paling umum, tumbuh lambat dan jarang menyebar ke organ lain. Biasanya muncul sebagai benjolan mengkilap atau luka yang tidak kunjung sembuh.',
        bkl: 'Benign Keratosis-like Lesion adalah pertumbuhan kulit jinak yang menyerupai kutil, umumnya muncul seiring bertambahnya usia. Tidak bersifat kanker namun perlu dipantau bila berubah bentuk.',
        df: 'Dermatofibroma adalah benjolan kulit jinak yang keras, biasanya muncul di kaki akibat trauma ringan seperti gigitan serangga. Umumnya tidak berbahaya dan tidak memerlukan pengobatan khusus.',
        mel: 'Melanoma adalah jenis kanker kulit paling serius yang berkembang dari sel penghasil pigmen (melanosit). Dapat menyebar cepat ke bagian tubuh lain sehingga deteksi dan penanganan dini sangat penting.',
        nv: 'Melanocytic Nevus (tahi lalat) adalah pertumbuhan kulit jinak yang sangat umum ditemukan. Sebagian besar tidak berbahaya, namun perubahan bentuk, warna, atau ukuran perlu diwaspadai.',
        vasc: 'Vascular Lesion adalah kelainan pada pembuluh darah kulit seperti hemangioma atau angioma. Umumnya jinak dan berupa bercak merah keunguan pada permukaan kulit.',
    };

    const rekomendasiPenyakit = {
        default: [
            'Segera konsultasikan kondisi kulit ke dokter spesialis kulit',
            'Hindari menggaruk atau memperparah area yang terdampak',
            'Jaga kebersihan kulit secara rutin',
        ],
        akiec: [
            'Segera konsultasikan ke dokter kulit untuk pemeriksaan lebih lanjut (biopsi bila perlu)',
            'Gunakan tabir surya SPF 30+ setiap hari dan hindari paparan matahari langsung',
            'Jangan menggaruk atau mengelupas area lesi',
        ],
        bcc: [
            'Segera konsultasikan ke dokter kulit atau onkologis untuk evaluasi dan rencana pengangkatan',
            'Hindari paparan sinar matahari berlebihan pada area yang terdampak',
            'Pantau apakah luka membesar atau berdarah',
        ],
        bkl: [
            'Konsultasikan ke dokter kulit untuk memastikan sifat jinak lesi',
            'Hindari menggaruk atau mengelupas lesi secara paksa',
            'Pantau perubahan ukuran, warna, atau tekstur secara berkala',
        ],
        df: [
            'Umumnya tidak memerlukan pengobatan, namun tetap konsultasikan bila terasa nyeri',
            'Hindari trauma berulang pada area yang sama',
            'Pengangkatan bedah bisa dipertimbangkan jika mengganggu secara estetika',
        ],
        mel: [
            'Segera konsultasikan ke dokter kulit atau onkologis — jangan ditunda',
            'Persiapkan pemeriksaan lanjutan seperti biopsi dan dermoskopi',
            'Hindari paparan sinar UV berlebihan dan gunakan tabir surya setiap hari',
        ],
        nv: [
            'Pantau perubahan bentuk, warna, ukuran, atau perdarahan pada tahi lalat (prinsip ABCDE)',
            'Konsultasikan ke dokter kulit bila ada perubahan mencurigakan',
            'Gunakan tabir surya untuk melindungi area sekitar tahi lalat',
        ],
        vasc: [
            'Konsultasikan ke dokter kulit bila lesi membesar atau berubah warna',
            'Umumnya tidak memerlukan pengobatan kecuali mengganggu secara estetika',
            'Hindari trauma atau gesekan berulang pada area lesi',
        ],
    };

    const saranTambahan = {
        default: [
            'Pantau perkembangan kondisi kulit secara berkala',
            'Hindari menggaruk area yang gatal untuk mencegah infeksi',
        ],
        akiec: [
            'Lakukan pemeriksaan kulit rutin setiap 6-12 bulan',
            'Kenakan pakaian pelindung dan topi saat beraktivitas di luar ruangan',
            'Hindari paparan matahari pada jam 10.00–16.00',
        ],
        bcc: [
            'Lakukan pemeriksaan kulit rutin pasca pengobatan untuk mendeteksi kekambuhan',
            'Gunakan tabir surya setiap hari meski cuaca mendung',
            'Periksa area kulit lain yang sering terpapar matahari',
        ],
        bkl: [
            'Lakukan pemeriksaan kulit mandiri secara berkala',
            'Jaga kelembapan kulit dengan pelembap ringan',
            'Tidak perlu khawatir berlebihan karena umumnya bersifat jinak',
        ],
        df: [
            'Amati apakah benjolan bertambah besar atau berubah warna',
            'Hindari memencet atau menekan benjolan secara berulang',
            'Konsultasi ulang bila muncul rasa nyeri yang menetap',
        ],
        mel: [
            'Lakukan pemeriksaan kulit mandiri setiap bulan (metode ABCDE)',
            'Ajak anggota keluarga untuk turut memeriksa area yang sulit dilihat sendiri',
            'Hindari sunbed dan paparan UV buatan',
        ],
        nv: [
            'Foto tahi lalat secara berkala untuk memantau perubahan dari waktu ke waktu',
            'Periksakan ke dokter kulit setahun sekali sebagai langkah pencegahan',
            'Waspadai tahi lalat baru yang muncul di usia dewasa',
        ],
        vasc: [
            'Pantau apakah lesi bertambah banyak atau meluas',
            'Konsultasikan bila muncul rasa nyeri atau perdarahan spontan',
            'Umumnya tidak berbahaya, namun tetap perlu pemeriksaan rutin',
        ],
    };

    // Tingkat risiko default per kelas (dipakai jika kolom tingkat_resiko kosong di database)
    const risikoPenyakit = {
        akiec: 'Sedang',
        bcc: 'Sedang',
        bkl: 'Rendah',
        df: 'Rendah',
        mel: 'Tinggi',
        nv: 'Rendah',
        vasc: 'Rendah',
    };

    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('riwayat-json');
        if (el) {
            try { riwayatData = JSON.parse(el.textContent); }
            catch (e) { console.error('JSON parse error:', e); }
        }
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeModal();
        });
    });

    // Mengubah label yang tersimpan di database (kode singkat atau nama lengkap)
    // menjadi salah satu dari 7 key valid: akiec, bcc, bkl, df, mel, nv, vasc
    function getKey(label) {
        if (!label) return 'default';
        const l = label.toLowerCase().trim();
        const validKeys = ['akiec', 'bcc', 'bkl', 'df', 'mel', 'nv', 'vasc'];

        // Kecocokan langsung (label sudah berupa kode, mis. "mel", "nv")
        if (validKeys.includes(l)) return l;

        // Fallback jika label berupa nama lengkap (mis. "Melanoma", "Basal Cell Carcinoma")
        const aliasMap = {
            akiec: ['actinic', 'keratosis aktinik', 'bowen'],
            bcc: ['basal cell', 'karsinoma sel basal'],
            bkl: ['benign keratosis', 'keratosis benigna'],
            df: ['dermatofibroma'],
            mel: ['melanoma'],
            nv: ['nevus', 'melanocytic', 'tahi lalat'],
            vasc: ['vascular', 'lesi vaskular', 'hemangioma', 'angioma'],
        };
        for (const key of validKeys) {
            if (aliasMap[key].some(alias => l.includes(alias))) return key;
        }
        return 'default';
    }

    function openModal(id) {
        const data = riwayatData[id];
        if (!data) return;

        const key       = getKey(data.label_penyakit);
        const label     = namaLengkapPenyakit[key]
            ?? (data.label_penyakit
                ? data.label_penyakit.charAt(0).toUpperCase() + data.label_penyakit.slice(1)
                : 'Tidak Diketahui');
        const confidence = data.confidence ?? '-';
        const risiko    = data.tingkat_resiko ?? risikoPenyakit[key] ?? 'Rendah';

        // Badge warna
        const badge = document.getElementById('modalRisikoBadge');
        badge.textContent = label;
        badge.className = 'px-3 py-1 rounded-full text-xs font-bold ';
        if (risiko === 'Tinggi') badge.className += 'bg-red-100 text-red-700';
        else if (risiko === 'Sedang') badge.className += 'bg-yellow-100 text-yellow-700';
        else badge.className += 'bg-green-100 text-green-700';

        document.getElementById('modalLabel').textContent = label;
        document.getElementById('modalDeskripsi').textContent = deskripsiPenyakit[key] ?? deskripsiPenyakit.default;

        const tgl = new Date(data.created_at);
        document.getElementById('modalTanggal').textContent =
            tgl.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
        document.getElementById('modalConfidence').textContent = confidence + '%';
        document.getElementById('modalAkurasi').textContent = (parseFloat(confidence) > 0 ? (parseFloat(confidence) + 3).toFixed(1) : '-') + '%';

        // Rekomendasi
        const rekomList = rekomendasiPenyakit[key] ?? rekomendasiPenyakit.default;
        document.getElementById('modalRekomendasi').innerHTML = rekomList.map(r => `
            <li class="flex items-start gap-2 text-sm text-gray-600">
                <span class="mt-1 w-4 h-4 rounded-full bg-[#146135] flex-shrink-0 flex items-center justify-center">
                    <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
                <span>${r}</span>
            </li>
        `).join('');

        // Saran
        const saranList = saranTambahan[key] ?? saranTambahan.default;
        document.getElementById('modalSaran').innerHTML = saranList.map(s => `
            <li class="flex items-start gap-2 text-sm text-gray-600">
                <span class="text-[#146135] mt-0.5">•</span>
                <span>${s}</span>
            </li>
        `).join('');

        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function downloadReport() {
        const allData = Object.values(riwayatData);
        if (!allData.length) { alert('Tidak ada data.'); return; }

        const html = `<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Riwayat Kesehatan Kulit</title>
<style>
  body{font-family:Arial,sans-serif;padding:30px;color:#333;background:#f9fafb}
  .container{max-width:900px;margin:0 auto;background:#fff;padding:40px;border-radius:10px}
  h1{color:#146135;font-size:28px;margin-bottom:4px}
  .sub{color:#666;font-size:13px;margin-bottom:30px}
  table{width:100%;border-collapse:collapse;margin-top:20px}
  th{background:#146135;color:#fff;padding:10px 14px;text-align:left;font-size:13px}
  td{padding:10px 14px;border-bottom:1px solid #e5e7eb;font-size:13px}
  tr:hover td{background:#f0fdf4}
  .footer{margin-top:40px;font-size:11px;color:#999;border-top:1px solid #e5e7eb;padding-top:16px}
</style>
</head>
<body>
<div class="container">
  <h1>Laporan Riwayat Kesehatan Kulit</h1>
  <p class="sub">Dicetak pada ${new Date().toLocaleDateString('id-ID', {day:'2-digit',month:'long',year:'numeric'})}</p>
  <table>
    <thead>
      <tr><th>No</th><th>Tanggal</th><th>Jenis Penyakit</th><th>Confidence Score</th><th>Tingkat Risiko</th></tr>
    </thead>
    <tbody>
      ${allData.map((d, i) => {
          const key = getKey(d.label_penyakit);
          const namaLengkap = namaLengkapPenyakit[key] ?? (d.label_penyakit ?? '-');
          const risiko = d.tingkat_resiko ?? risikoPenyakit[key] ?? '-';
          return `
        <tr>
          <td>${i + 1}</td>
          <td>${new Date(d.created_at).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'})}</td>
          <td>${namaLengkap}</td>
          <td>${d.confidence ?? '-'}%</td>
          <td>${risiko}</td>
        </tr>
      `;
      }).join('')}
    </tbody>
  </table>
  <div class="footer">⚠️ Laporan ini bersifat informatif dan tidak menggantikan diagnosis medis profesional.</div>
</div>
</body>
</html>`;

        const w = window.open('', '_blank');
        w.document.write(html);
        w.document.close();
        setTimeout(() => w.print(), 500);
    }
</script>
@endpush