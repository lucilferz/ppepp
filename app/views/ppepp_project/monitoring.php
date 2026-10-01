<?php
$pageTitle   = 'Monitoring Keterlaksanaan — ' . htmlspecialchars($project['ta_nama']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Project PPEPP', 'url' => BASE_URL . '/ppepp'],
  ['label' => htmlspecialchars($project['ta_nama']), 'url' => BASE_URL . '/ppepp/' . $project['id']],
  ['label' => 'Monitoring Keterlaksanaan'],
];
$pStats = $projectStats;
?>

<!-- Back Button to Project Detail -->
<div style="margin-bottom:16px;">
  <a href="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>" class="btn btn-outline"
     style="background:#fff;border:1.5px solid #cbd5e1;color:#334155;font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    ← Kembali ke Project <?= htmlspecialchars($project['ta_nama']) ?>
  </a>
</div>

<!-- Header Banner -->
<div style="background:linear-gradient(135deg,#0f172a 0%,#1a237e 60%,#283593 100%);border-radius:16px;padding:26px 30px;margin-bottom:24px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
  <div>
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,255,255,0.55);margin-bottom:6px;">
      Monitoring &amp; Checklist Pelaksanaan • Tahun Ajaran <?= htmlspecialchars($project['ta_nama']) ?>
    </div>
    <h2 style="font-size:24px;font-weight:900;color:#fff;margin-bottom:4px;letter-spacing:-0.4px;">
      Keterlaksanaan Penetapan Standar Fakultas
    </h2>
    <p style="font-size:13.5px;color:rgba(255,255,255,0.75);">
      Pantau dan checklist status keterlaksanaan per masing-masing nomor indikator/standar yang telah ditetapkan pada project ini.
    </p>
  </div>
  <div style="display:flex;gap:10px;flex-shrink:0;">
    <a href="<?= BASE_URL ?>/penetapan/create?project_id=<?= $project['id'] ?>" class="btn btn-primary" style="background:#4f46e5;border:none;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      + Tambah Penetapan
    </a>
    <a href="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>" class="btn btn-outline" style="background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.25);color:#fff;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
      Kembali ke Detail Project
    </a>
  </div>
</div>

<?php if (!empty($flash['message'])): ?>
<div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;<?= $flash['type']==='success' ? 'background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;' : 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;' ?>">
  <?= $flash['type']==='success' ? '✓' : '✕' ?> <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<!-- Summary Cards & Progress Bar -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px;margin-bottom:24px;">
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.03);">
    <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Indikator Target</div>
    <div style="font-size:26px;font-weight:900;color:#1e293b;margin-top:4px;" id="statTotal"><?= $pStats['total'] ?></div>
    <div style="font-size:11.5px;color:#94a3b8;margin-top:2px;">Indikator di <?= count($penetapans) ?> dokumen penetapan</div>
  </div>

  <div style="background:#fff;border:1.5px solid #a7f3d0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.03);">
    <div style="font-size:11.5px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.5px;">✓ Sudah Dilaksanakan</div>
    <div style="font-size:26px;font-weight:900;color:#065f46;margin-top:4px;" id="statTerlaksana"><?= $pStats['terlaksana'] ?></div>
    <div style="font-size:11.5px;color:#059669;margin-top:2px;">Target telah selesai dilaksanakan</div>
  </div>

  <div style="background:#fff;border:1.5px solid #fde68a;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.03);">
    <div style="font-size:11.5px;font-weight:700;color:#d97706;text-transform:uppercase;letter-spacing:0.5px;">⏳ Dalam Proses</div>
    <div style="font-size:26px;font-weight:900;color:#92400e;margin-top:4px;" id="statProses"><?= $pStats['proses'] ?></div>
    <div style="font-size:11.5px;color:#d97706;margin-top:2px;">Target sedang berlangsung</div>
  </div>

  <div style="background:#fff;border:1.5px solid #cbd5e1;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.03);">
    <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">✕ Belum Dilaksanakan</div>
    <div style="font-size:26px;font-weight:900;color:#475569;margin-top:4px;" id="statBelum"><?= $pStats['belum'] ?></div>
    <div style="font-size:11.5px;color:#94a3b8;margin-top:2px;">Target belum berjalan</div>
  </div>
</div>

