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

<!-- Header Panel (Clean Academic / Institutional Design) -->
<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px 26px;margin-bottom:20px;box-shadow:0 1px 3px rgba(15,23,42,0.04);">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap;">
    <div style="flex:1;min-width:280px;">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#64748b;">
          Tahap 4 : Pengendalian &amp; RTL (PPEPP)
        </span>
        <span style="display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:2px 10px;border-radius:6px;<?= $pengendalian['status'] === 'final' ? 'background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0;' : 'background:#fffbeb;color:#92400e;border:1px solid #fde68a;' ?>">
          <span style="width:6px;height:6px;border-radius:50%;background:currentColor;"></span>
          <?= $pengendalian['status'] === 'final' ? 'Dokumen Final' : 'Draft' ?>
        </span>
      </div>

      <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 10px;line-height:1.3;letter-spacing:-0.3px;">
        <?= htmlspecialchars($pengendalian['judul']) ?>
      </h1>

      <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;font-size:12.5px;color:#64748b;">
        <span>Acuan Penetapan: <strong style="color:#1e293b;"><?= htmlspecialchars($penetapan['judul'] ?? '—') ?></strong></span>
        <span>•</span>
        <span>Tahun Ajaran: <strong style="color:#1e293b;"><?= htmlspecialchars($penetapan['ta_nama'] ?? '—') ?></strong></span>
        <span>•</span>
        <span>Dibuat: <strong style="color:#1e293b;"><?= date('d F Y', strtotime($pengendalian['created_at'])) ?></strong></span>
      </div>

      <?php if (!empty($pengendalian['deskripsi'])): ?>
      <p style="font-size:13px;color:#475569;margin-top:10px;line-height:1.6;max-width:850px;">
        <?= htmlspecialchars($pengendalian['deskripsi']) ?>
      </p>
      <?php endif; ?>
    </div>

    <!-- Actions -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <a href="<?= BASE_URL ?>/pengendalian/<?= $pgId ?>/edit" class="btn btn-primary" style="font-size:13px;font-weight:600;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
          <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Edit RTL &amp; Berkas Koreksi
      </a>

      <?php if ($pengendalian['status'] === 'draft'): ?>
      <form action="<?= BASE_URL ?>/pengendalian/<?= $pgId ?>/finalize" method="POST" style="display:inline;" onsubmit="return confirm('Finalisasi dokumen pengendalian ini?')">
        <button type="submit" class="btn btn-success" style="font-size:13px;font-weight:600;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
            <path d="M9 11l3 3L22 4"/>
            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
          </svg>
          Finalisasi
        </button>
      </form>
      <?php endif; ?>

      <a href="<?= BASE_URL ?>/ppepp<?= !empty($pengendalian['ppepp_project_id']) ? '/' . $pengendalian['ppepp_project_id'] : '' ?>" class="btn btn-outline" style="font-size:13px;color:#475569;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
        Project Library
      </a>
    </div>
  </div>
</div>

<!-- Metrics Overview -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px;margin-bottom:20px;">
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);">
    <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Standar</div>
    <div style="font-size:26px;font-weight:800;color:#0f172a;line-height:1.2;margin-top:4px;">
      <?= $totalStandar ?> <span style="font-size:13px;font-weight:500;color:#64748b;">Standar</span>
    </div>
  </div>

  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #10b981;">
    <div style="font-size:11.5px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.5px;">Standar Terpenuhi</div>
    <div style="font-size:26px;font-weight:800;color:#065f46;line-height:1.2;margin-top:4px;">
      <?= $cntTerpenuhi ?> <span style="font-size:13px;font-weight:500;color:#059669;">Standar</span>
    </div>
  </div>

  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #d97706;">
    <div style="font-size:11.5px;font-weight:700;color:#b45309;text-transform:uppercase;letter-spacing:0.5px;">Perlu RTL &amp; Koreksi</div>
    <div style="font-size:26px;font-weight:800;color:#92400e;line-height:1.2;margin-top:4px;">
      <?= $cntPerluRtl ?> <span style="font-size:13px;font-weight:500;color:#b45309;">Standar</span>
    </div>
  </div>

  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #64748b;">
    <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Berkas RTL Terlampir</div>
    <div style="font-size:26px;font-weight:800;color:#0f172a;line-height:1.2;margin-top:4px;">
      <?= count($berkasList) ?> <span style="font-size:13px;font-weight:500;color:#64748b;">Berkas</span>
    </div>
  </div>
