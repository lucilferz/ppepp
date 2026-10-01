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
<div class="page-header" style="margin-bottom:24px;">
  <div>
    <h2 style="font-size:22px;font-weight:800;color:var(--text-main);">Buat Project PPEPP Baru</h2>
    <p class="page-desc">Inisialisasi satu siklus penjaminan mutu 5 tahap PPEPP untuk Tahun Ajaran yang dipilih.</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/ppepp" class="btn btn-outline">
      ← Kembali ke Daftar Project
    </a>
  </div>
</div>

<?php if (!empty($flash['message'])): ?>
<div class="alert" style="margin-bottom:20px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;<?= ($flash['type'] ?? '') === 'success' ? 'background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;' : 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;' ?>">
  <?= ($flash['type'] ?? '') === 'success' ? '✓' : '✕' ?> <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<div class="card" style="max-width:640px;">
  <div class="card-header" style="padding:18px 24px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;gap:12px;">
    <div style="width:36px;height:36px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;">
      📁
    </div>
    <div>
      <div class="card-title" style="font-size:16px;font-weight:800;color:#0f172a;margin:0;">Form Project PPEPP</div>
      <p style="font-size:12px;color:#64748b;margin:2px 0 0;">Lengkapi data project di bawah ini</p>
    </div>
  </div>

  <div class="card-body" style="padding:24px;">
    <!-- Banner Petunjuk -->
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px;font-size:12.5px;color:#475569;margin-bottom:20px;line-height:1.5;">
      💡 <strong>Petunjuk:</strong> Pilih <strong>Tahun Ajaran</strong> untuk project baru. Seluruh data 5 tahap PPEPP (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, & Peningkatan) akan terikat otomatis ke Tahun Ajaran ini.
    </div>

    <form method="POST" action="<?= BASE_URL ?>/ppepp/store">
      <!-- Pilihan Tahun Ajaran -->
      <div class="form-group" style="margin-bottom:20px;">
        <label style="font-size:13px;font-weight:800;color:#1e293b;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
          <span>📅 Pilih Tahun Ajaran</span>
          <span style="color:#dc2626;">*</span>
        </label>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;max-height:220px;overflow-y:auto;padding:2px 4px 6px 2px;">
          <?php foreach ($allTa as $ta): ?>
          <?php
            $isAvailable = in_array((int)$ta['id'], $availableIds, true);
          ?>
          <label class="ta-option-card"
                 style="border:1.5px solid <?= $isAvailable ? '#e2e8f0' : '#f1f5f9' ?>;border-radius:10px;padding:10px 12px;cursor:<?= $isAvailable ? 'pointer' : 'not-allowed' ?>;transition:all 0.15s;background:<?= $isAvailable ? '#ffffff' : '#f8fafc' ?>;<?= $isAvailable ? '' : 'opacity:0.55;' ?>"
                 <?= $isAvailable ? 'onclick="selectTahunAjaran(this)"' : '' ?>>
            <div style="display:flex;align-items:center;gap:8px;">
              <input type="radio" name="tahun_ajaran_id" value="<?= $ta['id'] ?>" <?= $isAvailable ? '' : 'disabled' ?> style="accent-color:#2563eb;width:16px;height:16px;">
              <div>
                <div style="font-size:13px;font-weight:800;color:<?= $isAvailable ? '#0f172a' : '#64748b' ?>;"><?= htmlspecialchars($ta['nama']) ?></div>
                <?php if (!$isAvailable): ?>
                <div style="font-size:10px;color:#dc2626;font-weight:700;margin-top:2px;">✓ Sudah Dibuat</div>
                <?php endif; ?>
              </div>
            </div>
          </label>
          <?php endforeach; ?>

          <!-- Opsi Tambah TA Baru -->
          <label class="ta-option-card"
                 style="border:1.5px dashed #3b82f6;border-radius:10px;padding:10px 12px;cursor:pointer;background:#eff6ff;transition:all 0.15s;"
                 onclick="showNewTaInput(this)">
            <div style="display:flex;align-items:center;gap:8px;">
              <input type="radio" name="tahun_ajaran_id" value="0" id="radioNewTa" style="accent-color:#2563eb;width:16px;height:16px;">
              <div style="font-size:13px;font-weight:800;color:#1d4ed8;">+ TA Baru...</div>
            </div>
          </label>
        </div>

        <input type="hidden" name="new_ta" value="0" id="newTaFlag">
        <div id="newTaGroup" style="display:none;margin-top:12px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:12px 14px;">
          <label style="font-size:12px;font-weight:700;color:#0369a1;margin-bottom:6px;display:block;">Tuliskan Nama Tahun Ajaran Baru:</label>
          <input type="text" name="new_ta_nama" id="newTaNama" class="form-control" placeholder="Contoh: 2027/2028"
                 style="font-size:13.5px;font-weight:800;padding:8px 12px;border-radius:8px;border:1px solid #7dd3fc;" oninput="document.getElementById('newTaFlag').value='1'">
        </div>
      </div>

      <!-- Judul Project -->
      <div class="form-group" style="margin-bottom:16px;">
        <label for="judul" style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:6px;display:block;">
          📝 Judul Project <span style="font-size:11.5px;font-weight:400;color:#64748b;">(Opsional — otomatis dibuat jika dikosongkan)</span>
        </label>
        <input type="text" id="judul" name="judul" class="form-control"
               placeholder="Contoh: PPEPP 2025/2026 Program Studi Sistem Informasi"
               style="padding:9px 12px;border-radius:8px;border:1px solid #cbd5e1;font-size:13px;">
      </div>

      <!-- Deskripsi -->
      <div class="form-group" style="margin-bottom:24px;">
        <label for="deskripsi" style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:6px;display:block;">
          📄 Deskripsi / Catatan Tambahan <span style="font-size:11.5px;font-weight:400;color:#64748b;">(Opsional)</span>
        </label>
        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"
                  placeholder="Tuliskan catatan singkat mengenai project ini..."
                  style="padding:9px 12px;border-radius:8px;border:1px solid #cbd5e1;font-size:13px;resize:vertical;"></textarea>
      </div>

      <!-- Tombol Aksi -->
      <div style="display:flex;gap:12px;align-items:center;padding-top:16px;border-top:1px solid #f1f5f9;">
        <button type="submit" class="btn btn-primary" style="padding:10px 22px;font-size:13.5px;font-weight:800;border-radius:8px;background:linear-gradient(135deg,#2563eb,#1d4ed8);border:none;color:#fff;display:inline-flex;align-items:center;gap:8px;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,0.25);">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><path d="M12 5v14M5 12h14"/></svg>
          Buat Project PPEPP
        </button>
        <a href="<?= BASE_URL ?>/ppepp" class="btn btn-outline" style="padding:10px 18px;font-size:13px;font-weight:700;border-radius:8px;">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>

<script>
function selectTahunAjaran(el) {
  document.querySelectorAll('.ta-option-card').forEach(function(c) {
    c.style.borderColor = '#e2e8f0';
    c.style.background = '#fff';
  });
  el.style.borderColor = '#2563eb';
  el.style.background = '#eff6ff';

  var input = el.querySelector('input[type="radio"]');
  if (input) input.checked = true;

  var newTaGroup = document.getElementById('newTaGroup');
  if (newTaGroup) newTaGroup.style.display = 'none';
  var newTaFlag = document.getElementById('newTaFlag');
  if (newTaFlag) newTaFlag.value = '0';
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

  var newTaGroup = document.getElementById('newTaGroup');
  if (newTaGroup) newTaGroup.style.display = 'block';
  var newTaFlag = document.getElementById('newTaFlag');
  if (newTaFlag) newTaFlag.value = '1';
  var newTaNama = document.getElementById('newTaNama');
  if (newTaNama) newTaNama.focus();
}
</script>
