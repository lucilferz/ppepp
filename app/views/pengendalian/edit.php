<?php
$pageTitle   = 'Form Pengendalian & RTL — ' . htmlspecialchars($pengendalian['judul']);
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

// Hitung statistik ketercapaian dan RTL
$totalStandar   = count($details);
$cntTerpenuhi   = 0;
$cntPerluRtl    = 0;
$cntRtlSelesai  = 0;

foreach ($details as $d) {
    $st = $d['status_capaian'] ?? 'belum_tercapai';
    if ($st === 'tercapai') {
        $cntTerpenuhi++;
    } else {
        $cntPerluRtl++;
        if (!empty(trim($d['rencana_tindak_lanjut'] ?? ''))) {
            $cntRtlSelesai++;
        }
    }
}
$pctRtl = $cntPerluRtl > 0 ? round(($cntRtlSelesai / $cntPerluRtl) * 100) : 100;
?>

<!-- Header Banner -->
<div style="background:linear-gradient(135deg,#0f172a 0%,#431407 50%,#7c2d12 100%);border-radius:16px;padding:26px 30px;margin-bottom:24px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;box-shadow:0 10px 30px rgba(124,45,18,0.25);">
  <div>
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,255,255,0.6);margin-bottom:6px;">
      Pengendalian Standar &amp; Rencana Tindak Lanjut (Tahap P • PPEPP)
    </div>
    <h2 style="font-size:24px;font-weight:900;color:#fff;margin-bottom:4px;letter-spacing:-0.4px;">
      <?= htmlspecialchars($pengendalian['judul']) ?>
    </h2>
    <div style="font-size:13px;color:rgba(255,255,255,0.75);display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:4px;">
      <span>Penetapan Acuan: <strong><?= htmlspecialchars($penetapan['judul'] ?? '—') ?></strong></span>
      <span>•</span>
      <span>Tahun Ajaran: <strong><?= htmlspecialchars($penetapan['ta_nama'] ?? '—') ?></strong></span>
      <?php if (!empty($evaluasi['judul'])): ?>
      <span>•</span>
      <span style="color:#a7f3d0;">Evaluasi Terhubung: <?= htmlspecialchars($evaluasi['judul']) ?> ✓</span>
      <?php endif; ?>
    </div>
  </div>

  <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
    <?php if ($cntPerluRtl > 0): ?>
    <button type="button" onclick="generateAllAIRtl()" id="btnGenerateAllAI"
            class="btn btn-primary"
            style="background:linear-gradient(135deg,#ea580c,#c2410c);border:none;font-weight:800;font-size:13px;display:flex;align-items:center;gap:8px;box-shadow:0 4px 14px rgba(234,88,12,0.4);cursor:pointer;padding:10px 18px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      ✨ Generate Semua RTL AI (1-Klik)
    </button>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>/pengendalian/<?= $pgId ?>" class="btn btn-outline"
       style="background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.25);color:#fff;font-size:12.5px;">
      Lihat Hasil ↗
    </a>
    <a href="<?= BASE_URL ?>/ppepp<?= !empty($pengendalian['ppepp_project_id']) ? '/' . $pengendalian['ppepp_project_id'] : '' ?>" class="btn btn-outline"
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

