<?php
$pageTitle   = 'Tugas & Draft Belum Selesai';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Tugas & Draft Belum Selesai'],
];

$incList        = $incompleteItems ?? [];
$projList       = $projects ?? [];
$priorityList   = $priorityTasks ?? [];
$isKaprodiRole  = !empty($isKaprodi);
$dosenAvailable = $dosenList ?? [];

$totalCount     = count($incList);
$priorityCount  = count($priorityList);

$countPen  = count(array_filter($incList, fn($x) => $x['stage'] === 'penetapan'));
$countPel  = count(array_filter($incList, fn($x) => $x['stage'] === 'pelaksanaan'));
$countEva  = count(array_filter($incList, fn($x) => $x['stage'] === 'evaluasi'));
$countPenG = count(array_filter($incList, fn($x) => $x['stage'] === 'pengendalian'));
$countPenI = count(array_filter($incList, fn($x) => $x['stage'] === 'peningkatan'));

// Hitungan status tugas delegasi untuk Kaprodi
$assignedTasksCount  = count(array_filter($incList, fn($x) => !empty($x['task'])));
$reviewNeededCount   = count(array_filter($incList, fn($x) => !empty($x['task']) && $x['task']['status'] === 'submitted'));
$approvedTasksCount  = count(array_filter($incList, fn($x) => !empty($x['task']) && $x['task']['status'] === 'approved'));
$unassignedCount     = count(array_filter($incList, fn($x) => empty($x['task'])));
?>

<!-- Header Context Card -->
<div class="card mb-4" style="padding:22px 26px;border-left:5px solid <?= $priorityCount > 0 ? '#f59e0b' : '#3b82f6' ?>;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:16px;">
      <div style="width:48px;height:48px;border-radius:var(--radius);background:<?= $priorityCount > 0 ? '#fef3c7' : 'var(--warning-bg)' ?>;border:1px solid <?= $priorityCount > 0 ? '#fde68a' : 'var(--warning-border)' ?>;color:<?= $priorityCount > 0 ? '#d97706' : 'var(--warning)' ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <?php if ($priorityCount > 0): ?>
          <span style="font-size:24px;">🔥</span>
        <?php else: ?>
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
        <?php endif; ?>
      </div>
      <div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
          <h2 style="font-size:18px;font-weight:700;color:var(--text-main);margin:0;">
            <?= $isKaprodiRole ? 'Monitoring Tugas &amp; Delegasi Dokumen Belum Selesai' : 'Tugas &amp; Draft Dokumen Belum Selesai' ?>
          </h2>
          <?php if ($priorityCount > 0): ?>
            <span class="badge" style="background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;font-weight:700;">
              🚨 <?= $priorityCount ?> Tugas Prioritas dari Kaprodi
            </span>
          <?php endif; ?>
          <?php if ($isKaprodiRole && $reviewNeededCount > 0): ?>
            <span class="badge" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-weight:700;">
              📬 <?= $reviewNeededCount ?> Menunggu Review / ACC Kaprodi
            </span>
          <?php endif; ?>
        </div>
        <p class="page-desc" style="margin:4px 0 0;font-size:13px;">
          <?php if ($isKaprodiRole): ?>
            Sebagai Ketua Program Studi, Anda dapat memantau draft dokumen PPEPP serta <strong>mendelegasikan tugas penyusunan dokumen kepada dosen lain</strong>, memeriksa hasil pengerjaan, memberi catatan revisi, dan memberikan ACC.
          <?php else: ?>
            Daftar dokumen PPEPP yang perlu dikerjakan. Tugas yang didelegasikan langsung oleh Kaprodi akan ditandai sebagai <strong>Tugas Prioritas</strong>.
          <?php endif; ?>
        </p>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      <a href="<?= BASE_URL ?>/ppepp" class="btn btn-outline btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
        </svg>
        Library Project PPEPP
      </a>
    </div>
  </div>
</div>

