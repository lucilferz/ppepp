<?php
$pageTitle   = 'Buat Evaluasi';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Evaluasi', 'url' => BASE_URL . '/evaluasi'],
  ['label' => 'Buat Evaluasi Baru'],
];
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h2>Buat Evaluasi Baru</h2>
    <p class="page-desc">Pilih penetapan sebagai acuan, lalu tentukan tipe evaluasi mutu (Internal, AMI, ASIK, dsb).</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/ppepp<?= !empty($projectId) ? '/' . $projectId : '' ?>" class="btn btn-outline">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Kembali ke Project Library
    </a>
  </div>
</div>

<div class="card" style="max-width:800px;margin:0 auto;">
  <div class="card-header">
    <h3 class="card-title">
      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Form Evaluasi Standar
    </h3>
  </div>
  <div class="card-body">
    <form action="<?= BASE_URL ?>/evaluasi/store" method="POST">
      <input type="hidden" name="project_id" value="<?= (int)($projectId ?? 0) ?>">

      <?php if (!empty($selectedProject)): ?>
      <div class="card mb-4" style="background:linear-gradient(135deg, #EFF6FF, #DBEAFE);border-color:#BFDBFE;">
        <div class="card-body" style="padding:14px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
          <div>
            <span style="font-size:11px;color:var(--info);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Folder Project PPEPP</span>
            <div style="font-size:14.5px;font-weight:700;color:var(--text-primary);"><?= htmlspecialchars($selectedProject['judul']) ?></div>
            <div style="font-size:12px;color:var(--text-secondary);">Acuan Penetapan difilter khusus untuk Project dan Tahun Ajaran ini.</div>
          </div>
          <span class="badge badge-info" style="font-size:12px;padding:5px 12px;">
            T.A. <?= htmlspecialchars($selectedProject['ta_nama']) ?>
          </span>
        </div>
      </div>
      <?php endif; ?>

      <div class="form-group mb-3">
        <label for="penetapan_id" class="form-label">Penetapan Acuan <span style="color:var(--danger);">*</span></label>
        <?php if (empty($penetapans)): ?>
        <div style="background:#FFFBEB;border:1.5px solid var(--warning);border-radius:8px;padding:14px 18px;font-size:13px;color:#92400E;">
          <strong>Belum ada Penetapan.</strong>
          <a href="<?= BASE_URL ?>/penetapan/create" style="color:var(--primary);text-decoration:underline;font-weight:600;margin-left:4px;">Buat penetapan terlebih dahulu →</a>
        </div>
        <?php else: ?>
        <select id="penetapan_id" name="penetapan_id" class="form-control" required onchange="fillJudul(this)">
          <option value="">— Pilih Penetapan —</option>
          <?php foreach ($penetapans as $p): ?>
          <option value="<?= $p['id'] ?>"
                  data-judul="<?= htmlspecialchars($p['judul']) ?>"
                  data-ta="<?= htmlspecialchars($p['ta_nama']) ?>"
                  data-sem="<?= htmlspecialchars($p['semester'] ?? '') ?>">
            <?= htmlspecialchars($p['judul']) ?> — <?= htmlspecialchars($p['ta_nama']) ?>
            <?= $p['status'] === 'final' ? '✓' : '(Draft)' ?>
          </option>
          <?php endforeach; ?>
        </select>
        <?php endif; ?>
        <div id="penetapanInfo" style="display:none;margin-top:8px;padding:10px 14px;background:#F0F9FF;border:1px solid #BAE6FD;border-radius:8px;font-size:13px;color:var(--info);"></div>
      </div>

      <div class="form-group mb-3">
        <label for="jenis" class="form-label">Jenis Evaluasi <span style="color:var(--danger);">*</span></label>
        <select id="jenis" name="jenis" class="form-control" required onchange="handleJenisChange(this)">
          <option value="internal">Internal — Evaluasi indikator dari penetapan</option>
          <option value="ami">AMI — Audit Mutu Internal</option>
          <option value="asik">ASIK — Audit Eksternal</option>
          <option value="gabungan">Gabungan (Internal + AMI/ASIK)</option>
          <option value="lainnya">Lainnya (Ketik Sendiri / Kustom)...</option>
        </select>
        
        <!-- Input dinamis jika memilih 'Lainnya' -->
        <div id="jenisLainnyaGroup" style="display:none;margin-top:10px;background:#F8FAFC;border:1.5px solid var(--border);padding:12px 14px;border-radius:8px;">
          <label for="jenis_custom" style="font-size:12.5px;font-weight:700;color:var(--text-primary);display:block;margin-bottom:6px;">
            Sebutkan Jenis Evaluasi yang Diinginkan: <span style="color:var(--danger);">*</span>
          </label>
          <input type="text" id="jenis_custom" name="jenis_custom" class="form-control"
                 placeholder="Contoh: Audit ISO 9001, Evaluasi Tengah Semester, Evaluasi Diri Prodi, dll."
                 style="background:#fff;">
        </div>

        <small style="color:var(--text-secondary);font-size:12px;margin-top:4px;display:block;">
          Pilih jenis evaluasi yang sesuai atau pilih "Lainnya" untuk menentukan jenis evaluasi khusus.
        </small>
      </div>

      <div class="form-group mb-3">
        <label for="judul" class="form-label">Judul Evaluasi <span style="color:var(--danger);">*</span></label>
        <input type="text" id="judul" name="judul" class="form-control"
               placeholder="Contoh: Laporan Evaluasi Internal T.A. 2025/2026" required>
      </div>

      <div class="form-group mb-4">
        <label for="deskripsi" class="form-label">Deskripsi <span style="font-weight:400;color:var(--text-secondary);">(opsional)</span></label>
        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                  placeholder="Catatan umum tentang evaluasi ini..."></textarea>
      </div>

      <?php if (!empty($penetapans)): ?>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <a href="<?= BASE_URL ?>/evaluasi" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
          </svg>
          Buat Evaluasi
        </button>
      </div>
      <?php endif; ?>

    </form>
  </div>