<!-- Summary Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px;margin-bottom:24px;">
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Standar Penetapan</div>
    <div style="font-size:26px;font-weight:900;color:#1e293b;margin-top:4px;" id="statTotal"><?= $totalStandar ?></div>
    <div style="font-size:11.5px;color:#94a3b8;margin-top:2px;">Dari dokumen penetapan acuan</div>
  </div>

  <div style="background:#fff;border:1.5px solid #a7f3d0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="font-size:11.5px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.5px;">✓ Standar Terpenuhi</div>
    <div style="font-size:26px;font-weight:900;color:#065f46;margin-top:4px;" id="statTerpenuhi"><?= $cntTerpenuhi ?></div>
    <div style="font-size:11.5px;color:#059669;margin-top:2px;">Tidak memerlukan form RTL</div>
  </div>

  <div style="background:#fff;border:1.5px solid #fed7aa;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="font-size:11.5px;font-weight:700;color:#ea580c;text-transform:uppercase;letter-spacing:0.5px;">⚠️ Belum Terpenuhi (Perlu RTL)</div>
    <div style="font-size:26px;font-weight:900;color:#9a3412;margin-top:4px;" id="statPerluRtl"><?= $cntPerluRtl ?></div>
    <div style="font-size:11.5px;color:#c2410c;margin-top:2px;">Memerlukan tindakan koreksi</div>
  </div>

  <div style="background:#fff;border:1.5px solid #c7d2fe;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="font-size:11.5px;font-weight:700;color:#4f46e5;text-transform:uppercase;letter-spacing:0.5px;">Progress Perumusan RTL</div>
    <div style="font-size:26px;font-weight:900;color:#3730a3;margin-top:4px;" id="statPctText"><?= $pctRtl ?>%</div>
    <div style="font-size:11.5px;color:#4f46e5;margin-top:2px;"><span id="statRtlCount"><?= $cntRtlSelesai ?></span> dari <?= $cntPerluRtl ?> RTL terisi</div>
  </div>
</div>

