<?php
$pageTitle   = 'Hasil Peningkatan Standar — ' . htmlspecialchars($peningkatan['judul']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Peningkatan', 'url' => BASE_URL . '/peningkatan'],
  ['label' => htmlspecialchars($peningkatan['judul'])],
];

$penetapan   = $peningkatan['penetapan'] ?? [];
$details     = $peningkatan['details'] ?? [];
$berkasList  = $peningkatan['berkas_list'] ?? [];
$pkId        = $peningkatan['id'];

// Pisahkan standar terpenuhi (yang ditingkatkan) dan belum terpenuhi (dari pengendalian)
$standarTerpenuhi = [];
$standarBelum     = [];
foreach ($details as $d) {
  if (($d['status_capaian'] ?? '') === 'tercapai') {
    $standarTerpenuhi[] = $d;
  } else {
    $standarBelum[] = $d;
  }
}

$totalStandar = count($details);
$cntTerpenuhi = count($standarTerpenuhi);
$cntBelum     = count($standarBelum);
$isFinal      = ($peningkatan['status'] === 'final');

$kriteriaMap = [];
foreach ($details as $d) {
  $kId = (int)($d['kriteria_id'] ?? 0);
  $kKode = !empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD';
  $kNama = $d['kriteria_nama'] ?? 'Kriteria';
  if (!isset($kriteriaMap[$kId])) {
    $kriteriaMap[$kId] = [
      'id'    => $kId,
      'kode'  => $kKode,
      'nama'  => $kNama,
      'count' => 0
    ];
  }
  $kriteriaMap[$kId]['count']++;
}
?>

<!-- Header Panel (Clean Academic / Institutional Design) -->
<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px 26px;margin-bottom:20px;box-shadow:0 1px 3px rgba(15,23,42,0.04);">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap;">
    <div style="flex:1;min-width:280px;">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#64748b;">
          Tahap 5 : Peningkatan Standar Mutu SPMI (PPEPP)
        </span>
        <span style="display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:2px 10px;border-radius:6px;<?= $isFinal ? 'background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0;' : 'background:#fffbeb;color:#92400e;border:1px solid #fde68a;' ?>">
          <span style="width:6px;height:6px;border-radius:50%;background:currentColor;"></span>
          <?= $isFinal ? 'Dokumen Final' : 'Draft' ?>
        </span>
      </div>

      <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 10px;line-height:1.3;letter-spacing:-0.3px;">
        <?= htmlspecialchars($peningkatan['judul']) ?>
      </h1>

      <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;font-size:12.5px;color:#64748b;">
        <span>Penetapan Acuan: <strong style="color:#1e293b;"><?= htmlspecialchars($penetapan['judul'] ?? '—') ?></strong></span>
        <span>•</span>
        <span>Tahun Ajaran: <strong style="color:#1e293b;"><?= htmlspecialchars($penetapan['ta_nama'] ?? '—') ?></strong></span>
        <span>•</span>
        <span>Dibuat: <strong style="color:#1e293b;"><?= date('d F Y', strtotime($peningkatan['created_at'])) ?></strong></span>
      </div>
    </div>

    <!-- Actions -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <button type="button" onclick="openExportModal()" class="btn btn-primary" style="font-size:13px;font-weight:600;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="9 18 15 12 9 6"/></svg>
        Mulai Siklus Baru
      </button>

      <a href="<?= BASE_URL ?>/peningkatan/<?= $pkId ?>/edit" class="btn btn-outline" style="font-size:13px;font-weight:600;color:#0f172a;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
          <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Edit Peningkatan
      </a>

      <?php if (!$isFinal): ?>
      <form action="<?= BASE_URL ?>/peningkatan/<?= $pkId ?>/finalize" method="POST" style="margin:0;">
        <button type="submit" class="btn btn-success" style="font-size:13px;font-weight:600;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
          Finalisasi
        </button>
      </form>
      <?php endif; ?>

      <a href="<?= BASE_URL ?>/ppepp<?= !empty($peningkatan['ppepp_project_id']) ? '/' . $peningkatan['ppepp_project_id'] : '' ?>" class="btn btn-outline" style="font-size:13px;color:#475569;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
        Project Library
      </a>
    </div>
  </div>
