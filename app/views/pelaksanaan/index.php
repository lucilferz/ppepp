<?php
$pageTitle   = 'Pelaksanaan';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Pelaksanaan'],
];
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h2>Pelaksanaan PPEPP</h2>
    <p class="page-desc">Rekam bukti pelaksanaan berdasarkan penetapan yang sudah ditetapkan</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/pelaksanaan/create<?= !empty($projectId) ? '?project_id=' . $projectId : '' ?>" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Buat Pelaksanaan
    </a>
  </div>
</div>

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
    </div>
    <div>
      <div class="stat-value"><?= $stats['total'] ?></div>
      <div class="stat-label">Total Pelaksanaan</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon gold">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
    <div>
      <div class="stat-value"><?= $stats['draft'] ?></div>
      <div class="stat-label">Draft</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">
      <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    </div>
    <div>
      <div class="stat-value"><?= $stats['final'] ?></div>
      <div class="stat-label">Final</div>
    </div>
  </div>
</div>

<!-- Daftar Pelaksanaan -->
<div class="card">
  <div class="card-header">
    <div class="card-title">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Daftar Pelaksanaan
    </div>
  </div>

  <?php if (empty($pelaksanaans)): ?>
  <div class="empty-state" style="padding:60px 20px;">
    <div class="empty-icon">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
    </div>
    <h3>Belum ada Pelaksanaan</h3>
    <p>Buat pelaksanaan pertama yang mengacu pada<br>penetapan standar.</p>
    <a href="<?= BASE_URL ?>/pelaksanaan/create<?= !empty($projectId) ? '?project_id=' . $projectId : '' ?>" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Buat Pelaksanaan
    </a>
  </div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Judul Pelaksanaan</th>
          <th>Penetapan Acuan</th>
          <th>Tahun Ajaran</th>
          <th>Status</th>
          <th>Dibuat</th>
          <th style="text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pelaksanaans as $pl): ?>
        <tr>
          <td>
            <div style="font-weight:600;color:#1e293b;"><?= htmlspecialchars($pl['judul']) ?></div>
            <?php if ($pl['deskripsi']): ?>
            <div style="font-size:12px;color:#64748b;margin-top:2px;"><?= htmlspecialchars(substr($pl['deskripsi'], 0, 60)) ?>...</div>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-size:13px;color:#475569;"><?= htmlspecialchars($pl['penetapan_judul']) ?></div>
          </td>
          <td>
            <span class="badge badge-primary"><?= htmlspecialchars($pl['tahun_ajaran_nama']) ?></span>
          </td>
          <td>
            <div style="display:flex;flex-direction:column;gap:4px;">
              <?php if ($pl['status'] === 'final'): ?>
              <span class="badge badge-success badge-dot">Final</span>
              <?php else: ?>
              <span class="badge badge-warning badge-dot">Draft</span>
              <?php endif; ?>
              <?php if (($pl['visibility_status'] ?? '') === 'nonaktif'): ?>
              <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:10px;background:#fef08a;color:#854d0e;display:inline-block;">Nonaktif</span>
              <?php elseif (($pl['visibility_status'] ?? '') === 'hidden'): ?>
              <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:10px;background:#e2e8f0;color:#475569;display:inline-block;">Hide</span>
              <?php endif; ?>
            </div>
          </td>
          <td class="td-muted" style="font-size:12.5px;"><?= date('d M Y', strtotime($pl['created_at'])) ?></td>
          <td style="text-align:right;">
            <div style="display:flex;gap:6px;justify-content:flex-end;align-items:center;flex-wrap:wrap;">
              <a href="<?= BASE_URL ?>/pelaksanaan/<?= $pl['id'] ?>" class="btn btn-sm btn-outline">
                Lihat
              </a>
              <a href="<?= BASE_URL ?>/pelaksanaan/<?= $pl['id'] ?>/edit" class="btn btn-sm btn-primary">
                Edit
              </a>
              <?php if (($pl['visibility_status'] ?? 'aktif') === 'aktif'): ?>
              <form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $pl['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="nonaktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/pelaksanaan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:#854d0e;border-color:#fde047;background:#fffbeb;font-size:11px;" title="Nonaktifkan dokumen">
                  Nonaktifkan
                </button>
              </form>
              <?php elseif (($pl['visibility_status'] ?? '') === 'nonaktif'): ?>
              <form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $pl['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="aktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/pelaksanaan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:#059669;border-color:#a7f3d0;background:#ecfdf5;font-size:11px;" title="Aktifkan kembali">
                  Aktifkan
                </button>
              </form>
              <form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $pl['id'] ?>/toggle-status" style="margin:0;" onsubmit="return confirm('Sembunyikan (Hide) pelaksanaan ini?')">
                <input type="hidden" name="visibility_status" value="hidden">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/pelaksanaan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:#64748b;font-size:11px;" title="Hide dokumen">
                  Hide
                </button>
              </form>
              <?php else: ?>
              <form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $pl['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="aktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/pelaksanaan">
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