<!-- SECTION 1: PERUMUSAN RTL & KOREKSI STANDAR -->
<div style="margin-bottom:36px;">
  <div style="margin-bottom:16px;">
    <h3 style="font-size:18px;font-weight:900;color:#1e293b;margin:0 0 2px;">
      1. Rencana Tindak Lanjut (RTL) &amp; Usulan Koreksi Standar
    </h3>
    <p style="font-size:13px;color:#64748b;margin:0;">
      Form RTL difokuskan khusus untuk standar yang belum terpenuhi atau tercapai sebagian pada tahap Evaluasi. Standar yang telah terpenuhi diberi tanda centang hijau.
    </p>
  </div>

  <div style="display:flex;flex-direction:column;gap:20px;">
    <?php foreach ($details as $idx => $d): ?>
    <?php
    $pdId        = (int)$d['id'];
    $kid         = (int)$d['kriteria_id'];
    $stCapaian   = $d['status_capaian'] ?? 'belum_tercapai';
    $isTerpenuhi = ($stCapaian === 'tercapai');
    $stTindakan  = $d['status_tindakan'] ?? 'belum';
    $displayKode = !empty($d['kode']) ? $d['kode'] : (!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD-' . ($idx + 1));
    ?>

    <?php if ($isTerpenuhi): ?>
    <!-- KONDISI A: STANDAR SUDAH TERPENUHI (TIDAK PERLU RTL) -->
    <div class="standar-card terpenuhi" id="card_pg_<?= $pdId ?>"
         style="background:#f0fdf4;border:1.5px solid #a7f3d0;border-radius:14px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
      <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:12px;flex:1;">
          <div style="padding:5px 12px;background:#059669;color:#fff;border-radius:8px;font-weight:900;font-size:13px;flex-shrink:0;">
            <?= htmlspecialchars($displayKode) ?>
          </div>
          <div style="flex:1;">
            <div style="display:flex;align-items:center;gap:8px;">
              <h4 style="font-size:16px;font-weight:800;color:#065f46;margin:0 0 4px;"><?= htmlspecialchars($d['kriteria_nama']) ?></h4>
              <span style="font-size:11px;font-weight:700;color:#059669;background:#ecfdf5;padding:2px 8px;border-radius:6px;">
                Indikator #<?= $idx + 1 ?>
              </span>
            </div>
            <?php if (!empty($d['target_capaian'])): ?>
            <div style="font-size:13.5px;color:#334155;line-height:1.5;">
              <strong>🎯 Pernyataan Standar:</strong> <?= htmlspecialchars($d['target_capaian']) ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($d['indikator'])): ?>
            <div style="font-size:13.5px;color:#047857;line-height:1.5;margin-top:2px;">
              <strong>📊 Target / Indikator:</strong> <?= htmlspecialchars($d['indikator']) ?>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <div style="display:flex;align-items:center;gap:8px;">
          <span style="font-size:12.5px;font-weight:800;padding:5px 14px;border-radius:20px;background:#059669;color:#fff;display:inline-flex;align-items:center;gap:6px;">
            ✓ Standar Terpenuhi (Tercapai Penuh)
          </span>
        </div>
      </div>
      <div style="padding:10px 20px 14px;background:#fff;border-top:1px dashed #a7f3d0;font-size:13px;color:#065f46;">
        💡 <strong>Keterangan:</strong> Indikator kriteria ini telah terpenuhi pada tahap Evaluasi dan tidak memerlukan form tindakan koreksi / RTL.
      </div>
    </div>

    <?php else: ?>
    <!-- KONDISI B: STANDAR BELUM TERPENUHI (WAJIB MENGISI RTL & KOREKSI) -->
    <div class="standar-card belum-terpenuhi" id="card_pg_<?= $pdId ?>"
         style="background:#fff;border:1.5px solid #fed7aa;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(234,88,12,0.06);">
      
      <!-- Card Header -->
      <div style="padding:16px 22px;background:#fff7ed;border-bottom:1.5px solid #fed7aa;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:12px;flex:1;">
          <div style="padding:5px 12px;background:#ea580c;color:#fff;border-radius:8px;font-weight:900;font-size:13px;flex-shrink:0;">
            <?= htmlspecialchars($displayKode) ?>
          </div>
          <div style="flex:1;">
            <div style="display:flex;align-items:center;gap:8px;">
              <h4 style="font-size:16px;font-weight:800;color:#9a3412;margin:0 0 4px;"><?= htmlspecialchars($d['kriteria_nama']) ?></h4>
              <span style="font-size:11px;font-weight:700;color:#ea580c;background:#fff7ed;padding:2px 8px;border-radius:6px;border:1px solid #fed7aa;">
                Indikator #<?= $idx + 1 ?>
              </span>
            </div>
            <div style="font-size:13px;color:#c2410c;"><?= htmlspecialchars($d['kriteria_deskripsi'] ?? '') ?></div>
          </div>
        </div>

        <div style="display:flex;align-items:center;gap:10px;">
          <span style="font-size:12.5px;font-weight:800;padding:5px 14px;border-radius:20px;background:#fee2e2;color:#991b1b;">
            ⚠️ <?= $stCapaian === 'sebagian' ? 'Tercapai Sebagian' : 'Belum Tercapai' ?> (Wajib RTL)
          </span>

          <!-- Tombol AI Single RTL -->
          <button type="button" onclick="generateSingleAIRtl(<?= $pdId ?>, <?= $kid ?>)" id="btn_ai_<?= $pdId ?>"
                  style="background:#fff;border:1.5px solid #ea580c;color:#ea580c;padding:7px 14px;border-radius:9px;font-size:12.5px;font-weight:800;cursor:pointer;display:flex;align-items:center;gap:6px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            ✨ Generate RTL AI
          </button>
        </div>
      </div>

      <!-- Card Body -->
      <div style="padding:22px;">
        
        <!-- Info Target Standar & Indikator dari Penetapan -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;margin-bottom:16px;">
          <?php if (!empty($d['strategi'])): ?>
          <div style="background:#f8faff;border-left:4px solid #4f46e5;padding:10px 14px;border-radius:0 10px 10px 0;">
            <div style="font-size:11.5px;font-weight:800;text-transform:uppercase;color:#4f46e5;margin-bottom:3px;letter-spacing:0.5px;">📌 Aturan / Dasar Hukum:</div>
            <div style="font-size:14.5px;color:#1e293b;line-height:1.6;font-weight:500;"><?= htmlspecialchars($d['strategi']) ?></div>
          </div>
          <?php endif; ?>

          <div style="background:#f0fdf4;border-left:4px solid #059669;padding:10px 14px;border-radius:0 10px 10px 0;">
            <div style="font-size:11.5px;font-weight:800;text-transform:uppercase;color:#065f46;margin-bottom:3px;letter-spacing:0.5px;">🎯 Pernyataan Standar:</div>
            <div style="font-size:14.5px;color:#1e293b;line-height:1.6;font-weight:500;"><?= htmlspecialchars($d['target_capaian'] ?: '—') ?></div>
          </div>

          <div style="background:#fffbeb;border-left:4px solid #d97706;padding:10px 14px;border-radius:0 10px 10px 0;">
            <div style="font-size:11.5px;font-weight:800;text-transform:uppercase;color:#92400e;margin-bottom:3px;letter-spacing:0.5px;">📊 Target / Indikator:</div>
            <div style="font-size:14.5px;color:#1e293b;line-height:1.6;font-weight:500;"><?= htmlspecialchars($d['indikator'] ?: '—') ?></div>
          </div>
        </div>

        <!-- Hasil Evaluasi Indikator (Ditarik Otomatis dari Modul Evaluasi) -->
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:18px;">
          <div style="font-size:11px;font-weight:800;text-transform:uppercase;color:#991b1b;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
            <span>📝 Hasil Analisis Evaluasi (dari Modul Evaluasi):</span>
          </div>
          <div style="font-size:12.5px;color:#7f1d1d;line-height:1.5;white-space:pre-wrap;"><?= !empty($d['evaluasi_teks']) ? htmlspecialchars($d['evaluasi_teks']) : (!empty($d['analisis_gap']) ? htmlspecialchars($d['analisis_gap']) : '<span style="font-style:italic;color:#991b1b;">Belum ada catatan evaluasi dari tahap sebelumnya</span>') ?></div>
        </div>

        <!-- FORM RTL INTERAKTIF (Akar Masalah, RTL, Usulan Koreksi, PIC, Target Waktu, Status) -->
        <div style="display:flex;flex-direction:column;gap:14px;">
          
          <!-- 1. Akar Masalah -->
          <div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
              <label style="font-size:12.5px;font-weight:800;color:#1e293b;">
                🔍 1. Analisis Akar Masalah / Penyebab Ketidaktercapaian:
              </label>
              <span id="saveStatus_akar_<?= $pdId ?>" style="font-size:11px;color:#94a3b8;"></span>
            </div>
            <textarea id="akar_masalah_<?= $pdId ?>" class="form-control" rows="2"
                      placeholder="Identifikasi kendala atau faktor utama penyebab standar belum terpenuhi..."
                      onchange="savePgField(<?= $pdId ?>, <?= $kid ?>, 'akar_masalah', this.value)"
                      style="font-size:12.5px;line-height:1.5;"><?= htmlspecialchars($d['akar_masalah'] ?? '') ?></textarea>
          </div>

          <!-- 2. Rencana Tindakan Koreksi (RTL) -->
          <div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
              <label style="font-size:12.5px;font-weight:800;color:#1e293b;">
                🛠️ 2. Rencana Tindakan Koreksi (RTL Konkret): <span style="color:#ef4444;">*</span>
              </label>
              <span id="saveStatus_rtl_<?= $pdId ?>" style="font-size:11px;color:#94a3b8;"></span>
            </div>
            <textarea id="rtl_<?= $pdId ?>" class="form-control" rows="3"
                      placeholder="Langkah-langkah perbaikan konkret dan terukur untuk mencapai target standar..."
                      onchange="savePgField(<?= $pdId ?>, <?= $kid ?>, 'rencana_tindak_lanjut', this.value)"
                      style="font-size:12.5px;line-height:1.5;"><?= htmlspecialchars($d['rencana_tindak_lanjut'] ?? '') ?></textarea>
          </div>

          <!-- 3. Usulan Koreksi Standar untuk Penetapan Berikutnya -->
          <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 14px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
              <label style="font-size:12.5px;font-weight:800;color:#92400e;">
                🔄 3. Usulan Koreksi / Penyesuaian Standar untuk Penetapan Berikutnya:
              </label>
              <span id="saveStatus_koreksi_<?= $pdId ?>" style="font-size:11px;color:#94a3b8;"></span>
            </div>
            <div style="font-size:11.5px;color:#b45309;margin-bottom:6px;">
              Jika standar dinilai perlu direvisi, disesuaikan formulanya, atau ditingkatkan pada siklus PPEPP berikutnya.
            </div>
            <textarea id="koreksi_standar_<?= $pdId ?>" class="form-control" rows="2"
                      placeholder="Contoh: Menyesuaikan target publikasi mahasiswa dari 100% menjadi bertahap 80% pada tahun pertama..."
                      onchange="savePgField(<?= $pdId ?>, <?= $kid ?>, 'koreksi_standar', this.value)"
                      style="font-size:12.5px;line-height:1.5;background:#fff;"><?= htmlspecialchars($d['koreksi_standar'] ?? '') ?></textarea>
          </div>

        </div>

      </div>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>

