<?php
/**
 * Test: Simulasikan save-rapat via HTTP POST langsung
 * Akses: http://ppepp.test/scratch/test_http_save.php?id=1
 */
$pdo = new PDO('mysql:host=localhost;dbname=ppepp', 'root', '');

// Ambil evaluasi ID dari query string
$evalId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Cek data saat ini
$stmt = $pdo->prepare("SELECT id, user_id, notulensi, rapat_topik, updated_at FROM evaluasi WHERE id = ?");
$stmt->execute([$evalId]);
$ev = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ev) {
    die("Evaluasi ID {$evalId} tidak ditemukan!");
}

$timestamp = date('H:i:s');

// Update langsung
$stmt2 = $pdo->prepare("UPDATE evaluasi SET notulensi = ?, updated_at = NOW() WHERE id = ?");
$testNotulensi = "Test simpan dari HTTP: {$timestamp}\nBaris 2\nBaris 3";
$stmt2->execute([$testNotulensi, $evalId]);

// Baca ulang
$stmt3 = $pdo->prepare("SELECT notulensi, updated_at FROM evaluasi WHERE id = ?");
$stmt3->execute([$evalId]);
$ev3 = $stmt3->fetch(PDO::FETCH_ASSOC);

header('Content-Type: text/plain');
echo "=== INFO EVALUASI ID {$evalId} ===\n";
echo "user_id     : " . $ev['user_id'] . "\n";
echo "rapat_topik : " . ($ev['rapat_topik'] ?? '(kosong)') . "\n";
echo "\n=== SESUDAH UPDATE ===\n";
echo "notulensi   : " . ($ev3['notulensi'] ?? '(kosong)') . "\n";
echo "updated_at  : " . ($ev3['updated_at'] ?? '-') . "\n";
echo "\n✓ Update berhasil!\n";
echo "\nNow open: http://ppepp.test/evaluasi/{$evalId}/edit dan cek apakah notulensi menampilkan: {$timestamp}";
