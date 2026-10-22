<?php
$pageTitle   = 'Project PPEPP';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Project PPEPP'],
];
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h2>Project PPEPP per Tahun Ajaran</h2>
    <p class="page-desc">Setiap Tahun Ajaran merupakan satu siklus PPEPP lengkap (Penetapan &rarr; Pelaksanaan &rarr; Evaluasi &rarr; Pengendalian &rarr; Peningkatan)</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/pending" class="btn btn-outline btn-sm">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      Tugas Belum Selesai
    </a>
    <button type="button" class="btn btn-primary btn-sm" data-modal-open="modalBuatProject">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
      </svg>
      + Buat Project PPEPP Baru
    </button>
  </div>
</div>

<?php if (!empty($flash['message'])): ?>
<div class="alert alert-<?= $flash['type']==='success' ? 'success' : 'error' ?>">
  <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
    <?php if ($flash['type']==='success'): ?>
      <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
    <?php else: ?>
      <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    <?php endif; ?>
  </svg>
  <div><?= htmlspecialchars($flash['message']) ?></div>
</div>
<?php endif; ?>

<?php if (empty($projects)): ?>
<!-- Empty State -->
<div class="card" style="padding:60px 24px;text-align:center;">
  <div class="empty-icon">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
    </svg>
  </div>
  <h3 style="font-size:16px;font-weight:700;color:var(--text-main);margin-bottom:4px;">Belum Ada Project PPEPP</h3>
  <p style="font-size:13px;color:var(--text-muted);max-width:440px;margin:0 auto 20px;">
    Inisiasi project PPEPP pertama untuk memulai siklus penjaminan mutu. Setiap project mewakili satu Tahun Ajaran.
  </p>
  <button type="button" class="btn btn-primary" data-modal-open="modalBuatProject">
    + Buat Project PPEPP Pertama
  </button>
</div>
<?php else: ?>

<!-- Filter Tabs (Linear / shadcn style) -->
<div class="tabs-segmented">
  <button type="button" class="btn-filter active" onclick="filterProjects('all', this)">
    Semua (<?= count($projects) ?>)
  </button>
  <?php
    $countAktif    = count(array_filter($projects, fn($p) => ($p['status'] ?? 'aktif') === 'aktif'));
    $countNonaktif = count(array_filter($projects, fn($p) => ($p['status'] ?? '') === 'nonaktif'));
    $countArsip    = count(array_filter($projects, fn($p) => ($p['status'] ?? '') === 'arsip'));
    $countSelesai  = count(array_filter($projects, fn($p) => ($p['status'] ?? '') === 'selesai'));
  ?>
  <button type="button" class="btn-filter" onclick="filterProjects('aktif', this)">
    Aktif (<?= $countAktif ?>)
  </button>
  <?php if ($countSelesai > 0): ?>
  <button type="button" class="btn-filter" onclick="filterProjects('selesai', this)">
    Selesai (<?= $countSelesai ?>)
  </button>
  <?php endif; ?>
  <?php if ($countNonaktif > 0): ?>
  <button type="button" class="btn-filter" onclick="filterProjects('nonaktif', this)">
    Tersembunyi (<?= $countNonaktif ?>)
  </button>
  <?php endif; ?>
  <?php if ($countArsip > 0): ?>
  <button type="button" class="btn-filter" onclick="filterProjects('arsip', this)">
    Arsip (<?= $countArsip ?>)
  </button>
  <?php endif; ?>
</div>

