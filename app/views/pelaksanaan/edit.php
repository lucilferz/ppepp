<?php
$pageTitle   = 'Edit Pelaksanaan & Kelola Bukti';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Pelaksanaan', 'url' => BASE_URL . '/pelaksanaan'],
  ['label' => htmlspecialchars($pelaksanaan['judul']), 'url' => BASE_URL . '/pelaksanaan/' . $pelaksanaan['id']],
  ['label' => 'Edit'],
];
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h2>Edit Informasi Pelaksanaan & Kelola Bukti</h2>
    <p class="page-desc">Perbarui judul, deskripsi, penetapan acuan, dan kelola link bukti fisik untuk setiap kriteria</p>
  </div>
  <div style="display:flex;gap:10px;">
    <a href="<?= BASE_URL ?>/pelaksanaan/<?= $pelaksanaan['id'] ?>" class="btn btn-outline">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
      Lihat Detail
    </a>
  </div>
</div>

<!-- FORM UTAMA: INFORMASI & ACUAN -->
<form method="POST" action="<?= BASE_URL ?>/pelaksanaan/<?= $pelaksanaan['id'] ?>/update" class="mb-4">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <!-- Form Kiri: Judul & Deskripsi -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Informasi Pelaksanaan
        </div>
      </div>
      <div class="card-body">
        <div class="form-group">
          <label for="judul">Judul / Kegiatan Pelaksanaan <span style="color:#ef4444;">*</span></label>
          <input type="text" id="judul" name="judul" class="form-control"
                 value="<?= htmlspecialchars($pelaksanaan['judul']) ?>" required>
        </div>
        <div class="form-group">
          <label for="deskripsi">Deskripsi Ringkasan (Opsional)</label>
          <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                    placeholder="Catatan tambahan mengenai lingkup pelaksanaan ini..."><?= htmlspecialchars($pelaksanaan['deskripsi'] ?? '') ?></textarea>
        </div>

        <div style="margin-top:15px;">
          <button type="submit" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
              <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
              <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
            </svg>
            Simpan Informasi Pelaksanaan
          </button>
        </div>
      </div>
    </div>

    <!-- Form Kanan: Pilih Penetapan Acuan -->
    <div>
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 01-2-2h11"/></svg>
            Pilih Penetapan Acuan
          </div>
        </div>
        <div class="card-body" style="padding:14px;">
          <div style="font-size:12.5px;color:#64748b;margin-bottom:12px;">Dokumen penetapan acuan pelaksanaan ini:</div>
          <div style="display:flex;flex-direction:column;gap:8px;">
            <?php foreach ($penetapans as $p): ?>
            <label style="display:flex;align-items:flex-start;gap:12px;padding:12px;background:#f8faff;border:1.5px solid <?= $p['id'] == $pelaksanaan['penetapan_id'] ? '#3f51b5' : '#e2e8f0' ?>;border-radius:10px;cursor:pointer;">
              <input type="radio" name="penetapan_id" value="<?= $p['id'] ?>" <?= $p['id'] == $pelaksanaan['penetapan_id'] ? 'checked' : '' ?> required
                     style="margin-top:2px;flex-shrink:0;accent-color:#3f51b5;">
              <div style="flex:1;min-width:0;">
                <div style="font-size:13.5px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($p['judul']) ?></div>
                <div style="display:flex;gap:7px;margin-top:4px;flex-wrap:wrap;">
                  <span class="badge badge-primary"><?= htmlspecialchars($p['ta_nama']) ?></span>
                  <span class="badge <?= $p['status'] === 'final' ? 'badge-success' : 'badge-warning' ?>"><?= $p['status'] === 'final' ? 'Final ✓' : 'Draft' ?></span>
                </div>
              </div>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

  </div>
</form>

