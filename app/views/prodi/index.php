<?php
$pageTitle   = 'Manajemen Program Studi (Prodi)';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Program Studi'],
];
$jenjangLabel = ['D3' => 'D-III', 'S1' => 'S-1', 'S2' => 'S-2', 'S3' => 'S-3'];
?>

<!-- Header -->
<div class="page-header" style="margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
  <div>
    <h2 style="font-size:20px;font-weight:800;color:var(--text-main);margin:0 0 4px;">Program Studi (Prodi)</h2>
    <p class="page-desc" style="margin:0;">Kelola program studi dan penugasan dosen ke masing-masing prodi</p>
  </div>
  <a href="<?= BASE_URL ?>/prodi/create" class="btn btn-primary" style="font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:6px;">
    + Tambah Prodi Baru
  </a>
</div>

<?php if (!empty($flash['message'])): ?>
<div class="alert alert-<?= $flash['type']==='success' ? 'success' : 'error' ?>" style="margin-bottom:16px;">
  <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<?php if (empty($prodis)): ?>
<div class="card" style="padding:48px 24px;text-align:center;">
  <div style="width:56px;height:56px;border-radius:12px;background:#f1f5f9;color:var(--text-muted);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
  </div>
  <div style="font-size:16px;font-weight:700;color:var(--text-main);margin-bottom:6px;">Belum ada Program Studi</div>
  <div style="font-size:13px;color:var(--text-muted);margin-bottom:20px;">Mulai dengan membuat prodi pertama dan menambahkan dosen ke dalamnya.</div>
  <a href="<?= BASE_URL ?>/prodi/create" class="btn btn-primary">
    + Buat Prodi Pertama
  </a>
</div>
<?php else: ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:18px;">
  <?php foreach ($prodis as $p): ?>
  <div class="card" style="border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.04);transition:all 0.2s;"
       onmouseover="this.style.borderColor='var(--primary)';" onmouseout="this.style.borderColor='var(--border)';">
    <!-- Card Header -->
    <div style="background:#f8fafc;padding:18px 20px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
      <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
          <span style="background:var(--primary);color:#fff;font-weight:700;font-size:12px;padding:3px 10px;border-radius:6px;">
            <?= htmlspecialchars($p['kode']) ?>
          </span>
          <span style="background:#f1f5f9;color:var(--text-muted);font-size:11.5px;font-weight:600;padding:2px 8px;border-radius:6px;border:1px solid var(--border);">
            <?= $jenjangLabel[$p['jenjang']] ?? $p['jenjang'] ?>
          </span>
          <?php if (!$p['aktif']): ?>
          <span style="background:#fef2f2;color:#dc2626;font-size:11px;font-weight:600;padding:2px 8px;border-radius:6px;border:1px solid #fecaca;">Nonaktif</span>
          <?php endif; ?>
        </div>
        <h3 style="font-size:15px;font-weight:700;color:var(--text-main);margin:0 0 4px;"><?= htmlspecialchars($p['nama']) ?></h3>
        <?php if ($p['kaprodi_nama']): ?>
        <div style="font-size:12px;color:var(--primary);font-weight:600;">Kaprodi: <?= htmlspecialchars($p['kaprodi_nama']) ?></div>
        <?php else: ?>
        <div style="font-size:12px;color:var(--text-muted);font-style:italic;">Kaprodi belum ditentukan</div>
        <?php endif; ?>
      </div>
      <div style="text-align:center;flex-shrink:0;">
        <div style="font-size:24px;font-weight:800;color:var(--primary);"><?= (int)$p['jumlah_dosen'] ?></div>
        <div style="font-size:11px;color:var(--text-muted);font-weight:600;">Dosen</div>
      </div>
    </div>

    <!-- Card Footer -->
    <div style="padding:12px 20px;display:flex;gap:8px;justify-content:flex-end;background:#fff;">
      <a href="<?= BASE_URL ?>/prodi/<?= $p['id'] ?>" class="btn btn-sm btn-outline" style="font-size:12px;font-weight:600;">
        Lihat Detail
      </a>
      <a href="<?= BASE_URL ?>/prodi/<?= $p['id'] ?>/edit" class="btn btn-sm btn-outline" style="font-size:12px;font-weight:600;">
        Edit
      </a>
      <form action="<?= BASE_URL ?>/prodi/<?= $p['id'] ?>/delete" method="POST" style="margin:0;"
            onsubmit="return confirm('Yakin hapus prodi <?= htmlspecialchars(addslashes($p['nama'])) ?>? Semua dosen akan dilepas dari prodi ini.')">
        <button type="submit" class="btn btn-sm btn-danger" style="font-size:12px;padding:5px 8px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
        </button>
      </form>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
