<?php
$pageTitle   = 'Buat Pelaksanaan';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Pelaksanaan', 'url' => BASE_URL . '/pelaksanaan'],
  ['label' => 'Buat Baru'],
];
?>

<div class="page-header">
  <div>
    <h2>Buat Pelaksanaan Baru</h2>
    <p class="page-desc">Pilih dokumen penetapan standar sebagai acuan pelaksanaan kegiatan mutu.</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/ppepp<?= !empty($projectId) ? '/' . $projectId : '' ?>" class="btn btn-outline">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Kembali ke Project Library
    </a>
  </div>
</div>

<?php if (empty($penetapans)): ?>
<div class="card">
  <div class="empty-state" style="padding:60px 20px;">
    <div class="empty-icon">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    </div>
    <h3>Belum Ada Dokumen Penetapan</h3>
    <p>Tahap Pelaksanaan memerlukan acuan standar dari tahap Penetapan.<br>Silakan buat dokumen penetapan standar terlebih dahulu.</p>
    <a href="<?= BASE_URL ?>/penetapan" class="btn btn-primary" style="margin-top:14px;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      Ke Daftar Penetapan
    </a>
  </div>
</div>
<?php else: ?>

<form method="POST" action="<?= BASE_URL ?>/pelaksanaan/store" id="createPelaksanaanForm">
  <input type="hidden" name="project_id" value="<?= (int)($projectId ?? 0) ?>">

  <?php if (!empty($selectedProject)): ?>
  <div class="card mb-4" style="background:linear-gradient(135deg, #F0FDF4, #ECFDF5);border-color:#A7F3D0;">
    <div class="card-body" style="padding:14px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:36px;height:36px;border-radius:8px;background:var(--success);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;">
          📁
        </div>
        <div>
          <span style="font-size:11px;color:var(--success);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Folder Project PPEPP</span>
          <div style="font-size:14.5px;font-weight:700;color:var(--text-primary);"><?= htmlspecialchars($selectedProject['judul']) ?></div>
          <div style="font-size:12px;color:var(--text-secondary);">Acuan Penetapan difilter khusus untuk Project dan Tahun Ajaran ini.</div>
        </div>
      </div>
      <span class="badge badge-success" style="font-size:12px;padding:5px 12px;">
        T.A. <?= htmlspecialchars($selectedProject['ta_nama']) ?>
      </span>
    </div>
  </div>
  <?php endif; ?>

  <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:20px;">

    <!-- Form Kiri -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Informasi Pelaksanaan
        </h3>
      </div>
      <div class="card-body">
        <div class="form-group mb-3">
          <label for="judul" class="form-label">Judul / Kegiatan Pelaksanaan <span style="color:var(--danger);">*</span></label>
          <input type="text" id="judul" name="judul" class="form-control"
                 placeholder="Contoh: Laporan Pelaksanaan SPMI 2025/2026" required>
        </div>
        <div class="form-group mb-0">
          <label for="deskripsi" class="form-label">Deskripsi Ringkasan (Opsional)</label>
          <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"
                    placeholder="Catatan tambahan mengenai lingkup pelaksanaan kegiatan ini..."></textarea>
        </div>
      </div>
    </div>

    <!-- Form Kanan: Pilih Penetapan -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
          Pilih Penetapan Acuan
        </h3>
      </div>
      <div class="card-body" style="padding:16px;">
        <p style="font-size:12.5px;color:var(--text-secondary);margin-bottom:12px;">Pilih dokumen penetapan standar yang menjadi acuan pelaksanaan ini.</p>
        <div style="display:flex;flex-direction:column;gap:8px;" id="penetapanList">
          <?php foreach ($penetapans as $idx => $p): 
            $isFirst = ($idx === 0);
          ?>
          <label class="penetapan-option" style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;background:<?= $isFirst ? '#F0F9FF' : '#FFFFFF' ?>;border:1.5px solid <?= $isFirst ? 'var(--primary)' : 'var(--border)' ?>;border-radius:8px;cursor:pointer;transition:all 0.18s;">
            <input type="radio" name="penetapan_id" value="<?= $p['id'] ?>" <?= $isFirst ? 'checked' : '' ?> required
                   onchange="selectPenetapan(<?= $p['id'] ?>, this)"
                   style="margin-top:3px;flex-shrink:0;accent-color:var(--primary);">
            <div style="flex:1;min-width:0;">
              <div style="font-size:13.5px;font-weight:600;color:var(--text-primary);"><?= htmlspecialchars($p['judul']) ?></div>
              <div style="display:flex;gap:6px;margin-top:6px;flex-wrap:wrap;">
                <span class="badge badge-info"><?= htmlspecialchars($p['ta_nama']) ?></span>
                <?php if (!empty($p['semester'])): ?>
                <span class="badge badge-neutral"><?= htmlspecialchars($p['semester']) ?></span>
                <?php endif; ?>
                <span class="badge <?= $p['status'] === 'final' ? 'badge-success' : 'badge-warning' ?>">
                  <?= $p['status'] === 'final' ? 'Final ✓' : 'Draft' ?>
                </span>
              </div>
            </div>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

  </div>

  <!-- Actions -->
  <div style="display:flex;align-items:center;justify-content:space-between;margin-top:20px;padding:16px 20px;background:#fff;border-radius:10px;border:1px solid var(--border);">
    <a href="<?= BASE_URL ?>/pelaksanaan" class="btn btn-outline">
      Batal
    </a>
    <button type="submit" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Simpan &amp; Lanjutkan Pelaksanaan
    </button>
  </div>

</form>

<script>
function selectPenetapan(id, radio) {
  document.querySelectorAll('.penetapan-option').forEach(function(el) {
    el.style.borderColor = 'var(--border)';
    el.style.background  = '#ffffff';
  });
  var selected = radio.closest('.penetapan-option');
  if (selected) {
    selected.style.borderColor = 'var(--primary)';
    selected.style.background  = '#F0F9FF';
  }
}

document.querySelectorAll('.penetapan-option input').forEach(function(r) {
  r.addEventListener('change', function() {
    selectPenetapan(this.value, this);
  });
});
</script>

<?php endif; ?>
