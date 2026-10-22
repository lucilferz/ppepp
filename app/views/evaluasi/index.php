<?php
$pageTitle   = 'Evaluasi Standar Mutu';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Evaluasi Diri'],
];
$jenisLabel = ['internal' => 'Internal', 'ami' => 'AMI', 'asik' => 'ASIK', 'gabungan' => 'Gabungan'];
?>

<!-- PPEPP 5-Step Stepper Progress Bar -->
<div class="ppepp-progress">
  <?php
  $steps = ['Penetapan', 'Pelaksanaan', 'Evaluasi', 'Pengendalian', 'Peningkatan'];
  foreach ($steps as $i => $s):
    $isActive = ($i === 2);
    $isDone   = ($i < 2);
  ?>
    <div class="pp-step <?= $isActive ? 'active' : '' ?> <?= $isDone ? 'done' : '' ?>">
      <div class="pp-step-inner">
        <div class="pp-step-circle">
          <?= $isDone ? '✓' : ($i + 1) ?>
        </div>
        <span class="pp-step-label"><?= $s ?></span>
      </div>
    </div>
    <?php if ($i < count($steps) - 1): ?>
    <div class="pp-connector <?= $isDone ? 'done' : '' ?>"></div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h2>Evaluasi PPEPP (E)</h2>
    <p class="page-desc">Evaluasi ketercapaian target indikator dan dokumentasi hasil audit internal/AMI berdasarkan penetapan yang telah disahkan</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/evaluasi/create" class="btn btn-primary btn-sm">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
      </svg>
      + Buat Evaluasi Baru
    </a>
  </div>
</div>

<!-- Stats Metrics -->
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
      </svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['total'] ?></div>
      <div class="stat-label">Total Dokumen Evaluasi</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon green">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
      </svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['final'] ?></div>
      <div class="stat-label">Evaluasi Final</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon gold">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
      </svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['draft'] ?></div>
      <div class="stat-label">Evaluasi Draft</div>
    </div>
  </div>
</div>

<!-- Daftar Evaluasi -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
      </svg>
      Daftar Dokumen Evaluasi &amp; Audit Mutu
    </h3>
    <span class="badge badge-info"><?= count($evaluasis) ?> Dokumen</span>
  </div>

  <?php if (empty($evaluasis)): ?>
  <div class="empty-state">
    <div class="empty-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
      </svg>
    </div>
    <h3>Belum Ada Dokumen Evaluasi</h3>
    <p>Buat dokumen evaluasi pertama berdasarkan penetapan yang telah ada untuk mengukur ketercapaian indikator.</p>
    <a href="<?= BASE_URL ?>/evaluasi/create" class="btn btn-primary btn-sm">
      + Buat Evaluasi Pertama
    </a>
  </div>
  <?php else: ?>
  <div class="table-wrap" style="border:none;border-radius:0;">
    <table>
      <thead>
        <tr>
          <th style="width:50px;">#</th>
          <th>Judul Evaluasi</th>
          <th>Acuan Penetapan</th>
          <th>Tahun Ajaran</th>
          <th style="width:100px;">Jenis</th>
          <th style="width:110px;">Status</th>
          <th style="width:110px;">Dibuat</th>
          <th style="width:200px;text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($evaluasis as $i => $ev): ?>
        <tr>
          <td class="td-muted"><?= $i + 1 ?></td>
          <td>
            <div style="font-weight:600;color:var(--text-main);"><?= htmlspecialchars($ev['judul']) ?></div>
            <?php if (!empty($ev['deskripsi'])): ?>
            <div class="td-muted" style="font-size:12px;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;">
              <?= htmlspecialchars($ev['deskripsi']) ?>
            </div>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-size:12.5px;color:var(--text-muted);"><?= htmlspecialchars($ev['penetapan_judul']) ?></div>
            <?php if ($ev['penetapan_status'] === 'draft'): ?>
            <span class="badge badge-warning" style="font-size:10px;margin-top:2px;">Draft Penetapan</span>
            <?php endif; ?>
          </td>
          <td><span class="badge badge-neutral"><?= htmlspecialchars($ev['tahun_ajaran_nama']) ?></span></td>
          <td>
            <?php $jText = $jenisLabel[$ev['jenis']] ?? ucwords($ev['jenis']); ?>
            <span class="badge badge-neutral"><?= htmlspecialchars($jText) ?></span>
          </td>
          <td>
            <div style="display:flex;flex-direction:column;gap:3px;">
              <span class="badge <?= $ev['status'] === 'final' ? 'badge-success' : 'badge-warning' ?> badge-dot">
                <?= $ev['status'] === 'final' ? 'Final' : 'Draft' ?>
              </span>
              <?php if (($ev['visibility_status'] ?? '') === 'nonaktif'): ?>
              <span class="badge badge-warning" style="font-size:10px;">Nonaktif</span>
              <?php elseif (($ev['visibility_status'] ?? '') === 'hidden'): ?>
              <span class="badge badge-neutral" style="font-size:10px;">Hide</span>
              <?php endif; ?>
            </div>
          </td>
          <td class="td-muted"><?= date('d M Y', strtotime($ev['created_at'])) ?></td>
          <td style="text-align:right;">
            <div style="display:inline-flex;gap:6px;align-items:center;">
              <a href="<?= BASE_URL ?>/evaluasi/<?= $ev['id'] ?>" class="btn btn-outline btn-sm">Audit</a>
              <a href="<?= BASE_URL ?>/evaluasi/<?= $ev['id'] ?>/edit" class="btn btn-primary btn-sm">Edit</a>

              <?php if (($ev['visibility_status'] ?? 'aktif') === 'aktif'): ?>
              <form method="POST" action="<?= BASE_URL ?>/evaluasi/<?= $ev['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="nonaktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/evaluasi">
                <button type="submit" class="btn btn-ghost btn-sm" title="Nonaktifkan dokumen">
                  Nonaktifkan
                </button>
              </form>
              <?php else: ?>
              <form method="POST" action="<?= BASE_URL ?>/evaluasi/<?= $ev['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="aktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/evaluasi">
                <button type="submit" class="btn btn-ghost btn-sm" title="Aktifkan kembali">
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
