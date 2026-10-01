<?php
$pageTitle = 'Peningkatan Standar — ' . htmlspecialchars($peningkatan['judul']);
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Peningkatan', 'url' => BASE_URL . '/peningkatan'],
  ['label' => htmlspecialchars($peningkatan['judul'])],
];

$penetapan = $peningkatan['penetapan'] ?? [];
$details = $peningkatan['details'] ?? [];
$pkId = $peningkatan['id'];
$berkasList = $peningkatan['berkas_list'] ?? [];
$tahunAjarans = $tahunAjarans ?? [];

// Pisahkan kriteria: Terpenuhi (Masuk Peningkatan) vs Belum Terpenuhi (Dari Pengendalian)
$standarTerpenuhi = [];
$standarBelum = [];

foreach ($details as $d) {
  $st = $d['status_capaian'] ?? 'belum_tercapai';
  if ($st === 'tercapai') {
    $standarTerpenuhi[] = $d;
  } else {
    $standarBelum[] = $d;
  }
}

$cntTerpenuhi = count($standarTerpenuhi);
$cntBelum = count($standarBelum);
$totalStandar = count($details);
?>

<!-- Header Banner -->
<div
  style="background:linear-gradient(135deg,#0f172a 0%,#064e3b 50%,#047857 100%);border-radius:16px;padding:26px 30px;margin-bottom:24px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;box-shadow:0 10px 30px rgba(5,150,105,0.25);">
  <div>
    <div
      style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,255,255,0.6);margin-bottom:6px;">
      Peningkatan Standar Mutu SPMI (Tahap P Ketiga • PPEPP)
    </div>
    <h2 style="font-size:24px;font-weight:900;color:#fff;margin-bottom:4px;letter-spacing:-0.4px;">
      <?= htmlspecialchars($peningkatan['judul']) ?>
    </h2>
    <div
      style="font-size:13px;color:rgba(255,255,255,0.75);display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:4px;">
      <span>Penetapan Acuan: <strong><?= htmlspecialchars($penetapan['judul'] ?? '—') ?></strong></span>
      <span>•</span>
      <span>Tahun Ajaran: <strong><?= htmlspecialchars($penetapan['ta_nama'] ?? '—') ?></strong></span>
    </div>
  </div>

  <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
    <?php if ($cntTerpenuhi > 0): ?>
      <button type="button" onclick="generateAllAIPeningkatan()" id="btnGenerateAllAI" class="btn btn-primary"
        style="background:linear-gradient(135deg,#10b981,#059669);border:none;font-weight:800;font-size:13px;display:flex;align-items:center;gap:8px;box-shadow:0 4px 14px rgba(16,185,129,0.4);cursor:pointer;padding:10px 18px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
          <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
        </svg>
        ✨ Generate Semua AI Peningkatan (1-Klik)
      </button>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>/peningkatan/<?= $pkId ?>" class="btn btn-outline"
      style="background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.25);color:#fff;font-size:12.5px;">
      Lihat Hasil ↗
    </a>
    <a href="<?= BASE_URL ?>/ppepp<?= !empty($peningkatan['ppepp_project_id']) ? '/' . $peningkatan['ppepp_project_id'] : '' ?>" class="btn btn-outline"
      style="background:rgba(255,255,255,0.06);border-color:rgba(255,255,255,0.2);color:#fff;font-size:12.5px;">
      ← Kembali ke Project Library
    </a>
  </div>
</div>

<?php if (!empty($flash['message'])): ?>
  <div
    style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;<?= $flash['type'] === 'success' ? 'background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;' : 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;' ?>">
    <?= $flash['type'] === 'success' ? '✓' : '✕' ?>   <?= htmlspecialchars($flash['message']) ?>
  </div>
<?php endif; ?>

<!-- Summary Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px;margin-bottom:24px;">
  <div
    style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total
      Standar Penetapan</div>
    <div style="font-size:26px;font-weight:900;color:#1e293b;margin-top:4px;" id="statTotal"><?= $totalStandar ?></div>
    <div style="font-size:11.5px;color:#94a3b8;margin-top:2px;">Dari siklus tahun berjalan</div>
  </div>

  <div
    style="background:#fff;border:1.5px solid #a7f3d0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="font-size:11.5px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.5px;">✓ Standar
      Terpenuhi (Masuk Peningkatan)</div>
    <div style="font-size:26px;font-weight:900;color:#065f46;margin-top:4px;" id="statTerpenuhi"><?= $cntTerpenuhi ?>
    </div>
    <div style="font-size:11.5px;color:#059669;margin-top:2px;">Dianalisis &amp; dinaikkan targetnya</div>
  </div>

  <div
    style="background:#fff;border:1.5px solid #fed7aa;border-radius:14px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
    <div style="font-size:11.5px;font-weight:700;color:#ea580c;text-transform:uppercase;letter-spacing:0.5px;">⚠️
      Standar Belum Terpenuhi (Pengendalian)</div>
    <div style="font-size:26px;font-weight:900;color:#9a3412;margin-top:4px;" id="statBelum"><?= $cntBelum ?></div>
    <div style="font-size:11.5px;color:#c2410c;margin-top:2px;">Tetap masuk tahun depan tanpa ubah target</div>
  </div>
