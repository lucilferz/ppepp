<?php
$pageTitle   = 'Form Evaluasi — ' . htmlspecialchars($evaluasi['judul']);
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
$cntTerisi     = 0;

foreach ($details as $d) {
    $st = $d['status_capaian'] ?? 'belum_tercapai';
    if ($st === 'tercapai') $cntTercapai++;
    elseif ($st === 'sebagian') $cntSebagian++;
    else $cntBelum++;

    if (!empty(trim($d['evaluasi_teks'] ?? ''))) $cntTerisi++;
}
$pctEvaluasi = $totalKriteria > 0 ? round(($cntTerisi / $totalKriteria) * 100) : 0;
?>

<!-- Header Banner -->
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#312e81 100%);border-radius:16px;padding:26px 30px;margin-bottom:24px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;box-shadow:0 10px 30px rgba(49,46,129,0.2);">
  <div>
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,255,255,0.6);margin-bottom:6px;">
      Evaluasi Pelaksanaan Standar (Tahap E • PPEPP)
    </div>
    <h2 style="font-size:24px;font-weight:900;color:#fff;margin-bottom:4px;letter-spacing:-0.4px;">
      <?= htmlspecialchars($evaluasi['judul']) ?>
    </h2>
    <div style="font-size:13px;color:rgba(255,255,255,0.75);display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:4px;">
      <span>Penetapan Acuan: <strong><?= htmlspecialchars($penetapan['judul'] ?? '—') ?></strong></span>
      <span>•</span>
      <span>Tahun Ajaran: <strong><?= htmlspecialchars($penetapan['ta_nama'] ?? '—') ?></strong></span>
      <span>•</span>
      <span>Jenis: <strong style="color:#a5b4fc;"><?= htmlspecialchars(ucwords($evaluasi['jenis'])) ?></strong></span>
      <?php if (!empty($pelaksanaan['judul'])): ?>
      <span>•</span>
      <span style="color:#34d399;">Pelaksanaan Terhubung ✓</span>
      <?php endif; ?>
    </div>
  </div>

  <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
    <button type="button" onclick="generateAllAIEvaluasi()" id="btnGenerateAllAI"
            class="btn btn-primary"
            style="background:linear-gradient(135deg,#6366f1,#8b5cf6);border:none;font-weight:800;font-size:13px;display:flex;align-items:center;gap:8px;box-shadow:0 4px 14px rgba(99,102,241,0.4);cursor:pointer;padding:10px 18px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      ✨ Generate Semua Evaluasi AI (1-Klik)
    </button>
    <a href="<?= BASE_URL ?>/evaluasi/<?= $evId ?>" class="btn btn-outline"
       style="background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.25);color:#fff;font-size:12.5px;">
      Lihat Hasil ↗
    </a>
    <a href="<?= BASE_URL ?>/ppepp<?= !empty($evaluasi['ppepp_project_id']) ? '/' . $evaluasi['ppepp_project_id'] : '' ?>" class="btn btn-outline"
       style="background:rgba(255,255,255,0.06);border-color:rgba(255,255,255,0.2);color:#fff;font-size:12.5px;">
      ← Kembali ke Project Library
    </a>
  </div>
</div>

<?php if (!empty($flash['message'])): ?>
<div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;<?= $flash['type']==='success' ? 'background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;' : 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;' ?>">
  <?= $flash['type']==='success' ? '✓' : '✕' ?> <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<!-- Summary Cards & Progress Evaluasi -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:24px;">
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Indikator Standar</div>
    <div style="font-size:26px;font-weight:900;color:#1e293b;margin-top:4px;" id="statTotal"><?= $totalKriteria ?></div>
    <div style="font-size:11.5px;color:#94a3b8;margin-top:2px;">Dari dokumen Penetapan acuan</div>
  </div>

  <div style="background:#fff;border:1.5px solid #a7f3d0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="font-size:11.5px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.5px;">✓ Tercapai Penuh</div>
    <div style="font-size:26px;font-weight:900;color:#065f46;margin-top:4px;" id="statTercapai"><?= $cntTercapai ?></div>
    <div style="font-size:11.5px;color:#059669;margin-top:2px;">Target telah terpenuhi</div>
  </div>

  <div style="background:#fff;border:1.5px solid #fde68a;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="font-size:11.5px;font-weight:700;color:#d97706;text-transform:uppercase;letter-spacing:0.5px;">⏳ Tercapai Sebagian</div>
    <div style="font-size:26px;font-weight:900;color:#92400e;margin-top:4px;" id="statSebagian"><?= $cntSebagian ?></div>
    <div style="font-size:11.5px;color:#d97706;margin-top:2px;">Proses pemenuhan target</div>
  </div>

  <div style="background:#fff;border:1.5px solid #cbd5e1;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">✕ Belum Tercapai</div>
    <div style="font-size:26px;font-weight:900;color:#475569;margin-top:4px;" id="statBelum"><?= $cntBelum ?></div>
    <div style="font-size:11.5px;color:#94a3b8;margin-top:2px;">Membutuhkan pengendalian</div>
  </div>
</div>

<!-- Progress Bar Evaluasi -->
<div style="background:#fff;border:1.5px solid #c7d2fe;border-radius:14px;padding:18px 22px;margin-bottom:28px;box-shadow:0 4px 16px rgba(79,70,229,0.06);">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
    <span style="font-size:13px;font-weight:800;color:#3730a3;">Kelengkapan Narasi Evaluasi Indikator</span>
    <span style="font-size:14px;font-weight:900;color:#4f46e5;" id="evalPctText"><?= $pctEvaluasi ?>% Terisi</span>
  </div>
  <div style="height:10px;background:#e0e7ff;border-radius:99px;overflow:hidden;">
    <div id="evalProgressBar" style="height:100%;width:<?= $pctEvaluasi ?>%;background:linear-gradient(90deg,#4f46e5,#10b981);border-radius:99px;transition:width 0.4s ease;"></div>
  </div>
</div>

