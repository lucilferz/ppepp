<?php
$pageTitle   = 'Hasil Pengendalian & RTL — ' . htmlspecialchars($pengendalian['judul']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Pengendalian', 'url' => BASE_URL . '/pengendalian'],
  ['label' => htmlspecialchars($pengendalian['judul'])],
];

$penetapan   = $pengendalian['penetapan'] ?? [];
$evaluasi    = $pengendalian['evaluasi'] ?? [];
$details     = $pengendalian['details'] ?? [];
$pgId        = $pengendalian['id'];
$berkasList  = $pengendalian['berkas_list'] ?? [];

$totalStandar = count($details);
$cntTerpenuhi = 0;
$cntPerluRtl  = 0;

$kriteriaMap = [];
foreach ($details as $idx => $d) {
    $st = $d['status_capaian'] ?? 'belum_tercapai';
    if ($st === 'tercapai') {
        $cntTerpenuhi++;
    } else {
        $cntPerluRtl++;
    }

    $kId = (int)($d['kriteria_id'] ?? 0);
    $kKode = !empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD';
    $kNama = $d['kriteria_nama'] ?? 'Kriteria';
    if (!isset($kriteriaMap[$kId])) {
        $kriteriaMap[$kId] = [
            'id'    => $kId,
            'kode'  => $kKode,
            'nama'  => $kNama,
            'count' => 0
        ];
    }
    $kriteriaMap[$kId]['count']++;
}
?>

<!-- Header Banner -->
<div style="background:linear-gradient(135deg,#0f172a,#431407 60%,#7c2d12);border-radius:16px;padding:24px 28px;margin-bottom:22px;color:#fff;box-shadow:0 8px 24px rgba(15,23,42,0.12);">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
    <div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;flex-wrap:wrap;">
        <span style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#fdba74;">
          Tahap Pengendalian &amp; RTL (PPEPP)
        </span>
        <span style="background:<?= $pengendalian['status'] === 'final' ? 'rgba(16,185,129,0.2)' : 'rgba(245,158,11,0.2)' ?>;border:1px solid <?= $pengendalian['status'] === 'final' ? 'rgba(16,185,129,0.4)' : 'rgba(245,158,11,0.4)' ?>;border-radius:20px;padding:3px 12px;font-size:12px;color:<?= $pengendalian['status'] === 'final' ? '#34d399' : '#fbbf24' ?>;font-weight:700;">
          <?= $pengendalian['status'] === 'final' ? '✓ Final' : '⏳ Draft' ?>
        </span>
      </div>
      <h2 style="font-size:22px;font-weight:900;color:#fff;margin:0 0 10px;line-height:1.3;letter-spacing:-0.4px;">
        <?= htmlspecialchars($pengendalian['judul']) ?>
      </h2>
      <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;font-size:12.5px;color:rgba(255,255,255,0.75);">
        <span style="background:rgba(255,255,255,0.1);padding:4px 10px;border-radius:7px;">Acuan: <strong><?= htmlspecialchars($penetapan['judul'] ?? '—') ?></strong></span>
        <span style="background:rgba(255,255,255,0.1);padding:4px 10px;border-radius:7px;">TA: <strong><?= htmlspecialchars($penetapan['ta_nama'] ?? '—') ?></strong></span>
        <span style="background:rgba(255,255,255,0.1);padding:4px 10px;border-radius:7px;">Dibuat: <strong><?= date('d M Y', strtotime($pengendalian['created_at'])) ?></strong></span>
      </div>
      <?php if (!empty($pengendalian['deskripsi'])): ?>
      <p style="font-size:13px;color:rgba(255,255,255,0.8);margin-top:8px;font-style:italic;max-width:800px;"><?= htmlspecialchars($pengendalian['deskripsi']) ?></p>
      <?php endif; ?>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <a href="<?= BASE_URL ?>/pengendalian/<?= $pgId ?>/edit" class="btn btn-primary" style="font-weight:700;font-size:13px;background:#ea580c;border:none;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
          <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Edit RTL &amp; Berkas Koreksi
      </a>
      <?php if ($pengendalian['status'] === 'draft'): ?>
      <form action="<?= BASE_URL ?>/pengendalian/<?= $pgId ?>/finalize" method="POST" style="display:inline;" onsubmit="return confirm('Finalisasi dokumen pengendalian ini?')">
        <button type="submit" class="btn btn-success" style="font-weight:700;font-size:13px;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
            <path d="M9 11l3 3L22 4"/>
            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
          </svg>
          Finalisasi
        </button>
      </form>
      <?php endif; ?>
      <a href="<?= BASE_URL ?>/ppepp<?= !empty($pengendalian['ppepp_project_id']) ? '/' . $pengendalian['ppepp_project_id'] : '' ?>" class="btn btn-outline" style="font-size:13px;color:rgba(255,255,255,0.8);border-color:rgba(255,255,255,0.3);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
        ← Project Library
      </a>
    </div>
  </div>
