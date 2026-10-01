<?php
$pageTitle   = 'Detail Penetapan — ' . htmlspecialchars($penetapan['judul']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Penetapan', 'url' => BASE_URL . '/penetapan'],
  ['label' => htmlspecialchars($penetapan['judul'])],
];

$details = $penetapan['details'] ?? [];
$berkasSkList = $penetapan['berkas_list'] ?? [];

// Hitung kriteria unik & statistik
$kriteriaMap = [];
$totalIndikatorCount = 0;
foreach ($details as $d) {
    $kId = $d['kriteria_id'] ?? 0;
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

    // Hitung estimasi indikator (berdasarkan baris / poin)
    if (!empty($d['indikator'])) {
        $indLines = array_filter(explode("\n", $d['indikator']), fn($l) => trim($l) !== '');
        $totalIndikatorCount += max(1, count($indLines));
    }
}
?>

<!-- Page Header Banner -->
<div style="background:linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);border-radius:16px;padding:26px 30px;margin-bottom:24px;color:#fff;box-shadow:0 8px 24px rgba(30,27,75,0.18);">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap;">
    <div style="flex:1;min-width:280px;">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap;">
        <span style="font-size:11.5px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#a5b4fc;background:rgba(255,255,255,0.1);padding:4px 10px;border-radius:6px;">
          Tahap 1: Penetapan Standar Mutu SPMI
        </span>
        <span style="font-size:12px;font-weight:800;padding:4px 12px;border-radius:20px;<?= $penetapan['status'] === 'final' ? 'background:#059669;color:#fff;' : 'background:#f59e0b;color:#1e293b;' ?>">
          <?= $penetapan['status'] === 'final' ? '✓ Dokumen Final' : '⏳ Status Draft' ?>
        </span>
      </div>

      <h2 style="font-size:24px;font-weight:900;color:#fff;margin:0 0 10px;letter-spacing:-0.4px;">
        <?= htmlspecialchars($penetapan['judul']) ?>
      </h2>

      <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:13px;color:#cbd5e1;align-items:center;">
        <span style="display:flex;align-items:center;gap:6px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          Tahun Ajaran: <strong style="color:#fff;"><?= htmlspecialchars($penetapan['tahun_ajaran']['nama'] ?? '—') ?></strong>
        </span>
        <span>•</span>
        <span style="display:flex;align-items:center;gap:6px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          Dibuat: <?= date('d M Y', strtotime($penetapan['created_at'])) ?>
        </span>
      </div>

      <?php if (!empty($penetapan['deskripsi'])): ?>
      <p style="font-size:13.5px;color:#e2e8f0;margin:12px 0 0;line-height:1.6;max-width:760px;">
        <?= htmlspecialchars($penetapan['deskripsi']) ?>
      </p>
      <?php endif; ?>
    </div>

    <!-- Actions -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <a href="<?= BASE_URL ?>/penetapan/download-template-excel?format=xlsx" class="btn btn-outline"
         style="color:#a7f3d0;border-color:rgba(167,243,208,0.4);background:rgba(255,255,255,0.06);font-weight:700;font-size:12.5px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Unduh Excel
      </a>
      <a href="<?= BASE_URL ?>/penetapan/<?= $penetapan['id'] ?>/edit-step2" class="btn btn-primary"
         style="background:#4f46e5;border:none;font-weight:800;font-size:12.5px;padding:9px 16px;box-shadow:0 4px 12px rgba(79,70,229,0.35);">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
          <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        <?= $penetapan['status'] === 'final' ? 'Kelola / Edit Standar' : 'Lanjutkan Isi Standar' ?>
      </a>
      <?php if ($penetapan['status'] === 'draft'): ?>
      <form action="<?= BASE_URL ?>/penetapan/<?= $penetapan['id'] ?>/finalize" method="POST" style="display:inline;" onsubmit="return confirm('Finalisasi penetapan standar ini? Dokumen yang difinalisasi siap dilanjutkan ke tahap Pelaksanaan.')">
        <button type="submit" class="btn btn-success" style="font-weight:800;font-size:12.5px;padding:9px 16px;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
            <path d="M9 11l3 3L22 4"/>
            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
          </svg>
          Finalisasi
        </button>
      </form>
      <?php endif; ?>
      <a href="<?= BASE_URL ?>/ppepp<?= !empty($penetapan['ppepp_project_id']) ? '/' . $penetapan['ppepp_project_id'] : '' ?>" class="btn btn-outline"
         style="color:#cbd5e1;border-color:rgba(255,255,255,0.25);font-size:12.5px;">
        ← Project Library
      </a>
    </div>
  </div>
