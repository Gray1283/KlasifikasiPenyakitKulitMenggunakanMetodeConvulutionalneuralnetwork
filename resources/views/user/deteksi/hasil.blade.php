{{-- resources/views/deteksi/hasil.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hasil Klasifikasi — DermaAI</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --g600: #146135; --g400: #3eb872; --g100: #d1f0de; --g50: #f0faf4;
      --navy: #0d1f2d; --muted: #5a7080; --cream: #f9f7f2;
      --r: 14px; --rl: 20px;
    }
    body { font-family: 'DM Sans', sans-serif; background: var(--cream); color: var(--navy); min-height: 100vh; display: flex; flex-direction: column; }

    /* ── HEADER ── */
    header {
      background: white;
      border-bottom: 1px solid rgba(0,0,0,0.07);
      padding: 0 2rem;
      height: 60px;
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .btn-back {
      display: flex; align-items: center; gap: 6px;
      background: none; border: none; cursor: pointer;
      color: var(--muted); font-size: 0.88rem; font-family: inherit;
      padding: 6px 10px; border-radius: 8px;
      transition: background 0.15s, color 0.15s;
    }
    .btn-back:hover { background: var(--g50); color: var(--g600); }
    .btn-back svg { width: 16px; height: 16px; }
    header h1 { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--navy); }

    /* ── MAIN ── */
    main {
      flex: 1;
      display: grid;
      grid-template-columns: 1fr 1.1fr;
      gap: 0;
      max-width: 1100px;
      margin: 2rem auto;
      width: calc(100% - 4rem);
      background: white;
      border-radius: var(--rl);
      border: 1px solid rgba(0,0,0,0.07);
      overflow: hidden;
    }

    /* ── LEFT: IMAGE ── */
    .image-panel {
      padding: 2rem;
      background: var(--navy);
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
      position: relative;
    }
    .img-label {
      font-size: 0.72rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.4);
    }
    .img-wrap {
      border-radius: var(--r);
      overflow: hidden;
      aspect-ratio: 1;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.1);
    }
    .img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .img-meta {
      display: flex;
      gap: 10px;
    }
    .meta-chip {
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 8px;
      padding: 6px 12px;
      font-size: 0.78rem;
      color: rgba(255,255,255,0.55);
    }
    .meta-chip span { color: white; font-weight: 500; }

    /* ── RIGHT: RESULT ── */
    .result-panel {
      padding: 2rem 2.2rem;
      display: flex;
      flex-direction: column;
      gap: 1.6rem;
    }

    .result-top { display: flex; flex-direction: column; gap: 0.5rem; }

    .result-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--g50);
      color: var(--g600);
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      padding: 4px 12px;
      border-radius: 100px;
      border: 1px solid var(--g100);
      width: fit-content;
    }
    .result-badge .dot { width: 6px; height: 6px; background: var(--g400); border-radius: 50%; animation: pulse 2s infinite; }

    .disease-name {
      font-family: 'Syne', sans-serif;
      font-size: 1.9rem;
      font-weight: 800;
      color: var(--navy);
      line-height: 1.15;
    }

    /* ── CONFIDENCE ── */
    .confidence-wrap { display: flex; flex-direction: column; gap: 8px; }
    .conf-header { display: flex; justify-content: space-between; align-items: center; }
    .conf-label { font-size: 0.8rem; color: var(--muted); font-weight: 500; }
    .conf-value {
      font-family: 'Syne', sans-serif;
      font-size: 1.4rem;
      font-weight: 800;
      color: var(--g600);
    }
    .conf-bar-bg {
      height: 8px;
      background: var(--g50);
      border-radius: 100px;
      overflow: hidden;
      border: 1px solid var(--g100);
    }
    .conf-bar-fill {
    height: 100%;
    background: var(--g600);
    border-radius: 100px;
    width: 0%;
    transition: width 1s ease;
}
    .conf-tiers {
      display: flex;
      justify-content: space-between;
      font-size: 0.7rem;
      color: var(--muted);
    }

    /* ── DIVIDER ── */
    .divider { height: 1px; background: rgba(0,0,0,0.06); }

    /* ── DESCRIPTION ── */
    .section-label {
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.07em;
      text-transform: uppercase;
      color: var(--muted);
      margin-bottom: 6px;
    }
    .description-text {
      font-size: 0.9rem;
      color: var(--muted);
      line-height: 1.75;
      font-weight: 300;
    }

    /* ── REKOMENDASI ── */
    .rekom-list {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .rekom-list li {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 0.87rem;
      color: var(--navy);
      line-height: 1.55;
    }
    .rekom-list li::before {
      content: '';
      width: 6px;
      height: 6px;
      background: var(--g400);
      border-radius: 50%;
      flex-shrink: 0;
      margin-top: 7px;
    }

    /* ── SARAN TAMBAHAN ── */
    .saran-box {
      background: var(--g50);
      border: 1px solid var(--g100);
      border-radius: var(--r);
      padding: 1rem 1.2rem;
      font-size: 0.87rem;
      color: #0e4726;
      line-height: 1.65;
    }

    /* ── ACTIONS ── */
    .actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .btn-primary {
      display: inline-flex; align-items: center; gap: 7px;
      background: var(--g600); color: white;
      padding: 10px 22px; border-radius: 10px;
      font-family: inherit; font-size: 0.88rem; font-weight: 600;
      border: none; cursor: pointer;
      transition: background 0.2s, transform 0.15s;
    }
    .btn-primary:hover { background: #0e4726; transform: translateY(-1px); }
    .btn-outline {
      display: inline-flex; align-items: center; gap: 7px;
      background: transparent; color: var(--navy);
      padding: 10px 20px; border-radius: 10px;
      font-family: inherit; font-size: 0.88rem; font-weight: 500;
      border: 1.5px solid rgba(0,0,0,0.12); cursor: pointer;
      transition: border-color 0.2s, background 0.2s;
    }
    .btn-outline:hover { border-color: var(--g400); background: var(--g50); }
    .btn-icon svg { width: 15px; height: 15px; }

    /* ── DISCLAIMER ── */
    .disclaimer {
      max-width: 1100px;
      margin: 0 auto 2rem;
      width: calc(100% - 4rem);
      background: white;
      border: 1px solid rgba(0,0,0,0.07);
      border-radius: var(--r);
      padding: 1rem 1.4rem;
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }
    .disc-icon {
      width: 32px; height: 32px; flex-shrink: 0;
      background: #FFF8E1;
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
    }
    .disc-icon svg { width: 16px; height: 16px; color: #B45309; }
    .disc-text { font-size: 0.83rem; color: var(--muted); line-height: 1.6; }
    .disc-text strong { color: var(--navy); }

    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
    @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    .result-panel > * { animation: fadeUp 0.4s ease both; }
    .result-panel > *:nth-child(1){animation-delay:.05s}
    .result-panel > *:nth-child(2){animation-delay:.1s}
    .result-panel > *:nth-child(3){animation-delay:.15s}
    .result-panel > *:nth-child(4){animation-delay:.2s}
    .result-panel > *:nth-child(5){animation-delay:.25s}
    .result-panel > *:nth-child(6){animation-delay:.3s}
    .result-panel > *:nth-child(7){animation-delay:.35s}

    @media (max-width: 768px) {
      main { grid-template-columns: 1fr; margin: 1rem; width: calc(100% - 2rem); }
      .image-panel { padding: 1.5rem; }
      .result-panel { padding: 1.5rem; }
      .disclaimer { margin: 0 1rem 1.5rem; width: calc(100% - 2rem); }
      .disease-name { font-size: 1.5rem; }
    }
  </style>
</head>
<body>

  <header>
    <button class="btn-back" onclick="history.back()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Kembali
    </button>
    <h1>Hasil Klasifikasi</h1>
  </header>

  <main>
    {{-- LEFT: Foto --}}
    <div class="image-panel">
      <span class="img-label">Foto yang dianalisis</span>
      <div class="img-wrap">
        <img src="{{ $gambar ?? asset('images/sample-skin.jpg') }}" alt="Foto kulit yang diunggah">
      </div>
      <div class="img-meta">
        <div class="meta-chip">Ukuran: <span>{{ $ukuran ?? '2.4 MB' }}</span></div>
        <div class="meta-chip">Waktu: <span>{{ $waktu ?? now()->format('H:i') }}</span></div>
      </div>
    </div>

    {{-- RIGHT: Hasil --}}
    <div class="result-panel">

      {{-- Badge status --}}
      <div class="result-top">
        <div class="result-badge"><span class="dot"></span> Analisis Selesai</div>
        <h2 class="disease-name">{{ $nama_penyakit ?? 'Tinea Corporis' }}</h2>
      </div>

      {{-- Confidence score --}}
      <div class="confidence-wrap">
        <div class="conf-header">
          <span class="conf-label">Confidence Score</span>
          <span class="conf-value">{{ $confidence ?? 94 }}%</span>
        </div>
        <div class="conf-bar-bg">
          <div class="conf-bar-fill"></div>
        </div>
        <div class="conf-tiers">
          <span>Rendah</span><span>Sedang</span><span>Tinggi</span>
        </div>
      </div>

      <div class="divider"></div>

      {{-- Deskripsi --}}
      <div>
        <p class="section-label">Deskripsi Penyakit</p>
        <p class="description-text">{{ $deskripsi ?? 'Tinea corporis (kurap badan) adalah infeksi jamur superfisial yang menyerang lapisan kulit luar. Ditandai dengan bercak merah melingkar, bersisik di tepinya, dan terasa gatal.' }}</p>
      </div>

      <div class="divider"></div>

      {{-- Rekomendasi --}}
      <div>
        <p class="section-label">Rekomendasi Penanganan</p>
        <ul class="rekom-list">
          @foreach ($rekomendasi ?? ['Gunakan krim antijamur topikal (clotrimazole/miconazole) 2x sehari', 'Jaga area yang terinfeksi tetap bersih dan kering', 'Hindari berbagi handuk atau pakaian dengan orang lain', 'Periksakan ke dokter jika tidak membaik dalam 2 minggu'] as $r)
            <li>{{ $r }}</li>
          @endforeach
        </ul>
      </div>

      {{-- Saran Tambahan --}}
      @if (!empty($saran))
      <div class="saran-box">
        <strong>Saran Tambahan:</strong> {{ $saran }}
      </div>
      @endif

      {{-- Tombol aksi --}}
      <div class="actions">
        <button class="btn-primary btn-icon" onclick="window.print()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          Cetak Laporan
        </button>
        <a href="{{ route('deteksi.index') }}" class="btn-outline btn-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          Deteksi Ulang
        </a>
      </div>

    </div>
  </main>

  <div class="disclaimer">
    <div class="disc-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    </div>
    <p class="disc-text">
      <strong>Penting:</strong> Hasil klasifikasi ini bersifat informatif dan tidak menggantikan diagnosis medis profesional. Selalu konsultasikan kondisi kulit Anda kepada dokter spesialis kulit (dermatologis) untuk penanganan yang tepat.
    </p>
  </div>

</body>
</html>