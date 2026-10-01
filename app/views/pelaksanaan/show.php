<?php
$pageTitle   = htmlspecialchars($pelaksanaan['judul']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Pelaksanaan', 'url' => BASE_URL . '/pelaksanaan'],
  ['label' => $pelaksanaan['judul']],
];
$p = $pelaksanaan ?? [];
$pen = $p['penetapan'] ?? [];
$details = $p['details'] ?? [];

$cStats = $p['checklist_stats'] ?? ['total' => count($details), 'terlaksana' => 0, 'proses' => 0, 'belum' => count($details), 'pct' => 0];

// Ekstraksi kriteria unik untuk filter
$kriteriaMap = [];
foreach ($details as $det) {
    $kId = (int)($det['kriteria_id'] ?? 0);
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

$totalBukti = 0;
foreach ($details as $d) {
    $totalBukti += count($d['bukti'] ?? []);
}
?>

<!-- Header Panel (Clean Academic / Institutional Design) -->
<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px 26px;margin-bottom:20px;box-shadow:0 1px 3px rgba(15,23,42,0.04);">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap;">
    <div style="flex:1;min-width:280px;">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#64748b;">
          Tahap 2 : Pelaksanaan Standar Mutu SPMI
        </span>
        <span style="display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:2px 10px;border-radius:6px;<?= $p['status'] === 'final' ? 'background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0;' : 'background:#fffbeb;color:#92400e;border:1px solid #fde68a;' ?>">
          <span style="width:6px;height:6px;border-radius:50%;background:currentColor;"></span>
          <?= $p['status'] === 'final' ? 'Dokumen Final' : 'Draft' ?>
        </span>
      </div>

      <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 10px;line-height:1.3;letter-spacing:-0.3px;">
        <?= htmlspecialchars($p['judul']) ?>
      </h1>

      <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;font-size:12.5px;color:#64748b;">
        <span>Acuan Penetapan: <strong style="color:#1e293b;"><?= htmlspecialchars($pen['judul'] ?? '—') ?></strong></span>
        <span>•</span>
        <span>Tahun Ajaran: <strong style="color:#1e293b;"><?= htmlspecialchars($pen['ta_nama'] ?? '—') ?></strong></span>
        <span>•</span>
        <span>Bukti Fisik: <strong style="color:#1e293b;"><?= $totalBukti ?> Tautan</strong></span>
      </div>
    </div>

    <!-- Actions -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <a href="<?= BASE_URL ?>/pelaksanaan/<?= $p['id'] ?>/edit" class="btn btn-primary" style="font-size:13px;font-weight:600;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit Pelaksanaan
      </a>

      <?php if ($p['status'] !== 'final'): ?>
      <form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $p['id'] ?>/finalize" onsubmit="return confirm('Finalisasi pelaksanaan ini?')" style="display:inline;">
        <button type="submit" class="btn btn-success" style="font-size:13px;font-weight:600;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          Finalisasi
        </button>
      </form>
      <?php endif; ?>

      <?php $pelVis = $p['visibility_status'] ?? 'aktif'; ?>
      <?php if ($pelVis === 'aktif'): ?>
      <form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $p['id'] ?>/toggle-status" onsubmit="return confirm('Nonaktifkan pelaksanaan ini?')" style="display:inline;">
        <input type="hidden" name="visibility_status" value="nonaktif">
        <button type="submit" class="btn btn-outline" style="font-size:13px;color:#475569;">
          Nonaktifkan
        </button>
      </form>
      <?php else: ?>
      <form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $p['id'] ?>/toggle-status" style="display:inline;">
        <input type="hidden" name="visibility_status" value="aktif">
        <button type="submit" class="btn btn-outline" style="font-size:13px;color:#059669;">
          Aktifkan Kembali
        </button>
      </form>
      <?php endif; ?>

      <a href="<?= BASE_URL ?>/ppepp<?= !empty($p['ppepp_project_id']) ? '/' . $p['ppepp_project_id'] : '' ?>" class="btn btn-outline" style="font-size:13px;color:#475569;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
        Project Library
      </a>
    </div>
  </div>
</div>

<!-- Metrics Overview -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px;margin-bottom:20px;">
  <!-- Total Standar -->
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);">
    <div style="font-size:11.5px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Total Standar</div>
    <div style="font-size:26px;font-weight:800;color:#0f172a;line-height:1.2;margin-top:4px;">
      <?= count($p['details']) ?> <span style="font-size:13px;font-weight:500;color:#64748b;">Standar</span>
    </div>
  </div>

  <!-- Keterlaksanaan Target -->
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #10b981;">
    <div style="display:flex;align-items:center;justify-content:space-between;">
      <span style="font-size:11.5px;color:#059669;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Tingkat Keterlaksanaan</span>
      <span style="font-size:14px;font-weight:800;color:#065f46;" id="summaryPctText"><?= $cStats['pct'] ?>%</span>
    </div>
    <div style="height:6px;background:#e2e8f0;border-radius:99px;margin-top:8px;overflow:hidden;">
      <div id="summaryProgressBar" style="height:100%;width:<?= $cStats['pct'] ?>%;background:#10b981;border-radius:99px;transition:width 0.3s;"></div>
    </div>
    <div style="font-size:11.5px;color:#64748b;margin-top:8px;display:flex;gap:8px;font-weight:600;">
      <span style="color:#059669;" id="summaryTerlaksanaCount"><?= $cStats['terlaksana'] ?> Selesai</span> •
      <span style="color:#b45309;" id="summaryProsesCount"><?= $cStats['proses'] ?> Proses</span> •
      <span style="color:#64748b;" id="summaryBelumCount"><?= $cStats['belum'] ?> Belum</span>
    </div>
  </div>

  <!-- Bukti Terlampir -->
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #1d4ed8;">
    <div style="font-size:11.5px;color:#1e40af;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Bukti Terlampir</div>
    <div style="font-size:26px;font-weight:800;color:#0f172a;line-height:1.2;margin-top:4px;">
      <?= $totalBukti ?> <span style="font-size:13px;font-weight:500;color:#1e40af;">Tautan Bukti</span>
    </div>
  </div>