</div>

<!-- Quick Metrics Overview -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px;margin-bottom:24px;">
  <!-- Card 1: Total Standar -->
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:18px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.03);display:flex;align-items:center;gap:14px;">
    <div style="width:44px;height:44px;border-radius:12px;background:#eef2ff;color:#4f46e5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    </div>
    <div>
      <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Standar</div>
      <div style="font-size:24px;font-weight:900;color:#1e293b;line-height:1.2;margin-top:2px;">
        <?= count($details) ?> <span style="font-size:13px;font-weight:600;color:#64748b;">Standar</span>
      </div>
    </div>
  </div>

  <!-- Card 2: Kriteria Tercover -->
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:18px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.03);display:flex;align-items:center;gap:14px;">
    <div style="width:44px;height:44px;border-radius:12px;background:#f0fdf4;color:#059669;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
    </div>
    <div>
      <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Kriteria SPMI Tercover</div>
      <div style="font-size:24px;font-weight:900;color:#1e293b;line-height:1.2;margin-top:2px;">
        <?= count($kriteriaMap) ?> <span style="font-size:13px;font-weight:600;color:#64748b;">Kriteria</span>
      </div>
    </div>
  </div>

  <!-- Card 3: Total Indikator Mutu -->
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:18px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.03);display:flex;align-items:center;gap:14px;">
    <div style="width:44px;height:44px;border-radius:12px;background:#fffbeb;color:#d97706;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
    </div>
    <div>
      <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Indikator Capaian</div>
      <div style="font-size:24px;font-weight:900;color:#1e293b;line-height:1.2;margin-top:2px;">
        <?= $totalIndikatorCount ?> <span style="font-size:13px;font-weight:600;color:#64748b;">Poin</span>
      </div>
    </div>
  </div>

  <!-- Card 4: Berkas SK Pengesahan -->
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:18px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.03);display:flex;align-items:center;gap:14px;">
    <div style="width:44px;height:44px;border-radius:12px;background:#f0f9ff;color:#0284c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
    </div>
    <div>
      <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Surat Keputusan (SK)</div>
      <div style="font-size:24px;font-weight:900;color:#1e293b;line-height:1.2;margin-top:2px;">
        <?= count($berkasSkList) ?> <span style="font-size:13px;font-weight:600;color:#64748b;">Berkas</span>
      </div>
    </div>
  </div>
</div>

<!-- Controls: Filter Kriteria, Search & View Switcher -->
<div class="card mb-4" style="border-radius:14px;border:1.5px solid #e2e8f0;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
    
    <!-- Search Bar -->
    <div style="flex:1;min-width:260px;position:relative;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" width="16" height="16" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="standardSearchInput" onkeyup="filterStandards()" placeholder="Cari kode, aturan, pernyataan standar, atau indikator..."
             style="width:100%;padding:9px 12px 9px 36px;border:1.5px solid #cbd5e1;border-radius:10px;font-size:13px;outline:none;font-family:inherit;background:#f8fafc;"
             onfocus="this.style.background='#fff';this.style.borderColor='#4f46e5';" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1';">
    </div>

    <!-- View Mode Switcher -->
    <div style="display:flex;align-items:center;gap:6px;background:#f1f5f9;padding:4px;border-radius:10px;">
      <button type="button" id="btnViewCard" onclick="switchStandardView('card')"
              style="border:none;background:#fff;color:#4f46e5;font-weight:800;font-size:12px;padding:6px 12px;border-radius:7px;cursor:pointer;display:flex;align-items:center;gap:6px;box-shadow:0 1px 4px rgba(0,0,0,0.08);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Tampilan Detail
      </button>
      <button type="button" id="btnViewTable" onclick="switchStandardView('table')"
              style="border:none;background:transparent;color:#64748b;font-weight:700;font-size:12px;padding:6px 12px;border-radius:7px;cursor:pointer;display:flex;align-items:center;gap:6px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        Tampilan Tabel
      </button>
    </div>
  </div>

  <!-- Kriteria Filter Pills -->
  <?php if (count($kriteriaMap) > 1): ?>
  <div style="margin-top:14px;padding-top:12px;border-top:1px solid #f1f5f9;display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
    <span style="font-size:11.5px;font-weight:700;color:#64748b;margin-right:4px;">Filter Kriteria:</span>
    <button type="button" class="kriteria-filter-btn active" data-kid="all" onclick="filterByKriteria('all', this)"
            style="border:none;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;cursor:pointer;background:#4f46e5;color:#fff;transition:all 0.15s;">
      Semua (<?= count($details) ?>)
    </button>
    <?php foreach ($kriteriaMap as $k): ?>
    <button type="button" class="kriteria-filter-btn" data-kid="<?= $k['id'] ?>" onclick="filterByKriteria('<?= $k['id'] ?>', this)"
            style="border:1.5px solid #cbd5e1;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;background:#fff;color:#475569;transition:all 0.15s;">
      <?= htmlspecialchars($k['kode']) ?> - <?= htmlspecialchars($k['nama']) ?> (<?= $k['count'] ?>)
    </button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ============================================================
     CONTAINER 1: CARD VIEW (DETAIL BERSIH)
     ============================================================ -->
