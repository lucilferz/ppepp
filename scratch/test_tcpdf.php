<?php
define('K_TCPDF_CALLS_IN_HTML', true);
define('K_ALLOWED_TCPDF_TAGS', '|write2DBarcode|');

require_once __DIR__ . '/../vendor/tcpdf/tcpdf.php';

$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();

$barcodeTag = $pdf->serializeTCPDFtag('write2DBarcode', [
    'https://unika.ac.id/verify?t=test123456',
    'QRCODE,M',
    '',
    '',
    24,
    24,
    ['border' => 0, 'padding' => 0, 'fgcolor' => [0, 0, 0], 'bgcolor' => false],
    'N'
]);

$logoFile = 'C:/laragon/www/ppepp/public/img/logo_univ.jpg';

$html = '<table border="1" cellpadding="4" style="width:100%;">';
$html .= '<tr>';
$html .= '<td width="12%" style="text-align:center;"><img src="' . $logoFile . '" width="60" /></td>';
$html .= '<td width="73%" style="text-align:center;"><h2>UNIVERSITAS KATOLIK SOEGIJAPRANATA</h2><h3>FAKULTAS ILMU KOMPUTER</h3></td>';
$html .= '<td width="15%" style="text-align:center;background-color:#E6E6E6;">KODE DOKUMEN<br><b>PPEPP-SI-2026</b></td>';
$html .= '</tr>';
$html .= '</table>';

$html .= '<br><table border="1" cellpadding="5" style="width:100%;text-align:center;">';
$html .= '<tr style="background-color:#E6E6E6;"><th>Penyusun GPM</th><th>Ketua Program Studi</th><th>Dekan Fakultas</th></tr>';
$html .= '<tr>';
$html .= '<td><tcpdf data="' . $barcodeTag . '" /><br><b>Agus Cahyo Nugroho, S.Kom., M.T.</b><br>NIP. 5812019362</td>';
$html .= '<td><tcpdf data="' . $barcodeTag . '" /><br><b>Dr. Albertus Dwiyoga W., M.Kom.</b><br>NIP. 5812015296</td>';
$html .= '<td><tcpdf data="' . $barcodeTag . '" /><br><b>Prof Dr. Ridwan Sanjaya, MS.IEC</b><br>NIP. 5812002255</td>';
$html .= '</tr></table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output(__DIR__ . '/test_output.pdf', 'F');
echo "File created! Size: " . filesize(__DIR__ . '/test_output.pdf') . " bytes\n";
