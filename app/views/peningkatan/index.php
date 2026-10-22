<?php
$pageTitle   = 'Peningkatan';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Peningkatan'],
];
?>

<!-- Stepper PPEPP Universal -->
<div class="card mb-4" style="background:#fff;border-radius:10px;">
  <div class="card-body" style="padding:16px 20px;">
    <div class="ppepp-progress">
      <a href="<?= BASE_URL ?>/penetapan" class="pp-step completed">
        <div class="pp-step-inner">
          <div class="pp-step-circle">✓</div>
          <div>
            <div class="pp-step-title">P1. Penetapan</div>
            <div class="pp-step-label">Standar Mutu</div>
          </div>
        </div>
      </a>
      <div class="pp-connector active"></div>
      <a href="<?= BASE_URL ?>/pelaksanaan" class="pp-step completed">
        <div class="pp-step-inner">
          <div class="pp-step-circle">✓</div>
          <div>
            <div class="pp-step-title">P2. Pelaksanaan</div>
            <div class="pp-step-label">Implementasi</div>
          </div>
        </div>
      </a>
      <div class="pp-connector active"></div>
      <a href="<?= BASE_URL ?>/evaluasi" class="pp-step completed">
        <div class="pp-step-inner">
          <div class="pp-step-circle">✓</div>
          <div>
            <div class="pp-step-title">E. Evaluasi</div>
            <div class="pp-step-label">Audit Mutu Internal</div>
          </div>
        </div>
      </a>
      <div class="pp-connector active"></div>
      <a href="<?= BASE_URL ?>/pengendalian" class="pp-step completed">
        <div class="pp-step-inner">
          <div class="pp-step-circle">✓</div>
          <div>
            <div class="pp-step-title">P3. Pengendalian</div>
            <div class="pp-step-label">RTL & Notulensi</div>
          </div>
        </div>
      </a>
      <div class="pp-connector active"></div>
      <a href="<?= BASE_URL ?>/peningkatan" class="pp-step active">
        <div class="pp-step-inner">
          <div class="pp-step-circle">5</div>
          <div>
            <div class="pp-step-title">P4. Peningkatan</div>
            <div class="pp-step-label">Pembaruan Standar</div>
          </div>
        </div>
      </a>
    </div>
  </div>
</div>

<div class="page-header">
  <div>
    <h2>Peningkatan PPEPP</h2>
    <p class="page-desc">Penetapan kembali standar mutu (tetap atau ditingkatkan) dan penyusunan SK/Kebijakan baru untuk siklus mutu berikutnya.</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/peningkatan/create" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Buat Peningkatan Baru
    </a>
  </div>
</div>

<!-- Stats -->
<div class="stats-grid mb-4" style="grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)$stats['total'] ?></div>
      <div class="stat-label">Total Peningkatan</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon gold">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)$stats['draft'] ?></div>
      <div class="stat-label">Status Draft</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)$stats['final'] ?></div>
      <div class="stat-label">Status Final / SK Terbit</div>
    </div>
  </div>
</div>