<!-- Project Progress Banner -->
<div style="background:#fff;border:1.5px solid #c7d2fe;border-radius:14px;padding:18px 22px;margin-bottom:24px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;box-shadow:0 4px 16px rgba(79,70,229,0.06);">
  <div style="flex:1;min-width:240px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
      <span style="font-size:13px;font-weight:800;color:#3730a3;">Persentase Keterlaksanaan Fakultas</span>
      <span style="font-size:14px;font-weight:900;color:#4f46e5;" id="statPct"><?= $pStats['pct'] ?>%</span>
    </div>
    <div style="height:10px;background:#e0e7ff;border-radius:99px;overflow:hidden;">
      <div id="statProgressBar" style="height:100%;width:<?= $pStats['pct'] ?>%;background:linear-gradient(90deg,#4f46e5,#059669);border-radius:99px;transition:width 0.4s ease;"></div>
    </div>
  </div>
</div>

<!-- Filter Tabs -->
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
  <button type="button" onclick="filterTargets('all', this)" class="filter-tab active"
          style="padding:8px 16px;border-radius:9px;border:1.5px solid #4f46e5;background:#4f46e5;color:#fff;font-size:12.5px;font-weight:700;cursor:pointer;">
    Semua Indikator (<?= $pStats['total'] ?>)
  </button>
  <button type="button" onclick="filterTargets('terlaksana', this)" class="filter-tab"
          style="padding:8px 16px;border-radius:9px;border:1.5px solid #e2e8f0;background:#fff;color:#059669;font-size:12.5px;font-weight:700;cursor:pointer;">
    ✓ Sudah Dilaksanakan (<?= $pStats['terlaksana'] ?>)
  </button>
  <button type="button" onclick="filterTargets('proses', this)" class="filter-tab"
          style="padding:8px 16px;border-radius:9px;border:1.5px solid #e2e8f0;background:#fff;color:#d97706;font-size:12.5px;font-weight:700;cursor:pointer;">
    ⏳ Dalam Proses (<?= $pStats['proses'] ?>)
  </button>
  <button type="button" onclick="filterTargets('belum', this)" class="filter-tab"
          style="padding:8px 16px;border-radius:9px;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;font-size:12.5px;font-weight:700;cursor:pointer;">
    ✕ Belum (<?= $pStats['belum'] ?>)
  </button>
</div>

<?php if (empty($penetapans)): ?>
<!-- Empty State -->
<div style="text-align:center;padding:64px 24px;background:#f8faff;border-radius:16px;border:2px dashed #cbd5e1;">
  <div style="width:72px;height:72px;background:#e0e7ff;border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
    <svg viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="1.5" width="36" height="36"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
  </div>
  <h3 style="font-size:18px;font-weight:800;color:#1e293b;margin-bottom:8px;">Belum Ada Penetapan di Project Ini</h3>
  <p style="font-size:13.5px;color:#64748b;max-width:420px;margin:0 auto 24px;">Buat dokumen Penetapan terlebih dahulu untuk dapat menggunakan checklist monitoring keterlaksanaan fakultas.</p>
  <a href="<?= BASE_URL ?>/penetapan/create?project_id=<?= $project['id'] ?>" class="btn btn-primary" style="padding:10px 22px;">+ Buat Penetapan Sekarang</a>
</div>
<?php else: ?>

