<?php
$pageTitle   = 'Dokumen Belum Dikerjakan';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Dokumen Belum Dikerjakan'],
];

$incList = $incompleteItems ?? [];
$projList = $projects ?? [];
$totalCount = count($incList);

$countPen  = count(array_filter($incList, fn($x) => $x['stage'] === 'penetapan'));
$countPel  = count(array_filter($incList, fn($x) => $x['stage'] === 'pelaksanaan'));
$countEva  = count(array_filter($incList, fn($x) => $x['stage'] === 'evaluasi'));
$countPenG = count(array_filter($incList, fn($x) => $x['stage'] === 'pengendalian'));
$countPenI = count(array_filter($incList, fn($x) => $x['stage'] === 'peningkatan'));
?>

<!-- Header Banner -->
<div style="background:#fff;border:1.5px solid var(--border);border-radius:var(--radius);padding:20px 24px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:20px;box-shadow:var(--shadow);">
  <div style="display:flex;align-items:center;gap:16px;">
    <div style="width:44px;height:44px;border-radius:10px;background:#fef2f2;border:1.5px solid #fca5a5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="22" height="22" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24">
        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
      </svg>
    </div>
    <div>
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
        <h2 style="font-size:16px;font-weight:800;color:var(--text-main);margin:0;">Dokumen Belum Dikerjakan</h2>
        <span style="background:#ef4444;color:#fff;font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;">
          <?= $totalCount ?> item
        </span>
      </div>
      <p style="font-size:12.5px;color:var(--text-muted);margin:0;">Dokumen dan tahap PPEPP dikelompokkan per <strong>Project / Tahun Ajaran</strong> agar lebih mudah dipilah dan ditindaklanjuti.</p>
    </div>
  </div>
  <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
    <a href="<?= BASE_URL ?>/ppepp" class="btn btn-outline btn-sm">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/><path d="M3 7l9 6 9-6"/></svg>
      Library Project PPEPP
    </a>
  </div>
</div>

