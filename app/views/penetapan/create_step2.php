<?php
$pageTitle   = 'Detail Penetapan Standar — ' . htmlspecialchars($penetapan['judul']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Penetapan', 'url' => BASE_URL . '/penetapan'],
  ['label' => htmlspecialchars($penetapan['judul'])],
];
$penId   = $penetapan['id'];
$details = $penetapan['details'] ?? [];
$allKriteriaList = !empty($allKriteria) ? array_values($allKriteria) : (isset($selectedKriteria) ? array_values($selectedKriteria) : []);
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
  <div>
    <h2 style="font-size:22px;font-weight:900;color:#1e293b;margin:0;">Pengisian Standar Penetapan</h2>
    <p class="page-desc" style="font-size:13px;color:#64748b;margin:4px 0 0;">Dokumen: <strong><?= htmlspecialchars($penetapan['judul']) ?></strong> (Langkah 2: Isi rincian atau impor Excel)</p>
  </div>
  <a href="<?= BASE_URL ?>/ppepp<?= !empty($penetapan['ppepp_project_id']) ? '/' . $penetapan['ppepp_project_id'] : '' ?>" class="btn btn-outline" style="font-size:13px;font-weight:700;">
    ← Kembali ke Project Library
  </a>
</div>

<!-- Progress Wizard -->
<div style="display:flex;align-items:center;gap:0;margin-bottom:24px;">
  <div style="display:flex;flex-direction:column;align-items:center;gap:5px;">
    <div style="width:36px;height:36px;border-radius:50%;background:#e2e8f0;color:#64748b;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;">1</div>
    <div style="font-size:11px;font-weight:600;color:#64748b;">Info Penetapan</div>
  </div>
  <div style="flex:1;height:2px;background:#3f51b5;max-width:80px;min-width:30px;"></div>
  <div style="display:flex;flex-direction:column;align-items:center;gap:5px;">
    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#1a237e,#3f51b5);color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;box-shadow:0 4px 14px rgba(26,35,126,0.3);">2</div>
    <div style="font-size:11px;font-weight:700;color:#3f51b5;">Pengisian Standar</div>
  </div>
  <div style="flex:1;height:2px;background:#e2e8f0;max-width:80px;min-width:30px;"></div>
  <div style="display:flex;flex-direction:column;align-items:center;gap:5px;">
    <div style="width:36px;height:36px;border-radius:50%;background:#e2e8f0;color:#94a3b8;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;">3</div>
    <div style="font-size:11px;font-weight:600;color:#94a3b8;">Simpan / Finalisasi</div>
  </div>
</div>

<!-- BANNER & PANEL IMPORT EXCEL STANDAR -->
<div style="background:#fff;border:1.5px solid #c7d2fe;border-radius:14px;padding:20px 24px;margin-bottom:24px;box-shadow:0 4px 16px rgba(79,70,229,0.06);">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:14px;">
      <div style="width:46px;height:46px;background:linear-gradient(135deg,#059669,#10b981);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0;box-shadow:0 4px 12px rgba(16,185,129,0.25);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/></svg>
      </div>
      <div>
        <h3 style="font-size:16px;font-weight:800;color:#1e293b;margin:0 0 4px;">Template &amp; Import Excel Standar</h3>
        <div style="font-size:12.5px;color:#64748b;">
          Download template Excel, isi data standar (No, Aturan, Pernyataan Standar, Indikator), lalu upload file Excel untuk memetakan seluruh baris standar ke form.
        </div>
      </div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <a href="<?= BASE_URL ?>/penetapan/download-template-excel?format=xlsx" class="btn btn-outline"
         style="color:#059669;border-color:#a7f3d0;background:#ecfdf5;font-weight:700;font-size:12.5px;display:flex;align-items:center;gap:6px;"
         title="Download format Excel resmi">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Download Template (.xlsx)
      </a>
      <a href="<?= BASE_URL ?>/penetapan/download-template-excel?format=csv" class="btn btn-outline"
         style="color:#475569;border-color:#e2e8f0;font-weight:600;font-size:12px;display:flex;align-items:center;gap:4px;"
         title="Download format CSV">
        .csv
      </a>
      <button type="button" onclick="triggerExcelUpload()" class="btn btn-primary"
              style="background:#059669;border:none;font-weight:700;font-size:12.5px;display:flex;align-items:center;gap:6px;cursor:pointer;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        Upload File Excel
      </button>
      <input type="file" id="excelFileInput" accept=".xlsx,.xls,.csv,.txt" style="display:none;" onchange="handleExcelUpload(this)">
    </div>
  </div>

  <!-- Loading State -->
  <div id="excelLoading" style="display:none;margin-top:14px;padding:16px;background:#f0fdf4;border:1.5px dashed #10b981;border-radius:10px;text-align:center;color:#065f46;font-size:13px;font-weight:600;">
    <div style="width:22px;height:22px;border:2.5px solid #a7f3d0;border-top-color:#059669;border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 8px;"></div>
    Membaca dan memecah isi file Excel...
  </div>

  <!-- Excel Split Results Container (Table & Mapping) -->
  <div id="excelResultsContainer" style="display:none;margin-top:16px;border-top:1.5px solid #e0e7ff;padding-top:16px;">
    <!-- Top Action Bar -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:10px;">
      <div>
        <div style="font-size:14px;font-weight:800;color:#1e293b;" id="excelResultsTitle">
          📋 Hasil Pembacaan File Excel (<span id="excelRowCount">0</span> standar di sheet ini)
        </div>
        <div style="font-size:12px;color:#64748b;" id="excelResultsSubtitle">
          Pilih sheet dan centang standar yang ingin dimasukkan ke form penetapan.
        </div>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <button type="button" id="btnApplyActiveSheet" onclick="applySelectedExcelRows(false, <?= $penId ?>)" class="btn btn-success btn-sm" style="font-weight:700;display:flex;align-items:center;gap:6px;background:#059669;color:#fff;border:none;padding:7px 14px;border-radius:8px;cursor:pointer;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="20 6 9 17 4 12"/></svg>
          Terapkan Sheet Ini (<span id="btnApplyActiveCount">0</span>)
        </button>
        <button type="button" id="btnApplyAllSheets" onclick="applySelectedExcelRows(true, <?= $penId ?>)" class="btn btn-primary btn-sm" style="font-weight:700;display:none;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;cursor:pointer;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="20 6 9 17 4 12"/></svg>
          Terapkan Semua Sheet (<span id="btnApplyAllCount">0</span>)
        </button>
        <button type="button" onclick="hideExcelResults()" class="btn btn-outline btn-sm" style="color:#64748b;padding:7px 12px;border-radius:8px;cursor:pointer;">
          Tutup Tabel
        </button>
      </div>
    </div>

    <!-- Sheet Tabs Pills Container -->
    <div id="excelSheetTabsContainer" style="display:none;margin-bottom:14px;background:#f8fafc;padding:10px 14px;border-radius:10px;border:1.5px solid #e2e8f0;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <div style="font-size:12.5px;font-weight:800;color:#334155;display:flex;align-items:center;gap:6px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" style="color:#4f46e5;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
        Pilih Sheet:
      </div>
      <div id="excelSheetTabsList" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <!-- Diisi dinamis via JS -->
      </div>
    </div>

    <!-- Kriteria Mapping Banner -->
    <div style="margin-bottom:14px;background:#f0f9ff;border:1.5px solid #bae6fd;border-radius:10px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <label for="globalExcelKriteriaSelect" style="font-size:13px;font-weight:800;color:#0c4a6e;margin:0;">
          📍 Kriteria untuk Sheet &ldquo;<span id="excelActiveSheetNameBadge" style="color:#0284c7;">Aktif</span>&rdquo;:
        </label>
        <select id="globalExcelKriteriaSelect" class="form-control" style="font-size:13px;font-weight:800;padding:6px 14px;border-radius:8px;max-width:380px;background:#fff;border:1.5px solid #0284c7;color:#0369a1;" onchange="applyGlobalKriteriaToExcelRows(this.value)">
          <!-- Diisi via JS -->
        </select>
      </div>
      <div style="font-size:12.5px;color:#0369a1;font-weight:700;" id="excelSelectionInfoBadge">
        ✓ Terpilih <span id="excelSelectedCount">0</span> dari <span id="excelActiveRowCount">0</span> standar di sheet ini
      </div>
    </div>

    <!-- Preview Table -->
    <div style="overflow-x:auto;border:1.5px solid #cbd5e1;border-radius:10px;background:#fff;max-height:480px;">
      <table style="width:100%;border-collapse:collapse;font-size:12.5px;" id="excelTablePreview">
        <thead>
          <tr style="background:#f1f5f9;border-bottom:1.5px solid #cbd5e1;color:#334155;font-weight:800;text-align:left;">
            <th style="padding:10px 12px;width:40px;text-align:center;">
              <input type="checkbox" id="excelSelectAllCheck" onchange="toggleSelectAllExcelRows(this.checked)" checked title="Pilih / Batal Semua" style="cursor:pointer;width:16px;height:16px;">
            </th>
            <th style="padding:10px 12px;width:45px;">No</th>
            <th style="padding:10px 12px;min-width:180px;">Aturan / Dasar Hukum</th>
            <th style="padding:10px 12px;min-width:240px;">Pernyataan Standar (Target)</th>
            <th style="padding:10px 12px;min-width:200px;">Indikator</th>
            <th style="padding:10px 12px;min-width:190px;background:#e0f2fe;color:#0369a1;">Kriteria</th>
            <th style="padding:10px 12px;width:100px;background:#e0f2fe;color:#0369a1;">Kode</th>
          </tr>
        </thead>
        <tbody id="excelTableBody">
          <!-- Diisi via JS -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- FORM UTAMA DETAIL STANDAR -->