<!-- ============================================================ -->
<!-- KHUSUS DOSEN: SECTION TUGAS PRIORITAS DARI KAPRODI          -->
<!-- ============================================================ -->
<?php if (!empty($priorityList)): ?>
<div class="card mb-4" style="border:2px solid #f59e0b;background:#fffdfa;box-shadow:0 4px 14px rgba(245,158,11,0.12);">
  <!-- Priority Header -->
  <div class="card-header" style="background:linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);border-bottom:1px solid #fde68a;padding:16px 20px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
      <div style="display:flex;align-items:center;gap:12px;">
        <span style="font-size:22px;">🔥</span>
        <div>
          <div style="display:flex;align-items:center;gap:8px;">
            <h3 style="font-size:15.5px;font-weight:800;color:#92400e;margin:0;letter-spacing:-0.01em;">
              TUGAS PRIORITAS DARI KAPRODI
            </h3>
            <span class="badge" style="background:#f59e0b;color:#fff;font-weight:700;font-size:11px;padding:3px 8px;border-radius:999px;">
              <?= count($priorityList) ?> TUGAS HARUS DIKERJAKAN
            </span>
          </div>
          <p style="font-size:12px;color:#b45309;margin:2px 0 0;">
            Dokumen berikut didelegasikan langsung oleh Kaprodi kepada Anda. Mohon segera kerjakan dan ajukan kembali ke Kaprodi untuk di-ACC.
          </p>
        </div>
      </div>
      <div style="font-size:11.5px;color:#b45309;font-weight:600;background:rgba(245,158,11,0.15);padding:4px 10px;border-radius:6px;border:1px solid #fde68a;">
        Prioritas Utama Penjaminan Mutu
      </div>
    </div>
  </div>

  <!-- Priority Items Grid -->
  <div class="card-body" style="padding:20px;">
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(360px, 1fr));gap:16px;">
      <?php foreach ($priorityList as $pt): ?>
      <?php
      $taskStatus = $pt['task_status']; // assigned, submitted, revision, approved
      $isRevision = ($taskStatus === 'revision');
      $isSubmitted = ($taskStatus === 'submitted');
      $isApproved = ($taskStatus === 'approved');

      $cardBorderColor = '#f59e0b';
      $cardBg = '#ffffff';
      if ($isRevision) {
        $cardBorderColor = '#ef4444';
        $cardBg = '#fffafb';
      } elseif ($isApproved) {
        $cardBorderColor = '#22c55e';
        $cardBg = '#fbfdfb';
      }
      ?>
      <div class="card" style="border:1.5px solid <?= $cardBorderColor ?>;background:<?= $cardBg ?>;padding:18px;display:flex;flex-direction:column;justify-content:space-between;gap:14px;box-shadow:0 2px 6px rgba(0,0,0,0.04);border-radius:10px;">
        <div>
          <!-- Header Badges -->
          <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:10px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:6px;">
              <span class="badge" style="background:#fef3c7;color:#b45309;font-weight:800;font-size:10.5px;border:1px solid #fde68a;">
                🚨 PRIORITAS TINGGI
              </span>
              <span class="badge badge-info" style="font-size:11px;text-transform:uppercase;">
                <?= htmlspecialchars($pt['stage_name']) ?>
              </span>
            </div>

            <!-- Task Status Badge -->
            <?php if ($isRevision): ?>
              <span class="badge" style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;font-weight:700;">
                ⚠️ Perlu Revisi
              </span>
            <?php elseif ($isSubmitted): ?>
              <span class="badge" style="background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe;font-weight:700;">
                ⏳ Menunggu Review Kaprodi
              </span>
            <?php elseif ($isApproved): ?>
              <span class="badge" style="background:#dcfce7;color:#166534;border:1px solid #86efac;font-weight:700;">
                ✅ Sudah Di-ACC
              </span>
            <?php else: ?>
              <span class="badge badge-warning" style="font-weight:700;">
                📌 Perlu Dikerjakan
              </span>
            <?php endif; ?>
          </div>

          <!-- Document Title & Project Context -->
          <h4 style="font-size:15px;font-weight:700;color:var(--text-main);margin:0 0 4px;line-height:1.4;">
            <?= htmlspecialchars($pt['title']) ?>
          </h4>
          <div style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:6px;margin-bottom:10px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
            <span>Tahun Ajaran <?= htmlspecialchars($pt['ta_nama']) ?></span>
            <?php if (!empty($pt['deadline'])): ?>
              <span>•</span>
              <span style="color:#b45309;font-weight:600;">📅 Tenggat: <?= date('d M Y', strtotime($pt['deadline'])) ?></span>
            <?php endif; ?>
          </div>

          <!-- Info Pemberi Tugas -->
          <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:8px 12px;font-size:12px;margin-bottom:10px;">
            <div style="color:#64748b;font-size:11px;">Ditugaskan oleh Kaprodi:</div>
            <div style="font-weight:700;color:#1e293b;"><?= htmlspecialchars($pt['kaprodi_nama']) ?></div>
            <div style="font-size:11px;color:#94a3b8;"><?= date('d M Y H:i', strtotime($pt['assigned_at'])) ?></div>
          </div>

          <!-- Instruksi Kaprodi -->
          <?php if (!empty($pt['instruksi'])): ?>
          <div style="background:#eff6ff;border-left:3px solid #3b82f6;border-radius:4px;padding:8px 12px;font-size:12px;color:#1e40af;margin-bottom:10px;">
            <div style="font-weight:700;font-size:11.5px;margin-bottom:2px;">📋 Instruksi dari Kaprodi:</div>
            <div style="color:#1e3a8a;"><?= nl2br(htmlspecialchars($pt['instruksi'])) ?></div>
          </div>
          <?php endif; ?>

          <!-- Alert Khusus: Note Catatan Revisi dari Kaprodi -->
          <?php if ($isRevision && !empty($pt['catatan_revisi'])): ?>
          <div style="background:#fef2f2;border:1px solid #fecaca;border-left:4px solid #ef4444;border-radius:6px;padding:10px 14px;margin-bottom:10px;">
            <div style="font-weight:800;color:#991b1b;font-size:12px;display:flex;align-items:center;gap:6px;">
              <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              CATATAN REVISI DARI KAPRODI:
            </div>
            <div style="font-size:12.5px;color:#7f1d1d;margin-top:4px;line-height:1.45;background:#ffffff;padding:8px 10px;border-radius:4px;border:1px solid #fee2e2;">
              <?= nl2br(htmlspecialchars($pt['catatan_revisi'])) ?>
            </div>
            <div style="font-size:11px;color:#991b1b;margin-top:4px;">
              *Silakan perbaiki dokumen di bawah, lalu klik tombol <strong>"Ajukan Ulang ke Kaprodi"</strong>.
            </div>
          </div>
          <?php endif; ?>

          <!-- Status Info: Submitted / Approved -->
          <?php if ($isSubmitted): ?>
          <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-left:3px solid #22c55e;border-radius:4px;padding:8px 12px;font-size:12px;color:#166534;margin-bottom:10px;">
            <div style="font-weight:700;">📬 Sudah Diajukan ke Kaprodi</div>
            <div style="font-size:11.5px;margin-top:1px;">
              Tugas telah diserahkan pada <?= date('d M Y H:i', strtotime($pt['submitted_at'])) ?>. Saat ini menunggu pemeriksaan dan persetujuan (ACC) dari Kaprodi.
            </div>
            <?php if (!empty($pt['catatan_dosen'])): ?>
              <div style="margin-top:4px;font-size:11px;color:#15803d;">
                <strong>Catatan yang Anda kirim:</strong> "<?= htmlspecialchars($pt['catatan_dosen']) ?>"
              </div>
            <?php endif; ?>
          </div>
          <?php elseif ($isApproved): ?>
          <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-left:3px solid #10b981;border-radius:4px;padding:8px 12px;font-size:12px;color:#065f46;margin-bottom:10px;">
            <div style="font-weight:700;">🎉 Tugas Telah Disetujui (Di-ACC) oleh Kaprodi</div>
            <div style="font-size:11.5px;margin-top:1px;">
              Diperiksa dan disetujui pada <?= date('d M Y H:i', strtotime($pt['reviewed_at'])) ?>. Dokumen telah valid.
            </div>
          </div>
          <?php endif; ?>
        </div>

        <!-- Action Buttons for Lecturer -->
        <div style="border-top:1px solid var(--border-subtle);padding-top:12px;display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
          <a href="<?= $pt['action_url'] ?>" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
            <?= htmlspecialchars($pt['action_label']) ?>
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </a>

          <?php if (!$isApproved): ?>
            <button type="button" 
                    onclick="openSubmitTaskModal(<?= $pt['task_id'] ?>, '<?= htmlspecialchars(addslashes($pt['title'])) ?>', '<?= htmlspecialchars(addslashes($pt['kaprodi_nama'])) ?>')" 
                    class="btn <?= $isRevision ? 'btn-danger' : 'btn-outline' ?> btn-sm" 
                    style="display:inline-flex;align-items:center;gap:6px;font-weight:600;">
              <span>🚀</span>
              <span><?= $isRevision ? 'Ajukan Ulang ke Kaprodi' : ($isSubmitted ? 'Update Pengajuan' : 'Tandai Selesai &amp; Ajukan ke Kaprodi') ?></span>
            </button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ============================================================ -->
