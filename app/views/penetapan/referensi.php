<?php
$pageTitle   = 'Referensi & Analisis AI';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Penetapan', 'url' => BASE_URL . '/penetapan'],
  ['label' => 'Referensi & AI'],
];
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h2>Referensi &amp; Analisis AI</h2>
    <p class="page-desc">Upload dokumen referensi (notulensi, evaluasi, laporan) dan analisis dengan AI untuk mengekstrak informasi sesuai kriteria penetapan</p>
  </div>
  <button class="btn btn-primary" data-modal-open="modalUpload">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
      <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
    </svg>
    Upload Dokumen
  </button>
</div>

<!-- AI Info Banner -->
<div class="card mb-4" style="background:#fff;padding:20px;">
  <div style="display:flex;align-items:flex-start;gap:14px;">
    <div style="width:40px;height:40px;background:var(--primary-subtle);border:1px solid #D3E0F0;color:var(--primary);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
      </svg>
    </div>
    <div style="flex:1;">
      <h3 style="font-size:15px;font-weight:700;color:var(--text-main);margin:0 0 4px;">Analisis Dokumen dengan AI (Gemini)</h3>
      <p style="font-size:12.5px;color:var(--text-muted);margin:0 0 12px;line-height:1.5;">
        Upload dokumen evaluasi, notulensi rapat, atau laporan — AI akan menganalisis isi dan mengekstrak informasi yang relevan dengan kriteria penetapan Anda.
      </p>

      <div style="display:flex;gap:16px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-muted);">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
          </svg>
          Mendukung PDF, DOCX, TXT
        </div>
        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-muted);">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
            <path d="M4 6h16M4 10h16M4 14h16"/>
          </svg>
          Analisis per kriteria prodi
        </div>
        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-muted);">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
          </svg>
          Hasil disimpan sebagai referensi penetapan
        </div>
      </div>

      <?php if (empty($user['gemini_api_key'])): ?>
      <div class="alert alert-warning" style="margin-top:14px;margin-bottom:0;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <div>
          <strong>API Key Akun Prodi Belum Dikonfigurasi.</strong> Buka menu <a href="<?= BASE_URL ?>/setting" style="color:var(--warning);text-decoration:underline;font-weight:700;">Pengaturan API Key</a> untuk menginput API Key khusus program studi Anda.
        </div>
      </div>
      <?php else: ?>
      <div class="alert alert-success" style="margin-top:14px;margin-bottom:0;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
        <div>
          <strong>API Key Akun Prodi Terpasang:</strong> Analisis AI akan diproses menggunakan Gemini API Key milik <?= htmlspecialchars($user['nama_prodi']) ?>.
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php if (!empty($referensi)): ?>
<!-- Kriteria Filter -->
<div class="card mb-4">
  <div class="card-header">
    <h3 class="card-title">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
      </svg>
      Pilih Kriteria untuk Analisis AI
    </h3>
    <div style="display:flex;gap:6px;">
      <button id="btnSelectAllKriteria" class="btn btn-outline btn-sm">Pilih Semua</button>
      <button id="btnSelectNoneKriteria" class="btn btn-outline btn-sm">Hapus Pilihan</button>
    </div>
  </div>
  <div class="card-body">
    <div class="checkbox-group" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:8px;">
      <?php foreach ($kriteria as $k): ?>
      <label class="checkbox-item" style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);cursor:pointer;background:#FFFFFF;">
        <input type="checkbox" id="kr_<?= $k['id'] ?>" name="kriteria_ids[]"
               value="<?= $k['id'] ?>" checked style="accent-color:var(--primary);">
        <span style="font-size:12.5px;">
          <strong style="color:var(--primary);"><?= htmlspecialchars($k['kode']) ?></strong>
          — <?= htmlspecialchars($k['nama']) ?>
        </span>
      </label>
      <?php endforeach; ?>
    </div>
    <div class="form-group mt-3" style="margin-bottom:0;">
      <label for="aiPertanyaan">Pertanyaan Khusus untuk AI (opsional)</label>
      <input type="text" id="aiPertanyaan" class="form-control"
             placeholder="Contoh: Fokus pada pencapaian IPK mahasiswa dan tingkat kelulusan...">
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Daftar File Referensi -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
      </svg>
      Dokumen Referensi
    </h3>
    <span class="badge badge-primary"><?= count($referensi) ?> file</span>
  </div>

  <?php if (empty($referensi)): ?>
  <div class="empty-state">
    <div class="empty-icon">
      <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
        <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
      </svg>
    </div>
    <h3>Belum ada dokumen referensi</h3>
    <p>Upload dokumen evaluasi, notulensi, atau laporan untuk dianalisis AI</p>
    <button class="btn btn-primary btn-sm" data-modal-open="modalUpload">
      Upload Dokumen Pertama
    </button>
  </div>
  <?php else: ?>
  <div class="card-body">
    <?php foreach ($referensi as $ref): ?>
    <?php $ext = strtolower($ref['tipe_file']); ?>
    <div class="file-list-item" id="refItem_<?= $ref['id'] ?>">
      <div class="file-icon <?= $ext ?>">
        <?= strtoupper($ext) ?>
      </div>
      <div class="file-info">
        <div class="file-name"><?= htmlspecialchars($ref['nama_asli']) ?></div>
        <div class="file-meta">
          <?= Referensi::formatSize($ref['ukuran']) ?>
          · <?= date('d M Y H:i', strtotime($ref['created_at'])) ?>
          <?php if ($ref['kriteria_kode']): ?>
          · <span class="badge badge-primary" style="font-size:10.5px;"><?= htmlspecialchars($ref['kriteria_kode']) ?></span>
          <?php endif; ?>
          <?php if ($ref['deskripsi']): ?>
          · <span style="color:var(--text-muted)"><?= htmlspecialchars($ref['deskripsi']) ?></span>
          <?php endif; ?>
          <?php if ($ref['konten_teks']): ?>
          · <span style="color:var(--success);font-size:11.5px;font-weight:600;">✓ Teks berhasil diekstrak</span>
          <?php else: ?>
          · <span style="color:var(--danger);font-size:11.5px;font-weight:600;">⚠ Teks tidak bisa diekstrak</span>
          <?php endif; ?>
        </div>
      </div>
      <div class="file-actions">
        <?php if ($ref['ai_hasil']): ?>
        <span class="badge badge-success">AI Selesai</span>
        <?php endif; ?>
        <?php if ($ref['konten_teks'] && !empty($kriteria)): ?>
        <button class="btn btn-primary btn-sm"
                data-analyze-btn="<?= $ref['id'] ?>">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
          </svg>
          <?= $ref['ai_hasil'] ? 'Analisis Ulang' : 'Analisis AI' ?>
        </button>
        <?php endif; ?>
        <button class="btn btn-outline btn-sm"
                data-delete-ref="<?= $ref['id'] ?>"
                data-nama="<?= htmlspecialchars($ref['nama_asli'], ENT_QUOTES) ?>">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13">
            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Panel Hasil AI per file -->
    <div id="aiPanel_<?= $ref['id'] ?>" style="display:<?= $ref['ai_hasil'] ? 'block' : 'none' ?>;margin-bottom:20px;margin-top:10px;">
      <div class="card" style="background:#FFFFFF;border:1px solid var(--border);border-radius:var(--radius);padding:18px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;border-bottom:1px solid var(--border);padding-bottom:10px;">
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:30px;height:30px;border-radius:var(--radius-sm);background:var(--primary-subtle);color:var(--primary);display:flex;align-items:center;justify-content:center;">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
              </svg>
            </div>
            <div>
              <p style="font-size:13px;font-weight:700;color:var(--text-main);margin:0;">Hasil Analisis AI</p>
              <p style="font-size:11.5px;color:var(--text-muted);margin:0;"><?= htmlspecialchars($ref['nama_asli']) ?></p>
            </div>
          </div>
        </div>
        <div id="aiLoading_<?= $ref['id'] ?>" class="ai-loading">
          <div class="spinner"></div>
          <span>AI sedang menganalisis dokumen...</span>
        </div>
        <div id="aiResult_<?= $ref['id'] ?>" class="ai-result-card" style="display:<?= $ref['ai_hasil'] ? 'block' : 'none' ?>;">
          <?= $ref['ai_hasil'] ? renderMarkdown($ref['ai_hasil']) : '' ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ============================================================
     MODAL: Upload File
     ============================================================ -->