<div id="standardCardContainer" style="display:flex;flex-direction:column;gap:16px;">
  <?php if (empty($details)): ?>
  <div class="card" style="padding:48px 24px;text-align:center;border-radius:14px;border:1.5px dashed #cbd5e1;">
    <div style="font-size:16px;font-weight:800;color:#1e293b;margin-bottom:6px;">Belum Ada Standar yang Ditetapkan</div>
    <p style="font-size:13px;color:#64748b;margin-bottom:16px;">Silakan isi standar atau upload file Excel melalui tombol kelola standar.</p>
    <a href="<?= BASE_URL ?>/penetapan/<?= $penetapan['id'] ?>/edit-step2" class="btn btn-primary btn-sm">+ Isi Standar / Import Excel</a>
  </div>
  <?php else: ?>
  <?php foreach ($details as $idx => $d): ?>
  <?php
  $kId  = (string)($d['kriteria_id'] ?? '');
  $kode = !empty($d['kode']) ? $d['kode'] : (!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD-' . ($idx + 1));
  ?>
  <div class="standard-item-card" data-kid="<?= $kId ?>" style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,0.02);overflow:hidden;transition:box-shadow 0.2s;">
    
    <!-- Card Header -->
    <div style="background:#f8fafc;padding:12px 18px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
      <div style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:12px;font-weight:900;padding:4px 10px;border-radius:6px;background:#1e1b4b;color:#fff;">
          <?= htmlspecialchars($kode) ?>
        </span>
        <span style="font-size:13px;font-weight:800;color:#1e293b;">
          <?= htmlspecialchars($d['kriteria_nama']) ?>
        </span>
      </div>
      <div style="font-size:11.5px;color:#64748b;font-weight:600;">
        Item #<?= $idx + 1 ?>
      </div>
    </div>

    <!-- Card Content -->
    <div style="padding:18px 20px;">
      
      <!-- Pernyataan Standar (Target) -->
      <div style="margin-bottom:14px;">
        <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.6px;color:#059669;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
          Pernyataan Standar (Target Capaian)
        </div>
        <div style="font-size:15px;color:#0f172a;font-weight:700;line-height:1.65;">
          <?= !empty($d['target_capaian']) ? nl2br(htmlspecialchars($d['target_capaian'])) : '<span style="color:#94a3b8;font-style:italic;">Belum ditentukan</span>' ?>
        </div>
      </div>

      <!-- Indikator Ketercapaian -->
      <div style="margin-bottom:14px;background:#f8fafc;border-radius:10px;padding:12px 14px;border:1px solid #e2e8f0;">
        <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.6px;color:#d97706;margin-bottom:6px;display:flex;align-items:center;gap:6px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><polyline points="20 6 9 17 4 12"/></svg>
          Indikator Ketercapaian Kuantitatif / Kualitatif
        </div>
        <div style="font-size:13.5px;color:#334155;line-height:1.65;">
          <?php if (!empty($d['indikator'])): ?>
            <?php
            $indLines = explode("\n", trim($d['indikator']));
            ?>
            <ul style="margin:0;padding-left:18px;display:flex;flex-direction:column;gap:4px;">
              <?php foreach ($indLines as $iLine): ?>
                <?php if (trim($iLine) !== ''): ?>
                <li><?= htmlspecialchars(trim($iLine)) ?></li>
                <?php endif; ?>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <span style="color:#94a3b8;font-style:italic;">Belum ada indikator spesifik</span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Aturan / Acuan / Dasar Hukum -->
      <?php if (!empty($d['strategi'])): ?>
      <div style="display:flex;align-items:flex-start;gap:8px;font-size:12.5px;color:#475569;background:#f0fdf4;padding:8px 12px;border-radius:8px;border:1px solid #bbf7d0;">
        <span style="font-weight:800;color:#166534;flex-shrink:0;">📌 Dasar Hukum / Acuan:</span>
        <span style="line-height:1.5;"><?= htmlspecialchars($d['strategi']) ?></span>
      </div>
      <?php endif; ?>

    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- ============================================================
     CONTAINER 2: TABLE VIEW (RINGKAS & RAPI)
     ============================================================ -->