</div>

<!-- Metrics Overview -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:20px;">
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);">
    <div style="font-size:11.5px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Total Standar</div>
    <div style="font-size:26px;font-weight:800;color:#0f172a;line-height:1.2;margin-top:4px;">
      <?= $totalStandar ?> <span style="font-size:13px;font-weight:500;color:#64748b;">Standar</span>
    </div>
  </div>

  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #10b981;">
    <div style="font-size:11.5px;color:#059669;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Target Ditingkatkan</div>
    <div style="font-size:26px;font-weight:800;color:#065f46;line-height:1.2;margin-top:4px;">
      <?= $cntTerpenuhi ?> <span style="font-size:13px;font-weight:500;color:#059669;">Standar</span>
    </div>
  </div>

  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #d97706;">
    <div style="font-size:11.5px;color:#b45309;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Dari Pengendalian</div>
    <div style="font-size:26px;font-weight:800;color:#92400e;line-height:1.2;margin-top:4px;">
      <?= $cntBelum ?> <span style="font-size:13px;font-weight:500;color:#b45309;">Standar</span>
    </div>
  </div>

  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,0.03);border-left:4px solid #64748b;">
    <div style="font-size:11.5px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Berkas SK Pengesahan</div>
    <div style="font-size:26px;font-weight:800;color:#0f172a;line-height:1.2;margin-top:4px;">
      <?= count($berkasList) ?> <span style="font-size:13px;font-weight:500;color:#64748b;">Berkas</span>
    </div>
  </div>
</div>

<!-- Toolbar Pencarian & Filter Kriteria -->
<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 18px;box-shadow:0 1px 3px rgba(15,23,42,0.03);margin-bottom:20px;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
    
    <!-- Search Bar -->
    <div style="flex:1;min-width:260px;position:relative;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" width="15" height="15" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="peningkatanSearchInput" onkeyup="filterPeningkatan()" placeholder="Cari kode, nama kriteria, target baru, alasan peningkatan..."
             style="width:100%;padding:8px 12px 8px 34px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;outline:none;font-family:inherit;background:#f8fafc;"
             onfocus="this.style.background='#fff';this.style.borderColor='#1a237e';" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1';">
    </div>

    <!-- Info Counter -->
    <div id="peningkatanResultCount" style="font-size:12px;font-weight:600;color:#64748b;">
      Menampilkan <?= $totalStandar ?> standar
    </div>
  </div>

  <?php if (count($kriteriaMap) > 1): ?>
  <div style="margin-top:12px;padding-top:10px;border-top:1px solid #f1f5f9;display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
    <span style="font-size:11.5px;font-weight:600;color:#64748b;margin-right:4px;">Filter Kriteria:</span>
    <button type="button" class="pk-kriteria-filter-btn active" data-kid="all" onclick="filterPeningkatanKriteria('all', this)"
            style="border:1px solid #0f172a;padding:3px 10px;border-radius:4px;font-size:11.5px;font-weight:600;cursor:pointer;background:#0f172a;color:#fff;">
      Semua (<?= $totalStandar ?>)
    </button>
    <?php foreach ($kriteriaMap as $k): ?>
    <button type="button" class="pk-kriteria-filter-btn" data-kid="<?= $k['id'] ?>" onclick="filterPeningkatanKriteria('<?= $k['id'] ?>', this)"
            style="border:1px solid #cbd5e1;padding:3px 10px;border-radius:4px;font-size:11.5px;font-weight:500;cursor:pointer;background:#fff;color:#475569;">
      <?= htmlspecialchars($k['kode']) ?> (<?= $k['count'] ?>)
    </button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- SECTION 1: STANDAR YANG DITINGKATKAN -->
<div style="margin-bottom:12px;">
  <h2 style="font-size:15px;font-weight:800;color:#0f172a;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 14px;">
    1. Standar yang Ditingkatkan Mutunya (<?= $cntTerpenuhi ?> Standar)
  </h2>
</div>

