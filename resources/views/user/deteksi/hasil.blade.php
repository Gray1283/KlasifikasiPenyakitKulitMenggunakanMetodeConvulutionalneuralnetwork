@extends('layouts.navbar')

@section('title', 'Hasil Klasifikasi — DermaAI')
@section('page_title', 'Hasil Klasifikasi')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap');
  :root{--g600:#146135;--g400:#3eb872;--g100:#d1f0de;--g50:#f0faf4;--navy:#0d1f2d;--muted:#5a7080;--cream:#f9f7f2}
  .hasil-wrap{background:var(--cream);min-height:calc(100vh - 60px);padding:2rem;display:flex;flex-direction:column;gap:1.2rem;align-items:center}

  /* back bar */
  .back-bar{width:100%;max-width:1060px;display:flex;align-items:center;gap:.8rem}
  .btn-back{display:inline-flex;align-items:center;gap:6px;background:white;border:1px solid rgba(0,0,0,.08);border-radius:9px;padding:7px 14px;font-family:'DM Sans',sans-serif;font-size:.85rem;color:var(--muted);cursor:pointer;transition:color .15s,background .15s;text-decoration:none}
  .btn-back:hover{background:var(--g50);color:var(--g600)}
  .btn-back svg{width:15px;height:15px}
  .back-title{font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:var(--navy)}

  /* main card */
  .result-card{width:100%;max-width:1060px;background:white;border-radius:20px;border:1px solid rgba(0,0,0,.07);overflow:hidden;display:grid;grid-template-columns:1fr 1.15fr}

  /* image panel */
  .img-panel{background:var(--navy);padding:2rem;display:flex;flex-direction:column;gap:1.2rem}
  .img-label{font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,.4)}
  .img-wrap{border-radius:12px;overflow:hidden;aspect-ratio:1;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1)}
  .img-wrap img{width:100%;height:100%;object-fit:cover;display:block}
  .img-meta{display:flex;gap:8px;flex-wrap:wrap}
  .meta-chip{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:8px;padding:5px 11px;font-size:.76rem;color:rgba(255,255,255,.5);font-family:'DM Sans',sans-serif}
  .meta-chip span{color:white;font-weight:500}

  /* result panel */
  .res-panel{padding:2rem 2.2rem;display:flex;flex-direction:column;gap:1.5rem;overflow-y:auto}

  .result-badge{display:inline-flex;align-items:center;gap:6px;background:var(--g50);color:var(--g600);font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:4px 12px;border-radius:100px;border:1px solid var(--g100);width:fit-content}
  .badge-dot{width:6px;height:6px;background:var(--g400);border-radius:50%;animation:pulse 2s infinite}
  .disease-name{font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:var(--navy);line-height:1.15;margin-top:.3rem}

  /* confidence */
  .conf-wrap{display:flex;flex-direction:column;gap:7px}
  .conf-header{display:flex;justify-content:space-between;align-items:center}
  .conf-label{font-size:.8rem;color:var(--muted);font-weight:500}
  .conf-value{font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;color:var(--g600)}
  .conf-bg{height:8px;background:var(--g50);border-radius:100px;overflow:hidden;border:1px solid var(--g100)}
  .conf-fill{height:100%;background:var(--g600);border-radius:100px;width:0;transition:width 1s ease}
  .conf-tiers{display:flex;justify-content:space-between;font-size:.7rem;color:var(--muted)}

  .divider{height:1px;background:rgba(0,0,0,.06)}
  .section-label{font-size:.7rem;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--muted);margin-bottom:5px;display:block}
  .desc-text{font-size:.9rem;color:var(--muted);line-height:1.75;font-weight:300}

  .rekom-list{list-style:none;display:flex;flex-direction:column;gap:5px}
  .rekom-list li{display:flex;align-items:flex-start;gap:9px;font-size:.87rem;color:var(--navy);line-height:1.55}
  .rekom-list li::before{content:'';width:6px;height:6px;background:var(--g400);border-radius:50%;flex-shrink:0;margin-top:7px}

  .saran-box{background:var(--g50);border:1px solid var(--g100);border-radius:12px;padding:.9rem 1.1rem;font-size:.86rem;color:#0e4726;line-height:1.65}

  .actions{display:flex;gap:10px;flex-wrap:wrap}
  .btn-primary{display:inline-flex;align-items:center;gap:7px;background:var(--g600);color:white;padding:10px 22px;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.88rem;font-weight:600;border:none;cursor:pointer;transition:background .2s,transform .15s;text-decoration:none}
  .btn-primary:hover{background:#0e4726;transform:translateY(-1px)}
  .btn-outline{display:inline-flex;align-items:center;gap:7px;background:transparent;color:var(--navy);padding:10px 20px;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.88rem;font-weight:500;border:1.5px solid rgba(0,0,0,.12);text-decoration:none;transition:border-color .2s,background .2s}
  .btn-outline:hover{border-color:var(--g400);background:var(--g50)}
  .actions svg{width:15px;height:15px}

  /* disclaimer */
  .disclaimer{width:100%;max-width:1060px;background:white;border:1px solid rgba(0,0,0,.07);border-radius:12px;padding:.9rem 1.3rem;display:flex;align-items:flex-start;gap:10px}
  .disc-icon{width:30px;height:30px;flex-shrink:0;background:#FFF8E1;border-radius:8px;display:flex;align-items:center;justify-content:center}
  .disc-icon svg{width:15px;height:15px;color:#B45309}
  .disc-text{font-size:.82rem;color:var(--muted);line-height:1.6}
  .disc-text strong{color:var(--navy)}

  @keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
  @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
  .res-panel>*{animation:fadeUp .35s ease both}
  .res-panel>*:nth-child(1){animation-delay:.05s}
  .res-panel>*:nth-child(2){animation-delay:.1s}
  .res-panel>*:nth-child(3){animation-delay:.15s}
  .res-panel>*:nth-child(4){animation-delay:.2s}
  .res-panel>*:nth-child(5){animation-delay:.25s}
  .res-panel>*:nth-child(6){animation-delay:.3s}
  .res-panel>*:nth-child(7){animation-delay:.35s}

  @media(max-width:768px){
    .hasil-wrap{padding:1rem}
    .result-card{grid-template-columns:1fr}
    .disease-name{font-size:1.4rem}
    .res-panel{padding:1.5rem}
    .img-panel{padding:1.5rem}
  }
</style>

<div class="hasil-wrap">

  {{-- Back bar --}}
  <div class="back-bar">
    <a href="{{ route('deteksi.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Kembali
    </a>
    <span class="back-title">Hasil Klasifikasi</span>
  </div>

  {{-- Result card --}}
  <div class="result-card">

    {{-- Kiri: foto --}}
    <div class="img-panel">
      <span class="img-label">Foto yang dianalisis</span>
      <div class="img-wrap">
        <img src="{{ $gambar }}" alt="Foto kulit yang diunggah">
      </div>
      <div class="img-meta">
        <div class="meta-chip">Ukuran: <span>{{ $ukuran }}</span></div>
        <div class="meta-chip">Waktu: <span>{{ $waktu }}</span></div>
      </div>
    </div>

    {{-- Kanan: hasil --}}
    <div class="res-panel">

      <div>
        <div class="result-badge"><span class="badge-dot"></span> Analisis Selesai</div>
        <h2 class="disease-name">{{ $nama_penyakit }}</h2>
      </div>

      <div class="conf-wrap">
        <div class="conf-header">
          <span class="conf-label">Confidence Score</span>
          <span class="conf-value">{{ $confidence }}%</span>
        </div>
        <div class="conf-bg">
          <div class="conf-fill" id="conf-fill-bar"></div>
        </div>
        <div class="conf-tiers">
          <span>Rendah</span><span>Sedang</span><span>Tinggi</span>
        </div>
      </div>

      <div class="divider"></div>

      <div>
        <span class="section-label">Deskripsi Penyakit</span>
        <p class="desc-text">{{ $deskripsi }}</p>
      </div>

      @if(!empty($rekomendasi))
      <div class="divider"></div>
      <div>
        <span class="section-label">Rekomendasi Penanganan</span>
        <ul class="rekom-list">
          @foreach($rekomendasi as $r)
            <li>{{ $r }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      @if(!empty($saran))
      <div class="saran-box">
        <strong>Saran Tambahan:</strong> {{ $saran }}
      </div>
      @endif

      <div class="actions">
        <button class="btn-primary" onclick="window.print()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          Cetak Laporan
        </button>
        <a href="{{ route('deteksi.index') }}" class="btn-outline">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          Deteksi Ulang
        </a>
      </div>

    </div>
  </div>

  {{-- Disclaimer --}}
  <div class="disclaimer">
    <div class="disc-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    </div>
    <p class="disc-text">
      <strong>Penting:</strong> Hasil klasifikasi ini bersifat informatif dan tidak menggantikan diagnosis medis profesional. Selalu konsultasikan kondisi kulit Anda kepada dokter spesialis kulit untuk penanganan yang tepat.
    </p>
  </div>

</div>

@push('scripts')
<script>
  // Set confidence bar width via JS agar tidak ada masalah Blade di dalam CSS
  document.addEventListener('DOMContentLoaded', () => {
    const bar = document.getElementById('conf-fill-bar');
    if (bar) bar.style.width = '{{ $confidence }}%';
  });
</script>
@endpush
@endsection