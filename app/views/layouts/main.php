<?php
$currentUser = $_SESSION['user'] ?? [];
$basePub = BASE_URL;
$basePubPath = parse_url(BASE_URL, PHP_URL_PATH);
$basePubPath = ($basePubPath === null || $basePubPath === false) ? '' : $basePubPath;
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
$appRelPath = $basePubPath !== '' ? ('/' . ltrim(substr($currentPath, strlen($basePubPath)), '/')) : $currentPath;

$roleLabel = [
  'kaprodi' => 'Ketua Program Studi',
  'dekan'   => 'Dekan / Pimpinan',
  'dosen'   => 'Dosen / Auditor',
];
$userRole = $currentUser['role'] ?? 'dosen';

$sidebarPendingBadge = 0;
$sidebarBadgeType = '';
if (!empty($currentUser['id'])) {
    try {
        $dbNav = (new Model())->getDb();
        if (in_array($userRole, ['kaprodi', 'dekan'])) {
            $stmtNB = $dbNav->prepare("SELECT COUNT(*) FROM tugas_dokumen WHERE kaprodi_id = ? AND status = 'submitted'");
            $stmtNB->execute([$currentUser['id']]);
            $sidebarPendingBadge = (int)$stmtNB->fetchColumn();
            $sidebarBadgeType = 'review';
        } else {
            $stmtNB = $dbNav->prepare("SELECT COUNT(*) FROM tugas_dokumen WHERE dosen_id = ? AND status IN ('assigned', 'revision')");
            $stmtNB->execute([$currentUser['id']]);
            $sidebarPendingBadge = (int)$stmtNB->fetchColumn();
            $sidebarBadgeType = 'priority';
        }
    } catch (\Throwable $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?><?= APP_NAME ?></title>
  <meta name="description" content="Platform PPEPP untuk Sistem Informasi Penjaminan Mutu Internal (SIPMI) Perguruan Tinggi">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=3.0">
  <script>
    window.PPEPP_BASE_URL = '<?= BASE_URL ?>';
    window.BASE_URL = '<?= BASE_URL ?>';
    window.BASE_APP = '<?= BASE_URL ?>';
    window.USER_HAS_API_KEY = <?= !empty($_SESSION['user']['gemini_api_key']) ? 'true' : 'false' ?>;
  </script>
</head>
<body>
  <!-- Sidebar Overlay (mobile) -->
  <div id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.6);z-index:99;backdrop-filter:blur(2px);"></div>

  <!-- Sidebar -->
  <aside class="sidebar">
    <!-- Brand Header -->
    <div class="sidebar-header">
      <a href="<?= BASE_URL ?>/dashboard" class="sidebar-brand">
        <div class="brand-logo">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M12 2L2 7l10 5 10-5-10-5z" />
            <path d="M2 17l10 5 10-5" />
            <path d="M2 12l10 5 10-5" />
          </svg>
        </div>
        <div>
          <div class="brand-title">SIPMI PPEPP</div>
          <div class="brand-sub">Penjaminan Mutu Internal</div>
        </div>
      </a>
    </div>

    <!-- User Profile Capsule -->
    <div class="sidebar-profile">
      <?php if (!empty($currentUser['avatar_url'])): ?>
        <img src="<?= htmlspecialchars($currentUser['avatar_url']) ?>" alt="Avatar"
          style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:1px solid rgba(255,255,255,0.2);flex-shrink:0;">
      <?php else: ?>
        <div class="user-avatar">
          <?= strtoupper(mb_substr(explode(' ', $currentUser['nama_lengkap'] ?: $currentUser['username'] ?: 'U')[0], 0, 1)) ?>
        </div>
      <?php endif; ?>
      <div class="profile-meta">
        <div class="profile-role"><?= $roleLabel[$userRole] ?? 'Dosen' ?></div>
        <div class="profile-name" title="<?= htmlspecialchars($currentUser['nama_lengkap'] ?: $currentUser['username']) ?>">
          <?= htmlspecialchars($currentUser['nama_lengkap'] ?: $currentUser['username']) ?>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
      <div class="nav-section-label">Menu Utama</div>
      <ul>
        <li class="nav-item">
          <a href="<?= BASE_URL ?>/dashboard" class="nav-link <?= ($appRelPath === '/dashboard' || $appRelPath === '/' || $appRelPath === '') ? 'active' : '' ?>">
            <span class="nav-icon">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1.5" />
                <rect x="14" y="3" width="7" height="7" rx="1.5" />
                <rect x="14" y="14" width="7" height="7" rx="1.5" />
                <rect x="3" y="14" width="7" height="7" rx="1.5" />
              </svg>
            </span>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= BASE_URL ?>/pending" class="nav-link <?= str_starts_with($appRelPath, '/pending') ? 'active' : '' ?>">
            <span class="nav-icon">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
            </span>
            <span>Tugas &amp; Draft Belum Selesai</span>
            <?php if ($sidebarPendingBadge > 0): ?>
              <?php if ($sidebarBadgeType === 'review'): ?>
                <span class="badge" style="margin-left:auto;font-size:10px;padding:2px 7px;border-radius:999px;background:#dbeafe;color:#1e40af;font-weight:700;" title="<?= $sidebarPendingBadge ?> tugas perlu dicek/ACC">
                  <?= $sidebarPendingBadge ?> Review
                </span>
              <?php else: ?>
                <span class="badge" style="margin-left:auto;font-size:10px;padding:2px 7px;border-radius:999px;background:#fef3c7;color:#b45309;border:1px solid #fde68a;font-weight:700;" title="<?= $sidebarPendingBadge ?> tugas prioritas dari Kaprodi">
                  🔥 <?= $sidebarPendingBadge ?> Prioritas
                </span>
              <?php endif; ?>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= BASE_URL ?>/penetapan/kriteria" class="nav-link <?= str_starts_with($appRelPath, '/penetapan/kriteria') ? 'active' : '' ?>">
            <span class="nav-icon">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="4" y1="6" x2="20" y2="6" />
                <line x1="4" y1="12" x2="20" y2="12" />
                <line x1="4" y1="18" x2="20" y2="18" />
              </svg>
            </span>
            <span>Kelola Kriteria Mutu</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= BASE_URL ?>/laporan" class="nav-link <?= str_starts_with($appRelPath, '/laporan') ? 'active' : '' ?>">
            <span class="nav-icon">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10 9 9 9 8 9" />
              </svg>
            </span>
            <span>Laporan Eksekutif PPEPP</span>
          </a>
        </li>
      </ul>

      <!-- PPEPP Siklus Nav -->
      <div class="nav-section-label" style="margin-top:14px;">Siklus PPEPP</div>
      <?php
      $isProjActive = ($appRelPath === '/ppepp' || str_starts_with($appRelPath, '/ppepp/') || str_starts_with($appRelPath, '/ppepp'));
      $inPpeppProject = str_starts_with($appRelPath, '/ppepp/') && preg_match('#/ppepp/(\d+)#', $appRelPath, $m);
      $activeProjId = $inPpeppProject ? (int) $m[1] : 0;
      ?>
      <ul>
        <li class="nav-item">
          <a href="<?= BASE_URL ?>/ppepp" class="nav-link <?= $isProjActive ? 'active' : '' ?>">
            <span class="nav-icon">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
              </svg>
            </span>
            <span>Project PPEPP</span>
          </a>
        </li>
      </ul>

      <?php if ($activeProjId): ?>
        <!-- Sub-menu 5 tahap saat dalam project -->
        <div class="ppepp-steps">
          <?php
          $ppSteps = [
            ['P', 'Penetapan', 'penetapan?project_id=' . $activeProjId],
            ['P', 'Pelaksanaan', 'pelaksanaan?project_id=' . $activeProjId],
            ['E', 'Evaluasi', 'evaluasi?project_id=' . $activeProjId],
            ['P', 'Pengendalian', 'pengendalian?project_id=' . $activeProjId],
            ['P', 'Peningkatan', 'peningkatan?project_id=' . $activeProjId],
          ];
          foreach ($ppSteps as [$letter, $nama, $targetRoute]):
            $isActive = str_contains($appRelPath, '/' . explode('?', $targetRoute)[0]);
            ?>
            <a href="<?= BASE_URL ?>/<?= $targetRoute ?>" class="ppepp-step <?= $isActive ? 'active' : '' ?>">
              <span class="step-num"><?= $letter ?></span>
              <span><?= $nama ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Contextual Stage Menus -->
      <?php if (str_starts_with($appRelPath, '/penetapan')): ?>
        <div class="nav-section-label" style="margin-top:14px;">Modul Penetapan</div>
        <ul>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/penetapan" class="nav-link <?= ($appRelPath === '/penetapan' || $appRelPath === '/penetapan/') ? 'active' : '' ?>">
              <span class="nav-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg></span>
              <span>Daftar Penetapan</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/penetapan/create" class="nav-link <?= str_starts_with($appRelPath, '/penetapan/create') ? 'active' : '' ?>">
              <span class="nav-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></span>
              <span>Buat Penetapan Baru</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/penetapan/referensi" class="nav-link <?= str_starts_with($appRelPath, '/penetapan/referensi') ? 'active' : '' ?>">
              <span class="nav-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></span>
              <span>Referensi &amp; Analisis AI</span>
            </a>
          </li>
        </ul>
      <?php elseif (str_starts_with($appRelPath, '/pelaksanaan')): ?>
        <div class="nav-section-label" style="margin-top:14px;">Modul Pelaksanaan</div>
        <ul>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/pelaksanaan" class="nav-link <?= ($appRelPath === '/pelaksanaan' || $appRelPath === '/pelaksanaan/') ? 'active' : '' ?>">
              <span class="nav-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
              <span>Daftar Pelaksanaan</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/pelaksanaan/create" class="nav-link <?= str_starts_with($appRelPath, '/pelaksanaan/create') ? 'active' : '' ?>">
              <span class="nav-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></span>
              <span>Buat Pelaksanaan</span>
            </a>
          </li>
        </ul>
      <?php elseif (str_starts_with($appRelPath, '/evaluasi')): ?>
        <div class="nav-section-label" style="margin-top:14px;">Modul Evaluasi</div>
        <ul>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/evaluasi" class="nav-link <?= ($appRelPath === '/evaluasi' || $appRelPath === '/evaluasi/') ? 'active' : '' ?>">
              <span class="nav-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg></span>
              <span>Daftar Evaluasi</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/evaluasi/create" class="nav-link <?= str_starts_with($appRelPath, '/evaluasi/create') ? 'active' : '' ?>">
              <span class="nav-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></span>
              <span>Buat Evaluasi</span>
            </a>
          </li>
        </ul>
      <?php elseif (str_starts_with($appRelPath, '/pengendalian')): ?>
        <div class="nav-section-label" style="margin-top:14px;">Modul Pengendalian</div>
        <ul>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/pengendalian" class="nav-link <?= ($appRelPath === '/pengendalian' || $appRelPath === '/pengendalian/') ? 'active' : '' ?>">
              <span class="nav-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></span>
              <span>Daftar Pengendalian (RTL)</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/pengendalian/create" class="nav-link <?= str_starts_with($appRelPath, '/pengendalian/create') ? 'active' : '' ?>">
              <span class="nav-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></span>
              <span>Buat Pengendalian</span>
            </a>
          </li>
        </ul>
      <?php elseif (str_starts_with($appRelPath, '/peningkatan')): ?>
        <div class="nav-section-label" style="margin-top:14px;">Modul Peningkatan</div>
        <ul>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/peningkatan" class="nav-link <?= ($appRelPath === '/peningkatan' || $appRelPath === '/peningkatan/') ? 'active' : '' ?>">
              <span class="nav-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></span>
              <span>Daftar Peningkatan</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/peningkatan/create" class="nav-link <?= str_starts_with($appRelPath, '/peningkatan/create') ? 'active' : '' ?>">
              <span class="nav-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></span>
              <span>Buat Peningkatan</span>
            </a>
          </li>
        </ul>
      <?php endif; ?>

      <?php if (in_array($currentUser['role'] ?? '', ['kaprodi', 'dekan']) || !empty($_SESSION['impersonator'])):
        $isAdminActive = ($appRelPath === '/admin' || str_starts_with($appRelPath, '/admin') || (str_starts_with($appRelPath, '/users') && !str_starts_with($appRelPath, '/users/api-key')) || str_starts_with($appRelPath, '/prodi'));
      ?>
        <div class="nav-section-label" style="margin-top:14px;">Administrasi</div>
        <ul>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/admin" class="nav-link <?= $isAdminActive ? 'active' : '' ?>">
              <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="3"/>
                  <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2 2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/>
                </svg>
              </span>
              <span>Kelola Pengguna &amp; Prodi</span>
            </a>
          </li>
        </ul>
      <?php endif; ?>
    </nav>

    <!-- Footer Sidebar -->
    <div class="sidebar-footer">
      <a href="<?= BASE_URL ?>/auth/logout" class="logout-btn">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15">
          <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
          <polyline points="16 17 21 12 16 7" />
          <line x1="21" y1="12" x2="9" y2="12" />
        </svg>
        <span>Keluar</span>
      </a>

      <a href="<?= BASE_URL ?>/setting" class="admin-gear-btn" title="Pengaturan AI &amp; Akun">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
          <circle cx="12" cy="12" r="3" />
          <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2 2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" />
        </svg>
      </a>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <!-- Top Bar -->
    <header class="topbar">
      <div class="topbar-left">
        <button id="menuToggle" class="btn btn-ghost btn-sm" style="display:none;padding:6px;">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="18" x2="21" y2="18" />
          </svg>
        </button>
        <div>
          <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
          <?php if (!empty($breadcrumbs)): ?>
            <nav class="breadcrumb">
              <?php foreach ($breadcrumbs as $i => $crumb): ?>
                <?php if ($i > 0): ?><span class="sep">/</span><?php endif; ?>
                <?php if (!empty($crumb['url'])): ?>
                  <a href="<?= $crumb['url'] ?>"><?= htmlspecialchars($crumb['label']) ?></a>
                <?php else: ?>
                  <span><?= htmlspecialchars($crumb['label']) ?></span>
                <?php endif; ?>
              <?php endforeach; ?>
            </nav>
          <?php endif; ?>
        </div>
      </div>
      <div class="topbar-right">
        <div class="user-badge">
          <?php if (!empty($currentUser['avatar_url'])): ?>
            <img src="<?= htmlspecialchars($currentUser['avatar_url']) ?>" alt="" width="28" height="28"
              style="border-radius:50%;object-fit:cover;flex-shrink:0;">
          <?php else: ?>
            <div class="user-avatar">
              <?= strtoupper(mb_substr(explode(' ', $currentUser['nama_lengkap'] ?: $currentUser['username'] ?: 'U')[0], 0, 1)) ?>
            </div>
          <?php endif; ?>
          <div>
            <div class="user-name">
              <?= htmlspecialchars($currentUser['nama_prodi'] ?: $currentUser['nama_lengkap'] ?: $currentUser['username'] ?: 'Pengguna') ?>
            </div>
            <div class="user-role">
              <?= ['dekan' => 'Dekan', 'kaprodi' => 'Kaprodi', 'dosen' => 'Dosen'][$currentUser['role'] ?? 'dosen'] ?? 'Dosen' ?>
            </div>
          </div>
        </div>
      </div>
    </header>

    <?php if (!empty($_SESSION['impersonator'])): ?>
      <div style="background:linear-gradient(90deg,#4338ca,#6366f1);color:#fff;padding:10px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:13px;border-bottom:1px solid rgba(255,255,255,0.2);box-shadow:0 2px 8px rgba(79,70,229,0.25);">
        <div style="display:flex;align-items:center;gap:10px;">
          <span style="font-size:16px;">🎭</span>
          <span>
            Mode Penyamaran: Anda sedang mengakses sistem sebagai <strong><?= htmlspecialchars($currentUser['nama_lengkap'] ?: $currentUser['username']) ?></strong>
            (<?= ucfirst($userRole) ?>) — Akun asli: <strong><?= htmlspecialchars($_SESSION['impersonator']['nama_lengkap']) ?></strong>
          </span>
        </div>
        <a href="<?= BASE_URL ?>/users/switch-back" class="btn btn-sm" style="background:#fff;color:#4338ca;font-weight:800;border:none;padding:5px 14px;border-radius:6px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:0 1px 4px rgba(0,0,0,0.15);">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 14l-4-4 4-4"/><path d="M5 10h11a4 4 0 1 1 0 8h-1"/></svg>
          Kembali ke Akun Asli
        </a>
      </div>
    <?php endif; ?>

    <!-- Content Area -->
    <main class="content-area">
      <?php if (isset($flash) && $flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : ($flash['type'] === 'warning' ? 'warning' : ($flash['type'] === 'info' ? 'info' : 'success')) ?>">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <?php if ($flash['type'] === 'success'): ?>
              <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
              <polyline points="22 4 12 14.01 9 11.01" />
            <?php elseif ($flash['type'] === 'error'): ?>
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            <?php elseif ($flash['type'] === 'warning'): ?>
              <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
              <line x1="12" y1="9" x2="12" y2="13" />
              <line x1="12" y1="17" x2="12.01" y2="17" />
            <?php else: ?>
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="16" x2="12" y2="12" />
              <line x1="12" y1="8" x2="12.01" y2="8" />
            <?php endif; ?>
          </svg>
          <div><?= htmlspecialchars($flash['message']) ?></div>
        </div>
      <?php endif; ?>

      <?= $content ?>
    </main>
  </div>

  <!-- Global AI Warning Modal -->
  <div class="modal-overlay" id="globalAiWarningModal">
    <div class="modal" style="max-width:480px;">
      <div id="aiModalHeader" class="modal-header" style="background:var(--danger);color:#fff;">
        <div style="display:flex;align-items:center;gap:10px;">
          <div id="aiModalIcon" style="width:32px;height:32px;background:rgba(255,255,255,0.2);border-radius:6px;display:flex;align-items:center;justify-content:center;">
            <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
              <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4" />
            </svg>
          </div>
          <div>
            <h3 id="aiModalTitle" style="color:#fff;margin:0;font-size:14px;font-weight:700;">Konfigurasi AI Diperlukan</h3>
          </div>
        </div>
        <button type="button" class="modal-close-btn" style="color:#fff;" data-modal-close="globalAiWarningModal">&times;</button>
      </div>
      <div class="modal-body">
        <div id="aiModalMessage" style="font-size:13.5px;color:var(--text-main);margin-bottom:14px;">
          API Key Gemini belum diisi atau tidak valid. Silakan masukkan API Key Gemini Anda di menu Pengaturan.
        </div>
        <div id="aiModalTips" class="card" style="background:var(--surface-hover);padding:12px 14px;font-size:12.5px;color:var(--text-muted);border:1px solid var(--border);">
          <strong>Petunjuk:</strong> Anda dapat memperoleh Gemini API Key secara gratis dari Google AI Studio (aistudio.google.com).
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close="globalAiWarningModal">Tutup</button>
        <a href="<?= BASE_URL ?>/users/api-key" id="aiModalBtnAction" class="btn btn-primary">Buka Pengaturan API Key</a>
      </div>
    </div>
  </div>

  <script src="<?= BASE_URL ?>/js/app.js?v=3.0"></script>
</body>
</html>