<!-- SECTION 1: FORM EVALUASI PER INDIKATOR DARI PENETAPAN -->
<div style="margin-bottom:36px;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <div>
      <h3 style="font-size:18px;font-weight:900;color:#1e293b;margin:0 0 2px;">
        1. Form Evaluasi Hasil Standar &amp; Indikator
      </h3>
      <p style="font-size:13px;color:#64748b;margin:0;">
        Seluruh indikator dan target dari Penetapan dimuat otomatis. Status pelaksanaan diambil langsung dari modul Pelaksanaan.
      </p>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:20px;">
    <?php foreach ($details as $idx => $d): ?>
    <?php
    $pdId      = (int)$d['id'];
    $kid       = (int)$d['kriteria_id'];
    $stPel     = $d['status_pelaksanaan'] ?? 'belum';
    $stCapaian = $d['status_capaian'] ?? 'belum_tercapai';
    $evalTeks  = $d['evaluasi_teks'] ?? '';
    $displayKode = !empty($d['kode']) ? $d['kode'] : (!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD-' . ($idx + 1));
    ?>
    <div class="eval-card" id="card_eval_<?= $pdId ?>"
         style="background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.03);">
      
      <!-- Card Header -->
      <div style="padding:16px 22px;background:#f8fafc;border-bottom:1.5px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:12px;">
          <div style="padding:5px 12px;background:#1a237e;color:#fff;border-radius:8px;font-weight:800;font-size:12px;">
            <?= htmlspecialchars($displayKode) ?>
          </div>
          <div>
            <div style="display:flex;align-items:center;gap:8px;">
              <h4 style="font-size:15px;font-weight:800;color:#1e293b;margin:0;"><?= htmlspecialchars($d['kriteria_nama']) ?></h4>
              <span style="font-size:11px;font-weight:700;color:#4f46e5;background:#eef2ff;padding:2px 8px;border-radius:6px;">
                Indikator #<?= $idx + 1 ?>
              </span>
            </div>
            <div style="font-size:11.5px;color:#64748b;"><?= htmlspecialchars($d['kriteria_deskripsi'] ?? '') ?></div>
          </div>
        </div>

        <div style="display:flex;align-items:center;gap:10px;">
          <!-- Status Pelaksanaan Badge (Realtime dari Pelaksanaan) -->
          <div style="text-align:right;">
            <div style="font-size:10.5px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:2px;">Status Pelaksanaan Fakultas:</div>
            <span style="font-size:11.5px;font-weight:800;padding:4px 12px;border-radius:20px;
                         <?= $stPel === 'terlaksana' ? 'background:#ecfdf5;color:#059669;' : ($stPel === 'proses' ? 'background:#fffbeb;color:#d97706;' : 'background:#f1f5f9;color:#64748b;') ?>">
              <?= $stPel === 'terlaksana' ? '✓ Sudah Dilaksanakan' : ($stPel === 'proses' ? '⏳ Dalam Proses' : '✕ Belum Dilaksanakan') ?>
            </span>
          </div>

          <!-- Tombol AI Single Indikator -->
          <button type="button" onclick="generateSingleAI(<?= $pdId ?>, <?= $kid ?>)" id="btn_ai_<?= $pdId ?>"
                  style="background:#f5f3ff;border:1.5px solid #c4b5fd;color:#6d28d9;padding:8px 14px;border-radius:9px;font-size:12px;font-weight:800;cursor:pointer;display:flex;align-items:center;gap:6px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            ✨ Generate AI
          </button>
        </div>
      </div>

      <!-- Card Body -->
      <div style="padding:22px;">
        
        <!-- Info Target Standar & Indikator dari Penetapan -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px;margin-bottom:18px;">
          <?php if (!empty($d['strategi'])): ?>
          <div style="background:#f8faff;border-left:3px solid #4f46e5;padding:10px 14px;border-radius:0 8px 8px 0;">
            <div style="font-size:10.5px;font-weight:800;text-transform:uppercase;color:#4f46e5;margin-bottom:2px;">📌 Aturan / Dasar Hukum</div>
            <div style="font-size:12.5px;color:#1e293b;line-height:1.5;"><?= htmlspecialchars($d['strategi']) ?></div>
          </div>
          <?php endif; ?>

          <div style="background:#f0fdf4;border-left:4px solid #059669;padding:10px 14px;border-radius:0 8px 8px 0;">
            <div style="font-size:11.5px;font-weight:800;text-transform:uppercase;color:#065f46;margin-bottom:2px;letter-spacing:0.5px;">🎯 Pernyataan Standar</div>
            <div style="font-size:14.5px;color:#1e293b;line-height:1.6;font-weight:500;"><?= !empty($d['target_capaian']) ? htmlspecialchars($d['target_capaian']) : '<span style="color:#94a3b8;font-style:italic;">Belum diisi</span>' ?></div>
          </div>

          <div style="background:#fffbeb;border-left:4px solid #d97706;padding:10px 14px;border-radius:0 8px 8px 0;">
            <div style="font-size:11.5px;font-weight:800;text-transform:uppercase;color:#92400e;margin-bottom:2px;letter-spacing:0.5px;">📊 Target / Indikator Ketercapaian</div>
            <div style="font-size:14.5px;color:#1e293b;line-height:1.6;font-weight:500;"><?= !empty($d['indikator']) ? htmlspecialchars($d['indikator']) : '<span style="color:#94a3b8;font-style:italic;">Belum diisi</span>' ?></div>
          </div>
        </div>

        <?php if (!empty($d['catatan_pelaksanaan'])): ?>
        <div style="background:#f1f5f9;border:1px solid #cbd5e1;border-radius:10px;padding:10px 14px;margin-bottom:18px;">
          <div style="font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;margin-bottom:2px;">Realisasi / Catatan Pelaksanaan Fakultas:</div>
          <div style="font-size:12.5px;color:#334155;"><?= htmlspecialchars($d['catatan_pelaksanaan']) ?></div>
        </div>
        <?php endif; ?>
        <?php
        $capPel = trim($d['capaian_angka_pelaksanaan'] ?? '');
        $capEv  = trim($d['capaian_angka_evaluasi'] ?? '');
        $hasPelAngka = ($capPel !== '');
        
        // Perhitungan awal perbandingan
        $badgeText  = '—';
        $badgeStyle = 'background:#f1f5f9;color:#64748b;border:1px solid #cbd5e1;';
        if ($hasPelAngka && $capEv === '') {
            $badgeText  = '⚠️ Wajib Diisi';
            $badgeStyle = 'background:#fffbeb;color:#b45309;border:1px solid #fde68a;font-weight:800;';
        } elseif ($capPel !== '' && $capEv !== '') {
            $pNum = (float)preg_replace('/[^0-9.]/', '', $capPel);
            $eNum = (float)preg_replace('/[^0-9.]/', '', $capEv);
            if ($pNum > $eNum) {
                $diff = round($pNum - $eNum, 2);
                $badgeText  = '📈 Melebihi Target (+' . $diff . ')';
                $badgeStyle = 'background:#ecfdf5;color:#047857;border:1.5px solid #a7f3d0;font-weight:900;';
            } elseif ($pNum == $eNum) {
                $badgeText  = '🎯 Sesuai Target (100%)';
                $badgeStyle = 'background:#eff6ff;color:#1d4ed8;border:1.5px solid #bfdbfe;font-weight:900;';
            } else {
                $diff = round($eNum - $pNum, 2);
                $badgeText  = '📉 Belum Mencapai (-' . $diff . ')';
                $badgeStyle = 'background:#fef2f2;color:#b91c1c;border:1.5px solid #fca5a5;font-weight:900;';
            }
        }
        ?>

        <!-- BOX PERBANDINGAN ANGKA KUANTITATIF (PELAKSANAAN VS EVALUASI) -->
        <div style="background:<?= $hasPelAngka ? '#fffbeb' : '#f8fafc' ?>;border:1.5px solid <?= $hasPelAngka ? '#fde68a' : '#e2e8f0' ?>;border-radius:12px;padding:14px 16px;margin-bottom:18px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;flex-wrap:wrap;gap:8px;">
            <div style="font-size:12.5px;font-weight:800;color:<?= $hasPelAngka ? '#92400e' : '#334155' ?>;display:flex;align-items:center;gap:6px;">
              📊 Perbandingan Angka Kuantitatif (Pelaksanaan vs Target Evaluasi)
            </div>
            <?php if ($hasPelAngka): ?>
            <span style="font-size:11px;font-weight:800;background:#fef3c7;color:#b45309;padding:3px 10px;border-radius:12px;border:1px solid #fcd34d;">
              ⚠️ Pelaksanaan Mengisi Angka: <?= htmlspecialchars($capPel) ?> (Evaluasi Harap Diisi)
            </span>
            <?php endif; ?>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr 200px;gap:12px;align-items:center;">
            <!-- Realisasi Pelaksanaan -->
            <div style="background:#fff;padding:10px 14px;border-radius:9px;border:1px solid #cbd5e1;">
              <div style="font-size:11px;color:#64748b;font-weight:700;margin-bottom:2px;">Realisasi Angka Pelaksanaan:</div>
              <div style="font-size:15px;font-weight:900;color:<?= $hasPelAngka ? '#059669' : '#94a3b8' ?>;" id="pelAngkaDisplay_<?= $pdId ?>" data-value="<?= htmlspecialchars($capPel) ?>">
                <?= $hasPelAngka ? htmlspecialchars($capPel) : '<i style="font-size:12px;font-weight:400;">Belum diisi di Pelaksanaan</i>' ?>
              </div>
            </div>

            <!-- Target / Realisasi Evaluasi -->
            <div>
              <label style="font-size:11px;color:#1e293b;font-weight:800;margin-bottom:3px;display:block;">
                Angka Evaluasi Target / Harapan <?= $hasPelAngka ? '<span style="color:#ef4444;">*</span>' : '(Opsional)' ?>:
              </label>
              <input type="text" id="eval_capaian_angka_<?= $pdId ?>" class="form-control"
                     placeholder="Misal: 80 atau 100%"
                     value="<?= htmlspecialchars($capEv) ?>"
                     onchange="saveEvalField(<?= $pdId ?>, <?= $kid ?>, 'capaian_angka', this.value); updateAngkaComparison(<?= $pdId ?>)"
                     style="font-weight:800;color:#1e3a8a;border:1.5px solid #93c5fd;background:#eff6ff;font-size:13px;padding:7px 12px;">
            </div>

            <!-- Hasil Perbandingan Badge -->
            <div style="text-align:center;">
              <div style="font-size:10.5px;color:#64748b;font-weight:700;margin-bottom:3px;text-transform:uppercase;">Hasil Perbandingan:</div>
              <span id="angkaComparisonBadge_<?= $pdId ?>" style="display:inline-block;padding:6px 12px;border-radius:20px;font-size:11.5px;<?= $badgeStyle ?>">
                <?= $badgeText ?>
              </span>
            </div>
          </div>
        </div>

        <!-- Form Isian Evaluasi Per Indikator -->
        <div style="margin-bottom:16px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <label style="font-size:12.5px;font-weight:800;color:#1e293b;display:flex;align-items:center;gap:6px;">
              <span>📝 Form Isian Evaluasi Indikator</span>
              <span style="color:#ef4444;">*</span>
            </label>
            <span id="saveStatus_<?= $pdId ?>" style="font-size:11.5px;color:#94a3b8;"></span>
          </div>
          <textarea id="eval_teks_<?= $pdId ?>" class="form-control" rows="4"
                    placeholder="Tuliskan analisis evaluasi capaian, akar masalah / faktor pendukung, serta rekomendasi tindak lanjut..."
                    onchange="saveEvalField(<?= $pdId ?>, <?= $kid ?>, 'evaluasi_teks', this.value)"
                    style="font-size:13px;line-height:1.6;"><?= htmlspecialchars($evalTeks) ?></textarea>
        </div>

        <!-- Status Capaian Selector Pills -->
        <div>
          <div style="font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Kesimpulan Status Capaian Evaluasi:</div>
          <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <label style="flex:1;min-width:140px;cursor:pointer;">
              <input type="radio" name="status_capaian_<?= $pdId ?>" value="tercapai" <?= $stCapaian === 'tercapai' ? 'checked' : '' ?>
                     onchange="saveEvalField(<?= $pdId ?>, <?= $kid ?>, 'status_capaian', 'tercapai')" style="display:none;" id="rad_tercapai_<?= $pdId ?>">
              <div id="pill_tercapai_<?= $pdId ?>"
                   style="padding:9px 14px;border-radius:9px;border:1.5px solid <?= $stCapaian === 'tercapai' ? '#10b981' : '#e2e8f0' ?>;background:<?= $stCapaian === 'tercapai' ? '#ecfdf5' : '#fff' ?>;color:<?= $stCapaian === 'tercapai' ? '#065f46' : '#64748b' ?>;font-weight:700;font-size:12px;display:flex;align-items:center;gap:6px;transition:all 0.2s;">
                <span>✓</span> Tercapai Penuh
              </div>
            </label>

            <label style="flex:1;min-width:140px;cursor:pointer;">
              <input type="radio" name="status_capaian_<?= $pdId ?>" value="sebagian" <?= $stCapaian === 'sebagian' ? 'checked' : '' ?>
                     onchange="saveEvalField(<?= $pdId ?>, <?= $kid ?>, 'status_capaian', 'sebagian')" style="display:none;" id="rad_sebagian_<?= $pdId ?>">
              <div id="pill_sebagian_<?= $pdId ?>"
                   style="padding:9px 14px;border-radius:9px;border:1.5px solid <?= $stCapaian === 'sebagian' ? '#f59e0b' : '#e2e8f0' ?>;background:<?= $stCapaian === 'sebagian' ? '#fffbeb' : '#fff' ?>;color:<?= $stCapaian === 'sebagian' ? '#92400e' : '#64748b' ?>;font-weight:700;font-size:12px;display:flex;align-items:center;gap:6px;transition:all 0.2s;">
                <span>⏳</span> Tercapai Sebagian
              </div>
            </label>

            <label style="flex:1;min-width:140px;cursor:pointer;">
              <input type="radio" name="status_capaian_<?= $pdId ?>" value="belum_tercapai" <?= $stCapaian === 'belum_tercapai' ? 'checked' : '' ?>
                     onchange="saveEvalField(<?= $pdId ?>, <?= $kid ?>, 'status_capaian', 'belum_tercapai')" style="display:none;" id="rad_belum_<?= $pdId ?>">
              <div id="pill_belum_<?= $pdId ?>"
                   style="padding:9px 14px;border-radius:9px;border:1.5px solid <?= $stCapaian === 'belum_tercapai' ? '#94a3b8' : '#e2e8f0' ?>;background:<?= $stCapaian === 'belum_tercapai' ? '#f1f5f9' : '#fff' ?>;color:<?= $stCapaian === 'belum_tercapai' ? '#475569' : '#64748b' ?>;font-weight:700;font-size:12px;display:flex;align-items:center;gap:6px;transition:all 0.2s;">
                <span>✕</span> Belum Tercapai
              </div>
            </label>
          </div>
        </div>

      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- SECTION 2: DOKUMENTASI RAPAT / RTM EVALUASI & NOTULENSI -->
<?php
$rapatTopik        = !empty($evaluasi['rapat_topik']) ? $evaluasi['rapat_topik'] : $evaluasi['judul'];
$rapatTa           = !empty($evaluasi['rapat_tahun_ajaran']) ? $evaluasi['rapat_tahun_ajaran'] : ($penetapan['ta_nama'] ?? (date('Y') . '/' . (date('Y') + 1)));
$rapatSemester     = !empty($evaluasi['rapat_semester']) ? $evaluasi['rapat_semester'] : ($penetapan['semester'] ?? 'Genap');
$rapatJenis        = !empty($evaluasi['rapat_jenis']) ? $evaluasi['rapat_jenis'] : 'Rapat';
$rapatKategori     = !empty($evaluasi['rapat_kategori']) ? $evaluasi['rapat_kategori'] : 'Fakultas';
$rapatTempat       = !empty($evaluasi['rapat_tempat']) ? $evaluasi['rapat_tempat'] : 'Ruang Rapat Dekanat';
$rapatTanggal      = !empty($evaluasi['rapat_tanggal']) ? $evaluasi['rapat_tanggal'] : date('Y-m-d');
$rapatJamMulai     = !empty($evaluasi['rapat_jam_mulai']) ? substr($evaluasi['rapat_jam_mulai'], 0, 5) : '09:00';
$rapatJamSelesai   = !empty($evaluasi['rapat_jam_selesai']) ? substr($evaluasi['rapat_jam_selesai'], 0, 5) : '11:00';
$rapatKetuaId      = !empty($evaluasi['rapat_ketua_id']) ? $evaluasi['rapat_ketua_id'] : 3;
$rapatNotulisId    = !empty($evaluasi['rapat_notulis_id']) ? $evaluasi['rapat_notulis_id'] : 1;
$rapatLampiranLink = $evaluasi['rapat_lampiran_link'] ?? '';
$rapatKriteriaStr  = $evaluasi['rapat_kriteria'] ?? '';
$selectedKriteria  = array_map('trim', explode(',', $rapatKriteriaStr));

$notulRapatId      = !empty($evaluasi['notulensi_rapat_id']) ? (int)$evaluasi['notulensi_rapat_id'] : 0;
$notulViewUrl      = "http://notulensi.test:8080/rapat/detail/" . $notulRapatId;
$notulPdfUrl       = "http://notulensi.test:8080/pdf/view/" . $notulRapatId;

$kriteriaStandardList = [
    "1. Visi, Misi, Tujuan, dan Sasaran",
    "2. Tata Pamong dan Kerja Sama",
    "3. Mahasiswa",
    "4. SDM",
    "5. Keuangan, Sarana, dan Prasarana",
    "6. Pendidikan",
    "7. Penelitian",
    "8. Pengabdian Kepada Masyarakat",
    "9. Luaran dan Capaian Tridharma"
];
?>

<div style="background:#fff;border:1.5px solid #c7d2fe;border-radius:16px;padding:24px 28px;margin-bottom:30px;box-shadow:0 4px 16px rgba(79,70,229,0.06);">
  
  <!-- Header Section 2 & Sync Bar -->
  <div style="margin-bottom:20px;border-bottom:1.5px solid #e0e7ff;padding-bottom:16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;">
    <div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
        <h3 style="font-size:18px;font-weight:900;color:#1e293b;margin:0;">
          2. Dokumentasi Rapat Tinjauan Manajemen (RTM) &amp; Notulensi
        </h3>
        <span style="font-size:11px;font-weight:800;background:#eef2ff;color:#4f46e5;border:1px solid #c7d2fe;padding:2px 8px;border-radius:20px;">
          API Notulensi Terintegrasi
        </span>
      </div>
      <p style="font-size:13px;color:#64748b;margin:0;">
        Kelola informasi rapat, absensi kehadiran, dan notulensi kesepakatan. Data tersimpan di PPEPP dan tersinkron otomatis ke Sistem Notulensi.
      </p>
    </div>

    <!-- Sync Controls & Direct Links -->
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
      <div id="notulensiSyncLinkContainer">
        <?php if ($notulRapatId > 0): ?>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
          <span class="badge" style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;font-size:11.5px;padding:6px 12px;border-radius:8px;font-weight:800;">
            ✓ Tersinkron ke Notulensi (#<?= $notulRapatId ?>)
          </span>
          <a href="<?= $notulViewUrl ?>" target="_blank" class="btn btn-sm btn-outline"
             style="background:#fff;border-color:#c7d2fe;color:#4f46e5;font-weight:700;font-size:12px;text-decoration:none;display:flex;align-items:center;gap:4px;">
            Buka di Notulensi ↗
          </a>
          <a href="<?= $notulPdfUrl ?>" target="_blank" class="btn btn-sm btn-outline"
             style="background:#fff;border-color:#fecaca;color:#dc2626;font-weight:700;font-size:12px;text-decoration:none;display:flex;align-items:center;gap:4px;">
            📄 Cetak PDF ↗
          </a>
        </div>
        <?php else: ?>
        <span style="font-size:11.5px;color:#94a3b8;font-style:italic;">
          Belum tersinkron ke Notulensi
        </span>
        <?php endif; ?>
      </div>

      <button type="button" onclick="saveRapatData(true)" class="btn btn-primary btn-sm" id="btnSyncNotulensi"
              style="background:linear-gradient(135deg,#4f46e5,#6366f1);border:none;font-weight:800;font-size:12px;padding:7px 16px;box-shadow:0 3px 10px rgba(79,70,229,0.3);display:flex;align-items:center;gap:6px;cursor:pointer;">
        💾 Simpan &amp; Sinkronkan
      </button>
      <span id="notulensiSaveStatus" style="font-size:12px;font-weight:700;min-width:90px;"></span>
    </div>
  </div>

  <!-- GRID FORM NOTULENSI LENGKAP (SESUAI NOTULENSI APP) -->
  <div style="display:flex;flex-direction:column;gap:22px;">

    <!-- CARD 1: INFORMASI UMUM RAPAT -->
    <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
      <h4 style="font-size:14px;font-weight:800;color:#1e293b;margin:0 0 14px;display:flex;align-items:center;gap:8px;">
        <span>📌</span> Informasi Umum Rapat
      </h4>

      <div style="display:grid;grid-template-columns:1fr;gap:14px;">
        <div>
          <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">
            Topik / Judul Kegiatan Rapat <span style="color:#ef4444;">*</span>
          </label>
          <input type="text" id="rapatTopikInput" class="form-control" style="font-size:13px;font-weight:600;"
                 value="<?= htmlspecialchars($rapatTopik) ?>" onchange="saveRapatData()" placeholder="Contoh: RTM Evaluasi Capaian Standar SPMI Semester Genap 2025/2026">
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:14px;">
          <div>
            <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">
              Tahun Ajaran <span style="color:#ef4444;">*</span>
            </label>
            <select id="rapatTaInput" class="form-control" style="font-size:12.5px;" onchange="saveRapatData()">
              <?php 
                $startYear = 2021;
                $currentYear = (int)date('Y');
                for ($i = $startYear; $i <= $currentYear + 2; $i++) {
                    $taVal = $i . '/' . ($i + 1);
                    $sel = ($taVal === $rapatTa) ? 'selected' : '';
                    echo "<option value=\"$taVal\" $sel>$taVal</option>";
                }
              ?>
            </select>
          </div>

          <div>
            <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">
              Semester <span style="color:#ef4444;">*</span>
            </label>
            <select id="rapatSemesterInput" class="form-control" style="font-size:12.5px;" onchange="saveRapatData()">
              <option value="Ganjil" <?= $rapatSemester === 'Ganjil' ? 'selected' : '' ?>>Ganjil (Agustus - Januari)</option>
              <option value="Genap" <?= $rapatSemester === 'Genap' ? 'selected' : '' ?>>Genap (Februari - Juli)</option>
            </select>
          </div>

          <div>
            <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">
              Jenis Kegiatan
            </label>
            <div style="display:flex;align-items:center;gap:14px;padding-top:6px;">
              <label style="font-size:12.5px;font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="radio" name="rapat_jenis" value="Rapat" <?= $rapatJenis === 'Rapat' ? 'checked' : '' ?> onchange="saveRapatData()">
                Rapat
              </label>
              <label style="font-size:12.5px;font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="radio" name="rapat_jenis" value="Aktivitas Lain" <?= $rapatJenis === 'Aktivitas Lain' ? 'checked' : '' ?> onchange="saveRapatData()">
                Aktivitas Lain
              </label>
              <label style="font-size:12.5px;font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="radio" name="rapat_jenis" value="RTM" <?= $rapatJenis === 'RTM' ? 'checked' : '' ?> onchange="saveRapatData()">
                RTM
              </label>
            </div>
          </div>

          <div>
            <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">
              Tingkat / Kategori
            </label>
            <select id="rapatKategoriInput" class="form-control" style="font-size:12.5px;" onchange="saveRapatData()">
              <option value="Fakultas" <?= $rapatKategori === 'Fakultas' ? 'selected' : '' ?>>Fakultas</option>
              <option value="Program Studi" <?= $rapatKategori === 'Program Studi' ? 'selected' : '' ?>>Program Studi</option>
              <option value="RTM" <?= $rapatKategori === 'RTM' ? 'selected' : '' ?>>RTM</option>
            </select>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:14px;">
          <div>
            <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">
              Tempat / Lokasi <span style="color:#ef4444;">*</span>
            </label>
            <input type="text" id="rapatTempatInput" class="form-control" style="font-size:12.5px;"
                   value="<?= htmlspecialchars($rapatTempat) ?>" onchange="saveRapatData()" placeholder="Ruang Rapat Dekanat / Ruang 8.2 / Zoom">
          </div>

          <div>
            <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">
              Tanggal Pelaksanaan <span style="color:#ef4444;">*</span>
            </label>
            <input type="date" id="rapatTanggalInput" class="form-control" style="font-size:12.5px;"
                   value="<?= htmlspecialchars($rapatTanggal) ?>" onchange="saveRapatData()">
          </div>

          <div>
            <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">
              Jam Mulai
            </label>
            <input type="time" id="rapatJamMulaiInput" class="form-control" style="font-size:12.5px;"
                   value="<?= htmlspecialchars($rapatJamMulai) ?>" onchange="saveRapatData()">
          </div>

          <div>
            <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">
              Jam Selesai
            </label>
            <input type="time" id="rapatJamSelesaiInput" class="form-control" style="font-size:12.5px;"
                   value="<?= htmlspecialchars($rapatJamSelesai) ?>" onchange="saveRapatData()">
          </div>
        </div>

        <!-- Link Lampiran Dokumen -->
        <div>
          <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">
            Tautan / Link Lampiran Dokumen <span style="font-weight:400;color:#64748b;">(Opsional - Google Drive/Cloud)</span>
          </label>
          <input type="url" id="rapatLampiranLinkInput" class="form-control" style="font-size:12.5px;"
                 value="<?= htmlspecialchars($rapatLampiranLink) ?>" onchange="saveRapatData()" placeholder="https://drive.google.com/drive/folders/...">
        </div>

      </div>
    </div>

    <!-- CARD 2: PIMPINAN RAPAT & KRITERIA STANDAR SPMI -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
      
      <!-- Pimpinan Rapat -->
      <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
        <h4 style="font-size:14px;font-weight:800;color:#1e293b;margin:0 0 14px;display:flex;align-items:center;gap:8px;">
          <span>👤</span> Pimpinan Rapat
        </h4>

        <div style="display:flex;flex-direction:column;gap:12px;">
          <div>
            <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">
              Ketua Rapat (Kaprodi / Dekan) <span style="color:#ef4444;">*</span>
            </label>
            <select id="rapatKetuaInput" class="form-control" style="font-size:12.5px;" onchange="saveRapatData()">
              <option value="">— Pilih Ketua Rapat —</option>
              <?php foreach (($allUsers ?? []) as $u): ?>
              <option value="<?= $u['id'] ?>" <?= $u['id'] == $rapatKetuaId ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['nama_lengkap']) ?> (<?= htmlspecialchars(ucwords($u['role'])) ?>)
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">
              Notulis Rapat (Sekprodi / Dosen) <span style="color:#ef4444;">*</span>
            </label>
            <select id="rapatNotulisInput" class="form-control" style="font-size:12.5px;" onchange="saveRapatData()">
              <option value="">— Pilih Notulis Rapat —</option>
              <?php foreach (($allUsers ?? []) as $u): ?>
              <option value="<?= $u['id'] ?>" <?= $u['id'] == $rapatNotulisId ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['nama_lengkap']) ?> (<?= htmlspecialchars(ucwords($u['role'])) ?>)
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <!-- Kriteria Standar SPMI Terkait -->
      <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
        <h4 style="font-size:14px;font-weight:800;color:#1e293b;margin:0 0 10px;display:flex;align-items:center;gap:8px;">
          <span>🎯</span> Kriteria Standar SPMI yang Dievaluasi
        </h4>
        <div style="font-size:11.5px;color:#64748b;margin-bottom:10px;">
          Pilih kriteria yang dibahas dalam rapat evaluasi / RTM ini:
        </div>

        <div style="max-height:165px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:8px;padding:10px;background:#fff;display:flex;flex-direction:column;gap:6px;">
          <?php foreach ($kriteriaStandardList as $kIdx => $kName): ?>
          <?php $isKChecked = in_array($kName, $selectedKriteria); ?>
          <label style="font-size:12px;font-weight:600;color:#334155;display:flex;align-items:center;gap:8px;cursor:pointer;padding:2px 4px;border-radius:4px;">
            <input type="checkbox" class="rapat-kriteria-cb" value="<?= htmlspecialchars($kName) ?>" <?= $isKChecked ? 'checked' : '' ?> onchange="saveRapatData()">
            <span><?= htmlspecialchars($kName) ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

    </div>

    <!-- CARD 3: ABSENSI KEHADIRAN PESERTA & NOTULENSI -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">
      
      <!-- Absensi Peserta Rapat -->
      <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
          <h4 style="font-size:14px;font-weight:800;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px;">
            <span>👥</span> Absensi Kehadiran Peserta Rapat
          </h4>
          <span id="absensiCountBadge" style="font-size:11.5px;font-weight:800;color:#4f46e5;background:#e0e7ff;padding:3px 10px;border-radius:20px;">
            <?= count($absensiList) ?> Peserta
          </span>
        </div>
        <div style="font-size:11.5px;color:#64748b;margin-bottom:12px;">
          Centang status kehadiran untuk masing-masing peserta rapat.
        </div>

        <div id="absensiListContainer" style="display:flex;flex-direction:column;gap:8px;max-height:380px;overflow-y:auto;padding-right:4px;">
          <?php foreach ($absensiList as $aIdx => $a): ?>
          <div class="absensi-item-row" data-idx="<?= $aIdx ?>"
               style="background:#fff;border:1px solid #e2e8f0;border-radius:9px;padding:8px 12px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
            <div style="font-size:12px;font-weight:700;color:#1e293b;" class="absensi-nama-text">
              <?= htmlspecialchars($a['nama']) ?>
            </div>
            
            <div style="display:flex;gap:4px;align-items:center;">
              <button type="button" onclick="setAbsensiStatus(<?= $aIdx ?>, 'HADIR')" class="abs-btn-hadir"
                      style="padding:4px 9px;border-radius:6px;font-size:11px;font-weight:800;cursor:pointer;border:1px solid <?= ($a['status'] ?? '') === 'HADIR' ? '#10b981' : '#e2e8f0' ?>;background:<?= ($a['status'] ?? '') === 'HADIR' ? '#ecfdf5' : '#fff' ?>;color:<?= ($a['status'] ?? '') === 'HADIR' ? '#065f46' : '#64748b' ?>;">
                ✓ HADIR
              </button>
              <button type="button" onclick="setAbsensiStatus(<?= $aIdx ?>, 'TIDAK HADIR')" class="abs-btn-tidak"
                      style="padding:4px 9px;border-radius:6px;font-size:11px;font-weight:800;cursor:pointer;border:1px solid <?= ($a['status'] ?? '') === 'TIDAK HADIR' ? '#ef4444' : '#e2e8f0' ?>;background:<?= ($a['status'] ?? '') === 'TIDAK HADIR' ? '#fef2f2' : '#fff' ?>;color:<?= ($a['status'] ?? '') === 'TIDAK HADIR' ? '#991b1b' : '#64748b' ?>;">
                ✕ TIDAK
              </button>
              <button type="button" onclick="setAbsensiStatus(<?= $aIdx ?>, 'IZIN')" class="abs-btn-izin"
                      style="padding:4px 9px;border-radius:6px;font-size:11px;font-weight:800;cursor:pointer;border:1px solid <?= ($a['status'] ?? '') === 'IZIN' ? '#f59e0b' : '#e2e8f0' ?>;background:<?= ($a['status'] ?? '') === 'IZIN' ? '#fffbeb' : '#fff' ?>;color:<?= ($a['status'] ?? '') === 'IZIN' ? '#92400e' : '#64748b' ?>;">
                ⏳ IZIN
              </button>
              <button type="button" onclick="removePeserta(<?= $aIdx ?>)" title="Hapus peserta ini"
                      style="padding:4px 7px;border-radius:6px;font-size:11px;cursor:pointer;border:1px solid #fee2e2;background:#fff5f5;color:#ef4444;font-weight:700;">
                ✕
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;">
          <input type="text" id="newPesertaInput" class="form-control" placeholder="Ketik nama peserta tambahan..." style="font-size:12px;flex:1;min-width:180px;">
          <button type="button" onclick="addNewPeserta()" class="btn btn-outline btn-sm" style="font-weight:700;white-space:nowrap;">
            + Tambah
          </button>
          <button type="button" onclick="loadAllDosen()" class="btn btn-outline btn-sm"
                  style="font-weight:700;color:#4f46e5;border-color:#c7d2fe;background:#eef2ff;white-space:nowrap;"
                  title="Muat seluruh 10 dosen fakultas ke daftar absensi rapat">
            👥 Muat Semua Dosen
          </button>
        </div>
      </div>

      <!-- Notulensi Pembahasan & Kesepakatan -->
      <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
          <h4 style="font-size:14px;font-weight:800;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px;">
            <span>📝</span> Notulensi Pembahasan & Kesepakatan Rapat
          </h4>
          <span style="font-size:11px;font-weight:700;color:#64748b;">Tersinkron Otomatis</span>
        </div>
        <div style="font-size:11.5px;color:#64748b;margin-bottom:12px;">
          Catat jalannya rapat, pokok bahasan, hasil evaluasi capaian, serta keputusan/tindak lanjut.
        </div>
        <textarea id="notulensiInput" class="form-control" rows="12" style="font-size:13px;line-height:1.6;font-family:inherit;background:#fff;"
                  placeholder="Contoh format notulensi:&#10;&#10;Pokok Pembahasan:&#10;1. Evaluasi pencapaian standar pendidikan...&#10;&#10;Hasil Evaluasi:&#10;...&#10;&#10;Keputusan Rapat:&#10;1. ..."
                  oninput="debounceSaveRapat()"><?= htmlspecialchars($evaluasi['notulensi'] ?? '') ?></textarea>
      </div>

    </div>

    <!-- CARD 4: FOTO DOKUMENTASI KEGIATAN RAPAT -->
    <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;flex-wrap:wrap;gap:10px;">
        <div>
          <h4 style="font-size:14px;font-weight:800;color:#1e293b;margin:0 0 2px;display:flex;align-items:center;gap:8px;">
            <span>📷</span> Foto / Dokumentasi Rapat Kegiatan
          </h4>
          <div style="font-size:11.5px;color:#64748b;">Format: JPG, PNG, WEBP (Maks 10 MB per foto). Klik foto untuk memperbesar.</div>
        </div>

        <div>
          <button type="button" onclick="document.getElementById('gambarFileInput').click()" class="btn btn-outline btn-sm"
                  style="color:#059669;border-color:#a7f3d0;background:#fff;font-weight:700;font-size:12px;">
            + Upload Foto Kegiatan
          </button>
          <input type="file" id="gambarFileInput" accept="image/*" style="display:none;" onchange="handleGambarUpload(this)">
        </div>
      </div>

      <!-- Gallery Grid Foto -->
      <div id="gambarGalleryContainer" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:10px;margin-top:12px;">
        <?php foreach ($gambarList as $img): ?>
        <?php
          $imgUrl = !empty($img['url']) ? $img['url'] : (str_starts_with($img['file_path'] ?? '', 'http') ? $img['file_path'] : BASE_URL . '/' . ltrim($img['file_path'] ?? '', '/'));
        ?>
        <div class="gambar-item-thumb" id="thumb_<?= htmlspecialchars($img['id'] ?? '') ?>"
             style="position:relative;border:1.5px solid #cbd5e1;border-radius:9px;overflow:hidden;background:#fff;aspect-ratio:1;cursor:pointer;"
             onclick="previewImageModal('<?= htmlspecialchars($imgUrl) ?>')">
          <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Dokumentasi" style="width:100%;height:100%;object-fit:cover;">
          <button type="button" onclick="event.stopPropagation(); deleteGambarItem('<?= htmlspecialchars($img['id'] ?? $img['file_path']) ?>')"
                  style="position:absolute;top:4px;right:4px;width:22px;height:22px;background:rgba(239,68,68,0.85);color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:11px;display:flex;align-items:center;justify-content:center;">
            ✕
          </button>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</div>

