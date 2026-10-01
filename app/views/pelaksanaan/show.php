<?php
$pageTitle   = htmlspecialchars($pelaksanaan['judul']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Pelaksanaan', 'url' => BASE_URL . '/pelaksanaan'],
  ['label' => $pelaksanaan['judul']],
];
$p = $pelaksanaan;
$pen = $p['penetapan'] ?? [];
?>

<!-- Header Info -->
<div style="background:linear-gradient(135deg,#0f172a,#1a237e);border-radius:14px;padding:22px 26px;margin-bottom:22px;">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
    <div>
      <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:rgba(255,255,255,0.4);margin-bottom:7px;">Pelaksanaan PPEPP</div>
      <h2 style="font-size:20px;font-weight:800;color:#fff;letter-spacing:-0.4px;margin-bottom:8px;"><?= htmlspecialchars($p['judul']) ?></h2>
      <div style="display:flex;gap:9px;flex-wrap:wrap;align-items:center;">
        <span style="background:rgba(255,255,255,0.1);border-radius:8px;padding:5px 12px;font-size:12px;color:rgba(255,255,255,0.7);">
          Acuan: <?= htmlspecialchars($pen['judul'] ?? '—') ?>
        </span>
        <span style="background:rgba(255,255,255,0.1);border-radius:8px;padding:5px 12px;font-size:12px;color:rgba(255,255,255,0.7);">
          <?= htmlspecialchars($pen['ta_nama'] ?? '') ?>
        </span>
        <?php if ($p['status'] === 'final'): ?>
        <span style="background:rgba(16,185,129,0.2);border:1px solid rgba(16,185,129,0.4);border-radius:8px;padding:5px 12px;font-size:12px;color:#34d399;font-weight:600;">✓ Final</span>
        <?php else: ?>
        <span style="background:rgba(245,158,11,0.2);border:1px solid rgba(245,158,11,0.4);border-radius:8px;padding:5px 12px;font-size:12px;color:#fbbf24;font-weight:600;">Draft</span>
        <?php endif; ?>
      </div>
    </div>
    <div style="display:flex;gap:9px;flex-wrap:wrap;">
      <a href="<?= BASE_URL ?>/pelaksanaan/<?= $p['id'] ?>/edit" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit Pelaksanaan
      </a>
      <?php if ($p['status'] !== 'final'): ?>
      <form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $p['id'] ?>/finalize" onsubmit="return confirm('Finalisasi pelaksanaan ini?')">
        <button type="submit" class="btn btn-success">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          Finalisasi
        </button>
      </form>
      <?php endif; ?>
      <?php $pelVis = $p['visibility_status'] ?? 'aktif'; ?>
      <?php if ($pelVis === 'aktif'): ?>
      <form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $p['id'] ?>/toggle-status" onsubmit="return confirm('Nonaktifkan pelaksanaan ini?\nDokumen akan berstatus Nonaktif tetapi tetap terlihat di daftar.')" style="display:inline;">
        <input type="hidden" name="visibility_status" value="nonaktif">
        <button type="submit" class="btn btn-outline" style="color:#fde047;border-color:rgba(253,224,71,0.4);">
          Nonaktifkan
        </button>
      </form>
      <?php elseif ($pelVis === 'nonaktif'): ?>
      <form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $p['id'] ?>/toggle-status" style="display:inline;">
        <input type="hidden" name="visibility_status" value="aktif">
        <button type="submit" class="btn btn-outline" style="color:#86efac;border-color:rgba(134,239,172,0.4);">
          Aktifkan
        </button>
      </form>
      <form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $p['id'] ?>/toggle-status" onsubmit="return confirm('Sembunyikan (Hide) pelaksanaan ini?')" style="display:inline;">
        <input type="hidden" name="visibility_status" value="hidden">
        <button type="submit" class="btn btn-outline" style="color:rgba(255,255,255,0.7);border-color:rgba(255,255,255,0.3);">
          Hide
        </button>
      </form>
      <?php else: ?>
      <form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $p['id'] ?>/toggle-status" style="display:inline;">
        <input type="hidden" name="visibility_status" value="aktif">
        <button type="submit" class="btn btn-outline" style="color:#86efac;border-color:rgba(134,239,172,0.4);">
          Aktifkan Kembali
        </button>
      </form>
      <?php endif; ?>
      <a href="<?= BASE_URL ?>/ppepp<?= !empty($p['ppepp_project_id']) ? '/' . $p['ppepp_project_id'] : '' ?>" class="btn btn-outline" style="color:rgba(255,255,255,0.7);border-color:rgba(255,255,255,0.2);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
        ← Kembali ke Project Library
      </a>
    </div>
  </div>
</div>

<!-- Info & Checklist Summary -->
<?php
$cStats = $p['checklist_stats'] ?? ['total' => count($p['details']), 'terlaksana' => 0, 'proses' => 0, 'belum' => count($p['details']), 'pct' => 0];

