<?php
$zip = new ZipArchive();
$zip->open(__DIR__ . '/../Turunan.xlsx');

$sharedStrings = [];
$sstXmlContent = $zip->getFromName('xl/sharedStrings.xml');
if ($sstXmlContent !== false) {
    $xml = @simplexml_load_string($sstXmlContent);
    if ($xml && isset($xml->si)) {
        foreach ($xml->si as $si) {
            if (isset($si->t)) {
                $sharedStrings[] = (string)$si->t;
            } elseif (isset($si->r)) {
                $str = '';
                foreach ($si->r as $r) {
                    $str .= (string)$r->t;
                }
                $sharedStrings[] = $str;
            } else {
                $sharedStrings[] = '';
            }
        }
    }
}

$sheets = [
    'Pendidikan' => 'xl/worksheets/sheet1.xml',
    'Penelitian' => 'xl/worksheets/sheet2.xml',
    'Pengabdian' => 'xl/worksheets/sheet3.xml',
    'tambahan'   => 'xl/worksheets/sheet4.xml',
];

foreach ($sheets as $name => $xmlPath) {
    echo "========================================\n";
    echo "SHEET: $name\n";
    echo "========================================\n";
    $content = $zip->getFromName($xmlPath);
    $xml = @simplexml_load_string($content);

    foreach ($xml->sheetData->row as $row) {
        $rNum = (string)$row['r'];
        $cols = [];
        foreach ($row->c as $c) {
            $r = (string)$c['r'];
            $t = (string)$c['t'];
            $val = '';
            if ($t === 's') {
                $sIdx = (int)$c->v;
                $val = $sharedStrings[$sIdx] ?? '';
            } elseif ($t === 'inlineStr') {
                $val = (string)($c->is->t ?? '');
            } else {
                $val = (string)($c->v ?? '');
            }
            $colLetter = preg_replace('/[0-9]/', '', $r);
            if (trim($val) !== '') {
                $cols[$colLetter] = trim($val);
            }
        }
        if (!empty($cols)) {
            echo "R$rNum: " . json_encode($cols, JSON_UNESCAPED_UNICODE) . "\n";
        }
    }
}
$zip->close();
