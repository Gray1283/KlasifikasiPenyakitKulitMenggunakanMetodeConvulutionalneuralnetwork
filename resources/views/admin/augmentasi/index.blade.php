@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-5xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Augmentasi Dataset</h1>
        <p class="text-sm text-gray-500 mt-1">
            Generate gambar tambahan untuk kelas yang datanya masih sedikit.
            Augmentasi hanya diambil dari bagian data yang akan dipakai untuk training (train),
            sehingga tidak mempengaruhi keakuratan evaluasi (val/test).
        </p>
    </div>

    {{-- Status Flask --}}
    <div class="mb-6 flex items-center gap-2 text-sm">
        <span class="w-2.5 h-2.5 rounded-full {{ $flaskOnline ? 'bg-[#3eb872]' : 'bg-red-500' }}"></span>
        <span class="text-gray-600">
            Flask API: {{ $flaskOnline ? 'Online' : 'Offline - augmentasi tidak akan berjalan' }}
        </span>
    </div>

    {{-- Info jumlah gambar per kelas --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <h2 class="font-semibold text-gray-700 mb-4">Jumlah Gambar per Kelas (saat ini)</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="dataset-info-grid">
            @foreach ($kelasList as $kode => $nama)
                @php $jumlah = $dataset[$kode] ?? 0; @endphp
                <div class="border border-gray-100 rounded-xl p-3 text-center" data-kelas="{{ $kode }}">
                    <p class="text-xs text-gray-400 uppercase">{{ $kode }}</p>
                    <p class="text-lg font-bold {{ $jumlah < 500 ? 'text-red-500' : 'text-[#0d1f2d]' }}" data-jumlah="{{ $jumlah }}">
                        {{ $jumlah }}
                    </p>
                    <p class="text-[11px] text-gray-400">{{ $nama }}</p>
                </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-400 mt-3">
            <span class="inline-block w-2 h-2 rounded-full bg-red-500 mr-1"></span>
            Kelas dengan jumlah di bawah 500 disarankan untuk diaugmentasi.
        </p>
    </div>

    {{-- Form Augmentasi --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-700 mb-4">Generate Augmentasi</h2>

        <form id="augment-form" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Pilih Kelas</label>
                <select name="label" id="input-label" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#3eb872]">
                    <option value="">-- Pilih kelas --</option>
                    @foreach ($kelasList as $kode => $nama)
                        <option value="{{ $kode }}">{{ strtoupper($kode) }} - {{ $nama }} ({{ $dataset[$kode] ?? 0 }} gambar)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Jumlah Gambar yang Digenerate</label>
                <input type="number" name="jumlah" id="input-jumlah" value="300" min="1" max="2000" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#3eb872]">
                <p class="text-xs text-gray-400 mt-1">
                    Catatan: jumlah ini diambil dari data train kelas tersebut, sumbernya dipakai berulang
                    dengan variasi acak (flip, rotasi, zoom, blur, dll) - bukan menambah data asli baru.
                </p>
            </div>

            <button type="submit" id="btn-submit"
                class="w-full bg-[#146135] hover:bg-[#0f4a27] text-white font-medium py-2.5 rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed">
                Generate Augmentasi
            </button>
        </form>

        {{-- Loading --}}
        <div id="augment-loading" class="hidden text-center py-6">
            <i class="fa-solid fa-spinner fa-spin text-3xl text-[#146135]"></i>
            <p class="text-sm text-gray-500 mt-2">Sedang memproses, mohon tunggu...</p>
        </div>

        {{-- Hasil sukses --}}
        <div id="augment-success" class="hidden mt-4 bg-[#f0faf4] border border-[#d1f0de] rounded-xl p-4">
            <p class="text-sm text-[#146135] font-medium" id="success-message"></p>
            <div class="text-xs text-gray-500 mt-2 grid grid-cols-3 gap-2">
                <div>
                    <p class="text-gray-500">Digenerate</p>
                    <p class="font-bold text-[#0d1f2d]" id="result-generated">-</p>
                </div>
                <div>
                    <p class="text-gray-500">Sumber (train)</p>
                    <p class="font-bold text-[#0d1f2d]" id="result-sumber">-</p>
                </div>
                <div>
                    <p class="text-gray-500">Total di folder</p>
                    <p class="font-bold text-[#0d1f2d]" id="result-total">-</p>
                </div>
            </div>
        </div>

        {{-- Hasil error --}}
        <div id="augment-error" class="hidden mt-4 bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm text-red-600" id="error-message"></p>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.getElementById('augment-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const label  = document.getElementById('input-label').value;
    const jumlah = document.getElementById('input-jumlah').value;
    const btn    = document.getElementById('btn-submit');

    if (!label) {
        alert('Pilih kelas dulu');
        return;
    }

    document.getElementById('augment-success').classList.add('hidden');
    document.getElementById('augment-error').classList.add('hidden');
    document.getElementById('augment-loading').classList.remove('hidden');
    btn.disabled = true;

    try {
        const response = await fetch('{{ route("admin.augmentasi.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                                 || document.querySelector('input[name="_token"]').value,
            },
            body: JSON.stringify({ label, jumlah }),
        });

        const result = await response.json();

        document.getElementById('augment-loading').classList.add('hidden');
        btn.disabled = false;

        if (result.success) {
            const data = result.data;
            document.getElementById('success-message').textContent = data.message;
            document.getElementById('result-generated').textContent = data.generated ?? '-';
            document.getElementById('result-sumber').textContent    = data.sumber_train ?? '-';
            document.getElementById('result-total').textContent     = data.total_di_folder ?? '-';
            document.getElementById('augment-success').classList.remove('hidden');

            // Update angka di grid info dataset (estimasi, refresh halaman untuk akurat)
            const cell = document.querySelector(`[data-kelas="${label}"] [data-jumlah]`);
            if (cell && data.total_di_folder) {
                cell.textContent = data.total_di_folder;
            }
        } else {
            document.getElementById('error-message').textContent = result.message || 'Augmentasi gagal';
            document.getElementById('augment-error').classList.remove('hidden');
        }
    } catch (err) {
        document.getElementById('augment-loading').classList.add('hidden');
        btn.disabled = false;
        document.getElementById('error-message').textContent = 'Gagal terhubung ke server: ' + err.message;
        document.getElementById('augment-error').classList.remove('hidden');
    }
});
</script>
@endpush

@endsection