// Ekstraksi kriteria unik untuk filter
$kriteriaMap = [];
foreach ($p['details'] as $det) {
    $kId = (int)$det['kriteria_id'];
    $kKode = !empty($det['kriteria_kode']) ? $det['kriteria_kode'] : 'STD';
    $kNama = $det['kriteria_nama'] ?? 'Kriteria';
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
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin-bottom:22px;">
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(26,35,126,0.04);">
    <div style="width:42px;height:42px;background:#eef2ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#3f51b5" stroke-width="2" width="20" height="20"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
    </div>
    <div>
      <div style="font-size:11.5px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Total Standar</div>
      <div style="font-size:22px;font-weight:900;color:#1e293b;line-height:1.2;margin-top:2px;"><?= count($p['details']) ?> <span style="font-size:13px;font-weight:600;color:#64748b;">Standar</span></div>
    </div>
  </div>

  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(26,35,126,0.04);">
    <div style="width:42px;height:42px;background:#ecfdf5;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" width="20" height="20"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <div style="flex:1;">
      <div style="display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:11.5px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Keterlaksanaan Target</span>
        <span style="font-size:13px;font-weight:900;color:#059669;" id="summaryPctText"><?= $cStats['pct'] ?>%</span>
      </div>
      <div style="height:7px;background:#e2e8f0;border-radius:99px;margin-top:6px;overflow:hidden;">
        <div id="summaryProgressBar" style="height:100%;width:<?= $cStats['pct'] ?>%;background:linear-gradient(90deg,#059669,#10b981);border-radius:99px;transition:width 0.3s;"></div>
      </div>
      <div style="font-size:11.5px;color:#64748b;margin-top:5px;display:flex;gap:8px;font-weight:600;">
        <span style="color:#059669;" id="summaryTerlaksanaCount">✓ <?= $cStats['terlaksana'] ?> Selesai</span> •
        <span style="color:#d97706;" id="summaryProsesCount">⏳ <?= $cStats['proses'] ?> Proses</span> •
        <span style="color:#64748b;" id="summaryBelumCount">✕ <?= $cStats['belum'] ?> Belum</span>
      </div>
    </div>
  </div>

  <?php
  $totalBukti = 0;
  foreach ($p['details'] as $d) $totalBukti += count($d['bukti']);
  ?>
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(26,35,126,0.04);">
    <div style="width:42px;height:42px;background:#f0f9ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" width="20" height="20"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
    </div>
    <div>
      <div style="font-size:11.5px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Total Bukti Terlampir</div>
      <div style="font-size:22px;font-weight:900;color:#1e293b;line-height:1.2;margin-top:2px;"><?= $totalBukti ?> <span style="font-size:13px;font-weight:600;color:#64748b;">Link Bukti</span></div>
    </div>
  </div>
</div>

<!-- Toolbar: Search, Filters & Expand Controls -->
<div class="card mb-4" style="border-radius:14px;border:1.5px solid #e2e8f0;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.02);margin-bottom:20px;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
    
    <!-- Search Bar -->
    <div style="flex:1;min-width:260px;position:relative;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" width="16" height="16" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="pelaksanaanSearchInput" onkeyup="filterPelaksanaan()" placeholder="Cari kode, nama kriteria, target, catatan realisasi..."
             style="width:100%;padding:9px 12px 9px 36px;border:1.5px solid #cbd5e1;border-radius:10px;font-size:13px;outline:none;font-family:inherit;background:#f8fafc;"
             onfocus="this.style.background='#fff';this.style.borderColor='#4f46e5';" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1';">
    </div>

    <!-- Expand / Collapse All -->
    <div style="display:flex;gap:8px;">
      <button type="button" onclick="expandAllPelaksanaan()" class="btn btn-outline btn-sm"
              style="font-size:12px;font-weight:700;color:#4f46e5;border-color:#c7d2fe;background:#eef2ff;padding:6px 12px;border-radius:8px;">
        ⤢ Buka Semua
      </button>
      <button type="button" onclick="collapseAllPelaksanaan()" class="btn btn-outline btn-sm"
              style="font-size:12px;font-weight:600;color:#64748b;padding:6px 12px;border-radius:8px;">
        ⤡ Tutup Semua
      </button>
    </div>
  </div>

  <!-- Filter Pills Bar -->
  <div style="margin-top:14px;padding-top:12px;border-top:1px solid #f1f5f9;display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between;">
    
    <!-- Kriteria Pills -->
    <?php if (count($kriteriaMap) > 1): ?>
    <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
      <span style="font-size:11.5px;font-weight:700;color:#64748b;">Kriteria:</span>
      <button type="button" class="pel-kriteria-filter-btn active" data-kid="all" onclick="filterPelaksanaanKriteria('all', this)"
              style="border:none;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#4f46e5;color:#fff;">
        Semua (<?= count($p['details']) ?>)
      </button>
      <?php foreach ($kriteriaMap as $k): ?>
      <button type="button" class="pel-kriteria-filter-btn" data-kid="<?= $k['id'] ?>" onclick="filterPelaksanaanKriteria('<?= $k['id'] ?>', this)"
              style="border:1.5px solid #cbd5e1;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:600;cursor:pointer;background:#fff;color:#475569;">
        <?= htmlspecialchars($k['kode']) ?> (<?= $k['count'] ?>)
      </button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Status Pills -->
    <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
      <span style="font-size:11.5px;font-weight:700;color:#64748b;">Status:</span>
      <button type="button" class="pel-status-filter-btn active" data-status="all" onclick="filterPelaksanaanStatus('all', this)"
              style="border:none;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#1e293b;color:#fff;">
        Semua Status
      </button>
      <button type="button" class="pel-status-filter-btn" data-status="terlaksana" onclick="filterPelaksanaanStatus('terlaksana', this)"
              style="border:1.5px solid #a7f3d0;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#ecfdf5;color:#065f46;">
        ✓ Selesai (<?= $cStats['terlaksana'] ?>)
      </button>
      <button type="button" class="pel-status-filter-btn" data-status="proses" onclick="filterPelaksanaanStatus('proses', this)"
              style="border:1.5px solid #fde68a;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#fffbeb;color:#92400e;">
        ⏳ Proses (<?= $cStats['proses'] ?>)
      </button>
      <button type="button" class="pel-status-filter-btn" data-status="belum" onclick="filterPelaksanaanStatus('belum', this)"
              style="border:1.5px solid #e2e8f0;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#f8fafc;color:#64748b;">
        ✕ Belum (<?= $cStats['belum'] ?>)
      </button>
    </div>

  </div>
</div>

<!-- Accordion per Indikator / Standar Target -->
<div class="kriteria-accordion" id="pelaksanaanAccordionList">
<?php foreach ($p['details'] as $idx => $det): ?>
<?php
$pdId   = (int)$det['id'];
$kid    = (int)$det['kriteria_id'];
$status = $det['status_pelaksanaan'] ?? 'belum';
$displayKode = !empty($det['kode']) ? $det['kode'] : (!empty($det['kriteria_kode']) ? $det['kriteria_kode'] : 'STD-' . ($idx + 1));
$isFirst = ($idx === 0);
?>

<div class="ka-item" id="det_<?= $pdId ?>" data-kid="<?= $kid ?>" data-status="<?= $status ?>" style="margin-bottom:14px;border:1.5px solid #e2e8f0;border-radius:12px;overflow:hidden;background:#fff;transition:box-shadow 0.2s;">
  <div class="ka-header" onclick="toggleKriteria(<?= $pdId ?>)" style="cursor:pointer;">
    <div class="ka-code"><?= htmlspecialchars($displayKode) ?></div>
    <div class="ka-title">
      <div style="display:flex;align-items:center;gap:8px;">
        <h4 style="margin:0;"><?= htmlspecialchars($det['kriteria_nama']) ?></h4>
        <span style="font-size:11px;font-weight:700;color:#4f46e5;background:#eef2ff;padding:2px 8px;border-radius:6px;">
          Standar #<?= $idx + 1 ?>
        </span>
      </div>
      <p><?= htmlspecialchars($det['kriteria_deskripsi'] ?? '') ?></p>
    </div>
    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
      <!-- Status Pelaksanaan Badge -->
      <span id="badgeStatus_<?= $pdId ?>"
            style="font-size:11px;font-weight:700;border-radius:20px;padding:3px 10px;
                   <?= $status === 'terlaksana' ? 'background:#ecfdf5;color:#059669;' : ($status === 'proses' ? 'background:#fffbeb;color:#d97706;' : 'background:#f1f5f9;color:#64748b;') ?>">
        <?= $status === 'terlaksana' ? '✓ Sudah Dilaksanakan' : ($status === 'proses' ? '⏳ Dalam Proses' : '✕ Belum Dilaksanakan') ?>
      </span>

      <?php $nbukti = count($det['bukti']); ?>
      <span id="badgeBukti_<?= $pdId ?>" style="font-size:11px;font-weight:700;color:<?= $nbukti > 0 ? '#0284c7' : '#94a3b8' ?>;background:<?= $nbukti > 0 ? '#e0f2fe' : '#f1f5f9' ?>;border-radius:20px;padding:3px 10px;">
        <?= $nbukti ?> bukti
      </span>
      <svg id="toggle_<?= $pdId ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="transition:transform 0.18s;transform:<?= $isFirst ? 'rotate(180deg)' : 'rotate(0deg)' ?>;">
        <polyline points="6 9 12 15 18 9"/>
      </svg>
    </div>
  </div>

  <div class="ka-body <?= $isFirst ? 'show' : '' ?>" id="body_<?= $pdId ?>" style="display:<?= $isFirst ? 'block' : 'none' ?>;padding:20px;background:#fff;border-top:1px solid #e2e8f0;">

    <!-- 1. ACUAN TARGET & INDIKATOR PENETAPAN (DITAMPILKAN DI AWAL) -->
    <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:12px;padding:16px 18px;margin-bottom:18px;">
      <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:#64748b;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
        Target Acuan Penetapan Standar
      </div>

      <!-- Pernyataan Standar Target -->
      <?php if (!empty($det['target_capaian'])): ?>
      <div style="margin-bottom:10px;">
        <div style="font-size:11px;font-weight:800;text-transform:uppercase;color:#059669;margin-bottom:2px;">🎯 Pernyataan Standar (Target Capaian):</div>
        <div style="font-size:14.5px;color:#0f172a;font-weight:700;line-height:1.6;"><?= nl2br(htmlspecialchars($det['target_capaian'])) ?></div>
      </div>
      <?php endif; ?>

      <!-- Indikator Target -->
      <?php if (!empty($det['indikator'])): ?>
      <div style="margin-bottom:10px;background:#fff;border-radius:8px;padding:10px 12px;border:1px solid #e2e8f0;">
        <div style="font-size:11px;font-weight:800;text-transform:uppercase;color:#d97706;margin-bottom:4px;">📊 Indikator Ketercapaian Mutu:</div>
        <div style="font-size:13.5px;color:#334155;line-height:1.6;"><?= nl2br(htmlspecialchars($det['indikator'])) ?></div>
      </div>
      <?php endif; ?>

      <!-- Dasar Hukum / Aturan -->
      <?php if (!empty($det['strategi'])): ?>
      <div style="font-size:12px;color:#475569;display:flex;align-items:flex-start;gap:6px;">
        <span style="font-weight:800;color:#4f46e5;">📌 Dasar Regulasi:</span>
        <span><?= htmlspecialchars($det['strategi']) ?></span>
      </div>
      <?php endif; ?>
    </div>

    <!-- 2. STATUS & REALISASI PELAKSANAAN -->
    <div style="background:#fff;border:1.5px solid #cbd5e1;border-radius:12px;padding:16px;margin-bottom:18px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px;">
        <div style="font-size:13px;font-weight:800;color:#1e293b;display:flex;align-items:center;gap:6px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" width="16" height="16"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
          Status Keterlaksanaan &amp; Realisasi (<?= htmlspecialchars($displayKode) ?>)
        </div>
        <span id="saveStatusIndicator_<?= $pdId ?>" style="font-size:11px;color:#94a3b8;"></span>
      </div>

      <!-- Pill Selector Status Pelaksanaan -->
      <div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;">
        <label style="flex:1;min-width:140px;cursor:pointer;">
          <input type="radio" name="status_pelaksanaan_<?= $pdId ?>" value="terlaksana" <?= $status === 'terlaksana' ? 'checked' : '' ?>
                 onchange="updateChecklistStatus(<?= $pdId ?>, <?= $kid ?>, <?= $p['id'] ?>, 'terlaksana')" style="display:none;" id="radio_terlaksana_<?= $pdId ?>">
          <div id="pill_terlaksana_<?= $pdId ?>"
               style="padding:10px 14px;border-radius:9px;border:1.5px solid <?= $status === 'terlaksana' ? '#10b981' : '#e2e8f0' ?>;background:<?= $status === 'terlaksana' ? '#ecfdf5' : '#f8fafc' ?>;color:<?= $status === 'terlaksana' ? '#065f46' : '#64748b' ?>;font-weight:700;font-size:12.5px;display:flex;align-items:center;gap:8px;transition:all 0.2s;">
            <span style="width:16px;height:16px;border-radius:50%;border:2px solid <?= $status === 'terlaksana' ? '#059669' : '#cbd5e1' ?>;display:flex;align-items:center;justify-content:center;background:<?= $status === 'terlaksana' ? '#059669' : '#fff' ?>;color:#fff;font-size:10px;font-weight:900;">✓</span>
            Sudah Dilaksanakan
          </div>
        </label>

        <label style="flex:1;min-width:140px;cursor:pointer;">
          <input type="radio" name="status_pelaksanaan_<?= $pdId ?>" value="proses" <?= $status === 'proses' ? 'checked' : '' ?>
                 onchange="updateChecklistStatus(<?= $pdId ?>, <?= $kid ?>, <?= $p['id'] ?>, 'proses')" style="display:none;" id="radio_proses_<?= $pdId ?>">
          <div id="pill_proses_<?= $pdId ?>"
               style="padding:10px 14px;border-radius:9px;border:1.5px solid <?= $status === 'proses' ? '#f59e0b' : '#e2e8f0' ?>;background:<?= $status === 'proses' ? '#fffbeb' : '#f8fafc' ?>;color:<?= $status === 'proses' ? '#92400e' : '#64748b' ?>;font-weight:700;font-size:12.5px;display:flex;align-items:center;gap:8px;transition:all 0.2s;">
            <span style="width:16px;height:16px;border-radius:50%;border:2px solid <?= $status === 'proses' ? '#d97706' : '#cbd5e1' ?>;display:flex;align-items:center;justify-content:center;background:<?= $status === 'proses' ? '#d97706' : '#fff' ?>;color:#fff;font-size:9px;font-weight:900;">⏳</span>
            Dalam Proses
          </div>
        </label>

        <label style="flex:1;min-width:140px;cursor:pointer;">
          <input type="radio" name="status_pelaksanaan_<?= $pdId ?>" value="belum" <?= $status === 'belum' ? 'checked' : '' ?>
                 onchange="updateChecklistStatus(<?= $pdId ?>, <?= $kid ?>, <?= $p['id'] ?>, 'belum')" style="display:none;" id="radio_belum_<?= $pdId ?>">
          <div id="pill_belum_<?= $pdId ?>"
               style="padding:10px 14px;border-radius:9px;border:1.5px solid <?= $status === 'belum' ? '#94a3b8' : '#e2e8f0' ?>;background:<?= $status === 'belum' ? '#f1f5f9' : '#f8fafc' ?>;color:<?= $status === 'belum' ? '#475569' : '#64748b' ?>;font-weight:700;font-size:12.5px;display:flex;align-items:center;gap:8px;transition:all 0.2s;">
            <span style="width:16px;height:16px;border-radius:50%;border:2px solid <?= $status === 'belum' ? '#64748b' : '#cbd5e1' ?>;display:flex;align-items:center;justify-content:center;background:<?= $status === 'belum' ? '#64748b' : '#fff' ?>;color:#fff;font-size:9px;font-weight:900;">✕</span>
            Belum Dilaksanakan
          </div>
        </label>
      </div>

      <!-- Realisasi & Catatan Pelaksanaan (Termasuk Capaian Angka Kuantitatif) -->
      <div style="display:grid;grid-template-columns:230px 1fr;gap:12px;margin-top:10px;">
        <div>
          <label style="font-size:12px;font-weight:800;color:#065f46;display:block;margin-bottom:4px;">
            📊 Realisasi Angka (Kuantitatif):
          </label>
          <input type="text" id="capaian_angka_<?= $pdId ?>" class="form-control"
                 placeholder="Contoh: 85 atau 4.0 atau 100%"
                 value="<?= htmlspecialchars($det['capaian_angka'] ?? '') ?>"
                 onchange="saveCatatanPelaksanaan(<?= $pdId ?>, <?= $kid ?>, <?= $p['id'] ?>)"
                 style="font-weight:800;color:#065f46;border:1.5px solid #a7f3d0;background:#f0fdf4;font-size:13px;padding:8px 12px;">
          <span style="font-size:10.5px;color:#64748b;margin-top:3px;display:block;">
            💡 Angka capaian kuantitatif untuk Evaluasi.
          </span>
        </div>
        <div>
          <label style="font-size:12px;font-weight:700;color:#475569;display:block;margin-bottom:4px;">Realisasi / Catatan Pelaksanaan</label>
          <textarea id="catatan_<?= $pdId ?>" class="form-control" rows="2"
                    placeholder="Tuliskan catatan realisasi pelaksanaan untuk indikator ini..."
                    onchange="saveCatatanPelaksanaan(<?= $pdId ?>, <?= $kid ?>, <?= $p['id'] ?>)"><?= htmlspecialchars($det['catatan_pelaksanaan'] ?? '') ?></textarea>
        </div>
      </div>
    </div>

    <!-- List Bukti -->
    <div style="margin-bottom:18px;">
      <div style="font-size:12.5px;font-weight:700;color:#64748b;margin-bottom:10px;display:flex;align-items:center;justify-content:space-between;">
        <span>Bukti Pelaksanaan (<?= count($det['bukti']) ?>)</span>
      </div>

      <div id="buktiList_<?= $pdId ?>">
        <?php if (empty($det['bukti'])): ?>
        <div id="emptyBukti_<?= $pdId ?>" style="text-align:center;padding:22px;background:#f8faff;border:1.5px dashed #e2e8f0;border-radius:10px;">
          <div style="font-size:13px;color:#94a3b8;">Belum ada bukti untuk kriteria ini</div>
        </div>
        <?php else: ?>
        <?php $emptyBukti = false; ?>
        <?php foreach ($det['bukti'] as $b): ?>
        <div class="bukti-item" id="bukti_<?= $b['id'] ?>" style="display:flex;align-items:flex-start;gap:12px;padding:13px 16px;background:#fff;border:1.5px solid #e2e8f0;border-radius:10px;margin-bottom:8px;transition:all 0.18s;">
          <div style="width:36px;height:36px;background:linear-gradient(135deg,#1d4ed8,#3b82f6);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
          </div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:13.5px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($b['judul_bukti']) ?></div>
            <a href="<?= htmlspecialchars($b['url_link']) ?>" target="_blank" rel="noopener"
               style="font-size:12.5px;color:#3f51b5;display:block;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:380px;">
              <?= htmlspecialchars($b['url_link']) ?> ↗
            </a>
            <?php if ($b['keterangan']): ?>
            <div style="font-size:12px;color:#64748b;margin-top:4px;"><?= htmlspecialchars($b['keterangan']) ?></div>
            <?php endif; ?>
            <div style="font-size:11px;color:#94a3b8;margin-top:5px;"><?= date('d M Y H:i', strtotime($b['created_at'])) ?></div>
          </div>
          <button type="button" onclick="deleteBukti(<?= $b['id'] ?>, <?= $p['id'] ?>, <?= $pdId ?>)"
                  style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:5px;border-radius:7px;display:flex;align-items:center;flex-shrink:0;"
                  onmouseover="this.style.color='#ef4444';this.style.background='#fef2f2';"
                  onmouseout="this.style.color='#94a3b8';this.style.background='none';"
                  title="Hapus bukti">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
          </button>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Form tambah bukti -->
      <div style="margin-top:12px;background:#f8faff;border:1.5px solid #e2e8f0;border-radius:10px;overflow:hidden;">
        <div style="padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#fafbff;display:flex;align-items:center;gap:8px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="#3f51b5" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
          <span style="font-size:12.5px;font-weight:700;color:#1e293b;">Tambah Bukti Link</span>
        </div>
        <div style="padding:14px 16px;">
          <div style="margin-bottom:10px;">
            <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:5px;">Judul Bukti <span style="color:#ef4444;">*</span></label>
            <input type="text" id="judulBukti_<?= $pdId ?>" placeholder="Contoh: Notulensi Rapat Prodi Feb 2025"
                   style="width:100%;padding:9px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;font-family:inherit;background:#fff;box-sizing:border-box;">
          </div>
          <div style="margin-bottom:10px;">
            <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:5px;">URL Link Bukti Fisik <span style="color:#ef4444;">*</span></label>
            <input type="url" id="urlLink_<?= $pdId ?>" placeholder="https://drive.google.com/... atau https://..."
                   style="width:100%;padding:9px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;font-family:inherit;background:#fff;box-sizing:border-box;">
          </div>
          <div style="margin-bottom:12px;">
            <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:5px;">Keterangan Singkat (Opsional)</label>
            <input type="text" id="ketBukti_<?= $pdId ?>" placeholder="Misal: Halaman 3-5, Surat Tugas No. 12"
                   style="width:100%;padding:9px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;font-family:inherit;background:#fff;box-sizing:border-box;">
          </div>
          <button type="button" onclick="addBukti(<?= $pdId ?>, <?= $kid ?>, <?= $p['id'] ?>)"
                  id="btnAddBukti_<?= $pdId ?>"
                  style="background:linear-gradient(135deg,#1a237e,#3f51b5);color:#fff;border:none;border-radius:9px;padding:10px 18px;font-size:13px;font-weight:600;font-family:inherit;cursor:pointer;display:flex;align-items:center;gap:7px;transition:all 0.18s;"
                  onmouseover="this.style.filter='brightness(1.1)'" onmouseout="this.style.filter='none'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            Tambah Bukti
          </button>
          <div id="buktiError_<?= $pdId ?>" style="display:none;margin-top:8px;font-size:12.5px;color:#991b1b;background:#fef2f2;border-radius:7px;padding:8px 12px;"></div>
        </div>
      </div>
    </div>

  </div><!-- end ka-body -->
</div><!-- end ka-item -->

<?php endforeach; ?>
</div><!-- end accordion -->

<script>
var BASE_URL_APP = '<?= BASE_URL ?>';
var currentPelKid = 'all';
var currentPelStatus = 'all';

function filterPelaksanaan() {
  var query = (document.getElementById('pelaksanaanSearchInput')?.value || '').toLowerCase().trim();
  var items = document.querySelectorAll('#pelaksanaanAccordionList .ka-item');
  var visibleCount = 0;

  items.forEach(function(item) {
    var kid = item.getAttribute('data-kid');
    var status = item.getAttribute('data-status');
    var text = item.innerText.toLowerCase();

    var matchKid = (currentPelKid === 'all' || kid === currentPelKid);
    var matchStatus = (currentPelStatus === 'all' || status === currentPelStatus);
    var matchQuery = (!query || text.indexOf(query) !== -1);

    if (matchKid && matchStatus && matchQuery) {
      item.style.display = '';
      visibleCount++;
    } else {
      item.style.display = 'none';
    }
  });
}

function filterPelaksanaanKriteria(kid, btnEl) {
  currentPelKid = String(kid);
  document.querySelectorAll('.pel-kriteria-filter-btn').forEach(function(btn) {
    btn.style.background = '#fff';
    btn.style.color = '#475569';
    btn.style.borderColor = '#cbd5e1';
    btn.classList.remove('active');
  });
  if (btnEl) {
    btnEl.style.background = '#4f46e5';
    btnEl.style.color = '#fff';
    btnEl.style.borderColor = '#4f46e5';
    btnEl.classList.add('active');
  }
  filterPelaksanaan();
}

function filterPelaksanaanStatus(status, btnEl) {
  currentPelStatus = status;
  document.querySelectorAll('.pel-status-filter-btn').forEach(function(btn) {
    btn.style.background = '#f8fafc';
    btn.style.color = '#64748b';
    btn.style.borderColor = '#e2e8f0';
    btn.classList.remove('active');
  });
  if (btnEl) {
    if (status === 'terlaksana') {
      btnEl.style.background = '#ecfdf5';
      btnEl.style.color = '#065f46';
      btnEl.style.borderColor = '#a7f3d0';
    } else if (status === 'proses') {
      btnEl.style.background = '#fffbeb';
      btnEl.style.color = '#92400e';
      btnEl.style.borderColor = '#fde68a';
    } else if (status === 'belum') {
      btnEl.style.background = '#f1f5f9';
      btnEl.style.color = '#475569';
      btnEl.style.borderColor = '#cbd5e1';
    } else {
      btnEl.style.background = '#1e293b';
      btnEl.style.color = '#fff';
      btnEl.style.borderColor = '#1e293b';
    }
    btnEl.classList.add('active');
  }
  filterPelaksanaan();
}

function expandAllPelaksanaan() {
  document.querySelectorAll('#pelaksanaanAccordionList .ka-body').forEach(function(b) {
    b.style.display = 'block';
    b.classList.add('show');
  });
  document.querySelectorAll('#pelaksanaanAccordionList svg[id^="toggle_"]').forEach(function(t) {
    t.style.transform = 'rotate(180deg)';
  });
}

function collapseAllPelaksanaan() {
  document.querySelectorAll('#pelaksanaanAccordionList .ka-body').forEach(function(b) {
    b.style.display = 'none';
    b.classList.remove('show');
  });
  document.querySelectorAll('#pelaksanaanAccordionList svg[id^="toggle_"]').forEach(function(t) {
    t.style.transform = 'rotate(0deg)';
  });
}

function toggleKriteria(pdId) {
  var body   = document.getElementById('body_' + pdId);
  var toggle = document.getElementById('toggle_' + pdId);
  if (!body) return;
  
  var isClosed = (body.style.display === 'none' || body.style.display === '' || !body.classList.contains('show'));
  if (isClosed) {
    body.style.display = 'block';
    body.classList.add('show');
    if (toggle) toggle.style.transform = 'rotate(180deg)';
  } else {
    body.style.display = 'none';
    body.classList.remove('show');
    if (toggle) toggle.style.transform = 'rotate(0deg)';
  }
}

function addBukti(pdId, kriteriaId, pelaksanaanId) {
  var judulEl = document.getElementById('judulBukti_' + pdId);
  var urlEl   = document.getElementById('urlLink_' + pdId);
  var ketEl   = document.getElementById('ketBukti_' + pdId);
  var errEl   = document.getElementById('buktiError_' + pdId);
  var btn     = document.getElementById('btnAddBukti_' + pdId);

  var judul   = judulEl ? judulEl.value.trim() : '';
  var url     = urlEl ? urlEl.value.trim() : '';
  var ket     = ketEl ? ketEl.value.trim() : '';

  if (errEl) errEl.style.display = 'none';

  if (!judul || !url) {
    if (errEl) {
      errEl.textContent = 'Judul dan URL link wajib diisi.';
      errEl.style.display = 'block';
    }
    return;
  }

  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<div style="width:13px;height:13px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite;"></div> Menyimpan...';
  }

  var body = new URLSearchParams();
  body.append('pelaksanaan_id', pelaksanaanId);
  body.append('kriteria_id', kriteriaId);
  body.append('penetapan_detail_id', pdId);
  body.append('judul_bukti', judul);
  body.append('url_link', url);
  body.append('keterangan', ket);

  fetch(BASE_URL_APP + '/pelaksanaan/bukti/add', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg> Tambah Bukti';
    }

    if (data.success) {
      // Hapus empty state jika ada
      var emptyEl = document.getElementById('emptyBukti_' + pdId);
      if (emptyEl) emptyEl.remove();

      // Tambah item baru ke list
      var listEl = document.getElementById('buktiList_' + pdId);
      if (listEl) {
        var newItem = document.createElement('div');
        newItem.className = 'bukti-item';
        newItem.id = 'bukti_' + data.bukti_id;
        newItem.style.cssText = 'display:flex;align-items:flex-start;gap:12px;padding:13px 16px;background:#fff;border:1.5px solid #e2e8f0;border-radius:10px;margin-bottom:8px;animation:fadeIn 0.3s ease;';
        newItem.innerHTML =
          '<div style="width:36px;height:36px;background:linear-gradient(135deg,#1d4ed8,#3b82f6);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>' +
          '</div>' +
          '<div style="flex:1;min-width:0;">' +
            '<div style="font-size:13.5px;font-weight:700;color:#1e293b;">' + escHtml(data.judul) + '</div>' +
            '<a href="' + escHtml(data.url) + '" target="_blank" style="font-size:12.5px;color:#3f51b5;display:block;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:380px;">' + escHtml(data.url) + ' ↗</a>' +
            (data.keterangan ? '<div style="font-size:12px;color:#64748b;margin-top:4px;">' + escHtml(data.keterangan) + '</div>' : '') +
            '<div style="font-size:11px;color:#94a3b8;margin-top:5px;">' + data.created + '</div>' +
          '</div>' +
          '<button type="button" onclick="deleteBukti(' + data.bukti_id + ', ' + pelaksanaanId + ', ' + pdId + ')" ' +
            'style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:5px;border-radius:7px;display:flex;align-items:center;flex-shrink:0;" ' +
            'onmouseover="this.style.color=\'#ef4444\';this.style.background=\'#fef2f2\';" ' +
            'onmouseout="this.style.color=\'#94a3b8\';this.style.background=\'none\';">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>' +
          '</button>';
        listEl.appendChild(newItem);
      }

      // Reset form
      if (judulEl) judulEl.value = '';
      if (urlEl) urlEl.value = '';
      if (ketEl) ketEl.value = '';

      // Update badge count
      var badge = document.getElementById('badgeBukti_' + pdId);
      if (badge) {
        var n = document.querySelectorAll('#buktiList_' + pdId + ' .bukti-item').length;
        badge.textContent = n + ' bukti';
        badge.style.color = '#0284c7';
        badge.style.background = '#e0f2fe';
      }
    } else {
      if (errEl) {
        errEl.textContent = data.message || 'Gagal menyimpan.';
        errEl.style.display = 'block';
      }
    }
  })
  .catch(function(err) {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg> Tambah Bukti';
    }
    if (errEl) {
      errEl.textContent = 'Terjadi kesalahan sistem / koneksi.';
      errEl.style.display = 'block';
    }
  });
}

