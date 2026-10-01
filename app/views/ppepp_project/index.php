<?php
$pageTitle   = 'Project PPEPP';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Project PPEPP'],
];
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom:24px;">
  <div>
    <h2 style="font-size:22px;font-weight:800;color:var(--text-main);">Project PPEPP per Tahun Ajaran</h2>
    <p class="page-desc">Setiap Tahun Ajaran adalah satu siklus PPEPP lengkap (Penetapan → Pelaksanaan → Evaluasi → Pengendalian → Peningkatan)</p>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap;">
    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline" style="font-weight:700;font-size:13px;border-color:#fca5a5;color:#dc2626;background:#fef2f2;display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
      📌 Filter Dokumen Belum Dikerjakan
    </a>
    <button type="button" class="btn btn-primary" data-modal-open="modalBuatProject">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      + Buat Project PPEPP Baru
    </button>
  </div>
</div>

<?php if (!empty($flash['message'])): ?>
<div class="alert" style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;<?= $flash['type']==='success' ? 'background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;' : 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;' ?>">
  <?= $flash['type']==='success' ? '✓' : '✕' ?> <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<?php if (empty($projects)): ?>
<!-- Empty State -->
<div style="text-align:center;padding:64px 24px;">
  <div style="width:72px;height:72px;background:linear-gradient(135deg,#e0e7ff,#c7d2fe);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
    <svg viewBox="0 0 24 24" fill="none" stroke="#3f51b5" stroke-width="1.5" width="36" height="36"><path d="M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/><path d="M3 7l9 6 9-6"/></svg>
  </div>
  <h3 style="font-size:18px;font-weight:700;color:var(--text-main);margin-bottom:8px;">Belum Ada Project PPEPP</h3>
  <p style="font-size:14px;color:var(--text-muted);max-width:400px;margin:0 auto 24px;">Buat project PPEPP pertama untuk memulai siklus penjaminan mutu. Setiap project mewakili satu Tahun Ajaran.</p>
  <button type="button" class="btn btn-primary" data-modal-open="modalBuatProject">
    + Buat Project PPEPP Pertama
  </button>
<?php else: ?>
<!-- Filter Tabs -->
<div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
  <button type="button" class="btn btn-sm btn-filter active" onclick="filterProjects('all', this)"
          style="padding:7px 16px;border-radius:20px;font-weight:700;font-size:13px;background:#1e293b;color:#fff;border:none;cursor:pointer;">
    Semua (<?= count($projects) ?>)
  </button>
  <?php
    $countAktif    = count(array_filter($projects, fn($p) => ($p['status'] ?? 'aktif') === 'aktif'));
    $countNonaktif = count(array_filter($projects, fn($p) => ($p['status'] ?? '') === 'nonaktif'));
    $countArsip    = count(array_filter($projects, fn($p) => ($p['status'] ?? '') === 'arsip'));
    $countSelesai  = count(array_filter($projects, fn($p) => ($p['status'] ?? '') === 'selesai'));
  ?>
  <button type="button" class="btn btn-sm btn-filter" onclick="filterProjects('aktif', this)"
          style="padding:7px 16px;border-radius:20px;font-weight:700;font-size:13px;background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;cursor:pointer;">
    Aktif (<?= $countAktif ?>)
  </button>
  <?php if ($countNonaktif > 0): ?>
  <button type="button" class="btn btn-sm btn-filter" onclick="filterProjects('nonaktif', this)"
          style="padding:7px 16px;border-radius:20px;font-weight:700;font-size:13px;background:#f1f5f9;color:#854d0e;border:1px solid #fde047;cursor:pointer;">
    Nonaktif / Tersembunyi (<?= $countNonaktif ?>)
  </button>
  <?php endif; ?>
  <?php if ($countSelesai > 0): ?>
  <button type="button" class="btn btn-sm btn-filter" onclick="filterProjects('selesai', this)"
          style="padding:7px 16px;border-radius:20px;font-weight:700;font-size:13px;background:#f1f5f9;color:#1e40af;border:1px solid #bfdbfe;cursor:pointer;">
    Selesai (<?= $countSelesai ?>)
  </button>
  <?php endif; ?>
  <?php if ($countArsip > 0): ?>
  <button type="button" class="btn btn-sm btn-filter" onclick="filterProjects('arsip', this)"
          style="padding:7px 16px;border-radius:20px;font-weight:700;font-size:13px;background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;cursor:pointer;">
    Arsip (<?= $countArsip ?>)
  </button>
  <?php endif; ?>
