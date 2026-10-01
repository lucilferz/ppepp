<?php
require_once __DIR__ . '/../app/helpers/ExcelHelper.php';

// Test running on Turunan.xlsx
$parsed = ExcelHelper::parseUploadedFile(__DIR__ . '/../Turunan.xlsx', 'xlsx');

echo "is_multisheet: " . ($parsed['is_multisheet'] ? 'true' : 'false') . "\n";
echo "Total sheets: " . count($parsed['sheets']) . "\n";
foreach ($parsed['sheets'] as $s) {
    echo "  - Sheet {$s['name']}: {$s['total']} rows\n";
}
echo "Total flattened rows: " . count($parsed['rows']) . "\n";
