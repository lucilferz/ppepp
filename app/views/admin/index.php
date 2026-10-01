<?php
$pageTitle   = 'Halaman Administrasi — Manajemen Pengguna & Prodi';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Halaman Admin'],
];

$roleBadges = [
  'dekan'   => ['bg' => '#fef3c7', 'text' => '#b45309', 'label' => '👑 Dekan'],
  'kaprodi' => ['bg' => '#ede9fe', 'text' => '#6d28d9', 'label' => '🎓 Kaprodi'],
  'dosen'   => ['bg' => '#e0f2fe', 'text' => '#0369a1', 'label' => '🧑‍🏫 Dosen'],
];

$jenjangLabel = [
  'D3' => 'Diploma 3 (D3)',
  'S1' => 'Sarjana (S1)',
  'S2' => 'Magister (S2)',
  'S3' => 'Doktor (S3)',
];
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
  <div>
    <div style="display:flex;align-items:center;gap:10px;">
      <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(79,70,229,0.3);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/>
        </svg>
      </div>
      <div>
        <h2 style="font-size:22px;font-weight:900;color:#1e293b;margin:0;">Halaman Administrasi</h2>
        <p class="page-desc" style="font-size:13px;color:#64748b;margin:2px 0 0;">
          Kelola Manajemen Pengguna (Dosen, Kaprodi, Dekan) dan Program Studi Fakultas
        </p>
      </div>
    </div>
  </div>

  <div style="display:flex;gap:10px;">
    <a href="<?= BASE_URL ?>/users/create" class="btn btn-primary" style="background:#4f46e5;border:none;font-weight:800;font-size:13px;display:flex;align-items:center;gap:6px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="17" y1="11" x2="23" y2="11"/></svg>
      + Tambah Pengguna
    </a>
    <a href="<?= BASE_URL ?>/prodi/create" class="btn btn-outline" style="border-color:#4f46e5;color:#4f46e5;font-weight:800;font-size:13px;display:flex;align-items:center;gap:6px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      + Tambah Prodi
    </a>
  </div>
</div>

<!-- Ringkasan Statistik -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:28px;">
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:18px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Pengguna</div>
    <div style="font-size:30px;font-weight:900;color:#1e293b;margin-top:4px;"><?= $totalUsers ?></div>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">
      <?= $totalDekan ?> Dekan • <?= $totalKaprodi ?> Kaprodi • <?= $totalDosen ?> Dosen
    </div>
  </div>

  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:18px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Program Studi</div>
    <div style="font-size:30px;font-weight:900;color:#4f46e5;margin-top:4px;"><?= $totalProdi ?></div>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">Fakultas Ilmu Komputer</div>
  </div>
</div>

<!-- Tabs Navigation -->
<div style="display:flex;gap:10px;border-bottom:2px solid #e2e8f0;margin-bottom:24px;padding-bottom:2px;">
  <button type="button" id="tabBtnUsers" onclick="switchAdminTab('users')"
          style="padding:10px 20px;font-weight:800;font-size:14px;border:none;background:none;cursor:pointer;color:#4f46e5;border-bottom:3px solid #4f46e5;margin-bottom:-3px;display:flex;align-items:center;gap:8px;">
    <span>👥 Manajemen Pengguna</span>
    <span style="background:#eef2ff;color:#4f46e5;font-size:11.5px;padding:2px 8px;border-radius:12px;"><?= $totalUsers ?></span>
  </button>
  <button type="button" id="tabBtnProdi" onclick="switchAdminTab('prodi')"
          style="padding:10px 20px;font-weight:800;font-size:14px;border:none;background:none;cursor:pointer;color:#64748b;border-bottom:3px solid transparent;margin-bottom:-3px;display:flex;align-items:center;gap:8px;">
    <span>🎓 Manajemen Program Studi</span>
    <span style="background:#f1f5f9;color:#64748b;font-size:11.5px;padding:2px 8px;border-radius:12px;"><?= $totalProdi ?></span>
  </button>
</div>

