@extends('layouts.admin')

@section('pageTitle', 'Manajemen Dataset')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">
            <i class="fa-solid fa-database text-[#146135] mr-2"></i>
            Dataset HAM10000
        </h2>
        <button onclick="document.getElementById('modalZip').style.display='block'"
            class="px-4 py-2 text-sm bg-[#146135] text-white rounded-lg hover:bg-[#0f4a27] flex items-center gap-2">
            <i class="fa-solid fa-file-zipper"></i>
            Upload ZIP
        </button>
    </div>

    {{-- Status Flask --}}
    @if(!$flaskOnline)
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl text-sm">
        <i class="fa-solid fa-triangle-exclamation mr-2"></i>
        Flask API offline — jumlah gambar diambil dari folder lokal. Pastikan Flask berjalan untuk upload ZIP.
    </div>
    @endif

    {{-- Alert --}}
    @if(session('success'))
    <div class="bg-[#f0faf4] border border-[#d1f0de] text-[#146135] px-4 py-3 rounded-xl text-sm">
        <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        <i class="fa-solid fa-triangle-exclamation mr-2"></i>{{ session('error') }}
    </div>
    @endif

    {{-- Card Statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
            <div class="bg-[#d1f0de] text-[#146135] rounded-lg p-3">
                <i class="fa-solid fa-images text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Gambar</p>
                <p class="text-xl font-bold text-[#0d1f2d]">{{ number_format($totalGambar) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
            <div class="bg-slate-100 text-slate-500 rounded-lg p-3">
                <i class="fa-solid fa-layer-group text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Kelas</p>
                <p class="text-xl font-bold text-[#0d1f2d]">{{ $totalKelas }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
            <div class="bg-yellow-100 text-yellow-600 rounded-lg p-3">
                <i class="fa-solid fa-arrow-up text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Terbanyak</p>
                <p class="text-xl font-bold text-[#0d1f2d]">{{ number_format($terbanyak?->jumlah_gambar ?? 0) }}</p>
                <p class="text-xs text-gray-400">{{ $terbanyak?->nama_penyakit ?? '-' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
            <div class="bg-red-100 text-red-600 rounded-lg p-3">
                <i class="fa-solid fa-arrow-down text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Tersedikit</p>
                <p class="text-xl font-bold text-[#0d1f2d]">{{ number_format($tersedikit?->jumlah_gambar ?? 0) }}</p>
                <p class="text-xs text-gray-400">{{ $tersedikit?->nama_penyakit ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Tabel Kelas --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
            <span class="font-semibold text-gray-700">Kelas Penyakit</span>
            <span class="text-xs text-gray-400">Klik "Upload ZIP" untuk menambah data massal</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">#</th>
                        <th class="px-5 py-3 text-left">Nama Penyakit</th>
                        <th class="px-5 py-3 text-left">Label</th>
                        <th class="px-5 py-3 text-left">Jumlah Gambar</th>
                        <th class="px-5 py-3 text-left">Distribusi</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($penyakit as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-5 py-3 font-semibold text-gray-800">{{ $item->nama_penyakit }}</td>
                        <td class="px-5 py-3">
                            @if($item->kode_label)
                                <span class="bg-gray-100 text-gray-600 text-xs font-mono px-2 py-1 rounded">
                                    {{ $item->kode_label }}
                                </span>
                            @else
                                <span class="text-red-400 text-xs">belum diset</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-600 font-medium">{{ number_format($item->jumlah_gambar) }}</td>
                        <td class="px-5 py-3">
                            @php
                                $persen = $totalGambar > 0
                                    ? round(($item->jumlah_gambar / $totalGambar) * 100, 1)
                                    : 0;
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full bg-[#3eb872]" style="width: {{ $persen }}%"></div>
                                </div>
                                <span class="text-gray-500 text-xs">{{ $persen }}%</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            @if($item->jumlah_gambar >= 100)
                                <span class="bg-[#d1f0de] text-[#146135] text-xs px-2.5 py-1 rounded-full">
                                    <i class="fa-solid fa-check mr-1"></i>Cukup
                                </span>
                            @elseif($item->jumlah_gambar > 0)
                                <span class="bg-yellow-100 text-yellow-700 text-xs px-2.5 py-1 rounded-full">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>Sedikit
                                </span>
                            @else
                                <span class="bg-red-100 text-red-600 text-xs px-2.5 py-1 rounded-full">
                                    <i class="fa-solid fa-xmark mr-1"></i>Kosong
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex gap-2 justify-center">
                                <a href="{{ route('admin.dataset.lihat', $item->id_penyakit) }}"
                                    class="px-3 py-1.5 text-xs bg-slate-50 text-slate-600 rounded-lg hover:bg-slate-100">
                                    <i class="fa-solid fa-eye mr-1"></i>Lihat
                                </a>
                                @if($item->kode_label)
                                <button onclick="bukaZipUntukKelas('{{ $item->kode_label }}', '{{ $item->nama_penyakit }}')"
                                    class="px-3 py-1.5 text-xs bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100">
                                    <i class="fa-solid fa-file-zipper mr-1"></i>ZIP
                                </button>
                                <button onclick="modalUpload({{ $item->id_penyakit }}, '{{ $item->nama_penyakit }}')"
                                    class="px-3 py-1.5 text-xs bg-[#f0faf4] text-[#146135] rounded-lg hover:bg-[#d1f0de]">
                                    <i class="fa-solid fa-upload mr-1"></i>Upload
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-400">
                            <i class="fa-solid fa-inbox text-4xl mb-3 block opacity-30"></i>
                            Belum ada kelas penyakit.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     MODAL UPLOAD ZIP
════════════════════════════════════════════════════════════ --}}
<div id="modalZip" class="fixed inset-0 bg-black bg-opacity-50 z-50" style="display:none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="font-bold text-gray-800">
                    <i class="fa-solid fa-file-zipper text-[#146135] mr-2"></i>
                    Upload Dataset ZIP
                </h3>
                <button onclick="tutupModalZip()" class="text-gray-400 hover:text-gray-600 text-xl">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                {{-- Info --}}
                <div class="bg-[#f0faf4] rounded-xl p-4 text-sm text-[#146135]">
                    <p class="font-semibold mb-1"><i class="fa-solid fa-circle-info mr-1"></i>Format ZIP yang benar:</p>
                    <p class="font-mono text-xs bg-white rounded p-2 mt-1 text-gray-600">
                        mel.zip<br>
                        └── gambar1.jpg<br>
                        └── gambar2.jpg<br>
                        └── ... (langsung di root ZIP, bukan subfolder)
                    </p>
                    <p class="mt-2 text-xs">Semua gambar akan disimpan ke folder kelas yang dipilih. Upload beberapa ZIP ke kelas yang sama akan digabung otomatis.</p>
                </div>

                {{-- Pilih Kelas --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas Tujuan</label>
                    <select id="zipLabel"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#3eb872]">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($penyakit as $item)
                            @if($item->kode_label)
                            <option value="{{ $item->kode_label }}">
                                {{ $item->nama_penyakit }} ({{ $item->kode_label }}) — {{ number_format($item->jumlah_gambar) }} gambar
                            </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- Drop Zone ZIP --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File ZIP</label>
                    <div id="dropZoneZip"
                        class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#3eb872] transition-colors cursor-pointer">
                        <i class="fa-solid fa-file-zipper text-3xl text-gray-300 mb-2 block"></i>
                        <p class="text-sm text-gray-500 mb-1">Klik atau drag & drop file ZIP</p>
                        <p id="zipFileName" class="text-xs text-[#146135] font-medium"></p>
                        <input type="file" id="zipFileInput" accept=".zip" class="hidden">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Maks 500MB per file.</p>
                </div>

                {{-- Progress --}}
                <div id="zipProgressWrap" class="hidden space-y-2">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span id="zipProgressLabel">Mengupload ZIP...</span>
                        <span id="zipProgressText">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div id="zipProgressBar" class="h-2.5 rounded-full bg-[#146135] transition-all" style="width:0%"></div>
                    </div>
                </div>

                {{-- Hasil --}}
                <div id="zipResult" class="hidden rounded-xl p-4 text-sm"></div>

                {{-- Tombol --}}
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" onclick="tutupModalZip()"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50">
                        Tutup
                    </button>
                    <button id="btnUploadZip" onclick="uploadZip()"
                        class="px-5 py-2 text-sm bg-[#146135] text-white rounded-lg hover:bg-[#0f4a27] disabled:opacity-50 flex items-center gap-2">
                        <i class="fa-solid fa-file-zipper"></i>
                        Upload & Ekstrak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Upload Gambar Satuan --}}
<div id="modalUpload" class="fixed inset-0 bg-black bg-opacity-50 z-50" style="display:none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="font-bold text-gray-800">
                    <i class="fa-solid fa-upload text-[#146135] mr-2"></i>
                    Upload Gambar — <span id="namaKelas"></span>
                </h3>
                <button onclick="document.getElementById('modalUpload').style.display='none'"
                    class="text-gray-400 hover:text-gray-600 text-xl">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="formUpload" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div id="dropZone"
                    class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#3eb872] transition-colors cursor-pointer">
                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-300 mb-2 block"></i>
                    <p class="text-sm text-gray-500 mb-1">Klik atau drag & drop gambar</p>
                    <p id="fileCount" class="text-xs text-[#146135] font-medium"></p>
                    <input type="file" id="fileInput" name="gambar[]" multiple accept="image/*" class="hidden">
                </div>
                <p class="text-xs text-gray-400">Format: JPG, JPEG, PNG. Maks 2MB per file.</p>
                <div id="progressWrap" class="hidden">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Mengupload...</span><span id="progressText">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="progressBar" class="h-2 rounded-full bg-[#3eb872] transition-all" style="width:0%"></div>
                    </div>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('modalUpload').style.display='none'"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" id="btnUpload"
                        class="px-4 py-2 text-sm bg-[#146135] text-white rounded-lg hover:bg-[#0f4a27]">
                        <i class="fa-solid fa-upload mr-1"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ── Modal ZIP ────────────────────────────────────────────────

function bukaZipUntukKelas(kodeLabel, namaKelas) {
    document.getElementById('zipLabel').value = kodeLabel;
    document.getElementById('modalZip').style.display = 'block';
}

function tutupModalZip() {
    document.getElementById('modalZip').style.display = 'none';
    document.getElementById('zipFileName').textContent = '';
    document.getElementById('zipFileInput').value = '';
    document.getElementById('zipProgressWrap').classList.add('hidden');
    document.getElementById('zipProgressBar').style.width = '0%';
    document.getElementById('zipResult').classList.add('hidden');
    document.getElementById('btnUploadZip').disabled = false;
}

document.getElementById('dropZoneZip').addEventListener('click', () => {
    document.getElementById('zipFileInput').click();
});

document.getElementById('zipFileInput').addEventListener('change', function () {
    document.getElementById('zipFileName').textContent = this.files[0]?.name ?? '';
});

const dzZip = document.getElementById('dropZoneZip');
dzZip.addEventListener('dragover', e => { e.preventDefault(); dzZip.classList.add('border-[#3eb872]', 'bg-[#f0faf4]'); });
dzZip.addEventListener('dragleave', () => dzZip.classList.remove('border-[#3eb872]', 'bg-[#f0faf4]'));
dzZip.addEventListener('drop', e => {
    e.preventDefault();
    dzZip.classList.remove('border-[#3eb872]', 'bg-[#f0faf4]');
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('zipFileInput').files = dt.files;
        document.getElementById('zipFileName').textContent = file.name;
    }
});

function uploadZip() {
    const label = document.getElementById('zipLabel').value;
    const file  = document.getElementById('zipFileInput').files[0];

    if (!label) { alert('Pilih kelas tujuan dulu!'); return; }
    if (!file)  { alert('Pilih file ZIP dulu!'); return; }

    const fd = new FormData();
    fd.append('label', label);
    fd.append('file', file);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    document.getElementById('zipProgressWrap').classList.remove('hidden');
    document.getElementById('zipResult').classList.add('hidden');
    document.getElementById('btnUploadZip').disabled = true;
    document.getElementById('zipProgressLabel').textContent = 'Mengupload ZIP...';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route("admin.dataset.upload-zip") }}');

    xhr.upload.onprogress = e => {
        if (e.lengthComputable) {
            const pct = Math.round(e.loaded / e.total * 100);
            document.getElementById('zipProgressBar').style.width = pct + '%';
            document.getElementById('zipProgressText').textContent = pct + '%';
            if (pct === 100) {
                document.getElementById('zipProgressLabel').textContent = 'Mengekstrak gambar...';
            }
        }
    };

    xhr.onload = () => {
        const res = JSON.parse(xhr.responseText);
        const el  = document.getElementById('zipResult');
        el.classList.remove('hidden');

        if (res.success) {
            el.className = 'rounded-xl p-4 text-sm bg-[#f0faf4] border border-[#d1f0de] text-[#146135]';
            el.innerHTML = `<i class="fa-solid fa-circle-check mr-2"></i>${res.message}`;
            // Reload tabel setelah 2 detik
            setTimeout(() => window.location.reload(), 2000);
        } else {
            el.className = 'rounded-xl p-4 text-sm bg-red-50 border border-red-200 text-red-700';
            el.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-2"></i>${res.message}`;
            document.getElementById('btnUploadZip').disabled = false;
        }
    };

    xhr.onerror = () => {
        const el = document.getElementById('zipResult');
        el.classList.remove('hidden');
        el.className = 'rounded-xl p-4 text-sm bg-red-50 border border-red-200 text-red-700';
        el.innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-2"></i>Upload gagal. Cek koneksi ke Flask API.';
        document.getElementById('btnUploadZip').disabled = false;
    };

    xhr.send(fd);
}

// ── Modal Upload Gambar Satuan ───────────────────────────────

function modalUpload(id, nama) {
    document.getElementById('namaKelas').textContent = nama;
    document.getElementById('formUpload').action = `/admin/dataset/${id}/upload`;
    document.getElementById('fileCount').textContent = '';
    document.getElementById('fileInput').value = '';
    document.getElementById('modalUpload').style.display = 'block';
}

document.getElementById('dropZone').addEventListener('click', () => document.getElementById('fileInput').click());
document.getElementById('fileInput').addEventListener('change', function () {
    document.getElementById('fileCount').textContent = this.files.length > 0 ? `${this.files.length} file dipilih` : '';
});

const dz = document.getElementById('dropZone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('border-[#3eb872]', 'bg-[#f0faf4]'); });
dz.addEventListener('dragleave', () => dz.classList.remove('border-[#3eb872]', 'bg-[#f0faf4]'));
dz.addEventListener('drop', e => {
    e.preventDefault();
    dz.classList.remove('border-[#3eb872]', 'bg-[#f0faf4]');
    const dt = new DataTransfer();
    [...e.dataTransfer.files].forEach(f => dt.items.add(f));
    document.getElementById('fileInput').files = dt.files;
    document.getElementById('fileCount').textContent = `${dt.files.length} file dipilih`;
});

document.getElementById('formUpload').addEventListener('submit', function (e) {
    e.preventDefault();
    const fi = document.getElementById('fileInput');
    if (!fi.files.length) { alert('Pilih gambar dulu!'); return; }

    const fd = new FormData(this);
    [...fi.files].forEach(f => fd.append('gambar[]', f));

    document.getElementById('progressWrap').classList.remove('hidden');
    document.getElementById('btnUpload').disabled = true;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', this.action);
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    xhr.upload.onprogress = e => {
        if (e.lengthComputable) {
            const pct = Math.round(e.loaded / e.total * 100);
            document.getElementById('progressBar').style.width = pct + '%';
            document.getElementById('progressText').textContent = pct + '%';
        }
    };
    xhr.onload = () => window.location.reload();
    xhr.onerror = () => {
        alert('Upload gagal.');
        document.getElementById('btnUpload').disabled = false;
    };
    xhr.send(fd);
});
</script>
@endsection