<?php
$pageTitle   = 'Detail Prodi: ' . htmlspecialchars($prodi['nama']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Admin', 'url' => BASE_URL . '/admin'],
  ['label' => htmlspecialchars($prodi['nama'])],
];

$jenjangLabel = ['D3' => 'D-III (Diploma 3)', 'S1' => 'S-1 (Sarjana)', 'S2' => 'S-2 (Magister)', 'S3' => 'S-3 (Doktor)'];
$dosenProdi   = $prodi['dosen'] ?? [];

// Dosen yang belum ada di prodi ini (untuk dropdown tambah)
$dosenLain = array_filter($allDosen, fn($u) => $u['prodi_id'] !== $prodi['id'] || $u['prodi_id'] === null);
$roleBadge = [
  'dekan'   => ['class' => 'badge-warning', 'label' => 'Dekan'],
  'kaprodi' => ['class' => 'badge-info', 'label' => 'Kaprodi'],
  'dosen'   => ['class' => 'badge-neutral', 'label' => 'Dosen'],
];
?>

<div class="page-header">
  <div>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
      <span class="badge badge-info font-mono" style="font-weight:700;"><?= htmlspecialchars($prodi['kode']) ?></span>
      <span class="badge badge-neutral"><?= $jenjangLabel[$prodi['jenjang']] ?? $prodi['jenjang'] ?></span>
      <span class="badge <?= $prodi['aktif'] ? 'badge-success' : 'badge-danger' ?> badge-dot">
        <?= $prodi['aktif'] ? 'Aktif' : 'Nonaktif' ?>
      </span>
    </div>
    <h2><?= htmlspecialchars($prodi['nama']) ?></h2>
    <?php if (!empty($prodi['deskripsi'])): ?>
    <p class="page-desc"><?= htmlspecialchars($prodi['deskripsi']) ?></p>
    <?php endif; ?>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>/edit" class="btn btn-primary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Edit Prodi
    </a>
    <a href="<?= BASE_URL ?>/admin" class="btn btn-outline">
      ← Kembali ke Admin
    </a>
  </div>
</div>

<?php if (!empty($flash['message'])): ?>
<div class="alert <?= ($flash['type'] ?? '') === 'success' ? 'alert-success' : 'alert-danger' ?> mb-4">
  <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<!-- Stats Grid -->
<div class="stats-grid mb-4" style="grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= count($dosenProdi) ?></div>
      <div class="stat-label">Total Dosen</div>
    </div>
  </div>

  <?php
    $cntKaprodi = count(array_filter($dosenProdi, fn($u) => $u['role'] === 'kaprodi'));
    $cntDosen   = count(array_filter($dosenProdi, fn($u) => $u['role'] === 'dosen'));
    $cntApiKey  = count(array_filter($dosenProdi, fn($u) => !empty($u['gemini_api_key'])));
  ?>
  <div class="stat-card">
    <div class="stat-icon green">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $cntKaprodi ?></div>
      <div class="stat-label">Kaprodi</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon gold">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $cntDosen ?></div>
      <div class="stat-label">Staf Dosen</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon <?= $cntApiKey === count($dosenProdi) && count($dosenProdi) > 0 ? 'green' : 'blue' ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $cntApiKey ?>/<?= count($dosenProdi) ?></div>
      <div class="stat-label">API Key AI Terpasang</div>
    </div>
  </div>
</div>

<!-- Dosen List + Add Form -->
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:20px;align-items:start;">

  <!-- Dosen List -->
  <div class="card" style="grid-column: span 2;">
    <div class="card-header">
      <h3 class="card-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        Daftar Dosen Prodi <?= htmlspecialchars($prodi['nama']) ?>
      </h3>
      <span class="badge badge-neutral"><?= count($dosenProdi) ?> orang</span>
    </div>

    <?php if (empty($dosenProdi)): ?>
    <div class="empty-state" style="padding:48px 20px;">
      <div class="empty-icon">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      </div>
      <h3>Belum Ada Dosen di Prodi Ini</h3>
      <p>Tambahkan dosen ke prodi ini melalui formulir di samping.</p>
    </div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:40px;">#</th>
            <th>Nama Lengkap</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status AI</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($dosenProdi as $idx => $d): ?>
          <?php $rb = $roleBadge[$d['role']] ?? $roleBadge['dosen']; ?>
          <tr>
            <td class="td-muted"><?= $idx + 1 ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:10px;">
                <?php if (!empty($d['avatar_url'])): ?>
                <img src="<?= htmlspecialchars($d['avatar_url']) ?>" alt="" width="32" height="32"
                     style="border-radius:50%;object-fit:cover;border:1px solid var(--border);flex-shrink:0;">
                <?php else: ?>
                <div style="width:32px;height:32px;border-radius:50%;background:var(--bg-main);border:1px solid var(--border);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;flex-shrink:0;">
                  <?= strtoupper(mb_substr(explode(' ', $d['nama_lengkap'] ?: 'U')[0], 0, 1)) ?>
                </div>
                <?php endif; ?>
                <div>
                  <div style="font-weight:600;font-size:13.5px;color:var(--text-primary);"><?= htmlspecialchars($d['nama_lengkap']) ?></div>
                  <div class="td-muted" style="font-size:11.5px;">@<?= htmlspecialchars($d['username'] ?? '') ?></div>
                </div>
              </div>
            </td>
            <td class="td-muted" style="font-size:12.5px;"><?= htmlspecialchars($d['email'] ?? '—') ?></td>
            <td>
              <span class="badge <?= $rb['class'] ?>">
                <?= $rb['label'] ?>
              </span>
            </td>
            <td>
              <?php if (!empty($d['gemini_api_key'])): ?>
              <span class="badge badge-success badge-dot">Aktif</span>
              <?php else: ?>
              <span class="badge badge-neutral">Belum ada key</span>
              <?php endif; ?>
            </td>
            <td style="text-align:right;">
              <form action="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>/remove-dosen" method="POST" style="margin:0;display:inline;"
                    onsubmit="return confirm('Lepas <?= htmlspecialchars(addslashes($d['nama_lengkap'])) ?> dari prodi ini?')">
                <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline" style="color:var(--danger);border-color:#FECACA;" title="Lepas dari prodi">
                  Lepas
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- Add Dosen Panel -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="17" y1="11" x2="23" y2="11"/></svg>
        Tambah Dosen ke Prodi
      </h3>
    </div>
    <form action="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>/add-dosen" method="POST">
      <div class="card-body">
        <div class="form-group mb-3">
          <label for="user_id" class="form-label">Pilih Dosen Terdaftar:</label>
          <select id="user_id" name="user_id" class="form-control" required>
            <option value="">— Pilih Dosen —</option>
            <?php foreach ($allDosen as $u): ?>
            <?php if ($u['prodi_id'] == $prodi['id']) continue; ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nama_lengkap']) ?> (<?= htmlspecialchars($u['email'] ?? '') ?>)<?= $u['prodi_id'] ? ' [Pindah dari prodi lain]' : '' ?></option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">Dosen yang sudah terikat di prodi lain akan dipindahkan ke prodi ini.</div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;">
          + Tambahkan ke Prodi
        </button>
      </div>

      <div class="card-footer" style="background:var(--bg-main);text-align:center;">
        <a href="<?= BASE_URL ?>/users/create" class="btn btn-outline btn-sm" style="width:100%;">
          + Daftarkan Akun Dosen Baru
        </a>
      </div>
    </form>
  </div>

</div>
