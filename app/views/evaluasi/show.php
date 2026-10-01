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

<!-- Header Panel (Clean Academic / Institutional Design) -->
<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px 26px;margin-bottom:20px;box-shadow:0 1px 3px rgba(15,23,42,0.04);">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap;">
    <div style="flex:1;min-width:280px;">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#64748b;">
          Tahap 3 : Evaluasi SPMI (PPEPP)
        </span>
        <span style="display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:2px 10px;border-radius:6px;<?= $evaluasi['status'] === 'final' ? 'background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0;' : 'background:#fffbeb;color:#92400e;border:1px solid #fde68a;' ?>">
          <span style="width:6px;height:6px;border-radius:50%;background:currentColor;"></span>
          <?= $evaluasi['status'] === 'final' ? 'Dokumen Final' : 'Draft' ?>
        </span>
      </div>

      <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 10px;line-height:1.3;letter-spacing:-0.3px;">
        <?= htmlspecialchars($evaluasi['judul']) ?>
      </h1>

      <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;font-size:12.5px;color:#64748b;">
        <span>Acuan Penetapan: <strong style="color:#1e293b;"><?= htmlspecialchars($penetapan['judul'] ?? '—') ?></strong></span>
        <span>•</span>
        <span>Tahun Ajaran: <strong style="color:#1e293b;"><?= htmlspecialchars($penetapan['ta_nama'] ?? '—') ?> <?= !empty($penetapan['semester']) ? '(' . htmlspecialchars($penetapan['semester']) . ')' : '' ?></strong></span>
        <span>•</span>
        <span>Jenis Evaluasi: <strong style="color:#1e293b;"><?= htmlspecialchars(ucwords($evaluasi['jenis'] ?? 'Internal')) ?></strong></span>
        <span>•</span>
        <span>Dibuat: <strong style="color:#1e293b;"><?= date('d F Y', strtotime($evaluasi['created_at'])) ?></strong></span>
      </div>

      <?php if (!empty($evaluasi['deskripsi'])): ?>
      <p style="font-size:13px;color:#475569;margin-top:10px;line-height:1.6;max-width:850px;">
        <?= htmlspecialchars($evaluasi['deskripsi']) ?>
      </p>
      <?php endif; ?>
    </div>

    <!-- Actions -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <a href="<?= BASE_URL ?>/evaluasi/<?= $evId ?>/edit" class="btn btn-primary" style="font-size:13px;font-weight:600;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
          <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Edit Evaluasi &amp; RTM
      </a>

      <?php if ($evaluasi['status'] === 'draft'): ?>
      <form action="<?= BASE_URL ?>/evaluasi/<?= $evId ?>/finalize" method="POST" style="display:inline;" onsubmit="return confirm('Finalisasi dokumen evaluasi ini?')">
        <button type="submit" class="btn btn-success" style="font-size:13px;font-weight:600;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
            <path d="M9 11l3 3L22 4"/>
            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
          </svg>
          Finalisasi
        </button>
      </form>
      <?php endif; ?>

      <a href="<?= BASE_URL ?>/ppepp<?= !empty($evaluasi['ppepp_project_id']) ? '/' . $evaluasi['ppepp_project_id'] : '' ?>" class="btn btn-outline" style="font-size:13px;color:#475569;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
        Project Library
      </a>
    </div>
  </div>
</div>

