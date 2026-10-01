<?php
$zip = new ZipArchive();
if ($zip->open(__DIR__ . '/../Turunan.xlsx') !== true) {
    die("Failed\n");
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

// 2. Workbook & Sheets mapping
$wbXml = @simplexml_load_string($zip->getFromName('xl/workbook.xml'));
$relsXml = @simplexml_load_string($zip->getFromName('xl/_rels/workbook.xml.rels'));

$relsMap = [];
if ($relsXml && isset($relsXml->Relationship)) {
    foreach ($relsXml->Relationship as $rel) {
        $id = (string)$rel['Id'];
        $target = (string)$rel['Target'];
        // Ensure path is relative to xl/
        if (!str_starts_with($target, 'xl/')) {
            $target = 'xl/' . ltrim($target, '/');
        }
        $relsMap[$id] = $target;
    }
}

$sheetsData = [];
if ($wbXml && isset($wbXml->sheets->sheet)) {
    foreach ($wbXml->sheets->sheet as $sheetNode) {
        $sheetName = (string)$sheetNode['name'];
        $rId = (string)$sheetNode->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
        $targetPath = $relsMap[$rId] ?? null;
        if (!$targetPath) continue;

        $content = $zip->getFromName($targetPath);
        if (!$content) continue;
        $sheetXml = @simplexml_load_string($content);
        if (!$sheetXml || !isset($sheetXml->sheetData->row)) continue;

        // Parse rows
        $rawRows = [];
        foreach ($sheetXml->sheetData->row as $rowNode) {
            $rNum = (int)$rowNode['r'];
            $colData = [];
            foreach ($rowNode->c as $cNode) {
                $ref = (string)$cNode['r'];
                $colLetter = preg_replace('/[0-9]/', '', $ref);
                $type = (string)$cNode['t'];
                $val = '';
                if ($type === 's') {
                    $val = $sharedStrings[(int)$cNode->v] ?? '';
                } elseif ($type === 'inlineStr') {
                    $val = (string)($cNode->is->t ?? '');
                } else {
                    $val = (string)($cNode->v ?? '');
                }
                $colData[$colLetter] = trim($val);
            }
            if (!empty(array_filter($colData, fn($v) => $v !== ''))) {
                $rawRows[$rNum] = $colData;
            }
        }

        // Process header & grouping
        // Find header row
        $headerRowIdx = null;
        $colMapping = [];
        foreach ($rawRows as $rNum => $cols) {
            foreach ($cols as $colLetter => $cellText) {
                $lower = strtolower($cellText);
                if (str_contains($lower, 'nomor') || str_contains($lower, 'nama standar') || str_contains($lower, 'indikator') || str_contains($lower, 'permen') || str_contains($lower, 'aturan')) {
                    $headerRowIdx = $rNum;
                    break 2;
                }
            }
        }

        if ($headerRowIdx !== null) {
            foreach ($rawRows[$headerRowIdx] as $colLetter => $cellText) {
                $lower = strtolower($cellText);
                if (str_contains($lower, 'nomor')) {
                    $colMapping['no'] = $colLetter;
                } elseif (str_contains($lower, 'permen') || str_contains($lower, 'acuan') || str_contains($lower, 'aturan')) {
                    $colMapping['aturan'] = $colLetter;
                } elseif (str_contains($lower, 'nama') || str_contains($lower, 'standar') || str_contains($lower, 'pernyataan')) {
                    $colMapping['pernyataan_standar'] = $colLetter;
                } elseif (str_contains($lower, 'indikator')) {
                    $colMapping['indikator'] = $colLetter;
                }
            }
        }

        // Grouping
        $processedRows = [];
        $currentStd = null;
        $stdSeq = 1;

        foreach ($rawRows as $rNum => $cols) {
            if ($headerRowIdx !== null && $rNum <= $headerRowIdx) continue;

            $noVal     = $cols[$colMapping['no'] ?? 'B'] ?? ($cols['B'] ?? ($cols['C'] ?? ''));
            $aturanVal = $cols[$colMapping['aturan'] ?? 'C'] ?? ($cols['C'] ?? ($cols['D'] ?? ''));
            $targetVal = $cols[$colMapping['pernyataan_standar'] ?? 'D'] ?? ($cols['D'] ?? ($cols['E'] ?? ''));
            $indVal    = $cols[$colMapping['indikator'] ?? 'E'] ?? ($cols['E'] ?? ($cols['F'] ?? ''));

            // Clean scientific / float in noVal (e.g. 4.4444444444444446E-2)
            if (is_numeric($noVal) && (str_contains($noVal, 'E') || str_contains($noVal, 'e') || str_contains($noVal, '.'))) {
                // If it looks like scientific or small decimal, replace with sequence
                $noVal = (string)$stdSeq;
            }

            if (!empty($targetVal)) {
                // New standard
                if ($currentStd !== null) {
                    $processedRows[] = $currentStd;
                }
                $currentStd = [
                    'no' => !empty($noVal) ? $noVal : (string)$stdSeq,
                    'aturan' => $aturanVal,
                    'pernyataan_standar' => $targetVal,
                    'indikator' => !empty($indVal) ? $indVal : '',
                ];
                $stdSeq++;
            } elseif (!empty($indVal) && $currentStd !== null) {
                // Subsequent indicator row for same standard
                if (!empty($currentStd['indikator'])) {
                    $currentStd['indikator'] .= "\n" . $indVal;
                } else {
                    $currentStd['indikator'] = $indVal;
                }
            }
        }
        if ($currentStd !== null) {
            $processedRows[] = $currentStd;
        }

        $sheetsData[$sheetName] = [
            'total' => count($processedRows),
            'rows' => $processedRows
        ];
    }
}
$zip->close();

foreach ($sheetsData as $sName => $sInfo) {
    echo "Sheet '$sName': {$sInfo['total']} standards found:\n";
    foreach (array_slice($sInfo['rows'], 0, 3) as $idx => $r) {
        echo "  [{$r['no']}] {$r['pernyataan_standar']}\n";
        echo "      Aturan: {$r['aturan']}\n";
        echo "      Indikator: " . str_replace("\n", " | ", $r['indikator']) . "\n";
    }
    echo "\n";
}
