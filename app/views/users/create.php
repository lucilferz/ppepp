<?php
$pageTitle   = 'Tambah Dosen Baru';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Admin', 'url' => BASE_URL . '/admin'],
  ['label' => 'Tambah Dosen'],
];
?>

<div style="max-width:640px;margin:0 auto;">
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:18px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,0.04);">
    <div style="background:linear-gradient(135deg,#0f172a,#1e1b4b);padding:22px 28px;border-bottom:1.5px solid #e2e8f0;">
      <h2 style="font-size:20px;font-weight:900;color:#fff;margin:0 0 4px;">+ Tambah Dosen Baru</h2>
      <p style="font-size:13px;color:rgba(255,255,255,0.7);margin:0;">Dosen baru akan dapat login dan membuat PPEPP sendiri</p>
    </div>

    <?php if (!empty($flash['message'])): ?>
    <div style="margin:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;">
      <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/users/store" method="POST" style="padding:28px;display:flex;flex-direction:column;gap:18px;">

      <div>
        <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">
          Nama Lengkap (dengan gelar) <span style="color:#ef4444;">*</span>
        </label>
        <input type="text" name="nama_lengkap" class="form-control" required
               placeholder="Contoh: Dr. Budi Santoso, S.T., M.T."
               style="font-size:13.5px;padding:10px 14px;border-radius:10px;">
        <div style="font-size:11.5px;color:#94a3b8;margin-top:4px;">Nama lengkap beserta gelar akademik</div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
        <div>
          <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">
            Username (NIP) <span style="color:#ef4444;">*</span>
          </label>
          <input type="text" name="username" class="form-control" required
                 placeholder="Contoh: 5812019362"
                 style="font-size:13.5px;padding:10px 14px;border-radius:10px;">
        </div>
        <div>
          <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">
            Role <span style="color:#ef4444;">*</span>
          </label>
          <select name="role" class="form-control" style="font-size:13.5px;padding:10px 14px;border-radius:10px;">
            <option value="dosen">🧑‍🏫 Dosen</option>
            <option value="kaprodi">🎓 Kaprodi</option>
            <option value="dekan">👑 Dekan</option>
          </select>
        </div>
      </div>

      <div>
        <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">
          Email (@unika.ac.id) <span style="color:#ef4444;">*</span>
        </label>
        <input type="email" name="email" class="form-control" required
               placeholder="Contoh: budi.santoso@unika.ac.id"
               style="font-size:13.5px;padding:10px 14px;border-radius:10px;">
        <div style="font-size:11.5px;color:#94a3b8;margin-top:4px;">Email ini juga digunakan untuk login Google</div>
      </div>

      <div>
        <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">
          Password <span style="color:#ef4444;">*</span>
        </label>
        <input type="password" name="password" class="form-control" required
               placeholder="Minimal 6 karakter"
               style="font-size:13.5px;padding:10px 14px;border-radius:10px;" minlength="6">
        <div style="font-size:11.5px;color:#94a3b8;margin-top:4px;">Dosen dapat mengubah password sendiri setelah login</div>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid #f1f5f9;">
        <a href="<?= BASE_URL ?>/admin" style="display:inline-flex;align-items:center;padding:10px 20px;background:#f1f5f9;color:#334155;font-weight:700;font-size:13px;border-radius:10px;text-decoration:none;">
          Batal / Kembali
        </a>
        <button type="submit" class="btn btn-primary" style="font-size:13px;padding:10px 24px;border-radius:10px;">
          + Simpan Dosen Baru
        </button>
      </div>
    </form>
  </div>
</div>