<!-- Bottom Sticky Bar -->
<div style="position:sticky;bottom:0;background:#fff;border-top:1.5px solid #e2e8f0;padding:16px 24px;border-radius:0 0 14px 14px;display:flex;align-items:center;justify-content:space-between;gap:12px;box-shadow:0 -4px 20px rgba(0,0,0,0.06);z-index:40;flex-wrap:wrap;margin-top:12px;">
  <div style="display:flex;align-items:center;gap:10px;">
    <a href="#" onclick="saveAndExit(event)" style="background:none;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:600;color:#64748b;text-decoration:none;display:flex;align-items:center;gap:6px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
      Simpan Draft &amp; Keluar
    </a>
    <span style="font-size:12px;color:#94a3b8;">Perubahan tersimpan otomatis saat Anda mengisi form atau mengklik status</span>
  </div>
  
  <div style="display:flex;align-items:center;gap:10px;">
    <form action="<?= BASE_URL ?>/evaluasi/<?= $evId ?>/finalize" method="POST" style="margin:0;">
      <button type="submit" class="btn btn-success btn-lg" style="font-size:13px;padding:10px 22px;font-weight:800;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Finalisasi Laporan Evaluasi
      </button>
    </form>
  </div>
</div>

<script>
var EVALUASI_ID = <?= (int)$evId ?>;
var BASE_APP    = '<?= BASE_URL ?>';
var CURRENT_ABSENSI = <?= json_encode($absensiList) ?>;
var ALL_USERS = <?= json_encode(array_values(array_map(function($u) {
    return [
        'user_id' => (int)$u['id'],
        'nama'    => $u['nama_lengkap']
    ];
}, $allUsers ?? []))) ?>;

