<?php
/**
 * Helper: ExcelHelper
 * Generator & Parser Template Excel / CSV untuk Penetapan PPEPP
 * Platform PPEPP Fakultas
 */

class ExcelHelper
{
    /**
     * Download Template Excel (.xlsx atau .csv)
     * Format Kolom: No | Aturan | Pernyataan Standar | Indikator
     */
    public static function downloadTemplate(string $format = 'xlsx'): void
    {
        if ($format === 'csv') {
            self::downloadCsvTemplate();
            return;
        }

        self::downloadXlsxTemplate();
    }

    /**
     * Download template dalam format CSV
     */
    public static function downloadCsvTemplate(): void
    {
        $filename = 'Template_Penetapan_Standar_PPEPP.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM untuk Excel
        fputs($out, "\xEF\xBB\xBF");

        // Header
        fputcsv($out, ['No', 'Aturan', 'Pernyataan Standar', 'Indikator']);

        // Baris kosong siap isi
        fputcsv($out, ['1', '', '', '']);
        fputcsv($out, ['2', '', '', '']);
        fputcsv($out, ['3', '', '', '']);
        fputcsv($out, ['4', '', '', '']);
        fputcsv($out, ['5', '', '', '']);

        fclose($out);
        exit;
    }

    /**
     * Download template dalam format asli OpenXML .xlsx (tanpa dependensi Composer)
     */
    public static function downloadXlsxTemplate(): void
    {
        $filename = 'Template_Penetapan_Standar_PPEPP.xlsx';

        // Data rows (hanya header dan baris kosong siap isi)
        $rows = [
            ['No', 'Aturan', 'Pernyataan Standar', 'Indikator'],
            ['1', '', '', ''],
            ['2', '', '', ''],
            ['3', '', '', ''],
            ['4', '', '', ''],
            ['5', '', '', ''],
        ];

        // Buat file ZIP XLSX in-memory atau temp
        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        $zip = new ZipArchive();
        if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            self::downloadCsvTemplate(); // fallback jika zip gagal
            return;
        }

        // Shared strings
        $sharedStrings = [];
        $stringIndex = [];
        $getStringIdx = function($str) use (&$sharedStrings, &$stringIndex) {
            $str = (string)$str;
            if (isset($stringIndex[$str])) {
                return $stringIndex[$str];
            }
            $idx = count($sharedStrings);
            $sharedStrings[] = $str;
            $stringIndex[$str] = $idx;
            return $idx;
        };

        // Build sheet XML
        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $sheetXml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n";
        $sheetXml .= '<cols>';
        $sheetXml .= '<col min="1" max="1" width="8" customWidth="1"/>';
        $sheetXml .= '<col min="2" max="2" width="35" customWidth="1"/>';
        $sheetXml .= '<col min="3" max="3" width="55" customWidth="1"/>';
        $sheetXml .= '<col min="4" max="4" width="45" customWidth="1"/>';
        $sheetXml .= '</cols>';
        $sheetXml .= '<sheetData>';

        $colLetters = ['A', 'B', 'C', 'D'];
        foreach ($rows as $rIdx => $row) {
            $rNum = $rIdx + 1;
            $sheetXml .= '<row r="' . $rNum . '">';
            foreach ($row as $cIdx => $val) {
                $cellRef = $colLetters[$cIdx] . $rNum;
                $sIdx = $getStringIdx($val);
                $sheetXml .= '<c r="' . $cellRef . '" t="s"><v>' . $sIdx . '</v></c>';
            }
            $sheetXml .= '</row>';
        }
        $sheetXml .= '</sheetData></worksheet>';

        // Build sharedStrings XML
        $sstXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $sstXml .= '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count($sharedStrings) . '">';
        foreach ($sharedStrings as $s) {
            $sstXml .= '<si><t>' . htmlspecialchars($s, ENT_XML1, 'UTF-8') . '</t></si>';
        }
        $sstXml .= '</sst>';

        // [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
            . '</Types>';

        // _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';

        // xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
            . '</Relationships>';

        // xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>'
            . '<sheet name="Standar Penetapan" sheetId="1" r:id="rId1"/>'
            . '</sheets>'
            . '</workbook>';

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rels);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);
        $zip->addFromString('xl/workbook.xml', $workbook);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->addFromString('xl/sharedStrings.xml', $sstXml);
        $zip->close();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tmpFile));
        header('Pragma: no-cache');
        header('Expires: 0');

        readfile($tmpFile);
        @unlink($tmpFile);
        exit;
    }

    /**
     * Parse File Excel / CSV yang Diupload
     * Mengembalikan array dengan struktur:
     * [
     *   'is_multisheet' => bool,
     *   'sheets'        => [['name' => string, 'total' => int, 'rows' => array], ...],
     *   'rows'          => array // semua baris digabung untuk fallback
     * ]
     */
    public static function parseUploadedFile(string $filePath, string $ext): array
    {
        $ext = strtolower($ext);

        if ($ext === 'csv' || $ext === 'txt') {
            return self::parseCsv($filePath);
        }

        if ($ext === 'xlsx') {
            return self::parseXlsx($filePath);
        }

        if ($ext === 'xls') {
            // Coba parse sebagai HTML table atau fallback ke CSV
            return self::parseXlsHtmlOrCsv($filePath);
        }

        return ['is_multisheet' => false, 'sheets' => [], 'rows' => []];
    }

    /**
     * Parser CSV dengan deteksi otomatis delimiter (koma, titik koma, tab)
     */
    public static function parseCsv(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false || trim($content) === '') {
            return ['is_multisheet' => false, 'sheets' => [], 'rows' => []];
        }

        // Hapus UTF-8 BOM jika ada
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        $lines = preg_split('/\r\n|\r|\n/', $content);
        if (empty($lines)) {
            return ['is_multisheet' => false, 'sheets' => [], 'rows' => []];
        }

        // Deteksi delimiter dari baris pertama
        $firstLine = $lines[0];
        $delimiter = ',';
        $semicolonCount = substr_count($firstLine, ';');
        $commaCount     = substr_count($firstLine, ',');
        $tabCount       = substr_count($firstLine, "\t");

        if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
            $delimiter = ';';
        } elseif ($tabCount > $commaCount && $tabCount > $semicolonCount) {
            $delimiter = "\t";
        }

        $fp = fopen($filePath, 'r');
        $rows = [];
        $header = null;

        while (($data = fgetcsv($fp, 0, $delimiter)) !== false) {
            // Lewati baris kosong
            if (empty(array_filter($data, fn($v) => trim($v) !== ''))) continue;

            if ($header === null) {
                $header = array_map(fn($h) => strtolower(trim($h)), $data);
                continue;
            }

            $rows[] = self::normalizeRow($data, $header);
        }
        fclose($fp);

        return [
            'is_multisheet' => false,
            'sheets'        => [
                [
                    'name'  => 'Sheet 1',
                    'total' => count($rows),
                    'rows'  => $rows
                ]
            ],
            'rows'          => $rows
        ];
    }

    /**
     * Parser XLSX asli via ZipArchive & SimpleXML dengan multi-sheet & smart grouping
     */
    public static function parseXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return ['is_multisheet' => false, 'sheets' => [], 'rows' => []];
        }

        // 1. Baca sharedStrings.xml
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

        // 2. Baca manifest relasi dan workbook untuk mendeteksi seluruh sheet
        $wbXmlContent = $zip->getFromName('xl/workbook.xml');
        $relsXmlContent = $zip->getFromName('xl/_rels/workbook.xml.rels');
        $wbXml = $wbXmlContent ? @simplexml_load_string($wbXmlContent) : null;
        $relsXml = $relsXmlContent ? @simplexml_load_string($relsXmlContent) : null;

        $relsMap = [];
        if ($relsXml && isset($relsXml->Relationship)) {
            foreach ($relsXml->Relationship as $rel) {
                $id = (string)$rel['Id'];
                $target = (string)$rel['Target'];
                if (!str_starts_with($target, 'xl/')) {
                    $target = 'xl/' . ltrim($target, '/');
                }
                $relsMap[$id] = $target;
            }
        }

        $sheetsList = [];
        if ($wbXml && isset($wbXml->sheets->sheet)) {
            foreach ($wbXml->sheets->sheet as $sheetNode) {
                $sName = (string)$sheetNode['name'];
                $rId = (string)$sheetNode->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                $targetPath = $relsMap[$rId] ?? null;
                if ($targetPath) {
                    $sheetsList[] = [
                        'name' => $sName,
                        'path' => $targetPath
                    ];
                }
            }
        }

        // Fallback jika tidak ada manifest relasi workbook
        if (empty($sheetsList)) {
            $sheetsList[] = [
                'name' => 'Sheet 1',
                'path' => 'xl/worksheets/sheet1.xml'
            ];
        }

        $allSheetsData = [];
        $allFlattenedRows = [];

        foreach ($sheetsList as $sInfo) {
            $sheetContent = $zip->getFromName($sInfo['path']);
            if ($sheetContent === false) continue;

            $sheetRows = self::parseSingleWorksheet($sheetContent, $sharedStrings, $sInfo['name']);
            if (!empty($sheetRows)) {
                $allSheetsData[] = [
                    'name'  => $sInfo['name'],
                    'total' => count($sheetRows),
                    'rows'  => $sheetRows
                ];
                foreach ($sheetRows as $sr) {
                    $allFlattenedRows[] = $sr;
                }
            }
        }

        $zip->close();

        return [
            'is_multisheet' => count($allSheetsData) > 1,
            'sheets'        => $allSheetsData,
            'rows'          => $allFlattenedRows
        ];
    }

    /**
     * Parse satu worksheet XML dengan pemetaan kolom otomatis & smart indicator grouping
     */
    private static function parseSingleWorksheet(string $sheetXmlContent, array $sharedStrings, string $sheetName = ''): array
    {
        $sheetXml = @simplexml_load_string($sheetXmlContent);
        if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
            return [];
        }

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

        if (empty($rawRows)) return [];

        // Deteksi baris header secara fleksibel (bisa di baris 1, 2, dll)
        $headerRowIdx = null;
        $colMapping = [];
        foreach ($rawRows as $rNum => $cols) {
            foreach ($cols as $colLetter => $cellText) {
                $lower = strtolower($cellText);
                if (
                    str_contains($lower, 'nomor') ||
                    str_contains($lower, 'nama standar') ||
                    str_contains($lower, 'indikator') ||
                    str_contains($lower, 'permen') ||
                    str_contains($lower, 'aturan') ||
                    str_contains($lower, 'pernyataan')
                ) {
                    $headerRowIdx = $rNum;
                    break 2;
                }
            }
        }

        if ($headerRowIdx !== null) {
            foreach ($rawRows[$headerRowIdx] as $colLetter => $cellText) {
                $lower = strtolower($cellText);
                if (str_contains($lower, 'nomor') || (str_contains($lower, 'no') && strlen($lower) <= 4)) {
                    $colMapping['no'] = $colLetter;
                } elseif (str_contains($lower, 'permen') || str_contains($lower, 'acuan') || str_contains($lower, 'aturan') || str_contains($lower, 'dasar')) {
                    $colMapping['aturan'] = $colLetter;
                } elseif (str_contains($lower, 'nama') || str_contains($lower, 'standar') || str_contains($lower, 'pernyataan') || str_contains($lower, 'target')) {
                    $colMapping['pernyataan_standar'] = $colLetter;
                } elseif (str_contains($lower, 'indikator') || str_contains($lower, 'ketercapaian') || str_contains($lower, 'ukuran')) {
                    $colMapping['indikator'] = $colLetter;
                }
            }
        }

        // Positional fallback jika header tidak terpetakan
        $availableCols = [];
        if (!empty($rawRows)) {
            $firstRowCols = reset($rawRows);
            $availableCols = array_keys($firstRowCols);
            sort($availableCols);
        }

        $processedRows = [];
        $currentStd = null;
        $stdSeq = 1;

        foreach ($rawRows as $rNum => $cols) {
            if ($headerRowIdx !== null && $rNum <= $headerRowIdx) continue;

            $noVal     = $cols[$colMapping['no'] ?? ''] ?? ($cols['B'] ?? ($cols['A'] ?? ($cols[$availableCols[0] ?? ''] ?? '')));
            $aturanVal = $cols[$colMapping['aturan'] ?? ''] ?? ($cols['C'] ?? ($cols['B'] ?? ($cols[$availableCols[1] ?? ''] ?? '')));
            $targetVal = $cols[$colMapping['pernyataan_standar'] ?? ''] ?? ($cols['D'] ?? ($cols['C'] ?? ($cols[$availableCols[2] ?? ''] ?? '')));
            $indVal    = $cols[$colMapping['indikator'] ?? ''] ?? ($cols['E'] ?? ($cols['D'] ?? ($cols[$availableCols[3] ?? ''] ?? '')));

            // Jangan masukkan baris yang hanya titik atau simbol kosong
            if (trim($targetVal) === '.' || trim($indVal) === '.') {
                continue;
            }

            // Sanitasi nomor standar jika bentuknya scientific float atau float kecil
            if (is_numeric($noVal) && (str_contains((string)$noVal, 'E') || str_contains((string)$noVal, 'e') || ((float)$noVal < 1 && (float)$noVal > 0))) {
                $noVal = (string)$stdSeq;
            }

            // Jika ada pernyataan standar baru
            if (!empty($targetVal)) {
                if ($currentStd !== null) {
                    $processedRows[] = $currentStd;
                }
                $currentStd = [
                    'no'                 => !empty($noVal) ? $noVal : (string)$stdSeq,
                    'sheet'              => $sheetName,
                    'aturan'             => $aturanVal,
                    'pernyataan_standar' => $targetVal,
                    'indikator'          => !empty($indVal) ? $indVal : '',
                ];
                $stdSeq++;
            } elseif (!empty($indVal) && $currentStd !== null) {
                // Baris indikator lanjutan untuk standar sebelumnya
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

        return $processedRows;
    }

    /**
     * Fallback untuk XLS (HTML table atau TSV/CSV)
     */
    private static function parseXlsHtmlOrCsv(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false || trim($content) === '') {
            return ['is_multisheet' => false, 'sheets' => [], 'rows' => []];
        }

        // Cek apakah berupa HTML table
        if (stripos($content, '<table') !== false && stripos($content, '<tr') !== false) {
            $rows = [];
            $dom = new DOMDocument();
            @$dom->loadHTML($content);
            $trNodes = $dom->getElementsByTagName('tr');
            $header = null;

            foreach ($trNodes as $tr) {
                $tdNodes = $tr->getElementsByTagName('td');
                if ($tdNodes->length === 0) {
                    $tdNodes = $tr->getElementsByTagName('th');
                }
                $row = [];
                foreach ($tdNodes as $td) {
                    $row[] = trim($td->textContent);
                }
                if (empty($row)) continue;

                if ($header === null) {
                    $header = array_map(fn($h) => strtolower(trim($h)), $row);
                    continue;
                }
                $rows[] = self::normalizeRow($row, $header);
            }
            return [
                'is_multisheet' => false,
                'sheets'        => [
                    [
                        'name'  => 'Sheet 1',
                        'total' => count($rows),
                        'rows'  => $rows
                    ]
                ],
                'rows'          => $rows
            ];
        }

        return self::parseCsv($filePath);
    }

    /**
     * Normalisasi baris data menjadi struktur standar:
     * ['no', 'aturan', 'pernyataan_standar', 'indikator']
     */
    private static function normalizeRow(array $data, array $header): array
    {
        $item = [
            'no'                 => '',
            'kriteria'           => '',
            'aturan'             => '',
            'pernyataan_standar' => '',
            'indikator'          => '',
        ];

        // Cari berdasarkan header jika cocok
        foreach ($header as $idx => $hName) {
            $val = trim((string)($data[$idx] ?? ''));
            if (str_contains($hName, 'kriteria')) {
                if (empty($item['kriteria'])) $item['kriteria'] = $val;
            } elseif (str_contains($hName, 'no') || str_contains($hName, 'nomor') || str_contains($hName, 'kode')) {
                if (empty($item['no'])) $item['no'] = $val;
            } elseif (str_contains($hName, 'aturan') || str_contains($hName, 'dasar') || str_contains($hName, 'kebijakan') || str_contains($hName, 'hukum') || str_contains($hName, 'dokumen')) {
                if (empty($item['aturan'])) $item['aturan'] = $val;
            } elseif (str_contains($hName, 'pernyataan') || str_contains($hName, 'standar') || str_contains($hName, 'target') || str_contains($hName, 'capaian')) {
                if (empty($item['pernyataan_standar'])) $item['pernyataan_standar'] = $val;
            } elseif (str_contains($hName, 'indikator') || str_contains($hName, 'ketercapaian') || str_contains($hName, 'ukuran')) {
                if (empty($item['indikator'])) $item['indikator'] = $val;
            }
        }

        // Positional fallback jika header tidak terdeteksi
        if (empty($item['no']) && isset($data[0])) $item['no'] = trim((string)$data[0]);
        if (empty($item['aturan']) && isset($data[1])) $item['aturan'] = trim((string)$data[1]);
        if (empty($item['pernyataan_standar']) && isset($data[2])) $item['pernyataan_standar'] = trim((string)$data[2]);
        if (empty($item['indikator']) && isset($data[3])) $item['indikator'] = trim((string)$data[3]);

        return $item;
    }

    /**
     * Konversi huruf kolom Excel (A, B, AA, ...) ke indeks 0-based
     */
    private static function colLetterToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $len = strlen($letters);
        $idx = 0;
        for ($i = 0; $i < $len; $i++) {
            $idx = $idx * 26 + (ord($letters[$i]) - 64);
        }
        return $idx - 1;
    }
}
