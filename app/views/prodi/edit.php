<?php
$pageTitle   = 'Edit Prodi: ' . htmlspecialchars($prodi['nama']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Admin', 'url' => BASE_URL . '/admin'],
  ['label' => htmlspecialchars($prodi['nama']), 'url' => BASE_URL . '/prodi/' . $prodi['id']],
  ['label' => 'Edit'],
];
?>

<div class="page-header">
  <div>
    <h2>Edit Program Studi</h2>
    <p class="page-desc">Perbarui data administrasi program studi <strong><?= htmlspecialchars($prodi['nama']) ?></strong>.</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>" class="btn btn-outline">
      ← Kembali ke Detail Prodi
    </a>
  </div>
</div>

<div class="card" style="max-width:680px;margin:0 auto;">
  <div class="card-header">
    <h3 class="card-title">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Perbarui Data Prodi
    </h3>
  </div>

  <?php if (!empty($flash['message'])): ?>
  <div class="alert alert-danger" style="margin:16px 20px 0;">
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>/update" method="POST">
    <div class="card-body">
      <div style="display:grid;grid-template-columns:120px 1fr;gap:14px;" class="mb-3">
        <div class="form-group mb-0">
          <label for="kode" class="form-label">Kode Prodi <span style="color:var(--danger);">*</span></label>
          <input type="text" id="kode" name="kode" class="form-control font-mono" required maxlength="10"
                 value="<?= htmlspecialchars($prodi['kode']) ?>"
                 style="text-transform:uppercase;text-align:center;font-weight:700;">
        </div>
        <div class="form-group mb-0">
          <label for="nama" class="form-label">Nama Program Studi <span style="color:var(--danger);">*</span></label>
          <input type="text" id="nama" name="nama" class="form-control" required
                 value="<?= htmlspecialchars($prodi['nama']) ?>">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:14px;" class="mb-3">
        <div class="form-group mb-0">
          <label for="jenjang" class="form-label">Jenjang</label>
          <select id="jenjang" name="jenjang" class="form-control">
            <?php foreach (['D3'=>'Diploma 3 (D3)','S1'=>'Sarjana (S1)','S2'=>'Magister (S2)','S3'=>'Doktor (S3)'] as $val => $lab): ?>
            <option value="<?= $val ?>" <?= $prodi['jenjang'] === $val ? 'selected' : '' ?>><?= $lab ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group mb-0">
          <label for="kaprodi_id" class="form-label">Kaprodi</label>
          <select id="kaprodi_id" name="kaprodi_id" class="form-control">
            <option value="">— Belum Ditentukan —</option>
            <?php foreach ($allDosen as $u): ?>
            <option value="<?= $u['id'] ?>" <?= $prodi['kaprodi_id'] == $u['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($u['nama_lengkap']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group mb-0">
          <label for="aktif" class="form-label">Status Operasional</label>
          <select id="aktif" name="aktif" class="form-control">
            <option value="1" <?= $prodi['aktif'] ? 'selected' : '' ?>>Aktif</option>
            <option value="0" <?= !$prodi['aktif'] ? 'selected' : '' ?>>Nonaktif</option>
          </select>
        </div>
      </div>

      <div class="form-group mb-0">
        <label for="deskripsi" class="form-label">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($prodi['deskripsi'] ?? '') ?></textarea>
      </div>
    </div>

    <div class="card-footer" style="display:flex;justify-content:flex-end;gap:10px;">
      <a href="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>" class="btn btn-outline">
        Batal
      </a>
      <button type="submit" class="btn btn-primary">
        Simpan Perubahan
      </button>
    </div>
  </form>
</div>
