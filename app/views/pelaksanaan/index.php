<?php
$pageTitle   = 'Pelaksanaan Standar Mutu';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Pelaksanaan Standar'],
];
?>

<!-- PPEPP 5-Step Stepper Progress Bar -->
<div class="ppepp-progress">
  <?php
  $steps = ['Penetapan', 'Pelaksanaan', 'Evaluasi', 'Pengendalian', 'Peningkatan'];
  foreach ($steps as $i => $s):
    $isActive = ($i === 1);
    $isDone   = ($i === 0);
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
    <h2>Pelaksanaan Standar Mutu (P2)</h2>
    <p class="page-desc">Rekam bukti fisik dan realisasi pelaksanaan indikator berdasarkan penetapan standar yang telah disahkan</p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/pelaksanaan/create<?= !empty($projectId) ? '?project_id=' . $projectId : '' ?>" class="btn btn-primary btn-sm">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
      </svg>
      + Buat Pelaksanaan Baru
    </a>
  </div>
</div>

<!-- Stats Metrics -->
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
      </svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['total'] ?></div>
      <div class="stat-label">Total Dokumen Pelaksanaan</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon green">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
      </svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['final'] ?></div>
      <div class="stat-label">Pelaksanaan Final</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon gold">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
      </svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['draft'] ?></div>
      <div class="stat-label">Pelaksanaan Draft</div>
    </div>
  </div>
</div>

<!-- Daftar Pelaksanaan Table -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
      </svg>
      Daftar Dokumen Pelaksanaan Standar
    </h3>
    <span class="badge badge-info"><?= count($pelaksanaans) ?> Dokumen</span>
  </div>

  <?php if (empty($pelaksanaans)): ?>
  <div class="empty-state">
    <div class="empty-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
      </svg>
    </div>
    <h3>Belum Ada Dokumen Pelaksanaan</h3>
    <p>Buat dokumen pelaksanaan pertama dengan mengacu pada dokumen penetapan standar yang telah ada.</p>
    <a href="<?= BASE_URL ?>/pelaksanaan/create<?= !empty($projectId) ? '?project_id=' . $projectId : '' ?>" class="btn btn-primary btn-sm">
      + Buat Pelaksanaan Pertama
    </a>
  </div>
  <?php else: ?>
  <div class="table-wrap" style="border:none;border-radius:0;">
    <table>
      <thead>
        <tr>
          <th>Judul Pelaksanaan</th>
          <th>Penetapan Acuan</th>
          <th>Tahun Ajaran</th>
          <th style="width:110px;">Status</th>
          <th style="width:110px;">Dibuat</th>
          <th style="width:160px;text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pelaksanaans as $pl): ?>
        <tr>
          <td>
            <div style="font-weight:600;color:var(--text-main);"><?= htmlspecialchars($pl['judul']) ?></div>
            <?php if (!empty($pl['deskripsi'])): ?>
            <div class="td-muted" style="font-size:12px;margin-top:2px;">
              <?= htmlspecialchars(mb_strimwidth($pl['deskripsi'], 0, 70, '...')) ?>
            </div>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-size:12.5px;color:var(--text-muted);"><?= htmlspecialchars($pl['penetapan_judul']) ?></div>
          </td>
          <td>
            <span class="badge badge-neutral"><?= htmlspecialchars($pl['tahun_ajaran_nama']) ?></span>
          </td>
          <td>
            <span class="badge <?= $pl['status'] === 'final' ? 'badge-success' : 'badge-warning' ?> badge-dot">
              <?= $pl['status'] === 'final' ? 'Final' : 'Draft' ?>
            </span>
          </td>
          <td class="td-muted"><?= date('d M Y', strtotime($pl['created_at'])) ?></td>
          <td style="text-align:right;">
            <div style="display:inline-flex;gap:6px;align-items:center;">
              <a href="<?= BASE_URL ?>/pelaksanaan/<?= $pl['id'] ?>" class="btn btn-outline btn-sm">
                Detail
              </a>
              <a href="<?= BASE_URL ?>/pelaksanaan/<?= $pl['id'] ?>/edit" class="btn btn-primary btn-sm">
                Edit
              </a>
              <form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $pl['id'] ?>/delete" style="margin:0;"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelaksanaan ini?');">
                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);" title="Hapus">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                  </svg>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