// ==================================================
// 1. Auto-Save Form Isian Evaluasi & Status Capaian
// ==================================================
function saveEvalField(pdId, kid, field, value) {
  var saveEl = document.getElementById('saveStatus_' + pdId);
  if (saveEl) saveEl.textContent = 'Menyimpan...';

  // Update Visual Pills jika field adalah status_capaian
  if (field === 'status_capaian') {
    ['tercapai', 'sebagian', 'belum'].forEach(function(stKey) {
      var fullVal = stKey === 'belum' ? 'belum_tercapai' : stKey;
      var pill = document.getElementById('pill_' + stKey + '_' + pdId);
      var rad  = document.getElementById('rad_' + stKey + '_' + pdId);
      if (pill) {
        if (fullVal === value) {
          if (rad) rad.checked = true;
          if (stKey === 'tercapai') {
            pill.style.borderColor = '#10b981'; pill.style.background = '#ecfdf5'; pill.style.color = '#065f46';
          } else if (stKey === 'sebagian') {
            pill.style.borderColor = '#f59e0b'; pill.style.background = '#fffbeb'; pill.style.color = '#92400e';
          } else {
            pill.style.borderColor = '#94a3b8'; pill.style.background = '#f1f5f9'; pill.style.color = '#475569';
          }
        } else {
          pill.style.borderColor = '#e2e8f0'; pill.style.background = '#fff'; pill.style.color = '#64748b';
        }
      }
    });
  }

  var body = new URLSearchParams();
  body.append('evaluasi_id', EVALUASI_ID);
  body.append('penetapan_detail_id', pdId);
  body.append('kriteria_id', kid);
  body.append('field', field);
  body.append('value', value);

  fetch(BASE_APP + '/evaluasi/save-detail', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (saveEl) {
      if (data.success) {
        saveEl.textContent = '✓ Tersimpan';
        saveEl.style.color = '#059669';
        setTimeout(function(){ if (saveEl) saveEl.textContent = ''; }, 2000);
      } else {
        saveEl.textContent = '✕ Gagal';
        saveEl.style.color = '#ef4444';
      }
    }
    recalculateStats();
  });
}