<!-- KELOLA BUKTI PER KRITERIA -->
<div class="card">
  <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
    <h3 class="card-title">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
      Input &amp; Kelola Link Bukti Fisik per Kriteria
    </h3>
    <span style="font-size:12px;color:var(--text-muted);">(Dapat menambah lebih dari 1 bukti per kriteria)</span>
  </div>
  <div class="card-body" style="padding:16px;">
    <?php if (empty($pelaksanaan['details'])): ?>
    <div style="padding:30px;text-align:center;color:#64748b;">
      Belum ada kriteria pada penetapan acuan ini.
    </div>
    <?php else: ?>
    
    <div style="display:flex;flex-direction:column;gap:14px;">
      <?php foreach ($pelaksanaan['details'] as $idx => $det): 
        $pdId = (int)$det['id'];
        $kid = (int)$det['kriteria_id'];
        $nBukti = count($det['bukti']);
        $displayKode = !empty($det['kode']) ? $det['kode'] : (!empty($det['kriteria_kode']) ? $det['kriteria_kode'] : 'STD-' . ($idx + 1));
      ?>
      <div style="border:1.5px solid #e2e8f0;border-radius:12px;overflow:hidden;background:#fff;" id="det_<?= $pdId ?>">
        <!-- Header Kriteria -->
        <div style="padding:16px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;cursor:pointer;"
             onclick="toggleKriteria(<?= $pdId ?>)">
          <div style="display:flex;align-items:flex-start;gap:14px;flex:1;">
            <span style="background:#3f51b5;color:#fff;font-weight:900;font-size:13px;padding:4px 10px;border-radius:6px;flex-shrink:0;">
              [<?= htmlspecialchars($displayKode) ?>]
            </span>
            <div style="flex:1;">
              <div style="font-weight:800;font-size:16px;color:#0f172a;margin-bottom:4px;"><?= htmlspecialchars($det['kriteria_nama']) ?></div>
              
              <?php if (!empty($det['target_capaian'])): ?>
              <div style="font-size:14.5px;color:#334155;margin-bottom:3px;line-height:1.5;">
                <strong style="color:#0f172a;">🎯 Pernyataan Standar:</strong> <?= htmlspecialchars($det['target_capaian']) ?>
              </div>
              <?php endif; ?>

              <?php if (!empty($det['indikator'])): ?>
              <div style="font-size:14.5px;color:#0369a1;line-height:1.5;">
                <strong style="color:#0284c7;">📊 Target / Indikator:</strong> <?= htmlspecialchars($det['indikator']) ?>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
            <span style="font-size:12.5px;font-weight:800;padding:5px 14px;border-radius:20px;color:<?= $nBukti > 0 ? '#059669' : '#d97706' ?>;background:<?= $nBukti > 0 ? '#ecfdf5' : '#fffbeb' ?>;">
              <?= $nBukti ?> Bukti Link
            </span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" id="toggle_<?= $pdId ?>" style="transition:transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
        </div>

        <!-- Body Kriteria -->
        <div id="body_<?= $pdId ?>" style="display:block;padding:18px;background:#fff;">
          
          <!-- Rincian Acuan Penetapan Standar -->
          <div style="background:#f8faff;border:1.5px solid #e0e7ff;border-radius:12px;padding:16px;margin-bottom:16px;display:flex;flex-direction:column;gap:12px;">
            <?php if (!empty($det['strategi'])): ?>
            <div style="background:#fff;border-left:4px solid #4f46e5;padding:10px 14px;border-radius:0 10px 10px 0;">
              <div style="font-size:12px;font-weight:800;text-transform:uppercase;color:#4f46e5;margin-bottom:3px;letter-spacing:0.5px;">📌 Aturan / Dasar Hukum:</div>
              <div style="font-size:15px;color:#1e293b;line-height:1.6;font-weight:500;"><?= htmlspecialchars($det['strategi']) ?></div>
            </div>
            <?php endif; ?>

            <?php if (!empty($det['target_capaian'])): ?>
            <div style="background:#fff;border-left:4px solid #059669;padding:10px 14px;border-radius:0 10px 10px 0;">
              <div style="font-size:12px;font-weight:800;text-transform:uppercase;color:#065f46;margin-bottom:3px;letter-spacing:0.5px;">🎯 Pernyataan Standar:</div>
              <div style="font-size:15px;color:#1e293b;line-height:1.6;font-weight:500;"><?= htmlspecialchars($det['target_capaian']) ?></div>
            </div>
            <?php endif; ?>

            <?php if (!empty($det['indikator'])): ?>
            <div style="background:#fff;border-left:4px solid #d97706;padding:10px 14px;border-radius:0 10px 10px 0;">
              <div style="font-size:12px;font-weight:800;text-transform:uppercase;color:#92400e;margin-bottom:3px;letter-spacing:0.5px;">📊 Target / Indikator Ketercapaian:</div>
              <div style="font-size:15px;color:#1e293b;line-height:1.6;font-weight:500;"><?= htmlspecialchars($det['indikator']) ?></div>
            </div>
            <?php endif; ?>
          </div>
          
          <!-- Lista Bukti yang Ada -->
          <div id="buktiList_<?= $pdId ?>" style="margin-bottom:14px;">
            <?php if ($nBukti === 0): ?>
            <div id="emptyBukti_<?= $pdId ?>" style="padding:12px 14px;background:#f8fafc;border:1px dashed #cbd5e1;border-radius:8px;font-size:12.5px;color:#64748b;text-align:center;">
              Belum ada bukti link untuk indikator ini. Silakan tambah form di bawah.
            </div>
            <?php else: ?>
            <?php foreach ($det['bukti'] as $b): ?>
            <div class="bukti-item" id="bukti_<?= $b['id'] ?>" style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;margin-bottom:8px;">
              <div style="width:32px;height:32px;background:#3f51b5;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
              </div>
              <div style="flex:1;min-width:0;">
                <div style="font-size:13.5px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($b['judul_bukti']) ?></div>
                <a href="<?= htmlspecialchars($b['url_link']) ?>" target="_blank" rel="noopener" style="font-size:12px;color:#3f51b5;display:block;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:400px;">
                  <?= htmlspecialchars($b['url_link']) ?> ↗
                </a>
                <?php if ($b['keterangan']): ?>
                <div style="font-size:12px;color:#64748b;margin-top:3px;"><?= htmlspecialchars($b['keterangan']) ?></div>
                <?php endif; ?>
              </div>
              <button type="button" onclick="deleteBukti(<?= $b['id'] ?>, <?= $pelaksanaan['id'] ?>, <?= $pdId ?>)" style="background:none;border:none;cursor:pointer;color:#ef4444;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:4px;" title="Hapus bukti link ini">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                Hapus
              </button>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- Form Tambah Bukti Baru -->
          <div style="background:#f0f4ff;border:1.5px solid #c7d2fe;border-radius:10px;padding:14px;">
            <div style="font-size:12.5px;font-weight:700;color:#1e293b;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
              <svg viewBox="0 0 24 24" fill="none" stroke="#3f51b5" stroke-width="2" width="15" height="15"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
              Tambah Bukti Link Baru untuk Indikator (<?= htmlspecialchars($displayKode) ?>)
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
              <div>
                <label style="font-size:12px;font-weight:600;color:#334155;display:block;margin-bottom:4px;">Judul Bukti <span style="color:#ef4444;">*</span></label>
                <input type="text" id="judulBukti_<?= $pdId ?>" placeholder="Contoh: Surat Tugas / RPS / Dokumen Evaluasi" class="form-control" style="font-size:13px;padding:8px 12px;">
              </div>
              <div>
                <label style="font-size:12px;font-weight:600;color:#334155;display:block;margin-bottom:4px;">URL Link Bukti Fisik <span style="color:#ef4444;">*</span></label>
                <input type="url" id="urlLink_<?= $pdId ?>" placeholder="https://drive.google.com/... atau https://..." class="form-control" style="font-size:13px;padding:8px 12px;">
              </div>
            </div>
            <div style="margin-bottom:10px;">
              <label style="font-size:12px;font-weight:600;color:#334155;display:block;margin-bottom:4px;">Keterangan Singkat (Opsional)</label>
              <input type="text" id="ketBukti_<?= $pdId ?>" placeholder="Contoh: Halaman 2-5, Lampiran SK No. 8" class="form-control" style="font-size:13px;padding:8px 12px;">
            </div>
            <button type="button" onclick="addBukti(<?= $pdId ?>, <?= $kid ?>, <?= $pelaksanaan['id'] ?>)" id="btnAddBukti_<?= $pdId ?>" class="btn btn-primary btn-sm">
              + Tambah Bukti Link Ini
            </button>
            <div id="buktiError_<?= $pdId ?>" style="display:none;margin-top:8px;font-size:12px;color:#991b1b;background:#fef2f2;border-radius:6px;padding:6px 10px;"></div>
          </div>

        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php endif; ?>
  </div>
