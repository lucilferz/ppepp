<?php
$pageTitle   = 'Dashboard';
$breadcrumbs = [['label' => 'Dashboard']];
?>

<!-- Greeting Banner -->
<div style="background:#fff;border:1.5px solid var(--border);border-radius:var(--radius);padding:24px 28px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;gap:20px;box-shadow:0 1px 4px rgba(26,35,126,0.05);">
  <div style="display:flex;align-items:center;gap:18px;">
    <div style="width:52px;height:52px;border-radius:14px;background:var(--bg);border:1.5px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="26" height="26" fill="none" stroke="var(--primary-light)" stroke-width="1.8" viewBox="0 0 24 24">
        <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
      </svg>
    </div>
    <div>
      <p style="font-size:12px;color:var(--text-muted);margin-bottom:3px;font-weight:500;">Selamat datang</p>
      <h2 style="font-size:18px;font-weight:800;color:var(--text-main);margin-bottom:2px;letter-spacing:-0.3px;">
        <?= htmlspecialchars($user['nama_prodi']) ?>
      </h2>
      <p style="font-size:13px;color:var(--text-muted);">Platform PPEPP — Penyusunan Laporan Evaluasi Diri (LED)</p>
    </div>
  </div>
  <?php if (!empty($tahunAktif) && is_array($tahunAktif) && !empty($tahunAktif['nama'])): ?>
  <div style="display:inline-flex;align-items:center;gap:7px;background:var(--bg);border:1.5px solid var(--border);padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;color:var(--text-main);flex-shrink:0;">
    <svg width="14" height="14" fill="none" stroke="var(--primary-light)" stroke-width="2" viewBox="0 0 24 24">
      <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
    </svg>
    <?= htmlspecialchars($tahunAktif['nama']) ?>
  </div>
  <?php endif; ?>
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
      <div class="stat-label">Kriteria Aktif</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon gold">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
        <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
        <polyline points="10 9 9 9 8 9"/>
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

<!-- BANNER SIKLUS BELUM DIKERJAKAN -->
<div class="card" style="border-color:#fca5a5;background:#fef2f2;margin-bottom:20px;">
  <div class="card-body" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding:18px 20px;">
    <div style="display:flex;align-items:center;gap:14px;">
      <div style="width:40px;height:40px;background:#ef4444;color:#fff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="20" height="20" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
          <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
      </div>
      <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
          <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">Dokumen &amp; Tahap Belum Dikerjakan</h3>
          <span class="badge" style="background:#ef4444;color:#fff;font-weight:700;font-size:11px;padding:2px 8px;border-radius:20px;">
            <?= count($incompleteItems ?? []) ?> item
          </span>
        </div>
        <p style="font-size:12.5px;color:#64748b;margin:0;">Halaman khusus untuk memfilter dan langsung mengerjakan dokumen yang masih kosong atau draft.</p>
      </div>
    </div>
    <a href="<?= BASE_URL ?>/pending" class="btn btn-danger btn-sm" style="white-space:nowrap;">
      Lihat Dokumen Belum Dikerjakan
    </a>
  </div>
</div>

