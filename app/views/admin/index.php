<?php
$pageTitle   = 'Halaman Administrasi — Manajemen Pengguna & Prodi';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Halaman Admin'],
];

$roleBadges = [
  'dekan'   => ['class' => 'badge-warning', 'label' => 'Dekan'],
  'kaprodi' => ['class' => 'badge-info', 'label' => 'Kaprodi'],
  'dosen'   => ['class' => 'badge-neutral', 'label' => 'Dosen'],
];

$jenjangLabel = [
  'D3' => 'Diploma 3 (D3)',
  'S1' => 'Sarjana (S1)',
  'S2' => 'Magister (S2)',
  'S3' => 'Doktor (S3)',
];
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h2>Halaman Administrasi</h2>
    <p class="page-desc">Kelola manajemen pengguna sistem (Dosen, Kaprodi, Dekan) dan konfigurasi Program Studi.</p>
  </div>

  <div class="page-actions">
    <a href="<?= BASE_URL ?>/users/create" class="btn btn-primary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="17" y1="11" x2="23" y2="11"/></svg>
      Tambah Pengguna
    </a>
    <a href="<?= BASE_URL ?>/prodi/create" class="btn btn-outline">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      Tambah Prodi
    </a>
  </div>
</div>

<!-- Ringkasan Statistik -->
<div class="stats-grid mb-4" style="grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)$totalUsers ?></div>
      <div class="stat-label">Total Pengguna Aktif</div>
      <div class="stat-sub"><?= (int)$totalDekan ?> Dekan • <?= (int)$totalKaprodi ?> Kaprodi • <?= (int)$totalDosen ?> Dosen</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon green">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)$totalProdi ?></div>
      <div class="stat-label">Program Studi Terdaftar</div>
      <div class="stat-sub">Fakultas Ilmu Komputer</div>
    </div>
  </div>
</div>

<!-- Segmented Tabs Navigation -->
<div class="segmented-control mb-4" style="max-width:440px;">
  <button type="button" id="tabBtnUsers" class="seg-item active" onclick="switchAdminTab('users')">
    Manajemen Pengguna (<?= (int)$totalUsers ?>)
  </button>
  <button type="button" id="tabBtnProdi" class="seg-item" onclick="switchAdminTab('prodi')">
    Program Studi (<?= (int)$totalProdi ?>)
  </button>
</div>

<!-- TAB 1: MANAJEMEN PENGGUNA -->
<div id="tabContentUsers">
  <div class="card">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <h3 class="card-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        Daftar Pengguna Sistem
      </h3>
      <div style="width:260px;">
        <input type="text" id="userSearchInput" placeholder="Cari nama, username, email..." onkeyup="filterUsersTable()"
               class="form-control" style="font-size:12.5px;padding:6px 12px;">
      </div>
    </div>

    <div class="table-wrap">
      <table id="usersTable">
        <thead>
          <tr>
            <th style="width:40px;">#</th>
            <th>Pengguna</th>
            <th>Role</th>
            <th>Program Studi</th>
            <th>Status API Key AI</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $i => $u):
            $badge = $roleBadges[$u['role']] ?? $roleBadges['dosen'];
            $hasKey = !empty($u['gemini_api_key']);
          ?>
          <tr class="user-row">
            <td class="td-muted"><?= $i + 1 ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:10px;">
                <?php if (!empty($u['avatar_url'])): ?>
                <img src="<?= htmlspecialchars($u['avatar_url']) ?>" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:1px solid var(--border);">
                <?php else: ?>
                <div style="width:32px;height:32px;border-radius:50%;background:var(--bg-main);color:var(--text-secondary);font-weight:700;font-size:12px;display:flex;align-items:center;justify-content:center;border:1px solid var(--border);">
                  <?= strtoupper(mb_substr($u['nama_lengkap'] ?: $u['username'], 0, 1)) ?>
                </div>
                <?php endif; ?>
                <div>
                  <div style="font-weight:600;color:var(--text-primary);font-size:13.5px;"><?= htmlspecialchars($u['nama_lengkap']) ?></div>
                  <div class="td-muted" style="font-size:11.5px;">
                    @<?= htmlspecialchars($u['username']) ?> &nbsp;•&nbsp; <?= htmlspecialchars($u['email']) ?>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <span class="badge <?= $badge['class'] ?>">
                <?= $badge['label'] ?>
              </span>
            </td>
            <td>
              <span style="font-weight:600;color:var(--text-primary);">
                <?= htmlspecialchars($u['prodi_nama'] ?: 'Semua / Fakultas') ?>
              </span>
              <?php if (!empty($u['prodi_kode'])): ?>
              <span class="badge badge-neutral" style="font-size:10px;margin-left:4px;">
                <?= htmlspecialchars($u['prodi_kode']) ?>
              </span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($hasKey): ?>
              <span class="badge badge-success badge-dot">
                Aktif (<?= htmlspecialchars($u['gemini_model'] ?: 'gemini-1.5-flash') ?>)
              </span>
              <?php else: ?>
              <span class="td-muted" style="font-size:12px;font-style:italic;">Belum terpasang</span>
              <?php endif; ?>
            </td>
            <td>
              <div style="display:flex;justify-content:flex-end;gap:6px;align-items:center;">
                <?php if ($u['id'] != $currentUser['id']): ?>
                <form action="<?= BASE_URL ?>/users/<?= $u['id'] ?>/login-as" method="POST" style="margin:0;">
                  <button type="submit" class="btn btn-sm" style="display:inline-flex;align-items:center;gap:4px;background:#4f46e5;color:#fff;border:none;font-weight:700;padding:5px 10px;border-radius:6px;cursor:pointer;" title="Login langsung ke akun <?= htmlspecialchars($u['nama_lengkap']) ?>">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Login
                  </button>
                </form>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/users/<?= $u['id'] ?>/edit" class="btn btn-sm btn-outline" title="Edit Pengguna">
                  Edit
                </a>
                <?php if ($u['id'] != $currentUser['id']): ?>
                <form action="<?= BASE_URL ?>/users/<?= $u['id'] ?>/delete" method="POST" style="margin:0;"
                      onsubmit="return confirm('Hapus user <?= htmlspecialchars(addslashes($u['nama_lengkap'])) ?>?')">
                  <button type="submit" class="btn btn-sm btn-danger" title="Hapus Pengguna">
                    Hapus
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
  </div>
