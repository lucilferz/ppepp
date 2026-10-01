<?php
$pageTitle   = 'Buat Peningkatan Standar';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Peningkatan', 'url' => BASE_URL . '/peningkatan'],
  ['label' => 'Buat Peningkatan Baru'],
];
?>

<div class="page-header">
  <div>
    <h2>Buat Dokumen Peningkatan Baru</h2>
    <p class="page-desc">Pilih Penetapan Standar acuan untuk menganalisis standar yang telah terpenuhi dan menaikkan target mutu untuk siklus berikutnya</p>
  </div>
  <a href="<?= BASE_URL ?>/ppepp<?= !empty($projectId) ? '/' . $projectId : '' ?>" class="btn btn-outline">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    ← Kembali ke Project Library
  </a>
</div>

<div class="card" style="border-radius:14px;border:1.5px solid #e2e8f0;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.03);">
  <div class="card-header" style="background:#f8fafc;padding:18px 22px;border-bottom:1.5px solid #e2e8f0;">
    <h3 class="card-title" style="font-size:15px;font-weight:800;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px;">
      <svg fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      Form Peningkatan Standar Baru (Tahap P3 • PPEPP)
    </h3>
  </div>
  <div class="card-body" style="padding:22px;">
    <form action="<?= BASE_URL ?>/peningkatan/store" method="POST">
      <input type="hidden" name="project_id" value="<?= (int)($projectId ?? 0) ?>">

      <?php if (!empty($selectedProject)): ?>
      <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;padding:12px 16px;margin-bottom:18px;display:flex;align-items:center;justify-space-between;gap:12px;">
        <div>
          <span style="font-size:11px;color:#047857;font-weight:800;text-transform:uppercase;">📌 Folder Project PPEPP</span>
          <div style="font-size:14px;font-weight:800;color:#064e3b;"><?= htmlspecialchars($selectedProject['judul']) ?></div>
          <div style="font-size:12px;color:#059669;">Acuan Penetapan difilter khusus untuk Project dan Tahun Ajaran ini.</div>
        </div>
        <span style="font-size:12px;color:#047857;font-weight:700;background:#fff;padding:4px 12px;border-radius:14px;border:1px solid #a7f3d0;white-space:nowrap;">
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
                  data-pg="<?= htmlspecialchars($p['pengendalian_judul'] ?? 'Belum ada pengendalian') ?>">
            <?= htmlspecialchars($p['judul']) ?> — <?= htmlspecialchars($p['ta_nama']) ?> <?= !empty($p['pengendalian_id']) ? '(Pengendalian Terhubung ✓)' : '' ?>
          </option>
          <?php endforeach; ?>
        </select>
        <?php endif; ?>
        <div id="penetapanInfo" style="display:none;margin-top:10px;padding:12px 16px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:9px;font-size:12.5px;color:#065f46;"></div>
      </div>

      <div class="form-group" style="margin-bottom:18px;">
        <label for="judul" style="font-size:13px;font-weight:800;color:#1e293b;margin-bottom:6px;display:block;">
          Judul Dokumen Peningkatan <span style="color:#ef4444;">*</span>
        </label>
        <input type="text" id="judul" name="judul" class="form-control"
               placeholder="Contoh: Peningkatan Standar Mutu &amp; SK Standar Baru T.A. 2026/2027" required
               style="font-size:13px;padding:10px 14px;">
      </div>

      <div class="form-group" style="margin-bottom:24px;">
        <label for="deskripsi" style="font-size:13px;font-weight:700;color:#64748b;margin-bottom:6px;display:block;">
          Deskripsi / Catatan Umum (Opsional)
        </label>
        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                  placeholder="Catatan umum tentang tujuan peningkatan mutu dan dasar kebijakan..."
                  style="font-size:13px;"></textarea>
      </div>

      <?php if (!empty($penetapans)): ?>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <a href="<?= BASE_URL ?>/peningkatan" class="btn btn-outline" style="font-size:13px;padding:9px 18px;">Batal</a>
        <button type="submit" class="btn btn-primary" style="background:#059669;border:none;font-weight:800;font-size:13px;padding:9px 20px;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/>
          </svg>
          Lanjut ke Analisis Peningkatan Standar →
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
      ' &nbsp;·&nbsp; <strong>Pengendalian:</strong> ' + opt.getAttribute('data-pg');
    var judulEl = document.getElementById('judul');
    if (!judulEl.value) {
      judulEl.value = 'Peningkatan Standar Mutu ' + opt.getAttribute('data-ta');
    }
  } else {
    el.style.display = 'none';
  }
}
</script>
