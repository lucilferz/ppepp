<?php
$pageTitle   = 'Penetapan';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Penetapan'],
];
?>

<!-- PPEPP Progress -->
<div class="ppepp-progress mb-4">
  <?php
  $steps = ['Penetapan', 'Pelaksanaan', 'Evaluasi', 'Pengendalian', 'Peningkatan'];
  foreach ($steps as $i => $s):
    $isActive = $i === 0;
    $isDone   = false;
  ?>
    <div class="pp-step <?= $isActive ? 'active' : '' ?> <?= $isDone ? 'done' : '' ?>">
      <div class="pp-step-inner">
        <div class="pp-step-circle">
          <?= $isDone ? '✓' : substr($s, 0, 1) ?>
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
    <h2>Daftar Penetapan</h2>
    <p class="page-desc">Kelola penetapan standar dan target per tahun ajaran untuk Program Studi <?= htmlspecialchars($user['nama_prodi']) ?></p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/penetapan/download-template-excel?format=xlsx" class="btn btn-outline" style="color:#059669;border-color:#a7f3d0;background:#ecfdf5;font-weight:700;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Template Excel
    </a>
    <a href="<?= BASE_URL ?>/penetapan/kriteria" class="btn btn-outline">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
      </svg>
      Kelola Kriteria
    </a>
    <a href="<?= BASE_URL ?>/penetapan/create" class="btn btn-primary">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
      </svg>
      Buat Penetapan Baru
    </a>
  </div>
</div>

