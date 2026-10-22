<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan_Rekapitulasi_PPEPP_<?= preg_replace('/[^a-zA-Z0-9_-]/', '_', ($reportData['project']['ta_nama'] ?? 'TA') . '_' . ($reportData['project']['judul'] ?? 'PPEPP')) ?>.pdf</title>
  <style>
    /* Pengaturan Dasar Halaman Cetak & PDF */
    @page {
      size: A4 landscape;
      margin: 1.2cm 1cm 1.2cm 1cm;
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Times New Roman', Times, serif;
      font-size: 8.5pt;
      line-height: 1.35;
      color: #000;
      background: #fff;
      margin: 0;
      padding: 0;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* Floating No-Print Action Toolbar */
    .no-print {
      position: fixed;
      top: 15px;
      right: 20px;
      z-index: 9999;
      display: flex;
      gap: 8px;
      background: rgba(255, 255, 255, 0.95);
      padding: 8px 12px;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-action {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      font-size: 12px;
      font-weight: 700;
      padding: 8px 16px;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border: 1px solid transparent;
      transition: all 0.2s;
    }

    .btn-pdf {
      background: #4f46e5;
      color: #fff;
    }

    .btn-pdf:hover {
      background: #4338ca;
    }

    .btn-print {
      background: #0f172a;
      color: #fff;
    }

    .btn-print:hover {
      background: #1e293b;
    }

    .btn-close {
      background: #fff;
      color: #475569;
      border-color: #cbd5e1;
    }

    .btn-close:hover {
      background: #f1f5f9;
    }

    /* Kop Surat Resmi Format RPS */
    table.header-box {
      border: 1px solid #000;
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
    }

    table.header-box td, table.header-box th {
      border: 1px solid #000;
      padding: 4px 6px;
      vertical-align: middle;
    }

    .bg-header {
      background-color: #E6E6E6 !important;
      font-weight: bold;
      text-align: center;
      -webkit-print-color-adjust: exact;
    }

    /* Bab / Chapter Styling */
    .chapter-header {
      background-color: #E6E6E6 !important;
      font-weight: bold;
      font-size: 10pt;
      text-transform: uppercase;
      padding: 6px 8px;
      border: 1px solid #000;
      margin-bottom: 4px;
      -webkit-print-color-adjust: exact;
    }

    .chapter-desc {
      font-size: 8pt;
      color: #333;
      margin-bottom: 8px;
      font-style: italic;
    }

    /* Tabel Tiap Bab */
    table.table-bab {
      width: 100%;
      border-collapse: collapse;
      border: 1px solid #000;
      font-size: 8pt;
      margin-bottom: 16px;
    }

    table.table-bab th, table.table-bab td {
      border: 1px solid #000;
      padding: 5px 6px;
      vertical-align: top;
      word-wrap: break-word;
    }

    table.table-bab th {
      background-color: #E6E6E6 !important;
      font-weight: bold;
      text-transform: uppercase;
      text-align: center;
      font-size: 8pt;
      -webkit-print-color-adjust: exact;
    }

    .page-break {
      page-break-before: always;
      margin-top: 20px;
    }

    /* QR Code Placeholder styling */
    .qr-box {
      width: 80px;
      height: 80px;
      margin: 4px auto 6px auto;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px dashed #666;
      background: #fafafa;
      font-size: 7pt;
      font-weight: bold;
      text-align: center;
    }

    @media print {
      .no-print {
        display: none !important;
      }
      body {
        padding: 0;
      }
      .page-break {
        page-break-before: always;
        margin-top: 0;
      }
      tr {
        page-break-inside: avoid;
      }
      thead {
        display: table-header-group;
      }
    }
  </style>
</head>
<body onload="if(window.location.search.indexOf('autoprint=1') !== -1) window.print();">

  <?php
  $proj     = $reportData['project'] ?? [];
  $pen      = $reportData['penetapan'] ?? [];
  $matrix   = $reportData['matrix'] ?? [];
  $stats    = $reportData['stats'] ?? [];
  $prodi    = $reportData['prodi'] ?? [];
  $dekan    = $reportData['dekan'] ?? [];
  $userCurr = $reportData['user'] ?? [];

  $namaProdi    = !empty($prodi['nama']) ? $prodi['nama'] : 'Sistem Informasi';
  $jenjangProdi = !empty($prodi['jenjang']) ? $prodi['jenjang'] : 'S1';
  $kodeProdi    = !empty($prodi['kode']) ? $prodi['kode'] : 'SI';
  $namaFakultas = 'Fakultas Ilmu Komputer';

  $namaKaprodi = !empty($prodi['kaprodi_nama']) ? $prodi['kaprodi_nama'] : 'Dr. Albertus Dwiyoga Widiantoro, S.Kom., M.Kom.';
  $nipKaprodi  = !empty($prodi['kaprodi_nip']) ? $prodi['kaprodi_nip'] : '5812015296';

  $namaDekan = !empty($dekan['nama_lengkap']) ? $dekan['nama_lengkap'] : 'Prof Dr. Ridwan Sanjaya, S.Kom., MS.IEC';
  $nipDekan  = !empty($dekan['username']) ? $dekan['username'] : '5812002255';

  $namaPenyusun = !empty($userCurr['nama_lengkap']) ? $userCurr['nama_lengkap'] : 'Agus Cahyo Nugroho, S.Kom., M.T.';
  $nipPenyusun  = !empty($userCurr['username']) ? $userCurr['username'] : '5812019362';

  $taClean     = preg_replace('/[^a-zA-Z0-9]/', '', $proj['ta_nama'] ?? '20242025');
  $kodeDokumen = 'PPEPP-' . strtoupper($kodeProdi) . '-' . $taClean;
  ?>

  <!-- Floating Toolbar -->
  <div class="no-print">
    <a href="<?= BASE_URL ?>/laporan/cetak?project_id=<?= (int)($proj['id'] ?? 0) ?>&format=pdf" class="btn-action btn-pdf">
      📄 Buka Versi Resmi TCPDF (Digital Sign Barcode) ↗
    </a>
    <button type="button" onclick="window.print()" class="btn-action btn-print">
      🖨️ Cetak Dokumen / Simpan PDF
    </button>
    <button type="button" onclick="window.close()" class="btn-action btn-close">
      ✕ Tutup
    </button>
  </div>

  <!-- ============================================================ -->
  <!-- HALAMAN 1: IDENTITAS DOKUMEN & PENGESAHAN RESMI             -->
  <!-- ============================================================ -->
  <table class="header-box" cellpadding="4">
    <tr>
      <td width="12%" align="center" rowspan="2">
        <img src="<?= BASE_URL ?>/img/logo_univ.jpg" alt="Logo" style="width:65px; height:auto;" onerror="this.src='<?= BASE_URL ?>/img/logo_univ.png'">
      </td>
      <td width="73%" align="center">
        <h2 style="margin:0; font-size:13pt; font-weight:bold;">UNIVERSITAS KATOLIK SOEGIJAPRANATA</h2>
        <h3 style="margin:2px 0 0 0; font-size:10.5pt; font-weight:bold;"><?= strtoupper(htmlspecialchars($namaFakultas)) ?></h3>
        <h4 style="margin:1px 0 0 0; font-size:9pt;">PROGRAM STUDI <?= strtoupper(htmlspecialchars($namaProdi)) ?> (<?= strtoupper(htmlspecialchars($jenjangProdi)) ?>)</h4>
      </td>
      <td width="15%" align="center" class="bg-header">
        <span style="font-size:7.5pt;">KODE DOKUMEN</span><br>
        <span style="font-size:9.5pt; font-weight:bold;"><?= htmlspecialchars($kodeDokumen) ?></span>
      </td>
    </tr>
    <tr>
      <td colspan="2" align="center" class="bg-header">
        <h3 style="margin:2px 0; font-size:11pt; font-weight:bold;">LAPORAN SIKLUS PENJAMINAN MUTU INTERNAL (SPMI) PPEPP</h3>
      </td>
    </tr>
    <tr class="bg-header" style="font-size:7.5pt;">
      <td width="35%">PROYEK SIKLUS PPEPP</td>
      <td width="30%">DOKUMEN ACUAN PENETAPAN</td>
      <td width="15%">TAHUN AKADEMIK</td>
      <td width="10%">SEMESTER</td>
      <td width="10%">STATUS</td>
    </tr>
    <tr align="center" style="font-size:8pt;">
      <td align="left"><b><?= htmlspecialchars($proj['judul'] ?? 'Proyek PPEPP') ?></b></td>
      <td align="left"><?= htmlspecialchars($pen['judul'] ?? '—') ?></td>
      <td><?= htmlspecialchars($proj['ta_nama'] ?? '—') ?></td>
      <td><?= ucfirst(htmlspecialchars($proj['semester'] ?? '—')) ?></td>
      <td><b><?= strtoupper(htmlspecialchars($proj['status'] ?? 'Aktif')) ?></b></td>
    </tr>
    <tr>
      <td colspan="5" style="padding:0;">
        <table cellpadding="6" style="width:100%; border:none; text-align:center;">
          <tr class="bg-header" style="font-size:7.5pt;">
            <td width="33%" style="border-right:1px solid #000;">Ketua Gugus Penjaminan Mutu (GPM)</td>
            <td width="34%" style="border-right:1px solid #000;">Ketua Program Studi</td>
            <td width="33%">Dekan Fakultas</td>
          </tr>
          <tr>
            <td style="border-right:1px solid #000; vertical-align:middle; padding-top:6px;">
              <div class="qr-box">[ QR-CODE DIGITAL SIGN ]</div>
              <u><b><?= htmlspecialchars($namaPenyusun) ?></b></u><br>
              <span style="font-size:7pt;">NIP. <?= htmlspecialchars($nipPenyusun) ?></span>
            </td>
            <td style="border-right:1px solid #000; vertical-align:middle; padding-top:6px;">
              <div class="qr-box">[ QR-CODE DIGITAL SIGN ]</div>
              <u><b><?= htmlspecialchars($namaKaprodi) ?></b></u><br>
              <span style="font-size:7pt;">NIP. <?= htmlspecialchars($nipKaprodi) ?></span>
            </td>
            <td style="vertical-align:middle; padding-top:6px;">
              <div class="qr-box">[ QR-CODE DIGITAL SIGN ]</div>
              <u><b><?= htmlspecialchars($namaDekan) ?></b></u><br>
              <span style="font-size:7pt;">NIP. <?= htmlspecialchars($nipDekan) ?></span>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- Ringkasan Eksekutif -->
  <div class="chapter-header">RINGKASAN EKSEKUTIF KETERCAPAIAN SIKLUS MUTU PPEPP</div>
  <div class="chapter-desc">Rekapitulasi komprehensif tingkat ketercapaian 5 tahap penjaminan mutu pada periode tahun akademik ini.</div>

  <table class="table-bab" cellpadding="5" style="text-align:center;">
    <tr class="bg-header">
      <td width="16%" rowspan="2" style="vertical-align:middle;">1. PENETAPAN (P)</td>
      <td width="26%" colspan="3">2. PELAKSANAAN (P)</td>
      <td width="26%" colspan="3">3. EVALUASI (E)</td>
      <td width="18%" colspan="2">4. PENGENDALIAN (P)</td>
      <td width="14%" rowspan="2" style="vertical-align:middle;">5. PENINGKATAN (P)</td>
    </tr>
    <tr class="bg-header" style="font-size:7.5pt;">
      <td width="9%">Terlaksana</td>
      <td width="8%">Proses</td>
      <td width="9%">Belum</td>
      <td width="9%">Tercapai</td>
      <td width="9%">Sebagian</td>
      <td width="8%">Belum</td>
      <td width="9%">Perlu RTL</td>
      <td width="9%">Selesai</td>
    </tr>
    <tr style="font-size:8.5pt;">
      <td><b><?= $stats['total_standar'] ?? count($matrix) ?></b> Standar<br><span style="font-size:7pt;color:#555;">Ditetapkan 100%</span></td>
      <td><b><?= $stats['pelaksanaan']['terlaksana'] ?? 0 ?></b><br><span style="font-size:7pt;color:#555;">(<?= $stats['pct_pelaksanaan'] ?? 0 ?>%)</span></td>
      <td><b><?= $stats['pelaksanaan']['proses'] ?? 0 ?></b></td>
      <td><b><?= $stats['pelaksanaan']['belum'] ?? 0 ?></b></td>
      <td><b><?= $stats['evaluasi']['tercapai'] ?? 0 ?></b><br><span style="font-size:7pt;color:#555;">(<?= $stats['pct_evaluasi'] ?? 0 ?>%)</span></td>
      <td><b><?= $stats['evaluasi']['sebagian'] ?? 0 ?></b></td>
      <td><b><?= $stats['evaluasi']['belum_tercapai'] ?? 0 ?></b></td>
      <td><b><?= $stats['pengendalian']['total_rtl'] ?? 0 ?></b> Standar</td>
      <td><b><?= $stats['pengendalian']['selesai'] ?? 0 ?></b><br><span style="font-size:7pt;color:#555;">(<?= $stats['pct_pengendalian'] ?? 100 ?>%)</span></td>
      <td><b><?= $stats['peningkatan']['ditingkatkan'] ?? 0 ?></b> Standar<br><span style="font-size:7pt;color:#555;">Target Naik</span></td>
    </tr>
  </table>

  <!-- Sistematika -->
  <table class="table-bab" cellpadding="4" style="font-size:8pt;">
    <tr class="bg-header"><td colspan="2" align="left"><b>SISTEMATIKA LAPORAN SIKLUS PPEPP</b></td></tr>
    <tr><td width="20%"><b>BAB I. TAHAP PENETAPAN</b></td><td width="80%">Pernyataan standar target capaian, indikator ketercapaian, dan dasar regulasi acuan mutu.</td></tr>
    <tr><td><b>BAB II. TAHAP PELAKSANAAN</b></td><td>Pencatatan realisasi kegiatan, status keterlaksanaan, capaian angka kuantitatif, dan tautan bukti.</td></tr>
    <tr><td><b>BAB III. TAHAP EVALUASI</b></td><td>Audit capaian aktual terhadap target standar, analisis ketercapaian, dan analisis gap mutu.</td></tr>
    <tr><td><b>BAB IV. TAHAP PENGENDALIAN</b></td><td>Analisis akar masalah dan Rencana Tindak Lanjut (RTL) koreksi standar beserta PIC & target waktu.</td></tr>
    <tr><td><b>BAB V. TAHAP PENINGKATAN</b></td><td>Peningkatan standar mutu baru (continuous improvement) bagi standar yang telah tercapai melampaui target.</td></tr>
  </table>

  <!-- ============================================================ -->
  <!-- BAB I: TAHAP PENETAPAN STANDAR MUTU (P)                      -->
  <!-- ============================================================ -->
  <div class="page-break"></div>
  <div class="chapter-header">BAB I. TAHAP PENETAPAN STANDAR MUTU (P)</div>
  <div class="chapter-desc">Memuat rumusan standar mutu SPMI, pernyataan target capaian, indikator ketercapaian mutu, serta dasar regulasi yang menjadi acuan pelaksanaan standar pada program studi.</div>

  <table class="table-bab" cellpadding="4">
    <thead>
      <tr class="bg-header">
        <th width="4%">NO</th>
        <th width="10%">KODE</th>
        <th width="16%">KRITERIA STANDAR</th>
        <th width="35%">PERNYATAAN STANDAR (TARGET CAPAIAN)</th>
        <th width="23%">INDIKATOR KETERCAPAIAN MUTU</th>
        <th width="12%">DASAR REGULASI</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($matrix as $i => $m): ?>
      <?php $isTurunan = !empty($m['is_turunan']); ?>
      <tr>
        <td align="center"><b><?= $i + 1 ?></b></td>
        <td>
          <b><?= htmlspecialchars($m['kriteria_kode']) ?></b>
          <?php if ($isTurunan): ?>
          <br><span style="font-size:6.5pt; color:#1d4ed8; font-weight:bold;">[Standar Turunan]</span>
          <?php endif; ?>
        </td>
        <td><b><?= htmlspecialchars($m['kriteria_nama']) ?></b></td>
        <td><?= nl2br(htmlspecialchars($m['p1_target'] ?: '—')) ?></td>
        <td><?= nl2br(htmlspecialchars($m['p1_indikator'] ?: '—')) ?></td>
        <td><?= htmlspecialchars(!empty($m['p1_aturan']) && $m['p1_aturan'] !== '—' ? $m['p1_aturan'] : '—') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- ============================================================ -->
  <!-- BAB II: TAHAP PELAKSANAAN STANDAR MUTU (P)                   -->
  <!-- ============================================================ -->
  <div class="page-break"></div>
  <div class="chapter-header">BAB II. TAHAP PELAKSANAAN STANDAR MUTU (P)</div>
  <div class="chapter-desc">Memuat status keterlaksanaan standar mutu SPMI yang dijalankan, isian realisasi angka kuantitatif (khusus standar turunan), serta catatan realisasi dan bukti fisik keterlaksanaan.</div>

  <table class="table-bab" cellpadding="4">
    <thead>
      <tr class="bg-header">
        <th width="4%">NO</th>
        <th width="10%">KODE</th>
        <th width="16%">KRITERIA STANDAR</th>
        <th width="28%">ACUAN PERNYATAAN STANDAR</th>
        <th width="14%">STATUS PELAKSANAAN</th>
        <th width="12%">REALISASI ANGKA</th>
        <th width="16%">CATATAN REALISASI &amp; BUKTI</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($matrix as $i => $m): ?>
      <?php
      $stP2         = $m['p2_status'] ?? 'belum';
      $isTurunan    = !empty($m['is_turunan']);
      $capaianAngka = trim($m['p2_capaian_angka'] ?? '');
      $statusText   = ($stP2 === 'terlaksana') ? '[TERLAKSANA]' : (($stP2 === 'proses') ? '[SEDANG PROSES]' : '[BELUM DILAKSANAKAN]');
      ?>
      <tr>
        <td align="center"><b><?= $i + 1 ?></b></td>
        <td>
          <b><?= htmlspecialchars($m['kriteria_kode']) ?></b>
          <?php if ($isTurunan): ?>
          <br><span style="font-size:6.5pt; color:#1d4ed8; font-weight:bold;">[Turunan]</span>
          <?php endif; ?>
        </td>
        <td><b><?= htmlspecialchars($m['kriteria_nama']) ?></b></td>
        <td><?= nl2br(htmlspecialchars($m['p1_target'] ?: '—')) ?></td>
        <td align="center"><b><?= $statusText ?></b></td>
        <td align="center">
          <?php if ($isTurunan): ?>
            <?= !empty($capaianAngka) ? '<b>' . htmlspecialchars($capaianAngka) . '</b>' : '<i>(Belum diisi)</i>' ?>
          <?php else: ?>
            <span style="color:#777;">— (Kualitatif)</span>
          <?php endif; ?>
        </td>
        <td><?= nl2br(htmlspecialchars($m['p2_catatan'] ?: '— Belum ada catatan')) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- ============================================================ -->
  <!-- BAB III: TAHAP EVALUASI PELAKSANAAN STANDAR MUTU (E)         -->
  <!-- ============================================================ -->
  <div class="page-break"></div>
  <div class="chapter-header">BAB III. TAHAP EVALUASI PELAKSANAAN STANDAR MUTU (E)</div>
  <div class="chapter-desc">Memuat hasil audit ketercapaian standar mutu terhadap target yang ditetapkan, hasil aktual pelaksanaan, status ketercapaian (Tercapai / Sebagian / Belum), dan analisis gap ketercapaian mutu.</div>

  <table class="table-bab" cellpadding="4">
    <thead>
      <tr class="bg-header">
        <th width="4%">NO</th>
        <th width="10%">KODE</th>
        <th width="16%">KRITERIA STANDAR</th>
        <th width="24%">TARGET STANDAR ACUAN</th>
        <th width="14%">STATUS CAPAIAN</th>
        <th width="18%">HASIL AKTUAL PELAKSANAAN</th>
        <th width="14%">ANALISIS EVALUASI &amp; GAP</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($matrix as $i => $m): ?>
      <?php
      $isTercapai = ($m['e_status'] === 'tercapai');
      $isSebagian = ($m['e_status'] === 'sebagian');
      $statusText = $isTercapai ? '[TERCAPAI PENUH]' : ($isSebagian ? '[SEBAGIAN TERCAPAI]' : '[BELUM TERCAPAI]');
      ?>
      <tr>
        <td align="center"><b><?= $i + 1 ?></b></td>
        <td><b><?= htmlspecialchars($m['kriteria_kode']) ?></b></td>
        <td><b><?= htmlspecialchars($m['kriteria_nama']) ?></b></td>
        <td><?= nl2br(htmlspecialchars($m['p1_target'] ?: '—')) ?></td>
        <td align="center"><b><?= $statusText ?></b></td>
        <td><?= nl2br(htmlspecialchars($m['e_hasil'] ?: '— Belum diisi')) ?></td>
        <td>
          <?= nl2br(htmlspecialchars($m['e_evaluasi'] ?: '—')) ?>
          <?php if (!empty($m['e_gap'])): ?>
          <br><span style="color:#555;"><i>Gap: <?= htmlspecialchars($m['e_gap']) ?></i></span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- ============================================================ -->
  <!-- BAB IV: TAHAP PENGENDALIAN STANDAR MUTU (P)                  -->
  <!-- ============================================================ -->
  <div class="page-break"></div>
  <div class="chapter-header">BAB IV. TAHAP PENGENDALIAN STANDAR MUTU (P)</div>
  <div class="chapter-desc">Memuat perumusan tindakan koreksi dan Rencana Tindak Lanjut (RTL) bagi standar yang belum/sebagian tercapai pada tahap Evaluasi, identifikasi akar masalah (Fishbone/5-Whys), serta penugasan PIC dan target waktu penyelesaian.</div>

  <table class="table-bab" cellpadding="4">
    <thead>
      <tr class="bg-header">
        <th width="4%">NO</th>
        <th width="10%">KODE</th>
        <th width="15%">KRITERIA STANDAR</th>
        <th width="12%">STATUS EVALUASI</th>
        <th width="18%">IDENTIFIKASI AKAR MASALAH</th>
        <th width="23%">RENCANA TINDAK LANJUT (RTL) / KOREKSI</th>
        <th width="11%">PENANGGUNG JAWAB (PIC)</th>
        <th width="7%">TARGET WAKTU</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($matrix as $i => $m): ?>
      <?php
      $isTercapai = ($m['e_status'] === 'tercapai');
      $statusEvalText = $isTercapai ? '[TERCAPAI]' : '[BELUM TERCAPAI]';
      ?>
      <tr>
        <td align="center"><b><?= $i + 1 ?></b></td>
        <td><b><?= htmlspecialchars($m['kriteria_kode']) ?></b></td>
        <td><b><?= htmlspecialchars($m['kriteria_nama']) ?></b></td>
        <td align="center"><b><?= $statusEvalText ?></b></td>
        <?php if ($isTercapai): ?>
        <td colspan="4" style="font-style:italic; color:#444; background-color:#fafafa;">
          Standar telah terpenuhi pada tahap Evaluasi. Tidak memerlukan tindakan koreksi (RTL).
        </td>
        <?php else: ?>
        <td><?= nl2br(htmlspecialchars($m['p4_akar_masalah'] ?: '— Belum dianalisis')) ?></td>
        <td>
          <b>RTL:</b> <?= nl2br(htmlspecialchars($m['p4_rtl'] ?: 'Sedang dirumuskan')) ?>
          <?php if (!empty($m['p4_koreksi_standar'])): ?>
          <br><span style="color:#444;"><i>Koreksi: <?= htmlspecialchars($m['p4_koreksi_standar']) ?></i></span>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($m['p4_pic'] ?: '—') ?></td>
        <td align="center"><?= htmlspecialchars($m['p4_waktu'] ?: '—') ?></td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- ============================================================ -->
  <!-- BAB V: TAHAP PENINGKATAN STANDAR MUTU (P)                    -->
  <!-- ============================================================ -->
  <div class="page-break"></div>
  <div class="chapter-header">BAB V. TAHAP PENINGKATAN STANDAR MUTU (P)</div>
  <div class="chapter-desc">Memuat peningkatan standar mutu (continuous quality improvement / Kaizen) bagi standar yang telah tercapai melampaui target, rumusan target standar baru, indikator baru, serta strategi peningkatan untuk siklus SPMI berikutnya.</div>

  <table class="table-bab" cellpadding="4">
    <thead>
      <tr class="bg-header">
        <th width="4%">NO</th>
        <th width="10%">KODE</th>
        <th width="15%">KRITERIA STANDAR</th>
        <th width="20%">TARGET STANDAR AWAL</th>
        <th width="12%">STATUS TINDAKAN</th>
        <th width="22%">TARGET STANDAR BARU (PENINGKATAN)</th>
        <th width="17%">INDIKATOR BARU &amp; KENAIKAN MUTU</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($matrix as $i => $m): ?>
      <?php $isTercapai = ($m['e_status'] === 'tercapai'); ?>
      <tr>
        <td align="center"><b><?= $i + 1 ?></b></td>
        <td><b><?= htmlspecialchars($m['kriteria_kode']) ?></b></td>
        <td><b><?= htmlspecialchars($m['kriteria_nama']) ?></b></td>
        <td><?= nl2br(htmlspecialchars($m['p1_target'] ?: '—')) ?></td>
        <?php if (!$isTercapai): ?>
        <td align="center"><span style="color:#555;">[DIPERTAHANKAN]</span></td>
        <td colspan="2" style="font-style:italic; color:#555; background-color:#fafafa;">
          Standar belum tercapai penuh, target mutu awal dipertahankan untuk siklus perbaikan berikutnya.
        </td>
        <?php else: ?>
        <td align="center"><span style="font-weight:bold; color:#000;">[DITINGKATKAN]</span></td>
        <td>
          <b><?= nl2br(htmlspecialchars($m['p5_target_baru'] ?: $m['p1_target'])) ?></b>
          <?php if (!empty($m['p5_strategi_baru'])): ?>
          <br><span style="color:#333;"><i>Strategi: <?= htmlspecialchars($m['p5_strategi_baru']) ?></i></span>
          <?php endif; ?>
        </td>
        <td>
          <?= nl2br(htmlspecialchars($m['p5_indikator_baru'] ?: '—')) ?>
          <?php if (!empty($m['p5_nilai_kenaikan'])): ?>
          <br><b>Kenaikan:</b> <?= htmlspecialchars($m['p5_nilai_kenaikan']) ?>
          <?php endif; ?>
        </td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div style="font-size:7pt; color:#444; font-style:italic; border-top:1px solid #000; padding-top:4px;">
    * Dokumen resmi Laporan Siklus Penjaminan Mutu Internal (SPMI) PPEPP ini disusun dan disahkan secara kelembagaan oleh Universitas Katolik Soegijapranata. Verifikasi digital keabsahan dokumen dapat dilakukan melalui pemindaian QR-Code pada lembar pengesahan resmi.
  </div>

</body>
</html>