<div id="standardsContainer">
  <?php if (empty($details)): ?>
  <div class="card" style="padding:40px;text-align:center;border-radius:14px;margin-bottom:20px;">
    <div style="font-size:15px;font-weight:800;color:#1e293b;margin-bottom:6px;">Belum Ada Standar yang Ditetapkan</div>
    <p style="font-size:13px;color:#64748b;margin-bottom:16px;">Gunakan Upload File Excel di atas atau klik tombol Tambah Standar di bawah.</p>
  </div>
  <?php else: ?>
  <?php foreach ($details as $idx => $d): ?>
  <?php
  $dId = (int)$d['id'];
  $kid = (int)$d['kriteria_id'];
  $displayKode = !empty($d['kode']) ? $d['kode'] : (!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD-' . ($idx + 1));
  $filled = 0;
  if (!empty($d['strategi']))       $filled++;
  if (!empty($d['target_capaian'])) $filled++;
  if (!empty($d['indikator']))      $filled++;
  $pct = round(($filled / 3) * 100);
  ?>

  <div class="ka-item" id="accordion_<?= $dId ?>" style="margin-bottom:16px;background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    
    <!-- Header Accordion -->
    <div class="ka-header" onclick="toggleAcc(<?= $dId ?>)" style="cursor:pointer;padding:16px 20px;display:flex;align-items:center;gap:14px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
      <div style="padding:4px 10px;background:#1a237e;color:#fff;border-radius:7px;font-weight:800;font-size:12px;" id="badgeKode_<?= $dId ?>">
        <?= htmlspecialchars($displayKode) ?>
      </div>
      <div style="flex:1;">
        <h4 style="font-size:14.5px;font-weight:800;color:#1e293b;margin:0 0 2px;"><?= htmlspecialchars($d['kriteria_nama']) ?></h4>
        <p style="font-size:12px;color:#64748b;margin:0;">Kode Standar: <strong id="subKodeText_<?= $dId ?>"><?= htmlspecialchars($displayKode) ?></strong></p>
      </div>
      
      <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
        <div style="width:80px;">
          <div style="height:6px;background:#e2e8f0;border-radius:10px;overflow:hidden;">
            <div id="progress_<?= $dId ?>" style="height:100%;background:linear-gradient(90deg,#4f46e5,#10b981);border-radius:10px;width:<?= $pct ?>%;transition:width 0.3s;"></div>
          </div>
          <div style="font-size:10.5px;color:<?= $pct === 100 ? '#059669' : '#64748b' ?>;margin-top:3px;text-align:center;font-weight:700;" id="pct_<?= $dId ?>"><?= $pct ?>%</div>
        </div>
        <span id="badge_<?= $dId ?>" style="font-size:11.5px;padding:4px 12px;border-radius:20px;font-weight:700;background:<?= $pct === 100 ? '#ecfdf5' : ($pct > 0 ? '#fffbeb' : '#f1f5f9') ?>;color:<?= $pct === 100 ? '#059669' : ($pct > 0 ? '#d97706' : '#64748b') ?>;">
          <?= $pct === 100 ? '✓ Lengkap' : ($pct > 0 ? 'Sebagian' : 'Kosong') ?>
        </span>
        <button type="button" onclick="event.stopPropagation(); deleteStandard(<?= $penId ?>, <?= $dId ?>)" class="btn btn-outline btn-sm" style="padding:4px 8px;color:#ef4444;border-color:#fecaca;" title="Hapus Standar Ini">
          🗑️
        </button>
        <svg id="toggle_<?= $dId ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" style="transition:transform 0.2s;transform:<?= $idx === 0 ? 'rotate(180deg)' : '' ?>;color:#64748b;">
          <polyline points="6 9 12 15 18 9"/>
        </svg>
      </div>
    </div>

    <!-- Body Detail Standar -->
    <div class="ka-body" id="body_<?= $dId ?>" style="padding:22px;display:<?= $idx === 0 ? 'block' : 'none' ?>;">
      
      <!-- Kriteria Terpilih (Langkah 1) & Kode Standar Input -->
      <div style="display:grid;grid-template-columns:1fr 220px;gap:14px;align-items:center;margin-bottom:18px;background:#eef2ff;padding:14px 16px;border-radius:10px;border:1.5px solid #c7d2fe;">
        <div>
          <label style="font-size:12px;font-weight:800;color:#3730a3;margin:0 0 4px;display:block;">🎯 Kriteria (Dipilih di Langkah 1):</label>
          <div style="display:flex;align-items:center;gap:8px;background:#fff;padding:8px 12px;border-radius:8px;border:1px solid #a5b4fc;font-size:13.5px;font-weight:700;color:#1e1b4b;">
            <span style="background:#4f46e5;color:#fff;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:900;text-transform:uppercase;">
              <?= htmlspecialchars(!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'KR') ?>
            </span>
            <span><?= htmlspecialchars($d['kriteria_nama'] ?? 'Kriteria') ?></span>
          </div>
          <input type="hidden" id="field_kriteria_<?= $dId ?>" value="<?= $kid ?>" data-penid="<?= $penId ?>" data-detailid="<?= $dId ?>" data-field="kriteria_id">
        </div>
        <div>
          <label style="font-size:12px;font-weight:800;color:#1e293b;margin:0 0 4px;display:block;">🏷️ Kode Standar:</label>
          <input type="text" class="form-control" id="field_kode_<?= $dId ?>" value="<?= htmlspecialchars($displayKode) ?>"
                 data-penid="<?= $penId ?>" data-detailid="<?= $dId ?>" data-field="kode"
                 onchange="saveStandardField(this); updateKodeBadge(<?= $dId ?>, this.value);"
                 style="font-size:13px;font-weight:700;background:#fff;" placeholder="Contoh: STD-01">
        </div>
      </div>

      <!-- 3 Header Excel Fields -->
      <div style="display:flex;flex-direction:column;gap:16px;">
        
        <!-- Header 1: Aturan / Dasar Hukum -->
        <div style="background:#f8faff;border:1.5px solid #c7d2fe;border-radius:12px;padding:16px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
            <label style="font-size:14.5px;font-weight:800;color:#1e1b4b;margin:0;display:flex;align-items:center;gap:8px;">
              <span style="display:inline-block;padding:3px 8px;background:#4f46e5;color:#fff;border-radius:5px;font-size:11px;font-weight:900;">HEADER 1</span>
              Aturan / Dasar Hukum / Regulasi Standar
            </label>
            <span class="save-indicator" id="save_strategi_<?= $dId ?>" style="font-size:12px;color:#10b981;font-weight:700;"></span>
          </div>
          <textarea class="form-control auto-grow" id="field_aturan_<?= $dId ?>" rows="3"
                    data-penid="<?= $penId ?>" data-detailid="<?= $dId ?>" data-field="strategi"
                    oninput="saveStandardField(this); updateProgress(<?= $dId ?>);"
                    placeholder="Contoh: Permendikbudristek No. 53 Tahun 2023, SK Rektor tentang Standar Kurikulum..."
                    style="font-size:15px;line-height:1.7;font-weight:500;"><?= htmlspecialchars($d['strategi'] ?? '') ?></textarea>
        </div>

        <!-- Header 2: Pernyataan Standar (Target Capaian) -->
        <div style="background:#f0fdf4;border:1.5px solid #a7f3d0;border-radius:12px;padding:16px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
            <label style="font-size:14.5px;font-weight:800;color:#064e3b;margin:0;display:flex;align-items:center;gap:8px;">
              <span style="display:inline-block;padding:3px 8px;background:#059669;color:#fff;border-radius:5px;font-size:11px;font-weight:900;">HEADER 2</span>
              Pernyataan Standar (Target Capaian) <span style="color:#ef4444;">*</span>
            </label>
            <span class="save-indicator" id="save_target_capaian_<?= $dId ?>" style="font-size:12px;color:#10b981;font-weight:700;"></span>
          </div>
          <textarea class="form-control auto-grow" id="field_target_<?= $dId ?>" rows="3"
                    data-penid="<?= $penId ?>" data-detailid="<?= $dId ?>" data-field="target_capaian"
                    oninput="saveStandardField(this); updateProgress(<?= $dId ?>);"
                    placeholder="Contoh: Program Studi menerapkan kurikulum berbasis OBE dengan persentase kelulusan tepat waktu minimal 85%..."
                    style="font-size:15px;line-height:1.7;font-weight:500;"><?= htmlspecialchars($d['target_capaian'] ?? '') ?></textarea>
        </div>

        <!-- Header 3: Indikator Ketercapaian -->
        <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;padding:16px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
            <label style="font-size:14.5px;font-weight:800;color:#78350f;margin:0;display:flex;align-items:center;gap:8px;">
              <span style="display:inline-block;padding:3px 8px;background:#d97706;color:#fff;border-radius:5px;font-size:11px;font-weight:900;">HEADER 3</span>
              Indikator Ketercapaian Standar <span style="color:#ef4444;">*</span>
            </label>
            <span class="save-indicator" id="save_indikator_<?= $dId ?>" style="font-size:12px;color:#10b981;font-weight:700;"></span>
          </div>
          <textarea class="form-control auto-grow" id="field_indikator_<?= $dId ?>" rows="3"
                    data-penid="<?= $penId ?>" data-detailid="<?= $dId ?>" data-field="indikator"
                    oninput="saveStandardField(this); updateProgress(<?= $dId ?>);"
                    placeholder="Contoh: Dokumen RPS terverifikasi 100%, IPK rata-rata lulusan >= 3.25..."
                    style="font-size:15px;line-height:1.7;font-weight:500;"><?= htmlspecialchars($d['indikator'] ?? '') ?></textarea>
        </div>

      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- SECTION: UPLOAD BERKAS SK PENETAPAN STANDAR MUTU (MULTI-UPLOAD SUPPORT) -->
<div style="background:#fff;border:1.5px solid #a5b4fc;border-radius:16px;padding:24px 28px;margin-top:24px;margin-bottom:24px;box-shadow:0 4px 16px rgba(79,70,229,0.06);">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:16px;border-bottom:1.5px solid #e0e7ff;padding-bottom:14px;">
    <div>
      <h3 style="font-size:18px;font-weight:900;color:#1e293b;margin:0 0 4px;display:flex;align-items:center;gap:8px;">
        <span>📜 Upload Berkas SK Penetapan Standar Mutu</span>
      </h3>
      <p style="font-size:13px;color:#64748b;margin:0;">
        Unggah Surat Keputusan (SK) Dekan/Pimpinan Fakultas pengesahan dokumen Penetapan ini. <strong>Dapat mengunggah lebih dari 1 berkas SK.</strong>
      </p>
    </div>

    <div>
      <button type="button" onclick="triggerUploadSkPenetapan()" class="btn btn-primary"
        style="background:#3f51b5;border:none;font-weight:800;font-size:12.5px;display:flex;align-items:center;gap:6px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15">
          <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
          <polyline points="17 8 12 3 7 8" />
          <line x1="12" y1="3" x2="12" y2="15" />
        </svg>
        + Upload Berkas SK Baru
      </button>
      <input type="file" id="skFileInputPenetapan" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display:none;" onchange="handleUploadSkPenetapan(this)">
    </div>
  </div>

  <!-- Form Metadata SK -->
  <div style="background:#f8faff;border:1px solid #c7d2fe;border-radius:10px;padding:14px;margin-bottom:16px;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:10px;align-items:end;">
    <div>
      <label style="font-size:11.5px;font-weight:700;color:#1e293b;margin-bottom:4px;display:block;">Nomor SK (Opsional):</label>
      <input type="text" id="skNomorInputPenetapan" class="form-control" placeholder="Contoh: SK/012/DEK/2026" style="font-size:12px;">
    </div>
    <div>
      <label style="font-size:11.5px;font-weight:700;color:#1e293b;margin-bottom:4px;display:block;">Judul / Perihal SK:</label>
      <input type="text" id="skJudulInputPenetapan" class="form-control" placeholder="Contoh: SK Pengesahan Standar Mutu SPMI" style="font-size:12px;">
    </div>
    <div>
      <label style="font-size:11.5px;font-weight:700;color:#1e293b;margin-bottom:4px;display:block;">Tanggal Penetapan SK:</label>
      <input type="date" id="skTglInputPenetapan" class="form-control" value="<?= date('Y-m-d') ?>" style="font-size:12px;">
    </div>
    <div>
      <button type="button" onclick="triggerUploadSkPenetapan()" class="btn btn-outline btn-sm"
        style="color:#3f51b5;border-color:#3f51b5;font-weight:700;height:35px;width:100%;">
        Pilih File SK &amp; Upload
      </button>
    </div>
  </div>

  <!-- Tabel Berkas SK yang Sudah Diupload -->
  <div style="overflow-x:auto;border:1px solid #e2e8f0;border-radius:10px;background:#fff;">
    <table style="width:100%;border-collapse:collapse;font-size:12.5px;" id="skTablePenetapan">
      <thead>
        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:800;text-align:left;">
          <th style="padding:10px 14px;width:30px;">#</th>
          <th style="padding:10px 14px;">Nomor SK</th>
          <th style="padding:10px 14px;">Perihal / Judul SK</th>
          <th style="padding:10px 14px;">Tanggal SK</th>
          <th style="padding:10px 14px;">Dokumen File</th>
          <th style="padding:10px 14px;width:100px;text-align:center;">Aksi</th>
        </tr>
      </thead>
      <tbody id="skTableBodyPenetapan">
        <?php 
          $berkasSkList = $penetapan['berkas_list'] ?? [];
          if (empty($berkasSkList)): 
        ?>
          <tr id="emptySkRowPenetapan">
            <td colspan="6" style="padding:24px;text-align:center;color:#94a3b8;font-style:italic;">
              Belum ada berkas SK yang diunggah. Anda dapat mengunggah satu atau beberapa berkas SK pengesahan.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($berkasSkList as $sIdx => $sk): ?>
            <tr id="sk_row_<?= htmlspecialchars($sk['id'] ?? '') ?>" style="border-bottom:1px solid #f1f5f9;">
              <td style="padding:10px 14px;color:#94a3b8;"><?= $sIdx + 1 ?></td>
              <td style="padding:10px 14px;font-weight:800;color:#1e293b;"><?= htmlspecialchars($sk['nomor_sk'] ?? '—') ?></td>
              <td style="padding:10px 14px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($sk['judul_sk'] ?? '—') ?></td>
              <td style="padding:10px 14px;color:#64748b;"><?= htmlspecialchars($sk['tanggal_sk'] ?? '—') ?></td>
              <td style="padding:10px 14px;">
                <a href="<?= htmlspecialchars($sk['url'] ?? BASE_URL . '/' . $sk['file_path']) ?>" target="_blank" style="color:#2563eb;font-weight:700;text-decoration:none;">
                  📄 <?= htmlspecialchars($sk['file_name'] ?? 'Buka SK') ?> ↗
                </a>
              </td>
              <td style="padding:10px 14px;text-align:center;">
                <button type="button" onclick="deleteSkItemPenetapan('<?= htmlspecialchars($sk['id'] ?? $sk['file_path']) ?>')" class="btn btn-outline btn-sm" style="color:#ef4444;border-color:#fca5a5;padding:4px 8px;font-size:11px;">
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

<!-- Tambah Standar Baru & Finalisasi Actions -->
<div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-top:20px;padding:16px;background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;">
  <div style="display:flex;align-items:center;gap:10px;">
    <select id="selectKriteriaForNew" class="form-control" style="font-size:13px;font-weight:600;min-width:240px;">
      <?php foreach ($allKriteriaList as $sk): ?>
      <option value="<?= $sk['id'] ?>"><?= htmlspecialchars(($sk['kode'] ? $sk['kode'] . ' ' : '') . $sk['nama']) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="button" onclick="addNewStandard(<?= $penId ?>)" class="btn btn-outline" style="font-weight:700;font-size:13px;color:#4f46e5;border-color:#c7d2fe;">
      + Tambah Standar Manual
    </button>
  </div>

  <div style="display:flex;gap:10px;">
    <a href="<?= BASE_URL ?>/ppepp<?= !empty($penetapan['ppepp_project_id']) ? '/' . $penetapan['ppepp_project_id'] : '' ?>" class="btn btn-outline" style="font-size:13px;">
      ← Kembali ke Project Library
    </a>
    <a href="<?= BASE_URL ?>/penetapan/<?= $penId ?>" class="btn btn-primary" style="font-weight:800;font-size:13px;background:#059669;border:none;">
      ✓ Selesai &amp; Lihat Dokumen
    </a>
  </div>
</div>

<script>
var BASE = '<?= BASE_URL ?>';
var SELECTED_KRITERIA_LIST = <?= json_encode(array_values($selectedKriteria)) ?>;
var ALL_KRITERIA_LIST      = <?= json_encode(array_values($allKriteria ?? $selectedKriteria)) ?>;

// Toggle accordion
function toggleAcc(id) {
  var b = document.getElementById('body_' + id);
  var t = document.getElementById('toggle_' + id);
  if (!b) return;
  var isHidden = (b.style.display === 'none' || !b.style.display);
  b.style.display = isHidden ? 'block' : 'none';
  if (t) t.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
}

function updateKodeBadge(id, val) {
  var badge = document.getElementById('badgeKode_' + id);
  var text = document.getElementById('subKodeText_' + id);
  if (badge) badge.textContent = val || 'STD';
  if (text) text.textContent = val || 'STD';
}

function updateKriteriaTitle(id, selectEl) {
  if (!selectEl || selectEl.selectedIndex < 0) return;
  var text = selectEl.options[selectEl.selectedIndex].text;
  var titleEl = document.querySelector('#accordion_' + id + ' .ka-header h4');
  if (titleEl) titleEl.textContent = text;
}

function updateProgress(id) {
  var a = document.getElementById('field_aturan_' + id)?.value.trim() || '';
  var t = document.getElementById('field_target_' + id)?.value.trim() || '';
  var i = document.getElementById('field_indikator_' + id)?.value.trim() || '';
  
  var filled = 0;
  if (a) filled++;
  if (t) filled++;
  if (i) filled++;
  var pct = Math.round((filled / 3) * 100);

  var progEl = document.getElementById('progress_' + id);
  var pctEl  = document.getElementById('pct_' + id);
  var badgeEl= document.getElementById('badge_' + id);

  if (progEl) progEl.style.width = pct + '%';
  if (pctEl) {
    pctEl.textContent = pct + '%';
    pctEl.style.color = pct === 100 ? '#059669' : '#64748b';
  }
  if (badgeEl) {
    badgeEl.textContent = pct === 100 ? '✓ Lengkap' : (pct > 0 ? 'Sebagian' : 'Kosong');
    badgeEl.style.background = pct === 100 ? '#ecfdf5' : (pct > 0 ? '#fffbeb' : '#f1f5f9');
    badgeEl.style.color = pct === 100 ? '#059669' : (pct > 0 ? '#d97706' : '#64748b');
  }
}

// Auto-save debounce timer
var saveTimers = {};
function saveStandardField(input) {
  var penId    = input.dataset.penid;
  var detailId = input.dataset.detailid;
  var field    = input.dataset.field;
  var val      = input.value;

  var ind = document.getElementById('save_' + field + '_' + detailId);
  if (ind) { ind.textContent = 'Menyimpan...'; ind.style.color = '#f59e0b'; }

  var key = field + '_' + detailId;
  clearTimeout(saveTimers[key]);

  saveTimers[key] = setTimeout(function() {
    var body = new URLSearchParams();
    body.append('penetapan_id', penId);
    body.append('detail_id', detailId);
    body.append('field', field);
    body.append('value', val);

    fetch(BASE + '/penetapan/save-draft-detail', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (ind) {
        if (data.success) {
          ind.textContent = '✓ Tersimpan ' + (data.saved_at || '');
          ind.style.color = '#10b981';
          setTimeout(function() { ind.textContent = ''; }, 2500);
        } else {
          ind.textContent = '✕ Gagal';
          ind.style.color = '#ef4444';
        }
      }
    })
    .catch(function() {
      if (ind) { ind.textContent = '✕ Error'; ind.style.color = '#ef4444'; }
    });
  }, 500);
}