<!-- SECTION 2: UPLOAD BERKAS NOTULENSI RAPAT RTM & BERKAS KOREKSI STANDAR -->
<div style="background:#fff;border:1.5px solid #fed7aa;border-radius:16px;padding:24px 28px;margin-bottom:30px;box-shadow:0 4px 16px rgba(234,88,12,0.06);">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:16px;border-bottom:1.5px solid #ffedd5;padding-bottom:14px;">
    <div>
      <h3 style="font-size:18px;font-weight:900;color:#1e293b;margin:0 0 4px;display:flex;align-items:center;gap:8px;">
        <span>📁 2. Upload Berkas Notulensi Rapat RTM &amp; Berkas Koreksi Standar</span>
      </h3>
      <p style="font-size:13px;color:#64748b;margin:0;">
        Unggah berkas Notulensi Rapat Tinjauan Manajemen (RTM), Berita Acara Pengendalian, atau Draft SK/Revisi Standar SPMI yang menjadi rujukan perbaikan standar pada siklus Penetapan berikutnya.
      </p>
    </div>

    <div>
      <button type="button" onclick="triggerUploadBerkas()" class="btn btn-primary"
              style="background:#ea580c;border:none;font-weight:800;font-size:12.5px;display:flex;align-items:center;gap:6px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        + Upload Notulensi / Berkas
      </button>
      <input type="file" id="berkasFileInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" style="display:none;" onchange="handleUploadBerkas(this)">
    </div>
  </div>

  <!-- Form Metadata Berkas (Notulensi Rapat & Koreksi Standar) -->
  <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:14px;margin-bottom:16px;display:grid;grid-template-columns:1fr 1.5fr auto;gap:10px;align-items:end;">
    <div>
      <label style="font-size:11.5px;font-weight:700;color:#9a3412;margin-bottom:4px;display:block;">Judul / Nama Notulensi Rapat:</label>
      <input type="text" id="berkasJudulInput" class="form-control" placeholder="Misal: Notulensi Rapat RTM & Berita Acara Pengendalian 2026" style="font-size:12px;">
    </div>
    <div>
      <label style="font-size:11.5px;font-weight:700;color:#9a3412;margin-bottom:4px;display:block;">Keterangan Ringkas:</label>
      <input type="text" id="berkasKetInput" class="form-control" placeholder="Notulensi rapat pembahasan RTL dan usulan koreksi standar..." style="font-size:12px;">
    </div>
    <div>
      <button type="button" onclick="triggerUploadBerkas()" class="btn btn-outline btn-sm" style="color:#ea580c;border-color:#ea580c;font-weight:700;height:35px;">
        Pilih File &amp; Upload
      </button>
    </div>
  </div>

  <!-- Tabel Daftar Berkas Koreksi yang Sudah Diupload -->
  <div style="overflow-x:auto;border:1px solid #e2e8f0;border-radius:10px;background:#fff;">
    <table style="width:100%;border-collapse:collapse;font-size:12.5px;" id="berkasTable">
      <thead>
        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:800;text-align:left;">
          <th style="padding:10px 14px;width:30px;">#</th>
          <th style="padding:10px 14px;">Judul Dokumen</th>
          <th style="padding:10px 14px;">Nama File</th>
          <th style="padding:10px 14px;">Keterangan</th>
          <th style="padding:10px 14px;width:120px;">Waktu Upload</th>
          <th style="padding:10px 14px;width:110px;text-align:center;">Aksi</th>
        </tr>
      </thead>
      <tbody id="berkasTableBody">
        <?php if (empty($berkasList)): ?>
        <tr id="emptyBerkasRow">
          <td colspan="6" style="padding:24px;text-align:center;color:#94a3b8;font-style:italic;">
            Belum ada berkas koreksi standar yang diunggah. Silakan upload berkas acuan untuk siklus berikutnya.
          </td>
        </tr>
        <?php else: ?>
        <?php foreach ($berkasList as $bIdx => $b): ?>
        <tr id="berkas_row_<?= htmlspecialchars($b['id'] ?? '') ?>" style="border-bottom:1px solid #f1f5f9;">
          <td style="padding:10px 14px;color:#94a3b8;"><?= $bIdx + 1 ?></td>
          <td style="padding:10px 14px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($b['judul'] ?? '—') ?></td>
          <td style="padding:10px 14px;">
            <a href="<?= htmlspecialchars($b['url'] ?? BASE_URL . '/' . $b['file_path']) ?>" target="_blank" style="color:#059669;font-weight:600;text-decoration:none;">
              📄 <?= htmlspecialchars($b['file_name'] ?? 'Lihat Berkas') ?> ↗
            </a>
          </td>
          <td style="padding:10px 14px;color:#64748b;"><?= htmlspecialchars($b['keterangan'] ?? '—') ?></td>
          <td style="padding:10px 14px;color:#94a3b8;font-size:11.5px;"><?= htmlspecialchars($b['time'] ?? '—') ?></td>
          <td style="padding:10px 14px;text-align:center;">
            <button type="button" onclick="deleteBerkasItem('<?= htmlspecialchars($b['id'] ?? $b['file_path']) ?>')" class="btn btn-outline btn-sm"
                    style="color:#ef4444;border-color:#fca5a5;padding:4px 8px;font-size:11px;">
              Hapus
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Sticky Bottom Bar -->
<div style="position:sticky;bottom:0;background:#fff;border-top:1.5px solid #e2e8f0;padding:16px 24px;border-radius:0 0 14px 14px;display:flex;align-items:center;justify-content:space-between;gap:12px;box-shadow:0 -4px 20px rgba(0,0,0,0.06);z-index:40;flex-wrap:wrap;margin-top:12px;">
  <div style="display:flex;align-items:center;gap:10px;">
    <a href="<?= BASE_URL ?>/pengendalian" style="background:none;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:600;color:#64748b;text-decoration:none;display:flex;align-items:center;gap:6px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
      Simpan Draft &amp; Keluar
    </a>
    <span style="font-size:12px;color:#94a3b8;">Perubahan form tersimpan otomatis saat Anda mengetik atau memilih status</span>
  </div>
  
  <div style="display:flex;align-items:center;gap:10px;">
    <form action="<?= BASE_URL ?>/pengendalian/<?= $pgId ?>/finalize" method="POST" style="margin:0;">
      <button type="submit" class="btn btn-success btn-lg" style="font-size:13px;padding:10px 22px;font-weight:800;background:#059669;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Finalisasi Dokumen Pengendalian
      </button>
    </form>
  </div>
