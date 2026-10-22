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
    <h2 style="font-size:22px;font-weight:900;color:#1e293b;margin:0;">Buat Penetapan Standar Baru</h2>
    <p class="page-desc" style="font-size:13px;color:#64748b;margin:4px 0 0;">Langkah 1: Isi informasi dasar dan pilih kriteria standar</p>
  </div>
  <a href="<?= BASE_URL ?>/ppepp<?= !empty($projectId) ? '/' . $projectId : '' ?>" class="btn btn-outline" style="font-size:13px;font-weight:700;">
    ← Kembali ke Project Library
  </a>
</div>

<!-- Progress Wizard -->
<div style="display:flex;align-items:center;gap:0;margin-bottom:28px;">
  <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
    <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#1a237e,#3f51b5);color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;box-shadow:0 4px 14px rgba(26,35,126,0.3);">1</div>
    <div style="font-size:11.5px;font-weight:700;color:#3f51b5;">Pilih Kriteria</div>
  </div>
  <div style="flex:1;height:2px;background:#e2e8f0;max-width:80px;min-width:40px;"></div>
  <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
    <div style="width:38px;height:38px;border-radius:50%;background:#e2e8f0;color:#94a3b8;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;">2</div>
    <div style="font-size:11.5px;font-weight:600;color:#94a3b8;">Upload & Analisis</div>
  </div>
  <div style="flex:1;height:2px;background:#e2e8f0;max-width:80px;min-width:40px;"></div>
  <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
    <div style="width:38px;height:38px;border-radius:50%;background:#e2e8f0;color:#94a3b8;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;">3</div>
    <div style="font-size:11.5px;font-weight:600;color:#94a3b8;">Simpan</div>
  </div>
</div>

