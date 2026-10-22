<?php
$pageTitle   = 'Buat Project PPEPP';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Project PPEPP', 'url' => BASE_URL . '/ppepp'],
  ['label' => 'Buat Project Baru'],
];

$availableIds = array_map(fn($t) => (int)$t['id'], $availableTa ?? []);
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h2>Buat Project PPEPP Baru</h2>
    <p class="page-desc">Inisialisasi satu siklus penjaminan mutu 5 tahap PPEPP untuk Tahun Ajaran yang dipilih.</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/ppepp" class="btn btn-outline btn-sm">
      &larr; Kembali ke Daftar Project
    </a>
  </div>
</div>

<?php if (!empty($flash['message'])): ?>
<div class="alert alert-<?= ($flash['type'] ?? '') === 'success' ? 'success' : 'error' ?>">
  <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<div class="card" style="max-width:640px;">
  <div class="card-header">
    <h3 class="card-title">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
      </svg>
      Formulir Project PPEPP
    </h3>
  </div>

  <div class="card-body">
    <div class="card mb-3" style="background:var(--surface-hover);padding:12px 14px;border:1px solid var(--border);font-size:12.5px;color:var(--text-muted);">
      Pilih <strong>Tahun Ajaran</strong> untuk project baru. Seluruh data 5 tahap PPEPP (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, &amp; Peningkatan) akan terikat otomatis ke Tahun Ajaran ini.
    </div>

    <form method="POST" action="<?= BASE_URL ?>/ppepp/store">
      <!-- Pilihan Tahun Ajaran -->
      <div class="form-group">
        <label class="form-label">
          Pilih Tahun Ajaran <span class="required-star">*</span>
        </label>

        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(150px, 1fr));gap:8px;max-height:200px;overflow-y:auto;padding:2px;">
          <?php foreach ($allTa as $ta): ?>
          <?php $isAvailable = in_array((int)$ta['id'], $availableIds, true); ?>
          <label class="ta-option-card"
                 style="border:1px solid <?= $isAvailable ? 'var(--border)' : 'var(--border-subtle)' ?>;border-radius:var(--radius-sm);padding:8px 10px;cursor:<?= $isAvailable ? 'pointer' : 'not-allowed' ?>;transition:all 0.15s;background:<?= $isAvailable ? 'var(--surface)' : 'var(--surface-hover)' ?>;<?= $isAvailable ? '' : 'opacity:0.5;' ?>"
                 <?= $isAvailable ? 'onclick="selectTahunAjaran(this)"' : '' ?>>
            <div style="display:flex;align-items:center;gap:6px;">
              <input type="radio" name="tahun_ajaran_id" value="<?= $ta['id'] ?>" <?= $isAvailable ? '' : 'disabled' ?> style="accent-color:var(--primary);">
              <div>
                <div style="font-size:12.5px;font-weight:600;color:var(--text-main);"><?= htmlspecialchars($ta['nama']) ?></div>
                <?php if (!$isAvailable): ?>
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
          <label class="form-label" style="font-size:11.5px;color:var(--primary);">Tuliskan Nama Tahun Ajaran Baru:</label>
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
        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"
                  placeholder="Tuliskan catatan ruang lingkup siklus penjaminan mutu..."></textarea>
      </div>

      <!-- Tombol Aksi -->
      <div style="display:flex;gap:10px;align-items:center;padding-top:14px;border-top:1px solid var(--border-subtle);">
        <button type="submit" class="btn btn-primary">
          Buat Project PPEPP
        </button>
        <a href="<?= BASE_URL ?>/ppepp" class="btn btn-outline">
          Batal
        </a>
      </div>
    </form>
  </div>
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

  var newTaGroup = document.getElementById('newTaGroup');
  if (newTaGroup) newTaGroup.style.display = 'none';
  var newTaFlag = document.getElementById('newTaFlag');
  if (newTaFlag) newTaFlag.value = '0';
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

  var newTaGroup = document.getElementById('newTaGroup');
  if (newTaGroup) newTaGroup.style.display = 'block';
  var newTaFlag = document.getElementById('newTaFlag');
  if (newTaFlag) newTaFlag.value = '1';
  var newTaNama = document.getElementById('newTaNama');
  if (newTaNama) newTaNama.focus();
}
</script>
