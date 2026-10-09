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
    <p class="page-desc">Pilih penetapan sebagai acuan</p>
  </div>
  <a href="<?= BASE_URL ?>/ppepp<?= !empty($projectId) ? '/' . $projectId : '' ?>" class="btn btn-outline">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Kembali ke Project Library
  </a>
</div>

<?php if (empty($penetapans)): ?>
<div class="card">
  <div class="empty-state" style="padding:60px 20px;">
    <div class="empty-icon">
      <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    </div>
    <h3>Belum ada Penetapan</h3>
    <p>Pelaksanaan mengacu pada penetapan standar.<br>Silakan buat penetapan terlebih dahulu.</p>
    <a href="<?= BASE_URL ?>/penetapan" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      Ke Daftar Penetapan
    </a>
  </div>
</div>
<?php else: ?>

<form method="POST" action="<?= BASE_URL ?>/pelaksanaan/store" id="createPelaksanaanForm">
  <input type="hidden" name="project_id" value="<?= (int)($projectId ?? 0) ?>">

  <?php if (!empty($selectedProject)): ?>
  <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
    <div>
      <span style="font-size:11px;color:#166534;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Folder Project PPEPP</span>
      <div style="font-size:14px;font-weight:700;color:#14532D;"><?= htmlspecialchars($selectedProject['judul']) ?></div>
      <div style="font-size:12px;color:#166534;">Acuan Penetapan difilter khusus untuk Project dan Tahun Ajaran ini.</div>
    </div>
    <span style="font-size:12px;color:#166534;font-weight:600;background:#fff;padding:4px 12px;border-radius:6px;border:1px solid #BBF7D0;white-space:nowrap;">
      T.A. <?= htmlspecialchars($selectedProject['ta_nama']) ?>
    </span>
  </div>
  <?php endif; ?>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <!-- Form Kiri -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Informasi Pelaksanaan
        </div>
      </div>
      <div class="card-body">
        <div class="form-group">
          <label for="judul">Judul / Kegiatan Pelaksanaan <span style="color:#ef4444;">*</span></label>
          <input type="text" id="judul" name="judul" class="form-control"
                 placeholder="Contoh: Laporan Pelaksanaan PPEPP 2025/2026" required>
        </div>
        <div class="form-group">
          <label for="deskripsi">Deskripsi Ringkasan (Opsional)</label>
          <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                    placeholder="Catatan tambahan mengenai lingkup pelaksanaan ini..."></textarea>
        </div>
      </div>
    </div>

    <!-- Form Kanan: Pilih Penetapan -->
    <div>
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            Pilih Penetapan Acuan
          </div>
        </div>
        <div class="card-body" style="padding:14px;">
          <div style="font-size:12.5px;color:#64748b;margin-bottom:12px;">Pilih dokumen penetapan standar yang menjadi acuan pelaksanaan ini.</div>
          <div style="display:flex;flex-direction:column;gap:8px;" id="penetapanList">
            <?php foreach ($penetapans as $idx => $p): 
              $isFirst = ($idx === 0);
            ?>
            <label style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;background:<?= $isFirst ? '#F8FAFC' : '#fff' ?>;border:1.5px solid <?= $isFirst ? 'var(--primary)' : '#e2e8f0' ?>;border-radius:8px;cursor:pointer;transition:all 0.15s;" class="penetapan-option">
              <input type="radio" name="penetapan_id" value="<?= $p['id'] ?>" <?= $isFirst ? 'checked' : '' ?> required
                     onchange="selectPenetapan(<?= $p['id'] ?>, this)"
                     style="margin-top:2px;flex-shrink:0;accent-color:var(--primary);">
              <div style="flex:1;min-width:0;">
                <div style="font-size:13.5px;font-weight:600;color:var(--text);"><?= htmlspecialchars($p['judul']) ?></div>
                <div style="display:flex;gap:7px;margin-top:6px;flex-wrap:wrap;">
                  <span class="badge badge-primary"><?= htmlspecialchars($p['ta_nama']) ?></span>
                  <?php if ($p['semester']): ?>
                  <span class="badge badge-gray"><?= htmlspecialchars($p['semester']) ?></span>
                  <?php endif; ?>
                  <span class="badge <?= $p['status'] === 'final' ? 'badge-success' : 'badge-warning' ?>"><?= $p['status'] === 'final' ? 'Final' : 'Draft' ?></span>
                </div>
              </div>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Preview Kriteria -->
      <div class="card" id="previewCard" style="display:none;margin-top:16px;">
        <div class="card-header">
          <div class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Kriteria yang Akan Dipantau
          </div>
        </div>
        <div class="card-body" style="padding:14px;" id="previewBody">
        </div>
      </div>
    </div>

  </div>

  <!-- Actions -->
  <div style="display:flex;align-items:center;justify-content:space-between;margin-top:24px;padding:16px 20px;background:#fff;border-radius:8px;border:1px solid #e2e8f0;">
    <a href="<?= BASE_URL ?>/pelaksanaan" class="btn btn-outline">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
      Batal
    </a>
    <button type="submit" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Buat Pelaksanaan
    </button>
  </div>

</form>

<!-- Data penetapan untuk preview (inline JSON) -->
<script>
var penetapanData = <?= json_encode(array_map(function($p) {
    global $penetapans;
    return [
        'id' => $p['id'],
        'judul' => $p['judul'],
    ];
}, $penetapans)) ?>;

function selectPenetapan(id, radio) {
  // Highlight selected
  document.querySelectorAll('.penetapan-option').forEach(function(el) {
    el.style.borderColor = '#e2e8f0';
    el.style.background  = '#fff';
  });
  var selected = radio.closest('.penetapan-option');
  if (selected) {
    selected.style.borderColor = 'var(--primary)';
    selected.style.background  = '#F8FAFC';
  }
}

document.querySelectorAll('.penetapan-option input').forEach(function(r) {
  r.addEventListener('change', function() {
    selectPenetapan(this.value, this);
  });
});
</script>

<?php endif; ?>
