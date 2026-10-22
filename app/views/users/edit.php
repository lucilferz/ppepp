<?php
$pageTitle   = 'Edit Data Pengguna';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Admin', 'url' => BASE_URL . '/admin'],
  ['label' => 'Edit: ' . htmlspecialchars($user['nama_lengkap'] ?? $user['username'])],
];

$roleLabel = [
  'dekan'   => 'Dekan',
  'kaprodi' => 'Kaprodi',
  'dosen'   => 'Dosen',
];
?>

<div class="page-header">
  <div>
    <h2>Edit Data Pengguna</h2>
    <p class="page-desc">Perbarui informasi profil, hak akses peran, atau kredensial akun pengguna.</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/admin" class="btn btn-outline">
      ← Kembali ke Admin
    </a>
  </div>
</div>

<div class="card" style="max-width:680px;margin:0 auto;">
  <div class="card-header" style="display:flex;align-items:center;gap:14px;">
    <?php if (!empty($user['avatar_url'])): ?>
    <img src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="" width="42" height="42"
         style="border-radius:50%;object-fit:cover;border:1.5px solid var(--border);flex-shrink:0;">
    <?php else: ?>
    <div style="width:42px;height:42px;border-radius:50%;background:var(--bg-main);border:1px solid var(--border);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;flex-shrink:0;">
      <?= strtoupper(mb_substr(explode(' ', $user['nama_lengkap'] ?: $user['username'])[0], 0, 1)) ?>
    </div>
    <?php endif; ?>
    <div>
      <h3 class="card-title" style="margin:0 0 2px;">
        <?= htmlspecialchars($user['nama_lengkap'] ?: $user['username']) ?>
      </h3>
      <div class="td-muted" style="font-size:12px;">
        <?= htmlspecialchars($user['email'] ?? '') ?> &nbsp;•&nbsp; Peran: <?= $roleLabel[$user['role']] ?? 'Dosen' ?>
      </div>
    </div>
  </div>

  <?php if (!empty($flash['message'])): ?>
  <div class="alert alert-danger" style="margin:16px 20px 0;">
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>/users/<?= $user['id'] ?>/update" method="POST">
    <div class="card-body">
      <div class="form-group mb-3">
        <label for="nama_lengkap" class="form-label">Nama Lengkap (dengan gelar) <span style="color:var(--danger);">*</span></label>
        <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" required
               value="<?= htmlspecialchars($user['nama_lengkap'] ?? '') ?>">
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:14px;" class="mb-3">
        <div class="form-group mb-0">
          <label for="email" class="form-label">Email <span style="color:var(--danger);">*</span></label>
          <input type="email" id="email" name="email" class="form-control" required
                 value="<?= htmlspecialchars($user['email'] ?? '') ?>">
        </div>
        <div class="form-group mb-0">
          <label for="role" class="form-label">Role / Hak Akses <span style="color:var(--danger);">*</span></label>
          <select id="role" name="role" class="form-control">
            <option value="dosen"   <?= $user['role'] === 'dosen'   ? 'selected' : '' ?>>Dosen</option>
            <option value="kaprodi" <?= $user['role'] === 'kaprodi' ? 'selected' : '' ?>>Kaprodi</option>
            <option value="dekan"   <?= $user['role'] === 'dekan'   ? 'selected' : '' ?>>Dekan</option>
          </select>
        </div>
      </div>

      <div class="form-group mb-3">
        <label for="password" class="form-label">
          Password Baru <span style="font-weight:400;color:var(--text-muted);">(Kosongkan jika tidak ingin mengubah)</span>
        </label>
        <input type="password" id="password" name="password" class="form-control"
               placeholder="Isi hanya jika ingin mengubah password" minlength="6">
      </div>

      <!-- Username (read-only) -->
      <div class="form-group mb-0">
        <label for="username" class="form-label">Username (NIP)</label>
        <input type="text" id="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" readonly
               style="background:var(--bg-main);color:var(--text-muted);cursor:not-allowed;">
        <div class="form-text">Username adalah identitas akun utama dan tidak dapat diubah.</div>
      </div>
    </div>

    <div class="card-footer" style="display:flex;justify-content:flex-end;gap:10px;">
      <a href="<?= BASE_URL ?>/admin" class="btn btn-outline">
        Batal
      </a>
      <button type="submit" class="btn btn-primary">
        Simpan Perubahan
      </button>
    </div>
  </form>
</div>
