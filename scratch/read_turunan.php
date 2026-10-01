<?php
$zip = new ZipArchive();
if ($zip->open(__DIR__ . '/../Turunan.xlsx') !== true) {
    die("Failed to open Turunan.xlsx\n");
}

// 1. Shared Strings
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

// 2. Sheets
$sheets = [
    'Pendidikan' => 'xl/worksheets/sheet1.xml',
    'Penelitian' => 'xl/worksheets/sheet2.xml',
    'Pengabdian' => 'xl/worksheets/sheet3.xml',
    'tambahan'   => 'xl/worksheets/sheet4.xml',
];

foreach ($sheets as $name => $xmlPath) {
    echo "========================================\n";
    echo "SHEET: $name ($xmlPath)\n";
    echo "========================================\n";
    $content = $zip->getFromName($xmlPath);
    if (!$content) {
        echo "Could not read $xmlPath\n";
        continue;
    }
    $xml = @simplexml_load_string($content);
    if (!$xml || !isset($xml->sheetData->row)) {
        echo "No rows found\n";
        continue;
    }

    $rowCount = count($xml->sheetData->row);
    echo "Total rows: $rowCount\n";

    $i = 0;
    foreach ($xml->sheetData->row as $row) {
        $i++;
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
            $cols[$colLetter] = trim($val);
        }
        
        // Print first 10 rows and any row with interesting content
        if ($i <= 10) {
            echo "Row $rNum: " . json_encode($cols, JSON_UNESCAPED_UNICODE) . "\n";
        }
    }
}

$zip->close();