// ==================================================
// 2. Generate AI untuk Single Indikator
// ==================================================
function generateSingleAI(pdId, kid) {
  if (typeof window.USER_HAS_API_KEY !== 'undefined' && !window.USER_HAS_API_KEY) {
    window.showAiWarningModal('🔑 API Key Gemini belum terpasang. Silakan masukkan API Key Gemini Anda terlebih dahulu di menu Pengaturan.', 'missing_key');
    return;
  }

  var btn = document.getElementById('btn_ai_' + pdId);
  var txtArea = document.getElementById('eval_teks_' + pdId);
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<span style="animation:spin 0.8s linear infinite;">⏳</span> Menganalisis...';
  }

  var body = new URLSearchParams();
  body.append('evaluasi_id', EVALUASI_ID);
  body.append('penetapan_detail_id', pdId);
  body.append('kriteria_id', kid);

  fetch(BASE_APP + '/evaluasi/generate-ai', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg> ✨ Generate AI';
    }
    if (data.success && data.evaluasi_teks) {
      if (txtArea) txtArea.value = data.evaluasi_teks;
      if (data.status_capaian) {
        saveEvalField(pdId, kid, 'status_capaian', data.status_capaian);
      }
      recalculateStats();
    } else {
      window.showAiWarningModal(data.message || 'Gagal generate evaluasi.', data.error_type);
    }
  })
  .catch(function(err) {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '✨ Generate AI';
    }
    window.showAiWarningModal('Koneksi Error: ' + err.message);
  });
}

