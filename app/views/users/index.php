<?php
$pageTitle   = 'Manajemen Pengguna (Dosen)';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Manajemen Pengguna'],
];

$roleBadge = [
  'dekan'   => ['bg' => '#dbeafe', 'text' => '#1d4ed8', 'label' => '👑 Dekan'],
  'kaprodi' => ['bg' => '#ede9fe', 'text' => '#6d28d9', 'label' => '🎓 Kaprodi'],
  'dosen'   => ['bg' => '#f1f5f9', 'text' => '#475569', 'label' => '🧑‍🏫 Dosen'],
];
?>

<!-- Header -->
<div style="background:linear-gradient(135deg,#0f172a,#1e1b4b,#312e81);border-radius:16px;padding:26px 32px;margin-bottom:24px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;box-shadow:0 8px 28px rgba(15,23,42,0.15);">
  <div>
    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1.2px;color:#a5b4fc;margin-bottom:6px;">Manajemen Pengguna Sistem</div>
    <h2 style="font-size:22px;font-weight:900;color:#fff;margin:0 0 4px;">👥 Daftar Dosen PPEPP</h2>
    <p style="font-size:13px;color:rgba(255,255,255,0.7);margin:0;">Kelola akun dosen, role, dan status API Key Gemini</p>
  </div>
  <a href="<?= BASE_URL ?>/users/create"
     style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;font-weight:800;font-size:13px;padding:11px 20px;border-radius:10px;text-decoration:none;box-shadow:0 4px 14px rgba(79,70,229,0.4);">
    + Tambah Dosen Baru
  </a>
</div>

<?php if (!empty($flash['message'])): ?>
<div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;<?= $flash['type']==='success' ? 'background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;' : 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;' ?>">
  <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<!-- Stats Row -->
<?php
$totalDosen   = count(array_filter($users, fn($u) => $u['role'] === 'dosen'));
$totalKaprodi = count(array_filter($users, fn($u) => $u['role'] === 'kaprodi'));
$totalDekan   = count(array_filter($users, fn($u) => $u['role'] === 'dekan'));
$noApiKey     = count(array_filter($users, fn($u) => empty($u['gemini_api_key'])));
?>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:14px;margin-bottom:24px;">
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:16px 18px;text-align:center;">
    <div style="font-size:26px;font-weight:900;color:#1e293b;"><?= count($users) ?></div>
    <div style="font-size:12px;color:#64748b;font-weight:700;">Total Pengguna</div>
  </div>
  <div style="background:#fff;border:1.5px solid #ddd6fe;border-radius:14px;padding:16px 18px;text-align:center;">
    <div style="font-size:26px;font-weight:900;color:#6d28d9;"><?= $totalKaprodi ?></div>
    <div style="font-size:12px;color:#7c3aed;font-weight:700;">Kaprodi</div>
  </div>
  <div style="background:#fff;border:1.5px solid #bfdbfe;border-radius:14px;padding:16px 18px;text-align:center;">
    <div style="font-size:26px;font-weight:900;color:#1d4ed8;"><?= $totalDekan ?></div>
    <div style="font-size:12px;color:#1d4ed8;font-weight:700;">Dekan</div>
  </div>
  <div style="background:#fff;border:1.5px solid #f1f5f9;border-radius:14px;padding:16px 18px;text-align:center;">
    <div style="font-size:26px;font-weight:900;color:#475569;"><?= $totalDosen ?></div>
    <div style="font-size:12px;color:#64748b;font-weight:700;">Dosen</div>
  </div>
  <div style="background:<?= $noApiKey > 0 ? '#fff7ed' : '#ecfdf5' ?>;border:1.5px solid <?= $noApiKey > 0 ? '#fed7aa' : '#a7f3d0' ?>;border-radius:14px;padding:16px 18px;text-align:center;">
    <div style="font-size:26px;font-weight:900;color:<?= $noApiKey > 0 ? '#c2410c' : '#059669' ?>;"><?= $noApiKey ?></div>
    <div style="font-size:12px;color:<?= $noApiKey > 0 ? '#ea580c' : '#059669' ?>;font-weight:700;">Belum Set API Key</div>
  </div>
</div>

