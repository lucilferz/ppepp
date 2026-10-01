<?php
$pageTitle   = 'Detail Prodi: ' . htmlspecialchars($prodi['nama']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Admin', 'url' => BASE_URL . '/admin'],
  ['label' => htmlspecialchars($prodi['nama'])],
];

$jenjangLabel = ['D3' => 'D-III', 'S1' => 'S-1', 'S2' => 'S-2', 'S3' => 'S-3'];
$dosenProdi   = $prodi['dosen'] ?? [];

// Dosen yang belum ada di prodi ini (untuk dropdown tambah)
$dosenLain = array_filter($allDosen, fn($u) => $u['prodi_id'] !== $prodi['id'] || $u['prodi_id'] === null);
$roleBadge = [
  'dekan'   => ['bg'=>'#dbeafe','text'=>'#1d4ed8','label'=>'👑 Dekan'],
  'kaprodi' => ['bg'=>'#ede9fe','text'=>'#6d28d9','label'=>'🎓 Kaprodi'],
  'dosen'   => ['bg'=>'#f1f5f9','text'=>'#475569','label'=>'🧑‍🏫 Dosen'],
];
?>

<!-- Header -->
<div style="background:linear-gradient(135deg,#0f172a,#0c4a6e);border-radius:16px;padding:26px 32px;margin-bottom:24px;color:#fff;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;box-shadow:0 8px 28px rgba(7,89,133,0.2);">
  <div style="flex:1;min-width:240px;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;flex-wrap:wrap;">
      <span style="background:#0284c7;color:#fff;font-weight:900;font-size:15px;padding:5px 14px;border-radius:8px;"><?= htmlspecialchars($prodi['kode']) ?></span>
      <span style="background:rgba(255,255,255,0.15);color:#7dd3fc;font-size:12px;font-weight:700;padding:4px 12px;border-radius:20px;"><?= $jenjangLabel[$prodi['jenjang']] ?? $prodi['jenjang'] ?></span>
      <span style="background:<?= $prodi['aktif'] ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' ?>;color:<?= $prodi['aktif'] ? '#6ee7b7' : '#fca5a5' ?>;font-size:12px;font-weight:700;padding:4px 12px;border-radius:20px;">
        <?= $prodi['aktif'] ? '✓ Aktif' : '✕ Nonaktif' ?>
      </span>
    </div>
    <h2 style="font-size:24px;font-weight:900;color:#fff;margin:0 0 6px;"><?= htmlspecialchars($prodi['nama']) ?></h2>
    <?php if (!empty($prodi['deskripsi'])): ?>
    <p style="font-size:13px;color:rgba(255,255,255,0.7);margin:0;"><?= htmlspecialchars($prodi['deskripsi']) ?></p>
    <?php endif; ?>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap;flex-shrink:0;">
    <a href="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>/edit"
       style="display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,0.12);border:1.5px solid rgba(255,255,255,0.25);color:#fff;font-weight:700;font-size:13px;padding:10px 18px;border-radius:10px;text-decoration:none;">
      ✏️ Edit Prodi
    </a>
    <a href="<?= BASE_URL ?>/admin"
       style="display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,0.07);border:1.5px solid rgba(255,255,255,0.15);color:rgba(255,255,255,0.8);font-size:13px;padding:10px 16px;border-radius:10px;text-decoration:none;">
      ← Kembali ke Admin
    </a>
  </div>
</div>

<?php if (!empty($flash['message'])): ?>
<div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;<?= $flash['type']==='success' ? 'background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;' : 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;' ?>">
  <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<!-- Stats -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin-bottom:24px;">
  <div style="background:#fff;border:1.5px solid #bae6fd;border-radius:14px;padding:18px 20px;text-align:center;">
    <div style="font-size:32px;font-weight:900;color:#0284c7;"><?= count($dosenProdi) ?></div>
    <div style="font-size:12px;color:#0284c7;font-weight:700;">Total Dosen</div>
  </div>
  <?php
    $cntKaprodi = count(array_filter($dosenProdi, fn($u) => $u['role'] === 'kaprodi'));
    $cntDosen   = count(array_filter($dosenProdi, fn($u) => $u['role'] === 'dosen'));
    $cntApiKey  = count(array_filter($dosenProdi, fn($u) => !empty($u['gemini_api_key'])));
  ?>
  <div style="background:#fff;border:1.5px solid #ddd6fe;border-radius:14px;padding:18px 20px;text-align:center;">
    <div style="font-size:32px;font-weight:900;color:#6d28d9;"><?= $cntKaprodi ?></div>
    <div style="font-size:12px;color:#6d28d9;font-weight:700;">Kaprodi</div>
  </div>
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:18px 20px;text-align:center;">
    <div style="font-size:32px;font-weight:900;color:#475569;"><?= $cntDosen ?></div>
    <div style="font-size:12px;color:#64748b;font-weight:700;">Dosen</div>
  </div>
  <div style="background:<?= $cntApiKey === count($dosenProdi) ? '#ecfdf5' : '#fff7ed' ?>;border:1.5px solid <?= $cntApiKey === count($dosenProdi) ? '#a7f3d0' : '#fed7aa' ?>;border-radius:14px;padding:18px 20px;text-align:center;">
    <div style="font-size:32px;font-weight:900;color:<?= $cntApiKey === count($dosenProdi) ? '#059669' : '#c2410c' ?>;"><?= $cntApiKey ?>/<?= count($dosenProdi) ?></div>
    <div style="font-size:12px;color:<?= $cntApiKey === count($dosenProdi) ? '#059669' : '#ea580c' ?>;font-weight:700;">Sudah Isi API Key</div>
  </div>