// ==================================================
// 3. Generate Semua Evaluasi AI Sekaligus (1-Klik)
// ==================================================
function generateAllAIEvaluasi() {
  if (typeof window.USER_HAS_API_KEY !== 'undefined' && !window.USER_HAS_API_KEY) {
    window.showAiWarningModal('🔑 API Key Gemini belum terpasang. Silakan masukkan API Key Gemini Anda terlebih dahulu di menu Pengaturan.', 'missing_key');
    return;
  }

  var btn = document.getElementById('btnGenerateAllAI');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<span style="animation:spin 0.8s linear infinite;">⏳</span> Memproses Seluruh Indikator...';
  }

  var body = new URLSearchParams();
  body.append('evaluasi_id', EVALUASI_ID);

  fetch(BASE_APP + '/evaluasi/generate-all-ai', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg> ✨ Generate Semua Evaluasi AI (1-Klik)';
    }
    if (data.success && data.generated) {
      Object.keys(data.generated).forEach(function(pdId) {
        var g = data.generated[pdId];
        var txtArea = document.getElementById('eval_teks_' + pdId);
        if (txtArea) txtArea.value = g.evaluasi_teks;
        if (g.status_capaian) {
          saveEvalField(pdId, g.kriteria_id, 'status_capaian', g.status_capaian);
        }
      });
      alert('✓ ' + data.message);
      recalculateStats();
    } else {
      window.showAiWarningModal(data.message || 'Gagal memproses evaluasi AI.', data.error_type);
    }
  })
  .catch(function(err) {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '✨ Generate Semua Evaluasi AI (1-Klik)';
    }
    window.showAiWarningModal('Koneksi Error: ' + err.message);
  });
}

// ==================================================
// 4. Absensi Kehadiran Peserta Rapat
// ==================================================
function setAbsensiStatus(idx, status) {
  if (CURRENT_ABSENSI[idx]) {
    CURRENT_ABSENSI[idx].status = status;
    renderAbsensiList();
    saveRapatData();
  }
}

function addNewPeserta() {
  var inp = document.getElementById('newPesertaInput');
  var val = inp ? inp.value.trim() : '';
  if (!val) return;

  CURRENT_ABSENSI.push({ nama: val, status: 'HADIR' });
  if (inp) inp.value = '';
  renderAbsensiList();
  saveRapatData();
}

function removePeserta(idx) {
  if (!CURRENT_ABSENSI[idx]) return;
  var target = CURRENT_ABSENSI[idx];
  if (!confirm('Hapus peserta "' + target.nama + '" dari daftar absensi?')) return;
  CURRENT_ABSENSI.splice(idx, 1);
  renderAbsensiList();
  saveRapatData();
}