</div>

<!-- Toolbar: Search, Filters & Expand Controls -->
<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 18px;box-shadow:0 1px 3px rgba(15,23,42,0.03);margin-bottom:20px;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
    
    <!-- Search Bar -->
    <div style="flex:1;min-width:260px;position:relative;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" width="15" height="15" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="pelaksanaanSearchInput" onkeyup="filterPelaksanaan()" placeholder="Cari kode, nama kriteria, target, catatan..."
             style="width:100%;padding:8px 12px 8px 34px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;outline:none;font-family:inherit;background:#f8fafc;"
             onfocus="this.style.background='#fff';this.style.borderColor='#1a237e';" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1';">
    </div>

    <!-- Status Buttons -->
    <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
      <button type="button" class="pel-status-filter-btn" data-status="all" onclick="filterPelaksanaanStatus('all', this)"
              style="border:1px solid #1e293b;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:#1e293b;color:#fff;">
        Semua (<?= count($p['details']) ?>)
      </button>
      <button type="button" class="pel-status-filter-btn" data-status="terlaksana" onclick="filterPelaksanaanStatus('terlaksana', this)"
              style="border:1px solid #e2e8f0;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:#fff;color:#065f46;">
        Selesai (<?= $cStats['terlaksana'] ?>)
      </button>
      <button type="button" class="pel-status-filter-btn" data-status="proses" onclick="filterPelaksanaanStatus('proses', this)"
              style="border:1px solid #e2e8f0;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:#fff;color:#92400e;">
        Dalam Proses (<?= $cStats['proses'] ?>)
      </button>
      <button type="button" class="pel-status-filter-btn" data-status="belum" onclick="filterPelaksanaanStatus('belum', this)"
              style="border:1px solid #e2e8f0;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:#fff;color:#475569;">
        Belum (<?= $cStats['belum'] ?>)
      </button>
    </div>

    <!-- Expand / Collapse All -->
    <div style="display:flex;gap:6px;">
      <button type="button" onclick="expandAllPelaksanaan()" class="btn btn-outline btn-sm"
              style="font-size:12px;font-weight:600;color:#0f172a;border-color:#cbd5e1;background:#fff;padding:5px 10px;">
        Buka Semua
      </button>
      <button type="button" onclick="collapseAllPelaksanaan()" class="btn btn-outline btn-sm"
              style="font-size:12px;font-weight:600;color:#64748b;border-color:#cbd5e1;background:#fff;padding:5px 10px;">
        Tutup Semua
      </button>
    </div>
  </div>

  <!-- Kriteria Filter Bar -->
  <?php if (count($kriteriaMap) > 1): ?>
  <div style="margin-top:12px;padding-top:10px;border-top:1px solid #f1f5f9;display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
    <span style="font-size:11.5px;font-weight:600;color:#64748b;margin-right:4px;">Filter Kriteria:</span>
    <button type="button" class="pel-kriteria-filter-btn active" data-kid="all" onclick="filterPelaksanaanKriteria('all', this)"
            style="border:1px solid #0f172a;padding:3px 10px;border-radius:4px;font-size:11.5px;font-weight:600;cursor:pointer;background:#0f172a;color:#fff;">
      Semua
    </button>
    <?php foreach ($kriteriaMap as $k): ?>
    <button type="button" class="pel-kriteria-filter-btn" data-kid="<?= $k['id'] ?>" onclick="filterPelaksanaanKriteria('<?= $k['id'] ?>', this)"
            style="border:1px solid #cbd5e1;padding:3px 10px;border-radius:4px;font-size:11.5px;font-weight:500;cursor:pointer;background:#fff;color:#475569;">
      <?= htmlspecialchars($k['kode']) ?>
    </button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
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

