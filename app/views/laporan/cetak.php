<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Matriks PPEPP — <?= htmlspecialchars($reportData['project']['ta_nama'] ?? '') ?></title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      font-size: 11pt;
      color: #1e293b;
      line-height: 1.4;
      padding: 24px;
      background: #fff;
    }
    .header-doc {
      text-align: center;
      border-bottom: 2.5px solid #0f172a;
      padding-bottom: 14px;
      margin-bottom: 20px;
    }
    .header-doc h1 { font-size: 16pt; font-weight: 900; text-transform: uppercase; color: #0f172a; }
    .header-doc h2 { font-size: 13pt; font-weight: 700; color: #334155; margin-top: 2px; }
    .meta-box {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      background: #f8fafc;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      padding: 12px 16px;
      font-size: 10pt;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 9pt;
      margin-bottom: 24px;
    }
    th, td {
      border: 1px solid #94a3b8;
      padding: 8px 10px;
      vertical-align: top;
    }
    th {
      background: #f1f5f9;
      font-weight: 800;
      color: #0f172a;
      text-align: left;
    }
    .badge-st {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 4px;
      font-weight: 800;
      font-size: 8pt;
    }
    .badge-ok { background: #dcfce7; color: #166534; }
    .badge-warn { background: #ffedd5; color: #9a3412; }
    .print-btn {
      padding: 8px 16px;
      background: #0f172a;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 700;
      font-size: 10pt;
      margin-bottom: 16px;
    }
    @media print {
      .print-btn { display: none; }
      body { padding: 0; }
      @page { size: landscape; margin: 15mm; }
    }
  </style>
</head>
<body>

  <button type="button" onclick="window.print()" class="print-btn">🖨️ Cetak Dokumen / Simpan PDF</button>

  <div class="header-doc">
    <h1>Laporan Matriks Siklus Penjaminan Mutu PPEPP</h1>
    <h2><?= htmlspecialchars($reportData['project']['judul']) ?> — Tahun Ajaran <?= htmlspecialchars($reportData['project']['ta_nama']) ?></h2>
  </div>

  <div class="meta-box">
    <div>
      <strong>Program / Fakultas:</strong> Fakultas / Program Studi<br>
      <strong>Tahun Ajaran:</strong> <?= htmlspecialchars($reportData['project']['ta_nama']) ?> (<?= ucfirst($reportData['project']['semester'] ?? '') ?>)<br>
      <strong>Dokumen Penetapan:</strong> <?= htmlspecialchars($reportData['penetapan']['judul'] ?? '—') ?>
    </div>
    <div>
      <strong>Total Standar:</strong> <?= $reportData['stats']['total_standar'] ?> Kriteria Standar<br>
      <strong>Ketercapaian:</strong> <?= $reportData['stats']['evaluasi']['tercapai'] ?> Terpenuhi (<?= $reportData['stats']['pct_evaluasi'] ?>%) &nbsp;·&nbsp; <?= $reportData['stats']['pengendalian']['total_rtl'] ?> Masuk RTL<br>
      <strong>Tanggal Cetak:</strong> <?= date('d/m/Y H:i') ?>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:3%;">#</th>
        <th style="width:12%;">Kriteria Standar</th>
        <th style="width:18%;">1. Penetapan (Target &amp; Indikator)</th>
        <th style="width:14%;">2. Pelaksanaan</th>
        <th style="width:17%;">3. Evaluasi Capaian</th>
        <th style="width:18%;">4. Pengendalian (RTL)</th>
        <th style="width:18%;">5. Peningkatan (Target Baru)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($reportData['matrix'] as $i => $m): ?>
      <?php $isTercapai = ($m['e_status'] === 'tercapai'); ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td>
          <strong>[<?= htmlspecialchars($m['kriteria_kode']) ?>]</strong><br>
          <?= htmlspecialchars($m['kriteria_nama']) ?>
        </td>
        <td>
          <strong>Pernyataan Standar:</strong> <?= htmlspecialchars($m['p1_target']) ?><br>
          <small style="color:#0369a1;"><strong>Target / Indikator:</strong> <?= htmlspecialchars($m['p1_indikator']) ?></small>
        </td>
        <td>
          <span class="badge-st <?= $m['p2_status'] === 'terlaksana' ? 'badge-ok' : 'badge-warn' ?>">
            <?= ucfirst($m['p2_status']) ?>
          </span><br>
          <small><?= htmlspecialchars($m['p2_catatan'] ?: '—') ?></small>
        </td>
        <td>
          <span class="badge-st <?= $isTercapai ? 'badge-ok' : 'badge-warn' ?>">
            <?= $isTercapai ? 'Tercapai' : 'Belum Tercapai' ?>
          </span><br>
          <small><?= htmlspecialchars($m['e_evaluasi'] ?: '—') ?></small>
        </td>
        <td>
          <?php if ($isTercapai): ?>
          <em style="color:#059669;">Standar terpenuhi</em>
          <?php else: ?>
          <strong>RTL:</strong> <?= htmlspecialchars($m['p4_rtl'] ?: '—') ?><br>
          <small>PIC: <?= htmlspecialchars($m['p4_pic'] ?: '—') ?> (<?= htmlspecialchars($m['p4_waktu'] ?: '—') ?>)</small>
          <?php endif; ?>
        </td>
        <td>
          <?php if (!$isTercapai): ?>
          <em style="color:#64748b;">Dilanjutkan standar sama</em>
          <?php else: ?>
          <strong>Target Baru:</strong> <?= htmlspecialchars($m['p5_target_baru'] ?: $m['p1_target']) ?><br>
          <small>Indikator Baru: <?= htmlspecialchars($m['p5_indikator_baru'] ?: '—') ?></small>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Tanda Tangan Pengesahan -->
  <div style="display:grid;grid-template-columns:1fr 1fr;margin-top:30px;font-size:10pt;">
    <div>
      Mengetahui,<br>
      <strong>Dekan / Pimpinan Fakultas</strong>
      <div style="height:60px;"></div>
      ( ............................................................ )
    </div>
    <div style="text-align:right;">
      Disusun Oleh,<br>
      <strong>Ketua Gugus Penjaminan Mutu (GPM)</strong>
      <div style="height:60px;"></div>
      ( ............................................................ )
    </div>
  </div>

</body>
</html>