<!-- TAB 1: MANAJEMEN PENGGUNA -->
<div id="tabContentUsers">
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.03);">
    <div style="padding:16px 20px;background:#f8fafc;border-bottom:1.5px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <div style="font-size:15px;font-weight:800;color:#1e293b;display:flex;align-items:center;gap:8px;">
        <span>Daftar Pengguna Sistem PPEPP</span>
      </div>
      <div style="display:flex;gap:10px;align-items:center;">
        <input type="text" id="userSearchInput" placeholder="Cari nama / username / email..." onkeyup="filterUsersTable()"
               class="form-control" style="font-size:12.5px;padding:6px 12px;width:240px;">
      </div>
    </div>

    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:13px;" id="usersTable">
        <thead>
          <tr style="background:#f1f5f9;color:#475569;font-weight:800;text-align:left;border-bottom:1.5px solid #cbd5e1;">
            <th style="padding:12px 16px;width:40px;">#</th>
            <th style="padding:12px 16px;">Pengguna</th>
            <th style="padding:12px 16px;">Role</th>
            <th style="padding:12px 16px;">Program Studi</th>
            <th style="padding:12px 16px;">Status API Key AI</th>
            <th style="padding:12px 16px;text-align:right;width:140px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $i => $u):
            $badge = $roleBadges[$u['role']] ?? $roleBadges['dosen'];
            $hasKey = !empty($u['gemini_api_key']);
          ?>
          <tr class="user-row" style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;">
            <td style="padding:12px 16px;color:#94a3b8;font-weight:700;"><?= $i + 1 ?></td>
            <td style="padding:12px 16px;">
              <div style="display:flex;align-items:center;gap:12px;">
                <?php if (!empty($u['avatar_url'])): ?>
                <img src="<?= htmlspecialchars($u['avatar_url']) ?>" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:1.5px solid #cbd5e1;">
                <?php else: ?>
                <div style="width:36px;height:36px;border-radius:50%;background:#e2e8f0;color:#475569;font-weight:800;font-size:14px;display:flex;align-items:center;justify-content:center;">
                  <?= strtoupper(mb_substr($u['nama_lengkap'] ?: $u['username'], 0, 1)) ?>
                </div>
                <?php endif; ?>
                <div>
                  <div style="font-weight:800;color:#1e293b;font-size:13.5px;"><?= htmlspecialchars($u['nama_lengkap']) ?></div>
                  <div style="font-size:11.5px;color:#64748b;margin-top:1px;">
                    @<?= htmlspecialchars($u['username']) ?> &nbsp;•&nbsp; <?= htmlspecialchars($u['email']) ?>
                  </div>
                </div>
              </div>
            </td>
            <td style="padding:12px 16px;">
              <span style="font-size:11.5px;font-weight:800;background:<?= $badge['bg'] ?>;color:<?= $badge['text'] ?>;padding:4px 10px;border-radius:12px;">
                <?= $badge['label'] ?>
              </span>
            </td>
            <td style="padding:12px 16px;">
              <span style="font-weight:700;color:#334155;">
                <?= htmlspecialchars($u['prodi_nama'] ?: 'Semua / Fakultas') ?>
              </span>
              <?php if (!empty($u['prodi_kode'])): ?>
              <span style="font-size:10.5px;font-weight:800;background:#f1f5f9;color:#64748b;padding:2px 6px;border-radius:6px;margin-left:4px;">
                <?= htmlspecialchars($u['prodi_kode']) ?>
              </span>
              <?php endif; ?>
            </td>
            <td style="padding:12px 16px;">
              <?php if ($hasKey): ?>
              <span style="font-size:11.5px;font-weight:700;color:#059669;background:#ecfdf5;padding:3px 10px;border-radius:10px;border:1px solid #a7f3d0;">
                ✓ Terpasang (<?= htmlspecialchars($u['gemini_model'] ?: 'gemini-1.5-flash') ?>)
              </span>
              <?php else: ?>
              <span style="font-size:11.5px;color:#94a3b8;font-style:italic;">Belum diisi</span>
              <?php endif; ?>
            </td>
            <td style="padding:12px 16px;text-align:right;">
              <div style="display:flex;justify-content:flex-end;gap:6px;">
                <a href="<?= BASE_URL ?>/users/<?= $u['id'] ?>/edit" class="btn btn-sm btn-outline" style="font-size:12px;padding:4px 10px;font-weight:700;" title="Edit User">
                  ✏️ Edit
                </a>
                <?php if ($u['id'] != $currentUser['id']): ?>
                <form action="<?= BASE_URL ?>/users/<?= $u['id'] ?>/delete" method="POST" style="margin:0;"
                      onsubmit="return confirm('Hapus user <?= htmlspecialchars(addslashes($u['nama_lengkap'])) ?>?')">
                  <button type="submit" class="btn btn-sm btn-outline" style="color:#ef4444;border-color:#fca5a5;font-size:12px;padding:4px 10px;" title="Hapus User">
                    🗑️
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
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.03);">
    <div style="padding:16px 20px;background:#f8fafc;border-bottom:1.5px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <div style="font-size:15px;font-weight:800;color:#1e293b;">
        Daftar Program Studi Fakultas
      </div>
      <a href="<?= BASE_URL ?>/prodi/create" class="btn btn-primary btn-sm" style="background:#4f46e5;border:none;font-weight:800;">
        + Tambah Prodi Baru
      </a>
    </div>

    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
          <tr style="background:#f1f5f9;color:#475569;font-weight:800;text-align:left;border-bottom:1.5px solid #cbd5e1;">
            <th style="padding:12px 16px;width:40px;">#</th>
            <th style="padding:12px 16px;">Kode</th>
            <th style="padding:12px 16px;">Nama Program Studi</th>
            <th style="padding:12px 16px;">Jenjang</th>
            <th style="padding:12px 16px;">Kaprodi</th>
            <th style="padding:12px 16px;">Jumlah Dosen</th>
            <th style="padding:12px 16px;">Status</th>
            <th style="padding:12px 16px;text-align:right;width:180px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($prodis as $i => $pr): ?>
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:12px 16px;color:#94a3b8;font-weight:700;"><?= $i + 1 ?></td>
            <td style="padding:12px 16px;">
              <span style="font-weight:900;color:#0284c7;background:#e0f2fe;padding:4px 10px;border-radius:6px;font-size:12px;">
                <?= htmlspecialchars($pr['kode']) ?>
              </span>
            </td>
            <td style="padding:12px 16px;">
              <a href="<?= BASE_URL ?>/prodi/<?= $pr['id'] ?>" style="font-weight:800;color:#1e293b;text-decoration:none;">
                <?= htmlspecialchars($pr['nama']) ?>
              </a>
              <?php if (!empty($pr['deskripsi'])): ?>
              <div style="font-size:11.5px;color:#64748b;margin-top:2px;"><?= htmlspecialchars($pr['deskripsi']) ?></div>
              <?php endif; ?>
            </td>
            <td style="padding:12px 16px;font-weight:700;color:#475569;">
              <?= $jenjangLabel[$pr['jenjang']] ?? $pr['jenjang'] ?>
            </td>
            <td style="padding:12px 16px;">
              <span style="font-weight:700;color:<?= !empty($pr['kaprodi_nama']) ? '#6d28d9' : '#94a3b8' ?>;">
                <?= htmlspecialchars($pr['kaprodi_nama'] ?: '— Belum Ditentukan —') ?>
              </span>
            </td>
            <td style="padding:12px 16px;">
              <span style="font-weight:800;color:#0284c7;background:#f0f9ff;padding:3px 10px;border-radius:10px;border:1px solid #bae6fd;">
                👥 <?= (int)($pr['total_dosen'] ?? 0) ?> Dosen
              </span>
            </td>
            <td style="padding:12px 16px;">
              <span style="font-size:11.5px;font-weight:800;padding:3px 10px;border-radius:12px;<?= $pr['aktif'] ? 'background:#ecfdf5;color:#059669;' : 'background:#fef2f2;color:#991b1b;' ?>">
                <?= $pr['aktif'] ? '✓ Aktif' : '✕ Nonaktif' ?>
              </span>
            </td>
            <td style="padding:12px 16px;text-align:right;">
              <div style="display:flex;justify-content:flex-end;gap:6px;">
                <a href="<?= BASE_URL ?>/prodi/<?= $pr['id'] ?>" class="btn btn-sm btn-outline" style="font-size:12px;padding:4px 10px;font-weight:700;color:#4f46e5;border-color:#c7d2fe;" title="Kelola Dosen & Detail">
                  🔍 Kelola
                </a>
                <a href="<?= BASE_URL ?>/prodi/<?= $pr['id'] ?>/edit" class="btn btn-sm btn-outline" style="font-size:12px;padding:4px 10px;font-weight:700;" title="Edit Prodi">
                  ✏️ Edit
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
    btnU.style.color = '#4f46e5';
    btnU.style.borderBottomColor = '#4f46e5';
    btnP.style.color = '#64748b';
    btnP.style.borderBottomColor = 'transparent';
  } else {
    contentU.style.display = 'none';
    contentP.style.display = 'block';
    btnP.style.color = '#4f46e5';
    btnP.style.borderBottomColor = '#4f46e5';
    btnU.style.color = '#64748b';
    btnU.style.borderBottomColor = 'transparent';
  }
}

function filterUsersTable() {
  var input = document.getElementById('userSearchInput');
  var filter = input.value.toLowerCase();
  var rows = document.querySelectorAll('.user-row');

  rows.forEach(function(row) {
    var text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
}
</script>
