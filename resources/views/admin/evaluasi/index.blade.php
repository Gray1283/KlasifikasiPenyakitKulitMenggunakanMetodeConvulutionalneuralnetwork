@extends('layouts.admin')

@section('pageTitle', 'Evaluasi Model')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">
            <i class="fa-solid fa-chart-pie text-[#146135] mr-2"></i>
            Evaluasi Model
        </h2>
        <span id="flask-badge" class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-500">
            <i class="fa-solid fa-circle text-xs mr-1"></i> Mengecek Flask...
        </span>
    </div>

    {{-- Form Evaluasi --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-700 mb-5">
            <i class="fa-solid fa-sliders text-[#146135] mr-2"></i>
            Parameter Evaluasi
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">

            {{-- Pilih Model --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">Model</label>
                <select id="select-model" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#3eb872]">
                    <option value="">Model Aktif (default)</option>
                    @foreach($models as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Pilih Split --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">Split Data</label>
                <div class="flex gap-2">
                    <button type="button" data-val="val"
                        onclick="selectSplit(this)"
                        class="split-btn flex-1 py-2.5 rounded-lg border text-sm font-medium transition-colors bg-[#146135] text-white border-[#146135]">
                        Validasi
                    </button>
                    <button type="button" data-val="test"
                        onclick="selectSplit(this)"
                        class="split-btn flex-1 py-2.5 rounded-lg border text-sm font-medium transition-colors bg-white text-gray-600 border-gray-200 hover:border-[#3eb872]">
                        Test
                        <span class="block text-[10px] opacity-60">gunakan sekali</span>
                    </button>
                </div>
            </div>

            {{-- Tombol --}}
            <div>
                <button id="btn-evaluate" onclick="runEvaluasi()"
                    class="w-full bg-[#146135] hover:bg-[#0f4a27] text-white font-semibold py-2.5 rounded-xl transition-colors">
                    <i class="fa-solid fa-play mr-2"></i> Jalankan Evaluasi
                </button>
            </div>
        </div>

        {{-- Warning test set --}}
        <div id="test-warning" class="hidden mt-4 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2.5 text-sm text-amber-700">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
            <strong>Perhatian:</strong> Test set hanya boleh dievaluasi sekali pada model final.
            Jangan gunakan untuk trial-and-error hyperparameter.
        </div>
    </div>

    {{-- Loading --}}
    <div id="loading-section" class="hidden bg-white rounded-xl shadow-sm p-10 text-center">
        <i class="fa-solid fa-circle-notch fa-spin text-[#146135] text-3xl mb-3 block"></i>
        <p class="text-sm text-gray-500">Sedang mengevaluasi model... ini mungkin memerlukan beberapa menit.</p>
    </div>

    {{-- Hasil Evaluasi --}}
    <div id="result-section" class="hidden space-y-6">

        {{-- Info model & split --}}
        <div class="bg-[#f0faf4] border border-[#d1f0de] rounded-xl px-5 py-3.5 flex flex-wrap gap-x-6 gap-y-1.5 text-sm text-[#146135]">
            <span><i class="fa-solid fa-brain mr-1.5 opacity-70"></i> Model: <strong id="res-model">-</strong></span>
            <span><i class="fa-solid fa-database mr-1.5 opacity-70"></i> Split: <strong id="res-split">-</strong></span>
            <span><i class="fa-solid fa-images mr-1.5 opacity-70"></i> Total Sampel: <strong id="res-samples">-</strong></span>
            <span><i class="fa-solid fa-clock mr-1.5 opacity-70"></i> <strong id="res-time">-</strong></span>
        </div>

        {{-- Kartu Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-5 text-center border-t-[3px] border-[#146135]">
                <p class="text-xs text-gray-400 mb-1.5">Overall Accuracy</p>
                <p id="res-overall" class="text-3xl font-bold text-[#0d1f2d]">-</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 text-center border-t-[3px] border-gray-200">
                <p class="text-xs text-gray-400 mb-1.5">Macro F1</p>
                <p id="res-macro-f1" class="text-3xl font-bold text-[#0d1f2d]">-</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 text-center border-t-[3px] border-gray-200">
                <p class="text-xs text-gray-400 mb-1.5">Weighted F1</p>
                <p id="res-weighted-f1" class="text-3xl font-bold text-[#0d1f2d]">-</p>
            </div>
        </div>

        {{-- Precision / Recall / F1 per kelas --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">
                <i class="fa-solid fa-table text-[#146135] mr-2"></i>
                Precision / Recall / F1 per Kelas
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-400 border-b">
                            <th class="pb-3 font-medium">Kelas</th>
                            <th class="pb-3 font-medium">Precision</th>
                            <th class="pb-3 font-medium">Recall</th>
                            <th class="pb-3 font-medium">F1-Score</th>
                            <th class="pb-3 font-medium">Support</th>
                        </tr>
                    </thead>
                    <tbody id="per-class-tbody" class="divide-y divide-gray-50"></tbody>
                </table>
            </div>
        </div>

        {{-- Confusion Matrix --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-1">
                <i class="fa-solid fa-grid-2 text-[#146135] mr-2"></i>
                Confusion Matrix
            </h3>
            <p class="text-xs text-gray-400 mb-4">Baris = label asli, Kolom = prediksi model. Diagonal = prediksi benar.</p>
            <div class="overflow-x-auto">
                <table id="cm-table" class="text-xs text-center border-collapse"></table>
            </div>
        </div>

    </div>

    {{-- Error --}}
    <div id="error-section" class="hidden bg-white rounded-xl shadow-sm p-8 text-center">
        <i class="fa-solid fa-circle-xmark text-red-400 text-4xl mb-3 block"></i>
        <p id="error-message" class="text-sm text-red-600">Terjadi error.</p>
    </div>

    {{-- History Evaluasi --}}
    @if(count($history) > 0)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-700 mb-4">
            <i class="fa-solid fa-clock-rotate-left text-gray-400 mr-2"></i>
            Riwayat Evaluasi
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-400 border-b">
                        <th class="pb-3 font-medium">Waktu</th>
                        <th class="pb-3 font-medium">Model</th>
                        <th class="pb-3 font-medium">Split</th>
                        <th class="pb-3 font-medium">Accuracy</th>
                        <th class="pb-3 font-medium">Macro F1</th>
                        <th class="pb-3 font-medium">Weighted F1</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach(array_reverse($history) as $h)
                    <tr class="text-gray-600 hover:bg-gray-50">
                        <td class="py-3 text-xs text-gray-400">{{ $h['evaluated_at'] }}</td>
                        <td class="py-3 font-mono text-xs">{{ $h['model_evaluated'] }}</td>
                        <td class="py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $h['split'] === 'test' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $h['split'] }}
                            </span>
                        </td>
                        <td class="py-3 font-semibold text-[#146135]">{{ $h['overall_accuracy'] }}%</td>
                        <td class="py-3 text-gray-500">{{ $h['macro_f1'] }}%</td>
                        <td class="py-3 text-gray-500">{{ $h['weighted_f1'] }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
let selectedSplit = 'val';

function selectSplit(btn) {
    document.querySelectorAll('.split-btn').forEach(b => {
        b.classList.remove('bg-[#146135]', 'text-white', 'border-[#146135]');
        b.classList.add('bg-white', 'text-gray-600', 'border-gray-200');
    });
    btn.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');
    btn.classList.add('bg-[#146135]', 'text-white', 'border-[#146135]');
    selectedSplit = btn.dataset.val;
    document.getElementById('test-warning').classList.toggle('hidden', selectedSplit !== 'test');
}

function runEvaluasi() {
    const modelName = document.getElementById('select-model').value;

    document.getElementById('result-section').classList.add('hidden');
    document.getElementById('error-section').classList.add('hidden');
    document.getElementById('loading-section').classList.remove('hidden');
    document.getElementById('btn-evaluate').disabled = true;

    fetch('{{ route("admin.evaluasi.run") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            model_name: modelName || null,
            split: selectedSplit,
        }),
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('loading-section').classList.add('hidden');
        document.getElementById('btn-evaluate').disabled = false;

        if (!data.success) {
            showError(data.message || 'Evaluasi gagal.');
            return;
        }
        showResult(data);
    })
    .catch(err => {
        document.getElementById('loading-section').classList.add('hidden');
        document.getElementById('btn-evaluate').disabled = false;
        showError('Tidak bisa terhubung ke server. ' + err.message);
    });
}

function showError(msg) {
    document.getElementById('error-section').classList.remove('hidden');
    document.getElementById('error-message').textContent = msg;
}

function showResult(data) {
    document.getElementById('result-section').classList.remove('hidden');

    document.getElementById('res-model').textContent    = data.model_evaluated || '-';
    document.getElementById('res-split').textContent    = data.split || '-';
    document.getElementById('res-samples').textContent  = data.total_samples || '-';
    document.getElementById('res-time').textContent     = data.evaluated_at || '-';

    document.getElementById('res-overall').textContent     = (data.overall_accuracy ?? '-') + '%';
    document.getElementById('res-macro-f1').textContent    = (data.macro_f1 ?? '-') + '%';
    document.getElementById('res-weighted-f1').textContent = (data.weighted_f1 ?? '-') + '%';

    // Tabel per kelas
    const tbody = document.getElementById('per-class-tbody');
    tbody.innerHTML = '';
    const perClass = data.per_class || {};
    Object.entries(perClass).forEach(([cls, m]) => {
        const recallColor = m.recall < 50
            ? 'text-red-600 font-semibold'
            : m.recall < 75
                ? 'text-amber-600'
                : 'text-[#146135]';
        tbody.innerHTML += `
            <tr class="hover:bg-gray-50">
                <td class="py-3 font-semibold text-gray-700">${cls}</td>
                <td class="py-3 text-gray-500">${m.precision}%</td>
                <td class="py-3 ${recallColor}">${m.recall}%</td>
                <td class="py-3 text-gray-500">${m.f1_score}%</td>
                <td class="py-3 text-gray-400">${m.support}</td>
            </tr>`;
    });

    // Confusion matrix
    buildConfusionMatrix(data.confusion_matrix, Object.keys(perClass));
}

function buildConfusionMatrix(cm, classes) {
    const table = document.getElementById('cm-table');
    table.innerHTML = '';

    let maxVal = 0;
    classes.forEach(r => classes.forEach(c => {
        if (cm[r][c] > maxVal) maxVal = cm[r][c];
    }));

    // Header
    let headerRow = '<tr><th class="p-2.5 text-gray-400 font-medium text-left">↓asli / pred→</th>';
    classes.forEach(c => {
        headerRow += `<th class="p-2.5 text-gray-600 font-semibold">${c}</th>`;
    });
    headerRow += '</tr>';
    table.innerHTML += headerRow;

    // Baris data
    classes.forEach(rowCls => {
        let row = `<tr><td class="p-2.5 text-gray-600 font-semibold text-left pr-4">${rowCls}</td>`;
        classes.forEach(colCls => {
            const val    = cm[rowCls]?.[colCls] ?? 0;
            const isDiag = rowCls === colCls;
            const ratio  = maxVal > 0 ? val / maxVal : 0;
            const bgColor = isDiag
                ? `rgba(20, 97, 53, ${ratio})`
                : val > 0
                    ? `rgba(239, 68, 68, ${ratio * 0.7})`
                    : 'transparent';
            const textColor = ratio > 0.6 ? 'text-white' : 'text-gray-700';
            row += `<td class="p-2.5 rounded ${textColor} font-medium" style="background:${bgColor}; min-width:40px;">${val}</td>`;
        });
        row += '</tr>';
        table.innerHTML += row;
    });
}

// Cek Flask
fetch('/test-ml')
.then(r => r.json())
.then(d => {
    const badge = document.getElementById('flask-badge');
    if (d.connected) {
        badge.className = 'text-xs px-3 py-1 rounded-full bg-[#d1f0de] text-[#146135]';
        badge.innerHTML = '<i class="fa-solid fa-circle text-xs mr-1"></i> Flask Online';
    } else {
        badge.className = 'text-xs px-3 py-1 rounded-full bg-red-100 text-red-600';
        badge.innerHTML = '<i class="fa-solid fa-circle text-xs mr-1"></i> Flask Offline';
    }
});
</script>
@endpush

@endsection