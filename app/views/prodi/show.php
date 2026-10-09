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
  'dekan'   => ['bg'=>'#dbeafe','text'=>'#1d4ed8','label'=>'Dekan'],
  'kaprodi' => ['bg'=>'#ede9fe','text'=>'#6d28d9','label'=>'Kaprodi'],
  'dosen'   => ['bg'=>'#f1f5f9','text'=>'#475569','label'=>'Dosen'],
];
?>

<!-- Header -->
<div style="background:var(--primary);border-radius:12px;padding:26px 30px;margin-bottom:24px;color:#fff;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
  <div style="flex:1;min-width:240px;">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap;">
      <span style="background:rgba(255,255,255,0.2);color:#fff;font-weight:800;font-size:14px;padding:4px 12px;border-radius:6px;"><?= htmlspecialchars($prodi['kode']) ?></span>
      <span style="background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.9);font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;"><?= $jenjangLabel[$prodi['jenjang']] ?? $prodi['jenjang'] ?></span>
      <span style="background:<?= $prodi['aktif'] ? 'rgba(16,185,129,0.25)' : 'rgba(239,68,68,0.25)' ?>;color:<?= $prodi['aktif'] ? '#6ee7b7' : '#fca5a5' ?>;font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;">
        <?= $prodi['aktif'] ? 'Aktif' : 'Nonaktif' ?>
      </span>
    </div>
    <h2 style="font-size:22px;font-weight:800;color:#fff;margin:0 0 6px;"><?= htmlspecialchars($prodi['nama']) ?></h2>
    <?php if (!empty($prodi['deskripsi'])): ?>
    <p style="font-size:13px;color:rgba(255,255,255,0.75);margin:0;"><?= htmlspecialchars($prodi['deskripsi']) ?></p>
    <?php endif; ?>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap;flex-shrink:0;">
    <a href="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>/edit" class="btn btn-outline"
       style="background:rgba(255,255,255,0.15);border-color:rgba(255,255,255,0.3);color:#fff;font-weight:600;font-size:13px;padding:9px 16px;border-radius:8px;text-decoration:none;">
      Edit Prodi
    </a>
    <a href="<?= BASE_URL ?>/admin" class="btn btn-outline"
       style="background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.2);color:rgba(255,255,255,0.85);font-size:13px;padding:9px 14px;border-radius:8px;text-decoration:none;">
      ← Kembali ke Admin
    </a>
  </div>
</div>

<?php if (!empty($flash['message'])): ?>
<div style="margin-bottom:16px;padding:12px 16px;border-radius:8px;font-size:13.5px;font-weight:600;<?= $flash['type']==='success' ? 'background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;' : 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;' ?>">
  <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<!-- Stats -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin-bottom:24px;">
  <div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:18px 20px;text-align:center;">
    <div style="font-size:28px;font-weight:800;color:var(--primary);"><?= count($dosenProdi) ?></div>
    <div style="font-size:12px;color:var(--text-muted);font-weight:600;">Total Dosen</div>
  </div>
  <?php
    $cntKaprodi = count(array_filter($dosenProdi, fn($u) => $u['role'] === 'kaprodi'));
    $cntDosen   = count(array_filter($dosenProdi, fn($u) => $u['role'] === 'dosen'));
    $cntApiKey  = count(array_filter($dosenProdi, fn($u) => !empty($u['gemini_api_key'])));
  ?>
  <div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:18px 20px;text-align:center;">
    <div style="font-size:28px;font-weight:800;color:#6d28d9;"><?= $cntKaprodi ?></div>
    <div style="font-size:12px;color:#6d28d9;font-weight:600;">Kaprodi</div>
  </div>
  <div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:18px 20px;text-align:center;">
    <div style="font-size:28px;font-weight:800;color:var(--text-muted);"><?= $cntDosen ?></div>
    <div style="font-size:12px;color:var(--text-muted);font-weight:600;">Dosen</div>
  </div>
  <div style="background:<?= $cntApiKey === count($dosenProdi) ? '#ecfdf5' : '#fff7ed' ?>;border:1.5px solid <?= $cntApiKey === count($dosenProdi) ? '#a7f3d0' : '#fed7aa' ?>;border-radius:12px;padding:18px 20px;text-align:center;">
    <div style="font-size:28px;font-weight:800;color:<?= $cntApiKey === count($dosenProdi) ? '#059669' : '#c2410c' ?>;"><?= $cntApiKey ?>/<?= count($dosenProdi) ?></div>
    <div style="font-size:12px;color:<?= $cntApiKey === count($dosenProdi) ? '#059669' : '#ea580c' ?>;font-weight:600;">Sudah Isi API Key</div>
  </div>
</div>

