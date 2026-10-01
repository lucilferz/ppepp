<?php
$db = new mysqli('localhost', 'root', '', 'ppepp');
$resE = $db->query("SELECT id, user_id, judul FROM evaluasi");
echo "=== DATA EVALUASI ===\n";
while ($e = $resE->fetch_assoc()) {
    echo "Evaluasi ID: {$e['id']} | User ID: {$e['user_id']} | Judul: {$e['judul']}\n";
}

$resU = $db->query("SELECT id, nama_lengkap, role FROM users");
echo "\n=== DATA USERS ===\n";
while ($u = $resU->fetch_assoc()) {
    echo "User ID: {$u['id']} | Nama: {$u['nama_lengkap']} | Role: {$u['role']}\n";
}