<!-- Metrics Overview -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px;margin-bottom:20px;">
  <!-- Total -->
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);">
    <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Standar / Indikator</div>
    <div style="font-size:26px;font-weight:800;color:#0f172a;line-height:1.2;margin-top:4px;">
      <?= $totalKriteria ?> <span style="font-size:13px;font-weight:500;color:#64748b;">Item</span>
    </div>
  </div>

  <!-- Tercapai Penuh -->
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #10b981;">
    <div style="font-size:11.5px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.5px;">Tercapai Penuh</div>
    <div style="font-size:26px;font-weight:800;color:#065f46;line-height:1.2;margin-top:4px;">
      <?= $cntTercapai ?> <span style="font-size:13px;font-weight:600;color:#059669;">(<?= $pctTercapai ?>%)</span>
    </div>
  </div>

  <!-- Tercapai Sebagian -->
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #f59e0b;">
    <div style="font-size:11.5px;font-weight:700;color:#b45309;text-transform:uppercase;letter-spacing:0.5px;">Tercapai Sebagian</div>
    <div style="font-size:26px;font-weight:800;color:#92400e;line-height:1.2;margin-top:4px;">
      <?= $cntSebagian ?> <span style="font-size:13px;font-weight:500;color:#b45309;">Item</span>
    </div>
  </div>

  <!-- Belum Tercapai -->
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #94a3b8;">
    <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Belum Tercapai</div>
    <div style="font-size:26px;font-weight:800;color:#334155;line-height:1.2;margin-top:4px;">
      <?= $cntBelum ?> <span style="font-size:13px;font-weight:500;color:#64748b;">Item</span>
    </div>
  </div>
</div>

<!-- Toolbar: Search & Filter -->
<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 18px;box-shadow:0 1px 3px rgba(15,23,42,0.03);margin-bottom:20px;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
    
    <!-- Search Bar -->
    <div style="flex:1;min-width:260px;position:relative;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" width="15" height="15" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="evaluasiSearchInput" onkeyup="filterEvaluasi()" placeholder="Cari kode, nama standar, narasi evaluasi..."
             style="width:100%;padding:8px 12px 8px 34px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;outline:none;font-family:inherit;background:#f8fafc;"
             onfocus="this.style.background='#fff';this.style.borderColor='#1a237e';" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1';">
    </div>

    <!-- Status Filter Buttons -->
    <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
      <button type="button" class="ev-status-filter-btn" data-status="all" onclick="filterEvaluasiStatus('all', this)"
              style="border:1px solid #1e293b;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:#1e293b;color:#fff;">
        Semua (<?= $totalKriteria ?>)
      </button>
      <button type="button" class="ev-status-filter-btn" data-status="tercapai" onclick="filterEvaluasiStatus('tercapai', this)"
              style="border:1px solid #e2e8f0;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:#fff;color:#065f46;">
        Tercapai Penuh (<?= $cntTercapai ?>)
      </button>
      <button type="button" class="ev-status-filter-btn" data-status="sebagian" onclick="filterEvaluasiStatus('sebagian', this)"
              style="border:1px solid #e2e8f0;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:#fff;color:#92400e;">
        Tercapai Sebagian (<?= $cntSebagian ?>)
      </button>
      <button type="button" class="ev-status-filter-btn" data-status="belum_tercapai" onclick="filterEvaluasiStatus('belum_tercapai', this)"
              style="border:1px solid #e2e8f0;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:#fff;color:#475569;">
        Belum Tercapai (<?= $cntBelum ?>)
      </button>
    </div>

    <!-- Counter -->
    <div id="evaluasiResultCount" style="font-size:12px;font-weight:600;color:#64748b;">
      Menampilkan <?= $totalKriteria ?> standar
    </div>
  </div>

  <!-- Kriteria Filter Bar jika ada > 1 kriteria -->
  <?php if (count($kriteriaMap) > 1): ?>
  <div style="margin-top:12px;padding-top:10px;border-top:1px solid #f1f5f9;display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
    <span style="font-size:11.5px;font-weight:600;color:#64748b;margin-right:4px;">Kriteria:</span>
    <button type="button" class="ev-kriteria-filter-btn active" data-kid="all" onclick="filterEvaluasiKriteria('all', this)"
            style="border:1px solid #0f172a;padding:3px 10px;border-radius:4px;font-size:11.5px;font-weight:600;cursor:pointer;background:#0f172a;color:#fff;">
      Semua
    </button>
    <?php foreach ($kriteriaMap as $k): ?>
    <button type="button" class="ev-kriteria-filter-btn" data-kid="<?= $k['id'] ?>" onclick="filterEvaluasiKriteria('<?= $k['id'] ?>', this)"
            style="border:1px solid #cbd5e1;padding:3px 10px;border-radius:4px;font-size:11.5px;font-weight:500;cursor:pointer;background:#fff;color:#475569;">
      <?= htmlspecialchars($k['kode']) ?>
    </button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- SECTION 1: RINCIAN HASIL EVALUASI -->
