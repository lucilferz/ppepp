<?php
/**
 * Migration: Create tugas_dokumen table
 */
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $sql = "CREATE TABLE IF NOT EXISTS `tugas_dokumen` (
      `id` INT NOT NULL AUTO_INCREMENT,
      `kaprodi_id` INT NOT NULL,
      `dosen_id` INT NOT NULL,
      `project_id` INT NOT NULL,
      `stage` ENUM('penetapan','pelaksanaan','evaluasi','pengendalian','peningkatan') NOT NULL,
      `document_id` INT DEFAULT NULL,
      `judul_tugas` VARCHAR(255) NOT NULL,
      `instruksi` TEXT DEFAULT NULL,
      `deadline` DATE DEFAULT NULL,
      `status` ENUM('assigned','submitted','revision','approved') NOT NULL DEFAULT 'assigned',
      `catatan_dosen` TEXT DEFAULT NULL,
      `catatan_revisi` TEXT DEFAULT NULL,
      `assigned_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `submitted_at` TIMESTAMP NULL DEFAULT NULL,
      `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
      `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_kaprodi` (`kaprodi_id`),
      KEY `idx_dosen` (`dosen_id`),
      KEY `idx_project_stage` (`project_id`, `stage`),
      KEY `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);
    echo "Migration Success: Table tugas_dokumen created or already exists.\n";
} catch (Exception $e) {
    echo "Migration Failed: " . $e->getMessage() . "\n";
    exit(1);
}