<div class="ka-item" id="det_<?= $pdId ?>" data-kid="<?= $kid ?>" data-status="<?= $status ?>"
     style="margin-bottom:12px;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;background:#fff;box-shadow:0 1px 3px rgba(15,23,42,0.03);">
  
  <!-- Header Accordion -->
  <div class="ka-header" onclick="toggleKriteria(<?= $pdId ?>)"
       style="cursor:pointer;padding:12px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;background:#f8fafc;border-bottom:1px solid #e2e8f0;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:10px;">
      <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:4px;background:#0f172a;color:#fff;">
        <?= htmlspecialchars($displayKode) ?>
      </span>
      <div>
        <span style="font-size:14px;font-weight:700;color:#0f172a;"><?= htmlspecialchars($det['kriteria_nama']) ?></span>
        <?php if (!empty($det['kriteria_deskripsi'])): ?>
        <span style="font-size:12px;color:#64748b;margin-left:6px;">— <?= htmlspecialchars($det['kriteria_deskripsi']) ?></span>
        <?php endif; ?>
      </div>
    </div>

    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
      <!-- Status Badge -->
      <span id="badgeStatus_<?= $pdId ?>"
            style="font-size:11px;font-weight:700;border-radius:4px;padding:3px 10px;
                   <?= $status === 'terlaksana' ? 'background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0;' : ($status === 'proses' ? 'background:#fffbeb;color:#92400e;border:1px solid #fde68a;' : 'background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;') ?>">
        <?= $status === 'terlaksana' ? 'Sudah Dilaksanakan' : ($status === 'proses' ? 'Dalam Proses' : 'Belum Dilaksanakan') ?>
      </span>

      <?php $nbukti = count($det['bukti']); ?>
      <span id="badgeBukti_<?= $pdId ?>" style="font-size:11px;font-weight:600;color:#64748b;background:#f1f5f9;border-radius:4px;padding:3px 8px;border:1px solid #e2e8f0;">
        <?= $nbukti ?> bukti
      </span>
      <svg id="toggle_<?= $pdId ?>" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" width="15" height="15" style="transition:transform 0.18s;transform:<?= $isFirst ? 'rotate(180deg)' : 'rotate(0deg)' ?>;">
        <polyline points="6 9 12 15 18 9"/>
      </svg>
    </div>
  </div>

  <div class="ka-body <?= $isFirst ? 'show' : '' ?>" id="body_<?= $pdId ?>" style="display:<?= $isFirst ? 'block' : 'none' ?>;padding:16px 18px;background:#fff;">

    <!-- 1. ACUAN TARGET & INDIKATOR PENETAPAN -->
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;margin-bottom:16px;">
      <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:10px;">
        Acuan Standar &amp; Target Mutu
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px;">
        <?php if (!empty($det['target_capaian'])): ?>
        <div>
          <div style="font-size:10.5px;font-weight:600;color:#64748b;margin-bottom:2px;">Pernyataan Standar (Target Capaian)</div>
          <div style="font-size:13px;color:#0f172a;line-height:1.6;font-weight:500;background:#fff;padding:8px 12px;border-radius:6px;border:1px solid #e2e8f0;">
            <?= nl2br(htmlspecialchars($det['target_capaian'])) ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($det['indikator'])): ?>
        <div>
          <div style="font-size:10.5px;font-weight:600;color:#64748b;margin-bottom:2px;">Indikator Ketercapaian Mutu</div>
          <div style="font-size:13px;color:#334155;line-height:1.6;background:#fff;padding:8px 12px;border-radius:6px;border:1px solid #e2e8f0;">
            <?= nl2br(htmlspecialchars($det['indikator'])) ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <?php if (!empty($det['strategi'])): ?>
      <div style="font-size:12px;color:#64748b;margin-top:10px;padding-top:8px;border-top:1px solid #e2e8f0;">
        <span style="font-weight:600;color:#475569;">Dasar Regulasi:</span> <?= htmlspecialchars($det['strategi']) ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- 2. STATUS & REALISASI PELAKSANAAN -->
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;margin-bottom:16px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px;">
        <div style="font-size:12px;font-weight:700;color:#0f172a;text-transform:uppercase;letter-spacing:0.5px;">
          Pembaruan Status &amp; Realisasi Standar
        </div>
        <span id="saveStatusIndicator_<?= $pdId ?>" style="font-size:11px;color:#94a3b8;"></span>
      </div>

      <!-- Segmented Status Selector -->
      <div style="display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap;">
        <label style="flex:1;min-width:140px;cursor:pointer;">
          <input type="radio" name="status_pelaksanaan_<?= $pdId ?>" value="terlaksana" <?= $status === 'terlaksana' ? 'checked' : '' ?>
                 onchange="updateChecklistStatus(<?= $pdId ?>, <?= $kid ?>, <?= $p['id'] ?>, 'terlaksana')" style="display:none;" id="radio_terlaksana_<?= $pdId ?>">
          <div id="pill_terlaksana_<?= $pdId ?>"
               style="padding:8px 12px;border-radius:6px;border:1px solid <?= $status === 'terlaksana' ? '#059669' : '#e2e8f0' ?>;background:<?= $status === 'terlaksana' ? '#ecfdf5' : '#fff' ?>;color:<?= $status === 'terlaksana' ? '#065f46' : '#475569' ?>;font-weight:600;font-size:12px;text-align:center;transition:all 0.15s;">
            Sudah Dilaksanakan
          </div>
        </label>

        <label style="flex:1;min-width:140px;cursor:pointer;">
          <input type="radio" name="status_pelaksanaan_<?= $pdId ?>" value="proses" <?= $status === 'proses' ? 'checked' : '' ?>
                 onchange="updateChecklistStatus(<?= $pdId ?>, <?= $kid ?>, <?= $p['id'] ?>, 'proses')" style="display:none;" id="radio_proses_<?= $pdId ?>">
          <div id="pill_proses_<?= $pdId ?>"
               style="padding:8px 12px;border-radius:6px;border:1px solid <?= $status === 'proses' ? '#d97706' : '#e2e8f0' ?>;background:<?= $status === 'proses' ? '#fffbeb' : '#fff' ?>;color:<?= $status === 'proses' ? '#92400e' : '#475569' ?>;font-weight:600;font-size:12px;text-align:center;transition:all 0.15s;">
            Dalam Proses
          </div>
        </label>

        <label style="flex:1;min-width:140px;cursor:pointer;">
          <input type="radio" name="status_pelaksanaan_<?= $pdId ?>" value="belum" <?= $status === 'belum' ? 'checked' : '' ?>
                 onchange="updateChecklistStatus(<?= $pdId ?>, <?= $kid ?>, <?= $p['id'] ?>, 'belum')" style="display:none;" id="radio_belum_<?= $pdId ?>">
          <div id="pill_belum_<?= $pdId ?>"
               style="padding:8px 12px;border-radius:6px;border:1px solid <?= $status === 'belum' ? '#64748b' : '#e2e8f0' ?>;background:<?= $status === 'belum' ? '#f1f5f9' : '#fff' ?>;color:<?= $status === 'belum' ? '#1e293b' : '#475569' ?>;font-weight:600;font-size:12px;text-align:center;transition:all 0.15s;">
            Belum Dilaksanakan
          </div>
        </label>
      </div>

      <!-- Realisasi & Catatan Pelaksanaan -->
      <div style="display:grid;grid-template-columns:220px 1fr;gap:12px;margin-top:8px;">
        <div>
          <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px;">
            Realisasi Angka (Kuantitatif):
          </label>
          <input type="text" id="capaian_angka_<?= $pdId ?>" class="form-control"
                 placeholder="Contoh: 85 atau 100%"
                 value="<?= htmlspecialchars($det['capaian_angka'] ?? '') ?>"
                 onchange="saveCatatanPelaksanaan(<?= $pdId ?>, <?= $kid ?>, <?= $p['id'] ?>)"
                 style="font-size:12.5px;padding:7px 10px;border-radius:6px;border:1px solid #cbd5e1;">
        </div>
        <div>
          <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px;">
            Catatan Realisasi Pelaksanaan:
          </label>
          <textarea id="catatan_<?= $pdId ?>" class="form-control" rows="2"
                    placeholder="Tuliskan catatan kemajuan realisasi pelaksanaan..."
                    onchange="saveCatatanPelaksanaan(<?= $pdId ?>, <?= $kid ?>, <?= $p['id'] ?>)"
                    style="font-size:12.5px;padding:7px 10px;border-radius:6px;border:1px solid #cbd5e1;"><?= htmlspecialchars($det['catatan_pelaksanaan'] ?? '') ?></textarea>
        </div>
      </div>
    </div>

    <!-- 3. LIST BUKTI FISIK / DOKUMEN -->
    <div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">
        Bukti Pelaksanaan (<?= count($det['bukti']) ?> Tautan)
      </div>

      <div id="buktiList_<?= $pdId ?>">
        <?php if (empty($det['bukti'])): ?>
        <div id="emptyBukti_<?= $pdId ?>" style="text-align:center;padding:16px;background:#f8fafc;border:1px dashed #cbd5e1;border-radius:6px;">
          <div style="font-size:12.5px;color:#94a3b8;">Belum ada tautan bukti fisik yang dilampirkan</div>
        </div>
        <?php else: ?>
        <?php foreach ($det['bukti'] as $b): ?>
        <div class="bukti-item" id="bukti_<?= $b['id'] ?>" style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;margin-bottom:6px;">
          <div style="flex:1;min-width:0;">
            <div style="font-size:13px;font-weight:700;color:#0f172a;"><?= htmlspecialchars($b['judul_bukti']) ?></div>
            <a href="<?= htmlspecialchars($b['url_link']) ?>" target="_blank" rel="noopener"
               style="font-size:12px;color:#1d4ed8;display:block;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:420px;">
              <?= htmlspecialchars($b['url_link']) ?> ↗
            </a>
            <?php if ($b['keterangan']): ?>
            <div style="font-size:11.5px;color:#64748b;margin-top:2px;"><?= htmlspecialchars($b['keterangan']) ?></div>
            <?php endif; ?>
          </div>
          <button type="button" onclick="deleteBukti(<?= $b['id'] ?>, <?= $p['id'] ?>, <?= $pdId ?>)"
                  style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;border-radius:4px;"
                  onmouseover="this.style.color='#dc2626';" onmouseout="this.style.color='#94a3b8';"
                  title="Hapus bukti">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
          </button>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Form tambah bukti -->
      <div style="margin-top:10px;background:#fff;border:1px solid #e2e8f0;border-radius:6px;padding:12px;">
        <div style="font-size:11.5px;font-weight:700;color:#0f172a;margin-bottom:8px;">Tambah Bukti Link</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:8px;">
          <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:3px;">Judul Dokumen / Bukti *</label>
            <input type="text" id="judulBukti_<?= $pdId ?>" placeholder="Misal: Notulensi Rapat Jurusan"
                   style="width:100%;padding:7px 10px;border:1px solid #cbd5e1;border-radius:4px;font-size:12.5px;font-family:inherit;">
          </div>
          <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:3px;">URL / Link Dokumen *</label>
            <input type="url" id="urlLink_<?= $pdId ?>" placeholder="https://..."
                   style="width:100%;padding:7px 10px;border:1px solid #cbd5e1;border-radius:4px;font-size:12.5px;font-family:inherit;">
          </div>
        </div>
        <div style="margin-bottom:8px;">
          <input type="text" id="ketBukti_<?= $pdId ?>" placeholder="Keterangan singkat (opsional)"
                 style="width:100%;padding:7px 10px;border:1px solid #cbd5e1;border-radius:4px;font-size:12px;font-family:inherit;">
        </div>
        <button type="button" onclick="addBukti(<?= $pdId ?>, <?= $kid ?>, <?= $p['id'] ?>)"
                id="btnAddBukti_<?= $pdId ?>" class="btn btn-outline btn-sm"
                style="font-size:12px;font-weight:600;color:#0f172a;border-color:#cbd5e1;background:#f8fafc;">
          Simpan Tautan Bukti
        </button>
        <div id="buktiError_<?= $pdId ?>" style="display:none;margin-top:6px;font-size:12px;color:#dc2626;"></div>
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
    btnEl.style.background = '#0f172a';
    btnEl.style.color = '#fff';
    btnEl.style.borderColor = '#0f172a';
    btnEl.classList.add('active');
  }
  filterPelaksanaan();
}