<!-- Daftar -->
<div class="card">
  <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
    <h3 class="card-title">
      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      Daftar Dokumen Peningkatan Standar
    </h3>
    <span class="badge badge-neutral"><?= count($peningkatans) ?> dokumen tersimpan</span>
  </div>

  <?php if (empty($peningkatans)): ?>
  <div class="empty-state" style="padding:60px 20px;">
    <div class="empty-icon">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
    </div>
    <h3>Belum Ada Dokumen Peningkatan</h3>
    <p>Buat peningkatan berdasarkan hasil pengendalian.<br>Tentukan target baru standar mutu dan persiapkan penetapan siklus akademik berikutnya.</p>
    <a href="<?= BASE_URL ?>/peningkatan/create" class="btn btn-primary" style="margin-top:14px;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Buat Peningkatan Pertama
    </a>
  </div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:50px;">#</th>
          <th>Judul Peningkatan</th>
          <th>Acuan Standar & Pengendalian</th>
          <th>Tahun Ajaran</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th style="text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($peningkatans as $i => $pk): ?>
        <tr>
          <td class="td-muted"><?= $i + 1 ?></td>
          <td>
            <div style="font-weight:600;color:var(--text-primary);"><?= htmlspecialchars($pk['judul']) ?></div>
            <?php if (!empty($pk['deskripsi'])): ?>
            <div class="td-muted" style="font-size:12px;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;">
              <?= htmlspecialchars($pk['deskripsi']) ?>
            </div>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-size:13px;font-weight:600;color:var(--text-primary);">
              <?= htmlspecialchars($pk['penetapan_judul'] ?? '—') ?>
            </div>
            <?php if (!empty($pk['pengendalian_judul']) && $pk['pengendalian_judul'] !== '—'): ?>
            <div style="font-size:11.5px;color:var(--success);display:flex;align-items:center;gap:4px;margin-top:2px;">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Pengendalian: <?= htmlspecialchars($pk['pengendalian_judul']) ?></span>
            </div>
            <?php endif; ?>
          </td>
          <td><span class="badge badge-info"><?= htmlspecialchars($pk['tahun_ajaran_nama'] ?? '—') ?></span></td>
          <td>
            <div style="display:flex;flex-direction:column;gap:4px;align-items:flex-start;">
              <span class="badge <?= $pk['status'] === 'final' ? 'badge-success' : 'badge-warning' ?> badge-dot">
                <?= $pk['status'] === 'final' ? 'Final' : 'Draft' ?>
              </span>
              <?php if (($pk['visibility_status'] ?? '') === 'nonaktif'): ?>
              <span class="badge badge-warning" style="font-size:10px;padding:2px 6px;">Nonaktif</span>
              <?php elseif (($pk['visibility_status'] ?? '') === 'hidden'): ?>
              <span class="badge badge-neutral" style="font-size:10px;padding:2px 6px;">Hide</span>
              <?php endif; ?>
            </div>
          </td>
          <td class="td-muted" style="font-size:12.5px;"><?= date('d M Y', strtotime($pk['created_at'])) ?></td>
          <td>
            <div style="display:flex;gap:6px;align-items:center;justify-content:flex-end;flex-wrap:wrap;">
              <a href="<?= BASE_URL ?>/peningkatan/<?= $pk['id'] ?>" class="btn btn-outline btn-sm">Detail</a>
              <a href="<?= BASE_URL ?>/peningkatan/<?= $pk['id'] ?>/edit" class="btn btn-primary btn-sm">Edit</a>
              
              <?php if (($pk['visibility_status'] ?? 'aktif') === 'aktif'): ?>
              <form method="POST" action="<?= BASE_URL ?>/peningkatan/<?= $pk['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="nonaktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/peningkatan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:var(--warning);border-color:#FDE047;background:#FFFBEB;font-size:11px;" title="Nonaktifkan dokumen">
                  Nonaktifkan
                </button>
              </form>
              <?php elseif (($pk['visibility_status'] ?? '') === 'nonaktif'): ?>
              <form method="POST" action="<?= BASE_URL ?>/peningkatan/<?= $pk['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="aktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/peningkatan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:var(--success);border-color:#A7F3D0;background:#ECFDF5;font-size:11px;" title="Aktifkan kembali">
                  Aktifkan
                </button>
              </form>
              <form method="POST" action="<?= BASE_URL ?>/peningkatan/<?= $pk['id'] ?>/toggle-status" style="margin:0;" onsubmit="return confirm('Sembunyikan (Hide) peningkatan ini?')">
                <input type="hidden" name="visibility_status" value="hidden">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/peningkatan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:var(--text-secondary);font-size:11px;" title="Hide dokumen">
                  Hide
                </button>
              </form>
              <?php else: ?>
              <form method="POST" action="<?= BASE_URL ?>/peningkatan/<?= $pk['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="aktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/peningkatan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:var(--success);border-color:#A7F3D0;background:#ECFDF5;font-size:11px;" title="Aktifkan kembali">
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
