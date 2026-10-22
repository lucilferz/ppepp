<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../app/models/Laporan.php';
require_once __DIR__ . '/../vendor/tcpdf/tcpdf.php';

$laporanModel = new Laporan();
$db = (new Model())->getDb();
$p = $db->query("SELECT id, user_id FROM ppepp_project LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$reportData = $laporanModel->getProjectReport((int)$p['id'], (int)$p['user_id']);

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

$taClean = preg_replace('/[^a-zA-Z0-9]/', '', $proj['ta_nama'] ?? '20242025');
$kodeDokumen = 'PPEPP-' . strtoupper($kodeProdi) . '-' . $taClean;

// Initialize TCPDF
$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator('SPMI PPEPP System');
$pdf->SetAuthor('Universitas Katolik Soegijapranata');
$pdf->SetTitle('Laporan Rekapitulasi PPEPP - ' . ($proj['judul'] ?? 'PPEPP'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->SetFont('helvetica', '', 8);
$pdf->AddPage();

$logoFile = 'C:/laragon/www/ppepp/public/img/logo_univ.jpg';

// Generate Verification Barcode Tags
$verifyBase = 'https://unika.ac.id/verify_ppepp?doc=' . urlencode($kodeDokumen);
$qrTagGpm = $pdf->serializeTCPDFtag('write2DBarcode', [
    $verifyBase . '&signer=gpm&nip=' . urlencode($nipPenyusun),
    'QRCODE,M', '', '', 22, 22,
    ['border' => 0, 'padding' => 0, 'fgcolor' => [0, 0, 0], 'bgcolor' => false],
    'N'
]);
$qrTagKaprodi = $pdf->serializeTCPDFtag('write2DBarcode', [
    $verifyBase . '&signer=kaprodi&nip=' . urlencode($nipKaprodi),
    'QRCODE,M', '', '', 22, 22,
    ['border' => 0, 'padding' => 0, 'fgcolor' => [0, 0, 0], 'bgcolor' => false],
    'N'
]);
$qrTagDekan = $pdf->serializeTCPDFtag('write2DBarcode', [
    $verifyBase . '&signer=dekan&nip=' . urlencode($nipDekan),
    'QRCODE,M', '', '', 22, 22,
    ['border' => 0, 'padding' => 0, 'fgcolor' => [0, 0, 0], 'bgcolor' => false],
    'N'
]);

// Build HTML content
$html = '
<style>
  table.header-box {
    border: 1px solid #000;
    width: 100%;
    border-collapse: collapse;
  }
  table.header-box td, table.header-box th {
    border: 1px solid #000;
    padding: 3px 5px;
  }
  .bg-header {
    background-color: #E6E6E6;
    font-weight: bold;
    text-align: center;
  }
  table.table-rekap {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #000;
  }
  table.table-rekap th, table.table-rekap td {
    border: 1px solid #000;
    padding: 4px;
    vertical-align: top;
  }
  .section-title {
    font-size: 9.5pt;
    font-weight: bold;
    margin-top: 8px;
    margin-bottom: 4px;
    text-transform: uppercase;
  }
</style>

<!-- HEADER BLOCK KOP RESMI SEPERTI REFERENSI RPS -->
<table class="header-box" cellpadding="3">
  <tr>
    <td width="12%" align="center" rowspan="2" style="vertical-align:middle;">
      <img src="' . $logoFile . '" width="60" />
    </td>
    <td width="73%" align="center" style="vertical-align:middle;">
      <h2 style="margin:0; font-size:13pt; font-weight:bold;">UNIVERSITAS KATOLIK SOEGIJAPRANATA</h2>
      <h3 style="margin:2px 0 0 0; font-size:10.5pt; font-weight:bold;">' . strtoupper(htmlspecialchars($namaFakultas)) . '</h3>
      <h4 style="margin:1px 0 0 0; font-size:9pt;">PROGRAM STUDI ' . strtoupper(htmlspecialchars($namaProdi)) . ' (' . strtoupper(htmlspecialchars($jenjangProdi)) . ')</h4>
    </td>
    <td width="15%" align="center" class="bg-header" style="vertical-align:middle;">
      <span style="font-size:7.5pt;">KODE DOKUMEN</span><br>
      <span style="font-size:9.5pt; font-weight:bold;">' . htmlspecialchars($kodeDokumen) . '</span>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center" class="bg-header" style="vertical-align:middle;">
      <h3 style="margin:2px 0; font-size:11pt; font-weight:bold;">REKAPITULASI LAPORAN SIKLUS PENJAMINAN MUTU (PPEPP)</h3>
    </td>
  </tr>
  <tr class="bg-header" style="font-size:7.5pt;">
    <td width="38%">PROYEK SIKLUS PPEPP</td>
    <td width="27%">DOKUMEN ACUAN PENETAPAN</td>
    <td width="15%">TAHUN AKADEMIK</td>
    <td width="10%">SEMESTER</td>
    <td width="10%">STATUS</td>
  </tr>
  <tr align="center" style="font-size:8pt;">
    <td align="left"><b>' . htmlspecialchars($proj['judul'] ?? 'Proyek PPEPP') . '</b></td>
    <td align="left">' . htmlspecialchars($pen['judul'] ?? '—') . '</td>
    <td>' . htmlspecialchars($proj['ta_nama'] ?? '—') . '</td>
    <td>' . ucfirst(htmlspecialchars($proj['semester'] ?? '—')) . '</td>
    <td><b>' . strtoupper(htmlspecialchars($proj['status'] ?? 'Aktif')) . '</b></td>
  </tr>
  <tr>
    <td colspan="5" style="padding:0;">
      <table cellpadding="4" style="width:100%; border:none; text-align:center;">
        <tr class="bg-header" style="font-size:7.5pt;">
          <td width="33%" style="border-right:1px solid #000;">Ketua Gugus Penjaminan Mutu (GPM)</td>
          <td width="34%" style="border-right:1px solid #000;">Ketua Program Studi</td>
          <td width="33%">Dekan Fakultas</td>
        </tr>
        <tr>
          <td style="border-right:1px solid #000; vertical-align:middle; padding-top:4px;">
            <tcpdf data="' . $qrTagGpm . '" /><br>
            <u><b>' . htmlspecialchars($namaPenyusun) . '</b></u><br>
            <span style="font-size:7pt;">NIP. ' . htmlspecialchars($nipPenyusun) . '</span>
          </td>
          <td style="border-right:1px solid #000; vertical-align:middle; padding-top:4px;">
            <tcpdf data="' . $qrTagKaprodi . '" /><br>
            <u><b>' . htmlspecialchars($namaKaprodi) . '</b></u><br>
            <span style="font-size:7pt;">NIP. ' . htmlspecialchars($nipKaprodi) . '</span>
          </td>
          <td style="vertical-align:middle; padding-top:4px;">
            <tcpdf data="' . $qrTagDekan . '" /><br>
            <u><b>' . htmlspecialchars($namaDekan) . '</b></u><br>
            <span style="font-size:7pt;">NIP. ' . htmlspecialchars($nipDekan) . '</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<div class="section-title">I. RINGKASAN EKSEKUTIF CAPAIAN SIKLUS MUTU PPEPP</div>
<table class="table-rekap" cellpadding="3" style="text-align:center; font-size:7.5pt;">
  <tr class="bg-header">
    <td width="14%" rowspan="2" style="vertical-align:middle;">1. PENETAPAN (P)</td>
    <td width="26%" colspan="3">2. PELAKSANAAN (P)</td>
    <td width="26%" colspan="3">3. EVALUASI (E)</td>
    <td width="18%" colspan="2">4. PENGENDALIAN (P)</td>
    <td width="16%" rowspan="2" style="vertical-align:middle;">5. PENINGKATAN (P)</td>
  </tr>
  <tr class="bg-header" style="font-size:7pt;">
    <td width="9%">Terlaksana</td>
    <td width="8%">Proses</td>
    <td width="9%">Belum</td>
    <td width="9%">Tercapai</td>
    <td width="9%">Sebagian</td>
    <td width="8%">Belum</td>
    <td width="9%">Perlu RTL</td>
    <td width="9%">Selesai</td>
  </tr>
  <tr style="font-size:8pt;">
    <td><b>' . ($stats['total_standar'] ?? count($matrix)) . '</b> Standar<br><span style="font-size:6.5pt;">Ditetapkan 100%</span></td>
    <td><b>' . ($stats['pelaksanaan']['terlaksana'] ?? 0) . '</b> (' . ($stats['pct_pelaksanaan'] ?? 0) . '%)</td>
    <td><b>' . ($stats['pelaksanaan']['proses'] ?? 0) . '</b></td>
    <td><b>' . ($stats['pelaksanaan']['belum'] ?? 0) . '</b></td>
    <td><b>' . ($stats['evaluasi']['tercapai'] ?? 0) . '</b> (' . ($stats['pct_evaluasi'] ?? 0) . '%)</td>
    <td><b>' . ($stats['evaluasi']['sebagian'] ?? 0) . '</b></td>
    <td><b>' . ($stats['evaluasi']['belum_tercapai'] ?? 0) . '</b></td>
    <td><b>' . ($stats['pengendalian']['total_rtl'] ?? 0) . '</b> Standar</td>
    <td><b>' . ($stats['pengendalian']['selesai'] ?? 0) . '</b> (' . ($stats['pct_pengendalian'] ?? 100) . '%)</td>
    <td><b>' . ($stats['peningkatan']['ditingkatkan'] ?? 0) . '</b> Standar<br><span style="font-size:6.5pt;">Target Naik</span></td>
  </tr>
</table>

<div class="section-title">II. MATRIKS REKAPITULASI SIKLUS MUTU PPEPP (PENETAPAN S.D. PENINGKATAN)</div>
<table class="table-rekap" cellpadding="3" style="font-size:7pt;">
  <thead>
    <tr class="bg-header" style="font-size:7.5pt;">
      <th width="3%" align="center">NO</th>
      <th width="12%">KODE &amp; KRITERIA</th>
      <th width="21%">1. PENETAPAN (P)<br><span style="font-size:6.5pt;font-weight:normal;">Target &amp; Indikator Mutu</span></th>
      <th width="16%">2. PELAKSANAAN (P)<br><span style="font-size:6.5pt;font-weight:normal;">Realisasi &amp; Catatan Bukti</span></th>
      <th width="16%">3. EVALUASI (E)<br><span style="font-size:6.5pt;font-weight:normal;">Ketercapaian &amp; Analisis</span></th>
      <th width="16%">4. PENGENDALIAN (P)<br><span style="font-size:6.5pt;font-weight:normal;">Akar Masalah &amp; RTL</span></th>
      <th width="16%">5. PENINGKATAN (P)<br><span style="font-size:6.5pt;font-weight:normal;">Target &amp; Indikator Baru</span></th>
    </tr>
  </thead>
  <tbody>';

foreach ($matrix as $i => $m) {
    $isTercapai = ($m['e_status'] === 'tercapai');
    $isSebagian = ($m['e_status'] === 'sebagian');
    $stP2       = $m['p2_status'] ?? 'belum';
    $isTurunan  = !empty($m['is_turunan']);
    $capaianAngka = trim($m['p2_capaian_angka'] ?? '');

    $turunanBadge = $isTurunan ? ' <span style="font-size:6pt; background-color:#eee; border:0.5px solid #666; padding:1px 2px;">[Turunan]</span>' : '';

    $statusP2Text = ($stP2 === 'terlaksana') ? '[✓ TERLAKSANA]' : (($stP2 === 'proses') ? '[⏳ PROSES]' : '[✕ BELUM]');
    $statusEText  = $isTercapai ? '[✓ TERCAPAI]' : ($isSebagian ? '[⚠️ SEBAGIAN]' : '[✕ BELUM TERCAPAI]');

    $html .= '<tr nobr="true">';
    $html .= '<td width="3%" align="center"><b>' . ($i + 1) . '</b></td>';
    $html .= '<td width="12%"><b>' . htmlspecialchars($m['kriteria_kode']) . '</b>' . $turunanBadge . '<br><b>' . htmlspecialchars($m['kriteria_nama']) . '</b></td>';
    
    // Penetapan
    $html .= '<td width="21%">';
    $html .= '<b>Target:</b> ' . nl2br(htmlspecialchars($m['p1_target'] ?: '—')) . '<br>';
    $html .= '<b>Indikator:</b> ' . nl2br(htmlspecialchars($m['p1_indikator'] ?: '—'));
    if (!empty($m['p1_aturan']) && $m['p1_aturan'] !== '—') {
        $html .= '<br><span style="color:#444;"><i>Dasar: ' . htmlspecialchars($m['p1_aturan']) . '</i></span>';
    }
    $html .= '</td>';

    // Pelaksanaan
    $html .= '<td width="16%">';
    $html .= '<b>' . $statusP2Text . '</b><br>';
    if ($isTurunan && $capaianAngka !== '') {
        $html .= '<span style="background-color:#f4f4f4; border:0.5px solid #999;"><b>Realisasi: ' . htmlspecialchars($capaianAngka) . '</b></span><br>';
    }
    $html .= htmlspecialchars($m['p2_catatan'] ?: '— Belum ada catatan realisasi');
    $html .= '</td>';

    // Evaluasi
    $html .= '<td width="16%">';
    $html .= '<b>' . $statusEText . '</b><br>';
    if (!empty($m['e_hasil'])) {
        $html .= '<b>Hasil:</b> ' . nl2br(htmlspecialchars($m['e_hasil'])) . '<br>';
    }
    $html .= nl2br(htmlspecialchars($m['e_evaluasi'] ?: '—'));
    if (!empty($m['e_gap'])) {
        $html .= '<br><span style="color:#555;"><i>Gap: ' . htmlspecialchars($m['e_gap']) . '</i></span>';
    }
    $html .= '</td>';

    // Pengendalian
    $html .= '<td width="16%">';
    if ($isTercapai) {
        $html .= '<i>✓ Standar terpenuhi. Tidak memerlukan tindakan koreksi (RTL).</i>';
    } else {
        if (!empty($m['p4_akar_masalah'])) {
            $html .= '<b>Akar Masalah:</b> ' . nl2br(htmlspecialchars($m['p4_akar_masalah'])) . '<br>';
        }
        $html .= '<b>RTL:</b> ' . nl2br(htmlspecialchars($m['p4_rtl'] ?: 'Sedang dirumuskan')) . '<br>';
        $html .= '<span style="font-size:6.5pt; color:#333;"><b>PIC:</b> ' . htmlspecialchars($m['p4_pic'] ?: '—') . ' | <b>Target:</b> ' . htmlspecialchars($m['p4_waktu'] ?: '—') . '</span>';
    }
    $html .= '</td>';

    // Peningkatan
    $html .= '<td width="16%">';
    if (!$isTercapai) {
        $html .= '<i>Standar belum tercapai, target awal dipertahankan untuk siklus perbaikan berikutnya.</i>';
    } else {
        $html .= '<b>Target Baru:</b> ' . nl2br(htmlspecialchars($m['p5_target_baru'] ?: $m['p1_target'])) . '<br>';
        if (!empty($m['p5_indikator_baru'])) {
            $html .= '<b>Indikator Baru:</b> ' . nl2br(htmlspecialchars($m['p5_indikator_baru'])) . '<br>';
        }
        if (!empty($m['p5_strategi_baru'])) {
            $html .= '<span style="color:#333;"><i>Strategi: ' . htmlspecialchars($m['p5_strategi_baru']) . '</i></span><br>';
        }
        if (!empty($m['p5_nilai_kenaikan'])) {
            $html .= '<b>Kenaikan:</b> ' . htmlspecialchars($m['p5_nilai_kenaikan']);
        }
    }
    $html .= '</td>';

    $html .= '</tr>';
}

$html .= '
  </tbody>
</table>

<div style="font-size:7pt; color:#444; margin-top:8px; font-style:italic;">
  * Dokumen rekapitulasi penjaminan mutu ini dihasilkan secara resmi melalui Platform Penjaminan Mutu Internal (SPMI) PPEPP Universitas Katolik Soegijapranata. Keabsahan dokumen dapat diverifikasi secara digital melalui pemindaian QR-Code pada lembar pengesahan di atas.
</div>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output(__DIR__ . '/test_rekap_ppepp.pdf', 'F');
echo "PDF successfully generated! Size: " . filesize(__DIR__ . '/test_rekap_ppepp.pdf') . " bytes\n";