</div>

<!-- Summary Metrics Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px;margin-bottom:22px;">
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
    <div style="width:44px;height:44px;background:#eef2ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" width="22" height="22"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
    </div>
    <div>
      <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Standar</div>
      <div style="font-size:24px;font-weight:900;color:#1e293b;line-height:1.2;margin-top:2px;"><?= $totalStandar ?> <span style="font-size:13px;font-weight:600;color:#64748b;">Standar</span></div>
    </div>
  </div>

  <div style="background:#fff;border:1.5px solid #a7f3d0;border-radius:14px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
    <div style="width:44px;height:44px;background:#ecfdf5;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" width="22" height="22"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <div>
      <div style="font-size:11.5px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.5px;">✓ Standar Terpenuhi</div>
      <div style="font-size:24px;font-weight:900;color:#065f46;line-height:1.2;margin-top:2px;"><?= $cntTerpenuhi ?> <span style="font-size:13px;font-weight:600;color:#059669;">Standar</span></div>
    </div>
  </div>

  <div style="background:#fff;border:1.5px solid #fed7aa;border-radius:14px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
    <div style="width:44px;height:44px;background:#fff7ed;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" width="22" height="22"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
    </div>
    <div>
      <div style="font-size:11.5px;font-weight:700;color:#ea580c;text-transform:uppercase;letter-spacing:0.5px;">⚠️ Perlu RTL &amp; Koreksi</div>
      <div style="font-size:24px;font-weight:900;color:#9a3412;line-height:1.2;margin-top:2px;"><?= $cntPerluRtl ?> <span style="font-size:13px;font-weight:600;color:#ea580c;">Standar</span></div>
    </div>
  </div>

  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
    <div style="width:44px;height:44px;background:#f8fafc;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" width="22" height="22"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
    </div>
    <div>
      <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Berkas RTL Terlampir</div>
      <div style="font-size:24px;font-weight:900;color:#1e293b;line-height:1.2;margin-top:2px;"><?= count($berkasList) ?> <span style="font-size:13px;font-weight:600;color:#64748b;">Berkas</span></div>
    </div>
  </div>
</div>

<!-- Toolbar: Search & Interactive Filters -->
<div class="card mb-4" style="border-radius:14px;border:1.5px solid #e2e8f0;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.02);margin-bottom:20px;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
    
    <!-- Search Bar -->
    <div style="flex:1;min-width:280px;position:relative;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" width="16" height="16" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="pengendalianSearchInput" onkeyup="filterPengendalian()" placeholder="Cari kode, nama standar, akar masalah, rencana tindak lanjut..."
             style="width:100%;padding:9px 12px 9px 36px;border:1.5px solid #cbd5e1;border-radius:10px;font-size:13px;outline:none;font-family:inherit;background:#f8fafc;"
             onfocus="this.style.background='#fff';this.style.borderColor='#ea580c';" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1';">
    </div>

    <!-- Info Counter -->
    <div id="pengendalianResultCount" style="font-size:12.5px;font-weight:700;color:#64748b;">
      Menampilkan <?= $totalStandar ?> standar
    </div>
  </div>

  <!-- Filter Pills Bar -->
  <div style="margin-top:14px;padding-top:12px;border-top:1px solid #f1f5f9;display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between;">
    
    <!-- Kriteria Pills -->
    <?php if (count($kriteriaMap) > 1): ?>
    <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
      <span style="font-size:11.5px;font-weight:700;color:#64748b;">Kriteria:</span>
      <button type="button" class="pg-kriteria-filter-btn active" data-kid="all" onclick="filterPengendalianKriteria('all', this)"
              style="border:none;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#ea580c;color:#fff;">
        Semua (<?= $totalStandar ?>)
      </button>
      <?php foreach ($kriteriaMap as $k): ?>
      <button type="button" class="pg-kriteria-filter-btn" data-kid="<?= $k['id'] ?>" onclick="filterPengendalianKriteria('<?= $k['id'] ?>', this)"
              style="border:1.5px solid #cbd5e1;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:600;cursor:pointer;background:#fff;color:#475569;">
        <?= htmlspecialchars($k['kode']) ?> (<?= $k['count'] ?>)
      </button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- RTL Status Pills -->
    <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
      <span style="font-size:11.5px;font-weight:700;color:#64748b;">Status RTL:</span>
      <button type="button" class="pg-status-filter-btn active" data-rtl="all" onclick="filterPengendalianRtl('all', this)"
              style="border:none;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#1e293b;color:#fff;">
        Semua Standar
      </button>
      <button type="button" class="pg-status-filter-btn" data-rtl="perlu" onclick="filterPengendalianRtl('perlu', this)"
              style="border:1.5px solid #fed7aa;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#fff7ed;color:#c2410c;">
        ⚠️ Perlu RTL (<?= $cntPerluRtl ?>)
      </button>
      <button type="button" class="pg-status-filter-btn" data-rtl="terpenuhi" onclick="filterPengendalianRtl('terpenuhi', this)"
              style="border:1.5px solid #a7f3d0;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#ecfdf5;color:#065f46;">
        ✓ Terpenuhi (<?= $cntTerpenuhi ?>)
      </button>
    </div>

  </div>