<!-- Toolbar Filter per Project, Stage, Status & Search -->
<div class="card mb-4" style="margin-bottom:20px;">
  <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
    
    <!-- Row 1: Filter Project Dropdown & Search -->
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
      <!-- Filter Project Dropdown -->
      <div style="flex:1;min-width:240px;">
        <label style="font-size:12px;font-weight:700;color:var(--text-muted);margin:0 0 4px;display:block;text-transform:uppercase;letter-spacing:0.4px;">Project / Tahun Ajaran</label>
        <select id="pendingProjectSelect" onchange="applyPendingFilters()" class="form-control" style="font-size:13px;">
          <option value="all">Semua Project (Tahun Ajaran)</option>
          <?php foreach ($projList as $pj): ?>
          <?php
          $pCount = count(array_filter($incList, fn($x) => $x['project_id'] == $pj['id']));
          ?>
          <option value="<?= $pj['id'] ?>">
            Project TA <?= htmlspecialchars($pj['ta_nama']) ?> — (<?= $pCount ?> Perlu Dikerjakan)
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Filter Status Dropdown -->
      <div style="width:180px;">
        <label style="font-size:12px;font-weight:700;color:var(--text-muted);margin:0 0 4px;display:block;text-transform:uppercase;letter-spacing:0.4px;">Status Dokumen</label>
        <select id="pendingStatusSelect" onchange="applyPendingFilters()" class="form-control" style="font-size:13px;">
          <option value="all">Semua Status</option>
          <option value="empty">Belum Dibuat</option>
          <option value="draft">Draft</option>
        </select>
      </div>

      <!-- Live Search Box -->
      <div style="flex:1;min-width:200px;">
        <label style="font-size:12px;font-weight:700;color:var(--text-muted);margin:0 0 4px;display:block;text-transform:uppercase;letter-spacing:0.4px;">Cari Kata Kunci</label>
        <div style="position:relative;">
          <input type="text" id="pendingSearchInput" onkeyup="applyPendingFilters()" placeholder="Cari nama dokumen..." class="form-control"
                 style="padding-left:34px;font-size:13px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" width="16" height="16"
               style="position:absolute;left:10px;top:50%;transform:translateY(-50%);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
      </div>
    </div>

    <!-- Row 2: Filter Stage Buttons -->
    <div style="border-top:1px solid var(--border);padding-top:10px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
      <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Filter Tahap PPEPP:</div>
      <div style="display:flex;gap:6px;flex-wrap:wrap;" id="stageFilterButtons">
        <button type="button" onclick="filterPendingStage('all', this)" class="btn btn-sm pending-stage-btn active"
                style="font-size:12px;font-weight:700;padding:6px 14px;border-radius:8px;background:#1e293b;color:#fff;border:1px solid #1e293b;cursor:pointer;">
          Semua Tahap (<?= $totalCount ?>)
        </button>
        <button type="button" onclick="filterPendingStage('penetapan', this)" class="btn btn-sm pending-stage-btn"
                style="font-size:12px;font-weight:700;padding:6px 14px;border-radius:8px;background:#fff;color:#1d4ed8;border:1px solid #bfdbfe;cursor:pointer;">
          Penetapan (<?= $countPen ?>)
        </button>
        <button type="button" onclick="filterPendingStage('pelaksanaan', this)" class="btn btn-sm pending-stage-btn"
                style="font-size:12px;font-weight:700;padding:6px 14px;border-radius:8px;background:#fff;color:#047857;border:1px solid #a7f3d0;cursor:pointer;">
          Pelaksanaan (<?= $countPel ?>)
        </button>
        <button type="button" onclick="filterPendingStage('evaluasi', this)" class="btn btn-sm pending-stage-btn"
                style="font-size:12px;font-weight:700;padding:6px 14px;border-radius:8px;background:#fff;color:#6d28d9;border:1px solid #ddd6fe;cursor:pointer;">
          Evaluasi (<?= $countEva ?>)
        </button>
        <button type="button" onclick="filterPendingStage('pengendalian', this)" class="btn btn-sm pending-stage-btn"
                style="font-size:12px;font-weight:700;padding:6px 14px;border-radius:8px;background:#fff;color:#c2410c;border:1px solid #ffedd5;cursor:pointer;">
          Pengendalian (<?= $countPenG ?>)
        </button>
        <button type="button" onclick="filterPendingStage('peningkatan', this)" class="btn btn-sm pending-stage-btn"
                style="font-size:12px;font-weight:700;padding:6px 14px;border-radius:8px;background:#fff;color:#059669;border:1px solid #a7f3d0;cursor:pointer;">
          Peningkatan (<?= $countPenI ?>)
        </button>
      </div>
    </div>

  </div>
</div>

<!-- ============================================================
     DAFTAR DOKUMEN DIKELOMPOKKAN PER PROJECT
     ============================================================ -->
<?php if (empty($incList)): ?>
<div class="card" style="border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.05);border:1.5px solid #a7f3d0;padding:48px 24px;text-align:center;background:#ecfdf5;">
  <div style="font-size:36px;margin-bottom:12px;">🎉</div>
  <h3 style="font-size:18px;font-weight:800;color:#065f46;margin:0 0 6px;">Luar Biasa! Seluruh Dokumen PPEPP Telah Selesai</h3>
  <p style="font-size:14px;color:#047857;margin:0;">Tidak ada dokumen atau tahap PPEPP yang masih kosong atau berstatus draft.</p>
</div>
<?php else: ?>

