<?php
$pageTitle = 'Hasil Peningkatan Standar — ' . htmlspecialchars($peningkatan['judul']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Peningkatan', 'url' => BASE_URL . '/peningkatan'],
  ['label' => htmlspecialchars($peningkatan['judul'])],
];

$penetapan = $peningkatan['penetapan'] ?? [];
$details = $peningkatan['details'] ?? [];
$pkId = $peningkatan['id'];
$berkasList = $peningkatan['berkas_list'] ?? [];
$tahunAjarans = $tahunAjarans ?? [];

$standarTerpenuhi = [];
$standarBelum = [];

foreach ($details as $d) {
  $st = $d['status_capaian'] ?? 'belum_tercapai';
  if ($st === 'tercapai') {
    $standarTerpenuhi[] = $d;
  } else {
    $standarBelum[] = $d;
  }
}

$cntTerpenuhi = count($standarTerpenuhi);
$cntBelum = count($standarBelum);
$totalStandar = count($details);
$isFinal = ($peningkatan['status'] === 'final');

$kriteriaMap = [];
foreach ($details as $d) {
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

<style>
.pk-section-title {
  font-size: 17px;
  font-weight: 900;
  color: #0f172a;
  margin: 0 0 4px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.pk-section-desc {
  font-size: 13.5px;
  color: #64748b;
  margin: 0;
  line-height: 1.5;
}
.pk-stat-card {
  background: #fff;
  border-radius: 16px;
  padding: 20px 24px;
  box-shadow: 0 2px 14px rgba(0,0,0,0.04);
  text-align: center;
}
.pk-stat-number {
  font-size: 38px;
  font-weight: 900;
  line-height: 1;
  margin: 8px 0 4px;
}
.pk-stat-label {
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.pk-standar-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 3px 14px rgba(0,0,0,0.05);
  border: 1.5px solid #e2e8f0;
  margin-bottom: 16px;
  transition: box-shadow 0.2s;
}
.pk-standar-card:hover {
  box-shadow: 0 6px 24px rgba(0,0,0,0.09);
}
.pk-standar-header {
  padding: 14px 20px;
  background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
  border-bottom: 1.5px solid #bbf7d0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}
.pk-kode-badge {
  background: #059669;
  color: #fff;
  font-weight: 900;
  font-size: 13px;
  padding: 5px 14px;
  border-radius: 8px;
  letter-spacing: 0.3px;
}
.pk-box {
  border-radius: 12px;
  padding: 16px 18px;
}
.pk-box-label {
  font-size: 11.5px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}
.pk-box-value {
  font-size: 14.5px;
  line-height: 1.65;
  font-weight: 500;
}
.pk-action-btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-weight: 800;
  font-size: 13px;
  padding: 10px 20px;
  border-radius: 10px;
  cursor: pointer;
  border: none;
  text-decoration: none;
  transition: opacity 0.15s, transform 0.1s;
}
.pk-action-btn:hover { opacity: 0.88; transform: translateY(-1px); }
</style>

<!-- ============================================================
     HEADER HALAMAN
     ============================================================ -->
<div style="background:linear-gradient(135deg,#0f172a 0%,#064e3b 50%,#065f46 100%);border-radius:18px;padding:28px 32px;margin-bottom:26px;color:#fff;display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap;box-shadow:0 10px 30px rgba(5,150,105,0.2);">
  <div style="flex:1;min-width:280px;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;flex-wrap:wrap;">
      <span style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1.2px;color:#6ee7b7;">
        Peningkatan Standar Mutu SPMI — Tahap P ke-3 (PPEPP)
      </span>
      <span style="background:<?= $isFinal ? '#059669' : '#d97706' ?>;color:#fff;font-size:12px;font-weight:800;padding:3px 12px;border-radius:20px;">
        <?= $isFinal ? '✓ Final' : '⏳ Draft' ?>
      </span>
    </div>
    <h2 style="font-size:24px;font-weight:900;color:#fff;margin:0 0 8px;line-height:1.3;">
      <?= htmlspecialchars($peningkatan['judul']) ?>
    </h2>
    <p style="font-size:13.5px;color:rgba(255,255,255,0.75);margin:0;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
      <span>📋 Penetapan Acuan: <strong><?= htmlspecialchars($penetapan['judul'] ?? '—') ?></strong></span>
      <span style="opacity:0.4;">|</span>
      <span>📅 Tahun Ajaran: <strong><?= htmlspecialchars($penetapan['ta_nama'] ?? '—') ?></strong></span>
      <span style="opacity:0.4;">|</span>
      <span>🗓 Dibuat: <?= date('d M Y', strtotime($peningkatan['created_at'])) ?></span>
    </p>
  </div>

  <div style="display:flex;gap:8px;align-items:flex-start;flex-wrap:wrap;flex-shrink:0;">
    <button type="button" onclick="openExportModal()" class="pk-action-btn"
      style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;box-shadow:0 4px 14px rgba(16,185,129,0.35);">
      🚀 Buat Penetapan Baru
    </button>
    <a href="<?= BASE_URL ?>/peningkatan/<?= $pkId ?>/edit" class="pk-action-btn"
      style="background:rgba(255,255,255,0.12);border:1.5px solid rgba(255,255,255,0.25);color:#fff;">
      ✏️ Edit
    </a>
    <?php if (!$isFinal): ?>
    <form action="<?= BASE_URL ?>/peningkatan/<?= $pkId ?>/finalize" method="POST" style="margin:0;">
      <button type="submit" class="pk-action-btn"
        style="background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:#fff;">
        ✓ Finalisasi
      </button>
    </form>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>/ppepp<?= !empty($peningkatan['ppepp_project_id']) ? '/' . $peningkatan['ppepp_project_id'] : '' ?>" class="pk-action-btn"
      style="background:rgba(255,255,255,0.07);border:1.5px solid rgba(255,255,255,0.2);color:rgba(255,255,255,0.8);font-size:12.5px;">
      ← Kembali ke Project Library
    </a>
  </div>
</div>

<!-- ============================================================
     KARTU STATISTIK RINGKAS
     ============================================================ -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:28px;">
  <div class="pk-stat-card" style="border:1.5px solid #e2e8f0;">
    <div class="pk-stat-label" style="color:#64748b;">📊 Total Standar</div>
    <div class="pk-stat-number" style="color:#1e293b;"><?= $totalStandar ?></div>
    <div style="font-size:12px;color:#94a3b8;">Dari siklus berjalan</div>
  </div>

  <div class="pk-stat-card" style="border:1.5px solid #a7f3d0;background:linear-gradient(135deg,#f0fdf4,#fff);">
    <div class="pk-stat-label" style="color:#059669;">✅ Ditingkatkan</div>
    <div class="pk-stat-number" style="color:#065f46;"><?= $cntTerpenuhi ?></div>
    <div style="font-size:12px;color:#059669;">Target baru tahun depan</div>
  </div>

  <div class="pk-stat-card" style="border:1.5px solid #fed7aa;background:linear-gradient(135deg,#fff7ed,#fff);">
    <div class="pk-stat-label" style="color:#ea580c;">⚠️ Dari Pengendalian</div>
    <div class="pk-stat-number" style="color:#9a3412;"><?= $cntBelum ?></div>
    <div style="font-size:12px;color:#c2410c;">Tetap masuk (dapat diedit)</div>
  </div>

  <div class="pk-stat-card" style="border:1.5px solid <?= $isFinal ? '#a7f3d0' : '#fde68a' ?>;background:linear-gradient(135deg,<?= $isFinal ? '#f0fdf4' : '#fffbeb' ?>,#fff);">
    <div class="pk-stat-label" style="color:<?= $isFinal ? '#059669' : '#d97706' ?>;">📌 Status Dokumen</div>
    <div style="font-size:22px;font-weight:900;color:<?= $isFinal ? '#065f46' : '#92400e' ?>;margin:8px 0 4px;">
      <?= $isFinal ? '✓ Final' : '⏳ Draft' ?>
    </div>
    <div style="font-size:12px;color:<?= $isFinal ? '#059669' : '#d97706' ?>;">
      <?= $isFinal ? 'Dokumen telah disahkan' : 'Belum difinalisasi' ?>
    </div>
  </div>
</div>

<!-- ============================================================
     TOOLBAR PENCARIAN & FILTER KRITERIA
     ============================================================ -->
<div class="card mb-4" style="border-radius:14px;border:1.5px solid #e2e8f0;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.02);margin-bottom:24px;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
    
    <!-- Search Bar -->
    <div style="flex:1;min-width:280px;position:relative;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" width="16" height="16" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="peningkatanSearchInput" onkeyup="filterPeningkatan()" placeholder="Cari kode, nama kriteria, target baru, alasan peningkatan..."
             style="width:100%;padding:9px 12px 9px 36px;border:1.5px solid #cbd5e1;border-radius:10px;font-size:13px;outline:none;font-family:inherit;background:#f8fafc;"
             onfocus="this.style.background='#fff';this.style.borderColor='#059669';" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1';">
    </div>

    <!-- Info Counter -->
    <div id="peningkatanResultCount" style="font-size:12.5px;font-weight:700;color:#64748b;">
      Menampilkan <?= $totalStandar ?> standar
    </div>
  </div>

  <?php if (count($kriteriaMap) > 1): ?>
  <div style="margin-top:14px;padding-top:12px;border-top:1px solid #f1f5f9;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <span style="font-size:11.5px;font-weight:700;color:#64748b;">Kriteria:</span>
    <button type="button" class="pk-kriteria-filter-btn active" data-kid="all" onclick="filterPeningkatanKriteria('all', this)"
            style="border:none;padding:4px 12px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;background:#059669;color:#fff;">
      Semua (<?= $totalStandar ?>)
    </button>
    <?php foreach ($kriteriaMap as $k): ?>
    <button type="button" class="pk-kriteria-filter-btn" data-kid="<?= $k['id'] ?>" onclick="filterPeningkatanKriteria('<?= $k['id'] ?>', this)"
            style="border:1.5px solid #cbd5e1;padding:4px 12px;border-radius:20px;font-size:11.5px;font-weight:600;cursor:pointer;background:#fff;color:#475569;">
      <?= htmlspecialchars($k['kode']) ?> (<?= $k['count'] ?>)
    </button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ============================================================
     BAGIAN 1: STANDAR YANG DITINGKATKAN
     ============================================================ -->
<div style="background:#fff;border:1.5px solid #a7f3d0;border-radius:18px;margin-bottom:24px;overflow:hidden;box-shadow:0 4px 18px rgba(5,150,105,0.05);">
  <div style="background:linear-gradient(135deg,#f0fdf4,#ecfdf5);padding:18px 24px;border-bottom:1.5px solid #a7f3d0;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
      <div class="pk-section-title">
        <span style="width:30px;height:30px;background:#059669;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">📈</span>
        Standar yang Ditingkatkan Mutunya
      </div>
      <p class="pk-section-desc">Standar-standar di bawah ini sudah terpenuhi dan akan <strong>masuk ke Penetapan tahun depan dengan target yang lebih tinggi</strong>.</p>
    </div>
    <span style="background:#059669;color:#fff;font-weight:800;font-size:13px;padding:6px 16px;border-radius:20px;white-space:nowrap;">
      <?= $cntTerpenuhi ?> Standar
    </span>
  </div>

  <div style="padding:22px;">
    <?php if (empty($standarTerpenuhi)): ?>
      <div style="padding:32px;text-align:center;color:#94a3b8;">
        <div style="font-size:32px;margin-bottom:10px;">📭</div>
        <div style="font-size:14px;font-style:italic;">Belum ada standar yang ditingkatkan pada siklus ini.</div>
      </div>
    <?php else: ?>
      <?php foreach ($standarTerpenuhi as $idx => $d): ?>
      <div class="pk-standar-card pk-filterable-item" data-kid="<?= (int)($d['kriteria_id'] ?? 0) ?>">
        <!-- Header Standar -->
        <div class="pk-standar-header">
          <div style="display:flex;align-items:center;gap:12px;">
            <div class="pk-kode-badge"><?= htmlspecialchars(!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : ('STD-' . ($idx+1))) ?></div>
            <div>
              <div style="font-size:15.5px;font-weight:800;color:#065f46;"><?= htmlspecialchars($d['kriteria_nama']) ?></div>
              <?php if (!empty($d['kriteria_deskripsi'])): ?>
              <div style="font-size:12px;color:#047857;margin-top:2px;"><?= htmlspecialchars($d['kriteria_deskripsi']) ?></div>
              <?php endif; ?>
            </div>
          </div>
          <?php if (!empty($d['nilai_kenaikan'])): ?>
          <span style="background:#d1fae5;color:#065f46;font-size:13px;font-weight:800;padding:5px 14px;border-radius:20px;white-space:nowrap;">
            📈 <?= htmlspecialchars($d['nilai_kenaikan']) ?>
          </span>
          <?php endif; ?>
        </div>

        <!-- Body Standar -->
        <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">

          <!-- Perbandingan Target Lama vs Baru -->
          <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px;">
            <!-- Target Lama -->
            <div class="pk-box" style="background:#f8fafc;border:1.5px solid #e2e8f0;">
              <div class="pk-box-label" style="color:#64748b;">🎯 Target Standar (Sebelumnya)</div>
              <div class="pk-box-value" style="color:#334155;"><?= htmlspecialchars($d['target_capaian'] ?: '—') ?></div>
              <?php if (!empty($d['indikator'])): ?>
              <div style="margin-top:8px;font-size:13px;color:#64748b;border-top:1px solid #e2e8f0;padding-top:8px;">
                <strong>Indikator Lama:</strong> <?= htmlspecialchars($d['indikator']) ?>
              </div>
              <?php endif; ?>
            </div>

            <!-- Target Baru (Ditingkatkan) -->
            <div class="pk-box" style="background:#ecfdf5;border:2px solid #6ee7b7;">
              <div class="pk-box-label" style="color:#065f46;">🚀 Target Baru (Setelah Ditingkatkan)</div>
              <div class="pk-box-value" style="color:#064e3b;font-weight:700;">
                <?= !empty($d['target_baru']) ? htmlspecialchars($d['target_baru']) : '<span style="color:#94a3b8;font-style:italic;font-weight:400;">Belum dirumuskan</span>' ?>
              </div>
              <?php if (!empty($d['indikator_baru'])): ?>
              <div style="margin-top:8px;font-size:13px;color:#047857;border-top:1px solid #a7f3d0;padding-top:8px;">
                <strong>Indikator Baru:</strong> <?= htmlspecialchars($d['indikator_baru']) ?>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Alasan & Strategi -->
          <?php if (!empty($d['alasan_peningkatan']) || !empty($d['strategi_baru'])): ?>
          <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px;">
            <?php if (!empty($d['alasan_peningkatan'])): ?>
            <div style="background:#fafafa;border:1.5px solid #e2e8f0;border-radius:12px;padding:14px 16px;">
              <div style="font-size:12px;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.4px;">🔍 Alasan Peningkatan</div>
              <div style="font-size:13.5px;color:#334155;line-height:1.65;"><?= nl2br(htmlspecialchars($d['alasan_peningkatan'])) ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($d['strategi_baru'])): ?>
            <div style="background:#fafafa;border:1.5px solid #e2e8f0;border-radius:12px;padding:14px 16px;">
              <div style="font-size:12px;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.4px;">📋 Program / Strategi Baru</div>
              <div style="font-size:13.5px;color:#334155;line-height:1.65;"><?= nl2br(htmlspecialchars($d['strategi_baru'])) ?></div>
            </div>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- ============================================================
     BAGIAN 2: STANDAR DARI PENGENDALIAN
     ============================================================ -->
<div style="background:#fff;border:1.5px solid #fed7aa;border-radius:18px;margin-bottom:24px;overflow:hidden;box-shadow:0 4px 18px rgba(234,88,12,0.04);">
  <div style="background:linear-gradient(135deg,#fff7ed,#fff);padding:18px 24px;border-bottom:1.5px solid #fed7aa;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
      <div class="pk-section-title">
        <span style="width:30px;height:30px;background:#ea580c;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">⚠️</span>
        Standar Belum Terpenuhi (Dari Pengendalian)
      </div>
      <p class="pk-section-desc">Standar-standar ini <strong>belum tercapai</strong> dan masih dalam proses pengendalian. Saat membuat Penetapan tahun depan, standar ini <strong>tetap diikutsertakan dengan target yang sama</strong> (masih bisa diedit nanti).</p>
    </div>
    <span style="background:#fed7aa;color:#9a3412;font-weight:800;font-size:13px;padding:6px 16px;border-radius:20px;white-space:nowrap;">
      <?= $cntBelum ?> Standar
    </span>
  </div>

  <div style="padding:22px;">
    <?php if (empty($standarBelum)): ?>
      <div style="background:#f0fdf4;border:1px solid #a7f3d0;border-radius:10px;padding:14px 18px;font-size:14px;color:#065f46;font-weight:600;">
        🎉 Luar biasa! Semua standar telah terpenuhi pada siklus ini.
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:10px;">
        <?php foreach ($standarBelum as $idx => $sb): ?>
        <div class="pk-filterable-item" data-kid="<?= (int)($sb['kriteria_id'] ?? 0) ?>" style="background:#fff;border:1.5px solid #fed7aa;border-radius:12px;padding:14px 18px;display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap;">
          <div style="display:flex;align-items:flex-start;gap:12px;flex:1;min-width:220px;">
            <div style="background:#ea580c;color:#fff;font-weight:900;font-size:12px;padding:4px 10px;border-radius:7px;flex-shrink:0;margin-top:2px;">
              <?= htmlspecialchars($sb['kriteria_kode'] ?? ('STD-' . ($idx+1))) ?>
            </div>
            <div>
              <div style="font-weight:800;font-size:14px;color:#1e293b;margin-bottom:3px;"><?= htmlspecialchars($sb['kriteria_nama']) ?></div>
              <div style="font-size:12.5px;color:#64748b;line-height:1.5;"><?= htmlspecialchars(substr($sb['target_capaian'] ?? '', 0, 100)) ?><?= strlen($sb['target_capaian'] ?? '') > 100 ? '…' : '' ?></div>
            </div>
          </div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;">
            <span style="font-size:12px;font-weight:700;color:#c2410c;background:#fff7ed;border:1px solid #fed7aa;padding:4px 12px;border-radius:20px;white-space:nowrap;">
              ⚠️ <?= ($sb['status_tindakan'] ?? 'belum') === 'selesai' ? 'Selesai Dikoreksi' : 'Sedang Dikendalikan' ?>
            </span>
            <span style="font-size:12px;font-weight:700;color:#059669;background:#f0fdf4;border:1px solid #a7f3d0;padding:4px 12px;border-radius:20px;white-space:nowrap;">
              ✓ Tetap Masuk Tahun Depan
            </span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ============================================================
     BAGIAN 3: BERKAS SK
     ============================================================ -->
<div style="background:#fff;border:1.5px solid #a7f3d0;border-radius:18px;margin-bottom:28px;overflow:hidden;box-shadow:0 4px 18px rgba(5,150,105,0.04);">
  <div style="background:linear-gradient(135deg,#f0fdf4,#fff);padding:18px 24px;border-bottom:1.5px solid #a7f3d0;">
    <div class="pk-section-title">
      <span style="width:30px;height:30px;background:#059669;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">📜</span>
      Berkas Surat Keputusan (SK) Pengesahan Standar Baru
    </div>
    <p class="pk-section-desc">Dokumen SK resmi yang mengesahkan standar mutu yang telah ditingkatkan.</p>
  </div>

  <div style="padding:22px;">
    <?php if (empty($berkasList)): ?>
      <div style="padding:28px;text-align:center;color:#94a3b8;">
        <div style="font-size:32px;margin-bottom:8px;">📂</div>
        <div style="font-size:14px;font-style:italic;">Belum ada berkas SK yang diunggah.</div>
        <a href="<?= BASE_URL ?>/peningkatan/<?= $pkId ?>/edit" style="display:inline-block;margin-top:12px;font-size:13px;font-weight:700;color:#059669;text-decoration:none;">
          + Upload SK melalui halaman Edit
        </a>
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:10px;">
        <?php foreach ($berkasList as $sk): ?>
        <div style="background:#f0fdf4;border:1px solid #a7f3d0;border-radius:12px;padding:14px 18px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
          <div>
            <div style="font-weight:800;font-size:14px;color:#065f46;">
              <?= htmlspecialchars($sk['nomor_sk'] ?? '—') ?> — <?= htmlspecialchars($sk['judul_sk'] ?? $sk['file_name']) ?>
            </div>
            <div style="font-size:12.5px;color:#047857;margin-top:3px;">
              📅 Tanggal SK: <?= htmlspecialchars($sk['tanggal_sk'] ?? '—') ?>
              <?php if (!empty($sk['keterangan'])): ?> &nbsp;·&nbsp; <?= htmlspecialchars($sk['keterangan']) ?><?php endif; ?>
            </div>
          </div>
          <a href="<?= htmlspecialchars($sk['url'] ?? BASE_URL . '/' . $sk['file_path']) ?>" target="_blank"
            style="display:inline-flex;align-items:center;gap:7px;background:#059669;color:#fff;font-weight:800;font-size:13px;padding:9px 18px;border-radius:9px;text-decoration:none;white-space:nowrap;box-shadow:0 3px 10px rgba(5,150,105,0.2);">
            📄 Buka & Download SK ↗
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ============================================================
     TOMBOL BUAT PENETAPAN BARU (CTA UTAMA)
     ============================================================ -->
<div style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);border:2px solid #6ee7b7;border-radius:18px;padding:28px 32px;margin-bottom:30px;box-shadow:0 8px 28px rgba(5,150,105,0.08);display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
  <div>
    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#059669;margin-bottom:6px;">
      Siklus Peningkatan Berkelanjutan (Continuous Quality Improvement)
    </div>
    <h3 style="font-size:20px;font-weight:900;color:#064e3b;margin:0 0 6px;">
      🚀 Mulai Siklus PPEPP Tahun Berikutnya
    </h3>
    <p style="font-size:13.5px;color:#065f46;margin:0;max-width:680px;line-height:1.55;">
      Sistem akan otomatis menggabungkan <strong><?= $cntTerpenuhi ?> standar yang telah ditingkatkan</strong> (dengan target &amp; indikator baru) dan <strong><?= $cntBelum ?> standar dari pengendalian</strong> ke dalam dokumen Penetapan baru.
    </p>
  </div>
  <button type="button" onclick="openExportModal()" class="pk-action-btn"
    style="background:#059669;color:#fff;font-size:14px;padding:14px 28px;box-shadow:0 6px 18px rgba(5,150,105,0.35);">
    Buat Penetapan Baru untuk Tahun Depan →
  </button>