<!-- FILTER TOOLBAR                                              -->
<!-- ============================================================ -->
<div class="card mb-4">
  <div class="card-body" style="display:flex;flex-direction:column;gap:14px;padding:16px 20px;">
    <!-- Row 1: Dropdown filters & Search -->
    <div style="display:grid;grid-template-columns:<?= $isKaprodiRole ? '1.5fr 1fr 1.2fr 1.5fr' : '2fr 1fr 2fr' ?>;gap:12px;" class="filter-controls-row">
      <div>
        <label class="form-label" style="font-size:11.5px;text-transform:uppercase;letter-spacing:0.04em;">Filter Project / Tahun Ajaran</label>
        <select id="pendingProjectSelect" onchange="applyPendingFilters()" class="form-control">
          <option value="all">Semua Project (Tahun Ajaran)</option>
          <?php foreach ($projList as $pj): ?>
          <?php $pCount = count(array_filter($incList, fn($x) => $x['project_id'] == $pj['id'])); ?>
          <option value="<?= $pj['id'] ?>">
            TA <?= htmlspecialchars($pj['ta_nama']) ?> (<?= $pCount ?> Perlu Dikerjakan)
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="form-label" style="font-size:11.5px;text-transform:uppercase;letter-spacing:0.04em;">Status Dokumen</label>
        <select id="pendingStatusSelect" onchange="applyPendingFilters()" class="form-control">
          <option value="all">Semua Status</option>
          <option value="empty">Belum Dibuat</option>
          <option value="draft">Draft</option>
        </select>
      </div>

      <?php if ($isKaprodiRole): ?>
      <div>
        <label class="form-label" style="font-size:11.5px;text-transform:uppercase;letter-spacing:0.04em;">Status Delegasi Tugas</label>
        <select id="pendingTaskFilterSelect" onchange="applyPendingFilters()" class="form-control">
          <option value="all">Semua Penugasan</option>
          <option value="unassigned">Belum Ditugaskan (<?= $unassignedCount ?>)</option>
          <option value="assigned">Sedang Dikerjakan Dosen</option>
          <option value="submitted">📬 Menunggu Review / ACC (<?= $reviewNeededCount ?>)</option>
          <option value="revision">⚠️ Sedang Direvisi Dosen</option>
          <option value="approved">✅ Sudah Di-ACC (<?= $approvedTasksCount ?>)</option>
        </select>
      </div>
      <?php endif; ?>

      <div>
        <label class="form-label" style="font-size:11.5px;text-transform:uppercase;letter-spacing:0.04em;">Cari Nama Dokumen</label>
        <div class="input-icon-wrap">
          <span class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
          </span>
          <input type="text" id="pendingSearchInput" onkeyup="applyPendingFilters()" placeholder="Ketik kata kunci dokumen..." class="form-control">
        </div>
      </div>
    </div>

    <!-- Row 2: Stage segmented filters -->
    <div style="border-top:1px solid var(--border);padding-top:12px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
      <span style="font-size:12px;font-weight:600;color:var(--text-muted);">Tahapan PPEPP:</span>
      <div class="tabs-segmented" style="margin-bottom:0;" id="stageFilterButtons">
        <button type="button" onclick="filterPendingStage('all', this)" class="btn-filter active">
          Semua (<?= $totalCount ?>)
        </button>
        <button type="button" onclick="filterPendingStage('penetapan', this)" class="btn-filter">
          Penetapan (<?= $countPen ?>)
        </button>
        <button type="button" onclick="filterPendingStage('pelaksanaan', this)" class="btn-filter">
          Pelaksanaan (<?= $countPel ?>)
        </button>
        <button type="button" onclick="filterPendingStage('evaluasi', this)" class="btn-filter">
          Evaluasi (<?= $countEva ?>)
        </button>
        <button type="button" onclick="filterPendingStage('pengendalian', this)" class="btn-filter">
          Pengendalian (<?= $countPenG ?>)
        </button>
        <button type="button" onclick="filterPendingStage('peningkatan', this)" class="btn-filter">
          Peningkatan (<?= $countPenI ?>)
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ============================================================ -->
<!-- DAFTAR DOKUMEN BELUM SELESAI PER PROJECT                    -->
<!-- ============================================================ -->
<?php if (empty($incList)): ?>
<div class="card" style="padding:48px 24px;text-align:center;">
  <div class="empty-icon" style="color:var(--success);background:var(--success-bg);border-color:var(--success-border);margin:0 auto 16px;">
    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
      <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
  </div>
  <h3 style="font-size:16px;font-weight:700;color:var(--text-main);margin-bottom:4px;">Semua Dokumen PPEPP Telah Lengkap</h3>
  <p style="font-size:13px;color:var(--text-muted);margin:0;">Tidak ditemukan dokumen atau tahap PPEPP yang masih kosong atau draft.</p>
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
  <div class="card project-group-card" data-project-id="<?= $pjId ?>">
    <!-- Group Header -->
    <div class="card-header" style="background:var(--surface-hover);padding:14px 20px;">
      <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;width:100%;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:12px;">
          <div style="width:36px;height:36px;border-radius:var(--radius-sm);background:var(--primary-subtle);border:1px solid #C9DBF0;color:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
              <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
            </svg>
          </div>
          <div>
            <div style="display:flex;align-items:center;gap:8px;">
              <h3 style="font-size:15px;font-weight:700;color:var(--text-main);margin:0;">
                Project PPEPP — Tahun Ajaran <?= htmlspecialchars($pj['ta_nama']) ?>
              </h3>
              <span class="badge badge-warning"><?= count($pjItems) ?> Item Belum Selesai</span>
            </div>
            <div style="font-size:12px;color:var(--text-muted);margin-top:1px;"><?= htmlspecialchars($pjTitle) ?></div>
          </div>
        </div>
        <a href="<?= BASE_URL ?>/ppepp/<?= $pjId ?>" class="btn btn-outline btn-sm">
          Buka Detail Project &rarr;
        </a>
      </div>
    </div>

    <!-- Group Items Grid -->
    <div class="card-body" style="padding:18px;">
      <div class="project-items-grid" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(340px, 1fr));gap:16px;">
        <?php foreach ($pjItems as $item): ?>
        <?php
        $task = $item['task'] ?? null;
        $tStatus = $task['status'] ?? 'unassigned';
        ?>
        <div class="card pending-card"
             data-project-id="<?= $item['project_id'] ?>"
             data-stage="<?= $item['stage'] ?>"
             data-status="<?= $item['status'] ?>"
             data-task-status="<?= $tStatus ?>"
             data-title="<?= strtolower(htmlspecialchars($item['title'] . ' ' . $item['ta_nama'] . ' ' . ($task['dosen_nama'] ?? ''))) ?>"
             style="padding:18px;display:flex;flex-direction:column;justify-content:space-between;gap:12px;border:1px solid <?= !empty($task) && $task['status'] === 'submitted' ? '#3b82f6' : 'var(--border)' ?>;border-radius:10px;">
          <div>
            <!-- Header Badges -->
            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px;">
              <span class="badge badge-info" style="font-size:11px;text-transform:uppercase;">
                <?= htmlspecialchars($item['stage_name']) ?>
              </span>
              <span class="badge <?= $item['status'] === 'draft' ? 'badge-warning' : 'badge-danger' ?> badge-dot">
                <?= htmlspecialchars($item['status_label']) ?>
              </span>
            </div>

            <!-- Title & Context -->
            <h4 style="font-size:14px;font-weight:700;color:var(--text-main);margin:0 0 4px;line-height:1.4;">
              <?= htmlspecialchars($item['title']) ?>
            </h4>
            <p style="font-size:12px;color:var(--text-muted);margin:0 0 10px;line-height:1.5;">
              <?= htmlspecialchars($item['desc']) ?>
            </p>

            <!-- ============================================================ -->
            <!-- KAPRODI: KOTAK DELEGASI TUGAS KE DOSEN                       -->
            <!-- ============================================================ -->
            <?php if ($isKaprodiRole): ?>
              <?php if (empty($task)): ?>
                <!-- Belum Ditugaskan -->
                <div style="background:#f8fafc;border:1px dashed #cbd5e1;border-radius:8px;padding:10px 12px;margin:8px 0;display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                  <div>
                    <div style="font-size:11.5px;font-weight:700;color:#64748b;display:flex;align-items:center;gap:4px;">
                      <span>👥</span> Belum Diberikan ke Dosen
                    </div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:1px;">Tugaskan ke dosen untuk dikerjakan.</div>
                  </div>
                  <button type="button" 
                          onclick="openAssignModal(<?= $item['project_id'] ?>, '<?= $item['stage'] ?>', '<?= htmlspecialchars(addslashes($item['stage_name'])) ?>', '<?= htmlspecialchars(addslashes($item['title'])) ?>', null)" 
                          class="btn btn-outline btn-sm" 
                          style="font-size:11.5px;padding:4px 9px;border-color:#cbd5e1;">
                    + Tugaskan ke Dosen
                  </button>
                </div>
              <?php else: ?>
                <!-- Sudah Ditugaskan ke Dosen -->
                <?php
                $tStat = $task['status'];
                $bgTask = '#f8fafc';
                $bdTask = '#e2e8f0';
                if ($tStat === 'submitted') {
                  $bgTask = '#eff6ff';
                  $bdTask = '#93c5fd';
                } elseif ($tStat === 'revision') {
                  $bgTask = '#fff5f5';
                  $bdTask = '#fca5a5';
                } elseif ($tStat === 'approved') {
                  $bgTask = '#f0fdf4';
                  $bdTask = '#86efac';
                }
                ?>
                <div style="background:<?= $bgTask ?>;border:1.5px solid <?= $bdTask ?>;border-radius:8px;padding:12px;margin:10px 0;">
                  <!-- Status Penugasan -->
                  <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-bottom:6px;flex-wrap:wrap;">
                    <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">
                      Delegasi Tugas:
                    </span>

                    <?php if ($tStat === 'submitted'): ?>
                      <span class="badge" style="background:#2563eb;color:#fff;font-weight:700;font-size:10.5px;animation:pulse 2s infinite;">
                        📬 Dikerjakan Dosen — Menunggu ACC Kaprodi
                      </span>
                    <?php elseif ($tStat === 'revision'): ?>
                      <span class="badge" style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;font-weight:700;font-size:10.5px;">
                        ⚠️ Dalam Proses Revisi Dosen
                      </span>
                    <?php elseif ($tStat === 'approved'): ?>
                      <span class="badge" style="background:#dcfce7;color:#166534;border:1px solid #86efac;font-weight:700;font-size:10.5px;">
                        ✅ Telah Di-ACC oleh Kaprodi
                      </span>
                    <?php else: ?>
                      <span class="badge" style="background:#fef3c7;color:#b45309;border:1px solid #fde68a;font-weight:700;font-size:10.5px;">
                        🕒 Ditugaskan — Sedang Dikerjakan
                      </span>
                    <?php endif; ?>
                  </div>

                  <!-- Nama Dosen -->
                  <div style="font-size:12.5px;font-weight:700;color:#1e293b;">
                    👤 <?= htmlspecialchars($task['dosen_nama']) ?>
                  </div>
                  <?php if (!empty($task['deadline'])): ?>
                    <div style="font-size:11px;color:#d97706;margin-top:2px;font-weight:600;">
                      📅 Batas Waktu: <?= date('d M Y', strtotime($task['deadline'])) ?>
                    </div>
                  <?php endif; ?>

                  <!-- Catatan Instruksi Awal -->
                  <?php if (!empty($task['instruksi'])): ?>
                    <div style="font-size:11.5px;color:#475569;margin-top:6px;background:rgba(255,255,255,0.7);padding:6px 8px;border-radius:4px;border:1px solid #e2e8f0;">
                      <strong>Instruksi Anda:</strong> "<?= htmlspecialchars($task['instruksi']) ?>"
                    </div>
                  <?php endif; ?>

                  <!-- Catatan dari Dosen jika sudah diserahkan -->
                  <?php if (!empty($task['catatan_dosen'])): ?>
                    <div style="font-size:11.5px;color:#1e40af;margin-top:6px;background:#ffffff;padding:6px 8px;border-radius:4px;border:1px solid #bfdbfe;">
                      <strong>Catatan Dosen:</strong> "<?= htmlspecialchars($task['catatan_dosen']) ?>"
                      <span style="font-size:10.5px;color:#64748b;display:block;"><?= date('d M Y H:i', strtotime($task['submitted_at'])) ?></span>
                    </div>
                  <?php endif; ?>

                  <!-- Catatan Revisi Aktif -->
                  <?php if (!empty($task['catatan_revisi']) && $tStat === 'revision'): ?>
                    <div style="font-size:11.5px;color:#991b1b;margin-top:6px;background:#ffffff;padding:6px 8px;border-radius:4px;border:1px solid #fecaca;">
                      <strong>Catatan Revisi Terakhir:</strong> "<?= htmlspecialchars($task['catatan_revisi']) ?>"
                    </div>
                  <?php endif; ?>

                  <!-- Tombol Aksi Kaprodi (ACC / Minta Revisi / Ubah) -->
                  <div style="margin-top:10px;padding-top:8px;border-top:1px solid rgba(0,0,0,0.06);display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                    <?php if ($tStat === 'submitted'): ?>
                      <!-- Tombol ACC / Setujui -->
                      <button type="button" 
                              onclick="openAccModal(<?= $task['id'] ?>, '<?= htmlspecialchars(addslashes($item['title'])) ?>', '<?= htmlspecialchars(addslashes($task['dosen_nama'])) ?>')" 
                              class="btn btn-success btn-sm" 
                              style="font-size:11.5px;padding:4px 10px;font-weight:700;">
                        ✅ ACC / Setujui
                      </button>

                      <!-- Tombol Minta Revisi -->
                      <button type="button" 
                              onclick="openRevisionModal(<?= $task['id'] ?>, '<?= htmlspecialchars(addslashes($item['title'])) ?>', '<?= htmlspecialchars(addslashes($task['dosen_nama'])) ?>')" 
                              class="btn btn-warning btn-sm" 
                              style="font-size:11.5px;padding:4px 10px;font-weight:700;">
                        ✍️ Minta Revisi
                      </button>
                    <?php elseif ($tStat === 'approved'): ?>
                      <span style="font-size:11.5px;color:#16a34a;font-weight:700;">
                        ✓ Telah di-ACC pada <?= date('d M Y', strtotime($task['reviewed_at'])) ?>
                      </span>
                    <?php endif; ?>

                    <!-- Reassign / Ubah Tugas -->
                    <button type="button" 
                            onclick="openAssignModal(<?= $item['project_id'] ?>, '<?= $item['stage'] ?>', '<?= htmlspecialchars(addslashes($item['stage_name'])) ?>', '<?= htmlspecialchars(addslashes($item['title'])) ?>', <?= $task['dosen_id'] ?>, '<?= htmlspecialchars(addslashes($task['instruksi'] ?? '')) ?>', '<?= $task['deadline'] ?? '' ?>')" 
                            class="btn btn-outline btn-sm" 
                            style="font-size:11px;padding:3px 8px;" title="Ubah dosen atau instruksi">
                      🔄 Ubah
                    </button>

                    <!-- Batalkan Penugasan -->
                    <button type="button" 
                            onclick="cancelTaskConfirm(<?= $task['id'] ?>)" 
                            class="btn btn-outline btn-sm text-danger" 
                            style="font-size:11px;padding:3px 8px;" title="Batalkan penugasan ini">
                      ❌
                    </button>
                  </div>
                </div>
              <?php endif; ?>
            <?php else: ?>
              <!-- TAMPILAN UNTUK DOSEN -->
              <?php if (!empty($task)): ?>
                <?php if ((int)$task['dosen_id'] === (int)$user['id']): ?>
                  <!-- Tugas Didelegasikan ke Dosen Ini -->
                  <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:8px;padding:9px 12px;margin:8px 0;display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:6px;">
                      <span style="font-size:14px;">🔥</span>
                      <div>
                        <div style="font-size:12px;font-weight:800;color:#92400e;">Tugas Prioritas Anda</div>
                        <div style="font-size:11px;color:#b45309;">Ditugaskan oleh Kaprodi <?= htmlspecialchars($task['kaprodi_nama']) ?></div>
                      </div>
                    </div>
                    <?php
                    $badgeStyle = 'background:#fef3c7;color:#b45309;border:1px solid #fde68a;';
                    $badgeLabel = '📌 Perlu Dikerjakan';
                    if ($task['status'] === 'submitted') {
                      $badgeStyle = 'background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe;';
                      $badgeLabel = '⏳ Menunggu Review';
                    } elseif ($task['status'] === 'approved') {
                      $badgeStyle = 'background:#dcfce7;color:#166534;border:1px solid #86efac;';
                      $badgeLabel = '✅ Sudah Di-ACC';
                    } elseif ($task['status'] === 'revision') {
                      $badgeStyle = 'background:#fee2e2;color:#991b1b;border:1px solid #fecaca;';
                      $badgeLabel = '⚠️ Perlu Revisi';
                    }
                    ?>
                    <span class="badge" style="<?= $badgeStyle ?>font-weight:700;font-size:11px;padding:3px 8px;">
                      <?= $badgeLabel ?>
                    </span>
                  </div>
                <?php else: ?>
                  <!-- Tugas Didelegasikan ke Dosen Lain -->
                  <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:7px 10px;margin:8px 0;font-size:11.5px;color:#64748b;display:flex;align-items:center;gap:6px;">
                    <span>👥</span>
                    <span>Ditugaskan kepada: <strong style="color:#334155;"><?= htmlspecialchars($task['dosen_nama']) ?></strong></span>
                  </div>
                <?php endif; ?>
              <?php else: ?>
                <!-- Draft Reguler Belum Ditugaskan -->
                <div style="background:#f8fafc;border:1px dashed #cbd5e1;border-radius:6px;padding:6px 10px;margin:8px 0;font-size:11.5px;color:#64748b;display:flex;align-items:center;gap:6px;">
                  <span>📝</span>
                  <span>Draft Reguler Prodi</span>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          <!-- Action Button Utama Dokumen -->
          <div style="border-top:1px solid var(--border-subtle);padding-top:10px;display:flex;justify-content:flex-end;gap:8px;">
            <a href="<?= $item['action_url'] ?>" class="btn btn-primary btn-sm">
              <?= htmlspecialchars($item['action_label']) ?>
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13">
                <polyline points="9 18 15 12 9 6"/>
              </svg>
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div id="pendingNoResult" class="card" style="display:none;padding:36px 20px;text-align:center;color:var(--text-muted);font-size:13.5px;">
  Tidak ditemukan dokumen yang cocok dengan filter yang dipilih.
