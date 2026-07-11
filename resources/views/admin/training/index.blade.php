@extends('layouts.admin')

@section('pageTitle', 'Training Model')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">
            <i class="fa-solid fa-brain text-[#146135] mr-2"></i>
            Training Model CNN
        </h2>
        <span id="flask-badge" class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-500">
            <i class="fa-solid fa-circle text-xs mr-1"></i> Mengecek Flask...
        </span>
    </div>

    {{-- Stats Terakhir --}}
    @if($stats)
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-[3px] border-[#146135]">
            <p class="text-xs text-gray-400 mb-1">Akurasi Terbaik</p>
            <p class="text-2xl font-bold text-[#0d1f2d]">{{ $stats['overall_accuracy'] ?? '-' }}%</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-[3px] border-[#3eb872]">
            <p class="text-xs text-gray-400 mb-1">Total Epoch</p>
            <p class="text-2xl font-bold text-[#0d1f2d]">{{ $stats['total_epoch_run'] ?? '-' }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-[3px] border-purple-400">
            <p class="text-xs text-gray-400 mb-1">Total Dataset</p>
            <p class="text-2xl font-bold text-[#0d1f2d]">{{ $stats['total_dataset'] ?? '-' }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-[3px] border-gray-300">
            <p class="text-xs text-gray-400 mb-1">Selesai</p>
            <p class="text-sm font-semibold text-gray-600">{{ $stats['finished_at'] ?? '-' }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Form Parameter --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">
                <i class="fa-solid fa-sliders text-[#146135] mr-2"></i>
                Parameter Training
            </h3>

            <div class="space-y-4">
                {{-- Epochs --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Epochs <span class="text-gray-400 font-normal">(berapa kali model belajar semua data)</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="range" id="epochs" min="1" max="50" value="10"
                            class="flex-1 accent-[#146135]"
                            oninput="document.getElementById('epochs-val').textContent = this.value">
                        <span id="epochs-val" class="w-10 text-center font-bold text-[#146135]">10</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400 mt-1">
                        <span>1 (cepat)</span><span>50 (lama tapi akurat)</span>
                    </div>
                </div>

                {{-- Batch Size --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Batch Size <span class="text-gray-400 font-normal">(jumlah gambar per iterasi)</span>
                    </label>
                    <div class="grid grid-cols-4 gap-2" id="batch-group">
                        @foreach([8, 16, 32, 64] as $b)
                        <button type="button" data-val="{{ $b }}"
                            onclick="selectBatch(this)"
                            class="batch-btn py-2 rounded-lg border text-sm font-medium transition-colors
                                   {{ $b == 32 ? 'bg-[#146135] text-white border-[#146135]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#3eb872]' }}">
                            {{ $b }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Learning Rate --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Learning Rate <span class="text-gray-400 font-normal">(seberapa cepat model belajar)</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2" id="lr-group">
                        @foreach(['0.0001' => 'Lambat', '0.001' => 'Normal', '0.01' => 'Cepat'] as $val => $label)
                        <button type="button" data-val="{{ $val }}"
                            onclick="selectLr(this)"
                            class="lr-btn py-2 rounded-lg border text-sm font-medium transition-colors
                                   {{ $val == '0.001' ? 'bg-[#146135] text-white border-[#146135]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#3eb872]' }}">
                            {{ $label }}<br><span class="text-xs opacity-70">{{ $val }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="pt-2 flex gap-3">
                    <button id="btn-start" onclick="startTraining()"
                        class="flex-1 bg-[#146135] hover:bg-[#0f4a27] text-white font-semibold py-3 rounded-xl transition-colors">
                        <i class="fa-solid fa-play mr-2"></i> Mulai Training
                    </button>
                    <button id="btn-stop" onclick="stopTraining()"
                        class="hidden px-5 bg-red-500 hover:bg-red-600 text-white font-semibold py-3 rounded-xl transition-colors">
                        <i class="fa-solid fa-stop mr-2"></i> Stop
                    </button>
                </div>
            </div>
        </div>

        {{-- Monitor Progress --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">
                <i class="fa-solid fa-chart-line text-[#3eb872] mr-2"></i>
                Progress Training
            </h3>

            {{-- Status idle --}}
            <div id="status-idle" class="text-center py-10 text-gray-400">
                <i class="fa-solid fa-clock text-4xl mb-3 block opacity-30"></i>
                <p class="text-sm">Belum ada training yang berjalan</p>
            </div>

            {{-- Status running --}}
            <div id="status-running" class="hidden space-y-4">

                {{-- Pesan status --}}
                <div class="bg-[#f0faf4] rounded-lg px-4 py-2.5 text-sm text-[#146135] flex items-center gap-2">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                    <span id="status-message">Mempersiapkan...</span>
                </div>

                {{-- Progress bar epoch --}}
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Epoch</span>
                        <span><span id="cur-epoch">0</span> / <span id="tot-epoch">0</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div id="epoch-bar" class="bg-[#3eb872] h-3 rounded-full transition-all duration-500" style="width:0%"></div>
                    </div>
                </div>

                {{-- Metrik --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-400 mb-0.5">Train Accuracy</p>
                        <p id="train-acc" class="text-xl font-bold text-[#146135]">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-400 mb-0.5">Val Accuracy</p>
                        <p id="val-acc" class="text-xl font-bold text-[#3eb872]">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-400 mb-0.5">Train Loss</p>
                        <p id="train-loss" class="text-xl font-bold text-orange-500">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-400 mb-0.5">Val Loss</p>
                        <p id="val-loss" class="text-xl font-bold text-red-500">-</p>
                    </div>
                </div>

            </div>

            {{-- Status finished --}}
            <div id="status-finished" class="hidden text-center py-6 space-y-3">
                <i class="fa-solid fa-circle-check text-5xl text-[#3eb872]"></i>
                <p class="font-semibold text-gray-700" id="finish-message">Training selesai!</p>
                <p class="text-xs text-gray-400">Model terbaik sudah tersimpan otomatis</p>
                <a href="{{ route('admin.model.index') }}" class="inline-block mt-2 bg-[#146135] text-white text-sm px-5 py-2 rounded-xl hover:bg-[#0f4a27]">
                    Lihat Model →
                </a>
            </div>

            {{-- Status error --}}
            <div id="status-error" class="hidden text-center py-6 space-y-2">
                <i class="fa-solid fa-circle-xmark text-5xl text-red-400"></i>
                <p class="text-sm text-red-600" id="error-message">Terjadi error.</p>
            </div>

        </div>
    </div>

    <div id="history-section" class="hidden space-y-6">

        {{-- Grafik --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">
                    <i class="fa-solid fa-chart-line text-[#146135] mr-2"></i>
                    Accuracy per Epoch
                </h3>
                <canvas id="chart-acc" height="200"></canvas>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">
                    <i class="fa-solid fa-chart-line text-orange-500 mr-2"></i>
                    Loss per Epoch
                </h3>
                <canvas id="chart-loss" height="200"></canvas>
            </div>
        </div>

        {{-- Riwayat Tabel --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">
                <i class="fa-solid fa-table text-purple-500 mr-2"></i>
                Riwayat Per Epoch
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-400 border-b">
                            <th class="pb-2 font-medium">Epoch</th>
                            <th class="pb-2 font-medium">Train Loss</th>
                            <th class="pb-2 font-medium">Train Acc</th>
                            <th class="pb-2 font-medium">Val Loss</th>
                            <th class="pb-2 font-medium">Val Acc</th>
                        </tr>
                    </thead>
                    <tbody id="history-tbody" class="divide-y divide-gray-50"></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let pollingInterval = null;
let selectedBatch   = 32;
let selectedLr      = 0.001;
let chartAcc        = null;
let chartLoss       = null;

// ── Inisialisasi Chart ───────────────────────────────────────
function initCharts() {
    const commonOptions = {
        responsive: true,
        animation: { duration: 300 },
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } },
        scales: {
            x: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } } },
            y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } } },
        }
    };

    chartAcc = new Chart(document.getElementById('chart-acc'), {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                { label: 'Train Acc', data: [], borderColor: '#146135', backgroundColor: '#14613520', tension: 0.3, fill: true, pointRadius: 3 },
                { label: 'Val Acc',   data: [], borderColor: '#3eb872', backgroundColor: '#3eb87220', tension: 0.3, fill: true, pointRadius: 3 },
            ]
        },
        options: { ...commonOptions, scales: { ...commonOptions.scales, y: { ...commonOptions.scales.y, min: 0, max: 100 } } }
    });

    chartLoss = new Chart(document.getElementById('chart-loss'), {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                { label: 'Train Loss', data: [], borderColor: '#f97316', backgroundColor: '#f9731620', tension: 0.3, fill: true, pointRadius: 3 },
                { label: 'Val Loss',   data: [], borderColor: '#ef4444', backgroundColor: '#ef444420', tension: 0.3, fill: true, pointRadius: 3 },
            ]
        },
        options: commonOptions
    });
}

// ── Update Chart dari history ────────────────────────────────
function updateCharts(history) {
    if (!chartAcc || !chartLoss) initCharts();

    const labels     = history.map(h => 'E' + h.epoch);
    const trainAcc   = history.map(h => h.train_acc);
    const valAcc     = history.map(h => h.val_acc);
    const trainLoss  = history.map(h => h.train_loss);
    const valLoss    = history.map(h => h.val_loss);

    chartAcc.data.labels                  = labels;
    chartAcc.data.datasets[0].data        = trainAcc;
    chartAcc.data.datasets[1].data        = valAcc;
    chartAcc.update();

    chartLoss.data.labels                 = labels;
    chartLoss.data.datasets[0].data       = trainLoss;
    chartLoss.data.datasets[1].data       = valLoss;
    chartLoss.update();
}

// ── Pilih Batch / LR ────────────────────────────────────────
function selectBatch(btn) {
    document.querySelectorAll('.batch-btn').forEach(b => {
        b.classList.remove('bg-[#146135]', 'text-white', 'border-[#146135]');
        b.classList.add('bg-white', 'text-gray-600', 'border-gray-200');
    });
    btn.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');
    btn.classList.add('bg-[#146135]', 'text-white', 'border-[#146135]');
    selectedBatch = parseInt(btn.dataset.val);
}

function selectLr(btn) {
    document.querySelectorAll('.lr-btn').forEach(b => {
        b.classList.remove('bg-[#146135]', 'text-white', 'border-[#146135]');
        b.classList.add('bg-white', 'text-gray-600', 'border-gray-200');
    });
    btn.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');
    btn.classList.add('bg-[#146135]', 'text-white', 'border-[#146135]');
    selectedLr = parseFloat(btn.dataset.val);
}

// ── Training ─────────────────────────────────────────────────
function startTraining() {
    const epochs = document.getElementById('epochs').value;

    fetch('{{ route("admin.training.start") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ epochs: parseInt(epochs), batch_size: selectedBatch, learning_rate: selectedLr })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success || (data.data && data.data.success)) {
            showRunning();
            startPolling();
        } else {
            alert('Gagal mulai training: ' + (data.error || data.message || 'Unknown error'));
        }
    })
    .catch(() => alert('Tidak bisa terhubung ke server.'));
}

function stopTraining() {
    fetch('{{ route("admin.training.stop") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(() => stopPolling());
}

function startPolling() { pollingInterval = setInterval(fetchStatus, 2000); }
function stopPolling()  { if (pollingInterval) clearInterval(pollingInterval); }

function fetchStatus() {
    fetch('{{ route("admin.training.status") }}')
    .then(r => r.json())
    .then(data => {
        updateUI(data);
        if (!data.is_training && ['finished', 'error', 'stopped'].includes(data.status)) {
            stopPolling();
        }
    });
}

// ── Update UI ────────────────────────────────────────────────
function updateUI(data) {
    const cur = data.current_epoch || 0;
    const tot = data.total_epochs  || 0;
    const pct = tot > 0 ? Math.round(cur / tot * 100) : 0;

    document.getElementById('cur-epoch').textContent    = cur;
    document.getElementById('tot-epoch').textContent    = tot;
    document.getElementById('epoch-bar').style.width    = pct + '%';
    document.getElementById('status-message').textContent = data.message || '';
    document.getElementById('train-acc').textContent    = data.train_acc  ? data.train_acc  + '%' : '-';
    document.getElementById('val-acc').textContent      = data.val_acc    ? data.val_acc    + '%' : '-';
    document.getElementById('train-loss').textContent   = data.train_loss ?? '-';
    document.getElementById('val-loss').textContent     = data.val_loss   ?? '-';

    // History table + chart
    if (data.history && data.history.length > 0) {
        document.getElementById('history-section').classList.remove('hidden');

        // Tabel (terbaru di atas)
        const tbody = document.getElementById('history-tbody');
        tbody.innerHTML = '';
        [...data.history].reverse().forEach(h => {
            const isBest = h.val_acc === Math.max(...data.history.map(x => x.val_acc));
            tbody.innerHTML += `
                <tr class="text-gray-600 hover:bg-gray-50 ${isBest ? 'bg-[#f0faf4]' : ''}">
                    <td class="py-2 font-medium">${h.epoch} ${isBest ? '<span class="text-xs text-[#146135] font-semibold">★ Best</span>' : ''}</td>
                    <td class="py-2">${h.train_loss}</td>
                    <td class="py-2 text-[#146135] font-medium">${h.train_acc}%</td>
                    <td class="py-2">${h.val_loss}</td>
                    <td class="py-2 text-[#3eb872] font-medium">${h.val_acc}%</td>
                </tr>`;
        });

        // Chart
        updateCharts(data.history);
    }

    if      (data.status === 'finished') showFinished(data.message);
    else if (data.status === 'error')    showError(data.message);
    else if (data.status === 'stopped')  showIdle();
}

// ── Show States ──────────────────────────────────────────────
function showRunning() {
    ['status-idle','status-finished','status-error'].forEach(id => document.getElementById(id).classList.add('hidden'));
    document.getElementById('status-running').classList.remove('hidden');
    document.getElementById('btn-start').classList.add('hidden');
    document.getElementById('btn-stop').classList.remove('hidden');
}
function showFinished(msg) {
    document.getElementById('status-running').classList.add('hidden');
    document.getElementById('status-finished').classList.remove('hidden');
    document.getElementById('finish-message').textContent = msg || 'Training selesai!';
    document.getElementById('btn-start').classList.remove('hidden');
    document.getElementById('btn-stop').classList.add('hidden');
}
function showError(msg) {
    document.getElementById('status-running').classList.add('hidden');
    document.getElementById('status-error').classList.remove('hidden');
    document.getElementById('error-message').textContent = msg || 'Terjadi error.';
    document.getElementById('btn-start').classList.remove('hidden');
    document.getElementById('btn-stop').classList.add('hidden');
}
function showIdle() {
    document.getElementById('status-running').classList.add('hidden');
    document.getElementById('status-idle').classList.remove('hidden');
    document.getElementById('btn-start').classList.remove('hidden');
    document.getElementById('btn-stop').classList.add('hidden');
}

// ── Cek Flask ────────────────────────────────────────────────
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

// ── Resume jika training sudah berjalan saat buka halaman ───
@if($status && $status['is_training'])
    showRunning();
    startPolling();
@elseif($status && $status['status'] === 'finished' && !empty($status['history']))
    const existingStatus = @json($status);
    showFinished(existingStatus.message);
    updateUI(existingStatus);
@endif
</script>
@endpush

@endsection