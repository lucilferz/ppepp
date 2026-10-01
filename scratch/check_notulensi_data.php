<?php
// Cek: notulensi di DB PPEPP setelah terakhir disimpan
$db = new mysqli('localhost', 'root', '', 'ppepp');
$res = $db->query("SELECT id, notulensi, absensi, notulensi_rapat_id, notulensi_sync_status FROM evaluasi WHERE id = 1");
$ev = $res->fetch_assoc();
echo "=== PPEPP Evaluasi ID 1 ===\n";
echo "notulensi_rapat_id: " . $ev['notulensi_rapat_id'] . "\n";
echo "notulensi_sync_status: " . $ev['notulensi_sync_status'] . "\n";
echo "notulensi (isi):\n" . ($ev['notulensi'] ?? '(KOSONG)') . "\n\n";

// Cek: notulensi di Notulensi DB
$db2 = new mysqli('localhost', 'root', '', 'notulensi_db');
$rapatId = $ev['notulensi_rapat_id'];
$res2 = $db2->query("SELECT nd.notulensi, r.topik FROM notulensi_detail nd JOIN rapat r ON r.id = nd.rapat_id WHERE nd.rapat_id = $rapatId");
$nd = $res2->fetch_assoc();
echo "=== Notulensi DB (Rapat #{$rapatId}) ===\n";
echo "Topik: " . ($nd['topik'] ?? '-') . "\n";
echo "notulensi (isi):\n" . ($nd['notulensi'] ?? '(KOSONG)') . "\n";
