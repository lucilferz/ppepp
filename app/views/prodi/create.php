<?php
$pageTitle   = 'Tambah Program Studi Baru';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Admin', 'url' => BASE_URL . '/admin'],
  ['label' => 'Tambah Prodi Baru'],
];
?>

<div style="max-width:680px;margin:0 auto;">
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:18px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,0.04);">
    <div style="background:linear-gradient(135deg,#0f172a,#0c4a6e);padding:24px 28px;">
      <h2 style="font-size:20px;font-weight:900;color:#fff;margin:0 0 4px;">🏫 Tambah Program Studi Baru</h2>
      <p style="font-size:13px;color:rgba(255,255,255,0.7);margin:0;">
        Dosen bisa ditambahkan ke prodi ini setelah prodi dibuat.
      </p>
    </div>

    <?php if (!empty($flash['message'])): ?>
    <div style="margin:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;">
      <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/prodi/store" method="POST" style="padding:28px;display:flex;flex-direction:column;gap:18px;">

      <div style="display:grid;grid-template-columns:120px 1fr;gap:14px;">
        <div>
          <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">
            Kode Prodi <span style="color:#ef4444;">*</span>
          </label>
          <input type="text" name="kode" class="form-control" required maxlength="10"
                 placeholder="SI / TI / MI"
                 style="font-size:14px;padding:10px 14px;border-radius:10px;font-weight:800;text-transform:uppercase;text-align:center;">
          <div style="font-size:11px;color:#94a3b8;margin-top:3px;">Contoh: SI, TI, MI</div>
        </div>
        <div>
          <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">
            Nama Program Studi <span style="color:#ef4444;">*</span>
          </label>
          <input type="text" name="nama" class="form-control" required
                 placeholder="Contoh: Teknik Informatika"
                 style="font-size:13.5px;padding:10px 14px;border-radius:10px;">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
        <div>
          <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">Jenjang</label>
          <select name="jenjang" class="form-control" style="font-size:13.5px;padding:10px 14px;border-radius:10px;">
            <option value="D3">D-III (Diploma 3)</option>
            <option value="S1" selected>S-1 (Sarjana)</option>
            <option value="S2">S-2 (Magister)</option>
            <option value="S3">S-3 (Doktor)</option>
          </select>
        </div>
        <div>
          <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">
            Kaprodi <span style="font-weight:400;color:#94a3b8;">(Opsional)</span>
          </label>
          <select name="kaprodi_id" class="form-control" style="font-size:13.5px;padding:10px 14px;border-radius:10px;">
            <option value="">— Belum Ditentukan —</option>
            <?php foreach ($allDosen as $u): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nama_lengkap']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div>
        <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">
          Deskripsi <span style="font-weight:400;color:#94a3b8;">(Opsional)</span>
        </label>
        <textarea name="deskripsi" class="form-control" rows="3"
                  placeholder="Deskripsi singkat program studi..."
                  style="font-size:13.5px;padding:10px 14px;border-radius:10px;resize:vertical;"></textarea>
      </div>

      <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:12px;padding:14px 16px;font-size:12.5px;color:#0369a1;line-height:1.6;">
        💡 <strong>Info:</strong> Setelah prodi dibuat, Anda dapat menambahkan dosen ke prodi ini melalui halaman detail prodi. Dosen yang ditambahkan ke prodi ini akan bisa membuat PPEPP dan melihat data sesama prodi.
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid #f1f5f9;">
        <a href="<?= BASE_URL ?>/admin" style="display:inline-flex;align-items:center;padding:10px 20px;background:#f1f5f9;color:#334155;font-weight:700;font-size:13px;border-radius:10px;text-decoration:none;">
          Batal / Kembali
        </a>
        <button type="submit" class="btn btn-primary" style="font-size:13px;padding:10px 24px;border-radius:10px;background:linear-gradient(135deg,#0284c7,#0369a1);border:none;">
          🏫 Buat Program Studi
        </button>
      </div>
    </form>
  </div>
</div>