function deleteBukti(buktiId, pelaksanaanId, pdId) {
  if (!confirm('Hapus bukti ini?')) return;

  var body = new URLSearchParams();
  body.append('bukti_id', buktiId);
  body.append('pelaksanaan_id', pelaksanaanId);

  fetch(BASE_URL_APP + '/pelaksanaan/bukti/delete', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      var el = document.getElementById('bukti_' + buktiId);
      if (el) {
        el.style.opacity = '0';
        setTimeout(function() { 
          el.remove();
          if (pdId) {
            var badge = document.getElementById('badgeBukti_' + pdId);
            if (badge) {
              var n = document.querySelectorAll('#buktiList_' + pdId + ' .bukti-item').length;
              badge.textContent = n + ' bukti';
              if (n === 0) {
                badge.style.color = '#94a3b8';
                badge.style.background = '#f1f5f9';
              }
            }
          }
        }, 200);
      }
    }
  });
}

function escHtml(str) {
  var d = document.createElement('div');
  d.appendChild(document.createTextNode(str));
  return d.innerHTML;
}

// ==================================================
// Checklist Status & Catatan Pelaksanaan Handler
// ==================================================
function updateChecklistStatus(pdId, kid, pelaksanaanId, status) {
  var indicator = document.getElementById('saveStatusIndicator_' + pdId);
  if (indicator) { indicator.textContent = 'Menyimpan status...'; indicator.style.color = '#94a3b8'; }

  // Update pills visual state immediately
  ['terlaksana', 'proses', 'belum'].forEach(function(st) {
    var pill = document.getElementById('pill_' + st + '_' + pdId);
    var radio = document.getElementById('radio_' + st + '_' + pdId);
    if (pill) {
      if (st === status) {
        if (radio) radio.checked = true;
        if (st === 'terlaksana') {
          pill.style.borderColor = '#10b981'; pill.style.background = '#ecfdf5'; pill.style.color = '#065f46';
        } else if (st === 'proses') {
          pill.style.borderColor = '#f59e0b'; pill.style.background = '#fffbeb'; pill.style.color = '#92400e';
        } else {
          pill.style.borderColor = '#94a3b8'; pill.style.background = '#f1f5f9'; pill.style.color = '#475569';
        }
      } else {
        pill.style.borderColor = '#e2e8f0'; pill.style.background = '#f8fafc'; pill.style.color = '#64748b';
      }
    }
  });

  // Update header badge
  var badge = document.getElementById('badgeStatus_' + pdId);
  if (badge) {
    if (status === 'terlaksana') {
      badge.textContent = '✓ Sudah Dilaksanakan'; badge.style.background = '#ecfdf5'; badge.style.color = '#059669';
    } else if (status === 'proses') {
      badge.textContent = '⏳ Dalam Proses'; badge.style.background = '#fffbeb'; badge.style.color = '#d97706';
    } else {
      badge.textContent = '✕ Belum Dilaksanakan'; badge.style.background = '#f1f5f9'; badge.style.color = '#64748b';
    }
  }

  var detEl = document.getElementById('det_' + pdId);
  if (detEl) detEl.setAttribute('data-status', status);

  var capaianAngka = document.getElementById('capaian_angka_' + pdId)?.value || '';

  var body = new URLSearchParams();
  body.append('pelaksanaan_id', pelaksanaanId);
  body.append('penetapan_detail_id', pdId);
  body.append('kriteria_id', kid);
  body.append('status_pelaksanaan', status);
  body.append('capaian_angka', capaianAngka);

  fetch(BASE_URL_APP + '/pelaksanaan/save-status', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (indicator) {
      indicator.textContent = '✓ Tersimpan';
      indicator.style.color = '#059669';
      setTimeout(function() { indicator.textContent = ''; }, 2000);
    }
    if (data.success && data.stats) {
      updateTopChecklistSummary(data.stats);
    }
  })
  .catch(function() {
    if (indicator) { indicator.textContent = '✕ Gagal menyimpan'; indicator.style.color = '#ef4444'; }
  });
}

