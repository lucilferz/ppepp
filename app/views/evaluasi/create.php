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
    <p class="page-desc">Pilih penetapan sebagai acuan, lalu isi hasil evaluasi per kriteria</p>
  </div>
  <a href="<?= BASE_URL ?>/ppepp<?= !empty($projectId) ? '/' . $projectId : '' ?>" class="btn btn-outline">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    ← Kembali ke Project Library
  </a>
</div>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Form Evaluasi Baru
    </h3>
  </div>
  <div class="card-body">
    <form action="<?= BASE_URL ?>/evaluasi/store" method="POST">
      <input type="hidden" name="project_id" value="<?= (int)($projectId ?? 0) ?>">

      <?php if (!empty($selectedProject)): ?>
      <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 16px;margin-bottom:18px;display:flex;align-items:center;justify-space-between;gap:12px;">
        <div>
          <span style="font-size:11px;color:#1d4ed8;font-weight:800;text-transform:uppercase;">📌 Folder Project PPEPP</span>
          <div style="font-size:14px;font-weight:800;color:#1e3a8a;"><?= htmlspecialchars($selectedProject['judul']) ?></div>
          <div style="font-size:12px;color:#3b82f6;">Acuan Penetapan difilter khusus untuk Project dan Tahun Ajaran ini.</div>
        </div>
        <span style="font-size:12px;color:#1d4ed8;font-weight:700;background:#fff;padding:4px 12px;border-radius:14px;border:1px solid #bfdbfe;white-space:nowrap;">
          📅 T.A. <?= htmlspecialchars($selectedProject['ta_nama']) ?>
        </span>
      </div>
      <?php endif; ?>

      <div class="form-group">
        <label for="penetapan_id">Penetapan Acuan <span style="color:#ef4444;">*</span></label>
        <?php if (empty($penetapans)): ?>
        <div style="background:#fef9e7;border:1.5px solid #f59e0b;border-radius:10px;padding:14px 18px;font-size:13px;color:#92400e;">
          <strong>Belum ada Penetapan.</strong>
          <a href="<?= BASE_URL ?>/penetapan/create" style="color:#1a237e;text-decoration:underline;">Buat penetapan terlebih dahulu →</a>
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
        <div id="penetapanInfo" style="display:none;margin-top:8px;padding:10px 14px;background:#f0f4ff;border-radius:8px;font-size:13px;color:#1a237e;"></div>
      </div>

      <div class="form-group">
        <label for="jenis">Jenis Evaluasi <span style="color:#ef4444;">*</span></label>
        <select id="jenis" name="jenis" class="form-control" required onchange="handleJenisChange(this)">
          <option value="internal">Internal — Evaluasi indikator dari penetapan</option>
          <option value="ami">AMI — Audit Mutu Internal</option>
          <option value="asik">ASIK — Audit Eksternal</option>
          <option value="gabungan">Gabungan (Internal + AMI/ASIK)</option>
          <option value="lainnya">Lainnya (Ketik Sendiri / Kustom)...</option>
        </select>
        
        <!-- Input dinamis jika memilih 'Lainnya' -->
        <div id="jenisLainnyaGroup" style="display:none;margin-top:10px;background:#f8faff;border:1.5px solid #c7d2fe;padding:12px 14px;border-radius:10px;">
          <label for="jenis_custom" style="font-size:12.5px;font-weight:700;color:#3730a3;display:block;margin-bottom:6px;">
            Sebutkan Jenis Evaluasi yang Diinginkan: <span style="color:#ef4444;">*</span>
          </label>
          <input type="text" id="jenis_custom" name="jenis_custom" class="form-control"
                 placeholder="Contoh: Audit ISO 9001, Evaluasi Tengah Semester, Evaluasi Diri Prodi, dll."
                 style="background:#fff;">
        </div>

        <small style="color:var(--text-muted);font-size:12px;margin-top:4px;display:block;">
          Pilih jenis evaluasi yang sesuai atau pilih "Lainnya" untuk menentukan jenis evaluasi khusus.
        </small>
      </div>

      <div class="form-group">
        <label for="judul">Judul Evaluasi <span style="color:#ef4444;">*</span></label>
        <input type="text" id="judul" name="judul" class="form-control"
               placeholder="Contoh: Laporan Evaluasi Internal T.A. 2025/2026" required>
      </div>

      <div class="form-group">
        <label for="deskripsi">Deskripsi <span style="font-weight:400;color:var(--text-muted);">(opsional)</span></label>
        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                  placeholder="Catatan umum tentang evaluasi ini..."></textarea>
      </div>

      <?php if (!empty($penetapans)): ?>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
        <a href="<?= BASE_URL ?>/evaluasi" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
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

    // Auto-fill judul jika masih kosong atau ada kata null
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