<!-- Grid Project Cards -->
<div id="projectGridContainer" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(330px, 1fr));gap:18px;">
  <?php foreach ($projects as $proj): ?>
  <?php
    $pct = $proj['progress_pct'];
    $started = $proj['steps_started'];
    $st = $proj['status'] ?? 'aktif';

    $statusBadge = match($st) {
      'aktif'    => ['label' => 'Aktif', 'class' => 'badge-success'],
      'nonaktif' => ['label' => 'Tersembunyi', 'class' => 'badge-warning'],
      'arsip'    => ['label' => 'Arsip', 'class' => 'badge-neutral'],
      'selesai'  => ['label' => 'Selesai', 'class' => 'badge-info'],
      default    => ['label' => ucfirst($st), 'class' => 'badge-neutral'],
    };
  ?>
  <div class="card project-card" data-project-status="<?= htmlspecialchars($st) ?>" data-status="<?= htmlspecialchars($st) ?>"
       style="display:flex;flex-direction:column;justify-content:space-between;">
    <div>
      <!-- Card Header -->
      <div class="card-header" style="background:var(--surface);">
        <div>
          <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);">
            Tahun Ajaran
          </div>
          <h3 style="font-size:16px;font-weight:700;color:var(--text-main);margin:2px 0 0;">
            <?= htmlspecialchars($proj['ta_nama']) ?>
          </h3>
          <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
            <?= htmlspecialchars($proj['judul']) ?>
          </div>
        </div>
        <span class="badge <?= $statusBadge['class'] ?> badge-dot">
          <?= $statusBadge['label'] ?>
        </span>
      </div>

      <!-- Card Body: Progress Bar & 5 Step Mini Grid -->
      <div class="card-body" style="padding:16px 20px;">
        <!-- Cycle Progress -->
        <div style="margin-bottom:14px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;font-size:12px;">
            <span style="color:var(--text-muted);font-weight:500;">Progres Siklus Mutu</span>
            <span style="color:var(--primary);font-weight:700;"><?= $started ?>/5 Tahap (<?= $pct ?>%)</span>
          </div>
          <div style="height:6px;background:var(--border-subtle);border-radius:var(--radius-pill);overflow:hidden;">
            <div style="height:100%;width:<?= $pct ?>%;background:var(--primary);border-radius:var(--radius-pill);transition:width 0.3s ease;"></div>
          </div>
        </div>

        <!-- 5-Step PPEPP Status Mini Grid -->
        <div style="display:grid;grid-template-columns:repeat(5, 1fr);gap:4px;margin-top:10px;">
          <?php
          $steps = [
            ['P','Penetapan','penetapan'],
            ['P','Pelaksanaan','pelaksanaan'],
            ['E','Evaluasi','evaluasi'],
            ['P','Pengendalian','pengendalian'],
            ['P','Peningkatan','peningkatan'],
          ];
          foreach ($steps as [$ltr, $nama, $key]):
            $s = $proj['stats'][$key] ?? ['total'=>0,'final'=>0];
            $t = (int)$s['total'];
            $f = (int)$s['final'];
            if ($f > 0) { $bg = 'var(--success-bg)'; $color = 'var(--success-text)'; $bdr = 'var(--success-border)'; }
            elseif ($t > 0) { $bg = 'var(--warning-bg)'; $color = 'var(--warning-text)'; $bdr = 'var(--warning-border)'; }
            else { $bg = 'var(--surface-hover)'; $color = 'var(--text-muted)'; $bdr = 'var(--border)'; }
          ?>
          <div style="text-align:center;padding:6px 2px;background:<?= $bg ?>;border:1px solid <?= $bdr ?>;border-radius:var(--radius-sm);" title="<?= $nama ?>: <?= $t ?> dokumen (<?= $f ?> final)">
            <div style="font-size:11px;font-weight:700;color:<?= $color ?>;"><?= $ltr ?></div>
            <div style="font-size:9.5px;font-weight:600;color:<?= $color ?>;margin-top:1px;"><?= $t > 0 ? $t : '&mdash;' ?></div>
          </div>
          <?php endforeach; ?>
        </div>

        <div style="display:grid;grid-template-columns:repeat(5, 1fr);gap:4px;margin-top:3px;">
          <?php foreach ($steps as [$ltr, $nama, $key]): ?>
          <div style="text-align:center;font-size:9px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            <?= $nama ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Card Footer / Actions -->
    <div class="card-footer" style="padding:10px 16px;">
      <a href="<?= BASE_URL ?>/ppepp/<?= $proj['id'] ?>" class="btn btn-primary btn-sm" style="flex:1;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
        </svg>
        Buka Project
      </a>

      <!-- Quick Toggle Active / Nonactive Button -->
      <?php if ($st === 'aktif'): ?>
      <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $proj['id'] ?>/toggle-status" style="margin:0;">
        <input type="hidden" name="status" value="nonaktif">
        <input type="hidden" name="return_url" value="<?= BASE_URL ?>/ppepp">
        <button type="submit" class="btn btn-outline btn-sm" title="Sembunyikan / Nonaktifkan Project">
          Sembunyikan
        </button>
      </form>
      <?php else: ?>
      <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $proj['id'] ?>/toggle-status" style="margin:0;">
        <input type="hidden" name="status" value="aktif">
        <input type="hidden" name="return_url" value="<?= BASE_URL ?>/ppepp">
        <button type="submit" class="btn btn-outline btn-sm" title="Aktifkan Project Kembali">
          Aktifkan
        </button>
      </form>
      <?php endif; ?>

      <a href="<?= BASE_URL ?>/ppepp/<?= $proj['id'] ?>/edit" class="btn btn-outline btn-sm" title="Edit Informasi Project">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
          <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
      </a>

      <!-- Hapus Project Permanen Button -->
      <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $proj['id'] ?>/destroy" style="margin:0;"
            onsubmit="return confirm('KONFIRMASI HAPUS PERMANEN:\n\nApakah Anda yakin ingin menghapus project \'<?= htmlspecialchars(addslashes($proj['judul'])) ?>\' (TA <?= htmlspecialchars(addslashes($proj['ta_nama'])) ?>)?\n\nSeluruh data 5 tahap PPEPP di dalamnya akan terhapus.');">
        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Project">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
            <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
          </svg>
        </button>
      </form>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<script>