<!-- Daftar Penetapan & Indikator Checklist -->
<div style="display:flex;flex-direction:column;gap:20px;">
  <?php foreach ($penetapans as $pen): ?>
  <?php
  $penId   = $pen['id'];
  $stPen   = $pen['stats'];
  $details = $pen['details'];
  ?>
  <div class="penetapan-card" style="background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.03);">
    <!-- Header Penetapan -->
    <div style="padding:18px 24px;background:linear-gradient(135deg,#0f172a,#1a237e);color:#fff;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
      <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
          <h3 style="font-size:17px;font-weight:800;color:#fff;margin:0;"><?= htmlspecialchars($pen['judul']) ?></h3>
          <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;<?= $pen['status'] === 'final' ? 'background:rgba(16,185,129,0.2);color:#34d399;' : 'background:rgba(245,158,11,0.2);color:#fbbf24;' ?>">
            <?= $pen['status'] === 'final' ? '✓ Final' : 'Draft' ?>
          </span>
        </div>
        <div style="font-size:12px;color:rgba(255,255,255,0.65);">
          <?= count($details) ?> indikator target standar • Tahun Ajaran <?= htmlspecialchars($pen['ta_nama']) ?>
        </div>
      </div>

      <div style="display:flex;align-items:center;gap:14px;flex-shrink:0;">
        <div style="text-align:right;">
          <div style="font-size:12px;color:rgba(255,255,255,0.7);font-weight:600;">Terlaksana: <strong style="color:#34d399;" id="penPct_<?= $penId ?>"><?= $stPen['pct'] ?>%</strong></div>
          <div style="font-size:11px;color:rgba(255,255,255,0.5);margin-top:2px;" id="penStatsText_<?= $penId ?>">
            <?= $stPen['terlaksana'] ?>/<?= $stPen['total'] ?> target selesai
          </div>
        </div>
        <?php if ($pen['pelaksanaan_id'] > 0): ?>
        <a href="<?= BASE_URL ?>/pelaksanaan/<?= $pen['pelaksanaan_id'] ?>" class="btn btn-sm btn-outline"
           style="background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.25);color:#fff;font-size:12px;">
          Buka Pelaksanaan ↗
        </a>
        <?php else: ?>
        <a href="<?= BASE_URL ?>/pelaksanaan/create?penetapan_id=<?= $penId ?>" class="btn btn-sm btn-primary"
           style="background:#059669;border:none;font-size:12px;">
          + Buat Pelaksanaan
        </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Body: List Indikator Target Standar (Dipisah per nomor indikator/standar) -->
    <div style="padding:20px 24px;display:flex;flex-direction:column;gap:16px;">
      <?php foreach ($details as $idx => $d): ?>
      <?php
      $pdId   = (int)$d['id'];
      $kid    = (int)$d['kriteria_id'];
      $status = $d['status_pelaksanaan'] ?? 'belum';
      $displayKode = !empty($d['kode']) ? $d['kode'] : (!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD-' . ($idx + 1));
      ?>
      <div class="target-item-card" data-status="<?= $status ?>" id="card_<?= $penId ?>_<?= $pdId ?>"
           style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:18px;transition:all 0.2s;">
        
        <!-- Header Target Indikator Standar -->
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:16px;flex-wrap:wrap;">
          <div style="display:flex;align-items:flex-start;gap:12px;">
            <div style="padding:6px 14px;background:#1a237e;color:#fff;border-radius:8px;font-weight:900;font-size:14px;flex-shrink:0;">
              <?= htmlspecialchars($displayKode) ?>
            </div>
            <div>
              <div style="display:flex;align-items:center;gap:10px;">
                <h4 style="font-size:17px;font-weight:800;color:#1e293b;margin:0 0 4px;"><?= htmlspecialchars($d['kriteria_nama']) ?></h4>
                <span style="font-size:12px;font-weight:700;color:#4f46e5;background:#eef2ff;padding:3px 10px;border-radius:6px;">
                  Indikator #<?= $idx + 1 ?>
                </span>
              </div>
              <div style="font-size:13.5px;color:#64748b;line-height:1.5;"><?= htmlspecialchars($d['kriteria_deskripsi'] ?? '') ?></div>
            </div>
          </div>

          <div style="display:flex;align-items:center;gap:8px;">
            <!-- Badge Status -->
            <span id="badgeStatus_<?= $penId ?>_<?= $pdId ?>"
                  style="font-size:13px;font-weight:800;padding:5px 14px;border-radius:20px;
                         <?= $status === 'terlaksana' ? 'background:#ecfdf5;color:#059669;' : ($status === 'proses' ? 'background:#fffbeb;color:#d97706;' : 'background:#f1f5f9;color:#64748b;') ?>">
              <?= $status === 'terlaksana' ? '✓ Sudah Dilaksanakan' : ($status === 'proses' ? '⏳ Dalam Proses' : '✕ Belum Dilaksanakan') ?>
            </span>
            <span id="saveStatusIndicator_<?= $penId ?>_<?= $pdId ?>" style="font-size:12px;color:#94a3b8;"></span>
          </div>
        </div>

        <!-- Target & Indikator Penetapan Dijabarkan Per Header Excel -->
        <div style="background:#fff;border:1.5px solid #cbd5e1;border-radius:12px;padding:16px;margin-bottom:16px;display:flex;flex-direction:column;gap:12px;">
          <?php if (!empty($d['strategi'])): ?>
          <div style="background:#f8faff;border-left:4px solid #4f46e5;padding:10px 14px;border-radius:0 10px 10px 0;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#4f46e5;margin-bottom:4px;letter-spacing:0.5px;">Aturan / Dasar Hukum</div>
            <div style="font-size:15px;color:#1e293b;line-height:1.7;white-space:pre-wrap;font-weight:500;"><?= htmlspecialchars($d['strategi']) ?></div>
          </div>
          <?php endif; ?>

          <div style="background:#f0fdf4;border-left:4px solid #059669;padding:10px 14px;border-radius:0 10px 10px 0;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#065f46;margin-bottom:4px;letter-spacing:0.5px;">Pernyataan Standar (Target Capaian)</div>
            <div style="font-size:15px;color:#1e293b;line-height:1.7;white-space:pre-wrap;font-weight:500;"><?= !empty($d['target_capaian']) ? htmlspecialchars($d['target_capaian']) : '<span style="color:#94a3b8;font-style:italic;">Belum diisi</span>' ?></div>
          </div>

          <?php if (!empty($d['indikator'])): ?>
          <div style="background:#fffbeb;border-left:4px solid #d97706;padding:10px 14px;border-radius:0 10px 10px 0;">
            <div style="font-size:12px;font-weight:800;text-transform:uppercase;color:#92400e;margin-bottom:4px;letter-spacing:0.5px;">📊 Indikator Ketercapaian</div>
            <div style="font-size:15px;color:#1e293b;line-height:1.7;white-space:pre-wrap;font-weight:500;"><?= htmlspecialchars($d['indikator']) ?></div>
          </div>
          <?php endif; ?>
        </div>

        <!-- Selector Checklist Pilihan (Sudah / Dalam Proses / Belum) - TERPISAH PER INDIKATOR -->
        <div style="margin-bottom:14px;">
          <div style="font-size:14px;font-weight:800;color:#1e293b;margin-bottom:8px;">
            Pilih Status Keterlaksanaan untuk Indikator (<?= htmlspecialchars($displayKode) ?>):
          </div>
          <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <label style="flex:1;min-width:160px;cursor:pointer;">
              <input type="radio" name="status_<?= $penId ?>_<?= $pdId ?>" value="terlaksana" <?= $status === 'terlaksana' ? 'checked' : '' ?>
                     onchange="updateMonitoringChecklist(<?= $penId ?>, <?= $pdId ?>, <?= $kid ?>, 'terlaksana')" style="display:none;" id="radio_terlaksana_<?= $penId ?>_<?= $pdId ?>">
              <div id="pill_terlaksana_<?= $penId ?>_<?= $pdId ?>"
                   style="padding:12px 16px;border-radius:10px;border:2px solid <?= $status === 'terlaksana' ? '#10b981' : '#cbd5e1' ?>;background:<?= $status === 'terlaksana' ? '#ecfdf5' : '#fff' ?>;color:<?= $status === 'terlaksana' ? '#065f46' : '#64748b' ?>;font-weight:800;font-size:14px;display:flex;align-items:center;gap:10px;transition:all 0.2s;">
                <span style="width:20px;height:20px;border-radius:50%;border:2px solid <?= $status === 'terlaksana' ? '#059669' : '#94a3b8' ?>;display:flex;align-items:center;justify-content:center;background:<?= $status === 'terlaksana' ? '#059669' : '#fff' ?>;color:#fff;font-size:12px;font-weight:900;">✓</span>
                Sudah Dilaksanakan
              </div>
            </label>

            <label style="flex:1;min-width:160px;cursor:pointer;">
              <input type="radio" name="status_<?= $penId ?>_<?= $pdId ?>" value="proses" <?= $status === 'proses' ? 'checked' : '' ?>
                     onchange="updateMonitoringChecklist(<?= $penId ?>, <?= $pdId ?>, <?= $kid ?>, 'proses')" style="display:none;" id="radio_proses_<?= $penId ?>_<?= $pdId ?>">
              <div id="pill_proses_<?= $penId ?>_<?= $pdId ?>"
                   style="padding:12px 16px;border-radius:10px;border:2px solid <?= $status === 'proses' ? '#f59e0b' : '#cbd5e1' ?>;background:<?= $status === 'proses' ? '#fffbeb' : '#fff' ?>;color:<?= $status === 'proses' ? '#92400e' : '#64748b' ?>;font-weight:800;font-size:14px;display:flex;align-items:center;gap:10px;transition:all 0.2s;">
                <span style="width:20px;height:20px;border-radius:50%;border:2px solid <?= $status === 'proses' ? '#d97706' : '#94a3b8' ?>;display:flex;align-items:center;justify-content:center;background:<?= $status === 'proses' ? '#d97706' : '#fff' ?>;color:#fff;font-size:11px;font-weight:900;">⏳</span>
                Dalam Proses
              </div>
            </label>

            <label style="flex:1;min-width:160px;cursor:pointer;">
              <input type="radio" name="status_<?= $penId ?>_<?= $pdId ?>" value="belum" <?= $status === 'belum' ? 'checked' : '' ?>
                     onchange="updateMonitoringChecklist(<?= $penId ?>, <?= $pdId ?>, <?= $kid ?>, 'belum')" style="display:none;" id="radio_belum_<?= $penId ?>_<?= $pdId ?>">
              <div id="pill_belum_<?= $penId ?>_<?= $pdId ?>"
                   style="padding:12px 16px;border-radius:10px;border:2px solid <?= $status === 'belum' ? '#94a3b8' : '#cbd5e1' ?>;background:<?= $status === 'belum' ? '#f1f5f9' : '#fff' ?>;color:<?= $status === 'belum' ? '#334155' : '#64748b' ?>;font-weight:800;font-size:14px;display:flex;align-items:center;gap:10px;transition:all 0.2s;">
                <span style="width:20px;height:20px;border-radius:50%;border:2px solid <?= $status === 'belum' ? '#475569' : '#94a3b8' ?>;display:flex;align-items:center;justify-content:center;background:<?= $status === 'belum' ? '#475569' : '#fff' ?>;color:#fff;font-size:11px;font-weight:900;">✕</span>
                Belum Dilaksanakan
              </div>
            </label>
          </div>
        </div>

        <!-- Realisasi & Catatan Pelaksanaan Fakultas -->
        <div>
          <label style="font-size:13.5px;font-weight:700;color:#334155;display:block;margin-bottom:6px;">Catatan Realisasi Pelaksanaan Fakultas</label>
          <textarea id="catatan_<?= $penId ?>_<?= $pdId ?>" class="form-control" rows="2"
                    placeholder="Tuliskan keterangan/catatan realisasi pelaksanaan fakultas untuk target ini..."
                    style="font-size:15px;line-height:1.6;"
                    onchange="saveMonitoringCatatan(<?= $penId ?>, <?= $pdId ?>, <?= $kid ?>)"><?= htmlspecialchars($d['catatan_pelaksanaan'] ?? '') ?></textarea>
        </div>

        <!-- Bukti Link Info -->
        <?php if (!empty($d['bukti'])): ?>
        <div style="margin-top:12px;font-size:12px;color:#059669;display:flex;align-items:center;gap:6px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
          <strong><?= count($d['bukti']) ?> Bukti Link Tersedia:</strong>
          <?php foreach ($d['bukti'] as $b): ?>
          <a href="<?= htmlspecialchars($b['url_link']) ?>" target="_blank" style="color:#0284c7;text-decoration:none;background:#e0f2fe;padding:2px 8px;border-radius:6px;">
            <?= htmlspecialchars($b['judul_bukti']) ?> ↗
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
var BASE_URL_APP = '<?= BASE_URL ?>';

// ==================================================
// Update Monitoring Checklist Status via AJAX
// ==================================================
function updateMonitoringChecklist(penId, pdId, kid, status) {
  var indicator = document.getElementById('saveStatusIndicator_' + penId + '_' + pdId);
  if (indicator) { indicator.textContent = 'Menyimpan...'; indicator.style.color = '#94a3b8'; }

  // Update Visual Pills
  ['terlaksana', 'proses', 'belum'].forEach(function(st) {
    var pill  = document.getElementById('pill_' + st + '_' + penId + '_' + pdId);
    var radio = document.getElementById('radio_' + st + '_' + penId + '_' + pdId);
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
        pill.style.borderColor = '#e2e8f0'; pill.style.background = '#fff'; pill.style.color = '#64748b';
      }
    }
  });

  // Update Badge Indikator
  var badge = document.getElementById('badgeStatus_' + penId + '_' + pdId);
  if (badge) {
    if (status === 'terlaksana') {
      badge.textContent = '✓ Sudah Dilaksanakan'; badge.style.background = '#ecfdf5'; badge.style.color = '#059669';
    } else if (status === 'proses') {
      badge.textContent = '⏳ Dalam Proses'; badge.style.background = '#fffbeb'; badge.style.color = '#d97706';
    } else {
      badge.textContent = '✕ Belum Dilaksanakan'; badge.style.background = '#f1f5f9'; badge.style.color = '#64748b';
    }
  }

  // Update parent card data-status attribute for tab filtering
  var card = document.getElementById('card_' + penId + '_' + pdId);
  if (card) card.dataset.status = status;

  var body = new URLSearchParams();
  body.append('penetapan_id', penId);
  body.append('penetapan_detail_id', pdId);
  body.append('kriteria_id', kid);
  body.append('status_pelaksanaan', status);

  fetch(BASE_URL_APP + '/ppepp/save-monitoring-status', {
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
    if (data.success && data.pen_stats) {
      // Update stats penetapan ini
      var penPct = document.getElementById('penPct_' + penId);
      var penText = document.getElementById('penStatsText_' + penId);
      if (penPct) penPct.textContent = data.pen_stats.pct + '%';
      if (penText) penText.textContent = data.pen_stats.terlaksana + '/' + data.pen_stats.total + ' target selesai';
      recalculateGlobalStats();
    }
  })
  .catch(function() {
    if (indicator) { indicator.textContent = '✕ Gagal'; indicator.style.color = '#ef4444'; }
  });
}