// Tambah Standar Baru Manual
function addNewStandard(penId) {
  var select = document.getElementById('selectKriteriaForNew');
  var kid = select ? select.value : '';
  if (!kid) return;

  var body = new URLSearchParams();
  body.append('penetapan_id', penId);
  body.append('kriteria_id', kid);

  fetch(BASE + '/penetapan/add-standard-item', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      window.location.reload();
    } else {
      alert('Gagal menambah standar: ' + data.message);
    }
  });
}

// Hapus Standar
function deleteStandard(penId, detailId) {
  if (!confirm('Yakin ingin menghapus standar ini?')) return;

  var body = new URLSearchParams();
  body.append('penetapan_id', penId);
  body.append('detail_id', detailId);

  fetch(BASE + '/penetapan/delete-standard-item', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      var el = document.getElementById('accordion_' + detailId);
      if (el) el.remove();
    } else {
      alert('Gagal menghapus standar: ' + data.message);
    }
  });
}

// ============================================================
// EXCEL MULTI-SHEET & SELECTABLE ROWS HANDLERS
// ============================================================
window.EXCEL_SHEETS = [];
window.EXCEL_ACTIVE_SHEET_IDX = 0;

function triggerExcelUpload() {
  var input = document.getElementById('excelFileInput');
  if (input) input.click();
}

