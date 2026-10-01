<?php
$pageTitle   = 'Laporan Eksekutif PPEPP';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Laporan PPEPP'],
];

$project       = $reportData['project'] ?? [];
$stats         = $reportData['stats'] ?? [];
$matrix        = $reportData['matrix'] ?? [];
$dokumen       = $reportData['dokumen'] ?? [];
$penetapan     = $reportData['penetapan'] ?? [];
$pelaksanaan   = $reportData['pelaksanaan'] ?? [];
$evaluasi      = $reportData['evaluasi'] ?? [];
$pengendalian  = $reportData['pengendalian'] ?? [];
$peningkatan   = $reportData['peningkatan'] ?? [];
?>

<style>
/* CSS Styling khusus halaman Laporan Eksekutif */
.laporan-header-box {
  background: #fff;
  border: 1.5px solid var(--border);
  border-radius: var(--radius);
  padding: 22px 28px;
  margin-bottom: 24px;
  box-shadow: var(--shadow);
}
.stat-pill-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 11.5px;
  font-weight: 800;
}
.tab-btn {
  padding: 10px 20px;
  border: none;
  background: transparent;
  font-weight: 800;
  font-size: 13px;
  color: #64748b;
  cursor: pointer;
  border-bottom: 2.5px solid transparent;
  transition: all 0.2s ease;
}
.tab-btn.active {
  color: #4f46e5;
  border-bottom-color: #4f46e5;
  background: rgba(79,70,229,0.04);
}
.table-matrix {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 12.5px;
}
.table-matrix th {
  background: #f8fafc;
  color: #334155;
  font-weight: 800;
  padding: 12px 14px;
  border-bottom: 1.5px solid #e2e8f0;
  border-top: 1px solid #e2e8f0;
  vertical-align: middle;
}
.table-matrix td {
  padding: 14px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: top;
  line-height: 1.5;
}
.table-matrix tr:hover td {
  background: #fbfdff;
}
.stage-tag {
  font-size: 10.5px;
  font-weight: 900;
  text-transform: uppercase;
  padding: 2px 8px;
  border-radius: 5px;
  display: inline-block;
  margin-bottom: 4px;
}
@media print {
  body { background: #fff !important; }
  .topbar, .sidebar, .sidebar-overlay, .page-actions, .no-print { display: none !important; }
  .main-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
  .card, .laporan-header-box { box-shadow: none !important; border: 1px solid #ccc !important; }
}
</style>

<!-- Top Controls: Project Selector & Export Buttons -->
<div class="no-print" style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
  <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
    <label style="font-size:13px;font-weight:800;color:#1e293b;margin:0;">Pilih Project PPEPP:</label>
    <form method="GET" action="<?= BASE_URL ?>/laporan" style="margin:0;">
      <select name="project_id" class="form-control" onchange="this.form.submit()"
              style="font-size:13px;font-weight:700;padding:8px 14px;border-radius:10px;min-width:280px;background:#fff;border:1.5px solid #cbd5e1;cursor:pointer;">
        <?php if (empty($projects)): ?>
        <option value="">— Belum ada Project PPEPP —</option>
        <?php else: ?>
        <?php foreach ($projects as $p): ?>
        <option value="<?= $p['id'] ?>" <?= $selectedProjectId == $p['id'] ? 'selected' : '' ?>>
          T.A. <?= htmlspecialchars($p['ta_nama']) ?> — <?= htmlspecialchars($p['judul']) ?>
        </option>
        <?php endforeach; ?>
        <?php endif; ?>
      </select>
    </form>
  </div>

  <?php if (!empty($project)): ?>
  <div style="display:flex;gap:8px;flex-wrap:wrap;">
    <a href="<?= BASE_URL ?>/laporan/export-csv?project_id=<?= $selectedProjectId ?>" class="btn btn-outline"
       style="font-size:12.5px;font-weight:700;background:#fff;border-color:#cbd5e1;color:#334155;display:inline-flex;align-items:center;gap:6px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export CSV / Excel
    </a>
    <a href="<?= BASE_URL ?>/laporan/cetak?project_id=<?= $selectedProjectId ?>" target="_blank" class="btn btn-primary"
       style="font-size:12.5px;font-weight:800;background:#4f46e5;border:none;display:inline-flex;align-items:center;gap:6px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Cetak / Simpan PDF ↗
    </a>
  </div>
  <?php endif; ?>
</div>

<?php if (empty($project)): ?>
<!-- Empty State -->
<div class="card" style="padding:60px 20px;text-align:center;border-radius:16px;">
  <div style="width:64px;height:64px;background:#e0e7ff;color:#4f46e5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="32" height="32"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
  </div>
  <h3 style="font-size:18px;font-weight:800;color:#1e293b;margin-bottom:6px;">Belum Ada Project PPEPP yang Dipilih</h3>
  <p style="font-size:13.5px;color:#64748b;max-width:440px;margin:0 auto 20px;">
    Silakan buat project PPEPP dan lengkapi standar pada siklus Penetapan hingga Peningkatan untuk melihat laporan komprehensif.
  </p>
  <a href="<?= BASE_URL ?>/ppepp/create" class="btn btn-primary" style="background:#4f46e5;border:none;font-weight:700;">
    + Buat Project PPEPP Baru
  </a>
</div>
<?php else: ?>

<!-- Project Header Banner -->
<div class="laporan-header-box">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:16px;">
      <div style="width:48px;height:48px;border-radius:12px;background:var(--bg);border:1.5px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg viewBox="0 0 24 24" fill="none" stroke="var(--primary-light)" stroke-width="1.8" width="24" height="24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      </div>
      <div>
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:4px;">
          Laporan Eksekutif &amp; Matriks Komprehensif PPEPP Fakultas
        </div>
        <h2 style="font-size:20px;font-weight:800;color:var(--text-main);margin:0 0 4px;letter-spacing:-0.3px;">
          <?= htmlspecialchars($project['judul'] ?? 'Project PPEPP') ?>
        </h2>
        <div style="font-size:12.5px;color:var(--text-muted);display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
          <span>Tahun Ajaran: <strong style="color:var(--text-main);"><?= htmlspecialchars($project['ta_nama']) ?></strong> (<?= ucfirst($project['semester'] ?? '') ?>)</span>
          <span>•</span>
          <span>Status: <strong style="color:#059669;"><?= ucfirst($project['status'] ?? 'aktif') ?></strong></span>
          <span>•</span>
          <span>Laporan per: <?= date('d F Y') ?></span>
        </div>
      </div>
    </div>

    <!-- Overall Progress Badge -->
    <div style="background:var(--bg);border:1.5px solid var(--border);border-radius:10px;padding:12px 18px;text-align:right;">
      <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.4px;">Ketercapaian Standar</div>
      <div style="font-size:26px;font-weight:800;color:var(--primary-light);margin-top:2px;">
        <?= $stats['pct_evaluasi'] ?? 0 ?>%
      </div>
      <div style="font-size:11px;color:var(--text-muted);">
        <?= $stats['evaluasi']['tercapai'] ?? 0 ?> dari <?= $stats['total_standar'] ?? 0 ?> Standar Terpenuhi
      </div>
    </div>
  </div>
</div>

<!-- 5 Tahap PPEPP Status Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:24px;">
  <!-- Penetapan -->
  <div style="background:#fff;border:1.5px solid #dbeafe;border-radius:14px;padding:16px 18px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
      <span style="font-size:11px;font-weight:800;color:#1d4ed8;background:#eff6ff;padding:3px 8px;border-radius:6px;">1. PENETAPAN</span>
      <span style="font-size:11px;font-weight:700;color:#059669;"><?= !empty($penetapan['id']) ? '✓ Ada' : 'Belum' ?></span>
    </div>
    <div style="font-size:22px;font-weight:900;color:#1e293b;"><?= $stats['total_standar'] ?? 0 ?></div>
    <div style="font-size:11.5px;color:#64748b;margin-top:2px;">Standar &amp; Indikator Ditetapkan</div>
  </div>

  <!-- Pelaksanaan -->
  <div style="background:#fff;border:1.5px solid #d1fae5;border-radius:14px;padding:16px 18px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
      <span style="font-size:11px;font-weight:800;color:#047857;background:#ecfdf5;padding:3px 8px;border-radius:6px;">2. PELAKSANAAN</span>
      <span style="font-size:11px;font-weight:700;color:#059669;"><?= $stats['pct_pelaksanaan'] ?? 0 ?>%</span>
    </div>
    <div style="font-size:22px;font-weight:900;color:#065f46;"><?= $stats['pelaksanaan']['terlaksana'] ?? 0 ?></div>
    <div style="font-size:11.5px;color:#047857;margin-top:2px;">Standar Telah Terlaksana</div>
  </div>

  <!-- Evaluasi -->
  <div style="background:#fff;border:1.5px solid #ede9fe;border-radius:14px;padding:16px 18px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
      <span style="font-size:11px;font-weight:800;color:#6d28d9;background:#f5f3ff;padding:3px 8px;border-radius:6px;">3. EVALUASI</span>
      <span style="font-size:11px;font-weight:700;color:#6d28d9;"><?= $stats['evaluasi']['tercapai'] ?? 0 ?>/<?= $stats['total_standar'] ?? 0 ?></span>
    </div>
    <div style="font-size:22px;font-weight:900;color:#4c1d95;"><?= $stats['evaluasi']['tercapai'] ?? 0 ?></div>
    <div style="font-size:11.5px;color:#6d28d9;margin-top:2px;">Standar Terpenuhi (Tercapai)</div>
  </div>

  <!-- Pengendalian -->
  <div style="background:#fff;border:1.5px solid #ffedd5;border-radius:14px;padding:16px 18px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
      <span style="font-size:11px;font-weight:800;color:#c2410c;background:#fff7ed;padding:3px 8px;border-radius:6px;">4. PENGENDALIAN</span>
      <span style="font-size:11px;font-weight:700;color:#c2410c;"><?= $stats['pengendalian']['total_rtl'] ?? 0 ?> Perlu RTL</span>
    </div>
    <div style="font-size:22px;font-weight:900;color:#9a3412;"><?= $stats['pengendalian']['selesai'] ?? 0 ?></div>
    <div style="font-size:11.5px;color:#c2410c;margin-top:2px;">RTL Selesai Dikoreksi</div>
  </div>

  <!-- Peningkatan -->
  <div style="background:#fff;border:1.5px solid #dcfce7;border-radius:14px;padding:16px 18px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
      <span style="font-size:11px;font-weight:800;color:#059669;background:#f0fdf4;padding:3px 8px;border-radius:6px;">5. PENINGKATAN</span>
      <span style="font-size:11px;font-weight:700;color:#059669;"><?= $stats['peningkatan']['ditingkatkan'] ?? 0 ?> Target Baru</span>
    </div>
    <div style="font-size:22px;font-weight:900;color:#064e3b;"><?= $stats['peningkatan']['ditingkatkan'] ?? 0 ?></div>
    <div style="font-size:11.5px;color:#059669;margin-top:2px;">Standar Ditingkatkan Mutunya</div>
  </div>
</div>

<!-- Navigation Tabs for Report -->
<div class="card" style="border-radius:16px;border:1.5px solid #e2e8f0;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.03);margin-bottom:24px;">
  
  <div class="no-print" style="display:flex;border-bottom:1.5px solid #e2e8f0;background:#f8fafc;padding:0 12px;gap:6px;">
    <button type="button" onclick="switchReportTab('matrix')" id="tabBtn_matrix" class="tab-btn active">
      📋 Matriks Lengkap PPEPP (5 Tahap)
    </button>
    <button type="button" onclick="switchReportTab('cards')" id="tabBtn_cards" class="tab-btn">
      📑 Ringkasan Kartu Per Standar
    </button>
    <button type="button" onclick="switchReportTab('rtm')" id="tabBtn_rtm" class="tab-btn">
      📜 Bukti RTM &amp; Dokumen SK
    </button>
  </div>

  <div class="card-body" style="padding:22px;">

    <!-- TAB 1: MATRIKS LENGKAP PPEPP (5 TAHAP) -->
    <div id="tabContent_matrix">
      <div style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
          <h3 style="font-size:16px;font-weight:900;color:#1e293b;margin:0 0 2px;">
            Matriks Penjaminan Mutu PPEPP Komprehensif
          </h3>
          <p style="font-size:12.5px;color:#64748b;margin:0;">
            Menyajikan alur lengkap tiap kriteria standar mulai dari Penetapan, Keterlaksanaan, Evaluasi Capaian, RTL Pengendalian, hingga Peningkatan Standar Baru.
          </p>
        </div>

        <div class="no-print" style="display:flex;align-items:center;gap:8px;">
          <input type="text" id="matrixSearch" placeholder="Cari standar / indikator..." onkeyup="filterMatrixTable(this.value)"
                 style="font-size:12px;padding:6px 12px;border:1.5px solid #cbd5e1;border-radius:8px;width:220px;">
        </div>
      </div>

      <div style="overflow-x:auto;border:1px solid #e2e8f0;border-radius:12px;">
        <table class="table-matrix" id="matrixTable">
          <thead>
            <tr>
              <th style="width:140px;">Kriteria Standar</th>
              <th style="width:220px;">1. Penetapan (P)</th>
              <th style="width:160px;">2. Pelaksanaan (P)</th>
              <th style="width:200px;">3. Evaluasi (E)</th>
              <th style="width:220px;">4. Pengendalian (P)</th>
              <th style="width:220px;">5. Peningkatan (P)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($matrix as $idx => $m): ?>
            <?php
            $isTercapai = ($m['e_status'] === 'tercapai');
            $stP2       = $m['p2_status'] ?? 'belum';
            ?>
            <tr class="matrix-row">
              <!-- Kriteria -->
              <td style="font-weight:700;">
                <div style="display:inline-block;padding:3px 8px;background:#1e293b;color:#fff;border-radius:6px;font-size:11.5px;font-weight:900;margin-bottom:4px;">
                  <?= htmlspecialchars($m['kriteria_kode']) ?>
                </div>
                <div style="font-size:13px;color:#1e293b;font-weight:800;"><?= htmlspecialchars($m['kriteria_nama']) ?></div>
                <?php if (!empty($m['kriteria_deskripsi'])): ?>
                <div style="font-size:11px;color:#64748b;margin-top:2px;"><?= htmlspecialchars($m['kriteria_deskripsi']) ?></div>
                <?php endif; ?>
              </td>

              <!-- 1. Penetapan -->
              <td>
                <span class="stage-tag" style="background:#eff6ff;color:#1d4ed8;">Penetapan Standar</span>
                <div style="font-size:13.5px;color:#1e293b;margin-bottom:4px;line-height:1.5;">
                  <strong>🎯 Pernyataan Standar:</strong> <?= htmlspecialchars($m['p1_target']) ?>
                </div>
                
                <div style="font-size:13px;color:#0369a1;margin-top:4px;line-height:1.5;">
                  <strong>Target / Indikator:</strong> <?= htmlspecialchars($m['p1_indikator']) ?>
                </div>
                <?php if (!empty($m['p1_aturan']) && $m['p1_aturan'] !== '—'): ?>
                <div style="font-size:12px;color:#64748b;margin-top:4px;">
                  <em>Aturan: <?= htmlspecialchars($m['p1_aturan']) ?></em>
                </div>
                <?php endif; ?>
              </td>

              <!-- 2. Pelaksanaan -->
              <td>
                <span class="stat-pill-badge" style="<?= $stP2 === 'terlaksana' ? 'background:#ecfdf5;color:#065f46;' : ($stP2 === 'proses' ? 'background:#fffbeb;color:#92400e;' : 'background:#fef2f2;color:#991b1b;') ?>">
                  <?= $stP2 === 'terlaksana' ? '✓ Terlaksana' : ($stP2 === 'proses' ? '⏳ Sedang Proses' : '✕ Belum Terlaksana') ?>
                </span>
                <?php if (!empty($m['p2_catatan'])): ?>
                <div style="font-size:11.5px;color:#334155;margin-top:6px;">
                  <?= htmlspecialchars($m['p2_catatan']) ?>
                </div>
                <?php endif; ?>
              </td>

              <!-- 3. Evaluasi -->
              <td>
                <span class="stat-pill-badge" style="<?= $isTercapai ? 'background:#d1fae5;color:#065f46;' : 'background:#fee2e2;color:#991b1b;' ?>">
                  <?= $isTercapai ? '✓ Tercapai Penuh' : ($m['e_status'] === 'sebagian' ? '⚠️ Sebagian Tercapai' : '✕ Belum Tercapai') ?>
                </span>
                <?php if (!empty($m['e_evaluasi'])): ?>
                <div style="font-size:11.5px;color:#475569;margin-top:6px;line-height:1.4;">
                  <?= nl2br(htmlspecialchars($m['e_evaluasi'])) ?>
                </div>
                <?php endif; ?>
              </td>

              <!-- 4. Pengendalian -->
              <td>
                <?php if ($isTercapai): ?>
                <div style="background:#f0fdf4;border:1px dashed #a7f3d0;border-radius:8px;padding:8px 10px;font-size:11.5px;color:#065f46;">
                  ✓ Standar terpenuhi, tidak memerlukan tindakan koreksi.
                </div>
                <?php else: ?>
                <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:8px 12px;font-size:11.5px;">
                  <strong style="color:#9a3412;">🛠️ RTL:</strong> <?= htmlspecialchars($m['p4_rtl'] ?: 'Sedang dirumuskan') ?><br>
                  <?php if (!empty($m['p4_akar_masalah'])): ?>
                  <div style="margin-top:4px;color:#c2410c;"><em>Akar Masalah: <?= htmlspecialchars($m['p4_akar_masalah']) ?></em></div>
                  <?php endif; ?>
                  <div style="margin-top:4px;color:#64748b;font-size:11px;">
                    PIC: <?= htmlspecialchars($m['p4_pic'] ?: '—') ?> &nbsp;·&nbsp; Waktu: <?= htmlspecialchars($m['p4_waktu'] ?: '—') ?>
                  </div>
                </div>
                <?php endif; ?>
              </td>

              <!-- 5. Peningkatan -->
              <td>
                <?php if (!$isTercapai): ?>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:8px 10px;font-size:11.5px;color:#64748b;">
                  Dilanjutkan ke tahun depan tanpa perubahan target (tetap/dapat diedit).
                </div>
                <?php else: ?>
                <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;padding:8px 12px;font-size:11.5px;">
                  <strong style="color:#065f46;">🎯 Target Baru:</strong><br>
                  <span style="font-weight:700;color:#064e3b;"><?= htmlspecialchars($m['p5_target_baru'] ?: $m['p1_target']) ?></span>
                  <?php if (!empty($m['p5_indikator_baru'])): ?>
                  <div style="margin-top:4px;color:#047857;"><strong>Indikator Baru:</strong> <?= htmlspecialchars($m['p5_indikator_baru']) ?></div>
                  <?php endif; ?>
                  <?php if (!empty($m['p5_nilai_kenaikan'])): ?>
                  <div style="margin-top:4px;color:#059669;font-weight:700;">📈 <?= htmlspecialchars($m['p5_nilai_kenaikan']) ?></div>
                  <?php endif; ?>
                </div>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 2: RINGKASAN KARTU PER STANDAR -->
    <div id="tabContent_cards" style="display:none;">
      <div style="display:flex;flex-direction:column;gap:18px;">
        <?php foreach ($matrix as $idx => $m): ?>
        <?php $isTercapai = ($m['e_status'] === 'tercapai'); ?>
        <div style="border:1.5px solid <?= $isTercapai ? '#a7f3d0' : '#fed7aa' ?>;border-radius:14px;overflow:hidden;background:#fff;">
          
          <div style="background:<?= $isTercapai ? '#f0fdf4' : '#fff7ed' ?>;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid <?= $isTercapai ? '#a7f3d0' : '#fed7aa' ?>;flex-wrap:wrap;gap:10px;">
            <div style="display:flex;align-items:center;gap:10px;">
              <span style="padding:4px 10px;background:<?= $isTercapai ? '#059669' : '#ea580c' ?>;color:#fff;border-radius:6px;font-weight:800;font-size:12px;">
                <?= htmlspecialchars($m['kriteria_kode']) ?>
              </span>
              <h4 style="font-size:15px;font-weight:800;color:#1e293b;margin:0;"><?= htmlspecialchars($m['kriteria_nama']) ?></h4>
            </div>

            <span class="stat-pill-badge" style="<?= $isTercapai ? 'background:#059669;color:#fff;' : 'background:#fee2e2;color:#991b1b;' ?>">
              <?= $isTercapai ? '✓ Terpenuhi &amp; Ditingkatkan' : '⚠️ Belum Terpenuhi (Dalam Pengendalian)' ?>
            </span>
          </div>

          <div style="padding:20px;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;">
            <!-- 1. Penetapan -->
            <div style="background:#f8fafc;padding:12px;border-radius:10px;border:1px solid #e2e8f0;">
              <div style="font-size:10.5px;font-weight:900;color:#1d4ed8;text-transform:uppercase;margin-bottom:4px;">1. Penetapan Target</div>
              <div style="font-size:12.5px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($m['p1_target']) ?></div>
              <div style="font-size:11.5px;color:#64748b;margin-top:4px;">Indikator: <?= htmlspecialchars($m['p1_indikator']) ?></div>
            </div>

            <!-- 2. Pelaksanaan -->
            <div style="background:#f8fafc;padding:12px;border-radius:10px;border:1px solid #e2e8f0;">
              <div style="font-size:10.5px;font-weight:900;color:#047857;text-transform:uppercase;margin-bottom:4px;">2. Pelaksanaan</div>
              <div style="font-size:12.5px;font-weight:700;color:#065f46;"><?= ucfirst($m['p2_status']) ?></div>
              <div style="font-size:11.5px;color:#64748b;margin-top:4px;"><?= htmlspecialchars($m['p2_catatan'] ?: 'Sesuai rencana') ?></div>
            </div>

            <!-- 3. Evaluasi -->
            <div style="background:#f8fafc;padding:12px;border-radius:10px;border:1px solid #e2e8f0;">
              <div style="font-size:10.5px;font-weight:900;color:#6d28d9;text-transform:uppercase;margin-bottom:4px;">3. Evaluasi Capaian</div>
              <div style="font-size:12.5px;font-weight:700;color:<?= $isTercapai ? '#059669' : '#b91c1c' ?>;">
                <?= $isTercapai ? '✓ Tercapai' : 'Belum Tercapai' ?>
              </div>
              <div style="font-size:11.5px;color:#64748b;margin-top:4px;"><?= htmlspecialchars(substr($m['e_evaluasi'] ?? '', 0, 70)) ?>...</div>
            </div>

            <!-- 4. Pengendalian / 5. Peningkatan -->
            <div style="background:<?= $isTercapai ? '#ecfdf5' : '#fff7ed' ?>;padding:12px;border-radius:10px;border:1px solid <?= $isTercapai ? '#a7f3d0' : '#fed7aa' ?>;">
              <div style="font-size:10.5px;font-weight:900;color:<?= $isTercapai ? '#065f46' : '#c2410c' ?>;text-transform:uppercase;margin-bottom:4px;">
                <?= $isTercapai ? '5. Peningkatan (Target Baru)' : '4. Tindakan Koreksi (RTL)' ?>
              </div>
              <?php if ($isTercapai): ?>
              <div style="font-size:12.5px;font-weight:800;color:#064e3b;"><?= htmlspecialchars($m['p5_target_baru'] ?: 'Dipertahankan standar sama') ?></div>
              <?php else: ?>
              <div style="font-size:12.5px;font-weight:700;color:#9a3412;"><?= htmlspecialchars($m['p4_rtl'] ?: 'Sedang dirumuskan') ?></div>
              <?php endif; ?>
            </div>
          </div>

        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- TAB 3: BUKTI DOKUMEN RTM & SK -->
    <div id="tabContent_rtm" style="display:none;">
      
      <!-- Dokumen Rapat RTM Evaluasi -->
      <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;margin-bottom:20px;">
        <h4 style="font-size:15px;font-weight:800;color:#1e293b;margin:0 0 12px;display:flex;align-items:center;gap:8px;">
          <span>📝 Dokumen Rapat Tinjauan Manajemen (RTM) Evaluasi</span>
        </h4>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;margin-bottom:16px;">
          <!-- Undangan -->
          <div style="background:#fff;padding:12px 16px;border-radius:10px;border:1px solid #e2e8f0;">
            <div style="font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;margin-bottom:4px;">Surat Undangan RTM:</div>
            <?php if (!empty($dokumen['evaluasi_undangan'])): ?>
            <a href="<?= BASE_URL . '/' . $dokumen['evaluasi_undangan'] ?>" target="_blank" style="color:#059669;font-weight:700;text-decoration:none;font-size:13px;">
              📄 <?= htmlspecialchars($dokumen['evaluasi_undangan_nama'] ?: 'Lihat Undangan') ?> ↗
            </a>
            <?php else: ?>
            <span style="font-size:12px;color:#94a3b8;font-style:italic;">Belum ada undangan diunggah</span>
            <?php endif; ?>
          </div>

          <!-- Notulensi -->
          <div style="background:#fff;padding:12px 16px;border-radius:10px;border:1px solid #e2e8f0;">
            <div style="font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;margin-bottom:4px;">Notulensi / Risalah Rapat:</div>
            <div style="font-size:12px;color:#334155;line-height:1.4;white-space:pre-wrap;max-height:80px;overflow-y:auto;"><?= !empty($dokumen['evaluasi_notulensi']) ? htmlspecialchars($dokumen['evaluasi_notulensi']) : '<span style="color:#94a3b8;font-style:italic;">Belum diisi</span>' ?></div>
          </div>
        </div>

        <!-- Daftar Hadir Absensi (8 Peserta) -->
        <?php if (!empty($dokumen['evaluasi_absensi'])): ?>
        <div style="background:#fff;padding:14px 18px;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:16px;">
          <div style="font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;margin-bottom:8px;">Daftar Kehadiran Tim RTM (Absensi):</div>
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px;">
            <?php foreach ($dokumen['evaluasi_absensi'] as $peserta): ?>
            <div style="font-size:12px;padding:6px 10px;background:#f8fafc;border-radius:6px;display:flex;align-items:center;justify-content:space-between;">
              <span style="font-weight:600;color:#1e293b;"><?= htmlspecialchars($peserta['nama'] ?? '') ?></span>
              <span style="font-weight:800;font-size:10.5px;color:<?= ($peserta['status'] ?? '') === 'HADIR' ? '#059669' : '#94a3b8' ?>;">
                <?= htmlspecialchars($peserta['status'] ?? '') ?>
              </span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Galeri Foto Kegiatan RTM -->
        <?php if (!empty($dokumen['evaluasi_gambar'])): ?>
        <div>
          <div style="font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;margin-bottom:8px;">Dokumentasi Foto Rapat:</div>
          <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <?php foreach ($dokumen['evaluasi_gambar'] as $foto): ?>
            <a href="<?= htmlspecialchars($foto['url'] ?? BASE_URL . '/' . $foto['file_path']) ?>" target="_blank">
              <img src="<?= htmlspecialchars($foto['url'] ?? BASE_URL . '/' . $foto['file_path']) ?>"
                   alt="Dokumentasi RTM"
                   style="width:110px;height:80px;object-fit:cover;border-radius:8px;border:1.5px solid #e2e8f0;box-shadow:0 2px 6px rgba(0,0,0,0.05);">
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Berkas SK Peningkatan Standar Baru -->
      <div style="background:#f0fdf4;border:1.5px solid #a7f3d0;border-radius:14px;padding:20px;">
        <h4 style="font-size:15px;font-weight:800;color:#065f46;margin:0 0 12px;display:flex;align-items:center;gap:8px;">
          <span>📜 Berkas Surat Keputusan (SK) Standar Baru / Kebijakan Mutu</span>
        </h4>

        <?php if (empty($dokumen['berkas_sk'])): ?>
        <div style="font-size:12.5px;color:#94a3b8;font-style:italic;">Belum ada berkas SK yang diunggah.</div>
        <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:8px;">
          <?php foreach ($dokumen['berkas_sk'] as $sk): ?>
          <div style="background:#fff;border:1px solid #a7f3d0;border-radius:8px;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div>
              <strong style="color:#065f46;font-size:13px;"><?= htmlspecialchars($sk['nomor_sk'] ?? '—') ?></strong> — <?= htmlspecialchars($sk['judul_sk'] ?? $sk['file_name']) ?>
              <div style="font-size:11.5px;color:#64748b;">Tanggal: <?= htmlspecialchars($sk['tanggal_sk'] ?? '—') ?></div>
            </div>
            <a href="<?= htmlspecialchars($sk['url'] ?? BASE_URL . '/' . $sk['file_path']) ?>" target="_blank"
               class="btn btn-outline btn-sm" style="color:#059669;border-color:#10b981;font-weight:700;">
              📄 Buka SK ↗
            </a>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

    </div>

  </div>
</div>
<?php endif; ?>

<script>
function switchReportTab(tabId) {
  ['matrix', 'cards', 'rtm'].forEach(function(t) {
    var c = document.getElementById('tabContent_' + t);
    var b = document.getElementById('tabBtn_' + t);
    if (c) c.style.display = (t === tabId ? 'block' : 'none');
    if (b) {
      if (t === tabId) b.classList.add('active');
      else b.classList.remove('active');
    }
  });
}

function filterMatrixTable(keyword) {
  var rows = document.querySelectorAll('.matrix-row');
  var query = keyword.toLowerCase();
  rows.forEach(function(r) {
    var text = r.textContent.toLowerCase();
    r.style.display = text.indexOf(query) > -1 ? '' : 'none';
  });
}
</script>