</div>

<!-- Toolbar: Search & Interactive Filters -->
<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 18px;box-shadow:0 1px 3px rgba(15,23,42,0.03);margin-bottom:20px;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
    
    <!-- Search Bar -->
    <div style="flex:1;min-width:260px;position:relative;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" width="15" height="15" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="pengendalianSearchInput" onkeyup="filterPengendalian()" placeholder="Cari kode, nama standar, akar masalah, rencana tindak lanjut..."
             style="width:100%;padding:8px 12px 8px 34px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;outline:none;font-family:inherit;background:#f8fafc;"
             onfocus="this.style.background='#fff';this.style.borderColor='#1a237e';" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1';">
    </div>

    <!-- RTL Status Buttons -->
    <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
      <button type="button" class="pg-status-filter-btn" data-rtl="all" onclick="filterPengendalianRtl('all', this)"
              style="border:1px solid #1e293b;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:#1e293b;color:#fff;">
        Semua Standar (<?= $totalStandar ?>)
      </button>
      <button type="button" class="pg-status-filter-btn" data-rtl="perlu" onclick="filterPengendalianRtl('perlu', this)"
              style="border:1px solid #e2e8f0;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:#fff;color:#92400e;">
        Perlu RTL (<?= $cntPerluRtl ?>)
      </button>
      <button type="button" class="pg-status-filter-btn" data-rtl="terpenuhi" onclick="filterPengendalianRtl('terpenuhi', this)"
              style="border:1px solid #e2e8f0;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:#fff;color:#065f46;">
        Terpenuhi (<?= $cntTerpenuhi ?>)
      </button>
    </div>

    <!-- Info Counter -->
    <div id="pengendalianResultCount" style="font-size:12px;font-weight:600;color:#64748b;">
      Menampilkan <?= $totalStandar ?> standar
    </div>
  </div>

  <!-- Kriteria Filter Bar -->
  <?php if (count($kriteriaMap) > 1): ?>
  <div style="margin-top:12px;padding-top:10px;border-top:1px solid #f1f5f9;display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
    <span style="font-size:11.5px;font-weight:600;color:#64748b;margin-right:4px;">Filter Kriteria:</span>
    <button type="button" class="pg-kriteria-filter-btn active" data-kid="all" onclick="filterPengendalianKriteria('all', this)"
            style="border:1px solid #0f172a;padding:3px 10px;border-radius:4px;font-size:11.5px;font-weight:600;cursor:pointer;background:#0f172a;color:#fff;">
      Semua
    </button>
    <?php foreach ($kriteriaMap as $k): ?>
    <button type="button" class="pg-kriteria-filter-btn" data-kid="<?= $k['id'] ?>" onclick="filterPengendalianKriteria('<?= $k['id'] ?>', this)"
            style="border:1px solid #cbd5e1;padding:3px 10px;border-radius:4px;font-size:11.5px;font-weight:500;cursor:pointer;background:#fff;color:#475569;">
      <?= htmlspecialchars($k['kode']) ?>
    </button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- SECTION 1: RINCIAN STANDAR & RTL -->
<div style="margin-bottom:12px;">
  <h2 style="font-size:15px;font-weight:800;color:#0f172a;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 14px;">
    1. Rincian Pengendalian Standar &amp; Rencana Tindak Lanjut
  </h2>
