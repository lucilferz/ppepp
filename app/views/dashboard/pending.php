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
<div class="card mb-4" style="background:#fff;padding:18px 22px;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:14px;">
      <div style="width:40px;height:40px;border-radius:var(--radius-sm);background:var(--danger-bg);border:1px solid var(--danger-border);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="20" height="20" fill="none" stroke="var(--danger)" stroke-width="2" viewBox="0 0 24 24">
          <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
      </div>
      <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px;">
          <h2 style="font-size:16px;font-weight:700;color:var(--text-main);margin:0;">Dokumen Belum Dikerjakan</h2>
          <span class="badge badge-warning" style="font-weight:700;">
            <?= $totalCount ?> item
          </span>
        </div>
        <p style="font-size:12.5px;color:var(--text-muted);margin:0;">Dokumen dan tahap PPEPP dikelompokkan per <strong>Project / Tahun Ajaran</strong> untuk mempermudah pemantauan dan penyelesaian.</p>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
      <a href="<?= BASE_URL ?>/ppepp" class="btn btn-outline btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/><path d="M3 7l9 6 9-6"/></svg>
        Library Project PPEPP
      </a>
    </div>
  </div>
</div>

<!-- Toolbar Filter per Project, Stage, Status & Search -->
<div class="card mb-4">
  <div class="card-body" style="display:flex;flex-direction:column;gap:12px;padding:16px 20px;">
    
    <!-- Row 1: Filter Project Dropdown & Search -->
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
      <!-- Filter Project Dropdown -->
      <div style="flex:1;min-width:240px;">
        <label style="font-size:11.5px;font-weight:600;color:var(--text-muted);margin:0 0 4px;display:block;text-transform:uppercase;letter-spacing:0.04em;">Project / Tahun Ajaran</label>
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
        <label style="font-size:11.5px;font-weight:600;color:var(--text-muted);margin:0 0 4px;display:block;text-transform:uppercase;letter-spacing:0.04em;">Status Dokumen</label>
        <select id="pendingStatusSelect" onchange="applyPendingFilters()" class="form-control" style="font-size:13px;">
          <option value="all">Semua Status</option>
          <option value="empty">Belum Dibuat</option>
          <option value="draft">Draft</option>
        </select>
      </div>

      <!-- Live Search Box -->
      <div style="flex:1;min-width:200px;">
        <label style="font-size:11.5px;font-weight:600;color:var(--text-muted);margin:0 0 4px;display:block;text-transform:uppercase;letter-spacing:0.04em;">Cari Kata Kunci</label>
        <div style="position:relative;">
          <input type="text" id="pendingSearchInput" onkeyup="applyPendingFilters()" placeholder="Cari nama dokumen..." class="form-control"
                 style="padding-left:32px;font-size:13px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" width="15" height="15"
               style="position:absolute;left:10px;top:50%;transform:translateY(-50%);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
      </div>
    </div>

    <!-- Row 2: Filter Stage Buttons -->
    <div style="border-top:1px solid var(--border);padding-top:10px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
      <div style="font-size:11.5px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.04em;">Filter Tahap PPEPP:</div>
      <div style="display:flex;gap:6px;flex-wrap:wrap;" id="stageFilterButtons">
        <button type="button" onclick="filterPendingStage('all', this)" class="btn btn-sm pending-stage-btn active"
                style="font-size:12px;font-weight:600;background:var(--primary);color:#fff;border-color:var(--primary);">
          Semua Tahap (<?= $totalCount ?>)
        </button>
        <button type="button" onclick="filterPendingStage('penetapan', this)" class="btn btn-sm pending-stage-btn"
                style="font-size:12px;font-weight:600;background:#fff;color:var(--primary);border-color:var(--border);">
          Penetapan (<?= $countPen ?>)
        </button>
        <button type="button" onclick="filterPendingStage('pelaksanaan', this)" class="btn btn-sm pending-stage-btn"
                style="font-size:12px;font-weight:600;background:#fff;color:var(--success);border-color:var(--border);">
          Pelaksanaan (<?= $countPel ?>)
        </button>
        <button type="button" onclick="filterPendingStage('evaluasi', this)" class="btn btn-sm pending-stage-btn"
                style="font-size:12px;font-weight:600;background:#fff;color:#6D28D9;border-color:var(--border);">
          Evaluasi (<?= $countEva ?>)
        </button>
        <button type="button" onclick="filterPendingStage('pengendalian', this)" class="btn btn-sm pending-stage-btn"
                style="font-size:12px;font-weight:600;background:#fff;color:var(--warning);border-color:var(--border);">
          Pengendalian (<?= $countPenG ?>)
        </button>
        <button type="button" onclick="filterPendingStage('peningkatan', this)" class="btn btn-sm pending-stage-btn"
                style="font-size:12px;font-weight:600;background:#fff;color:var(--success);border-color:var(--border);">
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
<div class="card" style="border:1px solid var(--success-border);padding:40px 24px;text-align:center;background:var(--success-bg);">
  <div style="width:48px;height:48px;border-radius:50%;background:#D1FAE5;color:var(--success);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
      <polyline points="20 6 9 17 4 12"/>
    </svg>
  </div>
  <h3 style="font-size:16px;font-weight:700;color:#065f46;margin:0 0 4px;">Seluruh Dokumen PPEPP Telah Selesai</h3>
  <p style="font-size:13px;color:#047857;margin:0;">Tidak ada dokumen atau tahap PPEPP yang masih kosong atau berstatus draft.</p>