function loadAllDosen() {
  if (!ALL_USERS || !ALL_USERS.length) {
    alert('Data daftar dosen tidak ditemukan di sistem.');
    return;
  }

  function norm(str) {
    return (str || '').toLowerCase().replace(/[^a-z0-9]/g, '');
  }

  var existingNorms = CURRENT_ABSENSI.map(function(item) {
    return norm(item.nama);
  });

  var addedCount = 0;
  ALL_USERS.forEach(function(u) {
    var uNorm = norm(u.nama);
    var exists = existingNorms.some(function(en) {
      return en === uNorm || (en.length > 5 && uNorm.indexOf(en) !== -1) || (uNorm.length > 5 && en.indexOf(uNorm) !== -1);
    });

    if (!exists) {
      CURRENT_ABSENSI.push({
        user_id: u.user_id,
        nama: u.nama,
        status: 'HADIR'
      });
      existingNorms.push(uNorm);
      addedCount++;
    }
  });

  renderAbsensiList();
  saveRapatData();

  if (addedCount > 0) {
    alert('✓ Berhasil menambahkan ' + addedCount + ' dosen ke daftar absensi rapat.');
  } else {
    alert('Semua dosen (' + ALL_USERS.length + ' orang) sudah ada di dalam daftar absensi.');
  }
}

function renderAbsensiList() {
  var container = document.getElementById('absensiListContainer');
  var badge = document.getElementById('absensiCountBadge');
  if (badge) badge.textContent = CURRENT_ABSENSI.length + ' Peserta';
  if (!container) return;

  var html = '';
  CURRENT_ABSENSI.forEach(function(a, idx) {
    var st = a.status || 'HADIR';
    html += '<div class="absensi-item-row" data-idx="' + idx + '" style="background:#fff;border:1px solid #e2e8f0;border-radius:9px;padding:8px 12px;display:flex;align-items:center;justify-content:space-between;gap:10px;">';
    html += '<div style="font-size:12.5px;font-weight:700;color:#1e293b;">' + escHtml(a.nama) + '</div>';
    html += '<div style="display:flex;gap:4px;align-items:center;">';
    html += '<button type="button" onclick="setAbsensiStatus(' + idx + ', \'HADIR\')" style="padding:4px 9px;border-radius:6px;font-size:11px;font-weight:800;cursor:pointer;border:1px solid ' + (st === 'HADIR' ? '#10b981' : '#e2e8f0') + ';background:' + (st === 'HADIR' ? '#ecfdf5' : '#fff') + ';color:' + (st === 'HADIR' ? '#065f46' : '#64748b') + ';">✓ HADIR</button>';
    html += '<button type="button" onclick="setAbsensiStatus(' + idx + ', \'TIDAK HADIR\')" style="padding:4px 9px;border-radius:6px;font-size:11px;font-weight:800;cursor:pointer;border:1px solid ' + (st === 'TIDAK HADIR' ? '#ef4444' : '#e2e8f0') + ';background:' + (st === 'TIDAK HADIR' ? '#fef2f2' : '#fff') + ';color:' + (st === 'TIDAK HADIR' ? '#991b1b' : '#64748b') + ';">✕ TIDAK</button>';
    html += '<button type="button" onclick="setAbsensiStatus(' + idx + ', \'IZIN\')" style="padding:4px 9px;border-radius:6px;font-size:11px;font-weight:800;cursor:pointer;border:1px solid ' + (st === 'IZIN' ? '#f59e0b' : '#e2e8f0') + ';background:' + (st === 'IZIN' ? '#fffbeb' : '#fff') + ';color:' + (st === 'IZIN' ? '#92400e' : '#64748b') + ';">⏳ IZIN</button>';
    html += '<button type="button" onclick="removePeserta(' + idx + ')" title="Hapus peserta ini" style="padding:4px 7px;border-radius:6px;font-size:11px;cursor:pointer;border:1px solid #fee2e2;background:#fff5f5;color:#ef4444;font-weight:700;">✕</button>';
    html += '</div></div>';
  });

  container.innerHTML = html;
}

// Debounce timer untuk auto-save notulensi saat mengetik
var _notulensiDebounceTimer = null;
function debounceSaveRapat() {
  if (_notulensiDebounceTimer) clearTimeout(_notulensiDebounceTimer);
  _notulensiDebounceTimer = setTimeout(function() { saveRapatData(false); }, 1200);
}

// Simpan & keluar: pastikan data tersimpan sebelum navigasi
function saveAndExit(e) {
  e.preventDefault();
  var notulSave = document.getElementById('notulensiSaveStatus');
  if (notulSave) { notulSave.textContent = '🔄 Menyimpan...'; notulSave.style.color = '#6366f1'; }
  // Batalkan debounce yang pending
  if (_notulensiDebounceTimer) { clearTimeout(_notulensiDebounceTimer); _notulensiDebounceTimer = null; }
  // Simpan dulu, lalu navigasi setelah selesai
  var notulSaveEl = document.getElementById('notulensiSaveStatus');
  var exitUrl = BASE_APP + '/evaluasi';
  // Override callback untuk navigasi setelah save
  var body = buildRapatPayload();
  fetch(BASE_APP + '/evaluasi/save-rapat', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  }).then(function() {
    window.location.href = exitUrl;
  }).catch(function() {
    window.location.href = exitUrl;
  });
}

// Kumpulkan semua data rapat ke URLSearchParams (dipakai oleh saveRapatData & saveAndExit)
function buildRapatPayload() {
  var notulensi = document.getElementById('notulensiInput')?.value || '';
  var topik = document.getElementById('rapatTopikInput')?.value || '';
  var tahunAjaran = document.getElementById('rapatTaInput')?.value || '';
  var semester = document.getElementById('rapatSemesterInput')?.value || '';
  var jenis = '';
  document.querySelectorAll('input[name="rapat_jenis"]').forEach(function(r) { if (r.checked) jenis = r.value; });
  var kategori = document.getElementById('rapatKategoriInput')?.value || '';
  var tempat = document.getElementById('rapatTempatInput')?.value || '';
  var tanggal = document.getElementById('rapatTanggalInput')?.value || '';
  var jamMulai = document.getElementById('rapatJamMulaiInput')?.value || '';
  var jamSelesai = document.getElementById('rapatJamSelesaiInput')?.value || '';
  var ketuaId = document.getElementById('rapatKetuaInput')?.value || '';
  var notulisId = document.getElementById('rapatNotulisInput')?.value || '';
  var lampiranLink = document.getElementById('rapatLampiranLinkInput')?.value || '';
  var kriteria = [];
  document.querySelectorAll('.rapat-kriteria-cb:checked').forEach(function(cb) { kriteria.push(cb.value); });
  var body = new URLSearchParams();
  body.append('evaluasi_id', EVALUASI_ID);
  body.append('topik', topik);
  body.append('tahun_ajaran', tahunAjaran);
  body.append('semester', semester);
  body.append('jenis', jenis);
  body.append('kategori', kategori);
  body.append('tempat', tempat);
  body.append('tanggal', tanggal);
  body.append('jam_mulai', jamMulai);
  body.append('jam_selesai', jamSelesai);
  body.append('ketua_id', ketuaId);
  body.append('notulis_id', notulisId);
  body.append('lampiran_link', lampiranLink);
  body.append('notulensi', notulensi);
  body.append('absensi', JSON.stringify(CURRENT_ABSENSI));
  kriteria.forEach(function(k) { body.append('kriteria[]', k); });
  return body;
}

function saveRapatData(showSync) {
  var notulSave = document.getElementById('notulensiSaveStatus');
  if (notulSave) {
    notulSave.textContent = showSync ? '🔄 Menyimpan & Sinkronisasi...' : 'Menyimpan...';
    notulSave.style.color = '#6366f1';
  }

  var body = buildRapatPayload();

  fetch(BASE_APP + '/evaluasi/save-rapat', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (notulSave) {
      if (data.success) {
        notulSave.textContent = '✓ Tersimpan & Tersinkron';
        notulSave.style.color = '#059669';

        // Update sync link container jika sync berhasil
        if (data.rapat_id && showSync) {
          var linkContainer = document.getElementById('notulensiSyncLinkContainer');
          if (linkContainer) {
            linkContainer.innerHTML =
              '<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">' +
              '<span class="badge" style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;font-size:11.5px;padding:6px 12px;border-radius:8px;font-weight:800;">✓ Tersinkron ke Notulensi (#' + data.rapat_id + ')</span>' +
              '<a href="' + (data.view_url || '') + '" target="_blank" class="btn btn-sm btn-outline" style="background:#fff;border-color:#c7d2fe;color:#4f46e5;font-weight:700;font-size:12px;text-decoration:none;display:flex;align-items:center;gap:4px;">Buka di Notulensi ↗</a>' +
              '<a href="' + (data.pdf_url || '') + '" target="_blank" class="btn btn-sm btn-outline" style="background:#fff;border-color:#fecaca;color:#dc2626;font-weight:700;font-size:12px;text-decoration:none;display:flex;align-items:center;gap:4px;">📄 Cetak PDF ↗</a>' +
              '</div>';
          }
        }
      } else {
        notulSave.textContent = '⚠ Gagal: ' + (data.message || 'Error');
        notulSave.style.color = '#dc2626';
      }
      setTimeout(function(){ if (notulSave) notulSave.textContent = ''; }, 4000);
    }
  })
  .catch(function(err) {
    console.error('Save Rapat / Sync Error:', err);
    if (notulSave) {
      notulSave.textContent = '⚠ Gagal / Koneksi Error';
      notulSave.style.color = '#dc2626';
      setTimeout(function(){ if (notulSave) notulSave.textContent = ''; }, 4000);
    }
  });
}