<div style="margin-bottom:12px;">
  <h2 style="font-size:15px;font-weight:800;color:#0f172a;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 14px;">
    1. Rincian Capaian Mutu per Standar
  </h2>
</div>

<div id="evaluasiListContainer" style="display:flex;flex-direction:column;gap:14px;margin-bottom:30px;">
  <?php foreach ($details as $idx => $d): ?>
  <?php
  $stPel       = $d['status_pelaksanaan'] ?? 'belum';
  $stCapaian   = $d['status_capaian'] ?? 'belum_tercapai';
  $displayKode = !empty($d['kode']) ? $d['kode'] : (!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD-' . ($idx + 1));
  $kId         = (int)($d['kriteria_id'] ?? 0);
  ?>
  <div class="ev-item-card" data-kid="<?= $kId ?>" data-status="<?= $stCapaian ?>"
       style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;background:#fff;box-shadow:0 1px 3px rgba(15,23,42,0.03);">
    
    <!-- Header Standar -->
    <div style="background:#f8fafc;padding:12px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid #e2e8f0;flex-wrap:wrap;">
      <div style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:11px;font-weight:700;background:#0f172a;color:#fff;padding:2px 8px;border-radius:4px;letter-spacing:0.3px;">
          <?= htmlspecialchars($displayKode) ?>
        </span>
        <div>
          <span style="font-weight:700;font-size:14px;color:#0f172a;"><?= htmlspecialchars($d['kriteria_nama']) ?></span>
          <?php if (!empty($d['kriteria_deskripsi'])): ?>
          <span style="font-size:12px;color:#64748b;margin-left:6px;">— <?= htmlspecialchars($d['kriteria_deskripsi']) ?></span>
          <?php endif; ?>
        </div>
      </div>

      <div>
        <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:4px;
                     <?= $stCapaian === 'tercapai' ? 'background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0;' : ($stCapaian === 'sebagian' ? 'background:#fffbeb;color:#92400e;border:1px solid #fde68a;' : 'background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;') ?>">
          <?= $stCapaian === 'tercapai' ? 'Tercapai Penuh' : ($stCapaian === 'sebagian' ? 'Tercapai Sebagian' : 'Belum Tercapai') ?>
        </span>
      </div>
    </div>

    <!-- Body Standar -->
    <div style="padding:16px 18px;display:flex;flex-direction:column;gap:12px;">
      
      <!-- Grid Target & Indikator -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:16px;">
        <!-- Target Capaian -->
        <div>
          <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:4px;">Pernyataan Standar (Target)</div>
          <div style="font-size:13px;color:#1e293b;line-height:1.6;background:#f8fafc;padding:10px 14px;border-radius:6px;border:1px solid #e2e8f0;">
            <?= nl2br(htmlspecialchars($d['target_capaian'] ?? $d['target'] ?? '—')) ?>
          </div>
        </div>

        <!-- Indikator Mutu -->
        <div>
          <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:4px;">Indikator Ketercapaian Mutu</div>
          <div style="font-size:13px;color:#1e293b;line-height:1.6;background:#f8fafc;padding:10px 14px;border-radius:6px;border:1px solid #e2e8f0;">
            <?= nl2br(htmlspecialchars($d['indikator'] ?? '—')) ?>
          </div>
        </div>
      </div>

      <!-- Realisasi Pelaksanaan Bar -->
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:6px;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;font-size:12.5px;">
        <div style="color:#334155;">
          <span style="color:#64748b;font-weight:600;">Status Pelaksanaan:</span>
          <span style="font-weight:700;color:<?= $stPel === 'terlaksana' ? '#059669' : ($stPel === 'proses' ? '#b45309' : '#64748b') ?>;margin-left:4px;">
            <?= $stPel === 'terlaksana' ? 'Sudah Dilaksanakan' : ($stPel === 'proses' ? 'Dalam Proses' : 'Belum Dilaksanakan') ?>
          </span>
          <?php if (!empty($d['catatan_pelaksanaan'])): ?>
          <span style="color:#94a3b8;margin:0 6px;">|</span>
          <span style="color:#64748b;">Catatan:</span> <?= htmlspecialchars($d['catatan_pelaksanaan']) ?>
          <?php endif; ?>
        </div>

        <?php
        $capPel = trim($d['capaian_angka_pelaksanaan'] ?? '');
        $capEv  = trim($d['capaian_angka_evaluasi'] ?? '');
        if ($capPel !== '' || $capEv !== ''):
            $badgeText  = '—';
            $badgeStyle = 'background:#f1f5f9;color:#475569;';
            if ($capPel !== '' && $capEv !== '') {
                $pNum = (float)preg_replace('/[^0-9.]/', '', $capPel);
                $eNum = (float)preg_replace('/[^0-9.]/', '', $capEv);
                if ($pNum > $eNum) {
                    $diff = round($pNum - $eNum, 2);
                    $badgeText  = 'Melebihi Target (+' . $diff . ')';
                    $badgeStyle = 'background:#ecfdf5;color:#047857;border:1px solid #bbf7d0;';
                } elseif ($pNum == $eNum) {
                    $badgeText  = 'Sesuai Target (100%)';
                    $badgeStyle = 'background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;';
                } else {
                    $diff = round($eNum - $pNum, 2);
                    $badgeText  = 'Belum Mencapai (-' . $diff . ')';
                    $badgeStyle = 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;';
                }
            }
        ?>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;">
          <span style="color:#64748b;">Data Angka:</span>
          <span style="font-weight:600;color:#1e293b;">Pelaksanaan: <strong><?= htmlspecialchars($capPel ?: '—') ?></strong></span>
          <span style="color:#94a3b8;">/</span>
          <span style="font-weight:600;color:#1e293b;">Evaluasi: <strong><?= htmlspecialchars($capEv ?: '—') ?></strong></span>
          <span style="font-weight:700;padding:2px 8px;border-radius:4px;font-size:11px;<?= $badgeStyle ?>">
            <?= $badgeText ?>
          </span>
        </div>
        <?php endif; ?>
      </div>

      <!-- Analisis & Narasi Evaluasi -->
      <div>
        <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:4px;">
          Analisis &amp; Narasi Evaluasi Mutu
        </div>
        <div style="font-size:13px;color:#1e293b;line-height:1.7;white-space:pre-wrap;background:#fff;padding:12px 14px;border-radius:6px;border:1px solid #e2e8f0;">
          <?= !empty($d['evaluasi_teks']) ? htmlspecialchars($d['evaluasi_teks']) : '<span style="color:#94a3b8;font-style:italic;">Belum ada catatan narasi evaluasi</span>' ?>
        </div>
      </div>

      <?php if (!empty($d['strategi'])): ?>
      <div style="font-size:12px;color:#64748b;padding-top:4px;">
        <span style="font-weight:600;color:#475569;">Dasar Regulasi:</span> <?= htmlspecialchars($d['strategi']) ?>
      </div>
      <?php endif; ?>

    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- SECTION 2: DOKUMENTASI RAPAT TINJAUAN MANAJEMEN (RTM) -->
<div style="margin-bottom:12px;">
  <h2 style="font-size:15px;font-weight:800;color:#0f172a;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 14px;">
    2. Dokumentasi Rapat Tinjauan Manajemen (RTM) &amp; Bukti Fisik
  </h2>
</div>

<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,0.03);margin-bottom:30px;">
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
  <div style="background:#f8fafc;padding:14px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
      <div style="font-size:14.5px;font-weight:700;color:#0f172a;">Dokumentasi Pelaksanaan RTM</div>
      <div style="font-size:12px;color:#64748b;margin-top:2px;">Hasil koordinasi dan kesepakatan Rapat Tinjauan Manajemen</div>
    </div>
    
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
      <?php if ($notulRapatId > 0): ?>
      <span style="font-size:11px;font-weight:700;color:#065f46;background:#ecfdf5;padding:3px 8px;border-radius:4px;border:1px solid #bbf7d0;">
        Terhubung Notulensi #<?= $notulRapatId ?>
      </span>
      <a href="<?= $notulViewUrl ?>" target="_blank" class="btn btn-outline btn-sm" style="font-size:11.5px;font-weight:600;color:#1e293b;border-color:#cbd5e1;background:#fff;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6"/><polyline points="15 8 12 11 9 8"/><line x1="12" y1="11" x2="12" y2="3"/></svg>
        Buka Web Notulensi ↗
      </a>
      <a href="<?= $notulPdfUrl ?>" target="_blank" class="btn btn-outline btn-sm" style="font-size:11.5px;font-weight:600;color:#065f46;border-color:#bbf7d0;background:#fff;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        Unduh PDF ↗
      </a>
      <?php endif; ?>
      <span style="font-size:11.5px;font-weight:600;color:#475569;background:#f1f5f9;padding:3px 10px;border-radius:4px;border:1px solid #e2e8f0;">
        <?= count($absensiList) ?> Hadir &nbsp;•&nbsp; <?= count($gambarList) ?> Foto
      </span>
    </div>
  </div>

  <div style="padding:20px;display:flex;flex-direction:column;gap:20px;">

    <!-- Informasi Umum Rapat -->
    <?php if ($hasRapatInfo): ?>
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:16px 18px;">
      <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:12px;">
        Informasi Pelaksanaan Rapat
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;">
        <?php if ($rapatTopik): ?>
        <div style="grid-column:1/-1;">
          <div style="font-size:11px;color:#64748b;font-weight:600;">Topik Rapat</div>
          <div style="font-size:14px;font-weight:700;color:#0f172a;margin-top:1px;"><?= htmlspecialchars($rapatTopik) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($rapatTanggal): ?>
        <div>
          <div style="font-size:11px;color:#64748b;font-weight:600;">Tanggal Pelaksanaan</div>
          <div style="font-size:13px;font-weight:700;color:#0f172a;margin-top:1px;"><?= date('d F Y', strtotime($rapatTanggal)) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($rapatTempat): ?>
        <div>
          <div style="font-size:11px;color:#64748b;font-weight:600;">Tempat / Ruang</div>
          <div style="font-size:13px;font-weight:700;color:#0f172a;margin-top:1px;"><?= htmlspecialchars($rapatTempat) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($rapatJamMulai): ?>
        <div>
          <div style="font-size:11px;color:#64748b;font-weight:600;">Waktu</div>
          <div style="font-size:13px;font-weight:700;color:#0f172a;margin-top:1px;"><?= substr($rapatJamMulai, 0, 5) ?><?= $rapatJamSelesai ? ' – ' . substr($rapatJamSelesai, 0, 5) : '' ?> WIB</div>
        </div>
        <?php endif; ?>
        <?php if ($rapatJenis): ?>
        <div>
          <div style="font-size:11px;color:#64748b;font-weight:600;">Jenis &amp; Kategori</div>
          <div style="font-size:13px;font-weight:700;color:#0f172a;margin-top:1px;"><?= htmlspecialchars($rapatJenis) ?><?= $rapatKategori ? ' (' . htmlspecialchars($rapatKategori) . ')' : '' ?></div>
        </div>
        <?php endif; ?>
        <?php if ($rapatTa): ?>
        <div>
          <div style="font-size:11px;color:#64748b;font-weight:600;">Tahun Ajaran / Semester</div>
          <div style="font-size:13px;font-weight:700;color:#0f172a;margin-top:1px;"><?= htmlspecialchars($rapatTa) ?><?= $rapatSemester ? ' — Semester ' . htmlspecialchars($rapatSemester) : '' ?></div>
        </div>
        <?php endif; ?>
      </div>
      <?php if ($rapatLampiran): ?>
      <div style="margin-top:12px;padding-top:10px;border-top:1px solid #e2e8f0;font-size:12.5px;">
        <span style="color:#64748b;font-weight:600;">Tautan Dokumen / Lampiran:</span>
        <a href="<?= htmlspecialchars($rapatLampiran) ?>" target="_blank" style="color:#1d4ed8;margin-left:6px;word-break:break-all;">
          <?= htmlspecialchars($rapatLampiran) ?> ↗
        </a>
      </div>
      <?php endif; ?>
      <?php if ($rapatKriteria): ?>
      <div style="margin-top:12px;padding-top:10px;border-top:1px solid #e2e8f0;">
        <div style="font-size:11px;color:#64748b;font-weight:600;margin-bottom:6px;">Kriteria SPMI yang Dievaluasi:</div>
        <div style="display:flex;flex-wrap:wrap;gap:6px;">
          <?php foreach (array_filter(array_map('trim', explode(',', $rapatKriteria))) as $kItem): ?>
          <span style="font-size:11.5px;font-weight:600;background:#fff;color:#334155;padding:2px 8px;border-radius:4px;border:1px solid #cbd5e1;"><?= htmlspecialchars($kItem) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Absensi & Notulensi -->
    <div style="display:grid;grid-template-columns:1fr 1.2fr;gap:20px;align-items:start;">
      
      <!-- Kiri: Absensi Peserta Rapat -->
      <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:16px;">
        <div style="font-size:11.5px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px;display:flex;align-items:center;justify-content:space-between;">
          <span>Daftar Hadir Peserta</span>
          <span style="font-size:11px;background:#e2e8f0;color:#334155;padding:1px 6px;border-radius:4px;font-weight:700;"><?= count($absensiList) ?> Orang</span>
        </div>
        <?php if (empty($absensiList)): ?>
        <div style="font-size:12.5px;color:#94a3b8;font-style:italic;">Belum ada catatan kehadiran</div>
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
        <div style="display:flex;flex-direction:column;gap:5px;max-height:360px;overflow-y:auto;padding-right:4px;">
          <?php foreach ($absensiList as $a): ?>
          <?php $st = strtoupper($a['status'] ?? 'HADIR'); ?>
          <div style="background:#fff;border:1px solid #e2e8f0;border-radius:6px;padding:7px 10px;display:flex;align-items:center;justify-content:space-between;font-size:12.5px;">
            <div style="font-weight:600;color:#0f172a;"><?= htmlspecialchars($a['nama']) ?></div>
            <span style="font-size:10.5px;font-weight:700;padding:2px 6px;border-radius:4px;
                         <?= $st === 'HADIR' ? 'background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0;' : ($st === 'IZIN' ? 'background:#fffbeb;color:#92400e;border:1px solid #fde68a;' : 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;') ?>">
              <?= htmlspecialchars($st) ?>
            </span>
          </div>
          <?php endforeach; ?>
        </div>
        <div style="margin-top:10px;padding-top:10px;border-top:1px solid #e2e8f0;display:flex;gap:12px;font-size:11.5px;font-weight:600;">
          <span style="color:#059669;"><?= $hadirCount ?> Hadir</span>
          <?php if ($izinCount > 0): ?><span style="color:#b45309;"><?= $izinCount ?> Izin</span><?php endif; ?>
          <?php if ($tidakCount > 0): ?><span style="color:#dc2626;"><?= $tidakCount ?> Tidak Hadir</span><?php endif; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Kanan: Notulensi Pembahasan Lengkap -->
      <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:16px;">
        <div style="font-size:11.5px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px;display:flex;align-items:center;justify-content:space-between;">
          <span>Notulensi Hasil Pembahasan &amp; Kesepakatan RTM</span>
          <?php if (!empty($evaluasi['notulensi'])): ?>
          <span style="font-size:11px;color:#64748b;"><?= mb_strlen($evaluasi['notulensi']) ?> karakter</span>
          <?php endif; ?>
        </div>
        <div style="font-size:13px;color:#1e293b;line-height:1.75;white-space:pre-wrap;background:#fff;padding:14px;border-radius:6px;border:1px solid #e2e8f0;min-height:120px;">
          <?= !empty($evaluasi['notulensi']) ? htmlspecialchars($evaluasi['notulensi']) : '<span style="color:#94a3b8;font-style:italic;">Belum ada catatan notulensi rapat yang diinput</span>' ?>
        </div>
      </div>

    </div>

    <!-- Foto Dokumentasi Fisik -->
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:16px;">
      <div style="font-size:11.5px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;">
        <span>Dokumentasi Foto Kegiatan RTM</span>
        <span style="font-size:11px;background:#e2e8f0;color:#334155;padding:1px 6px;border-radius:4px;font-weight:700;"><?= count($gambarList) ?> Foto</span>
      </div>
      <?php if (!empty($gambarList)): ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px;">
        <?php foreach ($gambarList as $img): ?>
        <?php
          $imgUrl = !empty($img['url']) ? $img['url'] : (str_starts_with($img['file_path'] ?? '', 'http') ? $img['file_path'] : BASE_URL . '/' . ltrim($img['file_path'] ?? '', '/'));
        ?>
        <div style="border-radius:6px;overflow:hidden;border:1px solid #cbd5e1;aspect-ratio:1;cursor:pointer;background:#fff;transition:border-color 0.15s, box-shadow 0.15s;"
             onmouseover="this.style.borderColor='#94a3b8';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'"
             onmouseout="this.style.borderColor='#cbd5e1';this.style.boxShadow='none'"
             onclick="previewImageModal('<?= htmlspecialchars($imgUrl) ?>')">
          <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Dokumentasi RTM" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <?php endforeach; ?>
      </div>
      <div style="margin-top:8px;font-size:11.5px;color:#64748b;">Klik foto untuk memperbesar tampilan resolusi penuh</div>
      <?php else: ?>
      <div style="font-size:12.5px;color:#94a3b8;font-style:italic;padding:14px 0;text-align:center;">Belum ada foto dokumentasi yang diupload</div>
      <?php endif; ?>
    </div>

  </div>
</div>

<!-- Modal Lightbox Preview Foto -->
<div id="modalImagePreview" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(15,23,42,0.85);z-index:9999;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(4px);" onclick="closeImageModal()">
  <div style="position:relative;max-width:90vw;max-height:90vh;display:flex;flex-direction:column;align-items:center;" onclick="event.stopPropagation()">
    <img id="modalPreviewImg" src="" alt="Preview Foto" style="max-width:100%;max-height:80vh;border-radius:8px;box-shadow:0 20px 50px rgba(0,0,0,0.5);object-fit:contain;background:#fff;">
    <div style="display:flex;gap:10px;margin-top:12px;">
      <a id="modalDownloadBtn" href="" target="_blank" class="btn btn-primary btn-sm" style="font-weight:600;display:flex;align-items:center;gap:6px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6"/><polyline points="15 8 12 11 9 8"/><line x1="12" y1="11" x2="12" y2="3"/></svg>
        Buka Resolusi Penuh ↗
      </a>
      <button type="button" onclick="closeImageModal()" class="btn btn-outline btn-sm" style="color:#fff;border-color:rgba(255,255,255,0.4);font-weight:600;">
        Tutup
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
    btnEl.style.background = '#0f172a';
    btnEl.style.color = '#fff';
    btnEl.style.borderColor = '#0f172a';
    btnEl.classList.add('active');
  }
  filterEvaluasi();
}

function filterEvaluasiStatus(status, btnEl) {
  currentEvStatus = status;
  document.querySelectorAll('.ev-status-filter-btn').forEach(function(btn) {
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