<!-- User Table -->
<div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,0.04);">
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
      <thead>
        <tr style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:1.5px solid #e2e8f0;">
          <th style="padding:14px 18px;text-align:left;font-weight:800;color:#1e293b;">#</th>
          <th style="padding:14px 18px;text-align:left;font-weight:800;color:#1e293b;">Nama Lengkap</th>
          <th style="padding:14px 18px;text-align:left;font-weight:800;color:#1e293b;">Username / Email</th>
          <th style="padding:14px 18px;text-align:center;font-weight:800;color:#1e293b;">Role</th>
          <th style="padding:14px 18px;text-align:center;font-weight:800;color:#1e293b;">API Key Gemini</th>
          <th style="padding:14px 18px;text-align:center;font-weight:800;color:#1e293b;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $idx => $u): ?>
        <?php $rb = $roleBadge[$u['role']] ?? $roleBadge['dosen']; ?>
        <tr style="border-bottom:1px solid #f1f5f9;<?= $currentUser['id'] == $u['id'] ? 'background:#fafafa;' : '' ?>">
          <td style="padding:14px 18px;color:#94a3b8;font-weight:700;"><?= $idx + 1 ?></td>
          <td style="padding:14px 18px;">
            <div style="display:flex;align-items:center;gap:10px;">
              <?php if (!empty($u['avatar_url'])): ?>
              <img src="<?= htmlspecialchars($u['avatar_url']) ?>" alt="" width="34" height="34"
                   style="border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;flex-shrink:0;">
              <?php else: ?>
              <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:13px;flex-shrink:0;">
                <?= strtoupper(mb_substr(explode(' ', $u['nama_lengkap'] ?: $u['username'])[0], 0, 1)) ?>
              </div>
              <?php endif; ?>
              <div>
                <div style="font-weight:800;color:#0f172a;line-height:1.3;"><?= htmlspecialchars($u['nama_lengkap'] ?: $u['username']) ?></div>
                <?php if ($currentUser['id'] == $u['id']): ?>
                <div style="font-size:11px;color:#059669;font-weight:700;">← Akun Anda</div>
                <?php endif; ?>
              </div>
            </div>
          </td>
          <td style="padding:14px 18px;">
            <div style="font-size:12.5px;color:#334155;font-weight:600;"><?= htmlspecialchars($u['username']) ?></div>
            <div style="font-size:12px;color:#94a3b8;"><?= htmlspecialchars($u['email'] ?? '') ?></div>
          </td>
          <td style="padding:14px 18px;text-align:center;">
            <span style="display:inline-block;background:<?= $rb['bg'] ?>;color:<?= $rb['text'] ?>;font-size:12px;font-weight:800;padding:4px 12px;border-radius:20px;white-space:nowrap;">
              <?= $rb['label'] ?>
            </span>
          </td>
          <td style="padding:14px 18px;text-align:center;">
            <?php if (!empty($u['gemini_api_key'])): ?>
            <span style="display:inline-flex;align-items:center;gap:5px;background:#ecfdf5;color:#059669;font-size:12px;font-weight:800;padding:4px 12px;border-radius:20px;">
              ✓ Sudah Diset
            </span>
            <?php else: ?>
            <span style="display:inline-flex;align-items:center;gap:5px;background:#fef2f2;color:#dc2626;font-size:12px;font-weight:800;padding:4px 12px;border-radius:20px;">
              ✗ Belum Diset
            </span>
            <?php endif; ?>
          </td>
          <td style="padding:14px 18px;text-align:center;">
            <div style="display:flex;justify-content:center;gap:6px;">
              <a href="<?= BASE_URL ?>/users/<?= $u['id'] ?>/edit"
                 style="display:inline-flex;align-items:center;gap:5px;background:#f1f5f9;color:#334155;font-size:12px;font-weight:700;padding:6px 12px;border-radius:8px;text-decoration:none;border:1px solid #e2e8f0;">
                ✏️ Edit
              </a>
              <?php if ($currentUser['id'] != $u['id']): ?>
              <form action="<?= BASE_URL ?>/users/<?= $u['id'] ?>/delete" method="POST" style="margin:0;"
                    onsubmit="return confirm('Yakin hapus user <?= htmlspecialchars(addslashes($u['nama_lengkap'] ?: $u['username'])) ?>?')">
                <button type="submit" style="display:inline-flex;align-items:center;gap:5px;background:#fef2f2;color:#dc2626;font-size:12px;font-weight:700;padding:6px 12px;border-radius:8px;border:1px solid #fecaca;cursor:pointer;">
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

<?php if (empty($users)): ?>
<div style="text-align:center;padding:48px;color:#94a3b8;font-style:italic;">Belum ada pengguna terdaftar.</div>
<?php endif; ?>
