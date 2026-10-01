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
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    ← Kembali ke Project Library
  </a>
</div>

<div class="card" style="border-radius:14px;border:1.5px solid #e2e8f0;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.03);">
  <div class="card-header" style="background:#f8fafc;padding:18px 22px;border-bottom:1.5px solid #e2e8f0;">
    <h3 class="card-title" style="font-size:15px;font-weight:800;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px;">
      <svg fill="none" stroke="#ea580c" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
      Form Pengendalian Standar Baru
    </h3>
  </div>
  <div class="card-body" style="padding:22px;">
    <form action="<?= BASE_URL ?>/pengendalian/store" method="POST">
      <input type="hidden" name="project_id" value="<?= (int)($projectId ?? 0) ?>">

      <?php if (!empty($selectedProject)): ?>
      <div style="background:#fff7ed;border:1px solid #ffedd5;border-radius:10px;padding:12px 16px;margin-bottom:18px;display:flex;align-items:center;justify-space-between;gap:12px;">
        <div>
          <span style="font-size:11px;color:#c2410c;font-weight:800;text-transform:uppercase;">📌 Folder Project PPEPP</span>
          <div style="font-size:14px;font-weight:800;color:#7c2d12;"><?= htmlspecialchars($selectedProject['judul']) ?></div>
          <div style="font-size:12px;color:#ea580c;">Acuan Penetapan difilter khusus untuk Project dan Tahun Ajaran ini.</div>
        </div>
        <span style="font-size:12px;color:#c2410c;font-weight:700;background:#fff;padding:4px 12px;border-radius:14px;border:1px solid #ffedd5;white-space:nowrap;">
          📅 T.A. <?= htmlspecialchars($selectedProject['ta_nama']) ?>
        </span>
      </div>
      <?php endif; ?>

      <div class="form-group" style="margin-bottom:18px;">
        <label for="penetapan_id" style="font-size:13px;font-weight:800;color:#1e293b;margin-bottom:6px;display:block;">
          Penetapan Standar Acuan <span style="color:#ef4444;">*</span>
        </label>
        <?php if (empty($penetapans)): ?>
        <div style="background:#fef9e7;border:1.5px solid #f59e0b;border-radius:10px;padding:14px 18px;font-size:13px;color:#92400e;">
          <strong>Belum ada Penetapan Standar.</strong>
          <a href="<?= BASE_URL ?>/penetapan/create" style="color:#1a237e;text-decoration:underline;">Buat penetapan terlebih dahulu →</a>
        </div>
        <?php else: ?>
        <select id="penetapan_id" name="penetapan_id" class="form-control" required onchange="fillPenetapanInfo(this)" style="font-size:13px;padding:10px 14px;">
          <option value="">— Pilih Penetapan Standar —</option>
          <?php foreach ($penetapans as $p): ?>
          <option value="<?= $p['id'] ?>"
                  data-judul="<?= htmlspecialchars($p['judul']) ?>"
                  data-ta="<?= htmlspecialchars($p['ta_nama']) ?>"
                  data-sem="<?= htmlspecialchars($p['semester'] ?? '') ?>"
                  data-ev="<?= htmlspecialchars($p['evaluasi_judul'] ?? 'Belum ada evaluasi') ?>">
            <?= htmlspecialchars($p['judul']) ?> — <?= htmlspecialchars($p['ta_nama']) ?> <?= !empty($p['evaluasi_id']) ? '(Evaluasi Terhubung ✓)' : '' ?>
          </option>
          <?php endforeach; ?>
        </select>
        <?php endif; ?>
        <div id="penetapanInfo" style="display:none;margin-top:10px;padding:12px 16px;background:#fff7ed;border:1px solid #fed7aa;border-radius:9px;font-size:12.5px;color:#9a3412;"></div>
      </div>

      <div class="form-group" style="margin-bottom:18px;">
        <label for="judul" style="font-size:13px;font-weight:800;color:#1e293b;margin-bottom:6px;display:block;">
          Judul Dokumen Pengendalian <span style="color:#ef4444;">*</span>
        </label>
        <input type="text" id="judul" name="judul" class="form-control"
               placeholder="Contoh: Rencana Tindak Lanjut (RTL) Pengendalian T.A. 2025/2026" required
               style="font-size:13px;padding:10px 14px;">
      </div>

      <div class="form-group" style="margin-bottom:24px;">
        <label for="deskripsi" style="font-size:13px;font-weight:700;color:#64748b;margin-bottom:6px;display:block;">
          Deskripsi / Catatan Umum (Opsional)
        </label>
        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                  placeholder="Catatan pengantar pengendalian standar dan arahan tindak lanjut..."
                  style="font-size:13px;"></textarea>
      </div>

      <?php if (!empty($penetapans)): ?>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <a href="<?= BASE_URL ?>/pengendalian" class="btn btn-outline" style="font-size:13px;padding:9px 18px;">Batal</a>
        <button type="submit" class="btn btn-primary" style="background:#ea580c;border:none;font-weight:800;font-size:13px;padding:9px 20px;">
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
    el.innerHTML = '📌 <strong>Penetapan:</strong> ' + opt.getAttribute('data-judul') +
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
