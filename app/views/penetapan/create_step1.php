<?php
$pageTitle   = 'Buat Penetapan — Langkah 1';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Penetapan', 'url' => BASE_URL . '/penetapan'],
  ['label' => 'Buat Baru — Pilih Kriteria'],
];
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
  <div>
    <h2>Buat Penetapan Standar Baru</h2>
    <p class="page-desc">Langkah 1: Isi informasi dasar dan pilih kriteria standar</p>
  </div>
  <a href="<?= BASE_URL ?>/ppepp<?= !empty($projectId) ? '/' . $projectId : '' ?>" class="btn btn-outline btn-sm">
    ← Kembali ke Project Library
  </a>
</div>

<!-- Progress Wizard -->
<div class="ppepp-progress mb-4">
  <div class="pp-step active">
    <div class="pp-step-inner">
      <div class="pp-step-circle">1</div>
      <span class="pp-step-label">Pilih Kriteria</span>
    </div>
  </div>
  <div class="pp-connector"></div>
  <div class="pp-step">
    <div class="pp-step-inner">
      <div class="pp-step-circle">2</div>
      <span class="pp-step-label">Upload &amp; Analisis</span>
    </div>
  </div>
  <div class="pp-connector"></div>
  <div class="pp-step">
    <div class="pp-step-inner">
      <div class="pp-step-circle">3</div>
      <span class="pp-step-label">Simpan</span>
    </div>
  </div>
</div>