function saveCatatanPelaksanaan(pdId, kid, pelaksanaanId) {
  var catatan = document.getElementById('catatan_' + pdId)?.value || '';
  var capaianAngka = document.getElementById('capaian_angka_' + pdId)?.value || '';
  var indicator = document.getElementById('saveStatusIndicator_' + pdId);
  if (indicator) { indicator.textContent = 'Menyimpan...'; indicator.style.color = '#94a3b8'; }

  var status = 'belum';
  ['terlaksana', 'proses', 'belum'].forEach(function(st) {
    var r = document.getElementById('radio_' + st + '_' + pdId);
    if (r && r.checked) status = st;
  });

  var body = new URLSearchParams();
  body.append('pelaksanaan_id', pelaksanaanId);
  body.append('penetapan_detail_id', pdId);
  body.append('kriteria_id', kid);
  body.append('status_pelaksanaan', status);
  body.append('catatan_pelaksanaan', catatan);
  body.append('capaian_angka', capaianAngka);

  fetch(BASE_URL_APP + '/pelaksanaan/save-status', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (indicator) {
      indicator.textContent = '✓ Catatan tersimpan';
      indicator.style.color = '#059669';
      setTimeout(function() { indicator.textContent = ''; }, 2000);
    }
  });
}

function updateTopChecklistSummary(stats) {
  var pctEl = document.getElementById('summaryPctText');
  var barEl = document.getElementById('summaryProgressBar');
  var terlaksanaEl = document.getElementById('summaryTerlaksanaCount');
  var prosesEl = document.getElementById('summaryProsesCount');
  var belumEl = document.getElementById('summaryBelumCount');

  if (pctEl) pctEl.textContent = stats.pct + '%';
  if (barEl) barEl.style.width = stats.pct + '%';
  if (terlaksanaEl) terlaksanaEl.textContent = '✓ ' + stats.terlaksana + ' Selesai';
  if (prosesEl) prosesEl.textContent = '⏳ ' + stats.proses + ' Proses';
  if (belumEl) belumEl.textContent = '✕ ' + stats.belum + ' Belum';
}

// Open first accordion
<?php if (!empty($p['details'])): ?>
document.getElementById('body_<?= $p['details'][0]['kriteria_id'] ?>').classList.add('show');
document.getElementById('toggle_<?= $p['details'][0]['kriteria_id'] ?>').style.transform = 'rotate(180deg)';
<?php endif; ?>

// CSS
var style = document.createElement('style');
style.textContent = '@keyframes spin{to{transform:rotate(360deg)}} @keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}';
document.head.appendChild(style);
</script>