<div class="modal-overlay" id="modalUpload">
  <div class="modal modal-lg">
    <div class="modal-header">
      <h3>
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18">
          <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
          <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
        Upload Dokumen Referensi
      </h3>
      <button class="modal-close" data-modal-close="modalUpload">✕</button>
    </div>
    <form action="<?= BASE_URL ?>/penetapan/referensi/upload" method="POST" enctype="multipart/form-data">
      <div class="modal-body">
        <!-- Dropzone -->
        <div class="dropzone mb-4">
          <input type="file" name="file" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx">
          <div class="dropzone-icon">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
              <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
          </div>
          <h4>Drag &amp; drop file di sini atau klik untuk pilih</h4>
          <p>Maksimal 10 MB per file</p>
          <div class="file-types">
            <span class="file-type-badge">PDF</span>
            <span class="file-type-badge">DOCX</span>
            <span class="file-type-badge">TXT</span>
            <span class="file-type-badge">XLS</span>
            <span class="file-type-badge">XLSX</span>
          </div>
        </div>

        <div class="grid-2">
          <div class="form-group">
            <label for="kriteria_id">Kaitkan dengan Kriteria (opsional)</label>
            <select name="kriteria_id" id="kriteria_id" class="form-control">
              <option value="">— Semua Kriteria —</option>
              <?php foreach ($kriteria as $k): ?>
              <option value="<?= $k['id'] ?>">[<?= htmlspecialchars($k['kode']) ?>] <?= htmlspecialchars($k['nama']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="deskripsi_file">Deskripsi File (opsional)</label>
            <input type="text" id="deskripsi_file" name="deskripsi" class="form-control"
                   placeholder="Contoh: Notulensi Rapat Prodi Jan 2024">
          </div>
        </div>

        <div class="alert alert-info" style="margin:0;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
          </svg>
          <div>
            Teks dari file akan diekstrak otomatis. Setelah upload, klik tombol <strong>"Analisis AI"</strong>
            untuk mendapatkan insight berdasarkan kriteria prodi Anda.
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close="modalUpload">Batal</button>
        <button type="submit" class="btn btn-primary">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15">
            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
          </svg>
          Upload File
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================================
     MODAL: Konfirmasi Hapus File
     ============================================================ -->
<div class="modal-overlay" id="modalDeleteRef">
  <div class="modal" style="max-width:420px;">
    <div class="modal-header">
      <h3 style="color:var(--danger);">Hapus Referensi</h3>
      <button class="modal-close" data-modal-close="modalDeleteRef">✕</button>
    </div>
    <div class="modal-body">
      <p style="font-size:13.5px;color:var(--text-main);">
        Apakah Anda yakin ingin menghapus file <strong id="deleteRefName"></strong>?
      </p>
      <p style="font-size:12.5px;color:var(--text-muted);margin-top:8px;">
        File dan hasil analisis AI akan dihapus permanen dan tidak dapat dikembalikan.
      </p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline" data-modal-close="modalDeleteRef">Batal</button>
      <form action="<?= BASE_URL ?>/penetapan/referensi/delete" method="POST" style="display:inline;">
        <input type="hidden" name="id" id="deleteRefId">
        <button type="submit" class="btn btn-danger">Hapus Permanen</button>
      </form>
    </div>
  </div>
</div>
