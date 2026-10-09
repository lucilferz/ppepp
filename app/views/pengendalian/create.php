<?php
$pageTitle   = 'Buat Pengendalian (RTL)';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Pengendalian', 'url' => BASE_URL . '/pengendalian'],
  ['label' => 'Buat Pengendalian Baru'],
];
?>

<div class="page-header">
  <div>
    <h2>Buat Pengendalian Baru</h2>
    <p class="page-desc">Pilih Penetapan Standar sebagai acuan untuk menyusun Rencana Tindak Lanjut (RTL) dan Usulan Koreksi Standar</p>
  </div>
  <a href="<?= BASE_URL ?>/ppepp<?= !empty($projectId) ? '/' . $projectId : '' ?>" class="btn btn-outline">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Kembali ke Project Library
  </a>
</div>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
      Form Pengendalian Standar Baru
    </h3>
  </div>
  <div class="card-body">
    <form action="<?= BASE_URL ?>/pengendalian/store" method="POST">
      <input type="hidden" name="project_id" value="<?= (int)($projectId ?? 0) ?>">

      <?php if (!empty($selectedProject)): ?>
      <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;padding:12px 16px;margin-bottom:18px;display:flex;align-items:center;justify-space-between;gap:12px;">
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

      <div class="form-group" style="margin-bottom:18px;">
        <label for="penetapan_id" style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:6px;display:block;">
          Penetapan Standar Acuan <span style="color:var(--danger);">*</span>
        </label>
        <?php if (empty($penetapans)): ?>
        <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:8px;padding:14px 18px;font-size:13px;color:#92400E;">
          <strong>Belum ada Penetapan Standar.</strong>
          <a href="<?= BASE_URL ?>/penetapan/create" style="color:var(--primary);text-decoration:underline;">Buat penetapan terlebih dahulu →</a>
        </div>
        <?php else: ?>
        <select id="penetapan_id" name="penetapan_id" class="form-control" required onchange="fillPenetapanInfo(this)" style="font-size:13px;padding:9px 12px;">
          <option value="">— Pilih Penetapan Standar —</option>
          <?php foreach ($penetapans as $p): ?>
          <option value="<?= $p['id'] ?>"
                  data-judul="<?= htmlspecialchars($p['judul']) ?>"
                  data-ta="<?= htmlspecialchars($p['ta_nama']) ?>"
                  data-sem="<?= htmlspecialchars($p['semester'] ?? '') ?>"
                  data-ev="<?= htmlspecialchars($p['evaluasi_judul'] ?? 'Belum ada evaluasi') ?>">
            <?= htmlspecialchars($p['judul']) ?> — <?= htmlspecialchars($p['ta_nama']) ?> <?= !empty($p['evaluasi_id']) ? '(Evaluasi Terhubung)' : '' ?>
          </option>
          <?php endforeach; ?>
        </select>
        <?php endif; ?>
        <div id="penetapanInfo" style="display:none;margin-top:10px;padding:10px 14px;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:6px;font-size:12.5px;color:var(--text);"></div>
      </div>

      <div class="form-group" style="margin-bottom:18px;">
        <label for="judul" style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:6px;display:block;">
          Judul Dokumen Pengendalian <span style="color:var(--danger);">*</span>
        </label>
        <input type="text" id="judul" name="judul" class="form-control"
               placeholder="Contoh: Rencana Tindak Lanjut (RTL) Pengendalian T.A. 2025/2026" required
               style="font-size:13px;padding:9px 12px;">
      </div>

      <div class="form-group" style="margin-bottom:24px;">
        <label for="deskripsi" style="font-size:13px;font-weight:600;color:var(--text-muted);margin-bottom:6px;display:block;">
          Deskripsi / Catatan Umum (Opsional)
        </label>
        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                  placeholder="Catatan pengantar pengendalian standar dan arahan tindak lanjut..."
                  style="font-size:13px;"></textarea>
      </div>

      <?php if (!empty($penetapans)): ?>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <a href="<?= BASE_URL ?>/pengendalian" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/>
          </svg>
          Lanjut ke Form RTL &amp; Koreksi Standar →
        </button>
      </div>
      <?php endif; ?>

    </form>
  </div>
</div>

<script>
function fillPenetapanInfo(sel) {
  var opt = sel.options[sel.selectedIndex];
  var el  = document.getElementById('penetapanInfo');
  if (opt.value) {
    el.style.display = 'block';
    el.innerHTML = '<strong>Penetapan:</strong> ' + opt.getAttribute('data-judul') +
      ' &nbsp;·&nbsp; <strong>Tahun Ajaran:</strong> ' + opt.getAttribute('data-ta') +
      ' &nbsp;·&nbsp; <strong>Status Evaluasi:</strong> ' + opt.getAttribute('data-ev');
    var judulEl = document.getElementById('judul');
    if (!judulEl.value) {
      judulEl.value = 'RTL Pengendalian ' + opt.getAttribute('data-ta');
    }
  } else {
    el.style.display = 'none';
  }
}
</script>
