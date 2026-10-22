<?php
$pageTitle   = 'Dashboard';
$breadcrumbs = [['label' => 'Dashboard']];
?>

<!-- Greeting & Academic Context Banner -->
<div class="card mb-4" style="padding:20px 24px;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:16px;">
      <div style="width:48px;height:48px;border-radius:var(--radius);background:var(--primary-subtle);border:1px solid #C9DBF0;display:flex;align-items:center;justify-content:center;color:var(--primary);flex-shrink:0;">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
        </svg>
      </div>
      <div>
        <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.04em;">Program Studi</div>
        <h2 style="font-size:18px;font-weight:700;color:var(--text-main);margin:2px 0 1px;">
          <?= htmlspecialchars($user['nama_prodi']) ?>
        </h2>
        <p style="font-size:12.5px;color:var(--text-muted);margin:0;">Sistem Informasi Penjaminan Mutu Internal (SIPMI) &bull; Siklus PPEPP</p>
      </div>
    </div>
    <?php if (!empty($tahunAktif) && is_array($tahunAktif) && !empty($tahunAktif['nama'])): ?>
    <div style="display:inline-flex;align-items:center;gap:8px;background:var(--surface-hover);border:1px solid var(--border);padding:8px 14px;border-radius:var(--radius-sm);font-size:12.5px;font-weight:600;color:var(--text-main);">
      <svg width="15" height="15" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24">
        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      Tahun Ajaran Aktif: <span style="color:var(--primary);"><?= htmlspecialchars($tahunAktif['nama']) ?></span>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
      </svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['kriteria'] ?></div>
      <div class="stat-label">Kriteria Mutu Aktif</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon gold">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
        <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
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
      <div class="stat-label">Penetapan Final</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon red">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['draft'] ?></div>
      <div class="stat-label">Penetapan Draft</div>
    </div>
  </div>
</div>

<!-- Jira / Linear Style Attention Card: Incomplete / Draft Work -->
<?php if (!empty($incompleteItems)): ?>
<div class="card mb-4" style="border-left:4px solid var(--warning);background:var(--warning-bg);border-color:var(--warning-border);">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding:16px 20px;">
    <div style="display:flex;align-items:center;gap:14px;">
      <div style="width:36px;height:36px;background:var(--warning);color:#fff;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
          <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
      </div>
      <div>
        <div style="display:flex;align-items:center;gap:8px;">
          <h3 style="font-size:14px;font-weight:700;color:var(--warning-text);margin:0;">Dokumen &amp; Tahapan Membutuhkan Tindakan</h3>
          <span class="badge badge-warning">
            <?= count($incompleteItems) ?> item
          </span>
        </div>
        <p style="font-size:12.5px;color:var(--text-muted);margin:2px 0 0;">Terdapat dokumen penjaminan mutu yang masih berstatus draft atau belum dilengkapi.</p>
      </div>
    </div>
    <a href="<?= BASE_URL ?>/pending" class="btn btn-outline btn-sm" style="border-color:var(--warning);color:var(--warning-text);background:#fff;">
      Tinjau Dokumen Belum Selesai &rarr;
    </a>
  </div>
</div>
<?php endif; ?>

