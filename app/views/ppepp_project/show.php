<?php
$pageTitle   = htmlspecialchars($project['judul'] ?? 'Project PPEPP');
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Project PPEPP', 'url' => BASE_URL . '/ppepp'],
  ['label' => htmlspecialchars($project['ta_nama'])],
];

$steps = [
  ['letter'=>'P','nama'=>'Penetapan',  'sub'=>'Standar & Target Mutu', 'key'=>'penetapan',   'route'=>'penetapan'],
  ['letter'=>'P','nama'=>'Pelaksanaan','sub'=>'Bukti & Realisasi Mutu','key'=>'pelaksanaan', 'route'=>'pelaksanaan'],
  ['letter'=>'E','nama'=>'Evaluasi',   'sub'=>'Capaian & Hasil Audit', 'key'=>'evaluasi',    'route'=>'evaluasi'],
  ['letter'=>'P','nama'=>'Pengendalian','sub'=>'Rencana Tindak Lanjut', 'key'=>'pengendalian','route'=>'pengendalian'],
  ['letter'=>'P','nama'=>'Peningkatan','sub'=>'Pembaruan Standar & SK','key'=>'peningkatan', 'route'=>'peningkatan'],
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

<!-- Back Navigation -->
<div class="mb-3">
  <a href="<?= BASE_URL ?>/ppepp" class="btn btn-outline btn-sm">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
      <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
    </svg>
    Kembali ke Daftar Project
  </a>
</div>

<!-- Project Context Overview Card -->
<div class="card mb-4">
  <div class="card-body" style="padding:24px 28px;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:24px;flex-wrap:wrap;">
      <div style="flex:1;min-width:280px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);">
            Project PPEPP
          </span>
          <?php
          $projStatus = $project['status'] ?? 'aktif';
          $statusBadge = match($projStatus) {
              'aktif'    => ['label' => 'Aktif', 'class' => 'badge-success'],
              'nonaktif' => ['label' => 'Tersembunyi', 'class' => 'badge-warning'],
              'arsip'    => ['label' => 'Arsip', 'class' => 'badge-neutral'],
              'selesai'  => ['label' => 'Selesai', 'class' => 'badge-info'],
              default    => ['label' => ucfirst($projStatus), 'class' => 'badge-neutral'],
          };
          ?>
          <span class="badge <?= $statusBadge['class'] ?> badge-dot">
            <?= $statusBadge['label'] ?>
          </span>
        </div>

        <h2 style="font-size:22px;font-weight:700;color:var(--text-main);margin:0 0 4px;letter-spacing:-0.02em;">
          Tahun Ajaran <?= htmlspecialchars($project['ta_nama']) ?>
        </h2>
        <div style="font-size:14px;color:var(--text-muted);"><?= htmlspecialchars($project['judul'] ?? '') ?></div>
        <?php if (!empty($project['deskripsi'])): ?>
        <p style="font-size:12.5px;color:var(--text-subtle);margin-top:6px;font-style:italic;line-height:1.5;">
          <?= htmlspecialchars($project['deskripsi']) ?>
        </p>
        <?php endif; ?>

        <!-- Progress Overview -->
        <div style="margin-top:16px;display:flex;align-items:center;gap:14px;max-width:360px;">
          <div style="flex:1;height:6px;background:var(--border-subtle);border-radius:var(--radius-pill);overflow:hidden;">
            <div style="height:100%;width:<?= $project['progress_pct'] ?>%;background:var(--primary);border-radius:var(--radius-pill);transition:width 0.3s ease;"></div>
          </div>
          <span style="font-size:12px;font-weight:700;color:var(--primary);">
            <?= $project['steps_done'] ?>/5 Tahap (<?= $project['progress_pct'] ?>%)
          </span>
        </div>
      </div>

      <!-- Action Buttons -->
      <div style="display:flex;flex-direction:column;gap:8px;min-width:180px;">
        <a href="<?= BASE_URL ?>/laporan?project_id=<?= $project['id'] ?>" class="btn btn-primary btn-sm">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
          </svg>
          Laporan Eksekutif PPEPP
        </a>
        <a href="<?= BASE_URL ?>/laporan/cetak?project_id=<?= $project['id'] ?>" target="_blank" class="btn btn-outline btn-sm" style="background:#fff;border-color:#cbd5e1;color:#1e293b;font-weight:700;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          Cetak Rekap PDF (5 Tahap) ↗
        </a>

        <div style="display:flex;gap:6px;">
          <a href="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/hidden" class="btn btn-outline btn-sm" style="flex:1;" title="Dokumen Tersembunyi">
            Tersembunyi (<?= (int)($totalHiddenCount ?? 0) ?>)
          </a>
          <a href="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/edit" class="btn btn-outline btn-sm" title="Edit Project">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
              <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
          </a>
          <button type="button" class="btn btn-danger btn-sm" data-modal-open="modalDeleteProjectShow" title="Hapus Project">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
              <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (!empty($flash['message'])): ?>
<div class="alert alert-<?= $flash['type']==='success' ? 'success' : 'error' ?>">
  <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<!-- Filter Toolbar -->
<div class="card mb-4">
  <div class="card-body" style="padding:16px 20px;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:12px;border-bottom:1px solid var(--border-subtle);padding-bottom:10px;">
      <div style="font-size:13px;font-weight:600;color:var(--text-main);">Filter Dokumen Siklus Project</div>
      <button type="button" onclick="resetProjectFilters()" class="btn btn-ghost btn-sm" style="color:var(--danger);font-size:12px;">
        Reset Filter
      </button>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:12px;">
      <div>
        <label class="form-label" style="font-size:11.5px;">Tahap PPEPP</label>
        <select id="filterStageSelect" onchange="applyProjectFilters()" class="form-control">
          <option value="all">Semua Tahap (5 Tahap)</option>
          <option value="penetapan">P1 — Penetapan Standar</option>
          <option value="pelaksanaan">P2 — Pelaksanaan Standar</option>
          <option value="evaluasi">E  — Evaluasi Capaian</option>
          <option value="pengendalian">P3 — Pengendalian (RTL)</option>
          <option value="peningkatan">P4 — Peningkatan Standar</option>
        </select>
      </div>

      <div>
        <label class="form-label" style="font-size:11.5px;">Kriteria SPMI / Prodi</label>
        <select id="filterKriteriaSelect" onchange="applyProjectFilters()" class="form-control">
          <option value="all">Semua Kriteria</option>
          <?php foreach ($allKriteria ?? [] as $ak): ?>
          <option value="<?= $ak['id'] ?>">
            <?= htmlspecialchars(($ak['kode'] ? '[' . $ak['kode'] . '] ' : '') . $ak['nama']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="form-label" style="font-size:11.5px;">Status Dokumen</label>
        <select id="filterStatusSelect" onchange="applyProjectFilters()" class="form-control">
          <option value="all">Semua Status</option>
          <option value="draft">Draft Saja</option>
          <option value="final">Final Saja</option>
          <option value="nonaktif">Nonaktif Saja</option>
        </select>
      </div>
    </div>
  </div>
</div>

<!-- 5 Steps Blocks Container -->
<div style="display:flex;flex-direction:column;gap:18px;">
<?php foreach ($steps as $stepIdx => $step): 
  $items          = $dataMaps[$step['key']];
  $visibleItems   = array_values(array_filter($items, fn($x) => ($x['visibility_status'] ?? 'aktif') !== 'hidden'));
  $hasVisibleData = !empty($visibleItems);
  $activeItems    = array_filter($visibleItems, fn($x) => ($x['visibility_status'] ?? 'aktif') === 'aktif');
  $final          = count(array_filter($activeItems, fn($x) => ($x['status'] ?? '') === 'final'));
  $total          = count($activeItems);
  $isDone         = $final > 0;
?>
  <div class="card step-card-block" data-stage="<?= $step['key'] ?>" style="overflow:hidden;">
    <!-- Step Block Header -->
    <div class="card-header" style="background:var(--surface);">
      <div style="display:flex;align-items:center;gap:14px;">
        <div style="width:36px;height:36px;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;background:var(--primary-subtle);border:1px solid #C9DBF0;color:var(--primary);flex-shrink:0;">
          <?= $step['letter'] ?>
        </div>
        <div>
          <div style="display:flex;align-items:center;gap:8px;">
            <h3 style="font-size:15px;font-weight:700;color:var(--text-main);margin:0;">
              <?= (string)($stepIdx + 1) ?>. <?= $step['nama'] ?>
            </h3>
            <?php if ($isDone): ?>
            <span class="badge badge-success badge-dot">
              <?= $final ?> Final
            </span>
            <?php elseif ($total > 0): ?>
            <span class="badge badge-warning badge-dot">
              <?= $total ?> Draft Aktif
            </span>
            <?php else: ?>
            <span class="badge badge-neutral">
              Belum dimulai
            </span>
            <?php endif; ?>
          </div>
          <div style="font-size:12px;color:var(--text-muted);margin-top:1px;"><?= $step['sub'] ?></div>
        </div>
      </div>

      <a href="<?= BASE_URL ?>/<?= $step['route'] ?>/create?project_id=<?= $project['id'] ?>" class="btn btn-primary btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Tambah <?= $step['nama'] ?>
      </a>
    </div>

    <!-- Document Items List -->
    <?php if ($hasVisibleData): ?>
    <div style="border-top:1px solid var(--border-subtle);">
      <?php foreach ($visibleItems as $ii => $item): 
        $isItemFinal    = ($item['status'] ?? '') === 'final';
        $itemVis        = $item['visibility_status'] ?? 'aktif';
        $isItemNonaktif = ($itemVis === 'nonaktif');
        $judul = htmlspecialchars($item['judul'] ?? '—');
        $acuan = htmlspecialchars($item['penetapan_judul'] ?? $item['evaluasi_judul'] ?? $item['pengendalian_judul'] ?? '');
      ?>
      <div class="doc-item-row" data-stage="<?= $step['key'] ?>" data-kriteria-ids="<?= htmlspecialchars($item['kriteria_ids_str'] ?? '') ?>" data-status="<?= htmlspecialchars($item['status'] ?? 'draft') ?>" data-visibility="<?= htmlspecialchars($itemVis) ?>"
           style="display:flex;align-items:center;gap:14px;padding:12px 20px;border-bottom:1px solid var(--border-subtle);background:<?= $isItemNonaktif ? 'var(--warning-bg)' : 'var(--surface)' ?>;">
        
        <div style="width:7px;height:7px;border-radius:50%;background:<?= $isItemNonaktif ? 'var(--warning)' : ($isItemFinal ? 'var(--success)' : 'var(--warning)') ?>;flex-shrink:0;"></div>

        <div style="flex:1;min-width:0;">
          <div style="font-size:13.5px;font-weight:600;color:var(--text-main);line-height:1.3;">
            <?= $judul ?>
          </div>

          <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:4px;">
            <?php if ($acuan): ?>
            <span class="badge badge-neutral" style="font-size:11px;">
              Acuan: <?= $acuan ?>
            </span>
            <?php endif; ?>

            <?php
            $itemKIds = array_filter(explode(',', $item['kriteria_ids_str'] ?? ''));
            foreach (array_slice($itemKIds, 0, 4) as $kId):
              $kObj = $kriteriaMap[(int)$kId] ?? null;
              if ($kObj):
            ?>
              <span class="badge badge-neutral" style="font-size:10.5px;cursor:pointer;" onclick="filterByKriteriaId('<?= $kObj['id'] ?>')" title="Filter berdasarkan kriteria ini">
                <?= htmlspecialchars($kObj['kode'] ?: $kObj['nama']) ?>
              </span>
            <?php endif; endforeach; ?>
            <?php if (count($itemKIds) > 4): ?>
              <span style="font-size:10.5px;color:var(--text-subtle);">+<?= count($itemKIds) - 4 ?> lainnya</span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Badges & Actions -->
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
          <span class="badge <?= $isItemNonaktif ? 'badge-warning' : ($isItemFinal ? 'badge-success' : 'badge-neutral') ?> badge-dot">
            <?= $isItemNonaktif ? 'Nonaktif' : ($isItemFinal ? 'Final' : 'Draft') ?>
          </span>

          <a href="<?= BASE_URL ?>/<?= $step['route'] ?>/<?= $item['id'] ?>" class="btn btn-outline btn-sm">
            Detail
          </a>

          <!-- Toggle Status -->
          <form method="POST" action="<?= BASE_URL ?>/<?= $step['route'] ?>/<?= $item['id'] ?>/toggle-status" style="margin:0;">
            <input type="hidden" name="return_url" value="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>">
            <button type="submit" class="btn btn-ghost btn-sm" title="<?= $isItemNonaktif ? 'Aktifkan Dokumen' : 'Sembunyikan/Nonaktifkan Dokumen' ?>">
              <?= $isItemNonaktif ? 'Aktifkan' : 'Hide' ?>
            </button>
          </form>

          <!-- Delete -->
          <form method="POST" action="<?= BASE_URL ?>/<?= $step['route'] ?>/<?= $item['id'] ?>/delete" style="margin:0;"
                onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?');">
            <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);" title="Hapus Dokumen">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
              </svg>
            </button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="padding:24px 20px;text-align:center;color:var(--text-subtle);font-size:12.5px;background:var(--surface);">
      Belum ada dokumen pada tahap ini. Klik <strong>&ldquo;Tambah <?= $step['nama'] ?>&rdquo;</strong> untuk memulai.
    </div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>

<!-- Modal Konfirmasi Hapus Project Permanen -->
<div class="modal-overlay" id="modalDeleteProjectShow">
  <div class="modal" style="max-width:460px;">
    <div class="modal-header" style="background:var(--danger);color:#fff;">
      <h3 class="modal-title" style="color:#fff;">Hapus Project Permanen</h3>
      <button type="button" class="modal-close-btn" style="color:#fff;" data-modal-close="modalDeleteProjectShow">&times;</button>
    </div>
    <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/destroy">
      <div class="modal-body">
        <div class="alert alert-error" style="margin-bottom:14px;">
          <div>
            <strong>PERHATIAN PENTING:</strong><br>
            Anda akan menghapus project <strong><?= htmlspecialchars($project['judul']) ?> (TA <?= htmlspecialchars($project['ta_nama']) ?>)</strong> secara <strong>PERMANEN</strong>.<br><br>
            Seluruh data 5 tahap PPEPP di dalamnya beserta berkas upload akan <strong>terhapus</strong> dan tidak dapat dikembalikan.
          </div>
        </div>
        <p style="font-size:13px;color:var(--text-main);margin:0;">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close="modalDeleteProjectShow">Batal</button>
        <button type="submit" class="btn btn-danger">Ya, Hapus Project Permanen</button>
      </div>
    </form>
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