function selectTahunAjaran(el) {
  document.querySelectorAll('.ta-option-card').forEach(function(c) {
    c.style.borderColor = 'var(--border)';
    c.style.background = 'var(--surface)';
  });
  el.style.borderColor = 'var(--primary)';
  el.style.background = 'var(--primary-subtle)';

  var input = el.querySelector('input[type="radio"]');
  if (input) input.checked = true;

  var newTaGrp = document.getElementById('newTaGroup');
  var newTaFlg = document.getElementById('newTaFlag');
  if (newTaGrp) newTaGrp.style.display = 'none';
  if (newTaFlg) newTaFlg.value = '0';
}

function showNewTaInput(el) {
  document.querySelectorAll('.ta-option-card').forEach(function(c) {
    c.style.borderColor = 'var(--border)';
    c.style.background = 'var(--surface)';
  });
  el.style.borderColor = 'var(--primary)';
  el.style.background = 'var(--primary-subtle)';

  var input = el.querySelector('input[type="radio"]');
  if (input) input.checked = true;

  var newTaGrp = document.getElementById('newTaGroup');
  var newTaFlg = document.getElementById('newTaFlag');
  if (newTaGrp) newTaGrp.style.display = 'block';
  if (newTaFlg) newTaFlg.value = '1';
  var namaInput = document.getElementById('newTaNama');
  if (namaInput) namaInput.focus();
}
</script>
<?php endif; ?>

