<?php
// Cek user_id evaluasi vs user yang ada di sistem
$pdo = new PDO('mysql:host=localhost;dbname=ppepp', 'root', '');

$stmt = $pdo->query("SELECT e.id, e.user_id, e.judul, e.notulensi, e.rapat_topik, e.updated_at, u.nama_lengkap
    FROM evaluasi e
    JOIN users u ON u.id = e.user_id
    ORDER BY e.id");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "=== DAFTAR EVALUASI & USER PEMILIK ===\n";
foreach ($rows as $r) {
    echo "Evaluasi #{$r['id']}: '{$r['judul']}'\n";
    echo "  user_id   : {$r['user_id']} ({$r['nama_lengkap']})\n";
    echo "  updated_at: {$r['updated_at']}\n";
    echo "  notulensi : " . (empty($r['notulensi']) ? '(KOSONG)' : 'ADA (' . strlen($r['notulensi']) . ' chars)') . "\n\n";
}

// Cek semua users
$users = $pdo->query("SELECT id, nama_lengkap, email FROM users")->fetchAll(PDO::FETCH_ASSOC);
echo "\n=== USERS DI SISTEM ===\n";
foreach ($users as $u) {
    echo "User #{$u['id']}: {$u['nama_lengkap']} ({$u['email']})\n";
}
