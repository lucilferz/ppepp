<?php
$pageTitle   = 'Edit Prodi: ' . htmlspecialchars($prodi['nama']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Admin', 'url' => BASE_URL . '/admin'],
  ['label' => htmlspecialchars($prodi['nama']), 'url' => BASE_URL . '/prodi/' . $prodi['id']],
  ['label' => 'Edit'],
];
?>

<div style="max-width:680px;margin:0 auto;">
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:18px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,0.04);">
    <div style="background:linear-gradient(135deg,#0c4a6e,#0369a1);padding:24px 28px;">
      <h2 style="font-size:20px;font-weight:900;color:#fff;margin:0 0 4px;">✏️ Edit Program Studi</h2>
      <p style="font-size:13px;color:rgba(255,255,255,0.7);margin:0;"><?= htmlspecialchars($prodi['nama']) ?></p>
    </div>

    <?php if (!empty($flash['message'])): ?>
    <div style="margin:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;">
      <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>/update" method="POST" style="padding:28px;display:flex;flex-direction:column;gap:18px;">

      <div style="display:grid;grid-template-columns:120px 1fr;gap:14px;">
        <div>
          <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">Kode Prodi</label>
          <input type="text" name="kode" class="form-control" required maxlength="10"
                 value="<?= htmlspecialchars($prodi['kode']) ?>"
                 style="font-size:14px;padding:10px 14px;border-radius:10px;font-weight:800;text-transform:uppercase;text-align:center;">
        </div>
        <div>
          <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">Nama Program Studi</label>
          <input type="text" name="nama" class="form-control" required
                 value="<?= htmlspecialchars($prodi['nama']) ?>"
                 style="font-size:13.5px;padding:10px 14px;border-radius:10px;">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr 100px;gap:14px;">
        <div>
          <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">Jenjang</label>
          <select name="jenjang" class="form-control" style="font-size:13.5px;padding:10px 14px;border-radius:10px;">
            <?php foreach (['D3'=>'D-III (Diploma 3)','S1'=>'S-1 (Sarjana)','S2'=>'S-2 (Magister)','S3'=>'S-3 (Doktor)'] as $val => $lab): ?>
            <option value="<?= $val ?>" <?= $prodi['jenjang'] === $val ? 'selected' : '' ?>><?= $lab ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">Kaprodi</label>
          <select name="kaprodi_id" class="form-control" style="font-size:13.5px;padding:10px 14px;border-radius:10px;">
            <option value="">— Belum Ditentukan —</option>
            <?php foreach ($allDosen as $u): ?>
            <option value="<?= $u['id'] ?>" <?= $prodi['kaprodi_id'] == $u['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($u['nama_lengkap']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">Status</label>
          <select name="aktif" class="form-control" style="font-size:13.5px;padding:10px 14px;border-radius:10px;">
            <option value="1" <?= $prodi['aktif'] ? 'selected' : '' ?>>✓ Aktif</option>
            <option value="0" <?= !$prodi['aktif'] ? 'selected' : '' ?>>✗ Nonaktif</option>
          </select>
        </div>
      </div>

      <div>
        <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3"
                  style="font-size:13.5px;padding:10px 14px;border-radius:10px;resize:vertical;"><?= htmlspecialchars($prodi['deskripsi'] ?? '') ?></textarea>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid #f1f5f9;">
        <a href="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>" style="display:inline-flex;align-items:center;padding:10px 20px;background:#f1f5f9;color:#334155;font-weight:700;font-size:13px;border-radius:10px;text-decoration:none;">
          Batal
        </a>
        <button type="submit" class="btn btn-primary" style="font-size:13px;padding:10px 24px;border-radius:10px;">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>