</div>
<?php else: ?>

<div id="projectGroupsContainer" style="display:flex;flex-direction:column;gap:20px;">
  <?php foreach ($projList as $pj): ?>
  <?php
  $pjId = $pj['id'];
  $pjTitle = $pj['judul'] ?? ('Project TA ' . $pj['ta_nama']);
  $pjItems = array_values(array_filter($incList, fn($x) => $x['project_id'] == $pjId));
  if (empty($pjItems)) continue;
  ?>
  <div class="project-group-card card" data-project-id="<?= $pjId ?>" style="overflow:hidden;margin-bottom:0;">
    
    <!-- Project Group Header -->
    <div style="background:#FFFFFF;padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:36px;height:36px;border-radius:var(--radius-sm);background:var(--primary-subtle);border:1px solid #D3E0F0;color:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
            <path d="M3 7l9 6 9-6"/>
          </svg>
        </div>
        <div>
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px;">
            <h3 style="font-size:15px;font-weight:700;color:var(--text-main);margin:0;">
              Project PPEPP — Tahun Ajaran <?= htmlspecialchars($pj['ta_nama']) ?>
            </h3>
            <span class="badge badge-warning" style="font-weight:700;">
              <?= count($pjItems) ?> Perlu Dikerjakan
            </span>
          </div>
          <p style="font-size:12px;color:var(--text-muted);margin:0;"><?= htmlspecialchars($pjTitle) ?></p>
        </div>
      </div>

      <a href="<?= BASE_URL ?>/ppepp/<?= $pjId ?>" class="btn btn-outline btn-sm">
        Buka Detail Project →
      </a>
    </div>

    <!-- Project Items Sub-Grid -->
    <div style="padding:16px 20px;">
      <div class="project-items-grid" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:14px;">
        <?php foreach ($pjItems as $item): ?>
        <?php
        $stageColors = [
          'penetapan'   => ['bg' => 'var(--primary-subtle)', 'text' => 'var(--primary)', 'border' => '#D3E0F0', 'icon' => 'P1'],
          'pelaksanaan' => ['bg' => 'var(--success-bg)', 'text' => 'var(--success)', 'border' => 'var(--success-border)', 'icon' => 'P2'],
          'evaluasi'    => ['bg' => '#F5F3FF', 'text' => '#6D28D9', 'border' => '#DDD6FE', 'icon' => 'E'],
          'pengendalian'=> ['bg' => 'var(--warning-bg)', 'text' => 'var(--warning)', 'border' => 'var(--warning-border)', 'icon' => 'P3'],
          'peningkatan' => ['bg' => 'var(--success-bg)', 'text' => 'var(--success)', 'border' => 'var(--success-border)', 'icon' => 'P4'],
        ];
        $sc = $stageColors[$item['stage']] ?? ['bg' => '#F8FAFC', 'text' => '#334155', 'border' => '#CBD5E1', 'icon' => 'DOC'];
        ?>
        <div class="pending-card"
             data-project-id="<?= $item['project_id'] ?>"
             data-stage="<?= $item['stage'] ?>"
             data-status="<?= $item['status'] ?>"
             data-title="<?= strtolower(htmlspecialchars($item['title'] . ' ' . $item['ta_nama'])) ?>"
             style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:14px;display:flex;flex-direction:column;justify-content:space-between;gap:12px;box-shadow:var(--shadow-xs);">
          
          <div>
            <!-- Stage & Status Badges -->
            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px;">
              <span class="badge" style="background:<?= $sc['bg'] ?>;color:<?= $sc['text'] ?>;border-color:<?= $sc['border'] ?>;font-weight:700;">
                <?= $sc['icon'] ?> • <?= htmlspecialchars($item['stage_name']) ?>
              </span>

              <span class="badge badge-dot" style="background:<?= $item['badge_bg'] ?>;color:<?= $item['badge_text'] ?>;border-color:<?= $item['badge_border'] ?>;font-weight:600;">
                <?= htmlspecialchars($item['status_label']) ?>
              </span>
            </div>

            <!-- Title & Info -->
            <h4 style="font-size:13.5px;font-weight:700;color:var(--text-main);margin:0 0 6px;line-height:1.4;">
              <?= htmlspecialchars($item['title']) ?>
            </h4>
            <p style="font-size:12px;color:var(--text-muted);margin:0;line-height:1.5;background:#F8FAFC;padding:8px 10px;border-radius:var(--radius-sm);border:1px solid var(--border-subtle);">
              <?= htmlspecialchars($item['desc']) ?>
            </p>
          </div>

          <!-- Action Button -->
          <div style="border-top:1px solid var(--border-subtle);padding-top:8px;display:flex;justify-content:flex-end;">
            <a href="<?= $item['action_url'] ?>" class="btn btn-primary btn-sm">
              <?= htmlspecialchars($item['action_label']) ?>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13" height="13"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
          </div>

        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
  <?php endforeach; ?>