</div>

<script>
var BASE_URL_APP = '<?= BASE_URL ?>';

function toggleKriteria(pdId) {
  var body = document.getElementById('body_' + pdId);
  var toggle = document.getElementById('toggle_' + pdId);
  if (!body) return;
  var isHidden = body.style.display === 'none';
  body.style.display = isHidden ? 'block' : 'none';
  if (toggle) toggle.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
}

function addBukti(pdId, kriteriaId, pelaksanaanId) {
  var judulEl = document.getElementById('judulBukti_' + pdId);
  var urlEl   = document.getElementById('urlLink_' + pdId);
  var ketEl   = document.getElementById('ketBukti_' + pdId);
  var errEl   = document.getElementById('buktiError_' + pdId);
  var btn     = document.getElementById('btnAddBukti_' + pdId);

  var judul   = judulEl ? judulEl.value.trim() : '';
  var url     = urlEl ? urlEl.value.trim() : '';
  var ket     = ketEl ? ketEl.value.trim() : '';

  if (errEl) errEl.style.display = 'none';

  if (!judul || !url) {
    if (errEl) {
      errEl.textContent = 'Judul dan URL link wajib diisi.';
      errEl.style.display = 'block';
    }
    return;
  }

  if (btn) {
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';
  }

  var body = new URLSearchParams();
  body.append('pelaksanaan_id', pelaksanaanId);
  body.append('kriteria_id', kriteriaId);
  body.append('penetapan_detail_id', pdId);
  body.append('judul_bukti', judul);
  body.append('url_link', url);
  body.append('keterangan', ket);

  fetch(BASE_URL_APP + '/pelaksanaan/bukti/add', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (btn) {
      btn.disabled = false;
      btn.textContent = '+ Tambah Bukti Link Ini';
    }

    if (data.success) {
      var emptyEl = document.getElementById('emptyBukti_' + pdId);
      if (emptyEl) emptyEl.remove();

      var listEl = document.getElementById('buktiList_' + pdId);
      if (listEl) {
        var newItem = document.createElement('div');
        newItem.className = 'bukti-item';
        newItem.id = 'bukti_' + data.bukti_id;
        newItem.style.cssText = 'display:flex;align-items:flex-start;gap:12px;padding:12px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;margin-bottom:8px;';
        newItem.innerHTML =
          '<div style="width:32px;height:32px;background:#3f51b5;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0;">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>' +
          '</div>' +
          '<div style="flex:1;min-width:0;">' +
            '<div style="font-size:13.5px;font-weight:700;color:#1e293b;">' + escHtml(data.judul) + '</div>' +
            '<a href="' + escHtml(data.url) + '" target="_blank" style="font-size:12px;color:#3f51b5;display:block;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:400px;">' + escHtml(data.url) + ' ↗</a>' +
            (data.keterangan ? '<div style="font-size:12px;color:#64748b;margin-top:3px;">' + escHtml(data.keterangan) + '</div>' : '') +
          '</div>' +
          '<button type="button" onclick="deleteBukti(' + data.bukti_id + ', ' + pelaksanaanId + ', ' + pdId + ')" style="background:none;border:none;cursor:pointer;color:#ef4444;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:4px;">' +
            'Hapus' +
          '</button>';
        listEl.appendChild(newItem);
      }

      if (judulEl) judulEl.value = '';
      if (urlEl) urlEl.value = '';
      if (ketEl) ketEl.value = '';

      var badge = document.querySelector('#det_' + pdId + ' span');
      if (badge) {
        var n = document.querySelectorAll('#buktiList_' + pdId + ' .bukti-item').length;
        badge.textContent = n + ' Bukti Link';
      }
    } else {
      if (errEl) {
        errEl.textContent = data.message || 'Gagal menyimpan.';
        errEl.style.display = 'block';
      }
    }
  })
  .catch(function(err) {
    if (btn) {
      btn.disabled = false;
      btn.textContent = '+ Tambah Bukti Link Ini';
    }
    if (errEl) {
      errEl.textContent = 'Terjadi kesalahan koneksi/sistem.';
      errEl.style.display = 'block';
    }
  });
}

function deleteBukti(buktiId, pelaksanaanId, pdId) {
  if (!confirm('Hapus bukti link ini?')) return;

  var body = new URLSearchParams();
  body.append('bukti_id', buktiId);
  body.append('pelaksanaan_id', pelaksanaanId);

  fetch(BASE_URL_APP + '/pelaksanaan/bukti/delete', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      var el = document.getElementById('bukti_' + buktiId);
      if (el) {
        el.style.opacity = '0';
        setTimeout(function() { 
          el.remove();
          if (pdId) {
            var badge = document.querySelector('#det_' + pdId + ' span');
            if (badge) {
              var n = document.querySelectorAll('#buktiList_' + pdId + ' .bukti-item').length;
              badge.textContent = n + ' Bukti Link';
            }
          }
        }, 200);
      }
    }
  });
}

function escHtml(str) {
  var d = document.createElement('div');
  d.appendChild(document.createTextNode(str));
  return d.innerHTML;
}
</script>
