<?php
$pageTitle   = 'Dokumen Tersembunyi — ' . htmlspecialchars($project['ta_nama']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Project PPEPP', 'url' => BASE_URL . '/ppepp'],
  ['label' => htmlspecialchars($project['ta_nama']), 'url' => BASE_URL . '/ppepp/' . $project['id']],
  ['label' => 'Dokumen Tersembunyi'],
];

$stages = [
  ['letter'=>'P','nama'=>'Penetapan',  'key'=>'penetapan',   'route'=>'penetapan',   'color'=>['#1a237e','#3f51b5','#eff6ff','#1d4ed8']],
  ['letter'=>'P','nama'=>'Pelaksanaan','key'=>'pelaksanaan', 'route'=>'pelaksanaan', 'color'=>['#064e3b','#059669','#ecfdf5','#047857']],
  ['letter'=>'E','nama'=>'Evaluasi',   'key'=>'evaluasi',    'route'=>'evaluasi',    'color'=>['#4c1d95','#7c3aed','#f5f3ff','#6d28d9']],
  ['letter'=>'P','nama'=>'Pengendalian','key'=>'pengendalian','route'=>'pengendalian','color'=>['#7c2d12','#ea580c','#fff7ed','#c2410c']],
  ['letter'=>'P','nama'=>'Peningkatan','key'=>'peningkatan', 'route'=>'peningkatan', 'color'=>['#065f46','#10b981','#ecfdf5','#059669']],
];

$dataMaps = [
  'penetapan'   => $penetapans   ?? [],
  'pelaksanaan' => $pelaksanaans ?? [],
  'evaluasi'    => $evaluasis    ?? [],
  'pengendalian'=> $pengendalians ?? [],
  'peningkatan' => $peningkatans  ?? [],
];

$totalHidden = 0;
foreach ($dataMaps as $k => $list) {
    $totalHidden += count(array_filter($list, fn($x) => ($x['visibility_status'] ?? '') === 'hidden'));
}
?>

<!-- Header -->
<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
  <a href="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>" class="btn btn-outline"
     style="background:#fff;border:1.5px solid #cbd5e1;color:#334155;font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:10px;">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    ← Kembali ke Project PPEPP
  </a>
</div>

<!-- Header Banner -->
<div style="background:linear-gradient(135deg,#334155 0%,#1e293b 100%);border-radius:16px;padding:26px 30px;margin-bottom:24px;color:#fff;">
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,255,255,0.6);">
      Arsip &amp; Visibilitas
    </div>
    <span style="font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;background:rgba(234,179,8,0.25);color:#fde047;border:1px solid rgba(253,224,71,0.4);">
      📁 Dokumen Tersembunyi (Hide)
    </span>
  </div>
  <h2 style="font-size:24px;font-weight:900;color:#fff;margin-bottom:4px;letter-spacing:-0.5px;">
    Dokumen Tersembunyi — Tahun Ajaran <?= htmlspecialchars($project['ta_nama']) ?>
  </h2>
  <p style="font-size:13.5px;color:rgba(255,255,255,0.75);margin:0;max-width:700px;line-height:1.6;">
    Halaman ini memuat seluruh dokumen yang telah Anda sembunyikan (Hide). Anda dapat mengklik tombol <strong>"Aktifkan Kembali"</strong> agar dokumen muncul kembali di siklus PPEPP utama.
  </p>
</div>

<?php if (!empty($flash['message'])): ?>
<div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;<?= $flash['type']==='success' ? 'background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;' : 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;' ?>">
  <?= $flash['type']==='success' ? '✓' : '✕' ?> <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<?php if ($totalHidden === 0): ?>
<!-- Empty State -->
<div style="text-align:center;padding:56px 24px;background:#fff;border:1.5px dashed #cbd5e1;border-radius:16px;">
  <div style="width:64px;height:64px;background:#f8fafc;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
    <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" width="32" height="32"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
  </div>
  <h3 style="font-size:17px;font-weight:700;color:var(--text-main);margin-bottom:6px;">Tidak Ada Dokumen yang Disembunyikan</h3>
  <p style="font-size:13.5px;color:var(--text-muted);max-width:440px;margin:0 auto 20px;">
    Semua dokumen pada Tahun Ajaran ini sedang aktif atau nonaktif terlihat di halaman utama project.
  </p>
  <a href="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>" class="btn btn-primary" style="font-size:13px;">
    Buka Siklus Project PPEPP
  </a>
</div>
<?php else: ?>

<!-- List Hidden Documents per Stage -->
<div style="display:flex;flex-direction:column;gap:18px;">
  <?php foreach ($stages as $stageIdx => $stg): 
    $items = $dataMaps[$stg['key']] ?? [];
    $hiddenItems = array_filter($items, fn($x) => ($x['visibility_status'] ?? '') === 'hidden');
    if (empty($hiddenItems)) continue;
    [$dk, $lc, $bg, $tc] = $stg['color'];
  ?>
  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.03);">
    <!-- Stage Header -->
    <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
      <div style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:15px;flex-shrink:0;background:linear-gradient(135deg,<?= $dk ?>,<?= $lc ?>);color:#fff;">
        <?= $stg['letter'] ?>
      </div>
      <div style="flex:1;">
        <span style="font-size:15px;font-weight:800;color:var(--text-main);">
          <?= ($stageIdx + 1) ?>. <?= $stg['nama'] ?>
        </span>
        <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;background:#fef08a;color:#854d0e;margin-left:8px;">
          <?= count($hiddenItems) ?> dokumen tersembunyi
        </span>
      </div>
    </div>

    <!-- Hidden Documents Rows -->
    <div>
      <?php foreach ($hiddenItems as $hiIdx => $item): 
        $judul = htmlspecialchars($item['judul'] ?? '—');
        $isFinal = ($item['status'] ?? '') === 'final';
      ?>
      <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;background:<?= $hiIdx % 2 === 0 ? '#fff' : '#fcfdfe' ?>;<?= $hiIdx < count($hiddenItems) - 1 ? 'border-bottom:1px solid #f1f5f9;' : '' ?>">
        <div style="width:8px;height:8px;border-radius:50%;background:#94a3b8;flex-shrink:0;"></div>
        <div style="flex:1;min-width:0;">
          <div style="font-size:14px;font-weight:700;color:var(--text-main);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            <?= $judul ?>
          </div>
          <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
            Status Asli: <span style="font-weight:600;color:<?= $isFinal ? '#059669' : '#d97706' ?>;"><?= $isFinal ? 'Final' : 'Draft' ?></span>
            &nbsp;·&nbsp; Dibuat: <?= date('d M Y', strtotime($item['created_at'])) ?>
          </div>
        </div>

        <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;background:#fef08a;color:#854d0e;flex-shrink:0;">
          Tersembunyi (Hide)
        </span>

        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
          <a href="<?= BASE_URL ?>/<?= $stg['route'] ?>/<?= $item['id'] ?>" class="btn btn-sm btn-outline" style="padding:6px 12px;font-size:12px;font-weight:700;">
            Lihat
          </a>
          <!-- Form Pulihkan / Aktifkan Kembali -->
          <form method="POST" action="<?= BASE_URL ?>/<?= $stg['route'] ?>/<?= $item['id'] ?>/toggle-status" style="margin:0;">
            <input type="hidden" name="visibility_status" value="aktif">
            <input type="hidden" name="return_url" value="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/hidden">
            <button type="submit" class="btn btn-sm btn-primary"
                    style="padding:6px 14px;background:#16a34a;border:none;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:5px;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polygon points="5 3 19 12 5 21 5 3"/></svg>
              Aktifkan Kembali
            </button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
