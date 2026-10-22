<?php
$pageTitle   = 'Penetapan Standar Mutu';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Penetapan Standar'],
];
?>

<!-- PPEPP 5-Step Stepper Progress Bar -->
<div class="ppepp-progress">
  <?php
  $steps = ['Penetapan', 'Pelaksanaan', 'Evaluasi', 'Pengendalian', 'Peningkatan'];
  foreach ($steps as $i => $s):
    $isActive = ($i === 0);
    $isDone   = false;
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
    <h2>Daftar Penetapan Standar Mutu</h2>
    <p class="page-desc">Kelola penetapan standar capaian dan indikator per tahun ajaran untuk Program Studi <?= htmlspecialchars($user['nama_prodi']) ?></p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/penetapan/download-template-excel?format=xlsx" class="btn btn-outline btn-sm">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
      </svg>
      Template Excel
    </a>
    <a href="<?= BASE_URL ?>/penetapan/kriteria" class="btn btn-outline btn-sm">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
        <line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/>
      </svg>
      Kelola Kriteria
    </a>
    <a href="<?= BASE_URL ?>/penetapan/create" class="btn btn-primary btn-sm">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
      </svg>
      + Buat Penetapan Baru
    </a>
  </div>
</div>

<!-- Stats Metrics -->
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
      </svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['total'] ?></div>
      <div class="stat-label">Total Penetapan</div>
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
      <div class="stat-label">Penetapan Final</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon gold">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['draft'] ?></div>
      <div class="stat-label">Penetapan Draft</div>
    </div>
  </div>
</div>

<!-- Tabel Penetapan -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
      </svg>
      Semua Dokumen Penetapan
    </h3>
    <span class="badge badge-info"><?= count($penetapans) ?> Dokumen</span>
  </div>

  <?php if (empty($penetapans)): ?>
  <div class="empty-state">
    <div class="empty-icon">
      <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
      </svg>
    </div>
    <h3>Belum Ada Dokumen Penetapan</h3>
    <p>Mulai siklus mutu dengan membuat penetapan standar untuk tahun ajaran aktif.</p>
    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
      <a href="<?= BASE_URL ?>/penetapan/kriteria" class="btn btn-outline">Kelola Kriteria Dulu</a>
      <a href="<?= BASE_URL ?>/penetapan/create" class="btn btn-primary">Buat Penetapan Pertama</a>
    </div>
  </div>
  <?php else: ?>
  <div class="table-wrap" style="border:none;border-radius:0;">
    <table>
      <thead>
        <tr>
          <th style="width:50px;">#</th>
          <th>Judul Penetapan</th>
          <th>Tahun Ajaran</th>
          <th style="width:110px;">Status</th>
          <th style="width:140px;">Progres Pelaksanaan</th>
          <th style="width:110px;">Dibuat</th>
          <th style="width:200px;text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($penetapans as $i => $p): ?>
        <tr>
          <td class="td-muted"><?= $i + 1 ?></td>
          <td>
            <div style="font-weight:600;color:var(--text-main);"><?= htmlspecialchars($p['judul']) ?></div>
            <?php if ($p['deskripsi']): ?>
            <div class="td-muted" style="font-size:12px;max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;">
              <?= htmlspecialchars($p['deskripsi']) ?>
            </div>
            <?php endif; ?>
          </td>
          <td><span class="badge badge-neutral"><?= htmlspecialchars($p['tahun_ajaran_nama']) ?></span></td>
          <td>
            <div style="display:flex;flex-direction:column;gap:3px;">
              <span class="badge <?= $p['status'] === 'final' ? 'badge-success' : 'badge-warning' ?> badge-dot">
                <?= $p['status'] === 'final' ? 'Final' : 'Draft' ?>
              </span>
              <?php if (($p['visibility_status'] ?? '') === 'nonaktif'): ?>
              <span class="badge badge-warning" style="font-size:10px;">Nonaktif</span>
              <?php elseif (($p['visibility_status'] ?? '') === 'hidden'): ?>
              <span class="badge badge-neutral" style="font-size:10px;">Hide</span>
              <?php endif; ?>
            </div>
          </td>
          <td>
            <?php
            $prog = isset($p['pelaksanaan_progress']) ? (int)$p['pelaksanaan_progress'] : 0;
            ?>
            <div style="display:flex;align-items:center;gap:8px;">
              <div style="flex:1;height:6px;background:var(--border-subtle);border-radius:var(--radius-pill);overflow:hidden;">
                <div style="width:<?= $prog ?>%;background:var(--primary);height:100%;border-radius:var(--radius-pill);"></div>
              </div>
              <span style="font-size:11.5px;font-weight:600;color:var(--text-muted);"><?= $prog ?>%</span>
            </div>
          </td>
          <td class="td-muted"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
          <td style="text-align:right;">
            <div style="display:inline-flex;gap:6px;align-items:center;">
              <a href="<?= BASE_URL ?>/penetapan/<?= $p['id'] ?>" class="btn btn-outline btn-sm">Detail</a>
              <a href="<?= BASE_URL ?>/penetapan/<?= $p['id'] ?>/edit-step2" class="btn btn-primary btn-sm">
                <?= $p['status'] === 'final' ? 'Edit' : 'Lanjut Draft' ?>
              </a>

              <?php if (($p['visibility_status'] ?? 'aktif') === 'aktif'): ?>
              <form method="POST" action="<?= BASE_URL ?>/penetapan/<?= $p['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="nonaktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/penetapan">
                <button type="submit" class="btn btn-ghost btn-sm" title="Nonaktifkan dokumen">
                  Nonaktifkan
                </button>
              </form>
              <?php else: ?>
              <form method="POST" action="<?= BASE_URL ?>/penetapan/<?= $p['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="aktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/penetapan">
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

<!-- Panduan Box -->
<div class="card mt-4" style="background:var(--surface-hover);">
  <div class="card-body" style="padding:18px 22px;">
    <div style="display:flex;gap:14px;align-items:flex-start;">
      <div style="color:var(--primary);margin-top:2px;">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
      </div>
      <div>
        <h4 style="font-size:13.5px;font-weight:700;color:var(--text-main);margin-bottom:4px;">Panduan Tahap Penetapan Standar (P1)</h4>
        <p style="font-size:12.5px;color:var(--text-muted);line-height:1.6;margin:0;">
          <strong>Langkah 1:</strong> Konfigurasi kriteria prodi di menu <a href="<?= BASE_URL ?>/penetapan/kriteria">Kelola Kriteria</a>.<br>
          <strong>Langkah 2:</strong> Upload dokumen referensi akreditasi di <a href="<?= BASE_URL ?>/penetapan/referensi">Referensi &amp; Analisis AI</a>.<br>
          <strong>Langkah 3:</strong> Buat penetapan dan isi target capaian per kriteria. Gunakan hasil analisis AI sebagai acuan perumusan standar.
        </p>
      </div>
    </div>
  </div>
</div>
