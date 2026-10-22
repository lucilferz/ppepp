<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../app/models/Laporan.php';
require_once __DIR__ . '/../vendor/tcpdf/tcpdf.php';

class PPEPP_Report_PDF extends TCPDF {
    public $docCode = '';
    public $namaProdi = '';
    public $namaFakultas = '';

    public function Header() {
        if ($this->page > 1) {
            $this->SetY(6);
            $this->SetFont('helvetica', '', 7);
            $this->SetTextColor(70, 70, 70);
            $this->Cell(170, 4, 'UNIVERSITAS KATOLIK SOEGIJAPRANATA | ' . strtoupper($this->namaFakultas) . ' - ' . strtoupper($this->namaProdi), 0, 0, 'L');
            $this->Cell(107, 4, 'KODE DOKUMEN: ' . $this->docCode, 0, 1, 'R');
            $this->SetDrawColor(180, 180, 180);
            $this->SetLineWidth(0.3);
            $this->Line(10, 11, 287, 11);
        }
    }

    public function Footer() {
        $this->SetY(-9);
        $this->SetFont('helvetica', '', 7);
        $this->SetTextColor(100, 100, 100);
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.2);
        $this->Line(10, $this->GetY() - 1, 287, $this->GetY() - 1);
        $this->Cell(160, 4, 'Laporan Siklus SPMI PPEPP | Dokumen Terverifikasi Digital Resmi Institusi', 0, 0, 'L');
        $this->Cell(117, 4, 'Halaman ' . $this->getAliasNumPage() . ' dari ' . $this->getAliasNbPages(), 0, 0, 'R');
    }
}

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

$taClean     = preg_replace('/[^a-zA-Z0-9]/', '', $proj['ta_nama'] ?? '20242025');
$kodeDokumen = 'PPEPP-' . strtoupper($kodeProdi) . '-' . $taClean;

// Inisialisasi PDF Kustom
$pdf = new PPEPP_Report_PDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->docCode = $kodeDokumen;
$pdf->namaProdi = $namaProdi;
$pdf->namaFakultas = $namaFakultas;

$pdf->SetCreator('SPMI PPEPP System');
$pdf->SetAuthor('Universitas Katolik Soegijapranata');
$pdf->SetTitle('Laporan Siklus PPEPP - ' . ($proj['judul'] ?? 'PPEPP'));
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
$pdf->SetMargins(10, 14, 10);
$pdf->SetHeaderMargin(6);
$pdf->SetFooterMargin(8);
$pdf->SetAutoPageBreak(true, 12);
$pdf->SetFont('helvetica', '', 7.5);

$logoFile = 'C:/laragon/www/ppepp/public/img/logo_univ.jpg';
$verifyBase = 'https://unika.ac.id/verify_ppepp?doc=' . urlencode($kodeDokumen);

$qrTagGpm = $pdf->serializeTCPDFtag('write2DBarcode', [
    $verifyBase . '&signer=gpm&nip=' . urlencode($nipPenyusun),
    'QRCODE,M', '', '', 20, 20,
    ['border' => 0, 'padding' => 0, 'fgcolor' => [0, 0, 0], 'bgcolor' => false], 'N'
]);
$qrTagKaprodi = $pdf->serializeTCPDFtag('write2DBarcode', [
    $verifyBase . '&signer=kaprodi&nip=' . urlencode($nipKaprodi),
    'QRCODE,M', '', '', 20, 20,
    ['border' => 0, 'padding' => 0, 'fgcolor' => [0, 0, 0], 'bgcolor' => false], 'N'
]);
$qrTagDekan = $pdf->serializeTCPDFtag('write2DBarcode', [
    $verifyBase . '&signer=dekan&nip=' . urlencode($nipDekan),
    'QRCODE,M', '', '', 20, 20,
    ['border' => 0, 'padding' => 0, 'fgcolor' => [0, 0, 0], 'bgcolor' => false], 'N'
]);