<!-- Content Grid -->
<div class="grid-2" style="gap:24px;">
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
      <div class="empty-state" style="padding:40px 24px;">
        <div class="empty-icon">
          <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
          </svg>
        </div>
        <h3>Belum ada penetapan</h3>
        <p>Buat penetapan pertama untuk tahun ajaran ini</p>
        <a href="<?= BASE_URL ?>/penetapan/create" class="btn btn-primary btn-sm">Buat Sekarang</a>
      </div>
      <?php else: ?>
      <div class="table-wrap">
        <table>
          <tbody>
            <?php foreach ($recentPenetapan as $p): ?>
            <tr>
              <td>
                <div style="font-weight:600;font-size:14px;"><?= htmlspecialchars($p['judul']) ?></div>
                <div style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($p['tahun_ajaran_nama']) ?></div>
              </td>
              <td>
                <span class="badge <?= $p['status'] === 'final' ? 'badge-success' : 'badge-warning' ?> badge-dot">
                  <?= ucfirst($p['status']) ?>
                </span>
              </td>
              <td>
                <a href="<?= BASE_URL ?>/penetapan/<?= $p['id'] ?>" class="btn btn-outline btn-sm">Detail</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Siklus PPEPP - Project Based -->
  <div class="card">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
      <h3 class="card-title">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
        </svg>
        Siklus PPEPP
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
      <div style="text-align:center;padding:32px 16px;">
        <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:14px;">Belum ada project PPEPP. Buat project untuk memulai siklus penjaminan mutu.</p>
        <a href="<?= BASE_URL ?>/ppepp" class="btn btn-primary">+ Buat Project PPEPP</a>
      </div>
      <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:10px;">
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
        <div style="display:flex;align-items:center;gap:14px;padding:12px 14px;border-radius:10px;background:#f8faff;border:1.5px solid #e2e8f0;">
          <div style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#1a237e,#3f51b5);flex-shrink:0;">
            <svg fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24" width="18" height="18">
              <path d="M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
            </svg>
          </div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:14px;font-weight:700;color:var(--text-main);"><?= htmlspecialchars($proj['ta_nama']) ?></div>
            <div style="display:flex;align-items:center;gap:8px;margin-top:5px;">
              <div style="flex:1;height:5px;background:#e2e8f0;border-radius:99px;overflow:hidden;">
                <div style="height:100%;width:<?= $pct ?>%;background:linear-gradient(90deg,#1a237e,#3f51b5);border-radius:99px;"></div>
              </div>
              <span style="font-size:11px;font-weight:700;color:#64748b;"><?= $stepsStarted ?>/5</span>
            </div>
          </div>
          <a href="<?= BASE_URL ?>/ppepp/<?= $proj['id'] ?>" class="btn btn-outline btn-sm">Buka</a>
        </div>
        <?php endforeach; ?>
        <a href="<?= BASE_URL ?>/ppepp" style="font-size:12px;text-align:center;color:var(--primary);text-decoration:none;padding:6px;">
          Lihat semua project →
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>



<!-- ============================================================

     SEKSI KELOLA KRITERIA PROGRAM STUDI (CRUD KRITERIA)
     ============================================================ -->
<div class="card mt-4" id="seksiKriteria">
  <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
      <h3 class="card-title" style="display:flex;align-items:center;gap:8px;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
          <path d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
        </svg>
        Kelola Kriteria Program Studi
      </h3>
      <p style="font-size:12px;color:var(--text-muted);margin-top:2px;">
        Daftar kriteria yang digunakan sebagai standar penetapan & pelaksanaan di <strong><?= htmlspecialchars($user['nama_prodi']) ?></strong>
      </p>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      <span class="badge badge-primary" style="font-weight:700;"><?= count($kriteria ?? []) ?> kriteria</span>
      <button type="button" class="btn btn-primary btn-sm" data-modal-open="modalTambahKriteria">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        + Tambah Kriteria
      </button>
    </div>
  </div>

  <?php if (empty($kriteria)): ?>
  <div class="empty-state" style="padding:40px 20px;">
    <div class="empty-icon">
      <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
      </svg>
    </div>
    <h3>Belum ada kriteria</h3>
    <p>Tambahkan kriteria pertama untuk prodi Anda</p>
    <button type="button" class="btn btn-primary" data-modal-open="modalTambahKriteria">Tambah Kriteria Pertama</button>
  </div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:70px;text-align:center;">Urutan</th>
          <th style="width:80px;">Kode</th>
          <th>Nama Kriteria</th>
          <th>Deskripsi</th>
          <th style="width:100px;">Status</th>
          <th style="width:170px;text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($kriteria as $k): ?>
        <tr>
          <td class="td-muted" style="text-align:center;font-weight:600;"><?= $k['urutan'] ?: '-' ?></td>
          <td>
            <?php if (!empty($k['kode'])): ?>
            <span style="display:inline-flex;align-items:center;justify-content:center;padding:4px 10px;background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff;border-radius:8px;font-weight:800;font-size:12px;">
              <?= htmlspecialchars($k['kode']) ?>
            </span>
            <?php else: ?>
            <span style="color:#94a3b8;font-size:12px;font-style:italic;">(Diisi saat penetapan)</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-weight:600;color:var(--text-main);"><?= htmlspecialchars($k['nama']) ?></div>
          </td>
          <td class="td-muted" style="max-width:320px;">
            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:13px;">
              <?= $k['deskripsi'] ? htmlspecialchars($k['deskripsi']) : '<span style="color:#cbd5e1;">—</span>' ?>
            </div>
          </td>
          <td>
            <?php if ($k['aktif']): ?>
            <span class="badge badge-success badge-dot">Aktif</span>
            <?php else: ?>
            <span class="badge badge-gray badge-dot">Nonaktif</span>
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
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13">
                  <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                  <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit
              </button>
              <?php if ($k['aktif']): ?>
              <button type="button" class="btn btn-danger btn-sm"
                data-delete-kriteria="<?= $k['id'] ?>"
                data-nama="<?= htmlspecialchars($k['nama'], ENT_QUOTES) ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13">
                  <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
                </svg>
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