<!-- Content Grid: Recent Penetapan & PPEPP Projects Overview -->
<div class="grid-2 mb-4">
  <!-- Penetapan Terbaru -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
        </svg>
        Penetapan Terbaru
      </h3>
      <a href="<?= BASE_URL ?>/penetapan" class="btn btn-outline btn-sm">Lihat Semua</a>
    </div>
    <div class="card-body" style="padding:0;">
      <?php if (empty($recentPenetapan)): ?>
      <div class="empty-state">
        <div class="empty-icon">
          <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
          </svg>
        </div>
        <h3>Belum Ada Dokumen Penetapan</h3>
        <p>Mulai siklus mutu dengan membuat penetapan standar pertama.</p>
        <a href="<?= BASE_URL ?>/penetapan/create" class="btn btn-primary btn-sm">Buat Penetapan Baru</a>
      </div>
      <?php else: ?>
      <div class="table-wrap" style="border:none;border-radius:0;">
        <table>
          <thead>
            <tr>
              <th>Judul Penetapan</th>
              <th style="width:110px;">Status</th>
              <th style="width:90px;text-align:right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentPenetapan as $p): ?>
            <tr>
              <td>
                <div style="font-weight:600;font-size:13.5px;color:var(--text-main);"><?= htmlspecialchars($p['judul']) ?></div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:2px;"><?= htmlspecialchars($p['tahun_ajaran_nama']) ?></div>
              </td>
              <td>
                <span class="badge <?= $p['status'] === 'final' ? 'badge-success' : 'badge-warning' ?> badge-dot">
                  <?= ucfirst($p['status']) ?>
                </span>
              </td>
              <td style="text-align:right;">
                <a href="<?= BASE_URL ?>/penetapan/<?= $p['id'] ?>" class="btn btn-outline btn-sm">Buka</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Siklus PPEPP Projects Overview -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
        </svg>
        Progress Siklus PPEPP
      </h3>
      <a href="<?= BASE_URL ?>/ppepp" class="btn btn-primary btn-sm">Kelola Project</a>
    </div>
    <div class="card-body">
      <?php
      $ppDb = $db;
      $ppStmt = $ppDb->prepare("SELECT proj.*, ta.nama AS ta_nama
                                FROM ppepp_project proj
                                JOIN tahun_ajaran ta ON ta.id = proj.tahun_ajaran_id
                                WHERE proj.user_id = ?
                                ORDER BY ta.nama DESC
                                LIMIT 3");
      $ppStmt->execute([$user['id']]);
      $ppProjects = $ppStmt->fetchAll(PDO::FETCH_ASSOC);
      ?>
      <?php if (empty($ppProjects)): ?>
      <div class="empty-state">
        <div class="empty-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
          </svg>
        </div>
        <h3>Belum Ada Project PPEPP</h3>
        <p>Inisiasi project per tahun ajaran untuk memantau 5 tahapan PPEPP.</p>
        <a href="<?= BASE_URL ?>/ppepp" class="btn btn-primary btn-sm">+ Buat Project PPEPP</a>
      </div>
      <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:12px;">
        <?php foreach ($ppProjects as $proj):
          $ppStepNames = ['penetapan','pelaksanaan','evaluasi','pengendalian','peningkatan'];
          $stepsStarted = 0;
          foreach ($ppStepNames as $ppStep) {
            $ppCount = $ppDb->prepare("SELECT COUNT(*) FROM `$ppStep` WHERE ppepp_project_id = ? AND user_id = ?");
            $ppCount->execute([$proj['id'], $user['id']]);
            if ((int)$ppCount->fetchColumn() > 0) $stepsStarted++;
          }
          $pct = (int)(($stepsStarted / 5) * 100);
        ?>
        <div style="padding:14px 16px;border-radius:var(--radius);background:var(--surface);border:1px solid var(--border);display:flex;align-items:center;gap:14px;">
          <div style="width:38px;height:38px;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;background:var(--primary-subtle);border:1px solid #C9DBF0;color:var(--primary);flex-shrink:0;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18">
              <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
            </svg>
          </div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:13.5px;font-weight:600;color:var(--text-main);"><?= htmlspecialchars($proj['ta_nama']) ?></div>
            <div style="display:flex;align-items:center;gap:8px;margin-top:6px;">
              <div style="flex:1;height:6px;background:var(--border-subtle);border-radius:var(--radius-pill);overflow:hidden;">
                <div style="height:100%;width:<?= $pct ?>%;background:var(--primary);border-radius:var(--radius-pill);"></div>
              </div>
              <span style="font-size:11.5px;font-weight:700;color:var(--text-muted);"><?= $stepsStarted ?>/5 Tahap</span>
            </div>
          </div>
          <a href="<?= BASE_URL ?>/ppepp/<?= $proj['id'] ?>" class="btn btn-outline btn-sm">Buka</a>
        </div>
        <?php endforeach; ?>
        <a href="<?= BASE_URL ?>/ppepp" style="font-size:12.5px;font-weight:600;text-align:center;color:var(--primary);text-decoration:none;padding:6px;">
          Lihat Semua Project PPEPP &rarr;
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Seksi Kelola Kriteria Program Studi -->
<div class="card" id="seksiKriteria">
  <div class="card-header">
    <div>
      <h3 class="card-title">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
        </svg>
        Kelola Kriteria Mutu Program Studi
      </h3>
      <p class="card-desc">
        Daftar acuan standar yang berlaku untuk Program Studi <strong><?= htmlspecialchars($user['nama_prodi']) ?></strong>
      </p>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      <span class="badge badge-info"><?= count($kriteria ?? []) ?> Kriteria</span>
      <button type="button" class="btn btn-primary btn-sm" data-modal-open="modalTambahKriteria">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Tambah Kriteria
      </button>
    </div>
  </div>

  <?php if (empty($kriteria)): ?>
  <div class="empty-state">
    <div class="empty-icon">
      <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
      </svg>
    </div>
    <h3>Belum Ada Kriteria Mutu</h3>
    <p>Tambahkan kriteria akreditasi/standar mutu pertama untuk prodi Anda.</p>
    <button type="button" class="btn btn-primary btn-sm" data-modal-open="modalTambahKriteria">Tambah Kriteria Pertama</button>
  </div>
  <?php else: ?>
  <div class="table-wrap" style="border:none;border-radius:0;">
    <table>
      <thead>
        <tr>
          <th style="width:70px;text-align:center;">Urutan</th>
          <th style="width:90px;">Kode</th>
          <th>Nama Kriteria</th>
          <th>Deskripsi</th>
          <th style="width:100px;">Status</th>
          <th style="width:160px;text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($kriteria as $k): ?>
        <tr>
          <td class="td-muted" style="text-align:center;font-weight:600;"><?= $k['urutan'] ?: '-' ?></td>
          <td>
            <?php if (!empty($k['kode'])): ?>
            <span class="badge badge-neutral" style="font-weight:700;">
              <?= htmlspecialchars($k['kode']) ?>
            </span>
            <?php else: ?>
            <span class="td-muted" style="font-style:italic;">(—)</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-weight:600;color:var(--text-main);"><?= htmlspecialchars($k['nama']) ?></div>
          </td>
          <td class="td-muted" style="max-width:320px;">
            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:12.5px;">
              <?= $k['deskripsi'] ? htmlspecialchars($k['deskripsi']) : '<span style="color:var(--text-subtle);">&mdash;</span>' ?>
            </div>
          </td>
          <td>
            <?php if ($k['aktif']): ?>
            <span class="badge badge-success badge-dot">Aktif</span>
            <?php else: ?>
            <span class="badge badge-neutral badge-dot">Nonaktif</span>
            <?php endif; ?>
          </td>
          <td style="text-align:right;">
            <div style="display:inline-flex;gap:6px;">
              <button type="button" class="btn btn-outline btn-sm"
                data-edit-kriteria
                data-id="<?= $k['id'] ?>"
                data-kode="<?= htmlspecialchars($k['kode'], ENT_QUOTES) ?>"
                data-nama="<?= htmlspecialchars($k['nama'], ENT_QUOTES) ?>"
                data-deskripsi="<?= htmlspecialchars($k['deskripsi'] ?? '', ENT_QUOTES) ?>"
                data-urutan="<?= $k['urutan'] ?>">
                Edit
              </button>
              <?php if ($k['aktif']): ?>
              <button type="button" class="btn btn-danger btn-sm"
                data-delete-kriteria="<?= $k['id'] ?>"
                data-nama="<?= htmlspecialchars($k['nama'], ENT_QUOTES) ?>">
                Nonaktifkan
              </button>
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

<!-- Modal: Tambah Kriteria -->
<div class="modal-overlay" id="modalTambahKriteria">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Tambah Kriteria Mutu Baru</h3>
      <button type="button" class="modal-close-btn" data-modal-close="modalTambahKriteria">&times;</button>
    </div>
    <form action="<?= BASE_URL ?>/penetapan/kriteria/save" method="POST">
      <input type="hidden" name="id" value="0">
      <div class="modal-body">
        <div class="grid-2 mb-3">
          <div class="form-group">
            <label for="addKode">Kode Kriteria <span class="required-star">*</span></label>
            <input type="text" id="addKode" name="kode" class="form-control"
                   placeholder="Contoh: C1, K1, STD-01" value="" maxlength="20" required>
          </div>
          <div class="form-group">
            <label for="addUrutan">Urutan Tampil</label>
            <input type="number" id="addUrutan" name="urutan" class="form-control"
                   placeholder="1, 2, 3 ..." min="0" value="<?= count($kriteria ?? []) + 1 ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="addNama">Nama Kriteria <span class="required-star">*</span></label>
          <input type="text" id="addNama" name="nama" class="form-control"
                 placeholder="Contoh: Tata Pamong & Tata Kelola" required>
        </div>
        <div class="form-group">
          <label for="addDeskripsi">Deskripsi</label>
          <textarea id="addDeskripsi" name="deskripsi" class="form-control"
                    placeholder="Deskripsi ruang lingkup kriteria..." rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close="modalTambahKriteria">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Kriteria</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Edit Kriteria -->
<div class="modal-overlay" id="modalEditKriteria">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Edit Kriteria Mutu</h3>
      <button type="button" class="modal-close-btn" data-modal-close="modalEditKriteria">&times;</button>
    </div>
    <form action="<?= BASE_URL ?>/penetapan/kriteria/save" method="POST" id="formEditKriteria">
      <input type="hidden" name="id" value="">
      <div class="modal-body">
        <div class="grid-2 mb-3">
          <div class="form-group">
            <label for="editKode">Kode Kriteria <span class="required-star">*</span></label>
            <input type="text" id="editKode" name="kode" class="form-control" maxlength="20" required>
          </div>
          <div class="form-group">
            <label for="editUrutan">Urutan Tampil</label>
            <input type="number" id="editUrutan" name="urutan" class="form-control" min="0">
          </div>
        </div>
        <div class="form-group">
          <label for="editNama">Nama Kriteria <span class="required-star">*</span></label>
          <input type="text" id="editNama" name="nama" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="editDeskripsi">Deskripsi</label>
          <textarea id="editDeskripsi" name="deskripsi" class="form-control" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close="modalEditKriteria">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Konfirmasi Nonaktifkan Kriteria -->
<div class="modal-overlay" id="modalDeleteKriteria">
  <div class="modal" style="max-width:440px;">
    <div class="modal-header">
      <h3 class="modal-title" style="color:var(--danger);">Nonaktifkan Kriteria</h3>
      <button type="button" class="modal-close-btn" data-modal-close="modalDeleteKriteria">&times;</button>
    </div>
    <div class="modal-body">
      <p style="font-size:13.5px;color:var(--text-main);">
        Apakah Anda yakin ingin menonaktifkan kriteria <strong id="deleteKriteriaName"></strong>?
      </p>
      <p style="font-size:12.5px;color:var(--text-muted);margin-top:8px;">
        Kriteria ini tidak akan muncul pada pilihan pembuatan penetapan baru, namun data arsip sebelumnya tetap aman.
      </p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline" data-modal-close="modalDeleteKriteria">Batal</button>
      <form action="<?= BASE_URL ?>/penetapan/kriteria/delete" method="POST" style="display:inline;">
        <input type="hidden" name="id" id="deleteKriteriaId">
        <button type="submit" class="btn btn-danger">Ya, Nonaktifkan</button>
      </form>
    </div>
  </div>
</div>