</div>

<script>
var PENGENDALIAN_ID = <?= (int)$pgId ?>;
var BASE_APP        = '<?= BASE_URL ?>';

// ==================================================
// 1. Auto-Save Field Pengendalian Detail
// ==================================================
function savePgField(pdId, kid, field, value) {
  var shortField = field.replace('rencana_tindak_lanjut', 'rtl');
  var saveEl = document.getElementById('saveStatus_' + shortField + '_' + pdId);
  if (saveEl) saveEl.textContent = 'Menyimpan...';

  var body = new URLSearchParams();
  body.append('pengendalian_id', PENGENDALIAN_ID);
  body.append('penetapan_detail_id', pdId);
  body.append('kriteria_id', kid);
  body.append('field', field);
  body.append('value', value);

  fetch(BASE_APP + '/pengendalian/save-detail', {
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
    recalculateRtlStats();
  });
}

// ==================================================
// 2. Generate AI RTL Single Indikator
// ==================================================
function generateSingleAIRtl(pdId, kid) {
  if (typeof window.USER_HAS_API_KEY !== 'undefined' && !window.USER_HAS_API_KEY) {
    window.showAiWarningModal('🔑 API Key Gemini belum terpasang. Silakan masukkan API Key Gemini Anda terlebih dahulu di menu Pengaturan.', 'missing_key');
    return;
  }

  var btn = document.getElementById('btn_ai_' + pdId);
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<span style="animation:spin 0.8s linear infinite;">⏳</span> Menyusun RTL...';
  }

  var body = new URLSearchParams();
  body.append('pengendalian_id', PENGENDALIAN_ID);
  body.append('penetapan_detail_id', pdId);
  body.append('kriteria_id', kid);

  fetch(BASE_APP + '/pengendalian/generate-ai', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg> ✨ Generate RTL AI';
    }
    if (data.success) {
      if (data.akar_masalah && document.getElementById('akar_masalah_' + pdId)) document.getElementById('akar_masalah_' + pdId).value = data.akar_masalah;
      if (data.rtl && document.getElementById('rtl_' + pdId))                   document.getElementById('rtl_' + pdId).value = data.rtl;
      if (data.koreksi_standar && document.getElementById('koreksi_standar_' + pdId)) document.getElementById('koreksi_standar_' + pdId).value = data.koreksi_standar;
      if (data.pic && document.getElementById('pic_' + pdId))                   document.getElementById('pic_' + pdId).value = data.pic;
      if (data.waktu && document.getElementById('waktu_' + pdId))               document.getElementById('waktu_' + pdId).value = data.waktu;
      recalculateRtlStats();
    } else {
      window.showAiWarningModal(data.message || 'Gagal generate RTL.', data.error_type);
    }
  })
  .catch(function(err) {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '✨ Generate RTL AI';
    }
    window.showAiWarningModal('Koneksi Error: ' + err.message);
  });
}

