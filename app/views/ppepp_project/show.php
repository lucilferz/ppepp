<?php
$pageTitle   = htmlspecialchars($project['judul'] ?? 'Project PPEPP');
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Project PPEPP', 'url' => BASE_URL . '/ppepp'],
  ['label' => htmlspecialchars($project['ta_nama'])],
];

$steps = [
  ['letter'=>'P','nama'=>'Penetapan',  'sub'=>'Standar & Target', 'key'=>'penetapan',   'route'=>'penetapan',   'color'=>['#1a237e','#3f51b5','#eff6ff','#1d4ed8']],
  ['letter'=>'P','nama'=>'Pelaksanaan','sub'=>'Bukti & Realisasi','key'=>'pelaksanaan', 'route'=>'pelaksanaan', 'color'=>['#064e3b','#059669','#ecfdf5','#047857']],
  ['letter'=>'E','nama'=>'Evaluasi',   'sub'=>'Capaian & Audit',  'key'=>'evaluasi',    'route'=>'evaluasi',    'color'=>['#4c1d95','#7c3aed','#f5f3ff','#6d28d9']],
  ['letter'=>'P','nama'=>'Pengendalian','sub'=>'RTL & Notulensi', 'key'=>'pengendalian','route'=>'pengendalian','color'=>['#7c2d12','#ea580c','#fff7ed','#c2410c']],
  ['letter'=>'P','nama'=>'Peningkatan','sub'=>'SK & Target Baru', 'key'=>'peningkatan', 'route'=>'peningkatan', 'color'=>['#065f46','#10b981','#ecfdf5','#059669']],
];

$dataMaps = [
  'penetapan'   => $penetapans   ?? [],
  'pelaksanaan' => $pelaksanaans ?? [],
  'evaluasi'    => $evaluasis    ?? [],
  'pengendalian'=> $pengendalians ?? [],
  'peningkatan' => $peningkatans  ?? [],
];

$kriteriaMap = [];
foreach ($allKriteria ?? [] as $ak) {
  $kriteriaMap[(int)$ak['id']] = $ak;
}
?>

<!-- Back Button to Project List -->
<div style="margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;">
  <a href="<?= BASE_URL ?>/ppepp" class="btn btn-outline"
     style="background:#fff;border:1.5px solid #cbd5e1;color:#334155;font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    ← Kembali ke Daftar Project
  </a>
</div>

