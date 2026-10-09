<?php
$pageTitle   = 'Edit Data Dosen';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Admin', 'url' => BASE_URL . '/admin'],
  ['label' => 'Edit: ' . htmlspecialchars($user['nama_lengkap'] ?? $user['username'])],
];
$roleBadge = [
  'dekan'   => 'Dekan',
  'kaprodi' => 'Kaprodi',
  'dosen'   => 'Dosen',
];
?>

<div style="max-width:640px;margin:0 auto;">
  <div class="card" style="border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
    <div style="background:var(--primary);padding:22px 28px;color:#fff;display:flex;align-items:center;gap:14px;">
      <?php if (!empty($user['avatar_url'])): ?>
      <img src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="" width="44" height="44"
           style="border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.3);flex-shrink:0;">
      <?php else: ?>
      <div style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,0.15);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;flex-shrink:0;">
        <?= strtoupper(mb_substr(explode(' ', $user['nama_lengkap'] ?: $user['username'])[0], 0, 1)) ?>
      </div>
      <?php endif; ?>
      <div>
        <h2 style="font-size:18px;font-weight:800;color:#fff;margin:0 0 2px;"><?= htmlspecialchars($user['nama_lengkap'] ?: $user['username']) ?></h2>
        <p style="font-size:12.5px;color:rgba(255,255,255,0.75);margin:0;"><?= htmlspecialchars($user['email'] ?? '') ?> &nbsp;·&nbsp; <?= $roleBadge[$user['role']] ?? 'Dosen' ?></p>
      </div>
    </div>

    <?php if (!empty($flash['message'])): ?>
    <div style="margin:16px;padding:12px 16px;border-radius:8px;font-size:13.5px;font-weight:600;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;">
      <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/users/<?= $user['id'] ?>/update" method="POST" style="padding:28px;display:flex;flex-direction:column;gap:18px;">

      <div>
        <label style="font-size:13px;font-weight:700;color:var(--text-main);display:block;margin-bottom:6px;">Nama Lengkap (dengan gelar)</label>
        <input type="text" name="nama_lengkap" class="form-control" required
               value="<?= htmlspecialchars($user['nama_lengkap'] ?? '') ?>"
               style="font-size:13.5px;padding:10px 14px;border-radius:8px;">
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
        <div>
          <label style="font-size:13px;font-weight:700;color:var(--text-main);display:block;margin-bottom:6px;">Email</label>
          <input type="email" name="email" class="form-control" required
                 value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                 style="font-size:13.5px;padding:10px 14px;border-radius:8px;">
        </div>
        <div>
          <label style="font-size:13px;font-weight:700;color:var(--text-main);display:block;margin-bottom:6px;">Role</label>
          <select name="role" class="form-control" style="font-size:13.5px;padding:10px 14px;border-radius:8px;">
            <option value="dosen"   <?= $user['role'] === 'dosen'   ? 'selected' : '' ?>>Dosen</option>
            <option value="kaprodi" <?= $user['role'] === 'kaprodi' ? 'selected' : '' ?>>Kaprodi</option>
            <option value="dekan"   <?= $user['role'] === 'dekan'   ? 'selected' : '' ?>>Dekan</option>
          </select>
        </div>
      </div>

      <div>
        <label style="font-size:13px;font-weight:700;color:var(--text-main);display:block;margin-bottom:6px;">
          Password Baru <span style="font-weight:400;color:var(--text-muted);">(Kosongkan jika tidak ingin mengubah)</span>
        </label>
        <input type="password" name="password" class="form-control"
               placeholder="Isi jika ingin mengubah password"
               style="font-size:13.5px;padding:10px 14px;border-radius:8px;" minlength="6">
      </div>

      <!-- Username (read-only) -->
      <div>
        <label style="font-size:13px;font-weight:700;color:var(--text-main);display:block;margin-bottom:6px;">Username (NIP)</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" readonly
               style="font-size:13.5px;padding:10px 14px;border-radius:8px;background:#f8fafc;color:var(--text-muted);">
        <div style="font-size:11.5px;color:var(--text-muted);margin-top:4px;">Username tidak dapat diubah</div>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid #f1f5f9;">
        <a href="<?= BASE_URL ?>/admin" class="btn btn-outline" style="padding:10px 20px;font-weight:600;font-size:13px;border-radius:8px;text-decoration:none;">
          Batal / Kembali
        </a>
        <button type="submit" class="btn btn-primary" style="font-size:13px;padding:10px 24px;border-radius:8px;font-weight:700;">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>