// ==================================================
// 3. Generate Semua RTL AI Sekaligus
// ==================================================
function generateAllAIRtl() {
  if (typeof window.USER_HAS_API_KEY !== 'undefined' && !window.USER_HAS_API_KEY) {
    window.showAiWarningModal('🔑 API Key Gemini belum terpasang. Silakan masukkan API Key Gemini Anda terlebih dahulu di menu Pengaturan.', 'missing_key');
    return;
  }

  var btn = document.getElementById('btnGenerateAllAI');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<span style="animation:spin 0.8s linear infinite;">⏳</span> Memproses Seluruh Standar Belum Terpenuhi...';
  }

  var body = new URLSearchParams();
  body.append('pengendalian_id', PENGENDALIAN_ID);

  fetch(BASE_APP + '/pengendalian/generate-all-ai', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg> ✨ Generate Semua RTL AI (1-Klik)';
    }
    if (data.success && data.generated) {
      Object.keys(data.generated).forEach(function(pdId) {
        var g = data.generated[pdId];
        if (g.akar_masalah && document.getElementById('akar_masalah_' + pdId)) document.getElementById('akar_masalah_' + pdId).value = g.akar_masalah;
        if (g.rtl && document.getElementById('rtl_' + pdId))                   document.getElementById('rtl_' + pdId).value = g.rtl;
        if (g.koreksi_standar && document.getElementById('koreksi_standar_' + pdId)) document.getElementById('koreksi_standar_' + pdId).value = g.koreksi_standar;
        if (g.pic && document.getElementById('pic_' + pdId))                   document.getElementById('pic_' + pdId).value = g.pic;
        if (g.waktu && document.getElementById('waktu_' + pdId))               document.getElementById('waktu_' + pdId).value = g.waktu;
      });
      alert('✓ ' + data.message);
      recalculateRtlStats();
    } else {
      window.showAiWarningModal(data.message || 'Gagal memproses RTL AI.', data.error_type);
    }
  })
  .catch(function(err) {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '✨ Generate Semua RTL AI (1-Klik)';
    }
    window.showAiWarningModal('Koneksi Error: ' + err.message);
  });
}