</div>

<!-- Grid Project Cards -->
<div id="projectGridContainer" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(330px,1fr));gap:20px;">
  <?php foreach ($projects as $proj): ?>
  <?php
    $pct = $proj['progress_pct'];
    $started = $proj['steps_started'];
    $st = $proj['status'] ?? 'aktif';

    $statusMeta = match($st) {
      'aktif'    => ['label' => 'Aktif', 'badgeBg' => '#22c55e', 'headerGrad' => 'linear-gradient(135deg,#0f172a,#1a237e)', 'cardBorder' => 'var(--border)'],
      'nonaktif' => ['label' => 'Nonaktif / Tersembunyi', 'badgeBg' => '#eab308', 'headerGrad' => 'linear-gradient(135deg,#451a03,#78350f)', 'cardBorder' => '#fef08a'],
      'arsip'    => ['label' => 'Arsip', 'badgeBg' => '#64748b', 'headerGrad' => 'linear-gradient(135deg,#334155,#475569)', 'cardBorder' => '#cbd5e1'],
      'selesai'  => ['label' => 'Selesai', 'badgeBg' => '#3b82f6', 'headerGrad' => 'linear-gradient(135deg,#1e3a8a,#2563eb)', 'cardBorder' => '#bfdbfe'],
      default    => ['label' => ucfirst($st), 'badgeBg' => '#64748b', 'headerGrad' => 'linear-gradient(135deg,#0f172a,#1a237e)', 'cardBorder' => 'var(--border)'],
    };
  ?>
  <div class="project-card" data-status="<?= htmlspecialchars($st) ?>"
       style="background:#fff;border:1.5px solid <?= $statusMeta['cardBorder'] ?>;border-radius:16px;overflow:hidden;transition:all 0.2s;box-shadow:0 2px 8px rgba(0,0,0,0.05);<?= $st === 'nonaktif' ? 'opacity:0.92;' : '' ?>"
       onmouseover="this.style.borderColor='#3f51b5';this.style.boxShadow='0 6px 24px rgba(26,35,126,0.12)';"
       onmouseout="this.style.borderColor='<?= $statusMeta['cardBorder'] ?>';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)';">
    <!-- Card Header -->
    <div style="padding:20px 22px;background:<?= $statusMeta['headerGrad'] ?>;color:#fff;position:relative;">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
        <div>
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,0.6);margin-bottom:4px;">
            Tahun Ajaran
          </div>
          <div style="font-size:22px;font-weight:800;color:#fff;letter-spacing:-0.5px;">
            <?= htmlspecialchars($proj['ta_nama']) ?>
          </div>
          <div style="font-size:12px;color:rgba(255,255,255,0.7);margin-top:3px;">
            <?= htmlspecialchars($proj['judul']) ?>
          </div>
        </div>
        <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;background:<?= $statusMeta['badgeBg'] ?>;color:#fff;flex-shrink:0;box-shadow:0 1px 4px rgba(0,0,0,0.2);">
          <?= $statusMeta['label'] ?>
        </span>
      </div>

      <!-- Progress Bar -->
      <div style="margin-top:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
          <span style="font-size:11px;color:rgba(255,255,255,0.7);">Progres Siklus</span>
          <span style="font-size:11px;font-weight:700;color:#fbbf24;"><?= $started ?>/5 tahap</span>
        </div>
        <div style="height:6px;background:rgba(255,255,255,0.15);border-radius:99px;overflow:hidden;">
          <div style="height:100%;width:<?= $pct ?>%;background:linear-gradient(90deg,#fbbf24,#f59e0b);border-radius:99px;transition:width 0.5s;"></div>
        </div>
      </div>
    </div>

    <!-- 5 Step PPEPP Status -->
    <div style="padding:14px 20px;display:flex;gap:6px;">
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
        if ($f > 0) { $stepBg = '#ecfdf5'; $stepColor = '#059669'; $stepBorder = '#a7f3d0'; }
        elseif ($t > 0) { $stepBg = '#fffbeb'; $stepColor = '#d97706'; $stepBorder = '#fde68a'; }
        else { $stepBg = '#f8fafc'; $stepColor = '#94a3b8'; $stepBorder = '#e2e8f0'; }
      ?>
      <div style="flex:1;text-align:center;padding:8px 4px;background:<?= $stepBg ?>;border:1px solid <?= $stepBorder ?>;border-radius:8px;">
        <div style="font-size:12px;font-weight:800;color:<?= $stepColor ?>;"><?= $ltr ?></div>
        <div style="font-size:9px;color:<?= $stepColor ?>;font-weight:600;margin-top:1px;"><?= $t > 0 ? $t : '—' ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Keterangan Step -->
    <div style="padding:0 20px 4px;display:flex;gap:6px;">
      <?php foreach ($steps as [$ltr, $nama, $key]): ?>
      <div style="flex:1;text-align:center;">
        <div style="font-size:9px;color:#94a3b8;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= $nama ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Action Buttons -->
    <div style="padding:14px 20px 18px;display:flex;gap:8px;border-top:1px solid var(--border);margin-top:10px;">
      <a href="<?= BASE_URL ?>/ppepp/<?= $proj['id'] ?>"
         style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:9px 14px;background:linear-gradient(135deg,#1a237e,#3f51b5);color:#fff;border-radius:9px;font-size:13px;font-weight:700;text-decoration:none;transition:all 0.18s;"
         onmouseover="this.style.filter='brightness(1.1)'" onmouseout="this.style.filter='none'">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        Buka Project
      </a>
      
      <!-- Quick Toggle Active / Nonactive Button -->
      <?php if ($st === 'aktif'): ?>
      <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $proj['id'] ?>/toggle-status" style="margin:0;">
        <input type="hidden" name="status" value="nonaktif">
        <input type="hidden" name="return_url" value="<?= BASE_URL ?>/ppepp">
        <button type="submit" class="btn btn-outline"
                style="padding:9px 12px;background:#fefce8;color:#a16207;border-color:#fef08a;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:4px;"
                title="Sembunyikan / Nonaktifkan Project">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/></svg>
          Hide
        </button>
      </form>
      <?php else: ?>
      <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $proj['id'] ?>/toggle-status" style="margin:0;">
        <input type="hidden" name="status" value="aktif">
        <input type="hidden" name="return_url" value="<?= BASE_URL ?>/ppepp">
        <button type="submit" class="btn btn-outline"
                style="padding:9px 12px;background:#ecfdf5;color:#059669;border-color:#a7f3d0;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:4px;"
                title="Aktifkan Project Kembali">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polygon points="5 3 19 12 5 21 5 3"/></svg>
          Aktifkan
        </button>
      </form>
      <?php endif; ?>
      <a href="<?= BASE_URL ?>/ppepp/<?= $proj['id'] ?>/edit"
         style="padding:9px 12px;background:#f1f5f9;color:#475569;border-radius:9px;font-size:13px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:4px;"
         title="Edit Informasi Project">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      </a>

      <!-- Hapus Project Permanen Button -->
      <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $proj['id'] ?>/destroy" style="margin:0;"
            onsubmit="return confirm('⚠️ KONFIRMASI HAPUS PERMANEN:\n\nApakah Anda YAKIN ingin menghapus project \'<?= htmlspecialchars(addslashes($proj['judul'])) ?>\' (TA <?= htmlspecialchars(addslashes($proj['ta_nama'])) ?>)?\n\nSELURUH DATA 5 TAHAP PPEPP (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, & Peningkatan) di dalamnya akan TERHAPUS TOTAL & PERMANEN!');">
        <button type="submit" class="btn"
                style="padding:9px 12px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:4px;"
                title="Hapus Project Permanen Beserta Seluruh Isinya">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
        </button>
      </form>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<script>
function filterProjects(status, btn) {
  document.querySelectorAll('.btn-filter').forEach(function(b) {
    b.style.background = '#f1f5f9';
    b.style.color = '#334155';
    b.style.border = '1px solid #cbd5e1';
  });
  btn.style.background = '#1e293b';
  btn.style.color = '#fff';
  btn.style.border = 'none';

  var cards = document.querySelectorAll('.project-card');
  cards.forEach(function(c) {
    if (status === 'all' || c.dataset.status === status) {
      c.style.display = 'block';
    } else {
      c.style.display = 'none';
    }
  });
}

function selectTahunAjaran(el) {
  document.querySelectorAll('.ta-option-card').forEach(function(c) {
    c.style.borderColor = '#e2e8f0';
    c.style.background = '#fff';
  });
  el.style.borderColor = '#2563eb';
  el.style.background = '#eff6ff';

  var input = el.querySelector('input[type="radio"]');
  if (input) input.checked = true;

  document.getElementById('newTaGroup').style.display = 'none';
  document.getElementById('newTaFlag').value = '0';
}

function showNewTaInput(el) {
  document.querySelectorAll('.ta-option-card').forEach(function(c) {
    c.style.borderColor = '#e2e8f0';
    c.style.background = '#fff';
  });
  el.style.borderColor = '#2563eb';
  el.style.background = '#eff6ff';

  var input = el.querySelector('input[type="radio"]');
  if (input) input.checked = true;

  document.getElementById('newTaGroup').style.display = 'block';
  document.getElementById('newTaFlag').value = '1';
  document.getElementById('newTaNama').focus();
}

function confirmDeleteProject(id, title, taName) {
  var form = document.getElementById('formDeleteProjectConfirm');
  var titleEl = document.getElementById('delProjectTitle');
  if (form) {
    form.action = BASE_URL + '/ppepp/' + id + '/destroy';
  }
  if (titleEl) {
    titleEl.textContent = title + ' (TA ' + taName + ')';
  }
  if (window.openModal) {
    window.openModal('modalDeleteProjectConfirm');
  }
}
</script>
<?php endif; ?>

<!-- Modal Buat Project Baru -->
<div class="modal-overlay" id="modalBuatProject">
  <div class="modal-card" style="max-width:560px;width:100%;max-height:90vh;border-radius:18px;overflow:hidden;box-shadow:0 24px 48px rgba(0,0,0,0.3);padding:0;border:none;display:flex;flex-direction:column;background:#fff;">
    <!-- Header Banner (Fixed at top) -->
    <div style="background:linear-gradient(135deg, #1e293b 0%, #1e1b4b 100%);color:#fff;padding:18px 24px;display:flex;align-items:center;justify-content:space-between;gap:14px;border-bottom:1px solid rgba(255,255,255,0.1);flex-shrink:0;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:38px;height:38px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
          📁
        </div>
        <div>
          <h3 style="font-size:16px;font-weight:800;margin:0;color:#fff;letter-spacing:-0.3px;">Buat Project PPEPP Baru</h3>
          <p style="font-size:11.5px;margin:2px 0 0;color:rgba(255,255,255,0.75);">Siklus penjaminan mutu 5 tahap per Tahun Ajaran</p>
        </div>
      </div>
      <button type="button" class="modal-close" data-modal-close="modalBuatProject" style="color:#fff;opacity:0.8;font-size:22px;line-height:1;background:none;border:none;cursor:pointer;padding:4px 8px;">&times;</button>
    </div>

    <!-- Form container filling remaining height -->
    <form method="POST" action="<?= BASE_URL ?>/ppepp/store" style="display:flex;flex-direction:column;overflow:hidden;flex:1;margin:0;">
      <div class="modal-body" style="padding:20px 24px;background:#fff;overflow-y:auto;flex:1;">
        <!-- Banner Info Singkat -->
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:12px;color:#475569;margin-bottom:16px;line-height:1.4;">
          💡 <strong>Petunjuk:</strong> Pilih <strong>Tahun Ajaran</strong> untuk project baru. Seluruh data 5 tahap PPEPP akan terikat otomatis ke Tahun Ajaran ini.
        </div>

        <!-- Pilihan Tahun Ajaran (Scrollable list if many) -->
        <div class="form-group" style="margin-bottom:16px;">
          <label style="font-size:12.5px;font-weight:800;color:#1e293b;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
            <span>📅 Pilih Tahun Ajaran</span>
            <span style="color:#dc2626;">*</span>
          </label>

          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(145px,1fr));gap:8px;max-height:180px;overflow-y:auto;padding:2px 4px 4px 2px;">
            <?php foreach ($allTa as $ta): ?>
            <?php
              $alreadyUsed = in_array((int)$ta['id'], $usedTaIds ?? [], true);
            ?>
            <label class="ta-option-card"
                   style="border:1.5px solid <?= $alreadyUsed ? '#f1f5f9' : '#e2e8f0' ?>;border-radius:10px;padding:9px 12px;cursor:<?= $alreadyUsed ? 'not-allowed' : 'pointer' ?>;transition:all 0.15s;background:<?= $alreadyUsed ? '#f8fafc' : '#ffffff' ?>;<?= $alreadyUsed ? 'opacity:0.55;' : '' ?>"
                   <?= !$alreadyUsed ? 'onclick="selectTahunAjaran(this)"' : '' ?>>
              <div style="display:flex;align-items:center;gap:8px;">
                <input type="radio" name="tahun_ajaran_id" value="<?= $ta['id'] ?>" <?= $alreadyUsed ? 'disabled' : '' ?> style="accent-color:#2563eb;width:15px;height:15px;">
                <div>
                  <div style="font-size:13px;font-weight:800;color:<?= $alreadyUsed ? '#64748b' : '#0f172a' ?>;"><?= htmlspecialchars($ta['nama']) ?></div>
                  <?php if ($alreadyUsed): ?>
                  <div style="font-size:9.5px;color:#dc2626;font-weight:700;margin-top:1px;">✓ Sudah Dibuat</div>
                  <?php endif; ?>
                </div>
              </div>
            </label>
            <?php endforeach; ?>

            <!-- Opsi Tambah TA Baru -->
            <label class="ta-option-card"
                   style="border:1.5px dashed #3b82f6;border-radius:10px;padding:9px 12px;cursor:pointer;background:#eff6ff;transition:all 0.15s;"
                   onclick="showNewTaInput(this)">
              <div style="display:flex;align-items:center;gap:8px;">
                <input type="radio" name="tahun_ajaran_id" value="0" id="radioNewTa" style="accent-color:#2563eb;width:15px;height:15px;">
                <div style="font-size:12.5px;font-weight:800;color:#1d4ed8;">+ TA Baru...</div>
              </div>
            </label>
          </div>

          <input type="hidden" name="new_ta" value="0" id="newTaFlag">
          <div id="newTaGroup" style="display:none;margin-top:10px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:10px 12px;">
            <label style="font-size:11.5px;font-weight:700;color:#0369a1;margin-bottom:4px;display:block;">Tuliskan Nama Tahun Ajaran Baru:</label>
            <input type="text" name="new_ta_nama" id="newTaNama" class="form-control" placeholder="Contoh: 2027/2028"
                   style="font-size:13px;font-weight:800;padding:8px 12px;border-radius:6px;border:1px solid #7dd3fc;" oninput="document.getElementById('newTaFlag').value='1'">
          </div>
        </div>

        <!-- Judul Project -->
        <div class="form-group" style="margin-bottom:12px;">
          <label for="judul" style="font-size:12.5px;font-weight:700;color:#1e293b;margin-bottom:4px;display:block;">
            📝 Judul Project <span style="font-size:11px;font-weight:500;color:#64748b;">(Opsional - Otomatis jika dikosongkan)</span>
          </label>
          <input type="text" id="judul" name="judul" class="form-control"
                 placeholder="Contoh: PPEPP 2025/2026 Program Studi Sistem Informasi"
                 style="padding:8px 12px;border-radius:8px;border:1px solid #cbd5e1;font-size:13px;">
        </div>

        <!-- Deskripsi -->
        <div class="form-group" style="margin-bottom:4px;">
          <label for="deskripsi" style="font-size:12.5px;font-weight:700;color:#1e293b;margin-bottom:4px;display:block;">
            📄 Deskripsi / Catatan Tambahan <span style="font-size:11px;font-weight:500;color:#64748b;">(Opsional)</span>
          </label>
          <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2"
                    placeholder="Tuliskan catatan singkat project ini..."
                    style="padding:8px 12px;border-radius:8px;border:1px solid #cbd5e1;font-size:12.5px;resize:vertical;"></textarea>
        </div>
      </div>

      <!-- Footer (Fixed at bottom) -->
      <div class="modal-footer" style="padding:14px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;flex-shrink:0;">
        <button type="button" class="btn btn-outline" data-modal-close="modalBuatProject" style="padding:9px 18px;font-size:13px;font-weight:700;border-radius:8px;">
          Batal
        </button>
        <button type="submit" class="btn btn-primary" style="padding:9px 20px;font-size:13px;font-weight:800;border-radius:8px;background:linear-gradient(135deg,#2563eb,#1d4ed8);border:none;color:#fff;display:inline-flex;align-items:center;gap:6px;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,0.3);">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15"><path d="M12 5v14M5 12h14"/></svg>
          Buat Project PPEPP
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Konfirmasi Hapus Project Permanen -->
<div class="modal-overlay" id="modalDeleteProjectConfirm">
  <div class="modal-card" style="max-width:480px;border-radius:18px;overflow:hidden;box-shadow:0 24px 48px rgba(0,0,0,0.3);padding:0;">
    <div style="background:linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);color:#fff;padding:22px 24px;display:flex;align-items:center;gap:14px;">
      <div style="width:44px;height:44px;background:rgba(255,255,255,0.22);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">
        🗑️
      </div>
      <div>
        <h3 style="font-size:17px;font-weight:800;margin:0 0 2px;color:#fff;">Hapus Project Permanen</h3>
        <p style="font-size:12px;margin:0;opacity:0.9;color:#fff;">Konfirmasi Penghapusan Project PPEPP</p>
      </div>
    </div>
    <form id="formDeleteProjectConfirm" method="POST" action="">
      <div class="modal-body" style="padding:22px 24px;background:#fff;">
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px 16px;color:#991b1b;font-size:13px;line-height:1.6;margin-bottom:16px;">
          ⚠️ <strong>PERHATIAN SANGAT PENTING:</strong><br>
          Anda akan menghapus project <strong id="delProjectTitle" style="color:#7f1d1d;">-</strong> secara <strong>PERMANEN</strong>.
          <br><br>
          Seluruh data 5 tahap PPEPP di dalamnya (<strong>Penetapan, Pelaksanaan, Evaluasi, Pengendalian, & Peningkatan</strong>) beserta berkas upload akan <strong>TERHAPUS TOTAL & TIDAK DAPAT DI-RECOVERY KEMBALI</strong>.
        </div>
        <p style="font-size:13px;color:#475569;margin:0;">Apakah Anda yakin ingin melanjutkan penghapusan ini?</p>
      </div>
      <div class="modal-footer" style="padding:16px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-outline" data-modal-close="modalDeleteProjectConfirm" style="padding:9px 18px;font-weight:600;border-radius:10px;">
          Batal
        </button>
        <button type="submit" class="btn btn-danger" style="padding:9px 20px;font-weight:800;border-radius:10px;background:#dc2626;color:#fff;border:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer;">
          🗑️ Ya, Hapus Project &amp; Seluruh Isinya
        </button>
      </div>
    </form>
  </div>
</div>