<!-- Form Step 1 -->
<form method="POST" action="<?= BASE_URL ?>/penetapan/step2" id="step1Form" style="max-width:780px;margin:0 auto;">

  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Informasi Penetapan Standar
      </div>
    </div>
    <div class="card-body">

      <?php if (!empty($selectedProject)): ?>
      <!-- Project and Tahun Ajaran Automatically Binding Banner -->
      <div style="background:var(--primary-subtle);border:1px solid #D3E0F0;border-radius:var(--radius-sm);padding:14px 18px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:12px;">
          <div style="width:36px;height:36px;border-radius:var(--radius-sm);background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
              <path d="M3 7l9 6 9-6"/>
            </svg>
          </div>
          <div>
            <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.04em;">Folder Project PPEPP</div>
            <div style="font-size:13.5px;font-weight:700;color:var(--text-main);"><?= htmlspecialchars($selectedProject['judul']) ?></div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;background:#fff;padding:4px 12px;border-radius:20px;border:1px solid var(--border);">
          <span style="font-size:12px;color:var(--primary);font-weight:700;">TA: <?= htmlspecialchars($selectedProject['ta_nama']) ?></span>
          <span class="badge badge-primary" style="font-size:10px;">Otomatis</span>
        </div>
      </div>
      <input type="hidden" name="ppepp_project_id" value="<?= $selectedProject['id'] ?>">
      <input type="hidden" name="tahun_ajaran_id" value="<?= $selectedProject['tahun_ajaran_id'] ?>">
      <?php elseif (!empty($userProjects)): ?>
      <!-- Select Folder Project (Tahun Ajaran automatically derived) -->
      <div class="form-group">
        <label for="ppepp_project_id">
          Pilih Folder Project PPEPP <span style="color:var(--danger);">*</span>
        </label>
        <select id="ppepp_project_id" name="ppepp_project_id" class="form-control" required onchange="onProjChange(this)">
          <option value="">— Pilih Folder Project —</option>
          <?php foreach ($userProjects as $p): ?>
          <option value="<?= $p['id'] ?>" data-ta-id="<?= $p['tahun_ajaran_id'] ?>" data-ta-nama="<?= htmlspecialchars($p['ta_nama']) ?>">
            <?= htmlspecialchars($p['judul']) ?> (Tahun Ajaran: <?= htmlspecialchars($p['ta_nama']) ?>)
          </option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" id="tahun_ajaran_id" name="tahun_ajaran_id" value="">
        <div id="taAutoBadge" style="display:none;margin-top:6px;font-size:12px;color:var(--primary);font-weight:600;">
          Tahun Ajaran Otomatis: <span id="taAutoText" style="color:var(--text-main);"></span>
        </div>
      </div>
      <script>
      function onProjChange(sel) {
        var opt = sel.options[sel.selectedIndex];
        var taId = opt ? opt.getAttribute('data-ta-id') : '';
        var taNama = opt ? opt.getAttribute('data-ta-nama') : '';
        document.getElementById('tahun_ajaran_id').value = taId || '';
        var badge = document.getElementById('taAutoBadge');
        if (taNama) {
          document.getElementById('taAutoText').innerText = taNama;
          badge.style.display = 'block';
        } else {
          badge.style.display = 'none';
        }
      }
      </script>
      <?php else: ?>
      <!-- No Project Created Yet -->
      <div style="background:var(--warning-bg);border:1px solid var(--warning-border);border-radius:var(--radius);padding:18px;text-align:center;margin-bottom:18px;">
        <div style="font-size:14px;font-weight:700;color:var(--warning);margin-bottom:4px;">Belum Ada Folder Project PPEPP</div>
        <div style="font-size:12.5px;color:var(--text-muted);margin-bottom:12px;">Buat Folder Project terlebih dahulu agar Tahun Ajaran terikat otomatis.</div>
        <a href="<?= BASE_URL ?>/ppepp" class="btn btn-sm btn-primary">
          + Buat Folder Project Sekarang
        </a>
      </div>
      <?php endif; ?>

      <div class="form-group">
        <label for="judul">Judul Penetapan <span style="color:var(--danger);">*</span></label>
        <input type="text" id="judul" name="judul" class="form-control"
               placeholder="Contoh: Penetapan Standar SPMI Prodi TI 2025/2026"
               required>
      </div>

      <div class="form-group">
        <label for="deskripsi">Deskripsi <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                  placeholder="Catatan atau konteks penetapan ini..."></textarea>
      </div>

      <!-- Info Box -->
      <div style="background:#F8FAFC;border:1px solid var(--border);border-radius:var(--radius-sm);padding:14px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
          <div style="font-size:12.5px;font-weight:600;color:var(--text-main);">Langkah Selanjutnya (Langkah 2):</div>
          <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">Anda dapat memasukkan standar secara langsung dengan pulldown kriteria, atau mengimpor file Excel secara otomatis.</div>
        </div>
        <a href="<?= BASE_URL ?>/penetapan/download-template-excel?format=xlsx" class="btn btn-sm btn-outline">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Download Template Excel (.xlsx)
        </a>
      </div>
    </div>
  </div>

  <!-- Pilih Kriteria SPMI -->
  <div class="card mb-4" style="margin-top:20px;">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
      <div class="card-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
          <path d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
        </svg>
        Pilih Kriteria Standar untuk Penetapan ini
        <span class="badge badge-primary" id="selectedCount" style="margin-left:8px;">
          0 dipilih
        </span>
      </div>
      <div style="display:flex;align-items:center;gap:6px;">
        <button type="button" id="selectAll" class="btn btn-sm btn-outline">
          Pilih Semua
        </button>
        <button type="button" id="clearAll" class="btn btn-sm btn-outline">
          Hapus Pilihan
        </button>
        <button type="button" onclick="openModal('modalTambahKriteriaStep1')" class="btn btn-sm btn-primary">
          + Tambah Kriteria
        </button>
      </div>
    </div>
    <div class="card-body">
      <p style="font-size:12.5px;color:var(--text-muted);margin-bottom:14px;">
        Pilihlah kriteria-kriteria di bawah ini yang akan ditetapkan. Kriteria yang dipilih pada Langkah 1 ini akan langsung dipakai di Langkah 2.
      </p>

      <div id="kriteriaCheckboxes" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:10px;">
        <?php foreach ($kriteria as $k): ?>
        <label class="checkbox-item" id="ci_<?= $k['id'] ?>"
               style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid var(--border);background:#FFFFFF;border-radius:var(--radius-sm);cursor:pointer;transition:all var(--transition);">
          <input type="checkbox" name="kriteria_ids[]" value="<?= $k['id'] ?>" onchange="updateCount()" class="k-check" style="flex-shrink:0;width:16px;height:16px;accent-color:var(--primary);">
          <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:8px;">
              <span class="badge badge-primary" style="font-weight:700;">
                <?= htmlspecialchars($k['kode'] ?? 'KR') ?>
              </span>
              <span style="font-size:13px;font-weight:600;color:var(--text-main);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                <?= htmlspecialchars($k['nama']) ?>
              </span>
            </div>
            <?php if (!empty($k['deskripsi'])): ?>
            <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
              <?= htmlspecialchars(mb_substr($k['deskripsi'], 0, 60)) ?>
            </div>
            <?php endif; ?>
          </div>
        </label>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Bottom Actions -->
  <div style="display:flex;align-items:center;justify-content:space-between;margin-top:20px;padding:14px 20px;background:#fff;border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow-xs);gap:12px;">
    <a href="<?= BASE_URL ?>/ppepp<?= !empty($projectId) ? '/' . $projectId : '' ?>" class="btn btn-outline">
      ← Batal
    </a>
    <button type="submit" class="btn btn-primary" style="font-weight:600;">
      Lanjut ke Langkah 2: Penyusunan Standar →
    </button>
  </div>

</form>

<script>
function updateCount() {
  var checks = document.querySelectorAll('.k-check:checked');
  var n = checks.length;
  document.getElementById('selectedCount').textContent = n === 0 ? '0 dipilih' : (n + ' dipilih');
  // Highlight selected
  document.querySelectorAll('.checkbox-item').forEach(function(el) {
    var cb = el.querySelector('input[type="checkbox"]');
    if (cb && cb.checked) {
      el.style.borderColor = 'var(--primary)';
      el.style.background = 'var(--primary-subtle)';
    } else {
      el.style.borderColor = 'var(--border)';
      el.style.background = '#FFFFFF';
    }
  });
}
document.getElementById('selectAll').addEventListener('click', function() {
  document.querySelectorAll('.k-check').forEach(function(c) { c.checked = true; });
  updateCount();
});
document.getElementById('clearAll').addEventListener('click', function() {
  document.querySelectorAll('.k-check').forEach(function(c) { c.checked = false; });
  updateCount();
});
updateCount();