// ==================================================
// 4. Upload & Kelola Berkas Koreksi Standar
// ==================================================
function triggerUploadBerkas() {
  var fileInput = document.getElementById('berkasFileInput');
  if (fileInput) fileInput.click();
}

function handleUploadBerkas(input) {
  if (!input.files || !input.files[0]) return;
  var judul = document.getElementById('berkasJudulInput')?.value || '';
  var ket   = document.getElementById('berkasKetInput')?.value || '';

  var fd = new FormData();
  fd.append('pengendalian_id', PENGENDALIAN_ID);
  fd.append('berkas_file', input.files[0]);
  fd.append('judul', judul);
  fd.append('keterangan', ket);

  fetch(BASE_APP + '/pengendalian/upload-berkas-koreksi', {
    method: 'POST',
    body: fd
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success && data.berkas) {
      var tbody = document.getElementById('berkasTableBody');
      var emptyRow = document.getElementById('emptyBerkasRow');
      if (emptyRow) emptyRow.remove();

      var b = data.berkas;
      var tr = document.createElement('tr');
      tr.id = 'berkas_row_' + b.id;
      tr.style.borderBottom = '1px solid #f1f5f9';
      tr.innerHTML = '<td style="padding:10px 14px;color:#94a3b8;">' + (tbody.children.length + 1) + '</td>' +
                     '<td style="padding:10px 14px;font-weight:700;color:#1e293b;">' + escHtml(b.judul) + '</td>' +
                     '<td style="padding:10px 14px;"><a href="' + b.url + '" target="_blank" style="color:#059669;font-weight:600;text-decoration:none;">📄 ' + escHtml(b.file_name) + ' ↗</a></td>' +
                     '<td style="padding:10px 14px;color:#64748b;">' + escHtml(b.keterangan || '—') + '</td>' +
                     '<td style="padding:10px 14px;color:#94a3b8;font-size:11.5px;">' + escHtml(b.time) + '</td>' +
                     '<td style="padding:10px 14px;text-align:center;"><button type="button" onclick="deleteBerkasItem(\'' + b.id + '\')" class="btn btn-outline btn-sm" style="color:#ef4444;border-color:#fca5a5;padding:4px 8px;font-size:11px;">Hapus</button></td>';
      tbody.appendChild(tr);

      // Reset input form
      if (document.getElementById('berkasJudulInput')) document.getElementById('berkasJudulInput').value = '';
      if (document.getElementById('berkasKetInput'))   document.getElementById('berkasKetInput').value = '';
      alert('✓ ' + data.message);
    } else {
      alert('Upload Gagal: ' + (data.message || 'Error'));
    }
  })
  .finally(function(){
    input.value = '';
  });
}

