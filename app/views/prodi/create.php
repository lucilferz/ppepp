<?php
$pageTitle   = 'Tambah Program Studi Baru';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Admin', 'url' => BASE_URL . '/admin'],
  ['label' => 'Tambah Prodi Baru'],
];
?>

<div class="page-header">
  <div>
    <h2>Tambah Program Studi Baru</h2>
    <p class="page-desc">Daftarkan Program Studi baru ke dalam unit akademik fakultas.</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/admin" class="btn btn-outline">
      ← Kembali ke Admin
    </a>
  </div>
</div>

<div class="card" style="max-width:680px;margin:0 auto;">
  <div class="card-header">
    <h3 class="card-title">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      Formulir Program Studi
    </h3>
  </div>

  <?php if (!empty($flash['message'])): ?>
  <div class="alert alert-danger" style="margin:16px 20px 0;">
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>/prodi/store" method="POST">
    <div class="card-body">
      <div style="display:grid;grid-template-columns:120px 1fr;gap:14px;" class="mb-3">
        <div class="form-group mb-0">
          <label for="kode" class="form-label">Kode Prodi <span style="color:var(--danger);">*</span></label>
          <input type="text" id="kode" name="kode" class="form-control font-mono" required maxlength="10"
                 placeholder="SI" style="text-transform:uppercase;text-align:center;font-weight:700;">
        </div>
        <div class="form-group mb-0">
          <label for="nama" class="form-label">Nama Program Studi <span style="color:var(--danger);">*</span></label>
          <input type="text" id="nama" name="nama" class="form-control" required
                 placeholder="Contoh: Sistem Informasi">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:14px;" class="mb-3">
        <div class="form-group mb-0">
          <label for="jenjang" class="form-label">Jenjang Pendidikan</label>
          <select id="jenjang" name="jenjang" class="form-control">
            <option value="D3">Diploma 3 (D3)</option>
            <option value="S1" selected>Sarjana (S1)</option>
            <option value="S2">Magister (S2)</option>
            <option value="S3">Doktor (S3)</option>
          </select>
        </div>
        <div class="form-group mb-0">
          <label for="kaprodi_id" class="form-label">Ketua Program Studi (Opsional)</label>
          <select id="kaprodi_id" name="kaprodi_id" class="form-control">
            <option value="">— Belum Ditentukan —</option>
            <?php foreach ($allDosen as $u): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nama_lengkap']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group mb-3">
        <label for="deskripsi" class="form-label">Deskripsi / Visi Prodi (Opsional)</label>
        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                  placeholder="Keterangan singkat tentang program studi..."></textarea>
      </div>

      <div class="alert alert-info" style="margin:0;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        <div>
          Setelah prodi dibuat, dosen dan tim penjaminan mutu dapat ditugaskan ke prodi ini melalui halaman detail prodi.
        </div>
      </div>
    </div>

    <div class="card-footer" style="display:flex;justify-content:flex-end;gap:10px;">
      <a href="<?= BASE_URL ?>/admin" class="btn btn-outline">
        Batal
      </a>
      <button type="submit" class="btn btn-primary">
        Buat Program Studi
      </button>
    </div>
  </form>
</div>