<div id="projectGroupsContainer" style="display:flex;flex-direction:column;gap:24px;">
  <?php foreach ($projList as $pj): ?>
  <?php
  $pjId = $pj['id'];
  $pjTitle = $pj['judul'] ?? ('Project TA ' . $pj['ta_nama']);
  $pjItems = array_values(array_filter($incList, fn($x) => $x['project_id'] == $pjId));
  if (empty($pjItems)) continue;
  ?>
  <div class="project-group-card" data-project-id="<?= $pjId ?>"
       style="background:#fff;border:1.5px solid #cbd5e1;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.04);overflow:hidden;transition:all 0.2s;">
    
    <!-- Project Group Header -->
    <div style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);padding:18px 24px;border-bottom:1.5px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#1a237e,#3f51b5);color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 3px 10px rgba(26,35,126,0.25);flex-shrink:0;">
          📁
        </div>
        <div>
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px;">
            <h3 style="font-size:17px;font-weight:900;color:#0f172a;margin:0;">
              Project PPEPP — Tahun Ajaran <?= htmlspecialchars($pj['ta_nama']) ?>
            </h3>
            <span class="badge" style="background:#ef4444;color:#fff;font-weight:800;font-size:11.5px;padding:3px 10px;border-radius:20px;">
              <?= count($pjItems) ?> Perlu Dikerjakan
            </span>
          </div>
          <p style="font-size:12.5px;color:#64748b;margin:0;"><?= htmlspecialchars($pjTitle) ?></p>
        </div>
      </div>

      <a href="<?= BASE_URL ?>/ppepp/<?= $pjId ?>" class="btn btn-outline btn-sm"
         style="font-weight:800;font-size:12.5px;padding:7px 16px;border-radius:8px;background:#fff;border-color:#a5b4fc;color:#312e81;display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
        Buka Detail Project Ini →
      </a>
    </div>

    <!-- Project Items Sub-Grid -->
    <div style="padding:20px 24px;">
      <div class="project-items-grid" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(330px, 1fr));gap:16px;">
        <?php foreach ($pjItems as $item): ?>
        <?php
        $stageColors = [
          'penetapan'   => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe', 'icon' => 'P1'],
          'pelaksanaan' => ['bg' => '#ecfdf5', 'text' => '#047857', 'border' => '#a7f3d0', 'icon' => 'P2'],
          'evaluasi'    => ['bg' => '#f5f3ff', 'text' => '#6d28d9', 'border' => '#ddd6fe', 'icon' => 'E'],
          'pengendalian'=> ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#ffedd5', 'icon' => 'P3'],
          'peningkatan' => ['bg' => '#ecfdf5', 'text' => '#059669', 'border' => '#a7f3d0', 'icon' => 'P4'],
        ];
        $sc = $stageColors[$item['stage']] ?? ['bg' => '#f8fafc', 'text' => '#334155', 'border' => '#cbd5e1', 'icon' => 'DOC'];
        ?>
        <div class="pending-card"
             data-project-id="<?= $item['project_id'] ?>"
             data-stage="<?= $item['stage'] ?>"
             data-status="<?= $item['status'] ?>"
             data-title="<?= strtolower(htmlspecialchars($item['title'] . ' ' . $item['ta_nama'])) ?>"
             style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:18px;display:flex;flex-direction:column;justify-content:space-between;gap:14px;box-shadow:0 2px 8px rgba(0,0,0,0.03);transition:all 0.2s;">
          
          <div>
            <!-- Stage & Status Badges -->
            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:10px;">
              <div style="display:inline-flex;align-items:center;gap:6px;background:<?= $sc['bg'] ?>;color:<?= $sc['text'] ?>;border:1px solid <?= $sc['border'] ?>;padding:4px 12px;border-radius:20px;font-weight:800;font-size:11.5px;text-transform:uppercase;">
                <span style="font-weight:900;"><?= $sc['icon'] ?></span>
                <span><?= htmlspecialchars($item['stage_name']) ?></span>
              </div>

              <span style="font-size:11.5px;font-weight:800;background:<?= $item['badge_bg'] ?>;color:<?= $item['badge_text'] ?>;border:1px solid <?= $item['badge_border'] ?>;padding:4px 12px;border-radius:20px;">
                ● <?= htmlspecialchars($item['status_label']) ?>
              </span>
            </div>

            <!-- Title & Info -->
            <h4 style="font-size:14.5px;font-weight:800;color:#0f172a;margin:0 0 6px;line-height:1.4;">
              <?= htmlspecialchars($item['title']) ?>
            </h4>
            <p style="font-size:12.5px;color:#64748b;margin:0;line-height:1.5;background:#f8fafc;padding:10px 12px;border-radius:8px;border:1px solid #f1f5f9;">
              <?= htmlspecialchars($item['desc']) ?>
            </p>
          </div>

          <!-- Action Button -->
          <div style="border-top:1px solid #f1f5f9;padding-top:10px;display:flex;justify-content:flex-end;">
            <a href="<?= $item['action_url'] ?>" class="btn btn-primary btn-sm"
               style="font-weight:800;font-size:12.5px;padding:8px 16px;border-radius:8px;background:linear-gradient(135deg,#059669,#10b981);border:none;box-shadow:0 3px 10px rgba(16,185,129,0.25);display:inline-flex;align-items:center;gap:6px;text-decoration:none;color:#fff;">
              <?= htmlspecialchars($item['action_label']) ?>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
          </div>

        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
  <?php endforeach; ?>
