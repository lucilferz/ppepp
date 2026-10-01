<?php
/**
 * Helper: Markdown to HTML Parser
 * Platform PPEPP Fakultas
 * Format Rapi Laporan LED & Cetak PDF.
 */

if (!function_exists('renderMarkdown')) {
    function renderMarkdown(?string $text): string
    {
        if (empty($text)) return '';

        // 1. Bersihkan kata pengantar AI (meta-intro filler)
        $patterns = [
            '/^Berikut\s+adalah\s+(data\s+spesifik\s+yang\s+berhasil\s+diekstraksi[^\n]*|hasil[^\n]*|analisis[^\n]*|temuan[^\n]*)\s*:\s*\n*/iu',
            '/^(Tentu|Baik),?\s*(berikut|saya)\s+[^\n]*\n*/iu',
            '/^Berikut\s+(ini\s+)?adalah\s+[^\n]*\n*/iu',
            '/^Berdasarkan\s+(dokumen|notulensi)\s+yang\s+(diberikan|diupload),?\s*berikut[^\n]*\n*/iu',
        ];
        foreach ($patterns as $p) {
            $text = preg_replace($p, '', $text);
        }
        $text = trim($text);

        // 2. Parse Markdown Tables (| col | col | \n | :--- | :--- | \n | val | val |)
        $lines = explode("\n", $text);
        $inTable = false;
        $tableHeader = [];
        $tableRows = [];
        $newLines = [];

        for ($i = 0; $i < count($lines); $i++) {
            $line = trim($lines[$i]);

            // Deteksi baris pemisah tabel (| :--- | :--- |) atau baris data tabel (| cell | cell |)
            if (str_starts_with($line, '|') && str_ends_with($line, '|')) {
                // Abaikan baris divider (:--- | :---)
                if (preg_match('/^\|(\s*:?-+:?\s*\|)+$/', $line)) {
                    continue;
                }

                $cells = array_map('trim', explode('|', trim($line, '|')));

                if (!$inTable) {
                    $inTable = true;
                    $tableHeader = $cells;
                } else {
                    $tableRows[] = $cells;
                }
                continue;
            }

            // Jika keluar dari area tabel, cetak HTML table
            if ($inTable) {
                $newLines[] = buildHtmlTable($tableHeader, $tableRows);
                $inTable = false;
                $tableHeader = [];
                $tableRows = [];
            }

            $newLines[] = $line;
        }

        if ($inTable) {
            $newLines[] = buildHtmlTable($tableHeader, $tableRows);
        }

        $processed = implode("\n", $newLines);

        // 3. Convert Markdown Headings
        $processed = preg_replace('/^#### (.*$)/m', '<h5 class="ai-h5" style="font-weight:700;color:#1e293b;margin:14px 0 6px;">$1</h5>', $processed);
        $processed = preg_replace('/^### (.*$)/m', '<h4 class="ai-h4" style="font-weight:700;color:#1e293b;margin:16px 0 8px;font-size:15px;border-bottom:1px solid #cbd5e1;padding-bottom:4px;">$1</h4>', $processed);
        $processed = preg_replace('/^## (.*$)/m', '<h3 class="ai-h3" style="font-weight:700;color:#1e293b;margin:18px 0 10px;font-size:17px;">$1</h3>', $processed);
        $processed = preg_replace('/^# (.*$)/m', '<h2 class="ai-h2" style="font-weight:800;color:#0f172a;margin:20px 0 12px;font-size:19px;">$1</h2>', $processed);

        // 4. Convert Bold & Italic
        $processed = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $processed);
        $processed = preg_replace('/\*([^\*]+)\*/s', '<em>$1</em>', $processed);

        // 5. Convert Bullet Lists
        $processed = preg_replace('/^\s*[\-\*]\s+(.*$)/m', '<li style="margin-bottom:4px;">$1</li>', $processed);
        $processed = preg_replace('/(<li style="margin-bottom:4px;">.*<\/li>\n?)+/s', '<ul style="margin:8px 0 12px;padding-left:22px;line-height:1.6;">$0</ul>', $processed);

        // 6. Convert Paragraphs & Line Breaks
        $finalLines = explode("\n", $processed);
        $finalHtml = [];
        foreach ($finalLines as $l) {
            $trimmed = trim($l);
            if (empty($trimmed)) continue;
            if (str_starts_with($trimmed, '<h') || str_starts_with($trimmed, '<ul') || str_starts_with($trimmed, '<ol') || str_starts_with($trimmed, '<div') || str_starts_with($trimmed, '<table') || str_starts_with($trimmed, '<li') || str_starts_with($trimmed, '<p')) {
                $finalHtml[] = $trimmed;
            } else {
                $finalHtml[] = '<p style="margin-bottom:8px;line-height:1.6;">' . $trimmed . '</p>';
            }
        }

        return implode("\n", $finalHtml);
    }
}

if (!function_exists('buildHtmlTable')) {
    function buildHtmlTable(array $headers, array $rows): string
    {
        $html = '<div class="table-responsive" style="margin:14px 0;overflow-x:auto;">';
        $html .= '<table class="table ai-table" style="width:100%;border-collapse:collapse;margin-bottom:1rem;font-size:13px;background:#fff;border:1px solid #cbd5e1;border-radius:6px;overflow:hidden;">';

        if (!empty($headers)) {
            $html .= '<thead><tr style="background:#f1f5f9;border-bottom:2px solid #94a3b8;">';
            foreach ($headers as $h) {
                $html .= '<th style="padding:9px 12px;text-align:left;font-weight:700;color:#1e293b;border:1px solid #cbd5e1;">' . htmlspecialchars($h) . '</th>';
            }
            $html .= '</tr></thead>';
        }

        if (!empty($rows)) {
            $html .= '<tbody>';
            foreach ($rows as $idx => $row) {
                $bg = ($idx % 2 === 0) ? '#ffffff' : '#f8fafc';
                $html .= '<tr style="background:' . $bg . ';border-bottom:1px solid #e2e8f0;">';
                foreach ($row as $cell) {
                    $cellContent = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', htmlspecialchars($cell));
                    $html .= '<td style="padding:8px 12px;color:#334155;border:1px solid #e2e8f0;vertical-align:top;line-height:1.5;">' . $cellContent . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody>';
        }

        $html .= '</table></div>';
        return $html;
    }
}
