<?php
$pageTitle   = 'Manajemen Program Studi (Prodi)';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Program Studi'],
];
$jenjangLabel = ['D3' => 'D-III', 'S1' => 'S-1', 'S2' => 'S-2', 'S3' => 'S-3'];
?>

<!-- Header -->
<div style="background:linear-gradient(135deg,#0f172a,#0c4a6e,#075985);border-radius:16px;padding:26px 32px;margin-bottom:24px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;box-shadow:0 8px 28px rgba(7,89,133,0.2);">
  <div>
    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1.2px;color:#7dd3fc;margin-bottom:6px;">Manajemen Institusi</div>
    <h2 style="font-size:22px;font-weight:900;color:#fff;margin:0 0 4px;">🏫 Program Studi (Prodi)</h2>
    <p style="font-size:13px;color:rgba(255,255,255,0.7);margin:0;">Kelola program studi dan penugasan dosen ke masing-masing prodi</p>
  </div>
  <a href="<?= BASE_URL ?>/prodi/create"
     style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#0ea5e9,#0284c7);color:#fff;font-weight:800;font-size:13px;padding:11px 20px;border-radius:10px;text-decoration:none;box-shadow:0 4px 14px rgba(14,165,233,0.4);">
    + Tambah Prodi Baru
  </a>
</div>

<?php if (!empty($flash['message'])): ?>
<div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;<?= $flash['type']==='success' ? 'background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;' : 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;' ?>">
  <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<?php if (empty($prodis)): ?>
<div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;padding:48px;text-align:center;color:#94a3b8;">
  <div style="font-size:48px;margin-bottom:12px;">🏫</div>
  <div style="font-size:16px;font-weight:700;color:#64748b;margin-bottom:6px;">Belum ada Program Studi</div>
  <div style="font-size:13px;margin-bottom:20px;">Mulai dengan membuat prodi pertama dan menambahkan dosen ke dalamnya.</div>
  <a href="<?= BASE_URL ?>/prodi/create" style="display:inline-flex;align-items:center;gap:8px;background:#0284c7;color:#fff;font-weight:800;font-size:13px;padding:11px 24px;border-radius:10px;text-decoration:none;">
    + Buat Prodi Pertama
  </a>
</div>
<?php else: ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(360px,1fr));gap:18px;">
  <?php foreach ($prodis as $p): ?>
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,0.04);transition:box-shadow 0.2s;"
       onmouseover="this.style.boxShadow='0 8px 28px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='0 4px 18px rgba(0,0,0,0.04)'">
    <!-- Card Header -->
    <div style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);padding:20px 22px;border-bottom:1.5px solid #bae6fd;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
      <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
          <span style="background:#0284c7;color:#fff;font-weight:900;font-size:14px;padding:4px 12px;border-radius:8px;">
            <?= htmlspecialchars($p['kode']) ?>
          </span>
          <span style="background:#e0f2fe;color:#0284c7;font-size:11.5px;font-weight:700;padding:3px 10px;border-radius:20px;">
            <?= $jenjangLabel[$p['jenjang']] ?? $p['jenjang'] ?>
          </span>
          <?php if (!$p['aktif']): ?>
          <span style="background:#fef2f2;color:#dc2626;font-size:11.5px;font-weight:700;padding:3px 10px;border-radius:20px;">Nonaktif</span>
          <?php endif; ?>
        </div>
        <h3 style="font-size:16px;font-weight:900;color:#0c4a6e;margin:0 0 4px;"><?= htmlspecialchars($p['nama']) ?></h3>
        <?php if ($p['kaprodi_nama']): ?>
        <div style="font-size:12.5px;color:#0284c7;font-weight:600;">🎓 Kaprodi: <?= htmlspecialchars($p['kaprodi_nama']) ?></div>
        <?php else: ?>
        <div style="font-size:12.5px;color:#94a3b8;font-style:italic;">Kaprodi belum ditentukan</div>
        <?php endif; ?>
      </div>
      <div style="text-align:center;flex-shrink:0;">
        <div style="font-size:28px;font-weight:900;color:#0284c7;"><?= (int)$p['jumlah_dosen'] ?></div>
        <div style="font-size:11px;color:#64748b;font-weight:700;">Dosen</div>
      </div>
    </div>

    <!-- Card Footer -->
    <div style="padding:14px 22px;display:flex;gap:8px;justify-content:flex-end;">
      <a href="<?= BASE_URL ?>/prodi/<?= $p['id'] ?>"
         style="display:inline-flex;align-items:center;gap:5px;background:#f0f9ff;color:#0284c7;font-size:12.5px;font-weight:700;padding:7px 14px;border-radius:8px;text-decoration:none;border:1px solid #bae6fd;">
        👁 Lihat Detail
      </a>
      <a href="<?= BASE_URL ?>/prodi/<?= $p['id'] ?>/edit"
         style="display:inline-flex;align-items:center;gap:5px;background:#f1f5f9;color:#334155;font-size:12.5px;font-weight:700;padding:7px 14px;border-radius:8px;text-decoration:none;border:1px solid #e2e8f0;">
        ✏️ Edit
      </a>
      <form action="<?= BASE_URL ?>/prodi/<?= $p['id'] ?>/delete" method="POST" style="margin:0;"
            onsubmit="return confirm('Yakin hapus prodi <?= htmlspecialchars(addslashes($p['nama'])) ?>? Semua dosen akan dilepas dari prodi ini.')">
        <button type="submit" style="display:inline-flex;align-items:center;gap:5px;background:#fef2f2;color:#dc2626;font-size:12.5px;font-weight:700;padding:7px 12px;border-radius:8px;border:1px solid #fecaca;cursor:pointer;">
          🗑️
        </button>
      </form>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
