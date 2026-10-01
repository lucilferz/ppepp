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

    if (!empty($d['indikator'])) {
        $indLines = array_filter(explode("\n", $d['indikator']), fn($l) => trim($l) !== '');
        $totalIndikatorCount += max(1, count($indLines));
    }
}
?>

<!-- Header Panel (Clean Academic / Institutional Design) -->
<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px 26px;margin-bottom:20px;box-shadow:0 1px 3px rgba(15,23,42,0.04);">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap;">
    <div style="flex:1;min-width:280px;">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#64748b;">
          Tahap 1 : Penetapan Standar Mutu SPMI
        </span>
        <span style="display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:2px 10px;border-radius:6px;<?= $penetapan['status'] === 'final' ? 'background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0;' : 'background:#fffbeb;color:#92400e;border:1px solid #fde68a;' ?>">
          <span style="width:6px;height:6px;border-radius:50%;background:currentColor;"></span>
          <?= $penetapan['status'] === 'final' ? 'Dokumen Final' : 'Draft' ?>
        </span>
      </div>

      <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 10px;line-height:1.3;letter-spacing:-0.3px;">
        <?= htmlspecialchars($penetapan['judul']) ?>
      </h1>

      <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;font-size:12.5px;color:#64748b;">
        <span>Tahun Ajaran: <strong style="color:#1e293b;"><?= htmlspecialchars($penetapan['tahun_ajaran']['nama'] ?? '—') ?></strong></span>
        <span>•</span>
        <span>Dibuat: <strong style="color:#1e293b;"><?= date('d F Y', strtotime($penetapan['created_at'])) ?></strong></span>
        <?php if (!empty($berkasSkList)): ?>
        <span>•</span>
        <span>SK Pengesahan: <strong style="color:#1e293b;"><?= count($berkasSkList) ?> Berkas</strong></span>
        <?php endif; ?>
      </div>

      <?php if (!empty($penetapan['deskripsi'])): ?>
      <p style="font-size:13px;color:#475569;margin-top:10px;line-height:1.6;max-width:850px;">
        <?= htmlspecialchars($penetapan['deskripsi']) ?>
      </p>
      <?php endif; ?>
    </div>

    <!-- Actions -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <a href="<?= BASE_URL ?>/penetapan/download-template-excel?format=xlsx" class="btn btn-outline" style="font-size:13px;color:#475569;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Unduh Excel
      </a>

      <a href="<?= BASE_URL ?>/penetapan/<?= $penetapan['id'] ?>/edit-step2" class="btn btn-primary" style="font-size:13px;font-weight:600;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
          <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        <?= $penetapan['status'] === 'final' ? 'Kelola / Edit Standar' : 'Lanjutkan Isi Standar' ?>
      </a>

      <?php if ($penetapan['status'] === 'draft'): ?>
      <form action="<?= BASE_URL ?>/penetapan/<?= $penetapan['id'] ?>/finalize" method="POST" style="display:inline;" onsubmit="return confirm('Finalisasi penetapan standar ini? Dokumen yang difinalisasi siap dilanjutkan ke tahap Pelaksanaan.')">
        <button type="submit" class="btn btn-success" style="font-size:13px;font-weight:600;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
            <path d="M9 11l3 3L22 4"/>
            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
          </svg>
          Finalisasi
        </button>
      </form>
      <?php endif; ?>

      <a href="<?= BASE_URL ?>/ppepp<?= !empty($penetapan['ppepp_project_id']) ? '/' . $penetapan['ppepp_project_id'] : '' ?>" class="btn btn-outline" style="font-size:13px;color:#475569;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
        Project Library
      </a>
    </div>
  </div>
</div>