function handleExcelUpload(input) {
  if (!input.files || !input.files[0]) return;
  var file = input.files[0];
  var fd = new FormData();
  fd.append('file', file);
  fd.append('excel_file', file);

  var loading = document.getElementById('excelLoading');
  var container = document.getElementById('excelResultsContainer');
  if (loading) loading.style.display = 'block';
  if (container) container.style.display = 'none';

  fetch(BASE + '/penetapan/upload-excel', {
    method: 'POST',
    body: fd
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (loading) loading.style.display = 'none';
    if (data.success && ((data.sheets && data.sheets.length > 0) || (data.rows && data.rows.length > 0))) {
      var kriteriaList = (typeof SELECTED_KRITERIA_LIST !== 'undefined' && SELECTED_KRITERIA_LIST.length > 0) 
                         ? SELECTED_KRITERIA_LIST 
                         : (typeof ALL_KRITERIA_LIST !== 'undefined' ? ALL_KRITERIA_LIST : []);

      var rawSheets = (data.sheets && data.sheets.length > 0) 
                      ? data.sheets 
                      : [{ name: 'Standar Excel', total: data.rows.length, rows: data.rows }];

      // Inisialisasi setiap sheet & auto-matching kriteria
      window.EXCEL_SHEETS = rawSheets.map(function(sheet, sIdx) {
        var sNameLower = String(sheet.name || '').toLowerCase();
        var matchedKId = null;

        // Auto-match kriteria berdasarkan nama sheet
        kriteriaList.forEach(function(k) {
          if (matchedKId) return;
          var kNama = String(k.nama || '').toLowerCase();
          var kKode = String(k.kode || '').toLowerCase();

          if (sNameLower.includes('didik') && (kNama.includes('didik') || kKode === 'c6')) {
            matchedKId = k.id;
          } else if ((sNameLower.includes('teliti') || sNameLower.includes('riset')) && (kNama.includes('teliti') || kKode === 'c7')) {
            matchedKId = k.id;
          } else if ((sNameLower.includes('abdi') || sNameLower.includes('pkm')) && (kNama.includes('abdi') || kKode === 'c8')) {
            matchedKId = k.id;
          } else if (kNama.includes(sNameLower) || sNameLower.includes(kNama)) {
            matchedKId = k.id;
          }
        });

        if (!matchedKId && kriteriaList.length > 0) {
          matchedKId = kriteriaList[sIdx % kriteriaList.length].id;
        }

        var processedRows = (sheet.rows || []).map(function(r, rIdx) {
          return {
            _checked: true,
            no: r.no ? String(r.no).trim() : String(rIdx + 1),
            kode: r.kode ? String(r.kode).trim() : ('STD-' + String(rIdx + 1).padStart(2, '0')),
            aturan: r.aturan || '',
            pernyataan_standar: r.pernyataan_standar || '',
            indikator: r.indikator || '',
            kriteria_id: matchedKId
          };
        });

        return {
          name: sheet.name || ('Sheet ' + (sIdx + 1)),
          kriteria_id: matchedKId,
          rows: processedRows
        };
      });

      window.EXCEL_ACTIVE_SHEET_IDX = 0;
      renderExcelSheetTabs();
      renderActiveSheet();

      if (container) {
        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }
    } else {
      alert('Gagal membaca Excel: ' + (data.message || 'Tidak ada baris standar yang ditemukan.'));
    }
  })
  .catch(function(err) {
    if (loading) loading.style.display = 'none';
    alert('Error upload file Excel: ' + err.message);
  })
  .finally(function() {
    input.value = '';
  });
}