</div>
<?php endif; ?>

<!-- ============================================================ -->
<!-- MODAL 1: TUGASKAN KE DOSEN (KAPRODI)                        -->
<!-- ============================================================ -->
<div id="modalAssignTask" class="modal-backdrop" style="display:none;">
  <div class="modal-dialog" style="max-width:540px;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" style="display:flex;align-items:center;gap:8px;font-size:16px;">
          <span>👥</span> Tugaskan Dokumen kepada Dosen
        </h3>
        <button type="button" onclick="closeModal('modalAssignTask')" class="close-btn">&times;</button>
      </div>
      <form action="<?= BASE_URL ?>/pending/assign" method="POST">
        <input type="hidden" name="project_id" id="assignProjectId" value="">
        <input type="hidden" name="stage" id="assignStage" value="">
        <input type="hidden" name="document_id" id="assignDocumentId" value="">

        <div class="modal-body" style="display:flex;flex-direction:column;gap:14px;">
          <!-- Info Item Dokumen -->
          <div style="background:#f1f5f9;border-radius:6px;padding:12px 14px;">
            <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;" id="assignStageName">Tahapan</div>
            <div style="font-size:14px;font-weight:700;color:#0f172a;margin-top:2px;" id="assignItemTitle">Judul Dokumen</div>
          </div>

          <!-- Dropdown Dosen -->
          <div>
            <label class="form-label" style="font-weight:600;font-size:13px;">Pilih Dosen yang Ditugaskan <span style="color:red;">*</span></label>
            <select name="dosen_id" id="assignDosenSelect" class="form-control" required style="font-size:13.5px;">
              <option value="">-- Pilih Dosen Pengampu / Penanggung Jawab --</option>
              <?php foreach ($dosenAvailable as $d): ?>
              <option value="<?= $d['id'] ?>">
                <?= htmlspecialchars($d['nama_lengkap']) ?> (NIP: <?= htmlspecialchars($d['username']) ?>)
              </option>
              <?php endforeach; ?>
            </select>
            <div style="font-size:11.5px;color:var(--text-muted);margin-top:4px;">
              Dosen terpilih akan menerima penanda <strong>Tugas Prioritas</strong> di halaman Tugas miliknya.
            </div>
          </div>

          <!-- Judul / Label Tugas -->
          <div>
            <label class="form-label" style="font-weight:600;font-size:13px;">Judul Tugas</label>
            <input type="text" name="judul_tugas" id="assignJudulTugas" class="form-control" placeholder="Contoh: Penyusunan Pelaksanaan Standar Pembelajaran TA 2025/2026">
          </div>

          <!-- Instruksi / Catatan Kaprodi -->
          <div>
            <label class="form-label" style="font-weight:600;font-size:13px;">Instruksi Pengerjaan untuk Dosen</label>
            <textarea name="instruksi" id="assignInstruksi" rows="3" class="form-control" placeholder="Tuliskan arahan, kriteria yang perlu dilengkapi, tautan folder Google Drive acuan, dll..."></textarea>
          </div>

          <!-- Deadline -->
          <div>
            <label class="form-label" style="font-weight:600;font-size:13px;">Batas Waktu / Tenggat Pengerjaan (Opsional)</label>
            <input type="date" name="deadline" id="assignDeadline" class="form-control" min="<?= date('Y-m-d') ?>">
          </div>
        </div>

        <div class="modal-footer" style="display:flex;justify-content:flex-end;gap:8px;">
          <button type="button" onclick="closeModal('modalAssignTask')" class="btn btn-outline btn-sm">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
            <span>🚀</span> Kirim Penugasan ke Dosen
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ============================================================ -->
<!-- MODAL 2: ACC / SETUJUI TUGAS (KAPRODI)                      -->
<!-- ============================================================ -->
<div id="modalAccTask" class="modal-backdrop" style="display:none;">
  <div class="modal-dialog" style="max-width:480px;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" style="display:flex;align-items:center;gap:8px;font-size:16px;color:#166534;">
          <span>✅</span> Konfirmasi ACC / Setujui Tugas
        </h3>
        <button type="button" onclick="closeModal('modalAccTask')" class="close-btn">&times;</button>
      </div>
      <form action="<?= BASE_URL ?>/pending/acc-task" method="POST">
        <input type="hidden" name="task_id" id="accTaskId" value="">

        <div class="modal-body" style="display:flex;flex-direction:column;gap:12px;">
          <p style="font-size:13.5px;color:var(--text-main);margin:0;line-height:1.5;">
            Apakah Anda yakin dokumen pengerjaan dari dosen berikut sudah benar dan siap untuk di-<strong>ACC (Disetujui)</strong>?
          </p>

          <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;padding:12px;">
            <div style="font-size:12px;color:#166534;font-weight:600;">Dosen Pelaksana:</div>
            <div style="font-size:14px;font-weight:700;color:#14532d;" id="accDosenNama">—</div>
            <div style="font-size:12px;color:#166534;margin-top:4px;" id="accTaskTitle">—</div>
          </div>

          <div style="font-size:12px;color:var(--text-muted);">
            Setelah di-ACC, status tugas di akun dosen akan berubah menjadi <strong>Disetujui</strong> dan penanda prioritas akan selesai.
          </div>
        </div>

        <div class="modal-footer" style="display:flex;justify-content:flex-end;gap:8px;">
          <button type="button" onclick="closeModal('modalAccTask')" class="btn btn-outline btn-sm">Batal</button>
          <button type="submit" class="btn btn-success btn-sm" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;">
            ✓ Ya, ACC &amp; Setujui Dokumen
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ============================================================ -->
<!-- MODAL 3: MINTA REVISI DENGAN CATATAN (KAPRODI)              -->
<!-- ============================================================ -->
<div id="modalRevisionTask" class="modal-backdrop" style="display:none;">
  <div class="modal-dialog" style="max-width:520px;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" style="display:flex;align-items:center;gap:8px;font-size:16px;color:#991b1b;">
          <span>✍️</span> Catatan Revisi untuk Dosen
        </h3>
        <button type="button" onclick="closeModal('modalRevisionTask')" class="close-btn">&times;</button>
      </div>
      <form action="<?= BASE_URL ?>/pending/revision-task" method="POST">
        <input type="hidden" name="task_id" id="revTaskId" value="">

        <div class="modal-body" style="display:flex;flex-direction:column;gap:12px;">
          <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:10px 14px;">
            <div style="font-size:12px;color:#991b1b;font-weight:600;">Dokumen &amp; Dosen:</div>
            <div style="font-size:13.5px;font-weight:700;color:#7f1d1d;" id="revTaskTitle">—</div>
            <div style="font-size:12px;color:#991b1b;" id="revDosenNama">—</div>
          </div>

          <div>
            <label class="form-label" style="font-weight:700;font-size:13px;color:#991b1b;">
              Tulis Catatan / Note Revisi yang Harus Diperbaiki <span style="color:red;">*</span>
            </label>
            <textarea name="catatan_revisi" id="revCatatanInput" rows="4" class="form-control" required placeholder="Contoh: Bukti RPS dan SK Mengajar pada Kriteria 4 masih belum tercantum linknya. Mohon lengkapi dan cek kembali sebelum akhir pekan..."></textarea>
            <div style="font-size:11.5px;color:var(--text-muted);margin-top:4px;">
              Catatan ini akan langsung tampil dengan penanda merah <strong>⚠️ PERLU REVISI</strong> di akun dosen.
            </div>
          </div>
        </div>

        <div class="modal-footer" style="display:flex;justify-content:flex-end;gap:8px;">
          <button type="button" onclick="closeModal('modalRevisionTask')" class="btn btn-outline btn-sm">Batal</button>
          <button type="submit" class="btn btn-warning btn-sm" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;">
            Kirim Catatan Revisi
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ============================================================ -->
<!-- MODAL 4: AJUKAN TUGAS KE KAPRODI (DOSEN)                    -->
<!-- ============================================================ -->
<div id="modalSubmitTask" class="modal-backdrop" style="display:none;">
  <div class="modal-dialog" style="max-width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" style="display:flex;align-items:center;gap:8px;font-size:16px;">
          <span>🚀</span> Ajukan Dokumen Selesai ke Kaprodi
        </h3>
        <button type="button" onclick="closeModal('modalSubmitTask')" class="close-btn">&times;</button>
      </div>
      <form action="<?= BASE_URL ?>/pending/submit-task" method="POST">
        <input type="hidden" name="task_id" id="submitTaskId" value="">

        <div class="modal-body" style="display:flex;flex-direction:column;gap:12px;">
          <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;padding:12px;">
            <div style="font-size:11.5px;color:#1e40af;font-weight:600;">Tugas yang Diajukan:</div>
            <div style="font-size:14px;font-weight:700;color:#1e3a8a;" id="submitTaskTitle">—</div>
            <div style="font-size:12px;color:#3b82f6;margin-top:2px;">Kepada Kaprodi: <strong id="submitKaprodiNama">—</strong></div>
          </div>

          <div>
            <label class="form-label" style="font-weight:600;font-size:13px;">Catatan Tambahan untuk Kaprodi (Opsional)</label>
            <textarea name="catatan_dosen" rows="3" class="form-control" placeholder="Contoh: Dokumen bukti pelaksanaan dan link Google Drive telah dilengkapi sesuai instruksi..."></textarea>
          </div>

          <div style="font-size:12px;color:var(--text-muted);">
            Setelah dikirim, Kaprodi akan menerima notifikasi status tugas untuk diperiksa kelengkapannya dan diberikan ACC.
          </div>
        </div>

        <div class="modal-footer" style="display:flex;justify-content:flex-end;gap:8px;">
          <button type="button" onclick="closeModal('modalSubmitTask')" class="btn btn-outline btn-sm">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;">
            🚀 Kirim ke Kaprodi
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Hidden Form for Cancel Task (Kaprodi) -->
<form id="formCancelTask" action="<?= BASE_URL ?>/pending/cancel-task" method="POST" style="display:none;">
  <input type="hidden" name="task_id" id="cancelTaskId" value="">