function filterPelaksanaanStatus(status, btnEl) {
  currentPelStatus = status;
  document.querySelectorAll('.pel-status-filter-btn').forEach(function(btn) {
    btn.style.background = '#fff';
    btn.style.color = '#475569';
    btn.style.borderColor = '#e2e8f0';
    btn.classList.remove('active');
  });
  if (btnEl) {
    btnEl.style.background = '#1e293b';
    btnEl.style.color = '#fff';
    btnEl.style.borderColor = '#1e293b';
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
    btn.textContent = 'Menyimpan...';
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
      btn.textContent = 'Simpan Tautan Bukti';
    }

    if (data.success) {
      if (judulEl) judulEl.value = '';
      if (urlEl) urlEl.value = '';
      if (ketEl) ketEl.value = '';

      var emptyEl = document.getElementById('emptyBukti_' + pdId);
      if (emptyEl) emptyEl.style.display = 'none';

      var list = document.getElementById('buktiList_' + pdId);
      if (list) {
        var b = data.bukti;
        var html = '<div class="bukti-item" id="bukti_' + b.id + '" style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;margin-bottom:6px;">' +
                   '<div style="flex:1;min-width:0;">' +
                   '<div style="font-size:13px;font-weight:700;color:#0f172a;">' + escHtml(b.judul_bukti) + '</div>' +
                   '<a href="' + escHtml(b.url_link) + '" target="_blank" rel="noopener" style="font-size:12px;color:#1d4ed8;display:block;margin-top:2px;">' + escHtml(b.url_link) + ' ↗</a>' +
                   (b.keterangan ? '<div style="font-size:11.5px;color:#64748b;margin-top:2px;">' + escHtml(b.keterangan) + '</div>' : '') +
                   '</div>' +
                   '<button type="button" onclick="deleteBukti(' + b.id + ', ' + pelaksanaanId + ', ' + pdId + ')" style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;border-radius:4px;">' +
                   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>' +
                   '</button>' +
                   '</div>';
        list.insertAdjacentHTML('beforeend', html);
      }

      var badge = document.getElementById('badgeBukti_' + pdId);
      if (badge) {
        var count = document.querySelectorAll('#buktiList_' + pdId + ' .bukti-item').length;
        badge.textContent = count + ' bukti';
      }
    } else {
      if (errEl) {
        errEl.textContent = data.error || 'Gagal menyimpan bukti.';
        errEl.style.display = 'block';
      }
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
        el.remove();
        if (pdId) {
          var badge = document.getElementById('badgeBukti_' + pdId);
          if (badge) {
            var n = document.querySelectorAll('#buktiList_' + pdId + ' .bukti-item').length;
            badge.textContent = n + ' bukti';
          }
        }
      }
    }
  });
}