</div>

<div id="pendingNoResult" style="display:none;text-align:center;padding:48px 20px;background:#fff;border:1.5px solid #cbd5e1;border-radius:16px;color:#64748b;font-size:14px;font-style:italic;">
  Tidak ditemukan dokumen yang cocok dengan filter Project, Tahap, Status, atau Kata Kunci yang dipilih.
</div>
<?php endif; ?>

<script>
var currentPendingStage = 'all';

function filterPendingStage(stage, btn) {
  currentPendingStage = stage;
  document.querySelectorAll('.pending-stage-btn').forEach(function(b) {
    b.style.background = '#fff';
    b.style.color = b.getAttribute('onclick').includes('penetapan') ? '#1d4ed8' :
                    (b.getAttribute('onclick').includes('pelaksanaan') ? '#047857' :
                    (b.getAttribute('onclick').includes('evaluasi') ? '#6d28d9' :
                    (b.getAttribute('onclick').includes('pengendalian') ? '#c2410c' :
                    (b.getAttribute('onclick').includes('peningkatan') ? '#059669' : '#1e293b'))));
    b.classList.remove('active');
  });

  btn.style.background = '#1e293b';
  btn.style.color = '#fff';
  btn.classList.add('active');

  applyPendingFilters();
}

function applyPendingFilters() {
  var projVal   = document.getElementById('pendingProjectSelect').value;
  var statusVal = document.getElementById('pendingStatusSelect').value;
  var searchVal = (document.getElementById('pendingSearchInput').value || '').toLowerCase();

  var projectGroups = document.querySelectorAll('.project-group-card');
  var totalVisibleCards = 0;

  projectGroups.forEach(function(group) {
    var groupId = group.getAttribute('data-project-id');
    var matchProj = (projVal === 'all' || groupId === projVal);

    if (!matchProj) {
      group.style.display = 'none';
      return;
    }

    var cards = group.querySelectorAll('.pending-card');
    var visibleInGroup = 0;

    cards.forEach(function(c) {
      var cStage  = c.getAttribute('data-stage');
      var cStatus = c.getAttribute('data-status');
      var cTitle  = c.getAttribute('data-title') || '';

      var matchStage  = (currentPendingStage === 'all' || cStage === currentPendingStage);
      var matchStatus = (statusVal === 'all' || cStatus === statusVal);
      var matchSearch = (searchVal === '' || cTitle.includes(searchVal));

      if (matchStage && matchStatus && matchSearch) {
        c.style.display = 'flex';
        visibleInGroup++;
        totalVisibleCards++;
      } else {
        c.style.display = 'none';
      }
    });

    if (visibleInGroup > 0) {
      group.style.display = 'block';
    } else {
      group.style.display = 'none';
    }
  });

  var noRes = document.getElementById('pendingNoResult');
  if (noRes) {
    noRes.style.display = (totalVisibleCards === 0) ? 'block' : 'none';
  }
}
</script>
