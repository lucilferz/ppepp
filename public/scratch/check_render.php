<?php
// Test endpoint: tampilkan isi textarea notulensi yang akan di-render
// Akses: http://ppepp.test/public/scratch/check_render.php?eval_id=1
$pdo = new PDO('mysql:host=localhost;dbname=ppepp', 'root', '');
$evalId = isset($_GET['eval_id']) ? (int)$_GET['eval_id'] : 1;

$stmt = $pdo->prepare("SELECT id, user_id, notulensi, rapat_topik, rapat_tanggal, absensi, updated_at FROM evaluasi WHERE id = ?");
$stmt->execute([$evalId]);
$ev = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: text/html; charset=utf-8');
echo '<pre>';
echo "Evaluasi ID: {$ev['id']}\n";
echo "User ID: {$ev['user_id']}\n";
echo "Updated: {$ev['updated_at']}\n\n";
echo "=== NOTULENSI (raw dari DB) ===\n";
echo htmlspecialchars($ev['notulensi'] ?? '(KOSONG)') . "\n\n";
echo "=== TEXTAREA CONTENT yang akan di-render ===\n";
echo '<textarea style="width:100%;height:200px">' . htmlspecialchars($ev['notulensi'] ?? '') . '</textarea>';
echo '</pre>';