</div>

<!-- SECTION 1: RINCIAN STANDAR & RTL -->
<div id="pengendalianListContainer" style="display:flex;flex-direction:column;gap:16px;margin-bottom:28px;">
  <?php foreach ($details as $idx => $d): ?>
  <?php
  $stCapaian   = $d['status_capaian'] ?? 'belum_tercapai';
  $isTerpenuhi = ($stCapaian === 'tercapai');
  $rtlStatus   = $isTerpenuhi ? 'terpenuhi' : 'perlu';
  $kId         = (int)($d['kriteria_id'] ?? 0);
  $displayKode = !empty($d['kode']) ? $d['kode'] : (!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD-' . ($idx + 1));
  ?>

  <div class="pg-item-card" data-kid="<?= $kId ?>" data-rtl="<?= $rtlStatus ?>"
       style="border:1.5px solid <?= $isTerpenuhi ? '#a7f3d0' : '#fed7aa' ?>;border-radius:14px;overflow:hidden;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,0.02);transition:box-shadow 0.2s;">
    
    <?php if ($isTerpenuhi): ?>
    <!-- Header Standar Terpenuhi -->
    <div style="background:#f0fdf4;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="padding:4px 10px;background:#059669;color:#fff;border-radius:7px;font-weight:900;font-size:12px;letter-spacing:0.3px;">
          <?= htmlspecialchars($displayKode) ?>
        </div>
        <div>
          <div style="font-weight:800;font-size:15px;color:#065f46;"><?= htmlspecialchars($d['kriteria_nama']) ?></div>
          <div style="font-size:12px;color:#047857;margin-top:2px;">Target: <?= htmlspecialchars($d['target_capaian'] ?: '—') ?></div>
        </div>
      </div>
      <span style="font-size:12px;font-weight:800;padding:5px 14px;border-radius:20px;background:#059669;color:#fff;">
        ✓ Standar Terpenuhi (Tidak Perlu Tindakan Koreksi)
      </span>
    </div>

    <?php else: ?>
    <!-- Header Standar Memerlukan RTL -->
    <div style="background:#fff7ed;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;border-bottom:1.5px solid #fed7aa;flex-wrap:wrap;">
      <div style="display:flex;align-items:center;gap:10px;">
        <div style="padding:4px 10px;background:#ea580c;color:#fff;border-radius:7px;font-weight:900;font-size:12px;letter-spacing:0.3px;">
          <?= htmlspecialchars($displayKode) ?>
        </div>
        <div>
          <div style="font-weight:800;font-size:14.5px;color:#9a3412;"><?= htmlspecialchars($d['kriteria_nama']) ?></div>
          <?php if (!empty($d['kriteria_deskripsi'])): ?>
          <div style="font-size:11.5px;color:#c2410c;margin-top:1px;"><?= htmlspecialchars($d['kriteria_deskripsi']) ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div style="display:flex;align-items:center;gap:8px;">
        <span style="font-size:11.5px;font-weight:800;padding:4px 12px;border-radius:20px;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;">
          ⚠️ <?= $stCapaian === 'sebagian' ? 'Tercapai Sebagian' : 'Belum Tercapai' ?>
        </span>
      </div>
    </div>

    <!-- Body Standar RTL -->
    <div style="padding:18px 20px;display:flex;flex-direction:column;gap:14px;">
      
      <!-- Target & Indikator -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px;">
        <div style="background:#f0fdf4;border-left:4px solid #059669;padding:12px 16px;border-radius:0 10px 10px 0;">
          <div style="font-size:11px;font-weight:800;text-transform:uppercase;color:#065f46;margin-bottom:3px;letter-spacing:0.5px;">🎯 Target Acuan:</div>
          <div style="font-size:14px;color:#0f172a;line-height:1.6;font-weight:600;"><?= nl2br(htmlspecialchars($d['target_capaian'] ?: '—')) ?></div>
        </div>

        <div style="background:#fffbeb;border-left:4px solid #d97706;padding:12px 16px;border-radius:0 10px 10px 0;">
          <div style="font-size:11px;font-weight:800;text-transform:uppercase;color:#92400e;margin-bottom:3px;letter-spacing:0.5px;">📊 Indikator Mutu:</div>
          <div style="font-size:14px;color:#0f172a;line-height:1.6;font-weight:500;"><?= nl2br(htmlspecialchars($d['indikator'] ?: '—')) ?></div>
        </div>
      </div>

      <!-- Hasil Analisis Evaluasi -->
      <?php if (!empty($d['evaluasi_teks'])): ?>
      <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:10px;padding:12px 16px;font-size:13.5px;line-height:1.65;color:#7f1d1d;">
        <strong style="color:#991b1b;">📝 Temuan &amp; Analisis Evaluasi:</strong> <?= nl2br(htmlspecialchars($d['evaluasi_teks'])) ?>
      </div>
      <?php endif; ?>

      <!-- Akar Masalah & Tindakan Koreksi (RTL) -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px;">
        <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:16px;">
          <div style="font-size:11.5px;font-weight:800;text-transform:uppercase;color:#ea580c;margin-bottom:6px;letter-spacing:0.5px;display:flex;align-items:center;gap:6px;">
            <span>🔍</span> Identifikasi Akar Masalah:
          </div>
          <div style="font-size:13.5px;color:#1e293b;line-height:1.65;white-space:pre-wrap;">
            <?= !empty($d['akar_masalah']) ? htmlspecialchars($d['akar_masalah']) : '<span style="color:#94a3b8;font-style:italic;">Belum dirumuskan</span>' ?>
          </div>
        </div>

        <div style="background:#fff;border:1.5px solid #fed7aa;border-radius:12px;padding:16px;">
          <div style="font-size:11.5px;font-weight:800;text-transform:uppercase;color:#c2410c;margin-bottom:6px;letter-spacing:0.5px;display:flex;align-items:center;gap:6px;">
            <span>🛠️</span> Rencana Tindakan Koreksi (RTL):
          </div>
          <div style="font-size:13.5px;color:#1e293b;line-height:1.65;white-space:pre-wrap;">
            <?= !empty($d['rencana_tindak_lanjut']) ? htmlspecialchars($d['rencana_tindak_lanjut']) : '<span style="color:#94a3b8;font-style:italic;">Belum dirumuskan</span>' ?>
          </div>
        </div>
      </div>

      <!-- Usulan Koreksi Standar untuk Siklus Berikutnya -->
      <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;padding:16px;">
        <div style="font-size:11.5px;font-weight:800;text-transform:uppercase;color:#92400e;margin-bottom:6px;letter-spacing:0.5px;display:flex;align-items:center;gap:6px;">
          <span>🔄</span> Usulan Koreksi / Penyesuaian Standar untuk Siklus Berikutnya:
        </div>
        <div style="font-size:13.5px;color:#1e293b;line-height:1.65;white-space:pre-wrap;">
          <?= !empty($d['koreksi_standar']) ? htmlspecialchars($d['koreksi_standar']) : '<span style="color:#94a3b8;font-style:italic;">Belum ada usulan koreksi standar</span>' ?>
        </div>
      </div>

    </div>
    <?php endif; ?>

  </div>
  <?php endforeach; ?>
</div>

<!-- SECTION 2: BERKAS NOTULENSI RAPAT RTM & BERKAS KOREKSI STANDAR -->
<div class="card" style="border-radius:16px;border:1.5px solid #fed7aa;overflow:hidden;box-shadow:0 4px 16px rgba(234,88,12,0.06);margin-bottom:30px;">
  <div class="card-header" style="background:linear-gradient(135deg,#fff7ed,#ffedd5);padding:16px 22px;border-bottom:1.5px solid #fed7aa;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <h3 class="card-title" style="font-size:15px;font-weight:800;color:#9a3412;margin:0;display:flex;align-items:center;gap:8px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" width="18" height="18"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Berkas Notulensi Rapat RTM &amp; Berkas Koreksi Standar
    </h3>
    <span style="font-size:12px;font-weight:700;color:#c2410c;background:#fff;padding:3px 10px;border-radius:20px;border:1px solid #fed7aa;">
      <?= count($berkasList) ?> Berkas
    </span>
  </div>

  <div class="card-body" style="padding:22px;">
    <?php if (empty($berkasList)): ?>
    <div style="padding:24px;text-align:center;color:#94a3b8;font-style:italic;font-size:13px;">
      Belum ada berkas pendukung tindakan koreksi yang diunggah.
    </div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:8px;">
      <?php foreach ($berkasList as $b): ?>
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
          <div style="font-weight:800;font-size:13.5px;color:#1e293b;"><?= htmlspecialchars($b['judul'] ?? $b['file_name']) ?></div>
          <?php if (!empty($b['keterangan'])): ?>
          <div style="font-size:12px;color:#64748b;margin-top:2px;"><?= htmlspecialchars($b['keterangan']) ?></div>
          <?php endif; ?>
        </div>
        <a href="<?= htmlspecialchars($b['url'] ?? BASE_URL . '/' . $b['file_path']) ?>" target="_blank"
           class="btn btn-outline btn-sm" style="color:#059669;border-color:#10b981;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
          📄 Buka &amp; Download Berkas ↗
        </a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
var currentPgKid = 'all';
var currentPgRtl = 'all';

function filterPengendalian() {
  var query = (document.getElementById('pengendalianSearchInput')?.value || '').toLowerCase().trim();
  var items = document.querySelectorAll('#pengendalianListContainer .pg-item-card');
  var visibleCount = 0;

  items.forEach(function(item) {
    var kid = item.getAttribute('data-kid');
    var rtl = item.getAttribute('data-rtl');
    var text = item.innerText.toLowerCase();

    var matchKid = (currentPgKid === 'all' || kid === currentPgKid);
    var matchRtl = (currentPgRtl === 'all' || rtl === currentPgRtl);
    var matchQuery = (!query || text.indexOf(query) !== -1);

    if (matchKid && matchRtl && matchQuery) {
      item.style.display = '';
      visibleCount++;
    } else {
      item.style.display = 'none';
    }
  });

  var countEl = document.getElementById('pengendalianResultCount');
  if (countEl) {
    countEl.textContent = 'Menampilkan ' + visibleCount + ' standar';
  }
}

function filterPengendalianKriteria(kid, btnEl) {
  currentPgKid = String(kid);
  document.querySelectorAll('.pg-kriteria-filter-btn').forEach(function(btn) {
    btn.style.background = '#fff';
    btn.style.color = '#475569';
    btn.style.borderColor = '#cbd5e1';
    btn.classList.remove('active');
  });
  if (btnEl) {
    btnEl.style.background = '#ea580c';
    btnEl.style.color = '#fff';
    btnEl.style.borderColor = '#ea580c';
    btnEl.classList.add('active');
  }
  filterPengendalian();
}

function filterPengendalianRtl(rtlStatus, btnEl) {
  currentPgRtl = rtlStatus;
  document.querySelectorAll('.pg-status-filter-btn').forEach(function(btn) {
    btn.style.background = '#f8fafc';
    btn.style.color = '#64748b';
    btn.style.borderColor = '#e2e8f0';
    btn.classList.remove('active');
  });
  if (btnEl) {
    if (rtlStatus === 'perlu') {
      btnEl.style.background = '#fff7ed';
      btnEl.style.color = '#c2410c';
      btnEl.style.borderColor = '#fed7aa';
    } else if (rtlStatus === 'terpenuhi') {
      btnEl.style.background = '#ecfdf5';
      btnEl.style.color = '#065f46';
      btnEl.style.borderColor = '#a7f3d0';
    } else {
      btnEl.style.background = '#1e293b';
      btnEl.style.color = '#fff';
      btnEl.style.borderColor = '#1e293b';
    }
    btnEl.classList.add('active');
  }
  filterPengendalian();
}
</script>