<div style="display:flex;flex-direction:column;gap:14px;margin-bottom:28px;">
  <?php if (empty($standarTerpenuhi)): ?>
    <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;background:#fff;border:1px dashed #cbd5e1;border-radius:10px;">
      Belum ada standar yang ditingkatkan pada siklus ini.
    </div>
  <?php else: ?>
    <?php foreach ($standarTerpenuhi as $idx => $d): ?>
    <div class="pk-filterable-item" data-kid="<?= (int)($d['kriteria_id'] ?? 0) ?>"
         style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,0.03);">
      
      <!-- Header Standar -->
      <div style="background:#f8fafc;padding:12px 18px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:10px;">
          <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:4px;background:#059669;color:#fff;">
            <?= htmlspecialchars(!empty($d['kriteria_kode']) ? $d['kriteria_kode'] : ('STD-' . ($idx+1))) ?>
          </span>
          <div>
            <span style="font-size:14px;font-weight:700;color:#0f172a;"><?= htmlspecialchars($d['kriteria_nama']) ?></span>
            <?php if (!empty($d['kriteria_deskripsi'])): ?>
            <span style="font-size:12px;color:#64748b;margin-left:6px;">— <?= htmlspecialchars($d['kriteria_deskripsi']) ?></span>
            <?php endif; ?>
          </div>
        </div>

        <?php if (!empty($d['nilai_kenaikan'])): ?>
        <span style="background:#ecfdf5;color:#065f46;font-size:11.5px;font-weight:700;padding:3px 10px;border-radius:4px;border:1px solid #bbf7d0;">
          Kenaikan: <?= htmlspecialchars($d['nilai_kenaikan']) ?>
        </span>
        <?php endif; ?>
      </div>

      <!-- Body Standar -->
      <div style="padding:16px 18px;display:flex;flex-direction:column;gap:12px;">

        <!-- Perbandingan Target Lama vs Baru -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px;">
          <!-- Target Lama -->
          <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:12px 14px;">
            <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:4px;">
              Target Standar (Sebelumnya)
            </div>
            <div style="font-size:13px;color:#334155;line-height:1.6;">
              <?= htmlspecialchars($d['target_capaian'] ?: '—') ?>
            </div>
            <?php if (!empty($d['indikator'])): ?>
            <div style="margin-top:6px;font-size:12px;color:#64748b;border-top:1px solid #e2e8f0;padding-top:6px;">
              <span style="font-weight:600;">Indikator Lama:</span> <?= htmlspecialchars($d['indikator']) ?>
            </div>
            <?php endif; ?>
          </div>

          <!-- Target Baru -->
          <div style="background:#fff;border:1px solid #bbf7d0;border-radius:6px;padding:12px 14px;">
            <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#065f46;margin-bottom:4px;">
              Target Baru (Setelah Ditingkatkan)
            </div>
            <div style="font-size:13.5px;color:#0f172a;font-weight:700;line-height:1.6;">
              <?= !empty($d['target_baru']) ? htmlspecialchars($d['target_baru']) : '<span style="color:#94a3b8;font-style:italic;font-weight:400;">Belum dirumuskan</span>' ?>
            </div>
            <?php if (!empty($d['indikator_baru'])): ?>
            <div style="margin-top:6px;font-size:12px;color:#047857;border-top:1px solid #bbf7d0;padding-top:6px;">
              <span style="font-weight:600;">Indikator Baru:</span> <?= htmlspecialchars($d['indikator_baru']) ?>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Alasan & Strategi -->
        <?php if (!empty($d['alasan_peningkatan']) || !empty($d['strategi_baru'])): ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px;">
          <?php if (!empty($d['alasan_peningkatan'])): ?>
          <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px 12px;">
            <div style="font-size:10.5px;font-weight:700;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.5px;">Alasan Peningkatan Mutu</div>
            <div style="font-size:12.5px;color:#334155;line-height:1.6;"><?= nl2br(htmlspecialchars($d['alasan_peningkatan'])) ?></div>
          </div>
          <?php endif; ?>

          <?php if (!empty($d['strategi_baru'])): ?>
          <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px 12px;">
            <div style="font-size:10.5px;font-weight:700;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.5px;">Program / Strategi Baru</div>
            <div style="font-size:12.5px;color:#334155;line-height:1.6;"><?= nl2br(htmlspecialchars($d['strategi_baru'])) ?></div>
          </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>

      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- SECTION 2: STANDAR DARI PENGENDALIAN -->