function hideExcelResults() {
  var container = document.getElementById('excelResultsContainer');
  if (container) container.style.display = 'none';
}

function renderExcelSheetTabs() {
  var tabsContainer = document.getElementById('excelSheetTabsContainer');
  var tabsList = document.getElementById('excelSheetTabsList');
  var btnApplyAll = document.getElementById('btnApplyAllSheets');
  if (!tabsList) return;

  if (!window.EXCEL_SHEETS || window.EXCEL_SHEETS.length <= 1) {
    if (tabsContainer) tabsContainer.style.display = 'none';
    if (btnApplyAll) btnApplyAll.style.display = 'none';
    return;
  }

  if (tabsContainer) tabsContainer.style.display = 'flex';
  if (btnApplyAll) btnApplyAll.style.display = 'inline-flex';

  var html = '';
  window.EXCEL_SHEETS.forEach(function(s, idx) {
    var isActive = idx === window.EXCEL_ACTIVE_SHEET_IDX;
    var checkedCount = s.rows.filter(function(r) { return r._checked; }).length;
    var totalCount = s.rows.length;

    var bgStyle = isActive ? 'background:#4f46e5;color:#fff;border:none;box-shadow:0 2px 8px rgba(79,70,229,0.3);' 
                           : 'background:#fff;color:#475569;border:1.5px solid #cbd5e1;';

    html += '<button type="button" onclick="switchExcelSheet(' + idx + ')" style="' + bgStyle + 'padding:6px 14px;border-radius:20px;font-size:12.5px;font-weight:700;display:flex;align-items:center;gap:6px;cursor:pointer;transition:all 0.15s ease;">';
    html += '<span>📄 ' + escHtml(s.name) + '</span>';
    html += '<span style="font-size:11px;padding:2px 7px;border-radius:10px;' + (isActive ? 'background:rgba(255,255,255,0.25);color:#fff;' : 'background:#f1f5f9;color:#64748b;') + '">';
    html += checkedCount + '/' + totalCount;
    html += '</span>';
    html += '</button>';
  });

  tabsList.innerHTML = html;
}