<!-- Metrics Overview -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:20px;">
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);">
    <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Standar</div>
    <div style="font-size:26px;font-weight:800;color:#0f172a;line-height:1.2;margin-top:4px;">
      <?= count($details) ?> <span style="font-size:13px;font-weight:500;color:#64748b;">Standar</span>
    </div>
  </div>

  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #1a237e;">
    <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Kriteria SPMI Tercover</div>
    <div style="font-size:26px;font-weight:800;color:#0f172a;line-height:1.2;margin-top:4px;">
      <?= count($kriteriaMap) ?> <span style="font-size:13px;font-weight:500;color:#64748b;">Kriteria</span>
    </div>
  </div>

  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #059669;">
    <div style="font-size:11.5px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.5px;">Indikator Capaian</div>
    <div style="font-size:26px;font-weight:800;color:#065f46;line-height:1.2;margin-top:4px;">
      <?= $totalIndikatorCount ?> <span style="font-size:13px;font-weight:500;color:#059669;">Poin</span>
    </div>
  </div>

  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #d97706;">
    <div style="font-size:11.5px;font-weight:700;color:#b45309;text-transform:uppercase;letter-spacing:0.5px;">Surat Keputusan (SK)</div>
    <div style="font-size:26px;font-weight:800;color:#92400e;line-height:1.2;margin-top:4px;">
      <?= count($berkasSkList) ?> <span style="font-size:13px;font-weight:500;color:#b45309;">Berkas</span>
    </div>
  </div>
</div>

<!-- Controls: Filter Kriteria, Search & View Switcher -->
<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 18px;box-shadow:0 1px 3px rgba(15,23,42,0.03);margin-bottom:20px;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
    
    <!-- Search Bar -->
    <div style="flex:1;min-width:260px;position:relative;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" width="15" height="15" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="standardSearchInput" onkeyup="filterStandards()" placeholder="Cari kode, nama standar, aturan, indikator..."
             style="width:100%;padding:8px 12px 8px 34px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;outline:none;font-family:inherit;background:#f8fafc;"
             onfocus="this.style.background='#fff';this.style.borderColor='#1a237e';" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1';">
    </div>

    <!-- View Mode Switcher -->
    <div style="display:flex;align-items:center;gap:4px;background:#f1f5f9;padding:3px;border-radius:6px;border:1px solid #e2e8f0;">
      <button type="button" id="btnViewCard" onclick="switchStandardView('card')"
              style="border:none;background:#fff;color:#0f172a;font-weight:600;font-size:12px;padding:5px 12px;border-radius:4px;cursor:pointer;display:flex;align-items:center;gap:6px;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Tampilan Detail
      </button>
      <button type="button" id="btnViewTable" onclick="switchStandardView('table')"
              style="border:none;background:transparent;color:#64748b;font-weight:500;font-size:12px;padding:5px 12px;border-radius:4px;cursor:pointer;display:flex;align-items:center;gap:6px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        Tampilan Tabel
      </button>
    </div>
  </div>

  <!-- Kriteria Filter Bar -->
  <?php if (count($kriteriaMap) > 1): ?>
  <div style="margin-top:12px;padding-top:10px;border-top:1px solid #f1f5f9;display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
    <span style="font-size:11.5px;font-weight:600;color:#64748b;margin-right:4px;">Filter Kriteria:</span>
    <button type="button" class="kriteria-filter-btn active" data-kid="all" onclick="filterByKriteria('all', this)"
            style="border:1px solid #0f172a;padding:3px 10px;border-radius:4px;font-size:11.5px;font-weight:600;cursor:pointer;background:#0f172a;color:#fff;">
      Semua (<?= count($details) ?>)
    </button>
    <?php foreach ($kriteriaMap as $k): ?>
    <button type="button" class="kriteria-filter-btn" data-kid="<?= $k['id'] ?>" onclick="filterByKriteria('<?= $k['id'] ?>', this)"
            style="border:1px solid #cbd5e1;padding:3px 10px;border-radius:4px;font-size:11.5px;font-weight:500;cursor:pointer;background:#fff;color:#475569;">
      <?= htmlspecialchars($k['kode']) ?> (<?= $k['count'] ?>)
    </button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ============================================================
     CONTAINER 1: CARD VIEW (DETAIL BERSIH)
     ============================================================ -->
