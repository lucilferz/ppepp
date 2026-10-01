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
    <h2>Referensi & Analisis AI</h2>
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
<div class="ai-panel mb-4">
  <div class="ai-panel-header">
    <div class="ai-icon">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
      </svg>
    </div>
    <div>
      <h3>Analisis Dokumen dengan AI (Gemini)</h3>
      <p>Upload dokumen evaluasi, notulensi rapat, atau laporan — AI akan menganalisis isi dan mengekstrak informasi yang relevan dengan kriteria penetapan Anda</p>
    </div>
  </div>
  <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:16px;">
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,0.75);">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
      </svg>
      Mendukung PDF, DOCX, TXT
    </div>
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,0.75);">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
        <path d="M4 6h16M4 10h16M4 14h16"/>
      </svg>
      Analisis per kriteria prodi
    </div>
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,0.75);">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
      </svg>
      Hasil disimpan sebagai referensi penetapan
    </div>
  </div>
  <?php if (empty($user['gemini_api_key'])): ?>
  <div style="margin-top:14px;background:rgba(232,160,32,0.15);border:1px solid rgba(232,160,32,0.3);border-radius:8px;padding:12px 16px;font-size:13px;color:var(--accent-light);">
    ⚠️ <strong>API Key Akun Prodi Belum Dikonfigurasi.</strong> Buka menu <a href="<?= BASE_URL ?>/setting" style="color:var(--accent-light);text-decoration:underline;font-weight:700;">Pengaturan API Key</a> untuk menginput API Key khusus program studi Anda.
  </div>
  <?php else: ?>
  <div style="margin-top:14px;background:rgba(34,160,107,0.15);border:1px solid rgba(34,160,107,0.3);border-radius:8px;padding:12px 16px;font-size:13px;color:#6ee7b7;">
    ✓ <strong>API Key Akun Prodi Terpasang:</strong> Analisis AI akan diproses menggunakan Gemini API Key milik <?= htmlspecialchars($user['nama_prodi']) ?>.
  </div>
  <?php endif; ?>
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
    <div style="display:flex;gap:8px;">
      <button id="btnSelectAllKriteria" class="btn btn-outline btn-sm">Pilih Semua</button>
      <button id="btnSelectNoneKriteria" class="btn btn-outline btn-sm">Hapus Pilihan</button>
    </div>
  </div>
  <div class="card-body">
    <div class="checkbox-group" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:10px;">
      <?php foreach ($kriteria as $k): ?>
      <div class="checkbox-item">
        <input type="checkbox" id="kr_<?= $k['id'] ?>" name="kriteria_ids[]"
               value="<?= $k['id'] ?>" checked>
        <label for="kr_<?= $k['id'] ?>">
          <span style="font-weight:700;color:var(--primary);"><?= htmlspecialchars($k['kode']) ?></span>
          — <?= htmlspecialchars($k['nama']) ?>
        </label>
      </div>
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
    <button class="btn btn-primary" data-modal-open="modalUpload">
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
          · <span class="badge badge-info" style="font-size:11px;"><?= htmlspecialchars($ref['kriteria_kode']) ?></span>
          <?php endif; ?>
          <?php if ($ref['deskripsi']): ?>
          · <span style="color:var(--text-muted)"><?= htmlspecialchars($ref['deskripsi']) ?></span>
          <?php endif; ?>
          <?php if ($ref['konten_teks']): ?>
          · <span style="color:var(--success);font-size:11px;">✓ Teks berhasil diekstrak</span>
          <?php else: ?>
          · <span style="color:var(--danger);font-size:11px;">⚠ Teks tidak bisa diekstrak</span>
          <?php endif; ?>
        </div>
      </div>
      <div class="file-actions">
        <?php if ($ref['ai_hasil']): ?>
        <span class="badge badge-success">✓ AI Selesai</span>
        <?php endif; ?>
        <?php if ($ref['konten_teks'] && !empty($kriteria)): ?>
        <button class="btn btn-accent btn-sm"
                data-analyze-btn="<?= $ref['id'] ?>">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
          </svg>
          <?= $ref['ai_hasil'] ? 'Analisis Ulang' : 'Analisis AI' ?>
        </button>
        <?php endif; ?>
        <button class="btn btn-outline btn-sm"
                data-delete-ref="<?= $ref['id'] ?>"
                data-nama="<?= htmlspecialchars($ref['nama_asli'], ENT_QUOTES) ?>">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Panel Hasil AI per file -->
    <div id="aiPanel_<?= $ref['id'] ?>" style="display:<?= $ref['ai_hasil'] ? 'block' : 'none' ?>;margin-bottom:20px;">
      <div class="ai-panel">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
          <div style="display:flex;align-items:center;gap:10px;">
            <div class="ai-icon" style="width:32px;height:32px;">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
              </svg>
            </div>
            <div>
              <p style="font-size:13px;font-weight:600;color:#fff;margin:0;">Hasil Analisis AI</p>
              <p style="font-size:11px;color:rgba(255,255,255,0.5);margin:0;"><?= htmlspecialchars($ref['nama_asli']) ?></p>
            </div>
          </div>
        </div>
        <div id="aiLoading_<?= $ref['id'] ?>" class="ai-loading">
          <div class="spinner"></div>
          <span>AI sedang menganalisis dokumen...</span>
        </div>
        <div id="aiResult_<?= $ref['id'] ?>" class="ai-result" style="display:<?= $ref['ai_hasil'] ? 'block' : 'none' ?>;">
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
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
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
          <h4>Drag & drop file di sini atau klik untuk pilih</h4>
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
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
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
      <p style="font-size:14px;">
        Apakah Anda yakin ingin menghapus file <strong id="deleteRefName"></strong>?
      </p>
      <p style="font-size:13px;color:var(--text-muted);margin-top:8px;">
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