</div>

<!-- ============================================================
     MODAL EKSPOR KE PENETAPAN BARU
     ============================================================ -->
<!-- ============================================================
     MODAL EKSPOR KE PENETAPAN BARU & PROJECT BARU
     ============================================================ -->
<div id="exportModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.65);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(3px);padding:16px;">
  <div style="background:#fff;border-radius:20px;max-width:540px;width:100%;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,0.25);">
    
    <!-- Modal Header Banner -->
    <div style="background:linear-gradient(135deg, #064e3b 0%, #065f46 100%);color:#fff;padding:20px 26px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-shrink:0;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;background:rgba(255,255,255,0.18);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
          🚀
        </div>
        <div>
          <h3 style="font-size:17px;font-weight:800;margin:0;color:#fff;">Mulai Siklus PPEPP Baru</h3>
          <p style="font-size:11.5px;margin:2px 0 0;color:rgba(255,255,255,0.8);">Otomatis membuat Project &amp; Dokumen Penetapan Baru</p>
        </div>
      </div>
      <button type="button" onclick="closeExportModal()" style="color:#fff;opacity:0.8;font-size:22px;line-height:1;background:none;border:none;cursor:pointer;padding:4px 8px;">&times;</button>
    </div>

    <!-- Modal Form -->
    <form action="<?= BASE_URL ?>/peningkatan/<?= $pkId ?>/export-to-penetapan" method="POST" style="display:flex;flex-direction:column;overflow:hidden;flex:1;margin:0;">
      <div style="padding:22px 26px;overflow-y:auto;flex:1;">
        
        <div style="background:#f0fdf4;border:1px solid #a7f3d0;border-radius:10px;padding:12px 14px;font-size:12.5px;color:#065f46;margin-bottom:18px;line-height:1.5;">
          💡 <strong>Hasil Siklus:</strong> <?= $cntTerpenuhi ?> standar hasil peningkatan akan menggunakan <strong>target baru</strong>, sedangkan <?= $cntBelum ?> standar pengendalian tetap disertakan. Sistem akan <strong>otomatis membuat Project PPEPP Baru</strong> untuk tahun ajaran yang Anda pilih.
        </div>

        <div class="form-group" style="margin-bottom:16px;">
          <label style="font-size:13px;font-weight:800;color:#1e293b;margin-bottom:6px;display:block;">
            📅 Pilih Tahun Ajaran Tujuan <span style="color:#ef4444;">*</span>
          </label>
          <select name="target_ta_id" id="exportTargetTaSelect" class="form-control" required style="font-size:13.5px;padding:10px 14px;border-radius:10px;" onchange="handleExportTaChange(this)">
            <option value="">— Pilih Tahun Ajaran Baru —</option>
            <?php foreach ($tahunAjarans as $ta): ?>
              <option value="<?= $ta['id'] ?>" <?= $ta['aktif'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($ta['nama']) ?> (<?= ucfirst($ta['semester'] ?? '') ?>)
                <?= $ta['aktif'] ? '— Aktif Saat Ini' : '' ?>
              </option>
            <?php endforeach; ?>
            <option value="custom" style="font-weight:800;color:#2563eb;">+ Input Tahun Ajaran Baru Manual...</option>
          </select>
        </div>

        <!-- Input TA Custom jika tidak ada di list -->
        <input type="hidden" name="new_ta" value="0" id="exportNewTaFlag">
        <div id="exportCustomTaGroup" style="display:none;margin-bottom:16px;background:#f8fafc;border:1px solid #cbd5e1;border-radius:10px;padding:12px 14px;">
          <label style="font-size:12px;font-weight:700;color:#1e293b;margin-bottom:4px;display:block;">Tuliskan Nama Tahun Ajaran Baru:</label>
          <input type="text" name="new_ta_nama" id="exportNewTaNama" class="form-control" placeholder="Contoh: 2026/2027"
                 style="font-size:13.5px;padding:8px 12px;border-radius:8px;" oninput="document.getElementById('exportNewTaFlag').value='1'">
        </div>

        <div class="form-group" style="margin-bottom:6px;">
          <label style="font-size:13px;font-weight:800;color:#1e293b;margin-bottom:6px;display:block;">
            📝 Judul Dokumen Penetapan Baru:
          </label>
          <input type="text" name="judul_penetapan_baru" class="form-control"
            value="Penetapan Standar Mutu (Hasil Peningkatan &amp; Pengendalian)" required
            style="font-size:13.5px;padding:10px 14px;border-radius:10px;">
        </div>
      </div>

      <!-- Modal Footer -->
      <div style="padding:14px 26px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;flex-shrink:0;">
        <button type="button" onclick="closeExportModal()" class="btn btn-outline"
          style="font-size:13px;padding:9px 18px;border-radius:8px;">Batal</button>
        <button type="submit" class="btn btn-primary"
          style="background:#059669;border:none;font-weight:800;font-size:13px;padding:9px 22px;border-radius:8px;box-shadow:0 4px 14px rgba(5,150,105,0.3);color:#fff;display:inline-flex;align-items:center;gap:6px;cursor:pointer;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15"><polyline points="20 6 9 17 4 12"/></svg>
          Konfirmasi &amp; Mulai Siklus Baru
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  var currentPkKid = 'all';

  function filterPeningkatan() {
    var query = (document.getElementById('peningkatanSearchInput')?.value || '').toLowerCase().trim();
    var items = document.querySelectorAll('.pk-filterable-item');
    var visibleCount = 0;

    items.forEach(function(item) {
      var kid = item.getAttribute('data-kid');
      var text = item.innerText.toLowerCase();

      var matchKid = (currentPkKid === 'all' || kid === currentPkKid);
      var matchQuery = (!query || text.indexOf(query) !== -1);

      if (matchKid && matchQuery) {
        item.style.display = '';
        visibleCount++;
      } else {
        item.style.display = 'none';
      }
    });

    var countEl = document.getElementById('peningkatanResultCount');
    if (countEl) {
      countEl.textContent = 'Menampilkan ' + visibleCount + ' standar';
    }
  }

  function filterPeningkatanKriteria(kid, btnEl) {
    currentPkKid = String(kid);
    document.querySelectorAll('.pk-kriteria-filter-btn').forEach(function(btn) {
      btn.style.background = '#fff';
      btn.style.color = '#475569';
      btn.style.borderColor = '#cbd5e1';
      btn.classList.remove('active');
    });
    if (btnEl) {
      btnEl.style.background = '#059669';
      btnEl.style.color = '#fff';
      btnEl.style.borderColor = '#059669';
      btnEl.classList.add('active');
    }
    filterPeningkatan();
  }

  function openExportModal() {
    var m = document.getElementById('exportModal');
    if (m) m.style.display = 'flex';
  }
  function closeExportModal() {
    var m = document.getElementById('exportModal');
    if (m) m.style.display = 'none';
  }
  function handleExportTaChange(sel) {
    var customGrp = document.getElementById('exportCustomTaGroup');
    var newFlag = document.getElementById('exportNewTaFlag');
    if (sel.value === 'custom') {
      if (customGrp) customGrp.style.display = 'block';
      if (newFlag) newFlag.value = '1';
    } else {
      if (customGrp) customGrp.style.display = 'none';
      if (newFlag) newFlag.value = '0';
    }
  }
</script>