<!-- Stats -->
<div class="stats-grid mb-4" style="grid-template-columns:repeat(3,1fr);">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
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
        <path d="M9 11l3 3L22 4"/>
        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
      </svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['final'] ?></div>
      <div class="stat-label">Final</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon gold">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <polyline points="12 6 12 12 16 14"/>
      </svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['draft'] ?></div>
      <div class="stat-label">Draft</div>
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
        <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
      </svg>
      Semua Penetapan
    </h3>
    <span class="badge badge-primary"><?= count($penetapans) ?> dokumen</span>
  </div>

  <?php if (empty($penetapans)): ?>
  <div class="empty-state">
    <div class="empty-icon">
      <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
      </svg>
    </div>
    <h3>Belum ada penetapan</h3>
    <p>Mulai dengan membuat penetapan untuk tahun ajaran ini.<br>Pastikan kriteria sudah dikonfigurasi terlebih dahulu.</p>
    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
      <a href="<?= BASE_URL ?>/penetapan/kriteria" class="btn btn-outline">Kelola Kriteria Dulu</a>
      <a href="<?= BASE_URL ?>/penetapan/create" class="btn btn-primary">Buat Penetapan Pertama</a>
    </div>
  </div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Judul Penetapan</th>
          <th>Tahun Ajaran</th>
          <th>Status</th>
          <th>Progres Pelaksanaan</th>
          <th>Dibuat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($penetapans as $i => $p): ?>
        <tr>
          <td class="td-muted"><?= $i + 1 ?></td>
          <td>
            <div style="font-weight:600;"><?= htmlspecialchars($p['judul']) ?></div>
            <?php if ($p['deskripsi']): ?>
            <div class="td-muted" style="font-size:12px;max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
              <?= htmlspecialchars($p['deskripsi']) ?>
            </div>
            <?php endif; ?>
          </td>
          <td><span class="badge badge-info"><?= htmlspecialchars($p['tahun_ajaran_nama']) ?></span></td>
          <td>
            <div style="display:flex;flex-direction:column;gap:4px;">
              <span class="badge <?= $p['status'] === 'final' ? 'badge-success' : 'badge-warning' ?> badge-dot">
                <?= $p['status'] === 'final' ? 'Final' : 'Draft' ?>
              </span>
              <?php if (($p['visibility_status'] ?? '') === 'nonaktif'): ?>
              <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:10px;background:#fef08a;color:#854d0e;display:inline-block;">Nonaktif</span>
              <?php elseif (($p['visibility_status'] ?? '') === 'hidden'): ?>
              <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:10px;background:#e2e8f0;color:#475569;display:inline-block;">Hide</span>
              <?php endif; ?>
            </div>
          </td>
          <td>
            <?php if (isset($p['pelaksanaan_progress'])): ?>
              <div class="progress" style="height:20px;background:#e2e8f0;border-radius:4px;overflow:hidden;">
                <div style="width:<?= $p['pelaksanaan_progress'] ?>%;background:linear-gradient(90deg,#1a237e,#3f51b5);height:100%;text-align:center;color:white;font-size:12px;line-height:20px;">
                  <?= $p['pelaksanaan_progress'] ?>%
                </div>
              </div>
            <?php else: ?>
              <span class="text-muted">0%</span>
            <?php endif; ?>
          </td>
          <td class="td-muted"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
          <td>
            <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
              <a href="<?= BASE_URL ?>/penetapan/<?= $p['id'] ?>" class="btn btn-outline btn-sm">Detail</a>
              <a href="<?= BASE_URL ?>/penetapan/<?= $p['id'] ?>/edit-step2" class="btn btn-primary btn-sm">
                <?= $p['status'] === 'final' ? 'Edit' : 'Lanjut Draft' ?>
              </a>
              <?php if (($p['visibility_status'] ?? 'aktif') === 'aktif'): ?>
              <form method="POST" action="<?= BASE_URL ?>/penetapan/<?= $p['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="nonaktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/penetapan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:#854d0e;border-color:#fde047;background:#fffbeb;font-size:11px;" title="Nonaktifkan dokumen">
                  Nonaktifkan
                </button>
              </form>
              <?php elseif (($p['visibility_status'] ?? '') === 'nonaktif'): ?>
              <form method="POST" action="<?= BASE_URL ?>/penetapan/<?= $p['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="aktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/penetapan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:#059669;border-color:#a7f3d0;background:#ecfdf5;font-size:11px;" title="Aktifkan kembali">
                  Aktifkan
                </button>
              </form>
              <form method="POST" action="<?= BASE_URL ?>/penetapan/<?= $p['id'] ?>/toggle-status" style="margin:0;" onsubmit="return confirm('Sembunyikan (Hide) penetapan ini?')">
                <input type="hidden" name="visibility_status" value="hidden">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/penetapan">
                <button type="submit" class="btn btn-outline btn-sm" style="color:#64748b;font-size:11px;" title="Hide dokumen">
                  Hide
                </button>
              </form>
              <?php else: ?>
              <form method="POST" action="<?= BASE_URL ?>/penetapan/<?= $p['id'] ?>/toggle-status" style="margin:0;">
                <input type="hidden" name="visibility_status" value="aktif">
                <input type="hidden" name="return_url" value="<?= BASE_URL ?>/penetapan">
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

<!-- Info Box -->
<div class="card mt-4" style="border-color:rgba(37,99,168,0.2);background:rgba(37,99,168,0.03);">
  <div class="card-body" style="padding:20px 24px;">
    <div style="display:flex;gap:14px;align-items:flex-start;">
      <div style="flex-shrink:0;color:var(--primary-light);">
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
      </div>
      <div>
        <p style="font-size:14px;font-weight:600;color:var(--primary);margin-bottom:6px;">Panduan Penetapan</p>
        <p style="font-size:13px;color:var(--text-muted);line-height:1.7;">
          <strong>Langkah 1:</strong> Konfigurasi kriteria prodi di menu <a href="<?= BASE_URL ?>/penetapan/kriteria">Kelola Kriteria</a>. <br>
          <strong>Langkah 2:</strong> Upload dokumen referensi (notulensi, evaluasi, dll) di <a href="<?= BASE_URL ?>/penetapan/referensi">Referensi & AI</a> untuk dianalisis. <br>
          <strong>Langkah 3:</strong> Buat penetapan baru dan isi target capaian per kriteria, gunakan hasil analisis AI sebagai acuan.
        </p>
      </div>
    </div>
  </div>
</div>