<!-- Dosen List + Add Form -->
<div style="display:grid;grid-template-columns:1fr 320px;gap:18px;align-items:start;" class="prodi-grid">

  <!-- Dosen List -->
  <div class="card" style="border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
    <div style="background:#f8fafc;padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
      <h3 style="font-size:15px;font-weight:700;color:var(--text-main);margin:0;">Dosen Prodi <?= htmlspecialchars($prodi['nama']) ?></h3>
    </div>
    <?php if (empty($dosenProdi)): ?>
    <div style="padding:32px;text-align:center;color:var(--text-muted);font-style:italic;">
      Belum ada dosen di prodi ini. Tambahkan dosen menggunakan form di samping.
    </div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;">
      <?php foreach ($dosenProdi as $idx => $d): ?>
      <?php $rb = $roleBadge[$d['role']] ?? $roleBadge['dosen']; ?>
      <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;<?= $idx % 2 === 0 ? 'background:#fafafa;' : '' ?>border-bottom:1px solid #f1f5f9;gap:12px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:200px;">
          <?php if (!empty($d['avatar_url'])): ?>
          <img src="<?= htmlspecialchars($d['avatar_url']) ?>" alt="" width="36" height="36"
               style="border-radius:50%;object-fit:cover;border:1.5px solid var(--border);flex-shrink:0;">
          <?php else: ?>
          <div style="width:36px;height:36px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
            <?= strtoupper(mb_substr(explode(' ', $d['nama_lengkap'] ?: 'U')[0], 0, 1)) ?>
          </div>
          <?php endif; ?>
          <div>
            <div style="font-weight:700;font-size:13.5px;color:var(--text-main);"><?= htmlspecialchars($d['nama_lengkap']) ?></div>
            <div style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($d['email'] ?? '') ?></div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
          <span style="background:<?= $rb['bg'] ?>;color:<?= $rb['text'] ?>;font-size:11.5px;font-weight:600;padding:3px 10px;border-radius:20px;">
            <?= $rb['label'] ?>
          </span>
          <?php if (!empty($d['gemini_api_key'])): ?>
          <span style="background:#ecfdf5;color:#059669;font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;border:1px solid #a7f3d0;" title="API Key Gemini terpasang">API Key OK</span>
          <?php else: ?>
          <span style="background:#fef2f2;color:#dc2626;font-size:11px;font-weight:600;padding:3px 8px;border-radius:6px;border:1px solid #fecaca;" title="API Key Gemini belum diset">No API Key</span>
          <?php endif; ?>
          <form action="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>/remove-dosen" method="POST" style="margin:0;"
                onsubmit="return confirm('Lepas <?= htmlspecialchars(addslashes($d['nama_lengkap'])) ?> dari prodi ini?')">
            <input type="hidden" name="user_id" value="<?= $d['id'] ?>">
            <button type="submit" title="Lepas dari prodi" class="btn btn-sm btn-danger"
                    style="font-size:11px;padding:3px 7px;border-radius:6px;">
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
  <div class="card" style="border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.04);position:sticky;top:80px;">
    <div style="background:#f8fafc;padding:14px 18px;border-bottom:1px solid var(--border);">
      <h3 style="font-size:14px;font-weight:700;color:var(--text-main);margin:0;">+ Tambah Dosen ke Prodi</h3>
    </div>
    <form action="<?= BASE_URL ?>/prodi/<?= $prodi['id'] ?>/add-dosen" method="POST" style="padding:18px;display:flex;flex-direction:column;gap:12px;">
      <div>
        <label style="font-size:12.5px;font-weight:700;color:var(--text-main);display:block;margin-bottom:6px;">Pilih Dosen:</label>
        <select name="user_id" class="form-control" required style="font-size:13px;padding:9px 12px;border-radius:8px;">
          <option value="">— Pilih Dosen —</option>
          <?php foreach ($allDosen as $u): ?>
          <?php if ($u['prodi_id'] == $prodi['id']) continue; // sudah di prodi ini ?>
          <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nama_lengkap']) ?> (<?= htmlspecialchars($u['email'] ?? '') ?>)<?= $u['prodi_id'] ? ' [Pindah dari prodi lain]' : '' ?></option>
          <?php endforeach; ?>
        </select>
        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Dosen yang sudah di prodi lain akan dipindahkan.</div>
      </div>
      <button type="submit" class="btn btn-primary" style="font-size:13px;padding:9px 14px;border-radius:8px;">
        + Tambahkan ke Prodi
      </button>
    </form>

    <div style="padding:0 18px 18px;">
      <div style="border-top:1px solid var(--border);padding-top:14px;">
        <a href="<?= BASE_URL ?>/users/create" class="btn btn-outline"
           style="display:flex;align-items:center;justify-content:center;gap:6px;font-size:12.5px;font-weight:600;padding:8px;border-radius:8px;width:100%;">
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
