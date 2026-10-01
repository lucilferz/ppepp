<?php
$pageTitle   = 'Evaluasi';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Evaluasi'],
];
$jenisLabel = ['internal' => 'Internal', 'ami' => 'AMI', 'asik' => 'ASIK', 'gabungan' => 'Gabungan'];
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h2>Evaluasi PPEPP</h2>
    <p class="page-desc">Evaluasi ketercapaian indikator dan dokumentasi hasil audit berdasarkan penetapan yang sudah dibuat</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/evaluasi/create" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Buat Evaluasi
    </a>
  </div>
</div>

<!-- Stats -->
<div class="stats-grid mb-4" style="grid-template-columns:repeat(3,1fr);">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['total'] ?></div>
      <div class="stat-label">Total Evaluasi</div>
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

<!-- Daftar Evaluasi -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
      Daftar Evaluasi
    </h3>
    <span class="badge badge-primary"><?= count($evaluasis) ?> dokumen</span>
  </div>

  <?php if (empty($evaluasis)): ?>
  <div class="empty-state" style="padding:60px 20px;">
    <div class="empty-icon">
      <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
    </div>
    <h3>Belum ada Evaluasi</h3>
    <p>Buat evaluasi pertama berdasarkan penetapan yang sudah ada.<br>Evaluasi digunakan untuk mengukur ketercapaian indikator.</p>
    <a href="<?= BASE_URL ?>/evaluasi/create" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Buat Evaluasi Pertama
    </a>
  </div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Judul Evaluasi</th>
          <th>Acuan Penetapan</th>
          <th>Tahun Ajaran</th>
          <th>Jenis</th>
          <th>Status</th>
          <th>Dibuat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($evaluasis as $i => $ev): ?>
        <tr>
          <td class="td-muted"><?= $i + 1 ?></td>
          <td>
            <div style="font-weight:600;"><?= htmlspecialchars($ev['judul']) ?></div>
            <?php if ($ev['deskripsi']): ?>
            <div class="td-muted" style="font-size:12px;max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($ev['deskripsi']) ?></div>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-size:13px;color:var(--text-muted);"><?= htmlspecialchars($ev['penetapan_judul']) ?></div>
            <?php if ($ev['penetapan_status'] === 'draft'): ?>
            <span style="font-size:10px;color:#d97706;background:#fffbeb;padding:2px 7px;border-radius:10px;font-weight:700;">Draft</span>
            <?php endif; ?>
          </td>
          <td><span class="badge badge-info"><?= htmlspecialchars($ev['tahun_ajaran_nama']) ?></span></td>
          <td>
            <?php
            $jBadge = ['internal' => 'badge-primary', 'ami' => 'badge-success', 'asik' => 'badge-info', 'gabungan' => 'badge-gray'];
            $jText  = $jenisLabel[$ev['jenis']] ?? ucwords($ev['jenis']);
            ?>
            <span class="badge <?= $jBadge[$ev['jenis']] ?? 'badge-primary' ?>"><?= htmlspecialchars($jText) ?></span>
          </td>
          <td>
            <div style="display:flex;flex-direction:column;gap:4px;">
              <span class="badge <?= $ev['status'] === 'final' ? 'badge-success' : 'badge-warning' ?> badge-dot">
                <?= $ev['status'] === 'final' ? 'Final' : 'Draft' ?>
              </span>
              <?php if (($ev['visibility_status'] ?? '') === 'nonaktif'): ?>
              <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:10px;background:#fef08a;color:#854d0e;display:inline-block;">Nonaktif</span>
              <?php elseif (($ev['visibility_status'] ?? '') === 'hidden'): ?>
              <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:10px;background:#e2e8f0;color:#475569;display:inline-block;">Hide</span>
              <?php endif; ?>
            </div>
          </td>
          <td class="td-muted"><?= date('d M Y', strtotime($ev['created_at'])) ?></td>
          <td>
            <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
              <a href="<?= BASE_URL ?>/evaluasi/<?= $ev['id'] ?>" class="btn btn-outline btn-sm">Audit</a>
              <a href="<?= BASE_URL ?>/evaluasi/<?= $ev['id'] ?>/edit" class="btn btn-primary btn-sm">Edit</a>
              <?php if (($ev['visibility_status'] ?? 'aktif') === 'aktif'): ?>
              <form method="POST" action="<?= BASE_URL ?>/evaluasi/<?= $ev['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="nonaktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/evaluasi">
                <button type="submit" class="btn btn-outline btn-sm" style="color:#854d0e;border-color:#fde047;background:#fffbeb;font-size:11px;" title="Nonaktifkan dokumen">
                  Nonaktifkan
                </button>
              </form>
              <?php elseif (($ev['visibility_status'] ?? '') === 'nonaktif'): ?>
              <form method="POST" action="<?= BASE_URL ?>/evaluasi/<?= $ev['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="aktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/evaluasi">
                <button type="submit" class="btn btn-outline btn-sm" style="color:#059669;border-color:#a7f3d0;background:#ecfdf5;font-size:11px;" title="Aktifkan kembali">
                  Aktifkan
                </button>
              </form>
              <form method="POST" action="<?= BASE_URL ?>/evaluasi/<?= $ev['id'] ?>/toggle-status" style="margin:0;" onsubmit="return confirm('Sembunyikan (Hide) evaluasi ini?')">
                <input type="hidden" name="visibility_status" value="hidden">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/evaluasi">
                <button type="submit" class="btn btn-outline btn-sm" style="color:#64748b;font-size:11px;" title="Hide dokumen">
                  Hide
                </button>
              </form>
              <?php else: ?>
              <form method="POST" action="<?= BASE_URL ?>/evaluasi/<?= $ev['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="aktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/evaluasi">
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