function saveMonitoringCatatan(penId, pdId, kid) {
  var catatan = document.getElementById('catatan_' + penId + '_' + pdId)?.value || '';
  var indicator = document.getElementById('saveStatusIndicator_' + penId + '_' + pdId);
  if (indicator) { indicator.textContent = 'Menyimpan...'; indicator.style.color = '#94a3b8'; }

  var status = 'belum';
  ['terlaksana', 'proses', 'belum'].forEach(function(st) {
    var r = document.getElementById('radio_' + st + '_' + penId + '_' + pdId);
    if (r && r.checked) status = st;
  });

  var body = new URLSearchParams();
  body.append('penetapan_id', penId);
  body.append('penetapan_detail_id', pdId);
  body.append('kriteria_id', kid);
  body.append('status_pelaksanaan', status);
  body.append('catatan_pelaksanaan', catatan);

  fetch(BASE_URL_APP + '/ppepp/save-monitoring-status', {
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
  });
}

function recalculateGlobalStats() {
  var cards = document.querySelectorAll('.target-item-card');
  var total = cards.length;
  var terlaksana = 0, proses = 0, belum = 0;
  cards.forEach(function(c) {
    var st = c.dataset.status || 'belum';
    if (st === 'terlaksana') terlaksana++;
    else if (st === 'proses') proses++;
    else belum++;
  });
  var pct = total > 0 ? Math.round((terlaksana / total) * 100) : 0;

  var elTot = document.getElementById('statTotal');
  var elTer = document.getElementById('statTerlaksana');
  var elPro = document.getElementById('statProses');
  var elBel = document.getElementById('statBelum');
  var elPct = document.getElementById('statPct');
  var elBar = document.getElementById('statProgressBar');

  if (elTot) elTot.textContent = total;
  if (elTer) elTer.textContent = terlaksana;
  if (elPro) elPro.textContent = proses;
  if (elBel) elBel.textContent = belum;
  if (elPct) elPct.textContent = pct + '%';
  if (elBar) elBar.style.width = pct + '%';
}

function filterTargets(status, btn) {
  document.querySelectorAll('.filter-tab').forEach(function(b) {
    b.style.background = '#fff';
    b.style.color = b.textContent.includes('Terlaksana') ? '#059669' : (b.textContent.includes('Proses') ? '#d97706' : (b.textContent.includes('Belum') ? '#64748b' : '#3730a3'));
    b.style.borderColor = '#e2e8f0';
  });
  btn.style.background = status === 'terlaksana' ? '#059669' : (status === 'proses' ? '#d97706' : (status === 'belum' ? '#64748b' : '#4f46e5'));
  btn.style.color = '#fff';
  btn.style.borderColor = btn.style.background;

  var cards = document.querySelectorAll('.target-item-card');
  cards.forEach(function(c) {
    if (status === 'all' || c.dataset.status === status) {
      c.style.display = 'block';
    } else {
      c.style.display = 'none';
    }
  });
}
</script>
