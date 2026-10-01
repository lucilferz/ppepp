<?php
$pdo = new PDO('mysql:host=localhost;dbname=ppepp', 'root', '');
$stmt = $pdo->query('SELECT id, notulensi, rapat_topik, updated_at FROM evaluasi WHERE id=1');
$ev = $stmt->fetch(PDO::FETCH_ASSOC);
echo "rapat_topik : " . ($ev['rapat_topik'] ?? '(kosong)') . "\n";
echo "updated_at  : " . ($ev['updated_at'] ?? '-') . "\n";
echo "notulensi   : " . (strlen($ev['notulensi'] ?? '') > 120 ? substr($ev['notulensi'], 0, 120) . '...' : ($ev['notulensi'] ?? '(kosong)')) . "\n";

// Cek berapa banyak evaluasi yang ada
$s2 = $pdo->query('SELECT id, judul, notulensi, rapat_topik, updated_at FROM evaluasi ORDER BY updated_at DESC LIMIT 5');
echo "\n=== 5 evaluasi terakhir diupdate ===\n";
while ($row = $s2->fetch(PDO::FETCH_ASSOC)) {
    echo "ID #{$row['id']}: " . $row['judul'] . " | updated: " . $row['updated_at'] . " | notulensi: " . (empty($row['notulensi']) ? '(kosong)' : 'ADA') . "\n";
}
