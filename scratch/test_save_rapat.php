<?php
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/core/Model.php';
require_once dirname(__DIR__) . '/core/View.php';
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/app/models/Evaluasi.php';
require_once dirname(__DIR__) . '/app/models/Penetapan.php';
require_once dirname(__DIR__) . '/app/models/TahunAjaran.php';
require_once dirname(__DIR__) . '/app/models/User.php';
require_once dirname(__DIR__) . '/app/services/NotulensiApiService.php';
require_once dirname(__DIR__) . '/app/controllers/EvaluasiController.php';

session_name(SESSION_NAME);
session_start();

// Set current user
$userModel = new User();
$user = $userModel->find(3); // User Kaprodi Yoga ID 3
$_SESSION['user_id'] = $user['id'];
$_SESSION['user'] = $user;

echo "Testing saveRapat for Evaluasi ID 1...\n";
$_POST = [
    'evaluasi_id' => 1,
    'topik' => 'RTM Evaluasi Capaian Standar SPMI Semester Genap 2025/2026',
    'tahun_ajaran' => '2025/2026',
    'semester' => 'Genap',
    'jenis' => 'RTM',
    'kategori' => 'Fakultas',
    'tempat' => 'Ruang Rapat Dekanat Lt. 2',
    'tanggal' => '2026-09-16',
    'jam_mulai' => '09:00:00',
    'jam_selesai' => '11:30:00',
    'ketua_id' => 3,
    'notulis_id' => 2,
    'lampiran_link' => '',
    'notulensi' => "TEST NOTULENSI DISIMPAN:\n1. Pembahasan 1\n2. Pembahasan 2",
    'absensi' => json_encode([['nama' => 'Agus Cahyo', 'status' => 'HADIR']]),
    'kriteria' => ['C1', 'C2']
];

$controller = new EvaluasiController();
// Tangkap output JSON
ob_start();
$controller->saveRapat();
$output = ob_get_clean();

echo "Response from saveRapat:\n";
echo $output . "\n\n";

// Cek di database evaluasi
$ev = (new Evaluasi())->find(1);
echo "Notulensi di DB Evaluasi:\n";
echo $ev['notulensi'] . "\n";