</div>

<!-- SECTION 1: ANALISIS & PENINGKATAN STANDAR TERPENUHI -->
<div style="margin-bottom:36px;">
  <div style="margin-bottom:16px;">
    <h3 style="font-size:18px;font-weight:900;color:#1e293b;margin:0 0 2px;display:flex;align-items:center;gap:8px;">
      <span>🚀 1. Analisis &amp; Peningkatan Standar yang Telah Terpenuhi</span>
    </h3>
    <p style="font-size:13px;color:#64748b;margin:0;">
      Hanya standar yang telah terpenuhi atau melebihi target yang dianalisis untuk ditingkatkan. Target &amp; indikator
      baru di bawah ini akan <strong>otomatis masuk ke Penetapan tahun berikutnya</strong> sebagai hasil peningkatan
      mutu.
    </p>
  </div>

  <?php if (empty($standarTerpenuhi)): ?>
    <div
      style="background:#fef9e7;border:1.5px solid #f59e0b;border-radius:12px;padding:20px;color:#92400e;font-size:13.5px;text-align:center;">
      <strong>Belum ada standar yang berstatus Terpenuhi / Tercapai Penuh pada siklus ini.</strong><br>
      Seluruh standar saat ini berada pada tahap Pengendalian untuk tindakan koreksi.
    </div>
  <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:20px;">
      <?php foreach ($standarTerpenuhi as $idx => $d): ?>
        <?php
        $pdId = (int) $d['id'];
        $kid = (int) $d['kriteria_id'];
        $displayKode = !empty($d['kriteria_kode']) ? $d['kriteria_kode'] : 'STD-' . ($idx + 1);
        ?>
        <div class="standar-pk-card" id="card_pk_<?= $pdId ?>"
          style="background:#fff;border:1.5px solid #a7f3d0;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(5,150,105,0.06);">

          <!-- Card Header -->
          <div
            style="padding:16px 22px;background:#f0fdf4;border-bottom:1.5px solid #a7f3d0;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:12px;">
              <div style="padding:5px 12px;background:#059669;color:#fff;border-radius:8px;font-weight:800;font-size:12px;">
                <?= htmlspecialchars($displayKode) ?>
              </div>
              <div>
                <div style="display:flex;align-items:center;gap:8px;">
                  <h4 style="font-size:15px;font-weight:800;color:#065f46;margin:0;">
                    <?= htmlspecialchars($d['kriteria_nama']) ?></h4>
                  <span
                    style="font-size:11px;font-weight:700;color:#059669;background:#d1fae5;padding:2px 8px;border-radius:6px;">
                    Indikator #<?= $idx + 1 ?>
                  </span>
                </div>
                <div style="font-size:11.5px;color:#047857;"><?= htmlspecialchars($d['kriteria_deskripsi'] ?? '') ?></div>
              </div>
            </div>

            <div style="display:flex;align-items:center;gap:10px;">
              <span
                style="font-size:11.5px;font-weight:800;padding:4px 12px;border-radius:20px;background:#d1fae5;color:#065f46;">
                ✓ Status: Terpenuhi Penuh (Layak Ditingkatkan)
              </span>

              <!-- Tombol AI Single Peningkatan -->
              <button type="button" onclick="generateSingleAIPeningkatan(<?= $pdId ?>, <?= $kid ?>)"
                id="btn_ai_pk_<?= $pdId ?>"
                style="background:#fff;border:1.5px solid #059669;color:#059669;padding:7px 14px;border-radius:9px;font-size:12px;font-weight:800;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                  <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                </svg>
                ✨ Generate Peningkatan AI
              </button>
            </div>
          </div>

          <!-- Card Body -->
          <div style="padding:22px;">

            <!-- Target Standar Lama vs Capaian Saat Ini -->
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;margin-bottom:18px;">
              <?php if (!empty($d['strategi'])): ?>
                <div style="background:#f8faff;border-left:4px solid #4f46e5;padding:10px 14px;border-radius:0 8px 8px 0;">
                  <div
                    style="font-size:11.5px;font-weight:800;text-transform:uppercase;color:#4f46e5;margin-bottom:2px;letter-spacing:0.5px;">
                    📌 Aturan / Dasar Hukum:</div>
                  <div style="font-size:14.5px;color:#1e293b;line-height:1.6;font-weight:500;">
                    <?= htmlspecialchars($d['strategi']) ?></div>
                </div>
              <?php endif; ?>

              <div style="background:#f8fafc;border-left:4px solid #64748b;padding:10px 14px;border-radius:0 8px 8px 0;">
                <div
                  style="font-size:11.5px;font-weight:800;text-transform:uppercase;color:#64748b;margin-bottom:2px;letter-spacing:0.5px;">
                  🎯 Pernyataan Standar Lama:</div>
                <div style="font-size:14.5px;color:#1e293b;line-height:1.6;font-weight:500;">
                  <?= htmlspecialchars($d['target_capaian'] ?: '—') ?></div>
              </div>

              <div style="background:#f0fdf4;border-left:4px solid #059669;padding:10px 14px;border-radius:0 8px 8px 0;">
                <div
                  style="font-size:11.5px;font-weight:800;text-transform:uppercase;color:#065f46;margin-bottom:2px;letter-spacing:0.5px;">
                  📊 Target / Indikator Lama:</div>
                <div style="font-size:14.5px;color:#1e293b;line-height:1.6;font-weight:500;">
                  <?= htmlspecialchars($d['indikator'] ?: '—') ?></div>
              </div>
            </div>

            <!-- FORM PENINGKATAN STANDAR BARU -->
            <div style="display:flex;flex-direction:column;gap:14px;">

              <!-- 1. Alasan Peningkatan -->
              <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                  <label style="font-size:12.5px;font-weight:800;color:#1e293b;">
                    🔍 1. Analisis Potensi &amp; Alasan Peningkatan Mutu Standar:
                  </label>
                  <span id="saveStatus_alasan_<?= $pdId ?>" style="font-size:11px;color:#94a3b8;"></span>
                </div>
                <textarea id="alasan_<?= $pdId ?>" class="form-control" rows="2"
                  placeholder="Analisis mengapa standar ini siap ditingkatkan ke tolok ukur yang lebih tinggi..."
                  onchange="savePkField(<?= $pdId ?>, <?= $kid ?>, 'alasan_peningkatan', this.value)"
                  style="font-size:12.5px;line-height:1.5;"><?= htmlspecialchars($d['alasan_peningkatan'] ?? '') ?></textarea>
              </div>

              <!-- 2. Target Standar Baru (Ditingkatkan) -> Masuk ke Penetapan Tahun Depan -->
              <div style="background:#ecfdf5;border:1.5px solid #a7f3d0;border-radius:12px;padding:14px 16px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                  <label style="font-size:13px;font-weight:800;color:#065f46;">
                    🎯 2. Pernyataan Standar / Target Baru (Ditingkatkan): <span style="color:#ef4444;">*</span>
                  </label>
                  <span id="saveStatus_target_<?= $pdId ?>" style="font-size:11px;color:#94a3b8;"></span>
                </div>
                <div style="font-size:11.5px;color:#047857;margin-bottom:6px;">
                  Rumusan target baru yang lebih tinggi. Nilai ini akan <strong>menjadi target standar di Penetapan tahun
                    depan</strong>.
                </div>
                <textarea id="target_baru_<?= $pdId ?>" class="form-control" rows="2"
                  placeholder="Contoh: Meningkatkan rata-rata persentase kelulusan tepat waktu menjadi minimal 90%..."
                  onchange="savePkField(<?= $pdId ?>, <?= $kid ?>, 'target_baru', this.value)"
                  style="font-size:12.5px;line-height:1.5;background:#fff;font-weight:600;"><?= htmlspecialchars($d['target_baru'] ?? '') ?></textarea>
              </div>

              <!-- 3. Indikator Baru (Ditingkatkan) -> Masuk ke Penetapan Tahun Depan -->
              <div style="background:#f0fdf4;border:1px solid #a7f3d0;border-radius:12px;padding:14px 16px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                  <label style="font-size:13px;font-weight:800;color:#065f46;">
                    📊 3. Indikator Ketercapaian Baru (Ditingkatkan): <span style="color:#ef4444;">*</span>
                  </label>
                  <span id="saveStatus_indikator_<?= $pdId ?>" style="font-size:11px;color:#94a3b8;"></span>
                </div>
                <textarea id="indikator_baru_<?= $pdId ?>" class="form-control" rows="2"
                  placeholder="Rumusan indikator terukur baru untuk mengukur standar yang ditingkatkan..."
                  onchange="savePkField(<?= $pdId ?>, <?= $kid ?>, 'indikator_baru', this.value)"
                  style="font-size:12.5px;line-height:1.5;background:#fff;"><?= htmlspecialchars($d['indikator_baru'] ?? '') ?></textarea>
              </div>

              <!-- 4. Program / Strategi Baru & Status -->
              <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px;align-items:end;">
                <div>
                  <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">📈 Program
                    Kerja / Strategi Pencapaian Baru:</label>
                  <input type="text" id="strategi_baru_<?= $pdId ?>" class="form-control"
                    value="<?= htmlspecialchars($d['strategi_baru'] ?? '') ?>"
                    placeholder="Misal: Workshop kurikulum berbasis OBE &amp; bimbingan intensif"
                    onchange="savePkField(<?= $pdId ?>, <?= $kid ?>, 'strategi_baru', this.value)" style="font-size:12px;">
                </div>

                <div>
                  <label style="font-size:12px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">🏷️ Estimasi
                    Kenaikan Target:</label>
                  <input type="text" id="nilai_kenaikan_<?= $pdId ?>" class="form-control"
                    value="<?= htmlspecialchars($d['nilai_kenaikan'] ?? '') ?>" placeholder="Misal: Naik 10-15%"
                    onchange="savePkField(<?= $pdId ?>, <?= $kid ?>, 'nilai_kenaikan', this.value)" style="font-size:12px;">
                </div>
              </div>

            </div>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- SECTION 2: RANGKUMAN STANDAR BELUM TERPENUHI (DARI PENGENDALIAN) -->