</form>

<!-- Modal Styles -->
<style>
.modal-backdrop {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}
.modal-dialog {
  width: 100%;
  animation: modalFadeIn 0.2s ease-out;
}
.modal-content {
  background: #ffffff;
  border-radius: var(--radius, 12px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  overflow: hidden;
  border: 1px solid var(--border);
}
.modal-header {
  padding: 16px 20px;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.modal-body {
  padding: 18px 20px;
}
.modal-footer {
  padding: 14px 20px;
  border-top: 1px solid var(--border);
  background: #f8fafc;
}
.close-btn {
  background: transparent;
  border: none;
  font-size: 22px;
  line-height: 1;
  color: var(--text-muted);
  cursor: pointer;
}
.close-btn:hover {
  color: var(--text-main);
}
@keyframes modalFadeIn {
  from { opacity: 0; transform: translateY(-8px) scale(0.98); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}
</style>

<script>
var currentPendingStage = 'all';

function filterPendingStage(stage, btn) {
  currentPendingStage = stage;
  document.querySelectorAll('#stageFilterButtons .btn-filter').forEach(function(b) {
    b.classList.remove('active');
  });
  if (btn) btn.classList.add('active');
  applyPendingFilters();
}

function applyPendingFilters() {
  var projEl = document.getElementById('pendingProjectSelect');
  var statusEl = document.getElementById('pendingStatusSelect');
  var taskEl = document.getElementById('pendingTaskFilterSelect');
  var searchEl = document.getElementById('pendingSearchInput');

  var projVal = projEl ? projEl.value : 'all';
  var statusVal = statusEl ? statusEl.value : 'all';
  var taskVal = taskEl ? taskEl.value : 'all';
  var searchVal = searchEl ? searchEl.value.toLowerCase() : '';

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
      var cTask   = c.getAttribute('data-task-status') || 'unassigned';
      var cTitle  = c.getAttribute('data-title') || '';

      var matchStage  = (currentPendingStage === 'all' || cStage === currentPendingStage);
      var matchStatus = (statusVal === 'all' || cStatus === statusVal);
      var matchTask   = (taskVal === 'all' || cTask === taskVal);
      var matchSearch = (searchVal === '' || cTitle.includes(searchVal));

      if (matchStage && matchStatus && matchTask && matchSearch) {
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

// Modal Handlers
function closeModal(modalId) {
  var el = document.getElementById(modalId);
  if (el) el.style.display = 'none';
}

function openAssignModal(projectId, stage, stageName, title, dosenId, instruksi, deadline) {
  document.getElementById('assignProjectId').value = projectId;
  document.getElementById('assignStage').value = stage;
  document.getElementById('assignStageName').textContent = stageName || stage;
  document.getElementById('assignItemTitle').textContent = title || '';
  document.getElementById('assignJudulTugas').value = title ? ('Penyusunan ' + title) : '';
  
  if (dosenId) {
    document.getElementById('assignDosenSelect').value = dosenId;
  } else {
    document.getElementById('assignDosenSelect').value = '';
  }
  
  document.getElementById('assignInstruksi').value = instruksi || '';
  document.getElementById('assignDeadline').value = deadline || '';

  document.getElementById('modalAssignTask').style.display = 'flex';
}

function openAccModal(taskId, title, dosenNama) {
  document.getElementById('accTaskId').value = taskId;
  document.getElementById('accTaskTitle').textContent = title;
  document.getElementById('accDosenNama').textContent = dosenNama;
  document.getElementById('modalAccTask').style.display = 'flex';
}

function openRevisionModal(taskId, title, dosenNama) {
  document.getElementById('revTaskId').value = taskId;
  document.getElementById('revTaskTitle').textContent = title;
  document.getElementById('revDosenNama').textContent = 'Dosen: ' + dosenNama;
  document.getElementById('revCatatanInput').value = '';
  document.getElementById('modalRevisionTask').style.display = 'flex';
}

function openSubmitTaskModal(taskId, title, kaprodiNama) {
  document.getElementById('submitTaskId').value = taskId;
  document.getElementById('submitTaskTitle').textContent = title;
  document.getElementById('submitKaprodiNama').textContent = kaprodiNama;
  document.getElementById('modalSubmitTask').style.display = 'flex';
}

function cancelTaskConfirm(taskId) {
  if (confirm('Apakah Anda yakin ingin membatalkan penugasan ini ke dosen?')) {
    document.getElementById('cancelTaskId').value = taskId;
    document.getElementById('formCancelTask').submit();
  }
}

// Close modals when clicking outside modal dialog
window.addEventListener('click', function(e) {
  if (e.target.classList.contains('modal-backdrop')) {
    e.target.style.display = 'none';
  }
});
</script>