<!-- Project Header Banner -->
<div style="background:linear-gradient(135deg,#0f172a 0%,#1a237e 60%,#283593 100%);border-radius:16px;padding:28px 32px;margin-bottom:24px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:20px;">
  <div>
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
      <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,255,255,0.6);">
        Project PPEPP
      </div>
      <?php
      $projStatus = $project['status'] ?? 'aktif';
      $badgeStyle = match($projStatus) {
          'aktif'    => 'background:rgba(34,197,94,0.25);color:#86efac;border:1px solid rgba(134,239,172,0.4);',
          'nonaktif' => 'background:rgba(234,179,8,0.25);color:#fde047;border:1px solid rgba(253,224,71,0.4);',
          'arsip'    => 'background:rgba(148,163,184,0.25);color:#cbd5e1;border:1px solid rgba(203,213,225,0.4);',
          'selesai'  => 'background:rgba(59,130,246,0.25);color:#93c5fd;border:1px solid rgba(147,197,253,0.4);',
          default    => 'background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);',
      };
      $badgeLabel = match($projStatus) {
          'aktif'    => 'Aktif',
          'nonaktif' => 'Nonaktif / Tersembunyi',
          'arsip'    => 'Arsip',
          'selesai'  => 'Selesai',
          default    => ucfirst($projStatus),
      };
      ?>
      <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;<?= $badgeStyle ?>">
        ● <?= $badgeLabel ?>
      </span>
    </div>
    <h2 style="font-size:28px;font-weight:900;color:#fff;margin-bottom:4px;letter-spacing:-0.5px;">
      Tahun Ajaran <?= htmlspecialchars($project['ta_nama']) ?>
    </h2>
    <p style="font-size:14px;color:rgba(255,255,255,0.7);"><?= htmlspecialchars($project['judul'] ?? '') ?></p>
    <?php if (!empty($project['deskripsi'])): ?>
    <p style="font-size:13px;color:rgba(255,255,255,0.55);margin-top:6px;font-style:italic;"><?= htmlspecialchars($project['deskripsi']) ?></p>
    <?php endif; ?>

    <!-- Progress -->
    <div style="margin-top:18px;display:flex;align-items:center;gap:14px;">
      <div style="font-size:13px;color:rgba(255,255,255,0.75);">
        <strong style="color:#fbbf24;"><?= $project['steps_done'] ?>/5</strong> tahap telah dimulai
      </div>
      <div style="flex:1;max-width:160px;height:8px;background:rgba(255,255,255,0.15);border-radius:99px;overflow:hidden;">
        <div style="height:100%;width:<?= $project['progress_pct'] ?>%;background:linear-gradient(90deg,#fbbf24,#f59e0b);border-radius:99px;"></div>
      </div>
      <span style="font-size:12px;color:#fbbf24;font-weight:700;"><?= $project['progress_pct'] ?>%</span>
    </div>
  </div>
  <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0;">
    <a href="<?= BASE_URL ?>/laporan?project_id=<?= $project['id'] ?>" class="btn btn-outline" style="background:rgba(255,255,255,0.18);border-color:rgba(255,255,255,0.35);color:#fff;font-weight:800;display:flex;align-items:center;gap:6px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      📊 Buka Laporan Eksekutif
    </a>
    <div style="display:flex;gap:8px;">
      <a href="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/hidden" class="btn btn-outline" style="background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.3);color:#fff;font-size:12.5px;font-weight:700;display:flex;align-items:center;gap:5px;flex:1;justify-content:center;" title="Kelola Dokumen yang Disembunyikan (Hide)">
        📁 Tersembunyi (<?= (int)($totalHiddenCount ?? 0) ?>)
      </a>
      <a href="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/edit" class="btn btn-outline" style="background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.2);color:#fff;font-size:12.5px;" title="Edit Informasi Project">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit
      </a>
      <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/destroy" style="margin:0;"
            onsubmit="return confirm('⚠️ KONFIRMASI HAPUS PERMANEN:\n\nApakah Anda YAKIN ingin menghapus project \'<?= htmlspecialchars(addslashes($project['judul'])) ?>\' (TA <?= htmlspecialchars(addslashes($project['ta_nama'])) ?>)?\n\nSELURUH DATA 5 TAHAP PPEPP (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, & Peningkatan) di dalamnya akan TERHAPUS TOTAL & PERMANEN!');">
        <button type="submit" class="btn"
                style="background:rgba(239,68,68,0.25);border:1px solid rgba(248,113,113,0.5);color:#fca5a5;font-size:12.5px;font-weight:700;display:flex;align-items:center;gap:5px;cursor:pointer;padding:8px 12px;border-radius:9px;"
                title="Hapus Project Permanen Beserta Seluruh Isinya">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
          Hapus
        </button>
      </form>
    </div>
  </div>
</div>

<?php if (!empty($flash['message'])): ?>
<div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;<?= $flash['type']==='success' ? 'background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;' : 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;' ?>">
  <?= $flash['type']==='success' ? '✓' : '✕' ?> <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<!-- FILTER CONTROL BAR DOKUMEN & TAHAP PROJECT -->
