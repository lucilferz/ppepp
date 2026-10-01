<?php
$pageTitle   = 'Hasil Evaluasi — ' . htmlspecialchars($evaluasi['judul']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Evaluasi', 'url' => BASE_URL . '/evaluasi'],
  ['label' => htmlspecialchars($evaluasi['judul'])],
];

$penetapan   = $evaluasi['penetapan'] ?? [];
$pelaksanaan = $evaluasi['pelaksanaan'] ?? [];
$details     = $evaluasi['details'] ?? [];
$evId        = $evaluasi['id'];
$absensiList = $evaluasi['absensi_list'] ?? [];
$gambarList  = $evaluasi['gambar_list'] ?? [];

// Hitung statistik capaian
$totalKriteria = count($details);
$cntTercapai   = 0;
$cntSebagian   = 0;
$cntBelum      = 0;

$kriteriaMap = [];
foreach ($details as $idx => $d) {
    $st = $d['status_capaian'] ?? 'belum_tercapai';
    if ($st === 'tercapai') $cntTercapai++;
    elseif ($st === 'sebagian') $cntSebagian++;
    else $cntBelum++;

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
$pctTercapai = $totalKriteria > 0 ? round(($cntTercapai / $totalKriteria) * 100) : 0;
?>

<!-- Header Banner -->
<div style="background:linear-gradient(135deg,#0f172a,#1e1b4b 60%,#312e81);border-radius:16px;padding:24px 28px;margin-bottom:22px;color:#fff;box-shadow:0 8px 24px rgba(15,23,42,0.12);">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
    <div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;flex-wrap:wrap;">
        <span style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#a5b4fc;">
          Tahap Evaluasi SPMI (PPEPP)
        </span>
        <span style="background:<?= $evaluasi['status'] === 'final' ? 'rgba(16,185,129,0.2)' : 'rgba(245,158,11,0.2)' ?>;border:1px solid <?= $evaluasi['status'] === 'final' ? 'rgba(16,185,129,0.4)' : 'rgba(245,158,11,0.4)' ?>;border-radius:20px;padding:3px 12px;font-size:12px;color:<?= $evaluasi['status'] === 'final' ? '#34d399' : '#fbbf24' ?>;font-weight:700;">
          <?= $evaluasi['status'] === 'final' ? '✓ Final' : '⏳ Draft' ?>
        </span>
      </div>
      <h2 style="font-size:22px;font-weight:900;color:#fff;margin:0 0 10px;line-height:1.3;letter-spacing:-0.4px;">
        <?= htmlspecialchars($evaluasi['judul']) ?>
      </h2>
      <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;font-size:12.5px;color:rgba(255,255,255,0.75);">
        <span style="background:rgba(255,255,255,0.1);padding:4px 10px;border-radius:7px;">Acuan: <strong><?= htmlspecialchars($penetapan['judul'] ?? '—') ?></strong></span>
        <span style="background:rgba(255,255,255,0.1);padding:4px 10px;border-radius:7px;">TA: <strong><?= htmlspecialchars($penetapan['ta_nama'] ?? '—') ?></strong></span>
        <span style="background:rgba(255,255,255,0.1);padding:4px 10px;border-radius:7px;">Jenis: <strong><?= htmlspecialchars(ucwords($evaluasi['jenis'] ?? 'Internal')) ?></strong></span>
        <span style="background:rgba(255,255,255,0.1);padding:4px 10px;border-radius:7px;">Dibuat: <strong><?= date('d M Y', strtotime($evaluasi['created_at'])) ?></strong></span>
      </div>
      <?php if (!empty($evaluasi['deskripsi'])): ?>
      <p style="font-size:13px;color:rgba(255,255,255,0.8);margin-top:8px;font-style:italic;max-width:800px;"><?= htmlspecialchars($evaluasi['deskripsi']) ?></p>
      <?php endif; ?>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <a href="<?= BASE_URL ?>/evaluasi/<?= $evId ?>/edit" class="btn btn-primary" style="font-weight:700;font-size:13px;background:#4f46e5;border:none;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
          <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Edit Evaluasi &amp; RTM
      </a>
      <?php if ($evaluasi['status'] === 'draft'): ?>
      <form action="<?= BASE_URL ?>/evaluasi/<?= $evId ?>/finalize" method="POST" style="display:inline;" onsubmit="return confirm('Finalisasi dokumen evaluasi ini?')">
        <button type="submit" class="btn btn-success" style="font-weight:700;font-size:13px;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
            <path d="M9 11l3 3L22 4"/>
            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
          </svg>
          Finalisasi
        </button>
      </form>
      <?php endif; ?>
      <a href="<?= BASE_URL ?>/ppepp<?= !empty($evaluasi['ppepp_project_id']) ? '/' . $evaluasi['ppepp_project_id'] : '' ?>" class="btn btn-outline" style="font-size:13px;color:rgba(255,255,255,0.8);border-color:rgba(255,255,255,0.3);">
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
      <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Indikator</div>
      <div style="font-size:24px;font-weight:900;color:#1e293b;line-height:1.2;margin-top:2px;"><?= $totalKriteria ?> <span style="font-size:13px;font-weight:600;color:#64748b;">Standar</span></div>
    </div>
  </div>

  <div style="background:#fff;border:1.5px solid #a7f3d0;border-radius:14px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
    <div style="width:44px;height:44px;background:#ecfdf5;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" width="22" height="22"><circle cx="12" cy="12" r="10"/><polyline points="16 12 12 8 8 12"/><line x1="12" y1="16" x2="12" y2="8"/></svg>
    </div>
    <div>
      <div style="font-size:11.5px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.5px;">✓ Tercapai Penuh</div>
      <div style="font-size:24px;font-weight:900;color:#065f46;line-height:1.2;margin-top:2px;"><?= $cntTercapai ?> <span style="font-size:13px;font-weight:600;color:#059669;">(<?= $pctTercapai ?>%)</span></div>
    </div>
  </div>

  <div style="background:#fff;border:1.5px solid #fde68a;border-radius:14px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
    <div style="width:44px;height:44px;background:#fffbeb;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" width="22" height="22"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <div>
      <div style="font-size:11.5px;font-weight:700;color:#d97706;text-transform:uppercase;letter-spacing:0.5px;">⏳ Tercapai Sebagian</div>
      <div style="font-size:24px;font-weight:900;color:#92400e;line-height:1.2;margin-top:2px;"><?= $cntSebagian ?> <span style="font-size:13px;font-weight:600;color:#d97706;">Standar</span></div>
    </div>
  </div>

  <div style="background:#fff;border:1.5px solid #cbd5e1;border-radius:14px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
    <div style="width:44px;height:44px;background:#f8fafc;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" width="22" height="22"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    </div>
    <div>
      <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">✕ Belum Tercapai</div>
      <div style="font-size:24px;font-weight:900;color:#475569;line-height:1.2;margin-top:2px;"><?= $cntBelum ?> <span style="font-size:13px;font-weight:600;color:#64748b;">Standar</span></div>
    </div>
  </div>
</div>

<!-- Toolbar: Search & Interactive Filters -->
<div class="card mb-4" style="border-radius:14px;border:1.5px solid #e2e8f0;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.02);margin-bottom:20px;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
    
    <!-- Search Bar -->
    <div style="flex:1;min-width:280px;position:relative;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" width="16" height="16" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="evaluasiSearchInput" onkeyup="filterEvaluasi()" placeholder="Cari kode, nama standar, target, narasi evaluasi..."
             style="width:100%;padding:9px 12px 9px 36px;border:1.5px solid #cbd5e1;border-radius:10px;font-size:13px;outline:none;font-family:inherit;background:#f8fafc;"
             onfocus="this.style.background='#fff';this.style.borderColor='#4f46e5';" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1';">
    </div>

    <!-- Info Counter -->
    <div id="evaluasiResultCount" style="font-size:12.5px;font-weight:700;color:#64748b;">
      Menampilkan <?= $totalKriteria ?> standar
    </div>
  </div>

  <!-- Filter Pills Bar -->
  <div style="margin-top:14px;padding-top:12px;border-top:1px solid #f1f5f9;display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between;">
    
    <!-- Kriteria Pills -->
    <?php if (count($kriteriaMap) > 1): ?>
    <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
      <span style="font-size:11.5px;font-weight:700;color:#64748b;">Kriteria:</span>
      <button type="button" class="ev-kriteria-filter-btn active" data-kid="all" onclick="filterEvaluasiKriteria('all', this)"
              style="border:none;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#4f46e5;color:#fff;">
        Semua (<?= $totalKriteria ?>)
      </button>
      <?php foreach ($kriteriaMap as $k): ?>
      <button type="button" class="ev-kriteria-filter-btn" data-kid="<?= $k['id'] ?>" onclick="filterEvaluasiKriteria('<?= $k['id'] ?>', this)"
              style="border:1.5px solid #cbd5e1;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:600;cursor:pointer;background:#fff;color:#475569;">
        <?= htmlspecialchars($k['kode']) ?> (<?= $k['count'] ?>)
      </button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Status Capaian Pills -->
    <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
      <span style="font-size:11.5px;font-weight:700;color:#64748b;">Status Capaian:</span>
      <button type="button" class="ev-status-filter-btn active" data-status="all" onclick="filterEvaluasiStatus('all', this)"
              style="border:none;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#1e293b;color:#fff;">
        Semua
      </button>
      <button type="button" class="ev-status-filter-btn" data-status="tercapai" onclick="filterEvaluasiStatus('tercapai', this)"
              style="border:1.5px solid #a7f3d0;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#ecfdf5;color:#065f46;">
        ✓ Tercapai Penuh (<?= $cntTercapai ?>)
      </button>
      <button type="button" class="ev-status-filter-btn" data-status="sebagian" onclick="filterEvaluasiStatus('sebagian', this)"
              style="border:1.5px solid #fde68a;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#fffbeb;color:#92400e;">
        ⏳ Sebagian (<?= $cntSebagian ?>)
      </button>
      <button type="button" class="ev-status-filter-btn" data-status="belum_tercapai" onclick="filterEvaluasiStatus('belum_tercapai', this)"
              style="border:1.5px solid #cbd5e1;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#f8fafc;color:#64748b;">
        ✕ Belum (<?= $cntBelum ?>)
      </button>
    </div>

  </div>
</div>

<!-- SECTION 1: RINCIAN HASIL EVALUASI -->
<div id="evaluasiListContainer" style="display:flex;flex-direction:column;gap:16px;margin-bottom:28px;">
  <?php foreach ($details as $idx => $d): ?>
  <?php
  $stPel       = $d['status_pelaksanaan'] ?? 'belum';
  $stCapaian   = $d['status_capaian'] ?? 'belum_tercapai';
  $displayKode = !empty($d['kode']) ? $d['kode'] : (!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD-' . ($idx + 1));
  $kId         = (int)($d['kriteria_id'] ?? 0);
  ?>
  <div class="ev-item-card" data-kid="<?= $kId ?>" data-status="<?= $stCapaian ?>"
       style="border:1.5px solid #e2e8f0;border-radius:14px;overflow:hidden;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,0.02);transition:box-shadow 0.2s;">
    
    <!-- Card Header -->
    <div style="background:#f8fafc;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;border-bottom:1.5px solid #e2e8f0;flex-wrap:wrap;">
      <div style="display:flex;align-items:center;gap:10px;">
        <div style="padding:4px 10px;background:#312e81;color:#fff;border-radius:7px;font-weight:900;font-size:12px;letter-spacing:0.3px;">
          <?= htmlspecialchars($displayKode) ?>
        </div>
        <div>
          <div style="font-weight:800;font-size:14.5px;color:#1e293b;"><?= htmlspecialchars($d['kriteria_nama']) ?></div>
          <?php if (!empty($d['kriteria_deskripsi'])): ?>
          <div style="font-size:11.5px;color:#64748b;margin-top:1px;"><?= htmlspecialchars($d['kriteria_deskripsi']) ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div style="display:flex;align-items:center;gap:8px;">
        <span style="font-size:11.5px;font-weight:800;padding:4px 12px;border-radius:20px;
                     <?= $stCapaian === 'tercapai' ? 'background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;' : ($stCapaian === 'sebagian' ? 'background:#fffbeb;color:#d97706;border:1px solid #fde68a;' : 'background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;') ?>">
          <?= $stCapaian === 'tercapai' ? '✓ Tercapai Penuh' : ($stCapaian === 'sebagian' ? '⏳ Tercapai Sebagian' : '✕ Belum Tercapai') ?>
        </span>
      </div>
    </div>

    <!-- Card Body -->
    <div style="padding:18px 20px;display:flex;flex-direction:column;gap:14px;">
      
      <!-- Target & Indikator Penetapan -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px;">
        <!-- Target Capaian -->
        <div style="background:#f0fdf4;border-left:4px solid #059669;padding:12px 16px;border-radius:0 10px 10px 0;">
          <div style="font-size:11px;font-weight:800;text-transform:uppercase;color:#065f46;margin-bottom:3px;letter-spacing:0.5px;">🎯 Pernyataan Standar (Target Capaian):</div>
          <div style="font-size:14px;color:#0f172a;line-height:1.6;font-weight:600;"><?= nl2br(htmlspecialchars($d['target_capaian'] ?? $d['target'] ?? '—')) ?></div>
        </div>

        <!-- Indikator Mutu -->
        <div style="background:#fffbeb;border-left:4px solid #d97706;padding:12px 16px;border-radius:0 10px 10px 0;">
          <div style="font-size:11px;font-weight:800;text-transform:uppercase;color:#92400e;margin-bottom:3px;letter-spacing:0.5px;">📊 Indikator Ketercapaian Mutu:</div>
          <div style="font-size:14px;color:#0f172a;line-height:1.6;font-weight:500;"><?= nl2br(htmlspecialchars($d['indikator'] ?? '—')) ?></div>
        </div>
      </div>

      <!-- Dasar Regulasi jika ada -->
      <?php if (!empty($d['strategi'])): ?>
      <div style="font-size:12px;color:#475569;display:flex;align-items:flex-start;gap:6px;background:#f8faff;padding:8px 12px;border-radius:8px;border:1px solid #e0e7ff;">
        <span style="font-weight:800;color:#4f46e5;">📌 Dasar Regulasi:</span>
        <span><?= htmlspecialchars($d['strategi']) ?></span>
      </div>
      <?php endif; ?>

      <!-- Realisasi Pelaksanaan & Komparasi Kuantitatif -->
      <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="font-size:12.5px;color:#334155;">
          <strong>Pelaksanaan:</strong>
          <span style="font-weight:700;color:<?= $stPel === 'terlaksana' ? '#059669' : ($stPel === 'proses' ? '#d97706' : '#64748b') ?>">
            <?= $stPel === 'terlaksana' ? '✓ Sudah Dilaksanakan' : ($stPel === 'proses' ? '⏳ Dalam Proses' : '✕ Belum Dilaksanakan') ?>
          </span>
          <?php if (!empty($d['catatan_pelaksanaan'])): ?>
          &nbsp;•&nbsp; <strong>Catatan:</strong> <?= htmlspecialchars($d['catatan_pelaksanaan']) ?>
          <?php endif; ?>
        </div>

        <?php
        $capPel = trim($d['capaian_angka_pelaksanaan'] ?? '');
        $capEv  = trim($d['capaian_angka_evaluasi'] ?? '');
        if ($capPel !== '' || $capEv !== ''):
            $badgeText  = '—';
            $badgeStyle = 'background:#e2e8f0;color:#475569;';
            if ($capPel !== '' && $capEv !== '') {
                $pNum = (float)preg_replace('/[^0-9.]/', '', $capPel);
                $eNum = (float)preg_replace('/[^0-9.]/', '', $capEv);
                if ($pNum > $eNum) {
                    $diff = round($pNum - $eNum, 2);
                    $badgeText  = '📈 Melebihi (+' . $diff . ')';
                    $badgeStyle = 'background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;';
                } elseif ($pNum == $eNum) {
                    $badgeText  = '🎯 Sesuai Target (100%)';
                    $badgeStyle = 'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;';
                } else {
                    $diff = round($eNum - $pNum, 2);
                    $badgeText  = '📉 Belum Mencapai (-' . $diff . ')';
                    $badgeStyle = 'background:#fef2f2;color:#b91c1c;border:1px solid #fca5a5;';
                }
            }
        ?>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;">
          <span style="font-weight:700;color:#64748b;">Angka:</span>
          <span style="font-weight:800;color:#059669;background:#fff;padding:2px 8px;border-radius:6px;border:1px solid #cbd5e1;">Pelaksanaan: <?= htmlspecialchars($capPel ?: '—') ?></span>
          <span style="font-weight:800;color:#1d4ed8;background:#fff;padding:2px 8px;border-radius:6px;border:1px solid #cbd5e1;">Evaluasi: <?= htmlspecialchars($capEv ?: '—') ?></span>
          <span style="font-weight:900;padding:3px 10px;border-radius:14px;<?= $badgeStyle ?>">
            <?= $badgeText ?>
          </span>
        </div>
        <?php endif; ?>
      </div>

      <!-- Narasi & Analisis Evaluator -->
      <div style="background:#fff;border:1.5px solid #c7d2fe;border-radius:10px;padding:14px 16px;">
        <div style="font-size:11px;font-weight:800;text-transform:uppercase;color:#3730a3;margin-bottom:6px;display:flex;align-items:center;gap:6px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" width="14" height="14"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          Analisis &amp; Narasi Evaluasi Mutu:
        </div>
        <div style="font-size:13.5px;color:#1e293b;line-height:1.65;white-space:pre-wrap;">
          <?= !empty($d['evaluasi_teks']) ? htmlspecialchars($d['evaluasi_teks']) : '<span style="color:#94a3b8;font-style:italic;">Belum ada narasi evaluasi</span>' ?>
        </div>
      </div>

    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- SECTION 2: DOKUMENTASI RAPAT TINJAUAN MANAJEMEN (RTM) -->
<div class="card" style="border-radius:16px;border:1.5px solid #c7d2fe;overflow:hidden;box-shadow:0 4px 16px rgba(79,70,229,0.06);margin-bottom:30px;">
  <?php
    $notulRapatId    = !empty($evaluasi['notulensi_rapat_id']) ? (int)$evaluasi['notulensi_rapat_id'] : 0;
    $notulViewUrl    = "http://notulensi.test:8080/rapat/detail/" . $notulRapatId;
    $notulPdfUrl     = "http://notulensi.test:8080/pdf/view/" . $notulRapatId;

    $rapatTopik      = $evaluasi['rapat_topik'] ?? '';
    $rapatTanggal    = $evaluasi['rapat_tanggal'] ?? '';
    $rapatTempat     = $evaluasi['rapat_tempat'] ?? '';
    $rapatJamMulai   = $evaluasi['rapat_jam_mulai'] ?? '';
    $rapatJamSelesai = $evaluasi['rapat_jam_selesai'] ?? '';
    $rapatJenis      = $evaluasi['rapat_jenis'] ?? '';
    $rapatKategori   = $evaluasi['rapat_kategori'] ?? '';
    $rapatTa         = $evaluasi['rapat_tahun_ajaran'] ?? '';
    $rapatSemester   = $evaluasi['rapat_semester'] ?? '';
    $rapatLampiran   = $evaluasi['rapat_lampiran_link'] ?? '';
    $rapatKriteria   = $evaluasi['rapat_kriteria'] ?? '';
    $hasRapatInfo    = $rapatTanggal || $rapatTempat || $rapatTopik || $rapatJamMulai || $rapatJenis || $rapatTa;
  ?>
  <div class="card-header" style="background:linear-gradient(135deg,#f0f4ff,#e0e7ff);padding:16px 22px;border-bottom:1.5px solid #c7d2fe;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <h3 class="card-title" style="font-size:15px;font-weight:800;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" width="18" height="18"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
      Dokumentasi Rapat Tinjauan Manajemen (RTM) &amp; Bukti Evaluasi
    </h3>
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
      <?php if ($notulRapatId > 0): ?>
      <span style="font-size:11px;font-weight:800;color:#065f46;background:#d1fae5;padding:4px 10px;border-radius:20px;border:1px solid #a7f3d0;display:inline-flex;align-items:center;gap:4px;">
        <span style="width:6px;height:6px;border-radius:50%;background:#10b981;display:inline-block;"></span>
        RTM Terintegrasi #<?= $notulRapatId ?>
      </span>
      <a href="<?= $notulViewUrl ?>" target="_blank" class="btn btn-sm" style="font-size:11.5px;font-weight:700;background:#4f46e5;color:#fff;border-radius:8px;padding:4px 12px;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6"/><polyline points="15 8 12 11 9 8"/><line x1="12" y1="11" x2="12" y2="3"/></svg>
        Buka Notulensi Web ↗
      </a>
      <a href="<?= $notulPdfUrl ?>" target="_blank" class="btn btn-sm" style="font-size:11.5px;font-weight:700;background:#059669;color:#fff;border-radius:8px;padding:4px 12px;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        PDF Notulensi ↗
      </a>
      <?php endif; ?>
      <span style="font-size:12px;font-weight:700;color:#4f46e5;background:#fff;padding:4px 12px;border-radius:20px;border:1px solid #c7d2fe;">
        <?= count($absensiList) ?> Hadir &nbsp;•&nbsp; <?= count($gambarList) ?> Foto
      </span>
    </div>
  </div>

  <div class="card-body" style="padding:22px;display:flex;flex-direction:column;gap:20px;">

    <!-- Informasi Umum Rapat -->
    <?php if ($hasRapatInfo): ?>
    <div style="background:#f0f4ff;border:1.5px solid #c7d2fe;border-radius:12px;padding:16px 20px;">
      <div style="font-size:11.5px;font-weight:800;text-transform:uppercase;color:#3730a3;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
        <span>📌</span> Informasi Pelaksanaan Rapat (RTM)
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
        <?php if ($rapatTopik): ?>
        <div style="grid-column:1/-1;">
          <div style="font-size:11px;font-weight:700;color:#4f46e5;text-transform:uppercase;margin-bottom:2px;">Topik Rapat</div>
          <div style="font-size:14px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($rapatTopik) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($rapatTanggal): ?>
        <div>
          <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:2px;">📅 Tanggal Pelaksanaan</div>
          <div style="font-size:13.5px;font-weight:700;color:#1e293b;"><?= date('d F Y', strtotime($rapatTanggal)) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($rapatTempat): ?>
        <div>
          <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:2px;">📍 Lokasi / Tempat</div>
          <div style="font-size:13.5px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($rapatTempat) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($rapatJamMulai): ?>
        <div>
          <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:2px;">⏰ Waktu Pelaksanaan</div>
          <div style="font-size:13.5px;font-weight:700;color:#1e293b;"><?= substr($rapatJamMulai, 0, 5) ?><?= $rapatJamSelesai ? ' – ' . substr($rapatJamSelesai, 0, 5) : '' ?> WIB</div>
        </div>
        <?php endif; ?>
        <?php if ($rapatJenis): ?>
        <div>
          <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:2px;">🏷 Jenis &amp; Kategori</div>
          <div style="font-size:13.5px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($rapatJenis) ?><?= $rapatKategori ? ' (' . htmlspecialchars($rapatKategori) . ')' : '' ?></div>
        </div>
        <?php endif; ?>
        <?php if ($rapatTa): ?>
        <div>
          <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:2px;">📚 Tahun Ajaran / Semester</div>
          <div style="font-size:13.5px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($rapatTa) ?><?= $rapatSemester ? ' — Semester ' . htmlspecialchars($rapatSemester) : '' ?></div>
        </div>
        <?php endif; ?>
      </div>
      <?php if ($rapatLampiran): ?>
      <div style="margin-top:12px;padding-top:12px;border-top:1px solid #c7d2fe;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
        <span style="font-size:11.5px;font-weight:700;color:#4f46e5;">🔗 Tautan / Lampiran:</span>
        <a href="<?= htmlspecialchars($rapatLampiran) ?>" target="_blank" style="font-size:12.5px;color:#2563eb;font-weight:600;word-break:break-all;">
          <?= htmlspecialchars($rapatLampiran) ?> ↗
        </a>
      </div>
      <?php endif; ?>
      <?php if ($rapatKriteria): ?>
      <div style="margin-top:12px;padding-top:12px;border-top:1px solid #c7d2fe;">
        <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:6px;">🎯 Kriteria SPMI yang Dievaluasi:</div>
        <div style="display:flex;flex-wrap:wrap;gap:6px;">
          <?php foreach (array_filter(array_map('trim', explode(',', $rapatKriteria))) as $kItem): ?>
          <span style="font-size:11.5px;font-weight:600;background:#eef2ff;color:#3730a3;padding:3px 10px;border-radius:6px;border:1px solid #c7d2fe;"><?= htmlspecialchars($kItem) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Absensi & Notulensi -->
    <div style="display:grid;grid-template-columns:1fr 1.2fr;gap:20px;align-items:start;">
      
      <!-- Kiri: Absensi Peserta Rapat -->
      <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px;">
        <div style="font-size:12px;font-weight:800;color:#475569;text-transform:uppercase;margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;gap:6px;">
          <span>📋 Daftar Hadir Peserta</span>
          <span style="font-size:11px;background:#e0e7ff;color:#3730a3;padding:2px 8px;border-radius:10px;font-weight:700;"><?= count($absensiList) ?> Orang</span>
        </div>
        <?php if (empty($absensiList)): ?>
        <div style="font-size:12.5px;color:#94a3b8;font-style:italic;">Belum ada data kehadiran rapat</div>
        <?php else: ?>
        <?php
          $hadirCount = 0; $izinCount = 0; $tidakCount = 0;
          foreach ($absensiList as $a) {
              $st = strtoupper($a['status'] ?? 'HADIR');
              if ($st === 'HADIR') $hadirCount++;
              elseif ($st === 'IZIN') $izinCount++;
              else $tidakCount++;
          }
        ?>
        <div style="display:flex;flex-direction:column;gap:6px;max-height:360px;overflow-y:auto;padding-right:4px;">
          <?php foreach ($absensiList as $a): ?>
          <?php $st = strtoupper($a['status'] ?? 'HADIR'); ?>
          <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:8px 12px;display:flex;align-items:center;justify-content:space-between;font-size:12.5px;">
            <div style="font-weight:700;color:#1e293b;"><?= htmlspecialchars($a['nama']) ?></div>
            <span style="font-size:10.5px;font-weight:800;padding:2px 8px;border-radius:6px;letter-spacing:0.3px;
                         <?= $st === 'HADIR' ? 'background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;' : ($st === 'IZIN' ? 'background:#fffbeb;color:#92400e;border:1px solid #fde68a;' : 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;') ?>">
              <?= htmlspecialchars($st) ?>
            </span>
          </div>
          <?php endforeach; ?>
        </div>
        <div style="margin-top:12px;padding-top:10px;border-top:1px solid #e2e8f0;display:flex;gap:12px;font-size:11.5px;font-weight:700;flex-wrap:wrap;">
          <span style="color:#059669;">✓ <?= $hadirCount ?> Hadir</span>
          <?php if ($izinCount > 0): ?><span style="color:#d97706;">⏳ <?= $izinCount ?> Izin</span><?php endif; ?>
          <?php if ($tidakCount > 0): ?><span style="color:#dc2626;">✕ <?= $tidakCount ?> Tidak Hadir</span><?php endif; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Kanan: Notulensi Pembahasan Lengkap -->
      <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px;">
        <div style="font-size:12px;font-weight:800;color:#475569;text-transform:uppercase;margin-bottom:8px;display:flex;align-items:center;justify-content:space-between;">
          <span>📝 Notulensi Pembahasan &amp; Kesepakatan RTM</span>
          <?php if (!empty($evaluasi['notulensi'])): ?>
          <span style="font-size:11px;font-weight:600;color:#64748b;"><?= mb_strlen($evaluasi['notulensi']) ?> karakter</span>
          <?php endif; ?>
        </div>
        <div style="font-size:13px;color:#1e293b;line-height:1.75;white-space:pre-wrap;background:#fff;padding:16px;border-radius:8px;border:1px solid #e2e8f0;min-height:120px;">
          <?= !empty($evaluasi['notulensi']) ? htmlspecialchars($evaluasi['notulensi']) : '<span style="color:#94a3b8;font-style:italic;">Belum ada catatan notulensi rapat yang diinput</span>' ?>
        </div>
      </div>

    </div>

    <!-- Foto Dokumentasi Fisik -->
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px;">
      <div style="font-size:12px;font-weight:800;color:#475569;text-transform:uppercase;margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;">
        <span>📷 Foto / Dokumentasi Fisik RTM</span>
        <span style="font-size:11px;background:#e0e7ff;color:#3730a3;padding:2px 8px;border-radius:10px;font-weight:700;"><?= count($gambarList) ?> Foto</span>
      </div>
      <?php if (!empty($gambarList)): ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:12px;">
        <?php foreach ($gambarList as $img): ?>
        <?php
          $imgUrl = !empty($img['url']) ? $img['url'] : (str_starts_with($img['file_path'] ?? '', 'http') ? $img['file_path'] : BASE_URL . '/' . ltrim($img['file_path'] ?? '', '/'));
        ?>
        <div style="border-radius:10px;overflow:hidden;border:1.5px solid #cbd5e1;aspect-ratio:1;cursor:pointer;background:#fff;transition:transform 0.15s, box-shadow 0.15s;"
             onmouseover="this.style.transform='scale(1.04)';this.style.boxShadow='0 6px 14px rgba(0,0,0,0.12)'"
             onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'"
             onclick="previewImageModal('<?= htmlspecialchars($imgUrl) ?>')">
          <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Dokumentasi RTM" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <?php endforeach; ?>
      </div>
      <div style="margin-top:10px;font-size:12px;color:#64748b;font-style:italic;">💡 Klik pada salah satu foto untuk memperbesar tampilan resolusi penuh</div>
      <?php else: ?>
      <div style="font-size:12.5px;color:#94a3b8;font-style:italic;padding:16px 0;text-align:center;">📷 Belum ada foto dokumentasi yang diupload</div>
      <?php endif; ?>
    </div>

  </div>
</div>

<!-- Modal Lightbox Preview Foto -->
<div id="modalImagePreview" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(15,23,42,0.85);z-index:9999;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(4px);" onclick="closeImageModal()">
  <div style="position:relative;max-width:90vw;max-height:90vh;display:flex;flex-direction:column;align-items:center;" onclick="event.stopPropagation()">
    <img id="modalPreviewImg" src="" alt="Preview Foto" style="max-width:100%;max-height:80vh;border-radius:12px;box-shadow:0 20px 50px rgba(0,0,0,0.5);object-fit:contain;background:#fff;">
    <div style="display:flex;gap:12px;margin-top:14px;">
      <a id="modalDownloadBtn" href="" target="_blank" class="btn btn-primary btn-sm" style="font-weight:700;background:#059669;border:none;display:flex;align-items:center;gap:6px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6"/><polyline points="15 8 12 11 9 8"/><line x1="12" y1="11" x2="12" y2="3"/></svg>
        Buka Foto Resolusi Penuh ↗
      </a>
      <button type="button" onclick="closeImageModal()" class="btn btn-outline btn-sm" style="color:#fff;border-color:rgba(255,255,255,0.4);font-weight:700;">
        ✕ Tutup
      </button>
    </div>
  </div>
</div>

<script>
var currentEvKid = 'all';
var currentEvStatus = 'all';

function filterEvaluasi() {
  var query = (document.getElementById('evaluasiSearchInput')?.value || '').toLowerCase().trim();
  var items = document.querySelectorAll('#evaluasiListContainer .ev-item-card');
  var visibleCount = 0;

  items.forEach(function(item) {
    var kid = item.getAttribute('data-kid');
    var status = item.getAttribute('data-status');
    var text = item.innerText.toLowerCase();

    var matchKid = (currentEvKid === 'all' || kid === currentEvKid);
    var matchStatus = (currentEvStatus === 'all' || status === currentEvStatus);
    var matchQuery = (!query || text.indexOf(query) !== -1);

    if (matchKid && matchStatus && matchQuery) {
      item.style.display = '';
      visibleCount++;
    } else {
      item.style.display = 'none';
    }
  });

  var countEl = document.getElementById('evaluasiResultCount');
  if (countEl) {
    countEl.textContent = 'Menampilkan ' + visibleCount + ' standar';
  }
}

function filterEvaluasiKriteria(kid, btnEl) {
  currentEvKid = String(kid);
  document.querySelectorAll('.ev-kriteria-filter-btn').forEach(function(btn) {
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
  filterEvaluasi();
}

function filterEvaluasiStatus(status, btnEl) {
  currentEvStatus = status;
  document.querySelectorAll('.ev-status-filter-btn').forEach(function(btn) {
    btn.style.background = '#f8fafc';
    btn.style.color = '#64748b';
    btn.style.borderColor = '#e2e8f0';
    btn.classList.remove('active');
  });
  if (btnEl) {
    if (status === 'tercapai') {
      btnEl.style.background = '#ecfdf5';
      btnEl.style.color = '#065f46';
      btnEl.style.borderColor = '#a7f3d0';
    } else if (status === 'sebagian') {
      btnEl.style.background = '#fffbeb';
      btnEl.style.color = '#92400e';
      btnEl.style.borderColor = '#fde68a';
    } else if (status === 'belum_tercapai') {
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
  filterEvaluasi();
}

function previewImageModal(url) {
  var modal = document.getElementById('modalImagePreview');
  var img   = document.getElementById('modalPreviewImg');
  var btn   = document.getElementById('modalDownloadBtn');
  if (modal && img) {
    img.src = url;
    if (btn) btn.href = url;
    modal.style.display = 'flex';
  }
}

function closeImageModal() {
  var modal = document.getElementById('modalImagePreview');
  if (modal) modal.style.display = 'none';
}
</script>
