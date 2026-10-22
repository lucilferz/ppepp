<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_GET['project_id'] = '1';
$_GET['format'] = 'pdf';

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/View.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Prodi.php';
require_once __DIR__ . '/../app/models/PpeppProject.php';
require_once __DIR__ . '/../app/models/Laporan.php';
require_once __DIR__ . '/../app/controllers/LaporanController.php';

session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user'] = [
    'id' => 1,
    'username' => '5812019362',
    'nama_lengkap' => 'Agus Cahyo Nugroho, S.Kom., M.T.',
    'email' => 'agus.nugroho@unika.ac.id',
    'role' => 'dosen',
    'prodi_id' => 1,
];

// Capture output
ob_start();
try {
    $controller = new LaporanController();
    // Intercept exit in Output if possible, or test up to writeHTML
    echo "Controller instantiated successfully.\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
$out = ob_get_clean();
echo $out;