// ==================================================
// 5. Upload Foto Kegiatan Rapat
// ==================================================

function handleGambarUpload(input) {
  if (!input.files || !input.files[0]) return;
  var fd = new FormData();
  fd.append('evaluasi_id', EVALUASI_ID);
  fd.append('gambar_file', input.files[0]);

  fetch(BASE_APP + '/evaluasi/upload-gambar', {
    method: 'POST',
    body: fd
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success && data.image) {
      var gallery = document.getElementById('gambarGalleryContainer');
      if (gallery) {
        var imgUrl = data.image.url || (BASE_APP + '/' + data.image.file_path);
        var imgDiv = document.createElement('div');
        imgDiv.className = 'gambar-item-thumb';
        imgDiv.id = 'thumb_' + data.image.id;
        imgDiv.style.cssText = 'position:relative;border:1.5px solid #cbd5e1;border-radius:9px;overflow:hidden;background:#fff;aspect-ratio:1;cursor:pointer;';
        imgDiv.onclick = function() { previewImageModal(imgUrl); };
        imgDiv.innerHTML = '<img src="' + imgUrl + '" alt="Dokumentasi" style="width:100%;height:100%;object-fit:cover;">' +
                           '<button type="button" onclick="event.stopPropagation(); deleteGambarItem(\'' + data.image.id + '\')" style="position:absolute;top:4px;right:4px;width:22px;height:22px;background:rgba(239,68,68,0.85);color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:11px;display:flex;align-items:center;justify-content:center;">✕</button>';
        gallery.appendChild(imgDiv);
        // Otomatis buka preview foto yang baru saja diupload
        previewImageModal(imgUrl);
      }
    } else {
      alert('Upload Foto Gagal: ' + data.message);
    }
  });
}

function deleteGambarItem(imgId) {
  if (!confirm('Hapus foto dokumentasi ini?')) return;
  var body = new URLSearchParams();
  body.append('evaluasi_id', EVALUASI_ID);
  body.append('image_id', imgId);

  fetch(BASE_APP + '/evaluasi/delete-gambar', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      var thumb = document.getElementById('thumb_' + imgId);
      if (thumb) thumb.remove();
    }
  });
}

function recalculateStats() {
  var cards = document.querySelectorAll('.eval-card');
  var total = cards.length;
  var terisi = 0;
  var tercapai = 0, sebagian = 0, belum = 0;

  cards.forEach(function(c) {
    var txt = c.querySelector('textarea')?.value.trim() || '';
    if (txt) terisi++;

    var radTercapai = c.querySelector('input[value="tercapai"]');
    var radSebagian = c.querySelector('input[value="sebagian"]');
    if (radTercapai && radTercapai.checked) tercapai++;
    else if (radSebagian && radSebagian.checked) sebagian++;
    else belum++;
  });

  var pct = total > 0 ? Math.round((terisi / total) * 100) : 0;
  var elTot = document.getElementById('statTotal');
  var elTer = document.getElementById('statTercapai');
  var elSeb = document.getElementById('statSebagian');
  var elBel = document.getElementById('statBelum');
  var elPct = document.getElementById('evalPctText');
  var elBar = document.getElementById('evalProgressBar');

  if (elTot) elTot.textContent = total;
  if (elTer) elTer.textContent = tercapai;
  if (elSeb) elSeb.textContent = sebagian;
  if (elBel) elBel.textContent = belum;
  if (elPct) elPct.textContent = pct + '% Terisi';
  if (elBar) elBar.style.width = pct + '%';
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

function updateAngkaComparison(pdId) {
  var pelEl = document.getElementById('pelAngkaDisplay_' + pdId);
  var evalInp = document.getElementById('eval_capaian_angka_' + pdId);
  var badge = document.getElementById('angkaComparisonBadge_' + pdId);

  if (!badge) return;

  var capPel = pelEl ? (pelEl.getAttribute('data-value') || '').trim() : '';
  var capEv = evalInp ? (evalInp.value || '').trim() : '';

  if (!capPel && !capEv) {
    badge.textContent = '—';
    badge.style.cssText = 'display:inline-block;padding:6px 12px;border-radius:20px;font-size:11.5px;background:#f1f5f9;color:#64748b;border:1px solid #cbd5e1;';
    return;
  }

  if (capPel && !capEv) {
    badge.textContent = '⚠️ Wajib Diisi';
    badge.style.cssText = 'display:inline-block;padding:6px 12px;border-radius:20px;font-size:11.5px;background:#fffbeb;color:#b45309;border:1px solid #fde68a;font-weight:800;';
    return;
  }

  var pNum = parseFloat(capPel.replace(/[^0-9.]/g, ''));
  var eNum = parseFloat(capEv.replace(/[^0-9.]/g, ''));

  if (isNaN(pNum) || isNaN(eNum)) {
    badge.textContent = '📊 ' + capPel + ' vs ' + capEv;
    badge.style.cssText = 'display:inline-block;padding:6px 12px;border-radius:20px;font-size:11.5px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-weight:800;';
    return;
  }

  if (pNum > eNum) {
    var diff = Math.round((pNum - eNum) * 100) / 100;
    badge.textContent = '📈 Melebihi Target (+' + diff + ')';
    badge.style.cssText = 'display:inline-block;padding:6px 12px;border-radius:20px;font-size:11.5px;background:#ecfdf5;color:#047857;border:1.5px solid #a7f3d0;font-weight:900;';
  } else if (pNum === eNum) {
    badge.textContent = '🎯 Sesuai Target (100%)';
    badge.style.cssText = 'display:inline-block;padding:6px 12px;border-radius:20px;font-size:11.5px;background:#eff6ff;color:#1d4ed8;border:1.5px solid #bfdbfe;font-weight:900;';
  } else {
    var diff = Math.round((eNum - pNum) * 100) / 100;
    badge.textContent = '📉 Belum Mencapai (-' + diff + ')';
    badge.style.cssText = 'display:inline-block;padding:6px 12px;border-radius:20px;font-size:11.5px;background:#fef2f2;color:#b91c1c;border:1.5px solid #fca5a5;font-weight:900;';
  }
}

function escHtml(str) {
  if (!str) return '';
  var d = document.createElement('div');
  d.appendChild(document.createTextNode(str));
  return d.innerHTML;
}
</script>

<!-- Modal Lightbox Preview Foto -->
<div id="modalImagePreview" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(15,23,42,0.85);z-index:9999;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(4px);" onclick="closeImageModal()">
  <div style="position:relative;max-width:90vw;max-height:90vh;display:flex;flex-direction:column;align-items:center;" onclick="event.stopPropagation()">
    <img id="modalPreviewImg" src="" alt="Preview Foto" style="max-width:100%;max-height:80vh;border-radius:12px;box-shadow:0 20px 50px rgba(0,0,0,0.5);object-fit:contain;background:#fff;">
    <div style="display:flex;gap:12px;margin-top:14px;">
      <a id="modalDownloadBtn" href="" target="_blank" class="btn btn-primary btn-sm" style="font-weight:700;background:#059669;border:none;display:flex;align-items:center;gap:6px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6"/><polyline points="15 8 12 11 9 8"/><line x1="12" y1="11" x2="12" y2="3"/></svg>
        Buka / Download Foto Full High-Res ↗
      </a>
      <button type="button" onclick="closeImageModal()" class="btn btn-outline btn-sm" style="color:#fff;border-color:rgba(255,255,255,0.4);font-weight:700;">
        ✕ Tutup
      </button>
    </div>
  </div>
</div>