</div>

<!-- TAB 2: MANAJEMEN PROGRAM STUDI -->
<div id="tabContentProdi" style="display:none;">
  <div class="card">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <h3 class="card-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        Daftar Program Studi
      </h3>
      <a href="<?= BASE_URL ?>/prodi/create" class="btn btn-primary btn-sm">
        + Tambah Prodi Baru
      </a>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:40px;">#</th>
            <th>Kode</th>
            <th>Nama Program Studi</th>
            <th>Jenjang</th>
            <th>Kaprodi</th>
            <th>Jumlah Dosen</th>
            <th>Status</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($prodis as $i => $pr): ?>
          <tr>
            <td class="td-muted"><?= $i + 1 ?></td>
            <td>
              <span class="badge badge-info font-mono" style="font-weight:700;">
                <?= htmlspecialchars($pr['kode']) ?>
              </span>
            </td>
            <td>
              <a href="<?= BASE_URL ?>/prodi/<?= $pr['id'] ?>" style="font-weight:600;color:var(--text-primary);text-decoration:none;">
                <?= htmlspecialchars($pr['nama']) ?>
              </a>
              <?php if (!empty($pr['deskripsi'])): ?>
              <div class="td-muted" style="font-size:11.5px;max-width:240px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                <?= htmlspecialchars($pr['deskripsi']) ?>
              </div>
              <?php endif; ?>
            </td>
            <td>
              <span style="font-weight:500;color:var(--text-secondary);">
                <?= $jenjangLabel[$pr['jenjang']] ?? $pr['jenjang'] ?>
              </span>
            </td>
            <td>
              <span style="font-weight:600;color:<?= !empty($pr['kaprodi_nama']) ? 'var(--text-primary)' : 'var(--text-muted)' ?>;">
                <?= htmlspecialchars($pr['kaprodi_nama'] ?: '— Belum Ditentukan —') ?>
              </span>
            </td>
            <td>
              <span class="badge badge-neutral">
                <?= (int)($pr['total_dosen'] ?? 0) ?> Dosen
              </span>
            </td>
            <td>
              <span class="badge <?= $pr['aktif'] ? 'badge-success' : 'badge-danger' ?> badge-dot">
                <?= $pr['aktif'] ? 'Aktif' : 'Nonaktif' ?>
              </span>
            </td>
            <td>
              <div style="display:flex;justify-content:flex-end;gap:6px;">
                <a href="<?= BASE_URL ?>/prodi/<?= $pr['id'] ?>" class="btn btn-sm btn-outline" title="Kelola Dosen & Detail">
                  Kelola
                </a>
                <a href="<?= BASE_URL ?>/prodi/<?= $pr['id'] ?>/edit" class="btn btn-sm btn-primary" title="Edit Prodi">
                  Edit
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
function switchAdminTab(tab) {
  var btnU = document.getElementById('tabBtnUsers');
  var btnP = document.getElementById('tabBtnProdi');
  var contentU = document.getElementById('tabContentUsers');
  var contentP = document.getElementById('tabContentProdi');

  if (tab === 'users') {
    contentU.style.display = 'block';
    contentP.style.display = 'none';
    btnU.classList.add('active');
    btnP.classList.remove('active');
  } else {
    contentU.style.display = 'none';
    contentP.style.display = 'block';
    btnP.classList.add('active');
    btnU.classList.remove('active');
  }
}

function filterUsersTable() {
  var input = document.getElementById('userSearchInput');
  var filter = (input ? input.value : '').toLowerCase();
  var rows = document.querySelectorAll('.user-row');

  rows.forEach(function(row) {
    var text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
}
</script>