<!-- Modal Buat Project Baru -->
<div class="modal-overlay" id="modalBuatProject">
  <div class="modal" style="max-width:540px;">
    <div class="modal-header">
      <h3 class="modal-title">Buat Project PPEPP Baru</h3>
      <button type="button" class="modal-close-btn" data-modal-close="modalBuatProject">&times;</button>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/ppepp/store">
      <div class="modal-body">
        <div class="card mb-3" style="background:var(--surface-hover);padding:10px 14px;border:1px solid var(--border);font-size:12.5px;color:var(--text-muted);">
          Pilih <strong>Tahun Ajaran</strong> untuk project baru. Seluruh data 5 tahap siklus PPEPP akan terikat otomatis ke Tahun Ajaran ini.
        </div>

        <!-- Pilihan Tahun Ajaran -->
        <div class="form-group">
          <label class="form-label">Pilih Tahun Ajaran <span class="required-star">*</span></label>
          <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(140px, 1fr));gap:8px;max-height:160px;overflow-y:auto;padding:2px;">
            <?php foreach ($allTa as $ta): ?>
            <?php $alreadyUsed = in_array((int)$ta['id'], $usedTaIds ?? [], true); ?>
            <label class="ta-option-card"
                   style="border:1px solid <?= $alreadyUsed ? 'var(--border-subtle)' : 'var(--border)' ?>;border-radius:var(--radius-sm);padding:8px 10px;cursor:<?= $alreadyUsed ? 'not-allowed' : 'pointer' ?>;transition:all 0.15s;background:<?= $alreadyUsed ? 'var(--surface-hover)' : 'var(--surface)' ?>;<?= $alreadyUsed ? 'opacity:0.5;' : '' ?>"
                   <?= !$alreadyUsed ? 'onclick="selectTahunAjaran(this)"' : '' ?>>
              <div style="display:flex;align-items:center;gap:6px;">
                <input type="radio" name="tahun_ajaran_id" value="<?= $ta['id'] ?>" <?= $alreadyUsed ? 'disabled' : '' ?> style="accent-color:var(--primary);">
                <div>
                  <div style="font-size:12.5px;font-weight:600;color:var(--text-main);"><?= htmlspecialchars($ta['nama']) ?></div>
                  <?php if ($alreadyUsed): ?>
                  <div style="font-size:9.5px;color:var(--danger);font-weight:600;">Sudah Dibuat</div>
                  <?php endif; ?>
                </div>
              </div>
            </label>
            <?php endforeach; ?>

            <!-- Opsi Tambah TA Baru -->
            <label class="ta-option-card"
                   style="border:1px dashed var(--primary);border-radius:var(--radius-sm);padding:8px 10px;cursor:pointer;background:var(--primary-subtle);"
                   onclick="showNewTaInput(this)">
              <div style="display:flex;align-items:center;gap:6px;">
                <input type="radio" name="tahun_ajaran_id" value="0" id="radioNewTa" style="accent-color:var(--primary);">
                <div style="font-size:12px;font-weight:600;color:var(--primary);">+ TA Baru...</div>
              </div>
            </label>
          </div>

          <input type="hidden" name="new_ta" value="0" id="newTaFlag">
          <div id="newTaGroup" style="display:none;margin-top:10px;background:var(--primary-subtle);border:1px solid #C9DBF0;border-radius:var(--radius-sm);padding:10px 12px;">
            <label class="form-label" style="font-size:11.5px;color:var(--primary);">Nama Tahun Ajaran Baru:</label>
            <input type="text" name="new_ta_nama" id="newTaNama" class="form-control" placeholder="Contoh: 2027/2028"
                   oninput="document.getElementById('newTaFlag').value='1'">
          </div>
        </div>

        <!-- Judul Project -->
        <div class="form-group">
          <label for="judul" class="form-label">
            Judul Project <span style="font-size:11px;font-weight:400;color:var(--text-muted);">(Opsional &bull; Otomatis jika kosong)</span>
          </label>
          <input type="text" id="judul" name="judul" class="form-control"
                 placeholder="Contoh: PPEPP 2025/2026 Program Studi Sistem Informasi">
        </div>

        <!-- Deskripsi -->
        <div class="form-group">
          <label for="deskripsi" class="form-label">
            Deskripsi / Catatan Tambahan <span style="font-size:11px;font-weight:400;color:var(--text-muted);">(Opsional)</span>
          </label>
          <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2"
                    placeholder="Catatan ruang lingkup siklus penjaminan mutu..."></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close="modalBuatProject">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan &amp; Buat Project</button>
      </div>
    </form>
  </div>
</div>