// ======================================================
// Modal AJAX Simpan Kriteria Baru
// ======================================================
document.getElementById('formTambahKriteriaStep1').addEventListener('submit', function(e) {
  e.preventDefault();
  var kodeInput = document.getElementById('step1AddKode');
  var namaInput = document.getElementById('step1AddNama');
  var deskInput = document.getElementById('step1AddDeskripsi');
  var errEl     = document.getElementById('step1AddError');
  var btn       = document.getElementById('step1AddBtn');

  errEl.style.display = 'none';

  if (!kodeInput.value.trim() || !namaInput.value.trim()) {
    errEl.textContent = 'Kode dan nama kriteria wajib diisi.';
    errEl.style.display = 'block';
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Menyimpan...';

  var body = new URLSearchParams();
  body.append('kode', kodeInput.value.trim());
  body.append('nama', namaInput.value.trim());
  body.append('deskripsi', deskInput.value.trim());

  fetch(BASE_URL + '/penetapan/kriteria/save-ajax', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    btn.disabled = false;
    btn.textContent = 'Simpan & Gunakan';

    if (data.success) {
      var k = data.kriteria;
      // Buat element checkbox baru
      var label = document.createElement('label');
      label.className = 'checkbox-item';
      label.id = 'ci_' + k.id;
      label.style.cssText = 'display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid var(--primary);background:var(--primary-subtle);border-radius:var(--radius-sm);cursor:pointer;';
      label.innerHTML =
        '<input type="checkbox" name="kriteria_ids[]" value="' + k.id + '" checked onchange="updateCount()" class="k-check" style="flex-shrink:0;width:16px;height:16px;accent-color:var(--primary);">' +
        '<div style="flex:1;min-width:0;">' +
          '<div style="display:flex;align-items:center;gap:8px;">' +
            '<span class="badge badge-primary" style="font-weight:700;">' + esc(k.kode || 'KR') + '</span>' +
            '<span style="font-size:13px;font-weight:600;color:var(--text-main);">' + esc(k.nama) + '</span>' +
          '</div>' +
          (k.deskripsi ? '<div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;">' + esc(k.deskripsi.substring(0, 55)) + '...</div>' : '') +
        '</div>';

      var container = document.getElementById('kriteriaCheckboxes');
      container.appendChild(label);

      // Reset form modal & close
      kodeInput.value = '';
      namaInput.value = '';
      deskInput.value = '';
      closeModal('modalTambahKriteriaStep1');

      updateCount();
    } else {
      errEl.textContent = data.message || 'Gagal menyimpan.';
      errEl.style.display = 'block';
    }
  })
  .catch(function(err) {
    btn.disabled = false;
    btn.textContent = 'Simpan & Gunakan';
    errEl.textContent = 'Error: ' + err.message;
    errEl.style.display = 'block';
  });
});

function esc(str) {
  var d = document.createElement('div');
  d.appendChild(document.createTextNode(str || ''));
  return d.innerHTML;
}
</script>

<!-- MODAL TAMBAH KRITERIA (STEP 1) -->
<div class="modal-overlay" id="modalTambahKriteriaStep1">
  <div class="modal">
    <div class="modal-header">
      <h3>
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Tambah Kriteria Baru
      </h3>
      <button class="modal-close" data-modal-close="modalTambahKriteriaStep1">✕</button>
    </div>
    <form id="formTambahKriteriaStep1">
      <div class="modal-body">
        <div id="step1AddError" style="display:none;background:var(--danger-bg);color:var(--danger);border:1px solid var(--danger-border);border-radius:var(--radius-sm);padding:10px 14px;font-size:12.5px;margin-bottom:14px;"></div>
        <div class="form-group mb-3">
          <label for="step1AddKode">Kode Kriteria <span style="color:var(--danger);">*</span></label>
          <input type="text" id="step1AddKode" name="kode" class="form-control"
                 placeholder="Contoh: C1, K1, STD-01" value="" maxlength="20" required>
        </div>
        <div class="form-group mb-3">
          <label for="step1AddNama">Nama Kriteria <span style="color:var(--danger);">*</span></label>
          <input type="text" id="step1AddNama" name="nama" class="form-control"
                 placeholder="Contoh: Tata Pamong & Tata Kelola" required>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label for="step1AddDeskripsi">Deskripsi <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
          <textarea id="step1AddDeskripsi" name="deskripsi" class="form-control"
                    placeholder="Deskripsi singkat tentang kriteria ini..." rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close="modalTambahKriteriaStep1">Batal</button>
        <button type="submit" class="btn btn-primary" id="step1AddBtn">
          Simpan &amp; Gunakan
        </button>
      </div>
    </form>
  </div>
</div>