function switchExcelSheet(newIdx) {
  if (newIdx === window.EXCEL_ACTIVE_SHEET_IDX) return;
  saveActiveSheetInputs();
  window.EXCEL_ACTIVE_SHEET_IDX = newIdx;
  renderExcelSheetTabs();
  renderActiveSheet();
}

function saveActiveSheetInputs() {
  if (!window.EXCEL_SHEETS || !window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX]) return;
  var sheet = window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX];
  var globalSelect = document.getElementById('globalExcelKriteriaSelect');
  if (globalSelect && globalSelect.value) {
    sheet.kriteria_id = globalSelect.value;
  }

  sheet.rows.forEach(function(r, idx) {
    var chk = document.getElementById('ex_chk_' + idx);
    var aturanEl = document.getElementById('ex_aturan_' + idx);
    var targetEl = document.getElementById('ex_target_' + idx);
    var indEl = document.getElementById('ex_indikator_' + idx);
    var kodeEl = document.getElementById('ex_kode_' + idx);

    if (chk) r._checked = chk.checked;
    if (aturanEl) r.aturan = aturanEl.value;
    if (targetEl) r.pernyataan_standar = targetEl.value;
    if (indEl) r.indikator = indEl.value;
    if (kodeEl) r.kode = kodeEl.value;
    r.kriteria_id = sheet.kriteria_id;
  });
}