</div>

<div id="pengendalianListContainer" style="display:flex;flex-direction:column;gap:14px;margin-bottom:28px;">
  <?php foreach ($details as $idx => $d): ?>
  <?php
  $stCapaian   = $d['status_capaian'] ?? 'belum_tercapai';
  $isTerpenuhi = ($stCapaian === 'tercapai');
  $rtlStatus   = $isTerpenuhi ? 'terpenuhi' : 'perlu';
  $kId         = (int)($d['kriteria_id'] ?? 0);
  $displayKode = !empty($d['kode']) ? $d['kode'] : (!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD-' . ($idx + 1));
  ?>

  <div class="pg-item-card" data-kid="<?= $kId ?>" data-rtl="<?= $rtlStatus ?>"
       style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;background:#fff;box-shadow:0 1px 3px rgba(15,23,42,0.03);">
    
    <?php if ($isTerpenuhi): ?>
    <!-- Standar Terpenuhi -->
    <div style="background:#f8fafc;padding:12px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <div style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:4px;background:#059669;color:#fff;">
          <?= htmlspecialchars($displayKode) ?>
        </span>
        <div>
          <span style="font-weight:700;font-size:14px;color:#0f172a;"><?= htmlspecialchars($d['kriteria_nama']) ?></span>
          <span style="font-size:12px;color:#64748b;margin-left:6px;">— Target: <?= htmlspecialchars($d['target_capaian'] ?: '—') ?></span>
        </div>
      </div>
      <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:4px;background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0;">
        Standar Terpenuhi (Tidak Perlu Tindakan Koreksi)
      </span>
    </div>

    <?php else: ?>
    <!-- Standar Memerlukan RTL -->
    <div style="background:#f8fafc;padding:12px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid #e2e8f0;flex-wrap:wrap;">
      <div style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:4px;background:#0f172a;color:#fff;">
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
        <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:4px;background:#fffbeb;color:#92400e;border:1px solid #fde68a;">
          <?= $stCapaian === 'sebagian' ? 'Tercapai Sebagian' : 'Belum Tercapai' ?>
        </span>
      </div>
    </div>

    <!-- Body Standar RTL -->
    <div style="padding:16px 18px;display:flex;flex-direction:column;gap:12px;">
      
      <!-- Target & Indikator -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px;">
        <div>
          <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:4px;">Target Acuan Penetapan</div>
          <div style="font-size:13px;color:#0f172a;line-height:1.6;font-weight:500;background:#f8fafc;padding:10px 12px;border-radius:6px;border:1px solid #e2e8f0;">
            <?= nl2br(htmlspecialchars($d['target_capaian'] ?: '—')) ?>
          </div>
        </div>

        <div>
          <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:4px;">Indikator Mutu</div>
          <div style="font-size:13px;color:#334155;line-height:1.6;background:#f8fafc;padding:10px 12px;border-radius:6px;border:1px solid #e2e8f0;">
            <?= nl2br(htmlspecialchars($d['indikator'] ?: '—')) ?>
          </div>
        </div>
      </div>

      <!-- Hasil Analisis Evaluasi -->
      <?php if (!empty($d['evaluasi_teks'])): ?>
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:6px;padding:10px 14px;font-size:13px;line-height:1.65;color:#334155;">
        <span style="font-weight:700;color:#0f172a;">Temuan Evaluasi Mutu:</span>
        <span style="margin-left:4px;"><?= nl2br(htmlspecialchars($d['evaluasi_teks'])) ?></span>
      </div>
      <?php endif; ?>

      <!-- Akar Masalah & Tindakan Koreksi (RTL) -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px;">
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:12px 14px;">
          <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:6px;">
            Identifikasi Akar Masalah
          </div>
          <div style="font-size:13px;color:#0f172a;line-height:1.65;white-space:pre-wrap;">
            <?= !empty($d['akar_masalah']) ? htmlspecialchars($d['akar_masalah']) : '<span style="color:#94a3b8;font-style:italic;">Belum dirumuskan</span>' ?>
          </div>
        </div>

        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:12px 14px;">
          <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:6px;">
            Rencana Tindakan Koreksi (RTL)
          </div>
          <div style="font-size:13px;color:#0f172a;line-height:1.65;white-space:pre-wrap;">
            <?= !empty($d['rencana_tindak_lanjut']) ? htmlspecialchars($d['rencana_tindak_lanjut']) : '<span style="color:#94a3b8;font-style:italic;">Belum dirumuskan</span>' ?>
          </div>
        </div>
      </div>

      <!-- Usulan Koreksi Standar untuk Siklus Berikutnya -->
      <?php if (!empty($d['koreksi_standar'])): ?>
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:6px;padding:10px 14px;">
        <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:4px;">
          Usulan Koreksi / Penyesuaian Standar untuk Siklus Berikutnya
        </div>
        <div style="font-size:13px;color:#0f172a;line-height:1.65;white-space:pre-wrap;">
          <?= htmlspecialchars($d['koreksi_standar']) ?>
        </div>
      </div>
      <?php endif; ?>

    </div>
    <?php endif; ?>

  </div>
  <?php endforeach; ?>
</div>

<!-- SECTION 2: BERKAS NOTULENSI RAPAT RTM & BERKAS KOREKSI STANDAR -->
<div style="margin-bottom:12px;">
  <h2 style="font-size:15px;font-weight:800;color:#0f172a;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 14px;">
    2. Berkas Pendukung &amp; Dokumen Tindakan Koreksi
  </h2>
</div>

<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,0.03);margin-bottom:30px;">
  <div style="background:#f8fafc;padding:14px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <div>
      <div style="font-size:14.5px;font-weight:700;color:#0f172a;">Berkas Notulensi Rapat &amp; Koreksi Standar</div>
      <div style="font-size:12px;color:#64748b;margin-top:2px;">Dokumen bukti fisik kesepakatan tindakan pengendalian</div>
    </div>
    <span style="font-size:11.5px;font-weight:600;color:#475569;background:#f1f5f9;padding:3px 10px;border-radius:4px;border:1px solid #e2e8f0;">
      <?= count($berkasList) ?> Berkas
    </span>
  </div>

  <div style="padding:18px 20px;">
    <?php if (empty($berkasList)): ?>
    <div style="padding:20px;text-align:center;color:#94a3b8;font-style:italic;font-size:13px;">
      Belum ada berkas pendukung tindakan koreksi yang diunggah.
    </div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:8px;">
      <?php foreach ($berkasList as $b): ?>
      <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
          <div style="font-weight:700;font-size:13px;color:#0f172a;"><?= htmlspecialchars($b['judul'] ?? $b['file_name']) ?></div>
          <?php if (!empty($b['keterangan'])): ?>
          <div style="font-size:12px;color:#64748b;margin-top:2px;"><?= htmlspecialchars($b['keterangan']) ?></div>
          <?php endif; ?>
        </div>
        <a href="<?= htmlspecialchars($b['url'] ?? BASE_URL . '/' . $b['file_path']) ?>" target="_blank"
           class="btn btn-outline btn-sm" style="font-size:11.5px;font-weight:600;color:#0f172a;border-color:#cbd5e1;background:#fff;">
          Unduh Berkas ↗
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
    btnEl.style.background = '#0f172a';
    btnEl.style.color = '#fff';
    btnEl.style.borderColor = '#0f172a';
    btnEl.classList.add('active');
  }
  filterPengendalian();
}

function filterPengendalianRtl(rtlStatus, btnEl) {
  currentPgRtl = rtlStatus;
  document.querySelectorAll('.pg-status-filter-btn').forEach(function(btn) {
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
  filterPengendalian();
}
</script>