<div class="card mb-4" style="border-radius:14px;box-shadow:0 4px 18px rgba(0,0,0,0.04);border:1.5px solid #cbd5e1;padding:20px 24px;margin-bottom:24px;background:#fff;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:14px;border-bottom:1px solid #f1f5f9;padding-bottom:12px;">
    <div style="display:flex;align-items:center;gap:10px;">
      <div style="width:34px;height:34px;background:linear-gradient(135deg,#1a237e,#3f51b5);color:#fff;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 2px 8px rgba(26,35,126,0.2);">
        🔍
      </div>
      <div>
        <h3 style="font-size:16px;font-weight:800;color:#1e293b;margin:0 0 2px;">Filter Dokumen Project Ini</h3>
        <p style="font-size:12px;color:#64748b;margin:0;">Filter tampilan dokumen berdasarkan <strong>Tahap PPEPP</strong>, <strong>Kriteria SPMI</strong>, atau <strong>Status Dokumen</strong>.</p>
      </div>
    </div>
    <button type="button" onclick="resetProjectFilters()" class="btn btn-sm btn-outline" style="font-size:12px;color:#ef4444;border-color:#fecaca;font-weight:700;">
      🔄 Reset Filter
    </button>
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:14px;align-items:center;">
    
    <!-- 1. Filter Tahap PPEPP -->
    <div>
      <label style="font-size:12px;font-weight:700;color:#1e293b;margin:0 0 5px;display:block;">Tahap PPEPP:</label>
      <select id="filterStageSelect" onchange="applyProjectFilters()" class="form-control" style="font-size:13px;">
        <option value="all">Semua Tahap (5 Tahap)</option>
        <option value="penetapan">P1 — Penetapan Standar</option>
        <option value="pelaksanaan">P2 — Pelaksanaan Standar</option>
        <option value="evaluasi">E  — Evaluasi Diri</option>
        <option value="pengendalian">P3 — Pengendalian (RTL)</option>
        <option value="peningkatan">P4 — Peningkatan Standar</option>
      </select>
    </div>

    <!-- 2. Filter Kriteria -->
    <div>
      <label style="font-size:12px;font-weight:700;color:#1e293b;margin:0 0 5px;display:block;">Kriteria SPMI / Prodi:</label>
      <select id="filterKriteriaSelect" onchange="applyProjectFilters()" class="form-control" style="font-size:13px;">
        <option value="all">Semua Kriteria</option>
        <?php foreach ($allKriteria ?? [] as $ak): ?>
        <option value="<?= $ak['id'] ?>">
          <?= htmlspecialchars(($ak['kode'] ? '[' . $ak['kode'] . '] ' : '') . $ak['nama']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- 3. Filter Status Dokumen -->
    <div>
      <label style="font-size:12px;font-weight:800;color:#1e293b;margin:0 0 5px;display:block;">⚡ Status Dokumen:</label>
      <select id="filterStatusSelect" onchange="applyProjectFilters()" class="form-control" style="font-size:13px;font-weight:700;border-color:#a5b4fc;background:#f8faff;">
        <option value="all">✨ Semua Status</option>
        <option value="draft">🟡 Draft Saja</option>
        <option value="final">🟢 Final Saja</option>
        <option value="nonaktif">⚠️ Nonaktif Saja</option>
      </select>
    </div>

  </div>
</div>

<!-- 5 Steps Container -->
<div style="display:flex;flex-direction:column;gap:18px;">
<?php foreach ($steps as $stepIdx => $step): 
  $items          = $dataMaps[$step['key']];
  $visibleItems   = array_values(array_filter($items, fn($x) => ($x['visibility_status'] ?? 'aktif') !== 'hidden'));
  $hasVisibleData = !empty($visibleItems);
  $activeItems    = array_filter($visibleItems, fn($x) => ($x['visibility_status'] ?? 'aktif') === 'aktif');
  $final          = count(array_filter($activeItems, fn($x) => ($x['status'] ?? '') === 'final'));
  $total          = count($activeItems);
  $isDone         = $final > 0;
  [$dk, $lc, $bg, $tc] = $step['color'];
?>
  <div class="step-card-block" data-stage="<?= $step['key'] ?>" style="background:#fff;border:1.5px solid <?= $hasVisibleData ? '#e2e8f0' : '#f1f5f9' ?>;border-radius:14px;overflow:hidden;">
    <!-- Tahap Header -->
    <div style="display:flex;align-items:center;gap:16px;padding:18px 24px;background:<?= $hasVisibleData ? '#f8faff' : '#fafbfc' ?>;">
      <!-- Step Number Badge -->
      <div style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:18px;flex-shrink:0;background:linear-gradient(135deg,<?= $dk ?>,<?= $lc ?>);color:#fff;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
        <?= $step['letter'] ?>
      </div>
      <!-- Step Info -->
      <div style="flex:1;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:2px;">
          <span style="font-size:16px;font-weight:800;color:var(--text-main);">
            <?= (string)($stepIdx + 1) ?>. <?= $step['nama'] ?>
          </span>
          <?php if ($isDone): ?>
          <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;color:#059669;background:#ecfdf5;">
            ✓ <?= $final ?> Final
          </span>
          <?php elseif ($total > 0): ?>
          <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;color:#d97706;background:#fffbeb;">
            <?= $total ?> Draft Aktif
          </span>
          <?php else: ?>
          <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;color:#94a3b8;background:#f1f5f9;">
            Belum dimulai
          </span>
          <?php endif; ?>
        </div>
        <div style="font-size:13px;color:var(--text-muted);"><?= $step['sub'] ?></div>
      </div>
      <!-- Action -->
      <a href="<?= BASE_URL ?>/<?= $step['route'] ?>/create?project_id=<?= $project['id'] ?>"
         style="padding:9px 18px;background:linear-gradient(135deg,<?= $dk ?>,<?= $lc ?>);color:#fff;border-radius:99px;font-size:13px;font-weight:700;text-decoration:none;white-space:nowrap;flex-shrink:0;display:flex;align-items:center;gap:6px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
        Tambah <?= $step['nama'] ?>
      </a>
    </div>

    <!-- Daftar Dokumen yang sudah ada (Aktif & Nonaktif yang tetap terlihat) -->
    <?php if ($hasVisibleData): ?>
    <div style="border-top:1px solid var(--border);">
      <?php foreach ($visibleItems as $ii => $item): 
        $isItemFinal  = ($item['status'] ?? '') === 'final';
        $itemVis      = $item['visibility_status'] ?? 'aktif';
        $isItemNonaktif = ($itemVis === 'nonaktif');
        $judul = htmlspecialchars($item['judul'] ?? '—');
        $acuan = htmlspecialchars($item['penetapan_judul'] ?? $item['evaluasi_judul'] ?? $item['pengendalian_judul'] ?? '');
      ?>
      <div class="doc-item-row" data-stage="<?= $step['key'] ?>" data-kriteria-ids="<?= htmlspecialchars($item['kriteria_ids_str'] ?? '') ?>" data-status="<?= htmlspecialchars($item['status'] ?? 'draft') ?>" data-visibility="<?= htmlspecialchars($itemVis) ?>"
           style="display:flex;align-items:center;gap:14px;padding:14px 24px;background:<?= $isItemNonaktif ? '#fefce8' : ($ii % 2 === 0 ? '#fff' : '#fafbfc') ?>;<?= $ii < count($visibleItems)-1 ? 'border-bottom:1px solid #f1f5f9;' : '' ?>;<?= $isItemNonaktif ? 'border-left:4px solid #eab308;' : '' ?>">
        <div style="width:8px;height:8px;border-radius:50%;background:<?= $isItemNonaktif ? '#eab308' : ($isItemFinal ? '#059669' : '#f59e0b') ?>;flex-shrink:0;"></div>
        <div style="flex:1;min-width:0;">
          <div style="font-size:14.5px;font-weight:700;color:<?= $isItemNonaktif ? '#854d0e' : 'var(--text-main)' ?>;line-height:1.3;">
            <?= $judul ?>
          </div>
          
          <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:6px;">
            <?php if ($acuan): ?>
            <span style="font-size:11.5px;color:var(--text-muted);background:#f1f5f9;padding:2px 8px;border-radius:6px;border:1px solid #e2e8f0;">
              Acuan: <?= $acuan ?>
            </span>
            <?php endif; ?>

            <!-- Badge Kriteria -->
            <?php 
              $rawKids = array_filter(explode(',', (string)($item['kriteria_ids_str'] ?? '')));
              if (!empty($rawKids)):
                $totalK = count($allKriteria ?? []);
                if (count($rawKids) >= $totalK && $totalK > 3):
            ?>
              <span onclick="filterByKriteriaId('all')" style="cursor:pointer;display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:800;padding:2.5px 9px;border-radius:6px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;box-shadow:0 1px 2px rgba(29,78,216,0.08);transition:all 0.15s;" title="Klik untuk filter semua kriteria">
                🎯 Semua Kriteria (<?= count($rawKids) ?>)
              </span>
            <?php else: ?>
              <?php foreach ($rawKids as $kid): 
                $kObj = $kriteriaMap[(int)$kid] ?? null;
                if ($kObj):
                  $kodeK = !empty($kObj['kode']) ? $kObj['kode'] : 'K' . $kObj['urutan'];
              ?>
              <span onclick="filterByKriteriaId('<?= (int)$kid ?>')" style="cursor:pointer;display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;padding:2.5px 8px;border-radius:6px;background:#f8fafc;color:#1e293b;border:1px solid #cbd5e1;transition:all 0.15s;" onmouseover="this.style.borderColor='#3b82f6';this.style.background='#eff6ff';" onmouseout="this.style.borderColor='#cbd5e1';this.style.background='#f8fafc';" title="Klik untuk filter kriteria: <?= htmlspecialchars($kObj['nama']) ?>">
                <span style="color:#2563eb;font-weight:900;"><?= htmlspecialchars($kodeK) ?></span>
                <span style="color:#475569;font-weight:600;font-size:10.5px;max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($kObj['nama']) ?></span>
              </span>
              <?php endif; endforeach; ?>
            <?php endif; endif; ?>
          </div>
        </div>

        <?php if ($isItemNonaktif): ?>
        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
          <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;color:#854d0e;background:#fef08a;">
            Nonaktif
          </span>
          <span style="font-size:11px;font-weight:600;padding:3px 8px;border-radius:20px;color:#64748b;background:#f1f5f9;">
            <?= $isItemFinal ? 'Final' : 'Draft' ?>
          </span>
        </div>
        <?php else: ?>
        <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;color:<?= $isItemFinal ? '#059669' : '#d97706' ?>;background:<?= $isItemFinal ? '#ecfdf5' : '#fffbeb' ?>;flex-shrink:0;">
          <?= $isItemFinal ? 'Final' : 'Draft' ?>
        </span>
        <?php endif; ?>

        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
          <a href="<?= BASE_URL ?>/<?= $step['route'] ?>/<?= $item['id'] ?>"
             class="btn btn-sm btn-outline"
             style="padding:6px 14px;font-size:12px;font-weight:700;">
            Buka
          </a>

          <?php if (!$isItemNonaktif): ?>
          <!-- 1. Tombol Nonaktifkan (dari status Aktif) -->
          <form method="POST" action="<?= BASE_URL ?>/<?= $step['route'] ?>/<?= $item['id'] ?>/toggle-status"
                onsubmit="return confirm('Nonaktifkan dokumen <?= $step['nama'] ?>: &quot;<?= addslashes($judul) ?>&quot;?\nDokumen akan berstatus Nonaktif tetapi tetap terlihat di daftar.')"
                style="display:inline;margin:0;">
            <input type="hidden" name="visibility_status" value="nonaktif">
            <input type="hidden" name="return_url" value="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline"
                    style="padding:6px 10px;color:#854d0e;border-color:#fde047;background:#fffbeb;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:4px;"
                    title="Nonaktifkan dokumen (tetap terlihat di daftar)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/></svg>
              Nonaktifkan
            </button>
          </form>
          <?php else: ?>
          <!-- 2. Dari status Nonaktif: Bisa Aktifkan ATAU Sembunyikan (Hide) -->
          <form method="POST" action="<?= BASE_URL ?>/<?= $step['route'] ?>/<?= $item['id'] ?>/toggle-status"
                style="display:inline;margin:0;">
            <input type="hidden" name="visibility_status" value="aktif">
            <input type="hidden" name="return_url" value="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline"
                    style="padding:6px 10px;color:#059669;border-color:#a7f3d0;background:#ecfdf5;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:4px;"
                    title="Aktifkan dokumen ini kembali">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polygon points="5 3 19 12 5 21 5 3"/></svg>
              Aktifkan
            </button>
          </form>

          <form method="POST" action="<?= BASE_URL ?>/<?= $step['route'] ?>/<?= $item['id'] ?>/toggle-status"
                onsubmit="return confirm('Sembunyikan (Hide) dokumen <?= $step['nama'] ?>: &quot;<?= addslashes($judul) ?>&quot;?\nDokumen akan disembunyikan dari daftar utama, dan dapat dibuka kembali melalui menu Dokumen Tersembunyi.')"
                style="display:inline;margin:0;">
            <input type="hidden" name="visibility_status" value="hidden">
            <input type="hidden" name="return_url" value="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline"
                    style="padding:6px 10px;color:#475569;border-color:#cbd5e1;background:#f8fafc;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:4px;"
                    title="Sembunyikan dokumen ini (Hide)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              Hide
            </button>
          </form>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <!-- Empty State untuk tahap ini -->
    <div style="padding:28px 24px;text-align:center;color:var(--text-muted);font-size:13px;border-top:1px solid var(--border);">
      <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" width="32" height="32" style="display:block;margin:0 auto 10px;"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Belum ada dokumen <?= $step['nama'] ?> aktif. Klik <strong>"Tambah <?= $step['nama'] ?>"</strong> untuk memulai.
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>

<!-- Banner Dokumen Tersembunyi jika ada -->
<?php if (($totalHiddenCount ?? 0) > 0): ?>
<div style="background:#f8fafc;border:1.5px solid #cbd5e1;border-radius:14px;padding:16px 22px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-top:18px;">
  <div style="display:flex;align-items:center;gap:12px;">
    <div style="width:38px;height:38px;background:#e2e8f0;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;">
      📁
    </div>
    <div>
      <div style="font-size:14px;font-weight:800;color:#1e293b;">
        Terdapat <?= (int)$totalHiddenCount ?> Dokumen yang Sedang Disembunyikan (Hide)
      </div>
      <div style="font-size:12.5px;color:#64748b;">
        Dokumen yang di-hide tidak muncul di daftar di atas. Anda dapat mengaktifkannya kembali kapan saja.
      </div>
    </div>
  </div>
  <a href="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/hidden" class="btn btn-outline" style="font-size:13px;font-weight:700;background:#fff;border-color:#94a3b8;color:#1e293b;display:flex;align-items:center;gap:6px;">
    Buka Halaman Dokumen Tersembunyi →
  </a>
</div>
<?php endif; ?>


<!-- Status & Visibility Management (Nonaktifkan / Aktifkan / Arsipkan) -->
<?php
$curStatus = $project['status'] ?? 'aktif';
$statusBgMap = [
    'aktif'    => ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'text' => '#166534', 'label' => 'Aktif', 'badgeBg' => '#22c55e'],
    'nonaktif' => ['bg' => '#fefce8', 'border' => '#fef08a', 'text' => '#854d0e', 'label' => 'Nonaktif / Tersembunyi', 'badgeBg' => '#eab308'],
    'arsip'    => ['bg' => '#f8fafc', 'border' => '#e2e8f0', 'text' => '#475569', 'label' => 'Diarsipkan', 'badgeBg' => '#64748b'],
    'selesai'  => ['bg' => '#eff6ff', 'border' => '#bfdbfe', 'text' => '#1e40af', 'label' => 'Selesai', 'badgeBg' => '#3b82f6'],
];
$stInfo = $statusBgMap[$curStatus] ?? $statusBgMap['aktif'];
?>
<div style="margin-top:32px;padding:20px 24px;background:<?= $stInfo['bg'] ?>;border:1.5px solid <?= $stInfo['border'] ?>;border-radius:14px;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
    <div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
        <span style="font-size:15px;font-weight:800;color:<?= $stInfo['text'] ?>;">Status &amp; Visibilitas Project</span>
        <span style="display:inline-block;padding:2px 10px;border-radius:99px;font-size:12px;font-weight:700;background:<?= $stInfo['badgeBg'] ?>;color:#fff;">
          <?= $stInfo['label'] ?>
        </span>
      </div>
      <div style="font-size:13px;color:<?= $stInfo['text'] ?>;opacity:0.9;">
        Project dapat dinonaktifkan (disembunyikan) atau diaktifkan kembali. Data Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan <strong>tetap aman dan tidak akan hilang</strong>.
      </div>
    </div>

    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
      <?php if ($curStatus === 'aktif'): ?>
      <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/toggle-status" style="margin:0;">
        <input type="hidden" name="status" value="nonaktif">
        <button type="submit" class="btn btn-outline" style="background:#fff;border-color:#eab308;color:#a16207;font-weight:700;font-size:13px;display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/></svg>
          Nonaktifkan / Sembunyikan
        </button>
      </form>
      <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/toggle-status" style="margin:0;">
        <input type="hidden" name="status" value="arsip">
        <button type="submit" class="btn btn-outline" style="background:#fff;border-color:#cbd5e1;color:#475569;font-weight:700;font-size:13px;display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
          Arsipkan
        </button>
      </form>
      <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/toggle-status" style="margin:0;">
        <input type="hidden" name="status" value="selesai">
        <button type="submit" class="btn btn-outline" style="background:#fff;border-color:#93c5fd;color:#1d4ed8;font-weight:700;font-size:13px;display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;">
          ✓ Tandai Selesai
        </button>
      </form>
      <?php else: ?>
      <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/toggle-status" style="margin:0;">
        <input type="hidden" name="status" value="aktif">
        <button type="submit" class="btn btn-primary" style="background:#16a34a;border:none;font-weight:700;font-size:13px;display:flex;align-items:center;gap:6px;padding:9px 18px;border-radius:8px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><polygon points="5 3 19 12 5 21 5 3"/></svg>
          Aktifkan Project Kembali
        </button>
      </form>
      <?php if ($curStatus !== 'arsip'): ?>
      <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/toggle-status" style="margin:0;">
        <input type="hidden" name="status" value="arsip">
        <button type="submit" class="btn btn-outline" style="background:#fff;border-color:#cbd5e1;color:#475569;font-weight:700;font-size:13px;display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;">
          Arsipkan
        </button>
      </form>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
