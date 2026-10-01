<?php
/**
 * Debug script: Simulasikan POST request ke saveRapat langsung via bootstrap
 * Jalankan via CLI: php scratch/debug_save_rapat.php
 */
define('BASE_PATH', __DIR__ . '/..');
define('BASE_URL', 'http://ppepp.test');

// Bootstrap minimal
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/app/models/Evaluasi.php';
require_once BASE_PATH . '/app/models/User.php';

$db = \core\Database::getInstance()->getConnection();

// --- 1. Cek data sekarang di DB sebelum simulasi ---
$stmt = $db->prepare("SELECT id, rapat_topik, notulensi, absensi, notulensi_rapat_id FROM evaluasi WHERE id = 1");
$stmt->execute();
$ev = $stmt->fetch(PDO::FETCH_ASSOC);

echo "=== SEBELUM SIMPAN ===\n";
echo "rapat_topik : " . ($ev['rapat_topik'] ?? '(kosong)') . "\n";
echo "notulensi   : " . (strlen($ev['notulensi'] ?? '') > 80 ? substr($ev['notulensi'], 0, 80) . '...' : ($ev['notulensi'] ?? '(kosong)')) . "\n";
echo "rapat_id    : " . ($ev['notulensi_rapat_id'] ?? '(null)') . "\n\n";

// --- 2. Simulasikan UPDATE langsung ---
$testNotulensi = "NOTULENSI BARU TEST: " . date('Y-m-d H:i:s') . "\nPoin 1: Evaluasi standar pendidikan.\nPoin 2: Keputusan rapat.";
$stmt2 = $db->prepare("UPDATE evaluasi SET notulensi = ?, updated_at = NOW() WHERE id = 1");
$stmt2->execute([$testNotulensi]);

// --- 3. Baca ulang ---
$stmt3 = $db->prepare("SELECT id, notulensi, updated_at FROM evaluasi WHERE id = 1");
$stmt3->execute();
$ev3 = $stmt3->fetch(PDO::FETCH_ASSOC);

echo "=== SESUDAH UPDATE LANGSUNG ===\n";
echo "notulensi   : " . (strlen($ev3['notulensi'] ?? '') > 100 ? substr($ev3['notulensi'], 0, 100) . '...' : ($ev3['notulensi'] ?? '(kosong)')) . "\n";
echo "updated_at  : " . ($ev3['updated_at'] ?? '-') . "\n\n";

// --- 4. Pastikan model find() membacanya ---
$model = new \app\models\Evaluasi();
$evFromModel = $model->find(1);

echo "=== VIA MODEL->find(1) ===\n";
echo "notulensi   : " . (strlen($evFromModel['notulensi'] ?? '') > 100 ? substr($evFromModel['notulensi'], 0, 100) . '...' : ($evFromModel['notulensi'] ?? '(kosong)')) . "\n";
echo "\n✓ Jika teks di atas SAMA dengan yang di-update, berarti save & read sudah benar.\n";
echo "  Masalah bisa ada di SESSION (auth/user check) atau di browser cache.\n";