<div id="standardCardContainer" style="display:flex;flex-direction:column;gap:14px;">
  <?php if (empty($details)): ?>
  <div class="card" style="padding:48px 24px;text-align:center;border-radius:10px;border:1px dashed #cbd5e1;background:#fff;">
    <div style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:6px;">Belum Ada Standar yang Ditetapkan</div>
    <p style="font-size:13px;color:#64748b;margin-bottom:16px;">Silakan masukkan standar atau unggah dokumen Excel untuk memulai.</p>
    <a href="<?= BASE_URL ?>/penetapan/<?= $penetapan['id'] ?>/edit-step2" class="btn btn-primary btn-sm">+ Isi Standar / Import Excel</a>
  </div>
  <?php else: ?>
  <?php foreach ($details as $idx => $d): ?>
  <?php
  $kId  = (string)($d['kriteria_id'] ?? '');
  $kode = !empty($d['kode']) ? $d['kode'] : (!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD-' . ($idx + 1));
  ?>
  <div class="standard-item-card" data-kid="<?= $kId ?>" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 1px 3px rgba(15,23,42,0.03);overflow:hidden;">
    
    <!-- Card Header -->
    <div style="background:#f8fafc;padding:12px 18px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
      <div style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:4px;background:#0f172a;color:#fff;">
          <?= htmlspecialchars($kode) ?>
        </span>
        <span style="font-size:13.5px;font-weight:700;color:#0f172a;">
          <?= htmlspecialchars($d['kriteria_nama']) ?>
        </span>
      </div>
      <div style="font-size:11.5px;color:#64748b;">
        Standar #<?= $idx + 1 ?>
      </div>
    </div>

    <!-- Card Content -->
    <div style="padding:16px 18px;display:flex;flex-direction:column;gap:12px;">
      
      <!-- Pernyataan Standar (Target) -->
      <div>
        <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:4px;">
          Pernyataan Standar (Target Capaian)
        </div>
        <div style="font-size:13.5px;color:#0f172a;line-height:1.65;font-weight:500;">
          <?= !empty($d['target_capaian']) ? nl2br(htmlspecialchars($d['target_capaian'])) : '<span style="color:#94a3b8;font-style:italic;">Belum ditentukan</span>' ?>
        </div>
      </div>

      <!-- Indikator Ketercapaian -->
      <div style="background:#f8fafc;border-radius:6px;padding:12px 14px;border:1px solid #e2e8f0;">
        <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:6px;">
          Indikator Ketercapaian Kuantitatif / Kualitatif
        </div>
        <div style="font-size:13px;color:#334155;line-height:1.65;">
          <?php if (!empty($d['indikator'])): ?>
            <?php $indLines = explode("\n", trim($d['indikator'])); ?>
            <ul style="margin:0;padding-left:18px;display:flex;flex-direction:column;gap:3px;">
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
      <div style="font-size:12px;color:#64748b;padding-top:2px;">
        <span style="font-weight:600;color:#475569;">Dasar Hukum / Acuan:</span>
        <span style="margin-left:4px;"><?= htmlspecialchars($d['strategi']) ?></span>
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
<div id="standardTableContainer" style="display:none;background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,0.03);">
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;" id="standardsTable">
      <thead>
        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:700;text-align:left;">
          <th style="padding:10px 14px;width:70px;">Kode</th>
          <th style="padding:10px 14px;width:160px;">Kriteria</th>
          <th style="padding:10px 14px;min-width:260px;">Pernyataan Standar (Target)</th>
          <th style="padding:10px 14px;min-width:240px;">Indikator Ketercapaian</th>
          <th style="padding:10px 14px;min-width:180px;">Dasar Hukum / Acuan</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($details as $idx => $d): ?>
        <?php
        $kId  = (string)($d['kriteria_id'] ?? '');
        $kode = !empty($d['kode']) ? $d['kode'] : (!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD-' . ($idx + 1));
        ?>
        <tr class="standard-table-row" data-kid="<?= $kId ?>" style="border-bottom:1px solid #f1f5f9;">
          <td style="padding:10px 14px;vertical-align:top;">
            <span style="font-weight:700;color:#0f172a;font-size:11.5px;background:#f1f5f9;padding:2px 6px;border-radius:4px;">
              <?= htmlspecialchars($kode) ?>
            </span>
          </td>
          <td style="padding:10px 14px;font-weight:600;color:#1e293b;vertical-align:top;">
            <?= htmlspecialchars($d['kriteria_nama']) ?>
          </td>
          <td style="padding:10px 14px;color:#0f172a;vertical-align:top;line-height:1.6;">
            <?= nl2br(htmlspecialchars($d['target_capaian'] ?? '—')) ?>
          </td>
          <td style="padding:10px 14px;color:#334155;vertical-align:top;line-height:1.6;">
            <?= nl2br(htmlspecialchars($d['indikator'] ?? '—')) ?>
          </td>
          <td style="padding:10px 14px;color:#64748b;vertical-align:top;line-height:1.5;font-size:12px;">
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
<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,0.03);margin-top:28px;">
  <div style="background:#f8fafc;padding:14px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <div>
      <div style="font-size:14.5px;font-weight:700;color:#0f172a;">Berkas Surat Keputusan (SK) Pengesahan Standar</div>
      <div style="font-size:12px;color:#64748b;margin-top:2px;">Dokumen legalitas pemberlakuan standar mutu</div>
    </div>
    <span style="font-size:11.5px;font-weight:600;color:#475569;background:#f1f5f9;padding:3px 10px;border-radius:4px;border:1px solid #e2e8f0;">
      <?= count($berkasSkList) ?> berkas terlampir
    </span>
  </div>
  <div style="padding:18px 20px;">
    <?php if (empty($berkasSkList)): ?>
    <div style="text-align:center;padding:20px 0;color:#64748b;font-size:13px;">
      Belum ada berkas SK yang diunggah untuk dokumen penetapan ini.
      <a href="<?= BASE_URL ?>/penetapan/<?= $penetapan['id'] ?>/edit-step2" style="color:#1d4ed8;font-weight:600;margin-left:6px;">Upload SK Pengesahan Sekarang →</a>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:700;text-align:left;">
            <th style="padding:9px 12px;width:35px;">#</th>
            <th style="padding:9px 12px;min-width:160px;">Nomor SK</th>
            <th style="padding:9px 12px;min-width:220px;">Perihal / Judul SK</th>
            <th style="padding:9px 12px;width:130px;">Tanggal SK</th>
            <th style="padding:9px 12px;width:100px;text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($berkasSkList as $bIdx => $sk): ?>
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:9px 12px;color:#94a3b8;"><?= $bIdx + 1 ?></td>
            <td style="padding:9px 12px;font-weight:700;color:#0f172a;"><?= htmlspecialchars($sk['nomor_sk'] ?? '—') ?></td>
            <td style="padding:9px 12px;color:#334155;"><?= htmlspecialchars($sk['judul_sk'] ?? '—') ?></td>
            <td style="padding:9px 12px;color:#64748b;"><?= htmlspecialchars($sk['tanggal_sk'] ?? '—') ?></td>
            <td style="padding:9px 12px;text-align:right;">
              <a href="<?= htmlspecialchars($sk['url'] ?? BASE_URL . '/' . $sk['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline"
                 style="font-size:11.5px;font-weight:600;color:#0f172a;border-color:#cbd5e1;background:#fff;">
                Unduh ↗
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
      btnTable.style.color = '#0f172a';
      btnTable.style.boxShadow = '0 1px 2px rgba(0,0,0,0.05)';
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
      btnCard.style.color = '#0f172a';
      btnCard.style.boxShadow = '0 1px 2px rgba(0,0,0,0.05)';
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
    b.style.border = '1px solid #cbd5e1';
  });

  if (btnEl) {
    btnEl.style.background = '#0f172a';
    btnEl.style.color = '#fff';
    btnEl.style.border = '1px solid #0f172a';
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