<div id="standardTableContainer" style="display:none;background:#fff;border:1.5px solid #cbd5e1;border-radius:14px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;" id="standardsTable">
      <thead>
        <tr style="background:#f1f5f9;border-bottom:1.5px solid #cbd5e1;color:#334155;font-weight:800;text-align:left;">
          <th style="padding:12px 14px;width:70px;">Kode</th>
          <th style="padding:12px 14px;width:160px;">Kriteria</th>
          <th style="padding:12px 14px;min-width:260px;">Pernyataan Standar (Target)</th>
          <th style="padding:12px 14px;min-width:240px;">Indikator Ketercapaian</th>
          <th style="padding:12px 14px;min-width:180px;">Dasar Hukum / Acuan</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($details as $idx => $d): ?>
        <?php
        $kId  = (string)($d['kriteria_id'] ?? '');
        $kode = !empty($d['kode']) ? $d['kode'] : (!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD-' . ($idx + 1));
        ?>
        <tr class="standard-table-row" data-kid="<?= $kId ?>" style="border-bottom:1px solid #e2e8f0;background:<?= $idx % 2 === 0 ? '#fff' : '#fafbfc' ?>;">
          <td style="padding:12px 14px;vertical-align:top;">
            <span style="font-weight:900;color:#1e1b4b;font-size:12px;background:#eef2ff;padding:3px 8px;border-radius:5px;">
              <?= htmlspecialchars($kode) ?>
            </span>
          </td>
          <td style="padding:12px 14px;font-weight:700;color:#1e293b;vertical-align:top;">
            <?= htmlspecialchars($d['kriteria_nama']) ?>
          </td>
          <td style="padding:12px 14px;color:#0f172a;font-weight:600;vertical-align:top;line-height:1.6;">
            <?= nl2br(htmlspecialchars($d['target_capaian'] ?? '—')) ?>
          </td>
          <td style="padding:12px 14px;color:#334155;vertical-align:top;line-height:1.6;">
            <?= nl2br(htmlspecialchars($d['indikator'] ?? '—')) ?>
          </td>
          <td style="padding:12px 14px;color:#64748b;vertical-align:top;line-height:1.5;font-size:12px;">
            <?= nl2br(htmlspecialchars($d['strategi'] ?? '—')) ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ============================================================
     BERKAS SURAT KEPUTUSAN (SK) PENGESAHAN STANDAR
     ============================================================ -->
<div class="card mb-4" style="border-radius:14px;border:1.5px solid #c7d2fe;overflow:hidden;box-shadow:0 2px 10px rgba(79,70,229,0.04);margin-top:28px;">
  <div class="card-header" style="background:#f8faff;padding:16px 22px;border-bottom:1.5px solid #c7d2fe;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <h3 class="card-title" style="font-size:15px;font-weight:800;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" width="18" height="18"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      Berkas Surat Keputusan (SK) Pengesahan Standar
    </h3>
    <span class="badge badge-primary" style="font-weight:800;font-size:12px;background:#4f46e5;">
      <?= count($berkasSkList) ?> berkas terlampir
    </span>
  </div>
  <div class="card-body" style="padding:22px;">
    <?php if (empty($berkasSkList)): ?>
    <div style="text-align:center;padding:24px 0;color:#64748b;font-size:13px;">
      Belum ada berkas SK yang diunggah untuk dokumen penetapan ini.
      <a href="<?= BASE_URL ?>/penetapan/<?= $penetapan['id'] ?>/edit-step2" style="color:#4f46e5;font-weight:700;text-decoration:underline;margin-left:6px;">Upload SK Pengesahan Sekarang →</a>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1.5px solid #e2e8f0;color:#475569;font-weight:800;text-align:left;">
            <th style="padding:10px 14px;width:35px;">#</th>
            <th style="padding:10px 14px;min-width:160px;">Nomor SK</th>
            <th style="padding:10px 14px;min-width:220px;">Perihal / Judul SK</th>
            <th style="padding:10px 14px;width:130px;">Tanggal SK</th>
            <th style="padding:10px 14px;width:120px;text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($berkasSkList as $bIdx => $sk): ?>
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:10px 14px;color:#94a3b8;font-weight:700;"><?= $bIdx + 1 ?></td>
            <td style="padding:10px 14px;font-weight:800;color:#1e293b;"><?= htmlspecialchars($sk['nomor_sk'] ?? '—') ?></td>
            <td style="padding:10px 14px;font-weight:600;color:#334155;"><?= htmlspecialchars($sk['judul_sk'] ?? '—') ?></td>
            <td style="padding:10px 14px;color:#64748b;"><?= htmlspecialchars($sk['tanggal_sk'] ?? '—') ?></td>
            <td style="padding:10px 14px;text-align:right;">
              <a href="<?= htmlspecialchars($sk['url'] ?? BASE_URL . '/' . $sk['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline"
                 style="font-weight:700;color:#4f46e5;border-color:#c7d2fe;background:#eef2ff;">
                📄 Unduh ↗
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
var CURRENT_KID_FILTER = 'all';

function switchStandardView(mode) {
  var cardContainer = document.getElementById('standardCardContainer');
  var tableContainer = document.getElementById('standardTableContainer');
  var btnCard = document.getElementById('btnViewCard');
  var btnTable = document.getElementById('btnViewTable');

  if (mode === 'table') {
    if (cardContainer) cardContainer.style.display = 'none';
    if (tableContainer) tableContainer.style.display = 'block';
    if (btnTable) {
      btnTable.style.background = '#fff';
      btnTable.style.color = '#4f46e5';
      btnTable.style.boxShadow = '0 1px 4px rgba(0,0,0,0.08)';
    }
    if (btnCard) {
      btnCard.style.background = 'transparent';
      btnCard.style.color = '#64748b';
      btnCard.style.boxShadow = 'none';
    }
  } else {
    if (cardContainer) cardContainer.style.display = 'flex';
    if (tableContainer) tableContainer.style.display = 'none';
    if (btnCard) {
      btnCard.style.background = '#fff';
      btnCard.style.color = '#4f46e5';
      btnCard.style.boxShadow = '0 1px 4px rgba(0,0,0,0.08)';
    }
    if (btnTable) {
      btnTable.style.background = 'transparent';
      btnTable.style.color = '#64748b';
      btnTable.style.boxShadow = 'none';
    }
  }
}

function filterByKriteria(kid, btnEl) {
  CURRENT_KID_FILTER = String(kid);

  var btns = document.querySelectorAll('.kriteria-filter-btn');
  btns.forEach(function(b) {
    b.style.background = '#fff';
    b.style.color = '#475569';
    b.style.border = '1.5px solid #cbd5e1';
  });

  if (btnEl) {
    btnEl.style.background = '#4f46e5';
    btnEl.style.color = '#fff';
    btnEl.style.border = 'none';
  }

  filterStandards();
}

function filterStandards() {
  var q = (document.getElementById('standardSearchInput')?.value || '').toLowerCase().trim();

  // Filter Cards
  var cards = document.querySelectorAll('.standard-item-card');
  cards.forEach(function(c) {
    var cKid = c.getAttribute('data-kid');
    var matchKid = (CURRENT_KID_FILTER === 'all' || CURRENT_KID_FILTER === cKid);
    var text = c.innerText.toLowerCase();
    var matchText = (q === '' || text.includes(q));

    c.style.display = (matchKid && matchText) ? 'block' : 'none';
  });

  // Filter Table Rows
  var rows = document.querySelectorAll('.standard-table-row');
  rows.forEach(function(r) {
    var rKid = r.getAttribute('data-kid');
    var matchKid = (CURRENT_KID_FILTER === 'all' || CURRENT_KID_FILTER === rKid);
    var text = r.innerText.toLowerCase();
    var matchText = (q === '' || text.includes(q));

    r.style.display = (matchKid && matchText) ? '' : 'none';
  });
}
</script>