<!-- Form Step 1 -->
<form method="POST" action="<?= BASE_URL ?>/penetapan/step2" id="step1Form" style="max-width:760px;margin:0 auto;">

  <div class="card" style="border-radius:14px;box-shadow:0 4px 20px rgba(0,0,0,0.04);border:1px solid #e2e8f0;">
    <div class="card-header" style="background:#f8fafc;padding:18px 24px;border-bottom:1px solid #e2e8f0;">
      <div class="card-title" style="font-size:16px;font-weight:800;color:#1e293b;display:flex;align-items:center;gap:8px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" style="color:#3f51b5;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
        Informasi Penetapan Standar
      </div>
    </div>
    <div class="card-body" style="padding:24px;">

      <?php if (!empty($selectedProject)): ?>
      <!-- Project and Tahun Ajaran Automatically Binding Banner -->
      <div style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);border:1.5px solid #bae6fd;border-radius:12px;padding:14px 18px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:12px;">
          <div style="width:38px;height:38px;border-radius:10px;background:#0284c7;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;">📁</div>
          <div>
            <div style="font-size:11px;font-weight:700;color:#0369a1;text-transform:uppercase;letter-spacing:0.5px;">Folder Project PPEPP</div>
            <div style="font-size:14px;font-weight:800;color:#0c4a6e;"><?= htmlspecialchars($selectedProject['judul']) ?></div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;background:#fff;padding:6px 14px;border-radius:20px;border:1px solid #bae6fd;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
          <span style="font-size:12.5px;color:#0284c7;font-weight:800;">📅 TA: <?= htmlspecialchars($selectedProject['ta_nama']) ?></span>
          <span style="font-size:10.5px;background:#e0f2fe;color:#0369a1;padding:2px 8px;border-radius:10px;font-weight:700;">Otomatis</span>
        </div>
      </div>
      <input type="hidden" name="ppepp_project_id" value="<?= $selectedProject['id'] ?>">
      <input type="hidden" name="tahun_ajaran_id" value="<?= $selectedProject['tahun_ajaran_id'] ?>">
      <?php elseif (!empty($userProjects)): ?>
      <!-- Select Folder Project (Tahun Ajaran automatically derived) -->
      <div class="form-group" style="margin-bottom:18px;">
        <label for="ppepp_project_id" style="font-size:13px;font-weight:800;color:#1e293b;margin-bottom:6px;display:block;">
          Pilih Folder Project PPEPP <span style="color:#ef4444;">*</span>
        </label>
        <select id="ppepp_project_id" name="ppepp_project_id" class="form-control" style="font-size:14px;padding:10px 14px;border-radius:8px;" required onchange="onProjChange(this)">
          <option value="">— Pilih Folder Project —</option>
          <?php foreach ($userProjects as $p): ?>
          <option value="<?= $p['id'] ?>" data-ta-id="<?= $p['tahun_ajaran_id'] ?>" data-ta-nama="<?= htmlspecialchars($p['ta_nama']) ?>">
            📁 <?= htmlspecialchars($p['judul']) ?> (Tahun Ajaran: <?= htmlspecialchars($p['ta_nama']) ?>)
          </option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" id="tahun_ajaran_id" name="tahun_ajaran_id" value="">
        <div id="taAutoBadge" style="display:none;margin-top:8px;font-size:12.5px;color:#0284c7;font-weight:700;">
          📅 Tahun Ajaran Otomatis: <span id="taAutoText" style="color:#0c4a6e;"></span>
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
      <div style="background:#fff7ed;border:1.5px solid #fed7aa;border-radius:12px;padding:18px;text-align:center;margin-bottom:18px;">
        <div style="font-size:32px;margin-bottom:6px;">📁</div>
        <div style="font-size:14px;font-weight:800;color:#c2410c;margin-bottom:4px;">Belum Ada Folder Project PPEPP</div>
        <div style="font-size:12.5px;color:#92400e;margin-bottom:14px;">Buat Folder Project terlebih dahulu agar Tahun Ajaran terikat otomatis.</div>
        <a href="<?= BASE_URL ?>/ppepp" class="btn btn-sm btn-primary" style="background:#ea580c;border:none;font-weight:800;padding:8px 18px;border-radius:8px;">
          + Buat Folder Project Sekarang
        </a>
      </div>
      <?php endif; ?>

      <div class="form-group" style="margin-bottom:18px;">
        <label for="judul" style="font-size:13px;font-weight:800;color:#1e293b;margin-bottom:6px;display:block;">Judul Penetapan <span style="color:#ef4444;">*</span></label>
        <input type="text" id="judul" name="judul" class="form-control"
               placeholder="Contoh: Penetapan Standar SPMI Prodi TI 2025/2026"
               style="font-size:14px;padding:10px 14px;border-radius:8px;"
               required>
      </div>

      <div class="form-group" style="margin-bottom:20px;">
        <label for="deskripsi" style="font-size:13px;font-weight:800;color:#1e293b;margin-bottom:6px;display:block;">Deskripsi <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                  placeholder="Catatan atau konteks penetapan ini..." style="font-size:13.5px;padding:10px 14px;border-radius:8px;"></textarea>
      </div>

      <!-- Info Box -->
      <div style="background:#f0fdf4;border:1.5px solid #a7f3d0;border-radius:10px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
          <div style="font-size:12.5px;font-weight:800;color:#065f46;">📊 Langkah Selanjutnya (Langkah 2):</div>
          <div style="font-size:12px;color:#047857;margin-top:2px;">Anda dapat memasukkan standar secara langsung dengan pulldown kriteria, atau mengimpor file Excel secara otomatis.</div>
        </div>
        <a href="<?= BASE_URL ?>/penetapan/download-template-excel?format=xlsx" class="btn btn-sm btn-outline"
           style="color:#059669;border-color:#059669;background:#fff;font-weight:700;font-size:11.5px;text-decoration:none;display:flex;align-items:center;gap:5px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Download Template Excel (.xlsx)
        </a>
      </div>
    </div>
  <!-- Pilih Kriteria SPMI -->
  <div class="card mb-4" style="border-radius:14px;box-shadow:0 4px 20px rgba(0,0,0,0.04);border:1px solid #e2e8f0;margin-top:20px;">
    <div class="card-header" style="background:#f8fafc;padding:18px 24px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
      <div class="card-title" style="font-size:16px;font-weight:800;color:#1e293b;display:flex;align-items:center;gap:8px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" style="color:#3f51b5;">
          <path d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
        </svg>
        Pilih Kriteria Standar untuk Penetapan ini
        <span class="badge badge-primary" id="selectedCount" style="margin-left:8px;font-size:12px;background:#3f51b5;color:#fff;padding:4px 10px;border-radius:20px;">
          0 dipilih
        </span>
      </div>
      <div style="display:flex;align-items:center;gap:8px;">
        <button type="button" id="selectAll" class="btn btn-sm btn-outline" style="font-size:12px;font-weight:700;">
          ✓ Pilih Semua
        </button>
        <button type="button" id="clearAll" class="btn btn-sm btn-outline" style="font-size:12px;color:#ef4444;border-color:#fecaca;">
          ✕ Hapus Pilihan
        </button>
        <button type="button" onclick="openModal('modalTambahKriteriaStep1')" class="btn btn-sm btn-primary" style="font-size:12px;font-weight:700;background:#3f51b5;border-color:#3f51b5;">
          + Tambah Kriteria Baru
        </button>
      </div>
    </div>
    <div class="card-body" style="padding:24px;">
      <p style="font-size:13px;color:#64748b;margin-bottom:16px;">
        Pilihlah kriteria-kriteria di bawah ini yang akan ditetapkan. Kriteria yang dipilih pada Langkah 1 ini akan langsung dipakai di Langkah 2 sehingga Anda tidak perlu memilih kriteria lagi di Langkah 2.
      </p>

      <div id="kriteriaCheckboxes" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:12px;">
        <?php foreach ($kriteria as $k): ?>
        <label class="checkbox-item" id="ci_<?= $k['id'] ?>"
               style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1.5px solid #e2e8f0;background:#f8faff;border-radius:10px;cursor:pointer;transition:all 0.2s;">
          <input type="checkbox" name="kriteria_ids[]" value="<?= $k['id'] ?>" onchange="updateCount()" class="k-check" style="flex-shrink:0;width:18px;height:18px;accent-color:#3f51b5;">
          <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:8px;">
              <span style="font-size:11px;font-weight:900;background:#3f51b5;color:#fff;padding:2px 7px;border-radius:5px;text-transform:uppercase;">
                <?= htmlspecialchars($k['kode'] ?? 'KR') ?>
              </span>
              <span style="font-size:13.5px;font-weight:700;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                <?= htmlspecialchars($k['nama']) ?>
              </span>
            </div>
            <?php if (!empty($k['deskripsi'])): ?>
            <div style="font-size:11.5px;color:#64748b;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
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
  <div style="display:flex;align-items:center;justify-content:space-between;margin-top:24px;padding:16px 22px;background:#fff;border-radius:14px;border:1px solid #e2e8f0;box-shadow:0 2px 8px rgba(26,35,126,0.07);gap:12px;">
    <a href="<?= BASE_URL ?>/ppepp<?= !empty($projectId) ? '/' . $projectId : '' ?>" class="btn btn-outline">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
      ← Batal / Kembali ke Project Library
    </a>
    <button type="submit" class="btn btn-primary btn-lg" style="background:#059669;border-color:#059669;font-weight:800;padding:12px 26px;font-size:14px;display:flex;align-items:center;gap:8px;">
      Lanjut ke Langkah 2: Penyusunan Standar
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><polyline points="9 18 15 12 9 6"/></svg>
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
      el.style.borderColor = '#3f51b5';
      el.style.background = '#eef2ff';
    } else {
      el.style.borderColor = '#e2e8f0';
      el.style.background = '#f8faff';
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
      label.style.cssText = 'display:flex;align-items:center;gap:10px;padding:10px 12px;border-color:#3f51b5;background:#eef2ff;';
      label.innerHTML =
        '<input type="checkbox" name="kriteria_ids[]" value="' + k.id + '" checked onchange="updateCount()" class="k-check" style="flex-shrink:0;">' +
        '<div style="flex-shrink:0;" onclick="event.stopPropagation()">' +
          '<input type="text" name="kriteria_kodes[' + k.id + ']" placeholder="Isi Kode..." value="' + esc(k.kode || '') + '" style="width:95px;padding:5px 8px;border:1.5px solid #cbd5e1;border-radius:6px;font-size:12px;font-weight:700;text-transform:uppercase;outline:none;background:#fff;" oninput="var cb=this.closest(\'.checkbox-item\').querySelector(\'.k-check\'); if(this.value.trim() && !cb.checked){cb.checked=true;updateCount();}">' +
        '</div>' +
        '<div style="flex:1;min-width:0;">' +
          '<div style="font-size:13px;font-weight:600;color:#1e293b;">' + esc(k.nama) + '</div>' +
          (k.deskripsi ? '<div style="font-size:11px;color:#64748b;margin-top:1px;">' + esc(k.deskripsi.substring(0, 55)) + '...</div>' : '') +
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
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Tambah Kriteria Baru
      </h3>
      <button class="modal-close" data-modal-close="modalTambahKriteriaStep1">✕</button>
    </div>
    <form id="formTambahKriteriaStep1">
      <div class="modal-body">
        <div id="step1AddError" style="display:none;background:#fef2f2;color:#991b1b;border-radius:8px;padding:10px 14px;font-size:13px;margin-bottom:14px;"></div>
        <div class="form-group mb-3">
          <label for="step1AddKode">Kode Kriteria <span style="color:#ef4444;">*</span></label>
          <input type="text" id="step1AddKode" name="kode" class="form-control"
                 placeholder="Contoh: C1, K1, STD-01" value="" maxlength="20" required>
        </div>
        <div class="form-group mb-3">
          <label for="step1AddNama">Nama Kriteria <span style="color:#ef4444;">*</span></label>
          <input type="text" id="step1AddNama" name="nama" class="form-control"
                 placeholder="Contoh: Tata Pamong & Tata Kelola" required>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label for="step1AddDeskripsi">Deskripsi <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
          <textarea id="step1AddDeskripsi" name="deskripsi" class="form-control"
                    placeholder="Deskripsi singkat tentang kriteria ini..." rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close="modalTambahKriteriaStep1">Batal</button>
        <button type="submit" class="btn btn-primary" id="step1AddBtn">
          Simpan & Gunakan
        </button>
      </div>
    </form>
  </div>
</div>