</div>

<div id="pendingNoResult" style="display:none;text-align:center;padding:40px 20px;background:#fff;border:1px solid var(--border);border-radius:var(--radius);color:var(--text-muted);font-size:13px;font-style:italic;">
  Tidak ditemukan dokumen yang cocok dengan filter Project, Tahap, Status, atau Kata Kunci yang dipilih.
</div>
<?php endif; ?>

<script>
var currentPendingStage = 'all';

function filterPendingStage(stage, btn) {
  currentPendingStage = stage;
  document.querySelectorAll('.pending-stage-btn').forEach(function(b) {
    b.style.background = '#fff';
    b.style.borderColor = 'var(--border)';
    b.style.color = b.getAttribute('onclick').includes('penetapan') ? 'var(--primary)' :
                    (b.getAttribute('onclick').includes('pelaksanaan') ? 'var(--success)' :
                    (b.getAttribute('onclick').includes('evaluasi') ? '#6D28D9' :
                    (b.getAttribute('onclick').includes('pengendalian') ? 'var(--warning)' :
                    (b.getAttribute('onclick').includes('peningkatan') ? 'var(--success)' : 'var(--text-main)'))));
    b.classList.remove('active');
  });

  btn.style.background = 'var(--primary)';
  btn.style.borderColor = 'var(--primary)';
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