function escHtml(str) {
  var d = document.createElement('div');
  d.appendChild(document.createTextNode(str));
  return d.innerHTML;
}

// Checklist Status & Catatan Pelaksanaan Handler
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
          pill.style.borderColor = '#059669'; pill.style.background = '#ecfdf5'; pill.style.color = '#065f46';
        } else if (st === 'proses') {
          pill.style.borderColor = '#d97706'; pill.style.background = '#fffbeb'; pill.style.color = '#92400e';
        } else {
          pill.style.borderColor = '#64748b'; pill.style.background = '#f1f5f9'; pill.style.color = '#1e293b';
        }
      } else {
        pill.style.borderColor = '#e2e8f0'; pill.style.background = '#fff'; pill.style.color = '#475569';
      }
    }
  });

  // Update header badge
  var badge = document.getElementById('badgeStatus_' + pdId);
  if (badge) {
    if (status === 'terlaksana') {
      badge.textContent = 'Sudah Dilaksanakan'; badge.style.background = '#ecfdf5'; badge.style.color = '#065f46'; badge.style.borderColor = '#bbf7d0';
    } else if (status === 'proses') {
      badge.textContent = 'Dalam Proses'; badge.style.background = '#fffbeb'; badge.style.color = '#92400e'; badge.style.borderColor = '#fde68a';
    } else {
      badge.textContent = 'Belum Dilaksanakan'; badge.style.background = '#f1f5f9'; badge.style.color = '#475569'; badge.style.borderColor = '#cbd5e1';
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
      if (data.success) {
        indicator.textContent = 'Status tersimpan';
        indicator.style.color = '#059669';
        setTimeout(function() { indicator.textContent = ''; }, 2000);
        if (data.checklist_stats) {
          updateTopChecklistSummary(data.checklist_stats);
        }
      } else {
        indicator.textContent = data.error || 'Gagal menyimpan';
        indicator.style.color = '#dc2626';
      }
    }
  });
}

function saveCatatanPelaksanaan(pdId, kid, pelaksanaanId) {
  var indicator = document.getElementById('saveStatusIndicator_' + pdId);
  if (indicator) { indicator.textContent = 'Menyimpan...'; indicator.style.color = '#94a3b8'; }

  var catatan = document.getElementById('catatan_' + pdId)?.value || '';
  var capaianAngka = document.getElementById('capaian_angka_' + pdId)?.value || '';

  var radios = document.getElementsByName('status_pelaksanaan_' + pdId);
  var status = 'belum';
  for (var i = 0; i < radios.length; i++) {
    if (radios[i].checked) { status = radios[i].value; break; }
  }

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
      indicator.textContent = 'Tersimpan';
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
  if (terlaksanaEl) terlaksanaEl.textContent = stats.terlaksana + ' Selesai';
  if (prosesEl) prosesEl.textContent = stats.proses + ' Proses';
  if (belumEl) belumEl.textContent = stats.belum + ' Belum';
}
</script>