<div style="margin-bottom:12px;">
  <h2 style="font-size:15px;font-weight:800;color:#0f172a;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 14px;">
    2. Standar Belum Terpenuhi (Dari Pengendalian) (<?= $cntBelum ?> Standar)
  </h2>
</div>

<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px 18px;box-shadow:0 1px 3px rgba(15,23,42,0.03);margin-bottom:28px;">
  <p style="font-size:13px;color:#64748b;margin:0 0 12px;line-height:1.5;">
    Standar-standar ini belum tercapai pada evaluasi berjalan dan akan <strong>tetap diikutsertakan ke dalam dokumen Penetapan siklus berikutnya</strong> dengan target yang dipertahankan.
  </p>

  <?php if (empty($standarBelum)): ?>
    <div style="padding:14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;font-size:13px;color:#065f46;font-weight:600;">
      Seluruh standar mutu telah berhasil terpenuhi pada siklus ini.
    </div>
  <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:8px;">
      <?php foreach ($standarBelum as $idx => $sb): ?>
      <div class="pk-filterable-item" data-kid="<?= (int)($sb['kriteria_id'] ?? 0) ?>"
           style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px 14px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:10px;flex:1;min-width:240px;">
          <span style="background:#0f172a;color:#fff;font-weight:700;font-size:11px;padding:2px 8px;border-radius:4px;flex-shrink:0;margin-top:2px;">
            <?= htmlspecialchars($sb['kriteria_kode'] ?? ('STD-' . ($idx+1))) ?>
          </span>
          <div>
            <div style="font-weight:700;font-size:13.5px;color:#0f172a;"><?= htmlspecialchars($sb['kriteria_nama']) ?></div>
            <div style="font-size:12px;color:#64748b;line-height:1.5;margin-top:2px;">
              <?= htmlspecialchars(substr($sb['target_capaian'] ?? '', 0, 110)) ?><?= strlen($sb['target_capaian'] ?? '') > 110 ? '…' : '' ?>
            </div>
          </div>
        </div>

        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
          <span style="font-size:11px;font-weight:700;color:#92400e;background:#fffbeb;border:1px solid #fde68a;padding:2px 8px;border-radius:4px;">
            <?= ($sb['status_tindakan'] ?? 'belum') === 'selesai' ? 'Selesai Dikoreksi' : 'Sedang Dikendalikan' ?>
          </span>
          <span style="font-size:11px;font-weight:700;color:#065f46;background:#ecfdf5;border:1px solid #bbf7d0;padding:2px 8px;border-radius:4px;">
            Tetap Masuk Tahun Depan
          </span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- SECTION 3: BERKAS SK -->
<div style="margin-bottom:12px;">
  <h2 style="font-size:15px;font-weight:800;color:#0f172a;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 14px;">
    3. Berkas Surat Keputusan (SK) Pengesahan Standar Baru
  </h2>
</div>

