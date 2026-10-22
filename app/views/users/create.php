<?php
$pageTitle   = 'Tambah Pengguna Baru';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Admin', 'url' => BASE_URL . '/admin'],
  ['label' => 'Tambah Pengguna'],
];
?>

<div class="page-header">
  <div>
    <h2>Tambah Pengguna Baru</h2>
    <p class="page-desc">Daftarkan akun pengelola, kaprodi, atau dosen penjaminan mutu ke dalam sistem SIPMI.</p>
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
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="17" y1="11" x2="23" y2="11"/></svg>
      Formulir Pengguna Baru
    </h3>
  </div>

  <?php if (!empty($flash['message'])): ?>
  <div class="alert alert-danger" style="margin:16px 20px 0;">
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>/users/store" method="POST">
    <div class="card-body">
      <div class="form-group mb-3">
        <label for="nama_lengkap" class="form-label">
          Nama Lengkap (dengan gelar) <span style="color:var(--danger);">*</span>
        </label>
        <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" required
               placeholder="Contoh: Dr. Budi Santoso, S.T., M.T.">
        <div class="form-text">Nama lengkap beserta gelar akademik resmi.</div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:14px;" class="mb-3">
        <div class="form-group mb-0">
          <label for="username" class="form-label">
            Username / NIP <span style="color:var(--danger);">*</span>
          </label>
          <input type="text" id="username" name="username" class="form-control" required
                 placeholder="Contoh: 5812019362">
        </div>
        <div class="form-group mb-0">
          <label for="role" class="form-label">
            Peran / Hak Akses <span style="color:var(--danger);">*</span>
          </label>
          <select id="role" name="role" class="form-control">
            <option value="dosen">Dosen</option>
            <option value="kaprodi">Kaprodi</option>
            <option value="dekan">Dekan</option>
          </select>
        </div>
      </div>

      <div class="form-group mb-3">
        <label for="email" class="form-label">
          Email Institusi (@unika.ac.id) <span style="color:var(--danger);">*</span>
        </label>
        <input type="email" id="email" name="email" class="form-control" required
               placeholder="Contoh: budi.santoso@unika.ac.id">
        <div class="form-text">Alamat email resmi untuk notifikasi dan integrasi SSO Google.</div>
      </div>

      <div class="form-group mb-0">
        <label for="password" class="form-label">
          Password Awal <span style="color:var(--danger);">*</span>
        </label>
        <input type="password" id="password" name="password" class="form-control" required
               placeholder="Minimal 6 karakter" minlength="6">
        <div class="form-text">Pengguna dapat memperbarui password secara mandiri setelah login.</div>
      </div>
    </div>

    <div class="card-footer" style="display:flex;justify-content:flex-end;gap:10px;">
      <a href="<?= BASE_URL ?>/admin" class="btn btn-outline">
        Batal
      </a>
      <button type="submit" class="btn btn-primary">
        Simpan Pengguna Baru
      </button>
    </div>
  </form>
</div>
