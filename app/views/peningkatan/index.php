<?php
$pageTitle   = 'Peningkatan';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Peningkatan'],
];
?>

<div class="page-header">
  <div>
    <h2>Peningkatan PPEPP</h2>
    <p class="page-desc">Penetapan kembali standar mutu (tetap atau ditingkatkan) dan penyusunan SK/Kebijakan baru</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/peningkatan/create" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Buat Peningkatan
    </a>
  </div>
</div>

<!-- Stats -->
<div class="stats-grid mb-4" style="grid-template-columns:repeat(3,1fr);">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['total'] ?></div>
      <div class="stat-label">Total Peningkatan</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon gold">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['draft'] ?></div>
      <div class="stat-label">Draft</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['final'] ?></div>
      <div class="stat-label">Final</div>
    </div>
  </div>
</div>

<!-- Daftar -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      Daftar Peningkatan Standar
    </h3>
    <span class="badge badge-primary"><?= count($peningkatans) ?> dokumen</span>
  </div>

  <?php if (empty($peningkatans)): ?>
  <div class="empty-state" style="padding:60px 20px;">
    <div class="empty-icon">
      <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
    </div>
    <h3>Belum ada Peningkatan</h3>
    <p>Buat peningkatan berdasarkan pengendalian yang ada.<br>Tentukan indikator yang tetap/diubah dan terbitkan penetapan tahun berikutnya.</p>
    <a href="<?= BASE_URL ?>/peningkatan/create" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Buat Peningkatan Pertama
    </a>
  </div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Judul Peningkatan</th>
          <th>Acuan Penetapan / Pengendalian</th>
          <th>Tahun Ajaran</th>
          <th>Status</th>
          <th>Dibuat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($peningkatans as $i => $pk): ?>
        <tr>
          <td class="td-muted"><?= $i + 1 ?></td>
          <td>
            <div style="font-weight:600;color:#1e293b;"><?= htmlspecialchars($pk['judul']) ?></div>
            <?php if ($pk['deskripsi']): ?>
            <div class="td-muted" style="font-size:12px;max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($pk['deskripsi']) ?></div>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-size:13px;font-weight:600;color:#1e293b;"><?= htmlspecialchars($pk['penetapan_judul']) ?></div>
            <?php if (!empty($pk['pengendalian_judul']) && $pk['pengendalian_judul'] !== '—'): ?>
            <div style="font-size:11.5px;color:#059669;">Pengendalian: <?= htmlspecialchars($pk['pengendalian_judul']) ?> ✓</div>
            <?php endif; ?>
          </td>
          <td><span class="badge badge-info"><?= htmlspecialchars($pk['tahun_ajaran_nama']) ?></span></td>
          <td>
            <div style="display:flex;flex-direction:column;gap:4px;">
              <span class="badge <?= $pk['status'] === 'final' ? 'badge-success' : 'badge-warning' ?> badge-dot">
                <?= $pk['status'] === 'final' ? 'Final' : 'Draft' ?>
              </span>
              <?php if (($pk['visibility_status'] ?? '') === 'nonaktif'): ?>
              <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:10px;background:#fef08a;color:#854d0e;display:inline-block;">Nonaktif</span>
              <?php elseif (($pk['visibility_status'] ?? '') === 'hidden'): ?>
              <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:10px;background:#e2e8f0;color:#475569;display:inline-block;">Hide</span>
              <?php endif; ?>
            </div>
          </td>
          <td class="td-muted"><?= date('d M Y', strtotime($pk['created_at'])) ?></td>
          <td>
            <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
              <a href="<?= BASE_URL ?>/peningkatan/<?= $pk['id'] ?>" class="btn btn-outline btn-sm">Detail</a>
              <a href="<?= BASE_URL ?>/peningkatan/<?= $pk['id'] ?>/edit" class="btn btn-primary btn-sm">Edit</a>
              <?php if (($pk['visibility_status'] ?? 'aktif') === 'aktif'): ?>
              <form method="POST" action="<?= BASE_URL ?>/peningkatan/<?= $pk['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="nonaktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/peningkatan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:#854d0e;border-color:#fde047;background:#fffbeb;font-size:11px;" title="Nonaktifkan dokumen">
                  Nonaktifkan
                </button>
              </form>
              <?php elseif (($pk['visibility_status'] ?? '') === 'nonaktif'): ?>
              <form method="POST" action="<?= BASE_URL ?>/peningkatan/<?= $pk['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="aktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/peningkatan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:#059669;border-color:#a7f3d0;background:#ecfdf5;font-size:11px;" title="Aktifkan kembali">
                  Aktifkan
                </button>
              </form>
              <form method="POST" action="<?= BASE_URL ?>/peningkatan/<?= $pk['id'] ?>/toggle-status" style="margin:0;" onsubmit="return confirm('Sembunyikan (Hide) peningkatan ini?')">
                <input type="hidden" name="visibility_status" value="hidden">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/peningkatan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:#64748b;font-size:11px;" title="Hide dokumen">
                  Hide
                </button>
              </form>
              <?php else: ?>
              <form method="POST" action="<?= BASE_URL ?>/peningkatan/<?= $pk['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="aktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/peningkatan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:#059669;border-color:#a7f3d0;background:#ecfdf5;font-size:11px;" title="Aktifkan kembali">
                  Aktifkan
                </button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