</div>

<!-- Dosen List + Add Form -->
<div style="display:grid;grid-template-columns:1fr 320px;gap:18px;align-items:start;" class="prodi-grid">

  <!-- Dosen List -->
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,0.04);">
    <div style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);padding:16px 22px;border-bottom:1.5px solid #bae6fd;display:flex;align-items:center;justify-content:space-between;">
      <h3 style="font-size:15px;font-weight:800;color:#0c4a6e;margin:0;">👥 Dosen Prodi <?= htmlspecialchars($prodi['nama']) ?></h3>
    </div>
    <?php if (empty($dosenProdi)): ?>
    <div style="padding:32px;text-align:center;color:#94a3b8;font-style:italic;">
      <div style="font-size:28px;margin-bottom:8px;">👤</div>
      Belum ada dosen di prodi ini. Tambahkan dosen menggunakan form di samping.
    </div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;">
      <?php foreach ($dosenProdi as $idx => $d): ?>
      <?php $rb = $roleBadge[$d['role']] ?? $roleBadge['dosen']; ?>
      <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 22px;<?= $idx % 2 === 0 ? 'background:#fafafa;' : '' ?>border-bottom:1px solid #f1f5f9;gap:12px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:200px;">
          <?php if (!empty($d['avatar_url'])): ?>
          <img src="<?= htmlspecialchars($d['avatar_url']) ?>" alt="" width="36" height="36"
               style="border-radius:50%;object-fit:cover;border:2px solid #bae6fd;flex-shrink:0;">
          <?php else: ?>
          <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0284c7,#0ea5e9);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:14px;flex-shrink:0;">
            <?= strtoupper(mb_substr(explode(' ', $d['nama_lengkap'] ?: 'U')[0], 0, 1)) ?>
          </div>
          <?php endif; ?>
          <div>
            <div style="font-weight:800;font-size:13.5px;color:#0f172a;"><?= htmlspecialchars($d['nama_lengkap']) ?></div>
            <div style="font-size:12px;color:#94a3b8;"><?= htmlspecialchars($d['email'] ?? '') ?></div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
          <span style="background:<?= $rb['bg'] ?>;color:<?= $rb['text'] ?>;font-size:11.5px;font-weight:700;padding:3px 10px;border-radius:20px;">
            <?= $rb['label'] ?>
          </span>
          <?php if (!empty($d['gemini_api_key'])): ?>
          <span style="background:#ecfdf5;color:#059669;font-size:11px;font-weight:700;padding:3px 8px;border-radius:20px;">🔑</span>
          <?php else: ?>
          <span style="background:#fef2f2;color:#dc2626;font-size:11px;font-weight:700;padding:3px 8px;border-radius:20px;">⚠️</span>
          <?php endif; ?>
          <form action="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>/remove-dosen" method="POST" style="margin:0;"
                onsubmit="return confirm('Lepas <?= htmlspecialchars(addslashes($d['nama_lengkap'])) ?> dari prodi ini?')">
            <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
            <button type="submit" title="Lepas dari prodi"
                    style="display:inline-flex;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-size:11px;padding:4px 8px;border-radius:6px;cursor:pointer;">
              ✕
            </button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Add Dosen Panel -->
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,0.04);position:sticky;top:80px;">
    <div style="background:linear-gradient(135deg,#f0fdf4,#ecfdf5);padding:16px 20px;border-bottom:1.5px solid #a7f3d0;">
      <h3 style="font-size:14px;font-weight:800;color:#065f46;margin:0;">+ Tambah Dosen ke Prodi</h3>
    </div>
    <form action="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>/add-dosen" method="POST" style="padding:18px;display:flex;flex-direction:column;gap:12px;">
      <div>
        <label style="font-size:12.5px;font-weight:800;color:#1e293b;display:block;margin-bottom:6px;">Pilih Dosen:</label>
        <select name="user_id" class="form-control" required style="font-size:13px;padding:9px 12px;border-radius:9px;">
          <option value="">— Pilih Dosen —</option>
          <?php foreach ($allDosen as $u): ?>
          <?php if ($u['prodi_id'] == $prodi['id']) continue; // sudah di prodi ini ?>
          <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nama_lengkap']) ?> (<?= htmlspecialchars($u['email'] ?? '') ?>)<?= $u['prodi_id'] ? ' [Pindah dari prodi lain]' : '' ?></option>
          <?php endforeach; ?>
        </select>
        <div style="font-size:11px;color:#94a3b8;margin-top:4px;">Dosen yang sudah di prodi lain akan dipindahkan.</div>
      </div>
      <button type="submit" class="btn btn-primary" style="font-size:13px;padding:10px;border-radius:9px;">
        + Tambahkan ke Prodi
      </button>
    </form>

    <div style="padding:0 18px 18px;">
      <div style="border-top:1px solid #e2e8f0;padding-top:14px;">
        <a href="<?= BASE_URL ?>/users/create"
           style="display:flex;align-items:center;justify-content:center;gap:7px;background:#f8fafc;color:#334155;font-size:12.5px;font-weight:700;padding:9px;border-radius:9px;text-decoration:none;border:1px solid #e2e8f0;">
          + Tambah Dosen Baru ke Sistem
        </a>
      </div>
    </div>
  </div>
</div>

<style>
@media (max-width: 768px) {
  .prodi-grid { grid-template-columns: 1fr !important; }
}
</style>