<div
  style="background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;padding:22px 26px;margin-bottom:32px;box-shadow:0 2px 10px rgba(0,0,0,0.02);">
  <div
    style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:14px;">
    <div>
      <h3 style="font-size:16px;font-weight:900;color:#1e293b;margin:0 0 2px;">
        ℹ️ 2. Standar Belum Terpenuhi (Dari Tahap Pengendalian)
      </h3>
      <p style="font-size:12.5px;color:#64748b;margin:0;">
        Standar-standar di bawah ini <strong>tidak masuk peningkatan</strong> karena belum tercapai dan sedang dalam
        pengendalian/koreksi. Saat membuat Penetapan tahun berikutnya, standar ini <strong>tetap masuk tanpa perubahan
          target (tetap dapat diedit)</strong>.
      </p>
    </div>
    <span class="badge" style="background:#fed7aa;color:#9a3412;font-weight:800;padding:6px 14px;border-radius:20px;">
      <?= $cntBelum ?> Standar dalam Pengendalian
    </span>
  </div>

  <?php if (empty($standarBelum)): ?>
    <div style="font-size:12.5px;color:#059669;padding:10px 14px;background:#f0fdf4;border-radius:8px;">
      ✓ Hebat! Seluruh standar pada siklus ini telah terpenuhi dan masuk ke daftar peningkatan di atas.
    </div>
  <?php else: ?>
    <div style="overflow-x:auto;border:1px solid #e2e8f0;border-radius:10px;">
      <table style="width:100%;border-collapse:collapse;font-size:12.5px;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:800;text-align:left;">
            <th style="padding:10px 14px;width:80px;">Kode</th>
            <th style="padding:10px 14px;">Nama Kriteria</th>
            <th style="padding:10px 14px;">Target Capaian Berjalan</th>
            <th style="padding:10px 14px;">Status RTL Pengendalian</th>
            <th style="padding:10px 14px;width:180px;">Perlakuan Tahun Depan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($standarBelum as $sb): ?>
            <tr style="border-bottom:1px solid #f1f5f9;">
              <td style="padding:10px 14px;font-weight:800;color:#9a3412;">
                <?= htmlspecialchars($sb['kriteria_kode'] ?? 'KTR') ?></td>
              <td style="padding:10px 14px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($sb['kriteria_nama']) ?>
              </td>
              <td style="padding:10px 14px;color:#64748b;">
                <?= htmlspecialchars(substr($sb['target_capaian'] ?? '', 0, 70)) ?>...</td>
              <td style="padding:10px 14px;">
                <span style="font-size:11.5px;font-weight:700;color:#c2410c;">
                  ⚠️
                  <?= htmlspecialchars($sb['status_tindakan'] ?? 'belum') === 'selesai' ? 'Selesai Dikoreksi' : 'Pengendalian' ?>
                </span>
              </td>
              <td style="padding:10px 14px;color:#059669;font-weight:700;">
                ✓ Dilanjutkan (Tetap/Dapat Diedit)
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- SECTION 3: UPLOAD BERKAS SK PENETAPAN STANDAR BARU / KEBIJAKAN -->
<div
  style="background:#fff;border:1.5px solid #a7f3d0;border-radius:16px;padding:24px 28px;margin-bottom:32px;box-shadow:0 4px 16px rgba(5,150,105,0.06);">
  <div
    style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:16px;border-bottom:1.5px solid #ecfdf5;padding-bottom:14px;">
    <div>
      <h3 style="font-size:18px;font-weight:900;color:#1e293b;margin:0 0 4px;display:flex;align-items:center;gap:8px;">
        <span>📜 3. Upload Berkas SK Penetapan Standar Baru / Kebijakan Peningkatan Mutu</span>
      </h3>
      <p style="font-size:13px;color:#64748b;margin:0;">
        Unggah berkas Surat Keputusan (SK) Dekan/Pimpinan Fakultas terkait penetapan standar baru atau kebijakan
        peningkatan mutu yang telah disahkan.
      </p>
    </div>

    <div>
      <button type="button" onclick="triggerUploadSk()" class="btn btn-primary"
        style="background:#059669;border:none;font-weight:800;font-size:12.5px;display:flex;align-items:center;gap:6px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15">
          <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
          <polyline points="17 8 12 3 7 8" />
          <line x1="12" y1="3" x2="12" y2="15" />
        </svg>
        + Upload Berkas SK
      </button>
      <input type="file" id="skFileInput" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display:none;"
        onchange="handleUploadSk(this)">
    </div>
  </div>

  <!-- Form Metadata SK -->
  <div
    style="background:#f0fdf4;border:1px solid #a7f3d0;border-radius:10px;padding:14px;margin-bottom:16px;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:10px;align-items:end;">
    <div>
      <label style="font-size:11.5px;font-weight:700;color:#065f46;margin-bottom:4px;display:block;">Nomor SK:</label>
      <input type="text" id="skNomorInput" class="form-control" placeholder="Contoh: SK/045/DEK/FT/2026"
        style="font-size:12px;">
    </div>
    <div>
      <label style="font-size:11.5px;font-weight:700;color:#065f46;margin-bottom:4px;display:block;">Judul / Perihal
        SK:</label>
      <input type="text" id="skJudulInput" class="form-control" placeholder="Contoh: SK Standar Mutu Pembelajaran 2026"
        style="font-size:12px;">
    </div>
    <div>
      <label style="font-size:11.5px;font-weight:700;color:#065f46;margin-bottom:4px;display:block;">Tanggal Penetapan
        SK:</label>
      <input type="date" id="skTglInput" class="form-control" value="<?= date('Y-m-d') ?>" style="font-size:12px;">
    </div>
    <div>
      <button type="button" onclick="triggerUploadSk()" class="btn btn-outline btn-sm"
        style="color:#059669;border-color:#059669;font-weight:700;height:35px;width:100%;">
        Pilih File SK &amp; Upload
      </button>
    </div>
  </div>

  <!-- Tabel Berkas SK yang Sudah Diupload -->
  <div style="overflow-x:auto;border:1px solid #e2e8f0;border-radius:10px;background:#fff;">
    <table style="width:100%;border-collapse:collapse;font-size:12.5px;" id="skTable">
      <thead>
        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:800;text-align:left;">
          <th style="padding:10px 14px;width:30px;">#</th>
          <th style="padding:10px 14px;">Nomor SK</th>
          <th style="padding:10px 14px;">Perihal / Judul SK</th>
          <th style="padding:10px 14px;">Tanggal SK</th>
          <th style="padding:10px 14px;">Dokumen File</th>
          <th style="padding:10px 14px;width:100px;text-align:center;">Aksi</th>
        </tr>
      </thead>
      <tbody id="skTableBody">
        <?php if (empty($berkasList)): ?>
          <tr id="emptySkRow">
            <td colspan="6" style="padding:24px;text-align:center;color:#94a3b8;font-style:italic;">
              Belum ada berkas SK yang diunggah. Silakan upload berkas SK pengesahan standar baru.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($berkasList as $sIdx => $sk): ?>
            <tr id="sk_row_<?= htmlspecialchars($sk['id'] ?? '') ?>" style="border-bottom:1px solid #f1f5f9;">
              <td style="padding:10px 14px;color:#94a3b8;"><?= $sIdx + 1 ?></td>
              <td style="padding:10px 14px;font-weight:800;color:#065f46;"><?= htmlspecialchars($sk['nomor_sk'] ?? '—') ?>
              </td>
              <td style="padding:10px 14px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($sk['judul_sk'] ?? '—') ?>
              </td>
              <td style="padding:10px 14px;color:#64748b;"><?= htmlspecialchars($sk['tanggal_sk'] ?? '—') ?></td>
              <td style="padding:10px 14px;">
                <a href="<?= htmlspecialchars($sk['url'] ?? BASE_URL . '/' . $sk['file_path']) ?>" target="_blank"
                  style="color:#059669;font-weight:700;text-decoration:none;">
                  📄 <?= htmlspecialchars($sk['file_name'] ?? 'Buka SK') ?> ↗
                </a>
              </td>
              <td style="padding:10px 14px;text-align:center;">
                <button type="button" onclick="deleteSkItem('<?= htmlspecialchars($sk['id'] ?? $sk['file_path']) ?>')"
                  class="btn btn-outline btn-sm" style="color:#ef4444;border-color:#fca5a5;padding:4px 8px;font-size:11px;">
                  Hapus
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- SECTION 4: BUAT PENETAPAN BARU SIKLUS TAHUN BERIKUTNYA -->
<div
  style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:2px solid #86efac;border-radius:16px;padding:24px 28px;margin-bottom:30px;box-shadow:0 8px 24px rgba(5,150,105,0.08);">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
    <div>
      <div
        style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#059669;margin-bottom:4px;">
        Siklus Berkelanjutan (Continuous Quality Improvement)
      </div>
      <h3 style="font-size:18px;font-weight:900;color:#064e3b;margin:0 0 4px;">
        🚀 Buat Dokumen Penetapan Baru untuk Siklus Tahun Ajaran Berikutnya
      </h3>
      <p style="font-size:13px;color:#065f46;margin:0;max-width:720px;">
        Otomatis menggabungkan <strong><?= $cntTerpenuhi ?> standar hasil peningkatan</strong> (menggunakan target &amp;
        indikator baru) serta <strong><?= $cntBelum ?> standar dari tahap pengendalian</strong> ke dalam dokumen
        Penetapan baru.
      </p>
    </div>

    <div>
      <button type="button" onclick="openExportModal()" class="btn btn-primary"
        style="background:#059669;border:none;font-weight:900;font-size:13.5px;padding:12px 24px;box-shadow:0 4px 14px rgba(5,150,105,0.4);">
        Buat Penetapan Baru Sekarang →
      </button>
    </div>
  </div>
