@extends('layouts.navbar')

@section('title', 'Riwayat Kesehatan')
@section('page_title', 'Riwayat Kesehatan')

@section('content')
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
                            <td class="px-6 py-4 text-sm text-gray-700 capitalize">
                                {{ $data->label_penyakit ?? '-' }}
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

    const deskripsiPenyakit = {
        default: 'Kondisi kulit yang terdeteksi oleh sistem AI. Untuk diagnosis akurat, silakan konsultasikan ke dokter kulit.',
        lupus: 'Lupus adalah penyakit autoimun yang dapat menyerang kulit, sendi, dan organ lain. Kondisi ini ditandai dengan ruam kupu-kupu di wajah dan sensitivitas terhadap cahaya matahari.',
        scabies: 'Scabies adalah infeksi kulit yang disebabkan oleh tungau Sarcoptes scabiei. Kondisi ini menyebabkan gatal hebat terutama pada malam hari dan dapat menyebar melalui kontak langsung.',
        dermatitis: 'Dermatitis kontak adalah peradangan kulit yang terjadi akibat kontak langsung dengan alergen atau iritan tertentu. Kondisi ini ditandai dengan kemerahan, gatal, dan terkadang lepuhan.',
        melanoma: 'Melanoma adalah jenis kanker kulit yang berkembang dari sel penghasil pigmen (melanosit). Deteksi dini sangat penting untuk penanganan yang efektif.',
        psoriasis: 'Psoriasis adalah penyakit kulit kronis yang menyebabkan sel kulit tumbuh terlalu cepat, membentuk bercak tebal berwarna merah dengan sisik perak.',
        eksim: 'Eksim (atopic dermatitis) adalah kondisi kulit yang menyebabkan kulit menjadi merah, gatal, dan meradang. Sering muncul pada anak-anak namun bisa terjadi di segala usia.',
    };

    const rekomendasiPenyakit = {
        default: [
            'Segera konsultasikan kondisi kulit ke dokter spesialis kulit',
            'Hindari menggaruk atau memperparah area yang terdampak',
            'Jaga kebersihan kulit secara rutin',
        ],
        lupus: [
            'Segera konsultasikan ke dokter kulit atau reumatologis',
            'Gunakan tabir surya SPF tinggi setiap hari',
            'Hindari paparan sinar matahari langsung terutama saat terik',
        ],
        scabies: [
            'Gunakan krim permethrin 5% yang diresepkan dokter ke seluruh tubuh',
            'Cuci semua pakaian, handuk, dan sprei dengan air panas',
            'Seluruh anggota keluarga perlu diperiksa dan diobati bersamaan',
        ],
        dermatitis: [
            'Segera hentikan kontak dengan bahan yang diduga menjadi pemicu',
            'Oleskan krim kortikosteroid ringan (Hydrocortisone 1%) pada area yang meradang sesuai anjuran dokter',
            'Gunakan sarung tangan pelindung jika harus bersentuhan dengan bahan kimia atau detergen',
        ],
        melanoma: [
            'Segera konsultasikan ke dokter kulit atau onkologis',
            'Jangan menunda pemeriksaan lebih lanjut',
            'Hindari paparan sinar UV berlebihan',
        ],
        psoriasis: [
            'Gunakan pelembap secara rutin untuk mengurangi kekeringan',
            'Oleskan krim kortikosteroid sesuai resep dokter',
            'Kelola stres karena dapat memperburuk kondisi',
        ],
        eksim: [
            'Gunakan pelembap bebas pewangi (fragrance-free) setidaknya dua kali sehari',
            'Pilih produk perawatan kulit berlabel hypoallergenic',
            'Hindari pemicu seperti wol, sabun keras, dan perubahan suhu ekstrem',
        ],
    };

    const saranTambahan = {
        default: [
            'Pantau perkembangan kondisi kulit secara berkala',
            'Hindari menggaruk area yang gatal untuk mencegah infeksi',
        ],
        lupus: [
            'Ikuti pemeriksaan darah rutin sesuai anjuran dokter',
            'Hindari stres berlebihan yang dapat memicu flare',
            'Konsumsi makanan bergizi dan istirahat cukup',
        ],
        scabies: [
            'Jika gatal tidak mereda setelah 2-4 minggu, kunjungi dokter kembali',
            'Hindari berbagi barang pribadi seperti handuk atau pakaian',
            'Bersihkan furnitur dengan vacuum cleaner secara menyeluruh',
        ],
        dermatitis: [
            'Gunakan pelembap bebas pewangi (fragrance-free) setidaknya dua kali sehari untuk menjaga kelembapan kulit',
            'Pilih produk perawatan kulit berlabel hypoallergenic',
            'Jika gejala tidak membaik dalam 1-2 minggu, segera berkonsultasi dokter kulit',
            'Hindari menggaruk area yang gatal untuk mencegah infeksi sekunder',
        ],
        melanoma: [
            'Lakukan pemeriksaan kulit mandiri setiap bulan',
            'Gunakan tabir surya setiap hari meski cuaca mendung',
            'Hindari sunbed dan paparan UV buatan',
        ],
        psoriasis: [
            'Hindari alkohol dan rokok yang dapat memperparah kondisi',
            'Mandi dengan air hangat, bukan panas',
            'Ikuti program terapi cahaya (phototherapy) jika direkomendasikan dokter',
        ],
        eksim: [
            'Ikuti pola tidur dan istirahat yang teratur',
            'Hindari perubahan suhu ekstrem dan keringat berlebihan',
            'Gunakan pakaian berbahan katun yang lembut',
        ],
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

    function getKey(label) {
        if (!label) return 'default';
        const l = label.toLowerCase();
        for (const key of Object.keys(deskripsiPenyakit)) {
            if (key !== 'default' && l.includes(key)) return key;
        }
        return 'default';
    }

    function openModal(id) {
        const data = riwayatData[id];
        if (!data) return;

        const key       = getKey(data.label_penyakit);
        const label     = data.label_penyakit
            ? data.label_penyakit.charAt(0).toUpperCase() + data.label_penyakit.slice(1)
            : 'Tidak Diketahui';
        const confidence = data.confidence ?? '-';
        const risiko    = data.tingkat_resiko ?? 'Rendah';

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
      ${allData.map((d, i) => `
        <tr>
          <td>${i + 1}</td>
          <td>${new Date(d.created_at).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'})}</td>
          <td style="text-transform:capitalize">${d.label_penyakit ?? '-'}</td>
          <td>${d.confidence ?? '-'}%</td>
          <td>${d.tingkat_resiko ?? '-'}</td>
        </tr>
      `).join('')}
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