<!-- ============================================================
     MODAL: Tambah Kriteria
     ============================================================ -->
<div class="modal-overlay" id="modalTambahKriteria">
  <div class="modal">
    <div class="modal-header">
      <h3>
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Tambah Kriteria Baru
      </h3>
      <button type="button" class="modal-close" data-modal-close="modalTambahKriteria">✕</button>
    </div>
    <form action="<?= BASE_URL ?>/penetapan/kriteria/save" method="POST">
      <input type="hidden" name="id" value="0">
      <div class="modal-body">
        <div class="grid-2 mb-3">
          <div class="form-group" style="margin-bottom:0;">
            <label for="addKode">Kode Kriteria *</label>
            <input type="text" id="addKode" name="kode" class="form-control"
                   placeholder="Contoh: C1, K1, STD-01" value="" maxlength="20" required>
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label for="addUrutan">Urutan</label>
            <input type="number" id="addUrutan" name="urutan" class="form-control"
                   placeholder="1, 2, 3 ..." min="0" value="<?= count($kriteria ?? []) + 1 ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="addNama">Nama Kriteria *</label>
          <input type="text" id="addNama" name="nama" class="form-control"
                 placeholder="Contoh: Tata Pamong & Tata Kelola" required>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label for="addDeskripsi">Deskripsi</label>
          <textarea id="addDeskripsi" name="deskripsi" class="form-control"
                    placeholder="Deskripsi singkat tentang kriteria ini..." rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close="modalTambahKriteria">Batal</button>
        <button type="submit" class="btn btn-primary">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
          </svg>
          Simpan Kriteria
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================================
     MODAL: Edit Kriteria
     ============================================================ -->
<div class="modal-overlay" id="modalEditKriteria">
  <div class="modal">
    <div class="modal-header">
      <h3>
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
          <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Edit Kriteria
      </h3>
      <button type="button" class="modal-close" data-modal-close="modalEditKriteria">✕</button>
    </div>
    <form action="<?= BASE_URL ?>/penetapan/kriteria/save" method="POST" id="formEditKriteria">
      <input type="hidden" name="id" value="">
      <div class="modal-body">
        <div class="grid-2 mb-3">
          <div class="form-group" style="margin-bottom:0;">
            <label for="editKode">Kode Kriteria *</label>
            <input type="text" id="editKode" name="kode" class="form-control" maxlength="20" required>
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label for="editUrutan">Urutan</label>
            <input type="number" id="editUrutan" name="urutan" class="form-control" min="0">
          </div>
        </div>
        <div class="form-group">
          <label for="editNama">Nama Kriteria *</label>
          <input type="text" id="editNama" name="nama" class="form-control" required>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label for="editDeskripsi">Deskripsi</label>
          <textarea id="editDeskripsi" name="deskripsi" class="form-control" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close="modalEditKriteria">Batal</button>
        <button type="submit" class="btn btn-primary">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
          </svg>
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================================
     MODAL: Konfirmasi Hapus/Nonaktifkan
     ============================================================ -->
<div class="modal-overlay" id="modalDeleteKriteria">
  <div class="modal" style="max-width:420px;">
    <div class="modal-header">
      <h3 style="color:var(--danger);">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
          <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        Nonaktifkan Kriteria
      </h3>
      <button type="button" class="modal-close" data-modal-close="modalDeleteKriteria">✕</button>
    </div>
    <div class="modal-body">
      <p style="font-size:14px;color:var(--text-main);">
        Apakah Anda yakin ingin menonaktifkan kriteria
        <strong id="deleteKriteriaName"></strong>?
      </p>
      <p style="font-size:13px;color:var(--text-muted);margin-top:10px;">
        Kriteria yang dinonaktifkan tidak akan muncul pada pilihan form penetapan baru, namun data historis tetap tersimpan dengan aman.
      </p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline" data-modal-close="modalDeleteKriteria">Batal</button>
      <form action="<?= BASE_URL ?>/penetapan/kriteria/delete" method="POST" style="display:inline;">
        <input type="hidden" name="id" id="deleteKriteriaId">
        <button type="submit" class="btn btn-danger">Nonaktifkan</button>
      </form>
    </div>
  </div>
</div>