</div>

<script>
function handleJenisChange(sel) {
  var customGroup = document.getElementById('jenisLainnyaGroup');
  var customInput = document.getElementById('jenis_custom');
  if (sel.value === 'lainnya') {
    if (customGroup) customGroup.style.display = 'block';
    if (customInput) {
      customInput.required = true;
      customInput.focus();
    }
  } else {
    if (customGroup) customGroup.style.display = 'none';
    if (customInput) {
      customInput.required = false;
      customInput.value = '';
    }
  }
}

function fillJudul(sel) {
  var opt = sel.options[sel.selectedIndex];
  var infoEl = document.getElementById('penetapanInfo');
  if (opt && opt.value) {
    var judulPen = opt.getAttribute('data-judul') || '';
    var taNama   = opt.getAttribute('data-ta') || '';
    var sem      = (opt.getAttribute('data-sem') || '').replace(/^null$/i, '').trim();

    infoEl.style.display = 'block';
    infoEl.innerHTML = '<strong>Penetapan:</strong> ' + escHtml(judulPen) +
      ' &nbsp;·&nbsp; <strong>TA:</strong> ' + escHtml(taNama) + (sem ? ' (' + escHtml(sem) + ')' : '');

    var judulEl = document.getElementById('judul');
    if (!judulEl.value || judulEl.value.toLowerCase().includes('null')) {
      judulEl.value = 'Evaluasi ' + taNama + (sem ? ' ' + sem : '');
    }
  } else {
    infoEl.style.display = 'none';
  }
}

function escHtml(str) {
  if (!str) return '';
  var d = document.createElement('div');
  d.appendChild(document.createTextNode(str));
  return d.innerHTML;
}
</script>