</div>

<!-- Modal Ekspor ke Penetapan Baru -->
<div id="exportModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.65);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(3px);padding:16px;">
  <div style="background:#fff;border-radius:20px;max-width:540px;width:100%;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,0.25);">
    
    <!-- Modal Header Banner -->
    <div style="background:linear-gradient(135deg, #064e3b 0%, #065f46 100%);color:#fff;padding:20px 26px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-shrink:0;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;background:rgba(255,255,255,0.18);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
          🚀
        </div>
        <div>
          <h3 style="font-size:17px;font-weight:800;margin:0;color:#fff;">Mulai Siklus PPEPP Baru</h3>
          <p style="font-size:11.5px;margin:2px 0 0;color:rgba(255,255,255,0.8);">Otomatis membuat Project &amp; Dokumen Penetapan Baru</p>
        </div>
      </div>
      <button type="button" onclick="closeExportModal()" style="color:#fff;opacity:0.8;font-size:22px;line-height:1;background:none;border:none;cursor:pointer;padding:4px 8px;">&times;</button>
    </div>

    <!-- Modal Form -->
    <form action="<?= BASE_URL ?>/peningkatan/<?= $pkId ?>/export-to-penetapan" method="POST" style="display:flex;flex-direction:column;overflow:hidden;flex:1;margin:0;">
      <div style="padding:22px 26px;overflow-y:auto;flex:1;">
        
        <div style="background:#f0fdf4;border:1px solid #a7f3d0;border-radius:10px;padding:12px 14px;font-size:12.5px;color:#065f46;margin-bottom:18px;line-height:1.5;">
          💡 <strong>Hasil Siklus:</strong> <?= $cntTerpenuhi ?> standar hasil peningkatan akan menggunakan <strong>target baru</strong>, sedangkan <?= $cntBelum ?> standar pengendalian tetap disertakan. Sistem akan <strong>otomatis membuat Project PPEPP Baru</strong> untuk tahun ajaran yang Anda pilih.
        </div>

        <div class="form-group" style="margin-bottom:16px;">
          <label style="font-size:13px;font-weight:800;color:#1e293b;margin-bottom:6px;display:block;">
            📅 Pilih Tahun Ajaran Tujuan <span style="color:#ef4444;">*</span>
          </label>
          <select name="target_ta_id" id="exportTargetTaSelectEdit" class="form-control" required style="font-size:13.5px;padding:10px 14px;border-radius:10px;" onchange="handleExportTaChange(this)">
            <option value="">— Pilih Tahun Ajaran Baru —</option>
            <?php foreach ($tahunAjarans as $ta): ?>
              <option value="<?= $ta['id'] ?>" <?= $ta['aktif'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($ta['nama']) ?> (<?= ucfirst($ta['semester'] ?? '') ?>)
                <?= $ta['aktif'] ? '— Aktif Saat Ini' : '' ?>
              </option>
            <?php endforeach; ?>
            <option value="custom" style="font-weight:800;color:#2563eb;">+ Input Tahun Ajaran Baru Manual...</option>
          </select>
        </div>

        <!-- Input TA Custom jika tidak ada di list -->
        <input type="hidden" name="new_ta" value="0" id="exportNewTaFlag">
        <div id="exportCustomTaGroup" style="display:none;margin-bottom:16px;background:#f8fafc;border:1px solid #cbd5e1;border-radius:10px;padding:12px 14px;">
          <label style="font-size:12px;font-weight:700;color:#1e293b;margin-bottom:4px;display:block;">Tuliskan Nama Tahun Ajaran Baru:</label>
          <input type="text" name="new_ta_nama" id="exportNewTaNama" class="form-control" placeholder="Contoh: 2026/2027"
                 style="font-size:13.5px;padding:8px 12px;border-radius:8px;" oninput="document.getElementById('exportNewTaFlag').value='1'">
        </div>

        <div class="form-group" style="margin-bottom:6px;">
          <label style="font-size:13px;font-weight:800;color:#1e293b;margin-bottom:6px;display:block;">
            📝 Judul Dokumen Penetapan Baru:
          </label>
          <input type="text" name="judul_penetapan_baru" class="form-control"
            value="Penetapan Standar Mutu (Hasil Peningkatan &amp; Pengendalian)" required
            style="font-size:13.5px;padding:10px 14px;border-radius:10px;">
        </div>
      </div>

      <!-- Modal Footer -->
      <div style="padding:14px 26px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;flex-shrink:0;">
        <button type="button" onclick="closeExportModal()" class="btn btn-outline"
          style="font-size:13px;padding:9px 18px;border-radius:8px;">Batal</button>
        <button type="submit" class="btn btn-primary"
          style="background:#059669;border:none;font-weight:800;font-size:13px;padding:9px 22px;border-radius:8px;box-shadow:0 4px 14px rgba(5,150,105,0.3);color:#fff;display:inline-flex;align-items:center;gap:6px;cursor:pointer;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15"><polyline points="20 6 9 17 4 12"/></svg>
          Konfirmasi &amp; Mulai Siklus Baru
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Sticky Bottom Bar -->
<div
  style="position:sticky;bottom:0;background:#fff;border-top:1.5px solid #e2e8f0;padding:16px 24px;border-radius:0 0 14px 14px;display:flex;align-items:center;justify-content:space-between;gap:12px;box-shadow:0 -4px 20px rgba(0,0,0,0.06);z-index:40;flex-wrap:wrap;margin-top:12px;">
  <div style="display:flex;align-items:center;gap:10px;">
    <a href="<?= BASE_URL ?>/peningkatan"
      style="background:none;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:600;color:#64748b;text-decoration:none;display:flex;align-items:center;gap:6px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
        <polyline points="15 18 9 12 15 6" />
      </svg>
      Simpan Draft &amp; Keluar
    </a>
    <span style="font-size:12px;color:#94a3b8;">Perubahan target baru tersimpan secara otomatis</span>
  </div>

  <div style="display:flex;align-items:center;gap:10px;">
    <form action="<?= BASE_URL ?>/peningkatan/<?= $pkId ?>/finalize" method="POST" style="margin:0;">
      <button type="submit" class="btn btn-success btn-lg"
        style="font-size:13px;padding:10px 22px;font-weight:800;background:#059669;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15">
          <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
          <polyline points="22 4 12 14.01 9 11.01" />
        </svg>
        Finalisasi Dokumen Peningkatan
      </button>
    </form>
  </div>
</div>

<script>
  var PENINGKATAN_ID = <?= (int) $pkId ?>;
  var BASE_APP = '<?= BASE_URL ?>';

  // ==================================================
  // 1. Auto-Save Field Peningkatan Detail
  // ==================================================
  function savePkField(pdId, kid, field, value) {
    var shortField = field.replace('alasan_peningkatan', 'alasan').replace('target_baru', 'target').replace('indikator_baru', 'indikator');
    var saveEl = document.getElementById('saveStatus_' + shortField + '_' + pdId);
    if (saveEl) saveEl.textContent = 'Menyimpan...';

    var body = new URLSearchParams();
    body.append('peningkatan_id', PENINGKATAN_ID);
    body.append('penetapan_detail_id', pdId);
    body.append('kriteria_id', kid);
    body.append('field', field);
    body.append('value', value);

    fetch(BASE_APP + '/peningkatan/save-detail', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (saveEl) {
          if (data.success) {
            saveEl.textContent = '✓ Tersimpan';
            saveEl.style.color = '#059669';
            setTimeout(function () { if (saveEl) saveEl.textContent = ''; }, 2000);
          } else {
            saveEl.textContent = '✕ Gagal';
            saveEl.style.color = '#ef4444';
          }
        }
      });
  }

  // ==================================================
  // 2. Generate AI Peningkatan Single Indikator
  // ==================================================
  function generateSingleAIPeningkatan(pdId, kid) {
    if (typeof window.USER_HAS_API_KEY !== 'undefined' && !window.USER_HAS_API_KEY) {
      window.showAiWarningModal('🔑 API Key Gemini belum terpasang. Silakan masukkan API Key Gemini Anda terlebih dahulu di menu Pengaturan.', 'missing_key');
      return;
    }

    var btn = document.getElementById('btn_ai_pk_' + pdId);
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span style="animation:spin 0.8s linear infinite;">⏳</span> Menganalisis...';
    }

    var body = new URLSearchParams();
    body.append('peningkatan_id', PENINGKATAN_ID);
    body.append('penetapan_detail_id', pdId);
    body.append('kriteria_id', kid);

    fetch(BASE_APP + '/peningkatan/generate-ai', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg> ✨ Generate Peningkatan AI';
        }
        if (data.success) {
          if (data.alasan) document.getElementById('alasan_' + pdId).value = data.alasan;
          if (data.target_baru) document.getElementById('target_baru_' + pdId).value = data.target_baru;
          if (data.indikator_baru) document.getElementById('indikator_baru_' + pdId).value = data.indikator_baru;
          if (data.strategi_baru) document.getElementById('strategi_baru_' + pdId).value = data.strategi_baru;
          if (data.nilai_kenaikan) document.getElementById('nilai_kenaikan_' + pdId).value = data.nilai_kenaikan;
        } else {
          window.showAiWarningModal(data.message || 'Gagal menyusun peningkatan.', data.error_type);
        }
      })
      .catch(function (err) {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '✨ Generate Peningkatan AI';
        }
        window.showAiWarningModal('Koneksi Error: ' + err.message);
      });
  }

  // ==================================================
  // 3. Generate Semua AI Peningkatan Sekaligus
  // ==================================================
  function generateAllAIPeningkatan() {
    if (typeof window.USER_HAS_API_KEY !== 'undefined' && !window.USER_HAS_API_KEY) {
      window.showAiWarningModal('🔑 API Key Gemini belum terpasang. Silakan masukkan API Key Gemini Anda terlebih dahulu di menu Pengaturan.', 'missing_key');
      return;
    }

    var btn = document.getElementById('btnGenerateAllAI');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span style="animation:spin 0.8s linear infinite;">⏳</span> Memproses Seluruh Standar Terpenuhi...';
    }

    var body = new URLSearchParams();
    body.append('peningkatan_id', PENINGKATAN_ID);

    fetch(BASE_APP + '/peningkatan/generate-all-ai', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg> ✨ Generate Semua AI Peningkatan (1-Klik)';
        }
        if (data.success && data.generated) {
          Object.keys(data.generated).forEach(function (pdId) {
            var g = data.generated[pdId];
            if (g.alasan && document.getElementById('alasan_' + pdId)) document.getElementById('alasan_' + pdId).value = g.alasan;
            if (g.target_baru && document.getElementById('target_baru_' + pdId)) document.getElementById('target_baru_' + pdId).value = g.target_baru;
            if (g.indikator_baru && document.getElementById('indikator_baru_' + pdId)) document.getElementById('indikator_baru_' + pdId).value = g.indikator_baru;
            if (g.strategi_baru && document.getElementById('strategi_baru_' + pdId)) document.getElementById('strategi_baru_' + pdId).value = g.strategi_baru;
            if (g.nilai_kenaikan && document.getElementById('nilai_kenaikan_' + pdId)) document.getElementById('nilai_kenaikan_' + pdId).value = g.nilai_kenaikan;
          });
          alert('✓ ' + data.message);
        } else {
          window.showAiWarningModal(data.message || 'Gagal memproses peningkatan AI.', data.error_type);
        }
      })
      .catch(function (err) {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '✨ Generate Semua AI Peningkatan (1-Klik)';
        }
        window.showAiWarningModal('Koneksi Error: ' + err.message);
      });
  }

  // ==================================================
  // 4. Upload & Kelola Berkas SK
  // ==================================================
  function triggerUploadSk() {
    var fileInput = document.getElementById('skFileInput');
    if (fileInput) fileInput.click();
  }

  function handleUploadSk(input) {
    if (!input.files || !input.files[0]) return;
    var nomor = document.getElementById('skNomorInput')?.value || '';
    var judul = document.getElementById('skJudulInput')?.value || '';
    var tgl = document.getElementById('skTglInput')?.value || '';

    var fd = new FormData();
    fd.append('peningkatan_id', PENINGKATAN_ID);
    fd.append('berkas_sk_file', input.files[0]);
    fd.append('nomor_sk', nomor);
    fd.append('judul_sk', judul);
    fd.append('tanggal_sk', tgl);

    fetch(BASE_APP + '/peningkatan/upload-sk', {
      method: 'POST',
      body: fd
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success && data.berkas) {
          var tbody = document.getElementById('skTableBody');
          var emptyRow = document.getElementById('emptySkRow');
          if (emptyRow) emptyRow.remove();

          var s = data.berkas;
          var tr = document.createElement('tr');
          tr.id = 'sk_row_' + s.id;
          tr.style.borderBottom = '1px solid #f1f5f9';
          tr.innerHTML = '<td style="padding:10px 14px;color:#94a3b8;">' + (tbody.children.length + 1) + '</td>' +
            '<td style="padding:10px 14px;font-weight:800;color:#065f46;">' + escHtml(s.nomor_sk) + '</td>' +
            '<td style="padding:10px 14px;font-weight:700;color:#1e293b;">' + escHtml(s.judul_sk) + '</td>' +
            '<td style="padding:10px 14px;color:#64748b;">' + escHtml(s.tanggal_sk) + '</td>' +
            '<td style="padding:10px 14px;"><a href="' + s.url + '" target="_blank" style="color:#059669;font-weight:700;text-decoration:none;">📄 ' + escHtml(s.file_name) + ' ↗</a></td>' +
            '<td style="padding:10px 14px;text-align:center;"><button type="button" onclick="deleteSkItem(\'' + s.id + '\')" class="btn btn-outline btn-sm" style="color:#ef4444;border-color:#fca5a5;padding:4px 8px;font-size:11px;">Hapus</button></td>';
          tbody.appendChild(tr);

          // Reset input form
          if (document.getElementById('skNomorInput')) document.getElementById('skNomorInput').value = '';
          if (document.getElementById('skJudulInput')) document.getElementById('skJudulInput').value = '';
          alert('✓ ' + data.message);
        } else {
          alert('Upload Gagal: ' + (data.message || 'Error'));
        }
      })
      .finally(function () {
        input.value = '';
      });
  }

  function deleteSkItem(berkasId) {
    if (!confirm('Hapus berkas SK ini?')) return;
    var body = new URLSearchParams();
    body.append('peningkatan_id', PENINGKATAN_ID);
    body.append('berkas_id', berkasId);

    fetch(BASE_APP + '/peningkatan/delete-sk', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success) {
          var row = document.getElementById('sk_row_' + berkasId);
          if (row) row.remove();
        }
      });
  }

  // Modal Export
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

  function escHtml(str) {
    if (!str) return '';
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
  }
</script>