function deleteBerkasItem(berkasId) {
  if (!confirm('Hapus berkas koreksi standar ini?')) return;
  var body = new URLSearchParams();
  body.append('pengendalian_id', PENGENDALIAN_ID);
  body.append('berkas_id', berkasId);

  fetch(BASE_APP + '/pengendalian/delete-berkas-koreksi', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      var row = document.getElementById('berkas_row_' + berkasId);
      if (row) row.remove();
    }
  });
}

function recalculateRtlStats() {
  var cards = document.querySelectorAll('.standar-card.belum-terpenuhi');
  var totalPerlu = cards.length;
  var terisi = 0;

  cards.forEach(function(c) {
    var txt = c.querySelector('textarea[id^="rtl_"]')?.value.trim() || '';
    if (txt) terisi++;
  });

  var pct = totalPerlu > 0 ? Math.round((terisi / totalPerlu) * 100) : 100;
  var elPct = document.getElementById('statPctText');
  var elCnt = document.getElementById('statRtlCount');

  if (elPct) elPct.textContent = pct + '%';
  if (elCnt) elCnt.textContent = terisi;
}

function escHtml(str) {
  if (!str) return '';
  var d = document.createElement('div');
  d.appendChild(document.createTextNode(str));
  return d.innerHTML;
}
</script>
