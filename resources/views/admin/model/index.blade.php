@extends('layouts.admin')

@section('pageTitle', 'Model CNN')

@section('content')
<div class="p-6 max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Model CNN</h1>
        <p class="text-sm text-gray-500 mt-1">
            Daftar model tersimpan dari hasil training. Aktifkan model yang ingin dipakai untuk prediksi.
        </p>
    </div>

    {{-- Status Flask --}}
    <div class="flex items-center gap-2 text-sm">
        <span class="w-2.5 h-2.5 rounded-full {{ $flaskOnline ? 'bg-[#3eb872] animate-pulse' : 'bg-red-500' }}"></span>
        <span class="text-gray-600">
            Flask API: <strong>{{ $flaskOnline ? 'Online' : 'Offline' }}</strong>
            @if(!$flaskOnline)
                <span class="text-red-500 ml-1">— daftar model tidak dapat dimuat</span>
            @endif
        </span>
    </div>

    @if($error)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-600">
        <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ $error }}
    </div>
    @endif

    {{-- Ringkasan Training Terakhir --}}
    @if($stats)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-700 mb-4">
            <i class="fa-solid fa-chart-line text-[#146135] mr-2"></i>Hasil Training Terakhir
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
            <div class="bg-[#f0faf4] rounded-xl p-3 border-l-[3px] border-[#146135]">
                <p class="text-xs text-gray-400 mb-1">Val Accuracy</p>
                <p class="text-2xl font-bold text-[#0d1f2d]">{{ $stats['overall_accuracy'] ?? '-' }}%</p>
            </div>
            <div class="bg-purple-50 rounded-xl p-3 border-l-[3px] border-purple-400">
                <p class="text-xs text-gray-400 mb-1">Balanced Acc</p>
                <p class="text-2xl font-bold text-[#0d1f2d]">{{ $stats['balanced_accuracy'] ?? '-' }}%</p>
            </div>
            <div class="bg-[#f0faf4] rounded-xl p-3 border-l-[3px] border-[#3eb872]">
                <p class="text-xs text-gray-400 mb-1">Test Accuracy</p>
                <p class="text-2xl font-bold text-[#0d1f2d]">{{ $stats['test_accuracy'] ?? '-' }}%</p>
            </div>
            <div class="bg-teal-50 rounded-xl p-3 border-l-[3px] border-teal-400">
                <p class="text-xs text-gray-400 mb-1">Test Balanced</p>
                <p class="text-2xl font-bold text-[#0d1f2d]">{{ $stats['test_balanced_accuracy'] ?? '-' }}%</p>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-3 text-center">
            Dilatih: {{ $stats['trained_at'] ?? '-' }} &bull;
            {{ $stats['total_epoch_run'] ?? '-' }} epoch &bull;
            Train/Val/Test: {{ $stats['train_size'] ?? '-' }}/{{ $stats['val_size'] ?? '-' }}/{{ $stats['test_size'] ?? '-' }} gambar
        </p>

        @if(!empty($stats['per_class']))
        <div class="mt-4 border-t border-gray-100 pt-4">
            <p class="text-xs font-medium text-gray-500 mb-3">Akurasi Per Kelas (Validasi)</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                @foreach($stats['per_class'] as $kelas => $info)
                <div class="text-center bg-gray-50 rounded-lg p-2">
                    <p class="text-xs font-semibold text-gray-600 uppercase">{{ $kelas }}</p>
                    <p class="text-base font-bold {{ ($info['accuracy'] ?? 0) >= 70 ? 'text-[#146135]' : (($info['accuracy'] ?? 0) >= 50 ? 'text-yellow-600' : 'text-red-500') }}">
                        {{ $info['accuracy'] ?? 0 }}%
                    </p>
                    <p class="text-[10px] text-gray-400">{{ $info['correct'] ?? 0 }}/{{ $info['total'] ?? 0 }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Daftar Model --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-gray-700">
                <i class="fa-solid fa-layer-group text-[#146135] mr-2"></i>Model Tersimpan
            </h2>
            <span class="text-xs text-gray-400">{{ count($models) }} model</span>
        </div>

        @if(count($models) === 0)
        <div class="text-center py-10 text-gray-400">
            <i class="fa-solid fa-box-open text-4xl mb-3 block opacity-30"></i>
            <p class="text-sm">Belum ada model tersimpan.</p>
            <p class="text-xs mt-1">Jalankan training untuk menghasilkan model baru.</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($models as $m)
            <div class="flex items-center justify-between border rounded-xl p-4 transition
                {{ $m['is_active'] ? 'bg-[#f0faf4] border-[#d1f0de]' : 'border-gray-100 hover:border-gray-200' }}">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ $m['is_active'] ? 'bg-[#146135]' : 'bg-gray-100' }}">
                        <i class="fa-solid fa-brain text-sm {{ $m['is_active'] ? 'text-white' : 'text-gray-400' }}"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-medium text-gray-700 text-sm">{{ $m['name'] }}</p>
                            @if($m['is_active'])
                            <span class="text-[10px] bg-[#146135] text-white px-2 py-0.5 rounded-full font-semibold tracking-wide">
                                AKTIF
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $m['size_mb'] }} MB &bull; {{ $m['created_at'] }}
                        </p>
                    </div>
                </div>

                @if(!$m['is_active'])
                <button
                    class="btn-switch-model flex-shrink-0 text-sm font-medium px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-[#146135] hover:text-white hover:border-[#146135] transition"
                    data-model-name="{{ $m['name'] }}">
                    <i class="fa-solid fa-bolt mr-1"></i>Aktifkan
                </button>
                @else
                <span class="flex-shrink-0 text-[#146135] text-sm font-medium px-4 py-2">
                    <i class="fa-solid fa-circle-check mr-1"></i>Dipakai
                </span>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- ============================================================ --}}
{{-- MODAL KONFIRMASI --}}
{{-- ============================================================ --}}
<style>
@keyframes modal-in {
    from { opacity: 0; transform: scale(0.96) translateY(10px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
.modal-animate {
    animation: modal-in 0.22s cubic-bezier(.4,0,.2,1) forwards;
}
</style>

<div id="modal-switch" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
    {{-- Backdrop --}}
    <div id="modal-backdrop" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

    {{-- Modal box --}}
    <div class="modal-animate relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">

        {{-- Garis aksen atas (tema hijau brand) --}}
        <div class="h-1 w-full bg-gradient-to-r from-[#3eb872] via-[#146135] to-[#0f4a27]"></div>

        {{-- ── STATE: CONFIRM ── --}}
        <div id="modal-body-confirm">

            {{-- Header --}}
            <div class="px-6 pt-6 pb-4 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-[#f0faf4] flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-brain text-[#146135] text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-base leading-tight">Ganti Model Aktif</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Konfirmasi penggantian model prediksi</p>
                </div>
            </div>

            <div class="px-6 pb-6 space-y-3">
                {{-- Info model --}}
                <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                    <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wider mb-1">Model dipilih</p>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-microchip text-[#3eb872] text-xs flex-shrink-0"></i>
                        <p id="modal-model-name" class="font-semibold text-gray-700 text-sm break-all leading-snug"></p>
                    </div>
                </div>

                {{-- Warning --}}
                <div class="flex gap-3 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-400 text-sm mt-0.5 flex-shrink-0"></i>
                    <p class="text-xs text-amber-700 leading-relaxed">
                        Semua prediksi baru akan langsung menggunakan model ini setelah diaktifkan.
                    </p>
                </div>

                {{-- Tombol --}}
                <div class="flex gap-2 pt-1">
                    <button id="btn-modal-cancel"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-500 text-sm font-medium hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button id="btn-modal-confirm"
                        class="flex-1 py-2.5 rounded-xl text-white text-sm font-semibold transition-all
                               bg-gradient-to-r from-[#146135] to-[#0f4a27] hover:from-[#0f4a27] hover:to-[#0d3d20]
                               shadow-md shadow-[#146135]/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-check text-xs"></i>
                        Ya, Aktifkan
                    </button>
                </div>
            </div>
        </div>

        {{-- ── STATE: LOADING ── --}}
        <div id="modal-body-loading" class="hidden px-6 py-10 text-center">
            <div class="relative w-16 h-16 mx-auto mb-5">
                <div class="absolute inset-0 rounded-full border-4 border-[#d1f0de]"></div>
                <div class="absolute inset-0 rounded-full border-4 border-[#146135] border-t-transparent animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fa-solid fa-brain text-[#146135] text-lg"></i>
                </div>
            </div>
            <p class="font-semibold text-gray-700 text-sm">Memuat Model...</p>
            <p class="text-xs text-gray-400 mt-1">Harap tunggu sebentar</p>
        </div>

        {{-- ── STATE: SUCCESS ── --}}
        <div id="modal-body-success" class="hidden px-6 py-10 text-center">
            <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-5">
                <i class="fa-solid fa-check text-emerald-600 text-2xl"></i>
            </div>
            <p class="font-bold text-gray-800">Model Berhasil Diaktifkan</p>
            <p id="modal-success-msg" class="text-xs text-gray-400 mt-1.5 leading-relaxed"></p>
            <div class="flex items-center justify-center gap-1.5 mt-4 text-xs text-gray-400">
                <i class="fa-solid fa-rotate-right animate-spin text-[10px]"></i>
                Memuat ulang halaman...
            </div>
        </div>

        {{-- ── STATE: ERROR ── --}}
        <div id="modal-body-error" class="hidden px-6 py-6">
            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-xl"></i>
            </div>
            <p class="font-bold text-gray-800 text-center text-sm">Gagal Mengaktifkan</p>
            <p id="modal-error-msg" class="text-xs text-red-500 text-center mt-1.5 leading-relaxed"></p>
            <button id="btn-modal-close-error"
                class="mt-5 w-full py-2.5 rounded-xl border border-gray-200 text-gray-500 text-sm font-medium hover:bg-gray-50 transition-colors">
                Tutup
            </button>
        </div>

    </div>
</div>

<script>
(function () {
    let selectedModel = null;

    const modal        = document.getElementById('modal-switch');
    const backdrop     = document.getElementById('modal-backdrop');
    const bodies       = {
        confirm : document.getElementById('modal-body-confirm'),
        loading : document.getElementById('modal-body-loading'),
        success : document.getElementById('modal-body-success'),
        error   : document.getElementById('modal-body-error'),
    };
    const modelNameEl  = document.getElementById('modal-model-name');
    const successMsgEl = document.getElementById('modal-success-msg');
    const errorMsgEl   = document.getElementById('modal-error-msg');

    function showModal(state) {
        modal.classList.remove('hidden');
        Object.values(bodies).forEach(el => el.classList.add('hidden'));
        bodies[state].classList.remove('hidden');
    }

    function hideModal() {
        modal.classList.add('hidden');
        selectedModel = null;
    }

    // Buka modal
    document.querySelectorAll('.btn-switch-model').forEach(btn => {
        btn.addEventListener('click', function () {
            selectedModel      = this.dataset.modelName;
            modelNameEl.textContent = selectedModel;
            showModal('confirm');
        });
    });

    // Tutup via backdrop & tombol batal
    backdrop.addEventListener('click', hideModal);
    document.getElementById('btn-modal-cancel').addEventListener('click', hideModal);
    document.getElementById('btn-modal-close-error').addEventListener('click', hideModal);

    // Konfirmasi aktifkan
    document.getElementById('btn-modal-confirm').addEventListener('click', async function () {
        if (!selectedModel) return;
        showModal('loading');

        try {
            const res = await fetch('{{ route("admin.model.switch") }}', {
                method : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ model_name: selectedModel }),
            });

            const result = await res.json();

            if (result.success) {
                successMsgEl.textContent = result.data?.message || result.message || `${selectedModel} berhasil diaktifkan!`;
                showModal('success');
                setTimeout(() => window.location.reload(), 1800);
            } else {
                errorMsgEl.textContent = result.data?.message || result.error || result.message || 'Terjadi kesalahan.';
                showModal('error');
            }
        } catch (err) {
            errorMsgEl.textContent = 'Gagal terhubung ke server: ' + err.message;
            showModal('error');
        }
    });
})();
</script>

@endsection