$commonCss = '
<style>
  table.header-box { border: 1px solid #000; width: 100%; border-collapse: collapse; }
  table.header-box td, table.header-box th { border: 1px solid #000; padding: 2.5px 4px; }
  .bg-header { background-color: #E6E6E6; font-weight: bold; text-align: center; }
  table.table-bab { width: 100%; border-collapse: collapse; border: 1px solid #000; }
  table.table-bab th { background-color: #E6E6E6; font-weight: bold; font-size: 7pt; text-align: center; border: 1px solid #000; padding: 3px 3px; }
  table.table-bab td { border: 1px solid #000; padding: 2.5px 3.5px; vertical-align: top; font-size: 7pt; line-height: 1.2; }
  .chapter-title-box { background-color: #E6E6E6; border: 1px solid #000; padding: 3.5px 5px; font-weight: bold; font-size: 8.5pt; text-transform: uppercase; margin-bottom: 2px; }
  .chapter-desc { font-size: 7pt; color: #333; margin-bottom: 4px; font-style: italic; }
  .badge-status { font-weight: bold; font-size: 6.5pt; text-transform: uppercase; }
</style>
';

// ============================================================
// HALAMAN 1: IDENTITAS DOKUMEN, PENGESAHAN DIGITAL, & RINGKASAN EKSEKUTIF
// ============================================================
$pdf->AddPage();
$h1 = $commonCss . '
<table class="header-box" cellpadding="2.5">
  <tr>
    <td width="11%" align="center" rowspan="2" style="vertical-align:middle;">
      <img src="' . $logoFile . '" width="54" />
    </td>
    <td width="74%" align="center" style="vertical-align:middle;">
      <h2 style="margin:0; font-size:12pt; font-weight:bold;">UNIVERSITAS KATOLIK SOEGIJAPRANATA</h2>
      <h3 style="margin:1px 0 0 0; font-size:10pt; font-weight:bold;">' . strtoupper(htmlspecialchars($namaFakultas)) . '</h3>
      <h4 style="margin:1px 0 0 0; font-size:8.5pt;">PROGRAM STUDI ' . strtoupper(htmlspecialchars($namaProdi)) . ' (' . strtoupper(htmlspecialchars($jenjangProdi)) . ')</h4>
    </td>
    <td width="15%" align="center" class="bg-header" style="vertical-align:middle;">
      <span style="font-size:7pt;">KODE DOKUMEN</span><br>
      <span style="font-size:9pt; font-weight:bold;">' . htmlspecialchars($kodeDokumen) . '</span>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center" class="bg-header" style="vertical-align:middle;">
      <h3 style="margin:1px 0; font-size:10pt; font-weight:bold;">LAPORAN SIKLUS PENJAMINAN MUTU INTERNAL (SPMI) PPEPP</h3>
    </td>
  </tr>
  <tr class="bg-header" style="font-size:7pt;">
    <td width="35%">PROYEK SIKLUS PPEPP</td>
    <td width="30%">DOKUMEN ACUAN PENETAPAN</td>
    <td width="15%">TAHUN AKADEMIK</td>
    <td width="10%">SEMESTER</td>
    <td width="10%">STATUS</td>
  </tr>
  <tr align="center" style="font-size:7.5pt;">
    <td align="left"><b>' . htmlspecialchars($proj['judul'] ?? 'Proyek PPEPP') . '</b></td>
    <td align="left">' . htmlspecialchars($pen['judul'] ?? '—') . '</td>
    <td>' . htmlspecialchars($proj['ta_nama'] ?? '—') . '</td>
    <td>' . ucfirst(htmlspecialchars($proj['semester'] ?? '—')) . '</td>
    <td><b>' . strtoupper(htmlspecialchars($proj['status'] ?? 'Aktif')) . '</b></td>
  </tr>
  <tr>
    <td colspan="5" style="padding:0;">
      <table cellpadding="3" style="width:100%; border:none; text-align:center;">
        <tr class="bg-header" style="font-size:7pt;">
          <td width="33%" style="border-right:1px solid #000;">Ketua Gugus Penjaminan Mutu (GPM)</td>
          <td width="34%" style="border-right:1px solid #000;">Ketua Program Studi</td>
          <td width="33%">Dekan Fakultas</td>
        </tr>
        <tr>
          <td style="border-right:1px solid #000; vertical-align:middle; padding-top:3px;">
            <tcpdf data="' . $qrTagGpm . '" /><br>
            <u><b>' . htmlspecialchars($namaPenyusun) . '</b></u><br>
            <span style="font-size:6.5pt;">NIP. ' . htmlspecialchars($nipPenyusun) . '</span>
          </td>
          <td style="border-right:1px solid #000; vertical-align:middle; padding-top:3px;">
            <tcpdf data="' . $qrTagKaprodi . '" /><br>
            <u><b>' . htmlspecialchars($namaKaprodi) . '</b></u><br>
            <span style="font-size:6.5pt;">NIP. ' . htmlspecialchars($nipKaprodi) . '</span>
          </td>
          <td style="vertical-align:middle; padding-top:3px;">
            <tcpdf data="' . $qrTagDekan . '" /><br>
            <u><b>' . htmlspecialchars($namaDekan) . '</b></u><br>
            <span style="font-size:6.5pt;">NIP. ' . htmlspecialchars($nipDekan) . '</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<br><br>
<div class="chapter-title-box">RINGKASAN EKSEKUTIF KETERCAPAIAN SIKLUS MUTU PPEPP</div>
<div class="chapter-desc">Rekapitulasi komprehensif tingkat ketercapaian 5 tahap penjaminan mutu pada periode tahun akademik ini.</div>

<table class="table-bab" cellpadding="3.5" style="text-align:center; font-size:7.5pt;">
  <thead>
    <tr class="bg-header">
      <th width="16%" rowspan="2" style="vertical-align:middle;">1. PENETAPAN (P)</th>
      <th width="26%" colspan="3">2. PELAKSANAAN (P)</th>
      <th width="26%" colspan="3">3. EVALUASI (E)</th>
      <th width="18%" colspan="2">4. PENGENDALIAN (P)</th>
      <th width="14%" rowspan="2" style="vertical-align:middle;">5. PENINGKATAN (P)</th>
    </tr>
    <tr class="bg-header" style="font-size:7pt;">
      <th width="9%">Terlaksana</th>
      <th width="8%">Proses</th>
      <th width="9%">Belum</th>
      <th width="9%">Tercapai</th>
      <th width="9%">Sebagian</th>
      <th width="8%">Belum</th>
      <th width="9%">Perlu RTL</th>
      <th width="9%">Selesai</th>
    </tr>
  </thead>
  <tbody>
    <tr style="font-size:8pt;">
      <td><b>' . ($stats['total_standar'] ?? count($matrix)) . '</b> Standar<br><span style="font-size:6.5pt;color:#555;">Ditetapkan 100%</span></td>
      <td><b>' . ($stats['pelaksanaan']['terlaksana'] ?? 0) . '</b><br><span style="font-size:6.5pt;color:#555;">(' . ($stats['pct_pelaksanaan'] ?? 0) . '%)</span></td>
      <td><b>' . ($stats['pelaksanaan']['proses'] ?? 0) . '</b></td>
      <td><b>' . ($stats['pelaksanaan']['belum'] ?? 0) . '</b></td>
      <td><b>' . ($stats['evaluasi']['tercapai'] ?? 0) . '</b><br><span style="font-size:6.5pt;color:#555;">(' . ($stats['pct_evaluasi'] ?? 0) . '%)</span></td>
      <td><b>' . ($stats['evaluasi']['sebagian'] ?? 0) . '</b></td>
      <td><b>' . ($stats['evaluasi']['belum_tercapai'] ?? 0) . '</b></td>
      <td><b>' . ($stats['pengendalian']['total_rtl'] ?? 0) . '</b> Standar</td>
      <td><b>' . ($stats['pengendalian']['selesai'] ?? 0) . '</b><br><span style="font-size:6.5pt;color:#555;">(' . ($stats['pct_pengendalian'] ?? 100) . '%)</span></td>
      <td><b>' . ($stats['peningkatan']['ditingkatkan'] ?? 0) . '</b> Standar<br><span style="font-size:6.5pt;color:#555;">Target Naik</span></td>
    </tr>
  </tbody>
</table>

<br><br>
<table class="table-bab" cellpadding="3" style="font-size:7.5pt;">
  <thead>
    <tr class="bg-header"><th colspan="2" align="left"><b>SISTEMATIKA LAPORAN SIKLUS PPEPP</b></th></tr>
  </thead>
  <tbody>
    <tr><td width="22%"><b>BAB I. TAHAP PENETAPAN (P)</b></td><td width="78%">Pernyataan standar target capaian, indikator ketercapaian, dan dasar regulasi acuan mutu.</td></tr>
    <tr><td><b>BAB II. TAHAP PELAKSANAAN (P)</b></td><td>Pencatatan realisasi kegiatan, status keterlaksanaan, capaian angka kuantitatif, dan tautan bukti.</td></tr>
    <tr><td><b>BAB III. TAHAP EVALUASI (E)</b></td><td>Audit capaian aktual terhadap target standar, analisis ketercapaian, dan analisis gap mutu.</td></tr>
    <tr><td><b>BAB IV. TAHAP PENGENDALIAN (P)</b></td><td>Analisis akar masalah dan Rencana Tindak Lanjut (RTL) koreksi standar beserta PIC & target waktu.</td></tr>
    <tr><td><b>BAB V. TAHAP PENINGKATAN (P)</b></td><td>Peningkatan standar mutu baru (continuous improvement) bagi standar yang telah tercapai melampaui target.</td></tr>
  </tbody>
</table>
';
$pdf->writeHTML($h1, true, false, true, false, '');

// ============================================================
// BAB I: TAHAP PENETAPAN (P)
// ============================================================
$pdf->AddPage();
$hBab1 = $commonCss . '
<div class="chapter-title-box">BAB I. TAHAP PENETAPAN STANDAR MUTU (P)</div>
<div class="chapter-desc">Memuat rumusan standar mutu SPMI, pernyataan target capaian, indikator ketercapaian mutu, serta dasar regulasi yang menjadi acuan pelaksanaan standar pada program studi.</div>

<table class="table-bab" cellpadding="3">
  <thead>
    <tr class="bg-header">
      <th width="4%">NO</th>
      <th width="10%">KODE</th>
      <th width="15%">KRITERIA STANDAR</th>
      <th width="35%">PERNYATAAN STANDAR (TARGET CAPAIAN)</th>
      <th width="24%">INDIKATOR KETERCAPAIAN MUTU</th>
      <th width="12%">DASAR REGULASI</th>
    </tr>
  </thead>
  <tbody>';
foreach ($matrix as $i => $m) {
    $isTurunan = !empty($m['is_turunan']);
    $badge = $isTurunan ? '<br><span style="font-size:6pt; color:#1d4ed8; font-weight:bold;">[Standar Turunan]</span>' : '';
    $hBab1 .= '<tr nobr="true">';
    $hBab1 .= '<td width="4%" align="center"><b>' . ($i + 1) . '</b></td>';
    $hBab1 .= '<td width="10%"><b>' . htmlspecialchars($m['kriteria_kode']) . '</b>' . $badge . '</td>';
    $hBab1 .= '<td width="15%"><b>' . htmlspecialchars($m['kriteria_nama']) . '</b></td>';
    $hBab1 .= '<td width="35%">' . nl2br(htmlspecialchars($m['p1_target'] ?: '—')) . '</td>';
    $hBab1 .= '<td width="24%">' . nl2br(htmlspecialchars($m['p1_indikator'] ?: '—')) . '</td>';
    $hBab1 .= '<td width="12%">' . htmlspecialchars(!empty($m['p1_aturan']) && $m['p1_aturan'] !== '—' ? $m['p1_aturan'] : '—') . '</td>';
    $hBab1 .= '</tr>';
}
$hBab1 .= '</tbody></table>';
$pdf->writeHTML($hBab1, true, false, true, false, '');

// ============================================================
// BAB II: TAHAP PELAKSANAAN (P)
// ============================================================
$pdf->AddPage();
$hBab2 = $commonCss . '
<div class="chapter-title-box">BAB II. TAHAP PELAKSANAAN STANDAR MUTU (P)</div>
<div class="chapter-desc">Memuat status keterlaksanaan standar mutu SPMI yang dijalankan, isian realisasi angka kuantitatif (khusus standar turunan), serta catatan realisasi dan bukti fisik keterlaksanaan.</div>

<table class="table-bab" cellpadding="3">
  <thead>
    <tr class="bg-header">
      <th width="4%">NO</th>
      <th width="10%">KODE</th>
      <th width="15%">KRITERIA STANDAR</th>
      <th width="28%">ACUAN PERNYATAAN STANDAR</th>
      <th width="13%">STATUS PELAKSANAAN</th>
      <th width="12%">REALISASI ANGKA</th>
      <th width="18%">CATATAN REALISASI &amp; BUKTI</th>
    </tr>
  </thead>
  <tbody>';
foreach ($matrix as $i => $m) {
    $stP2       = $m['p2_status'] ?? 'belum';
    $isTurunan  = !empty($m['is_turunan']);
    $capaianAngka = trim($m['p2_capaian_angka'] ?? '');
    $statusText = ($stP2 === 'terlaksana') ? '[TERLAKSANA]' : (($stP2 === 'proses') ? '[SEDANG PROSES]' : '[BELUM DILAKSANAKAN]');
    $turunanBadge = $isTurunan ? '<br><span style="font-size:6pt; color:#1d4ed8; font-weight:bold;">[Turunan]</span>' : '';

    $hBab2 .= '<tr nobr="true">';
    $hBab2 .= '<td width="4%" align="center"><b>' . ($i + 1) . '</b></td>';
    $hBab2 .= '<td width="10%"><b>' . htmlspecialchars($m['kriteria_kode']) . '</b>' . $turunanBadge . '</td>';
    $hBab2 .= '<td width="15%"><b>' . htmlspecialchars($m['kriteria_nama']) . '</b></td>';
    $hBab2 .= '<td width="28%">' . nl2br(htmlspecialchars($m['p1_target'] ?: '—')) . '</td>';
    $hBab2 .= '<td width="13%" align="center"><span class="badge-status">' . $statusText . '</span></td>';
    $hBab2 .= '<td width="12%" align="center">';
    if ($isTurunan) {
        $hBab2 .= !empty($capaianAngka) ? '<b>' . htmlspecialchars($capaianAngka) . '</b>' : '<i>(Belum diisi)</i>';
    } else {
        $hBab2 .= '<span style="color:#777;">— (Kualitatif)</span>';
    }
    $hBab2 .= '</td>';
    $hBab2 .= '<td width="18%">' . nl2br(htmlspecialchars($m['p2_catatan'] ?: '— Belum ada catatan')) . '</td>';
    $hBab2 .= '</tr>';
}
$hBab2 .= '</tbody></table>';
$pdf->writeHTML($hBab2, true, false, true, false, '');

// ============================================================
// BAB III: TAHAP EVALUASI (E)
// ============================================================
$pdf->AddPage();
$hBab3 = $commonCss . '
<div class="chapter-title-box">BAB III. TAHAP EVALUASI PELAKSANAAN STANDAR MUTU (E)</div>
<div class="chapter-desc">Memuat hasil audit ketercapaian standar mutu terhadap target yang ditetapkan, hasil aktual pelaksanaan, status ketercapaian (Tercapai / Sebagian / Belum), dan analisis gap ketercapaian mutu.</div>

<table class="table-bab" cellpadding="3">
  <thead>
    <tr class="bg-header">
      <th width="4%">NO</th>
      <th width="10%">KODE</th>
      <th width="15%">KRITERIA STANDAR</th>
      <th width="25%">TARGET STANDAR ACUAN</th>
      <th width="13%">STATUS CAPAIAN</th>
      <th width="18%">HASIL AKTUAL PELAKSANAAN</th>
      <th width="15%">ANALISIS EVALUASI &amp; GAP</th>
    </tr>
  </thead>
  <tbody>';
foreach ($matrix as $i => $m) {
    $isTercapai = ($m['e_status'] === 'tercapai');
    $isSebagian = ($m['e_status'] === 'sebagian');
    $statusText = $isTercapai ? '[TERCAPAI PENUH]' : ($isSebagian ? '[SEBAGIAN TERCAPAI]' : '[BELUM TERCAPAI]');

    $hBab3 .= '<tr nobr="true">';
    $hBab3 .= '<td width="4%" align="center"><b>' . ($i + 1) . '</b></td>';
    $hBab3 .= '<td width="10%"><b>' . htmlspecialchars($m['kriteria_kode']) . '</b></td>';
    $hBab3 .= '<td width="15%"><b>' . htmlspecialchars($m['kriteria_nama']) . '</b></td>';
    $hBab3 .= '<td width="25%">' . nl2br(htmlspecialchars($m['p1_target'] ?: '—')) . '</td>';
    $hBab3 .= '<td width="13%" align="center"><span class="badge-status">' . $statusText . '</span></td>';
    $hBab3 .= '<td width="18%">' . nl2br(htmlspecialchars($m['e_hasil'] ?: '— Belum diisi')) . '</td>';
    $hBab3 .= '<td width="15%">' . nl2br(htmlspecialchars($m['e_evaluasi'] ?: '—'));
    if (!empty($m['e_gap'])) {
        $hBab3 .= '<br><span style="color:#555;"><i>Gap: ' . htmlspecialchars($m['e_gap']) . '</i></span>';
    }
    $hBab3 .= '</td>';
    $hBab3 .= '</tr>';
}
$hBab3 .= '</tbody></table>';
$pdf->writeHTML($hBab3, true, false, true, false, '');

// ============================================================
// BAB IV: TAHAP PENGENDALIAN (P)
// ============================================================
$pdf->AddPage();
$hBab4 = $commonCss . '
<div class="chapter-title-box">BAB IV. TAHAP PENGENDALIAN STANDAR MUTU (P)</div>
<div class="chapter-desc">Memuat perumusan tindakan koreksi dan Rencana Tindak Lanjut (RTL) bagi standar yang belum/sebagian tercapai pada tahap Evaluasi, identifikasi akar masalah (Fishbone/5-Whys), serta penugasan PIC dan target waktu penyelesaian.</div>

<table class="table-bab" cellpadding="3">
  <thead>
    <tr class="bg-header">
      <th width="4%">NO</th>
      <th width="10%">KODE</th>
      <th width="14%">KRITERIA STANDAR</th>
      <th width="12%">STATUS EVALUASI</th>
      <th width="18%">IDENTIFIKASI AKAR MASALAH</th>
      <th width="24%">RENCANA TINDAK LANJUT (RTL) / KOREKSI</th>
      <th width="11%">PENANGGUNG JAWAB (PIC)</th>
      <th width="7%">TARGET WAKTU</th>
    </tr>
  </thead>
  <tbody>';
foreach ($matrix as $i => $m) {
    $isTercapai = ($m['e_status'] === 'tercapai');
    $statusEvalText = $isTercapai ? '[TERCAPAI]' : '[BELUM TERCAPAI]';

    $hBab4 .= '<tr nobr="true">';
    $hBab4 .= '<td width="4%" align="center"><b>' . ($i + 1) . '</b></td>';
    $hBab4 .= '<td width="10%"><b>' . htmlspecialchars($m['kriteria_kode']) . '</b></td>';
    $hBab4 .= '<td width="14%"><b>' . htmlspecialchars($m['kriteria_nama']) . '</b></td>';
    $hBab4 .= '<td width="12%" align="center"><b>' . $statusEvalText . '</b></td>';
    
    if ($isTercapai) {
        $hBab4 .= '<td colspan="4" style="font-style:italic; color:#444; background-color:#fafafa;">';
        $hBab4 .= 'Standar telah terpenuhi pada tahap Evaluasi. Tidak memerlukan tindakan koreksi (RTL).';
        $hBab4 .= '</td>';
    } else {
        $hBab4 .= '<td width="18%">' . nl2br(htmlspecialchars($m['p4_akar_masalah'] ?: '— Belum dianalisis')) . '</td>';
        $hBab4 .= '<td width="24%"><b>RTL:</b> ' . nl2br(htmlspecialchars($m['p4_rtl'] ?: 'Sedang dirumuskan'));
        if (!empty($m['p4_koreksi_standar'])) {
            $hBab4 .= '<br><span style="color:#444;"><i>Koreksi: ' . htmlspecialchars($m['p4_koreksi_standar']) . '</i></span>';
        }
        $hBab4 .= '</td>';
        $hBab4 .= '<td width="11%">' . htmlspecialchars($m['p4_pic'] ?: '—') . '</td>';
        $hBab4 .= '<td width="7%" align="center">' . htmlspecialchars($m['p4_waktu'] ?: '—') . '</td>';
    }
    $hBab4 .= '</tr>';
}
$hBab4 .= '</tbody></table>';
$pdf->writeHTML($hBab4, true, false, true, false, '');

// ============================================================
// BAB V: TAHAP PENINGKATAN (P)
// ============================================================
$pdf->AddPage();
$hBab5 = $commonCss . '
<div class="chapter-title-box">BAB V. TAHAP PENINGKATAN STANDAR MUTU (P)</div>
<div class="chapter-desc">Memuat peningkatan standar mutu (continuous quality improvement / Kaizen) bagi standar yang telah tercapai melampaui target, rumusan target standar baru, indikator baru, serta strategi peningkatan untuk siklus SPMI berikutnya.</div>

<table class="table-bab" cellpadding="3">
  <thead>
    <tr class="bg-header">
      <th width="4%">NO</th>
      <th width="10%">KODE</th>
      <th width="14%">KRITERIA STANDAR</th>
      <th width="20%">TARGET STANDAR AWAL</th>
      <th width="12%">STATUS TINDAKAN</th>
      <th width="22%">TARGET STANDAR BARU (PENINGKATAN)</th>
      <th width="18%">INDIKATOR BARU &amp; KENAIKAN MUTU</th>
    </tr>
  </thead>
  <tbody>';
foreach ($matrix as $i => $m) {
    $isTercapai = ($m['e_status'] === 'tercapai');

    $hBab5 .= '<tr nobr="true">';
    $hBab5 .= '<td width="4%" align="center"><b>' . ($i + 1) . '</b></td>';
    $hBab5 .= '<td width="10%"><b>' . htmlspecialchars($m['kriteria_kode']) . '</b></td>';
    $hBab5 .= '<td width="14%"><b>' . htmlspecialchars($m['kriteria_nama']) . '</b></td>';
    $hBab5 .= '<td width="20%">' . nl2br(htmlspecialchars($m['p1_target'] ?: '—')) . '</td>';
    
    if (!$isTercapai) {
        $hBab5 .= '<td width="12%" align="center"><span style="color:#555;">[DIPERTAHANKAN]</span></td>';
        $hBab5 .= '<td colspan="2" style="font-style:italic; color:#555; background-color:#fafafa;">';
        $hBab5 .= 'Standar belum tercapai penuh, target mutu awal dipertahankan untuk siklus perbaikan berikutnya.';
        $hBab5 .= '</td>';
    } else {
        $hBab5 .= '<td width="12%" align="center"><span style="font-weight:bold; color:#000;">[DITINGKATKAN]</span></td>';
        $hBab5 .= '<td width="22%"><b>' . nl2br(htmlspecialchars($m['p5_target_baru'] ?: $m['p1_target'])) . '</b>';
        if (!empty($m['p5_strategi_baru'])) {
            $hBab5 .= '<br><span style="color:#333;"><i>Strategi: ' . htmlspecialchars($m['p5_strategi_baru']) . '</i></span>';
        }
        $hBab5 .= '</td>';
        $hBab5 .= '<td width="18%">' . nl2br(htmlspecialchars($m['p5_indikator_baru'] ?: '—'));
        if (!empty($m['p5_nilai_kenaikan'])) {
            $hBab5 .= '<br><b>Kenaikan:</b> ' . htmlspecialchars($m['p5_nilai_kenaikan']);
        }
        $hBab5 .= '</td>';
    }
    $hBab5 .= '</tr>';
}
$hBab5 .= '</tbody></table>';

$hBab5 .= '
<br>
<div style="font-size:6.5pt; color:#444; font-style:italic; border-top:1px solid #888; padding-top:3px;">
  * Dokumen resmi Laporan Siklus Penjaminan Mutu Internal (SPMI) PPEPP ini disusun dan disahkan secara kelembagaan oleh Universitas Katolik Soegijapranata. Verifikasi digital keabsahan dokumen dapat dilakukan melalui pemindaian QR-Code pada lembar pengesahan resmi.
</div>';
$pdf->writeHTML($hBab5, true, false, true, false, '');

$pdf->Output(__DIR__ . '/test_rekap_ppepp_bab_v2.pdf', 'F');
echo "Bab report PDF v2 successfully generated! Size: " . filesize(__DIR__ . '/test_rekap_ppepp_bab_v2.pdf') . " bytes\n";
