@extends('layouts.admin')

@section('pageTitle', 'Manajemen Riwayat Kesehatan')

@section('content')

<div class="space-y-6">

{{-- Header --}}
<div class="flex justify-between items-center">
    <div>
        <h2 class="text-xl font-bold text-gray-800">
            <i class="fa-solid fa-clipboard-pulse text-[#146135] mr-2"></i>
            Riwayat Kesehatan
        </h2>
    </div>
    <div class="flex gap-2">
        <button onclick="window.print()"
            class="flex items-center gap-1 px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-600">
            <i class="fa-solid fa-print"></i> Cetak
        </button>
        <a href="{{ route('admin.riwayat_kesehatan.export') }}"
            class="flex items-center gap-1 px-3 py-1.5 text-sm bg-[#146135] hover:bg-[#0f4a27] text-white rounded-lg">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
    </div>
</div>

{{-- Statistik Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
        <div class="bg-[#d1f0de] text-[#146135] rounded-lg p-3">
            <i class="fa-solid fa-layer-group text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Total Hasil</p>
            <p class="text-xl font-bold text-[#0d1f2d]">{{ $totalHasil ?? 0 }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
        <div class="bg-rose-100 text-rose-600 rounded-lg p-3">
            <i class="fa-solid fa-virus text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Penyakit Terbanyak</p>
            <p class="text-xl font-bold text-[#0d1f2d]">
                {{ $penyakitTerbanyak?->penyakit?->nama_penyakit ?? '-' }}
            </p>
            <p class="text-xs text-gray-400">
                {{ $penyakitTerbanyak?->total ?? 0 }} kasus
            </p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
        @php $rerata = $rataAkurasi ?? 0; @endphp
        <div class="rounded-lg p-3 {{ $rerata >= 80 ? 'bg-[#d1f0de] text-[#146135]' : ($rerata >= 60 ? 'bg-yellow-100 text-yellow-600' : 'bg-red-100 text-red-600') }}">
            <i class="fa-solid fa-chart-line text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Rata-rata Akurasi</p>
            <p class="text-xl font-bold text-[#0d1f2d]">{{ $rataAkurasi ?? '0.0' }}%</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
        <div class="bg-slate-100 text-slate-500 rounded-lg p-3">
            <i class="fa-solid fa-calendar-check text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Prediksi Hari Ini</p>
            <p class="text-xl font-bold text-[#0d1f2d]">{{ $prediksiHariIni ?? 0 }}</p>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="flex justify-between items-center px-5 py-4 border-b border-gray-100">
        <span class="font-semibold text-gray-700">Data Hasil Klasifikasi</span>
        <div class="flex gap-2">
            {{-- Filter by penyakit (dari database) --}}
            <select id="filterPenyakit"
                class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#3eb872]">
                <option value="">Semua Penyakit</option>
                @foreach($penyakitList ?? [] as $penyakit)
                    <option value="{{ strtolower($penyakit->nama_penyakit) }}">{{ $penyakit->nama_penyakit }}</option>
                @endforeach
            </select>
            {{-- Search by nama user / email --}}
            <div class="flex items-center border border-gray-200 rounded-lg px-3 py-1.5 gap-2">
                <i class="fa-solid fa-search text-gray-400 text-sm"></i>
                <input type="text" id="searchInput"
                    placeholder="Cari nama / email user..."
                    class="text-sm focus:outline-none w-44">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="tabelRiwayat">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-5 py-3 text-left">#</th>
                    <th class="px-5 py-3 text-left">User</th>
                    <th class="px-5 py-3 text-left">Gambar</th>
                    <th class="px-5 py-3 text-left">Penyakit</th>
                    <th class="px-5 py-3 text-left">Model</th>
                    <th class="px-5 py-3 text-left">Akurasi</th>
                    <th class="px-5 py-3 text-left">Hasil Prediksi</th>
                    <th class="px-5 py-3 text-left">Tanggal</th>
                    <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="tabelBody">
                @forelse($riwayat ?? [] as $index => $item)
                <tr class="hover:bg-gray-50"
                    data-penyakit="{{ strtolower($item->penyakit?->nama_penyakit ?? '') }}"
                    data-user="{{ strtolower($item->gambar?->user?->name ?? '') }}"
                    data-email="{{ strtolower($item->gambar?->user?->email ?? '') }}">
                    <td class="px-5 py-3 text-gray-400">{{ (isset($riwayat) && method_exists($riwayat, 'firstItem')) ? $riwayat->firstItem() + $index : $index + 1 }}</td>
                    <td class="px-5 py-3">
                        <p class="font-semibold text-gray-800">{{ $item->gambar?->user?->name ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $item->gambar?->user?->email ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-3">
                        @if($item->gambar?->nama_file)
                        <div class="w-12 h-12 rounded-lg overflow-hidden cursor-pointer hover:opacity-80"
                             onclick="lihatGambar('{{ asset('storage/' . $item->gambar->nama_file) }}')">
                          <img src="{{ asset('storage/' . $item->gambar->nama_file) }}"
                             alt="Gambar" class="w-full h-full object-cover"  onerror="this.parentElement.innerHTML='<div class=\'w-full h-full bg-gray-100 flex items-center justify-center\'><i class=\'fa-solid fa-image text-gray-400\'></i></div>'">
                        </div>
                        @else
                            <span class="text-gray-300 text-xs">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <span class="bg-red-100 text-red-600 text-xs font-medium px-2.5 py-1 rounded-full">
                            {{ $item->penyakit?->nama_penyakit ?? 'Tidak Diketahui' }}
                        </span>
                    </td>
                        <td class="px-5 py-3 text-gray-500">{{ $item->model?->nama_tampilan ?? '-' }}</td>                    <td class="px-5 py-3">
                        @php $akurasi = $item->tingkat_akurasi ?? 0; @endphp
                        <div class="flex items-center gap-2">
                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $akurasi >= 80 ? 'bg-[#3eb872]' : ($akurasi >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                    style="width: {{ min($akurasi, 100) }}%"></div>
                            </div>
                            <span class="font-semibold text-gray-700">{{ number_format($akurasi, 1) }}%</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $item->hasil_prediksi ?? '-' }}</td>
                    <td class="px-5 py-3">
                        <p class="text-gray-700">{{ \Carbon\Carbon::parse($item->tanggal_prediksi)->format('d M Y') }}</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($item->tanggal_prediksi)->format('H:i') }}</p>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex gap-1 justify-center">
                            <button onclick="lihatDetail({{ $item->id_hasil }})"
                                class="p-1.5 text-slate-500 hover:bg-slate-50 rounded-lg" title="Detail">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button onclick="hapusData({{ $item->id_hasil }})"
                                class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-12 text-gray-400">
                        <i class="fa-solid fa-inbox text-4xl mb-3 block opacity-30"></i>
                        Belum ada data riwayat klasifikasi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($riwayat) && method_exists($riwayat, 'links'))
    <div class="px-5 py-3 border-t border-gray-100 flex justify-between items-center">
        <p class="text-sm text-gray-500">
            Menampilkan {{ $riwayat->firstItem() }}–{{ $riwayat->lastItem() }} dari {{ $riwayat->total() }} data
        </p>
        {{ $riwayat->links() }}
    </div>
    @endif
</div>

{{-- Modal Detail --}}
<div id="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 z-50" style="display:none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="font-bold text-gray-800">
                    <i class="fa-solid fa-clipboard-list text-[#146135] mr-2"></i>Detail Hasil Klasifikasi
                </h3>
                <button onclick="tutupModal('modalDetail')" class="text-gray-400 hover:text-gray-600 text-xl">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div id="modalDetailBody" class="p-6"></div>
        </div>
    </div>
</div>

{{-- Modal Gambar --}}
<div id="modalGambar" class="fixed inset-0 bg-black bg-opacity-80 z-50" style="display:none;"
    onclick="tutupModal('modalGambar')">
    <div class="flex items-center justify-center min-h-screen">
        <img id="gambarPreview" src="" alt="Preview" class="max-w-lg max-h-screen rounded-xl shadow-2xl">
    </div>
</div>

{{-- Form Hapus --}}
<form id="formHapus" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

{{-- SweetAlert2 (kalau sudah ada di layouts/admin.blade.php, baris ini boleh dihapus biar tidak double-load) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Search hanya berdasarkan nama user & email
    function filterTable() {
        const search   = document.getElementById('searchInput').value.toLowerCase().trim();
        const penyakit = document.getElementById('filterPenyakit').value.toLowerCase();
        const rows     = document.querySelectorAll('#tabelBody tr');

        rows.forEach(row => {
            const rowUser     = row.getAttribute('data-user')    || '';
            const rowEmail    = row.getAttribute('data-email')   || '';
            const rowPenyakit = row.getAttribute('data-penyakit') || '';

            const matchSearch   = search === '' || rowUser.includes(search) || rowEmail.includes(search);
            const matchPenyakit = penyakit === '' || rowPenyakit === penyakit;

            row.style.display = (matchSearch && matchPenyakit) ? '' : 'none';
        });
    }

    document.getElementById('searchInput').addEventListener('keyup', filterTable);
    document.getElementById('filterPenyakit').addEventListener('change', filterTable);

    // Lihat gambar
    function lihatGambar(url) {
        document.getElementById('gambarPreview').src = url;
        document.getElementById('modalGambar').style.display = 'block';
    }

    // Tutup modal
    function tutupModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    // Lihat detail
    function lihatDetail(id) {
        document.getElementById('modalDetail').style.display = 'block';
        document.getElementById('modalDetailBody').innerHTML = `
            <div class="text-center py-8">
                <i class="fa-solid fa-spinner fa-spin text-[#146135] text-2xl"></i>
            </div>`;

        fetch(`/admin/riwayat-kesehatan/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('modalDetailBody').innerHTML = `
                <div class="flex gap-4">
                    <div class="w-40 flex-shrink-0">
                        ${data.path_gambar
                            ? `<img src="/storage/${data.path_gambar}" class="w-full h-40 object-cover rounded-xl">`
                            : `<div class="w-full h-40 bg-gray-100 rounded-xl flex items-center justify-center text-gray-300"><i class="fa-solid fa-image text-3xl"></i></div>`
                        }
                    </div>
                    <div class="flex-1">
                        <table class="w-full text-sm">
                            <tr class="border-b"><td class="py-2 text-gray-400 w-40">ID Hasil</td><td class="py-2 font-medium">${data.id_hasil}</td></tr>
                            <tr class="border-b"><td class="py-2 text-gray-400">User</td><td class="py-2 font-medium">${data.user_name}</td></tr>
                            <tr class="border-b"><td class="py-2 text-gray-400">Email</td><td class="py-2">${data.user_email}</td></tr>
                            <tr class="border-b"><td class="py-2 text-gray-400">Penyakit</td><td class="py-2"><span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">${data.nama_penyakit}</span></td></tr>
                            <tr class="border-b"><td class="py-2 text-gray-400">Model</td><td class="py-2">${data.nama_model}</td></tr>
                            <tr class="border-b"><td class="py-2 text-gray-400">Akurasi</td><td class="py-2 font-bold">${data.tingkat_akurasi}%</td></tr>
                            <tr class="border-b"><td class="py-2 text-gray-400">Hasil Prediksi</td><td class="py-2">${data.hasil_prediksi}</td></tr>
                            <tr><td class="py-2 text-gray-400">Tanggal</td><td class="py-2">${data.tanggal_prediksi}</td></tr>
                        </table>
                    </div>
                </div>`;
        })
        .catch(() => {
            document.getElementById('modalDetailBody').innerHTML =
                `<p class="text-red-500 text-center py-4">Gagal memuat data.</p>`;
        });
    }

    // Hapus data
    function hapusData(id) {
        Swal.fire({
            title: 'Hapus data ini?',
            text: 'Data hasil klasifikasi yang dihapus tidak bisa dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#146135',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formHapus');
                form.action = `/admin/riwayat-kesehatan/${id}`;
                form.submit();
            }
        });
    }
</script>

@endsection