function renderActiveSheet() {
  if (!window.EXCEL_SHEETS || !window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX]) return;
  var sheet = window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX];

  var badgeEl = document.getElementById('excelActiveSheetNameBadge');
  if (badgeEl) badgeEl.textContent = sheet.name;

  var kriteriaList = (typeof SELECTED_KRITERIA_LIST !== 'undefined' && SELECTED_KRITERIA_LIST.length > 0) 
                     ? SELECTED_KRITERIA_LIST 
                     : (typeof ALL_KRITERIA_LIST !== 'undefined' ? ALL_KRITERIA_LIST : []);

  // Populate dropdown kriteria & pilih kriteria sheet
  var globalSelect = document.getElementById('globalExcelKriteriaSelect');
  if (globalSelect && kriteriaList.length > 0) {
    var optHtml = '';
    kriteriaList.forEach(function(k) {
      var label = (k.kode ? escHtml(k.kode) + ' - ' : '') + escHtml(k.nama);
      var isSel = String(k.id) === String(sheet.kriteria_id) ? ' selected' : '';
      optHtml += '<option value="' + k.id + '"' + isSel + '>' + label + '</option>';
    });
    globalSelect.innerHTML = optHtml;
  }

  var tbody = document.getElementById('excelTableBody');
  if (!tbody) return;

  var currentKObj = kriteriaList.find(function(k) { return String(k.id) === String(sheet.kriteria_id); }) || kriteriaList[0];
  var currentKLabel = currentKObj ? ((currentKObj.kode ? escHtml(currentKObj.kode) + ' - ' : '') + escHtml(currentKObj.nama)) : 'Kriteria';

  var html = '';
  sheet.rows.forEach(function(r, idx) {
    var isChecked = r._checked !== false;

    var kBadgeHtml = '<input type="hidden" class="excel-row-kriteria" id="ex_krit_' + idx + '" value="' + (currentKObj ? currentKObj.id : '') + '">' +
      '<div class="excel-kriteria-badge" style="background:#e0f2fe;border:1px solid #bae6fd;color:#0369a1;font-size:11.5px;font-weight:700;padding:6px 10px;border-radius:8px;line-height:1.4;">' +
        '✓ ' + currentKLabel +
      '</div>';

    html += '<tr id="ex_row_' + idx + '" style="border-bottom:1px solid #e2e8f0;background:' + (isChecked ? (idx % 2 === 0 ? '#fff' : '#fafbfc') : '#f8fafc') + ';opacity:' + (isChecked ? '1' : '0.5') + ';">';
    html += '<td style="padding:10px 12px;text-align:center;vertical-align:top;">' +
            '<input type="checkbox" id="ex_chk_' + idx + '" class="excel-row-checkbox" ' + (isChecked ? 'checked' : '') + ' onchange="onExcelRowCheckChange(' + idx + ', this.checked)" style="cursor:pointer;width:16px;height:16px;">' +
            '</td>';
    html += '<td style="padding:10px 12px;font-weight:700;color:#64748b;vertical-align:top;">' + escHtml(String(r.no || (idx + 1))) + '</td>';
    html += '<td style="padding:10px 12px;vertical-align:top;"><textarea class="form-control" id="ex_aturan_' + idx + '" rows="2" style="font-size:12px;width:100%;">' + escHtml(r.aturan || '') + '</textarea></td>';
    html += '<td style="padding:10px 12px;vertical-align:top;"><textarea class="form-control" id="ex_target_' + idx + '" rows="2" style="font-size:12px;width:100%;">' + escHtml(r.pernyataan_standar || '') + '</textarea></td>';
    html += '<td style="padding:10px 12px;vertical-align:top;"><textarea class="form-control" id="ex_indikator_' + idx + '" rows="2" style="font-size:12px;width:100%;">' + escHtml(r.indikator || '') + '</textarea></td>';
    html += '<td style="padding:10px 12px;vertical-align:top;background:#f0f9ff;">' + kBadgeHtml + '</td>';
    html += '<td style="padding:10px 12px;vertical-align:top;background:#f0f9ff;"><input type="text" class="form-control" id="ex_kode_' + idx + '" value="' + escHtml(r.kode || '') + '" style="font-size:12px;font-weight:700;"></td>';
    html += '</tr>';
  });

  tbody.innerHTML = html;
  updateExcelSelectionCounts();
}

function onExcelRowCheckChange(idx, checked) {
  if (!window.EXCEL_SHEETS || !window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX]) return;
  var sheet = window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX];
  if (sheet.rows[idx]) {
    sheet.rows[idx]._checked = checked;
  }
  var rowEl = document.getElementById('ex_row_' + idx);
  if (rowEl) {
    rowEl.style.opacity = checked ? '1' : '0.5';
    rowEl.style.background = checked ? (idx % 2 === 0 ? '#fff' : '#fafbfc') : '#f8fafc';
  }
  updateExcelSelectionCounts();
  renderExcelSheetTabs();
}

function toggleSelectAllExcelRows(checked) {
  if (!window.EXCEL_SHEETS || !window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX]) return;
  var sheet = window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX];
  sheet.rows.forEach(function(r, idx) {
    r._checked = checked;
    var chk = document.getElementById('ex_chk_' + idx);
    if (chk) chk.checked = checked;
    var rowEl = document.getElementById('ex_row_' + idx);
    if (rowEl) {
      rowEl.style.opacity = checked ? '1' : '0.5';
      rowEl.style.background = checked ? (idx % 2 === 0 ? '#fff' : '#fafbfc') : '#f8fafc';
    }
  });
  updateExcelSelectionCounts();
  renderExcelSheetTabs();
}

function applyGlobalKriteriaToExcelRows(selectedKId) {
  if (!selectedKId || !window.EXCEL_SHEETS || !window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX]) return;
  var sheet = window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX];
  sheet.kriteria_id = selectedKId;

  var kriteriaList = (typeof SELECTED_KRITERIA_LIST !== 'undefined' && SELECTED_KRITERIA_LIST.length > 0) 
                     ? SELECTED_KRITERIA_LIST 
                     : (typeof ALL_KRITERIA_LIST !== 'undefined' ? ALL_KRITERIA_LIST : []);
  var obj = kriteriaList.find(function(k) { return String(k.id) === String(selectedKId); });
  if (!obj) return;
  var label = (obj.kode ? escHtml(obj.kode) + ' - ' : '') + escHtml(obj.nama);

  var inputs = document.querySelectorAll('.excel-row-kriteria');
  inputs.forEach(function(inp) {
    inp.value = selectedKId;
    var container = inp.parentElement;
    var badge = container ? container.querySelector('.excel-kriteria-badge') : null;
    if (badge) {
      badge.innerHTML = '✓ ' + label;
    }
  });
}