<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,0.03);margin-bottom:28px;">
  <div style="background:#f8fafc;padding:14px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <div>
      <div style="font-size:14.5px;font-weight:700;color:#0f172a;">Dokumen Legalitas Pengesahan Standar Ditingkatkan</div>
      <div style="font-size:12px;color:#64748b;margin-top:2px;">Surat keputusan pemberlakuan standar baru</div>
    </div>
    <span style="font-size:11.5px;font-weight:600;color:#475569;background:#f1f5f9;padding:3px 10px;border-radius:4px;border:1px solid #e2e8f0;">
      <?= count($berkasList) ?> Berkas
    </span>
  </div>

  <div style="padding:18px 20px;">
    <?php if (empty($berkasList)): ?>
      <div style="padding:20px;text-align:center;color:#94a3b8;font-style:italic;font-size:13px;">
        Belum ada berkas SK yang diunggah.
        <a href="<?= BASE_URL ?>/peningkatan/<?= $pkId ?>/edit" style="color:#1d4ed8;font-weight:600;margin-left:6px;">Upload SK melalui halaman Edit →</a>
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:8px;">
        <?php foreach ($berkasList as $sk): ?>
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
          <div>
            <div style="font-weight:700;font-size:13px;color:#0f172a;">
              <?= htmlspecialchars($sk['nomor_sk'] ?? '—') ?> — <?= htmlspecialchars($sk['judul_sk'] ?? $sk['file_name']) ?>
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">
              Tanggal SK: <?= htmlspecialchars($sk['tanggal_sk'] ?? '—') ?>
              <?php if (!empty($sk['keterangan'])): ?> &nbsp;·&nbsp; <?= htmlspecialchars($sk['keterangan']) ?><?php endif; ?>
            </div>
          </div>
          <a href="<?= htmlspecialchars($sk['url'] ?? BASE_URL . '/' . $sk['file_path']) ?>" target="_blank"
             class="btn btn-outline btn-sm" style="font-size:11.5px;font-weight:600;color:#0f172a;border-color:#cbd5e1;background:#fff;">
            Unduh SK ↗
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- SECTION 4: CTA PENETAPAN BARU -->
<div class="card" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:22px 24px;margin-bottom:30px;box-shadow:0 1px 3px rgba(15,23,42,0.03);display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
  <div style="flex:1;min-width:280px;">
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#64748b;margin-bottom:4px;">
      Siklus Peningkatan Mutu Berkelanjutan
    </div>
    <h3 style="font-size:17px;font-weight:800;color:#0f172a;margin:0 0 4px;">
      Mulai Siklus PPEPP Tahun Berikutnya
    </h3>
    <p style="font-size:13px;color:#64748b;margin:0;line-height:1.5;">
      Sistem akan menggabungkan <?= $cntTerpenuhi ?> standar yang telah ditingkatkan dan <?= $cntBelum ?> standar dari pengendalian ke dokumen Penetapan baru.
    </p>
  </div>
  <button type="button" onclick="openExportModal()" class="btn btn-primary" style="font-size:13px;font-weight:600;padding:10px 20px;">
    Buat Dokumen Penetapan Baru →
  </button>
</div>