function applyProjectFilters() {
  var stageVal    = document.getElementById('filterStageSelect').value;
  var kriteriaVal = document.getElementById('filterKriteriaSelect').value;
  var statusVal   = document.getElementById('filterStatusSelect').value;

  var stepCards = document.querySelectorAll('.step-card-block');
  stepCards.forEach(function(card) {
    var cardStage = card.getAttribute('data-stage');
    var isStageMatch = (stageVal === 'all' || cardStage === stageVal);

    if (!isStageMatch) {
      card.style.display = 'none';
      return;
    }

    var itemRows = card.querySelectorAll('.doc-item-row');
    var visibleCount = 0;

    itemRows.forEach(function(row) {
      var itemKriteriaStr = row.getAttribute('data-kriteria-ids') || '';
      var itemKriteriaArr = itemKriteriaStr.split(',').filter(Boolean);
      var itemStatus      = row.getAttribute('data-status');
      var itemVis         = row.getAttribute('data-visibility');

      var isKriteriaMatch = (kriteriaVal === 'all' || itemKriteriaArr.includes(kriteriaVal));
      var isStatusMatch   = (statusVal === 'all') ||
                            (statusVal === 'draft' && itemStatus === 'draft' && itemVis !== 'nonaktif') ||
                            (statusVal === 'final' && itemStatus === 'final' && itemVis !== 'nonaktif') ||
                            (statusVal === 'nonaktif' && itemVis === 'nonaktif');

      if (isKriteriaMatch && isStatusMatch) {
        row.style.display = 'flex';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });

    if (itemRows.length > 0) {
      var emptyNotice = card.querySelector('.filter-empty-notice');
      if (visibleCount === 0) {
        if (!emptyNotice) {
          emptyNotice = document.createElement('div');
          emptyNotice.className = 'filter-empty-notice';
          emptyNotice.style.cssText = 'padding:18px 24px;text-align:center;color:#64748b;font-size:12.5px;font-style:italic;background:#f8fafc;border-top:1px solid #e2e8f0;';
          emptyNotice.textContent = 'Tidak ada dokumen yang cocok dengan filter kriteria/status yang dipilih.';
          card.appendChild(emptyNotice);
        } else {
          emptyNotice.style.display = 'block';
        }
      } else if (emptyNotice) {
        emptyNotice.style.display = 'none';
      }
    }

    card.style.display = 'block';
  });
}

function filterByKriteriaId(kId) {
  var sel = document.getElementById('filterKriteriaSelect');
  if (sel) {
    sel.value = kId;
    applyProjectFilters();
  }
}

function resetProjectFilters() {
  document.getElementById('filterStageSelect').value = 'all';
  document.getElementById('filterKriteriaSelect').value = 'all';
  document.getElementById('filterStatusSelect').value = 'all';
  applyProjectFilters();
}
</script>

<!-- Modal Konfirmasi Hapus Project Permanen -->
<div class="modal-overlay" id="modalDeleteProjectShow">
  <div class="modal-card" style="max-width:480px;border-radius:18px;overflow:hidden;box-shadow:0 24px 48px rgba(0,0,0,0.3);padding:0;">
    <div style="background:linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);color:#fff;padding:22px 24px;display:flex;align-items:center;gap:14px;">
      <div style="width:44px;height:44px;background:rgba(255,255,255,0.22);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">
        🗑️
      </div>
      <div>
        <h3 style="font-size:17px;font-weight:800;margin:0 0 2px;color:#fff;">Hapus Project Permanen</h3>
        <p style="font-size:12px;margin:0;opacity:0.9;color:#fff;">Konfirmasi Penghapusan Project PPEPP</p>
      </div>
    </div>
    <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/destroy">
      <div class="modal-body" style="padding:22px 24px;background:#fff;">
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px 16px;color:#991b1b;font-size:13px;line-height:1.6;margin-bottom:16px;">
          ⚠️ <strong>PERHATIAN SANGAT PENTING:</strong><br>
          Anda akan menghapus project <strong style="color:#7f1d1d;"><?= htmlspecialchars($project['judul']) ?> (TA <?= htmlspecialchars($project['ta_nama']) ?>)</strong> secara <strong>PERMANEN</strong>.
          <br><br>
          Seluruh data 5 tahap PPEPP di dalamnya (<strong>Penetapan, Pelaksanaan, Evaluasi, Pengendalian, & Peningkatan</strong>) beserta berkas upload akan <strong>TERHAPUS TOTAL & TIDAK DAPAT DI-RECOVERY KEMBALI</strong>.
        </div>
        <p style="font-size:13px;color:#475569;margin:0;">Apakah Anda yakin ingin melanjutkan penghapusan ini?</p>
      </div>
      <div class="modal-footer" style="padding:16px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-outline" data-modal-close="modalDeleteProjectShow" style="padding:9px 18px;font-weight:600;border-radius:10px;">
          Batal
        </button>
        <button type="submit" class="btn btn-danger" style="padding:9px 20px;font-weight:800;border-radius:10px;background:#dc2626;color:#fff;border:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer;">
          🗑️ Ya, Hapus Project &amp; Seluruh Isinya
        </button>
      </div>
    </form>
  </div>
</div>