function updateExcelSelectionCounts() {
  if (!window.EXCEL_SHEETS || !window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX]) return;
  var sheet = window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX];
  var activeChecked = sheet.rows.filter(function(r) { return r._checked; }).length;
  var activeTotal = sheet.rows.length;

  var allChecked = 0;
  window.EXCEL_SHEETS.forEach(function(s) {
    allChecked += s.rows.filter(function(r) { return r._checked; }).length;
  });

  var countEl = document.getElementById('excelRowCount');
  var activeRowEl = document.getElementById('excelActiveRowCount');
  var selCountEl = document.getElementById('excelSelectedCount');
  var btnActiveCount = document.getElementById('btnApplyActiveCount');
  var btnAllCount = document.getElementById('btnApplyAllCount');
  var headerCheck = document.getElementById('excelSelectAllCheck');

  if (countEl) countEl.textContent = activeTotal;
  if (activeRowEl) activeRowEl.textContent = activeTotal;
  if (selCountEl) selCountEl.textContent = activeChecked;
  if (btnActiveCount) btnActiveCount.textContent = activeChecked;
  if (btnAllCount) btnAllCount.textContent = allChecked;

  if (headerCheck) {
    headerCheck.checked = activeChecked === activeTotal && activeTotal > 0;
    headerCheck.indeterminate = activeChecked > 0 && activeChecked < activeTotal;
  }
}

function applySelectedExcelRows(applyAllSheets, penId) {
  saveActiveSheetInputs();

  var items = [];
  var targetSheets = applyAllSheets ? window.EXCEL_SHEETS : [window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX]];

  targetSheets.forEach(function(s) {
    var kid = s.kriteria_id;
    s.rows.forEach(function(r, idx) {
      if (!r._checked) return;
      if (!kid) return;

      items.push({
        kriteria_id: kid,
        kode: r.kode || ('STD-' + String(idx + 1).padStart(2, '0')),
        aturan: r.aturan || '',
        pernyataan_standar: r.pernyataan_standar || '',
        indikator: r.indikator || ''
      });
    });
  });

  if (items.length === 0) {
    alert('Tidak ada standar yang dicentang untuk dimasukkan ke form.');
    return;
  }

  var confirmMsg = applyAllSheets 
    ? 'Apakah Anda yakin ingin menerapkan ' + items.length + ' standar terpilih dari seluruh sheet?'
    : 'Apakah Anda yakin ingin menerapkan ' + items.length + ' standar terpilih dari sheet "' + window.EXCEL_SHEETS[window.EXCEL_ACTIVE_SHEET_IDX].name + '"?';

  if (!confirm(confirmMsg)) return;

  var body = new URLSearchParams();
  body.append('penetapan_id', penId);
  body.append('items', JSON.stringify(items));

  fetch(BASE + '/penetapan/apply-excel', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      alert('✓ ' + data.message + '\nHalaman akan memuat ulang untuk menampilkan standar yang baru diterapkan.');
      window.location.reload();
    } else {
      alert('Error: ' + (data.message || 'Gagal menyimpan data import.'));
    }
  })
  .catch(function(err) {
    alert('Error koneksi saat import: ' + err.message);
  });
}

function applyAllExcelRows(penId) {
  applySelectedExcelRows(false, penId);
}

function escHtml(str) {
  if (!str) return '';
  var d = document.createElement('div');
  d.appendChild(document.createTextNode(str));
  return d.innerHTML;
}

// Upload & Delete Berkas SK Penetapan
function triggerUploadSkPenetapan() {
  document.getElementById('skFileInputPenetapan').click();
}

function handleUploadSkPenetapan(input) {
  if (!input.files || !input.files[0]) return;
  var fd = new FormData();
  fd.append('penetapan_id', <?= $penId ?>);
  fd.append('nomor_sk', document.getElementById('skNomorInputPenetapan') ? document.getElementById('skNomorInputPenetapan').value : '');
  fd.append('judul_sk', document.getElementById('skJudulInputPenetapan') ? document.getElementById('skJudulInputPenetapan').value : '');
  fd.append('tanggal_sk', document.getElementById('skTglInputPenetapan') ? document.getElementById('skTglInputPenetapan').value : '');
  fd.append('berkas_sk_file', input.files[0]);

  fetch(BASE + '/penetapan/upload-sk', {
    method: 'POST',
    body: fd
  })
  .then(function (r) { return r.json(); })
  .then(function (data) {
    if (data.success && data.berkas) {
      var tbody = document.getElementById('skTableBodyPenetapan');
      var emptyRow = document.getElementById('emptySkRowPenetapan');
      if (emptyRow) emptyRow.remove();

      var s = data.berkas;
      var rowCount = tbody.querySelectorAll('tr').length + 1;
      var tr = document.createElement('tr');
      tr.id = 'sk_row_' + s.id;
      tr.style.cssText = 'border-bottom:1px solid #f1f5f9;';
      tr.innerHTML = '<td style="padding:10px 14px;color:#94a3b8;">' + rowCount + '</td>' +
        '<td style="padding:10px 14px;font-weight:800;color:#1e293b;">' + escHtml(s.nomor_sk || '—') + '</td>' +
        '<td style="padding:10px 14px;font-weight:700;color:#1e293b;">' + escHtml(s.judul_sk || '—') + '</td>' +
        '<td style="padding:10px 14px;color:#64748b;">' + escHtml(s.tanggal_sk || '—') + '</td>' +
        '<td style="padding:10px 14px;"><a href="' + escHtml(s.url) + '" target="_blank" style="color:#2563eb;font-weight:700;text-decoration:none;">📄 ' + escHtml(s.file_name) + ' ↗</a></td>' +
        '<td style="padding:10px 14px;text-align:center;"><button type="button" onclick="deleteSkItemPenetapan(\'' + s.id + '\')" class="btn btn-outline btn-sm" style="color:#ef4444;border-color:#fca5a5;padding:4px 8px;font-size:11px;">Hapus</button></td>';
      tbody.appendChild(tr);

      if (document.getElementById('skNomorInputPenetapan')) document.getElementById('skNomorInputPenetapan').value = '';
      if (document.getElementById('skJudulInputPenetapan')) document.getElementById('skJudulInputPenetapan').value = '';
      alert('✓ ' + data.message);
    } else {
      alert('Upload Gagal: ' + (data.message || 'Error'));
    }
  })
  .finally(function () {
    input.value = '';
  });
}

function deleteSkItemPenetapan(berkasId) {
  if (!confirm('Hapus berkas SK ini?')) return;
  var body = new URLSearchParams();
  body.append('penetapan_id', <?= $penId ?>);
  body.append('berkas_id', berkasId);

  fetch(BASE + '/penetapan/delete-sk', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function (r) { return r.json(); })
  .then(function (data) {
    if (data.success) {
      var row = document.getElementById('sk_row_' + berkasId);
      if (row) row.remove();
    }
  });
}
</script>