<!-- MODAL EKSPOR KE PENETAPAN BARU -->
<div id="exportModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.6);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(2px);padding:16px;">
  <div style="background:#fff;border-radius:12px;max-width:520px;width:100%;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.15);">
    
    <!-- Modal Header -->
    <div style="background:#f8fafc;padding:16px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;gap:12px;">
      <div>
        <h3 style="font-size:15px;font-weight:800;margin:0;color:#0f172a;">Mulai Siklus PPEPP Baru</h3>
        <p style="font-size:12px;margin:2px 0 0;color:#64748b;">Membuat project dan dokumen Penetapan untuk tahun ajaran baru</p>
      </div>
      <button type="button" onclick="closeExportModal()" style="color:#64748b;font-size:20px;line-height:1;background:none;border:none;cursor:pointer;padding:4px;">&times;</button>
    </div>

    <!-- Modal Form -->
    <form action="<?= BASE_URL ?>/peningkatan/<?= $pkId ?>/export-to-penetapan" method="POST" style="display:flex;flex-direction:column;overflow:hidden;flex:1;margin:0;">
      <div style="padding:20px;overflow-y:auto;flex:1;">
        
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:12px 14px;font-size:12.5px;color:#334155;margin-bottom:16px;line-height:1.5;">
          <strong>Ringkasan:</strong> <?= $cntTerpenuhi ?> standar hasil peningkatan menggunakan target baru, dan <?= $cntBelum ?> standar pengendalian tetap disertakan.
        </div>

        <div class="form-group" style="margin-bottom:14px;">
          <label style="font-size:12.5px;font-weight:700;color:#0f172a;margin-bottom:4px;display:block;">
            Pilih Tahun Ajaran Tujuan <span style="color:#dc2626;">*</span>
          </label>
          <select name="target_ta_id" id="exportTargetTaSelect" class="form-control" required style="font-size:13px;padding:8px 12px;border-radius:6px;" onchange="handleExportTaChange(this)">
            <option value="">— Pilih Tahun Ajaran Baru —</option>
            <?php foreach ($tahunAjarans as $ta): ?>
              <option value="<?= $ta['id'] ?>" <?= $ta['aktif'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($ta['nama']) ?> (<?= ucfirst($ta['semester'] ?? '') ?>)
                <?= $ta['aktif'] ? '— Aktif' : '' ?>
              </option>
            <?php endforeach; ?>
            <option value="custom" style="font-weight:700;color:#1d4ed8;">+ Input Tahun Ajaran Baru Manual...</option>
          </select>
        </div>

        <!-- Input TA Custom jika tidak ada di list -->
        <input type="hidden" name="new_ta" value="0" id="exportNewTaFlag">
        <div id="exportCustomTaGroup" style="display:none;margin-bottom:14px;background:#f8fafc;border:1px solid #cbd5e1;border-radius:6px;padding:10px 12px;">
          <label style="font-size:11.5px;font-weight:600;color:#475569;margin-bottom:3px;display:block;">Tuliskan Nama Tahun Ajaran Baru:</label>
          <input type="text" name="new_ta_nama" id="exportNewTaNama" class="form-control" placeholder="Contoh: 2026/2027"
                 style="font-size:13px;padding:7px 10px;border-radius:4px;" oninput="document.getElementById('exportNewTaFlag').value='1'">
        </div>

        <div class="form-group" style="margin-bottom:6px;">
          <label style="font-size:12.5px;font-weight:700;color:#0f172a;margin-bottom:4px;display:block;">
            Judul Dokumen Penetapan Baru:
          </label>
          <input type="text" name="judul_penetapan_baru" class="form-control"
            value="Penetapan Standar Mutu (Hasil Peningkatan &amp; Pengendalian)" required
            style="font-size:13px;padding:8px 12px;border-radius:6px;">
        </div>
      </div>

      <!-- Modal Footer -->
      <div style="padding:12px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;gap:8px;justify-content:flex-end;">
        <button type="button" onclick="closeExportModal()" class="btn btn-outline" style="font-size:12.5px;padding:7px 14px;">Batal</button>
        <button type="submit" class="btn btn-primary" style="font-size:12.5px;padding:7px 16px;">
          Konfirmasi &amp; Mulai Siklus
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  var currentPkKid = 'all';

  function filterPeningkatan() {
    var query = (document.getElementById('peningkatanSearchInput')?.value || '').toLowerCase().trim();
    var items = document.querySelectorAll('.pk-filterable-item');
    var visibleCount = 0;

    items.forEach(function(item) {
      var kid = item.getAttribute('data-kid');
      var text = item.innerText.toLowerCase();

      var matchKid = (currentPkKid === 'all' || kid === currentPkKid);
      var matchQuery = (!query || text.indexOf(query) !== -1);

      if (matchKid && matchQuery) {
        item.style.display = '';
        visibleCount++;
      } else {
        item.style.display = 'none';
      }
    });

    var countEl = document.getElementById('peningkatanResultCount');
    if (countEl) {
      countEl.textContent = 'Menampilkan ' + visibleCount + ' standar';
    }
  }

  function filterPeningkatanKriteria(kid, btnEl) {
    currentPkKid = String(kid);
    document.querySelectorAll('.pk-kriteria-filter-btn').forEach(function(btn) {
      btn.style.background = '#fff';
      btn.style.color = '#475569';
      btn.style.borderColor = '#cbd5e1';
      btn.classList.remove('active');
    });
    if (btnEl) {
      btnEl.style.background = '#0f172a';
      btnEl.style.color = '#fff';
      btnEl.style.borderColor = '#0f172a';
      btnEl.classList.add('active');
    }
    filterPeningkatan();
  }

  function openExportModal() {
    var m = document.getElementById('exportModal');
    if (m) m.style.display = 'flex';
  }
  function closeExportModal() {
    var m = document.getElementById('exportModal');
    if (m) m.style.display = 'none';
  }
  function handleExportTaChange(sel) {
    var customGrp = document.getElementById('exportCustomTaGroup');
    var newFlag = document.getElementById('exportNewTaFlag');
    if (sel.value === 'custom') {
      if (customGrp) customGrp.style.display = 'block';
      if (newFlag) newFlag.value = '1';
    } else {
      if (customGrp) customGrp.style.display = 'none';
      if (newFlag) newFlag.value = '0';
    }
  }
</script>