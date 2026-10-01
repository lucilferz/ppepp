-- Database Dump: ppepp
-- Platform PPEPP Fakultas
-- Generated: 2026-08-19 04:25:23

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Struktur tabel `users`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nama lengkap dengan gelar',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('dosen','kaprodi','dekan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dosen',
  `prodi_id` int DEFAULT NULL,
  `google_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gemini_api_key` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gemini_model` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'gemini-2.0-flash',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `google_id` (`google_id`),
  KEY `prodi_id` (`prodi_id`),
  CONSTRAINT `users_prodi_fk` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data untuk tabel `users`

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `prodi_id`, `google_id`, `avatar_url`, `gemini_api_key`, `gemini_model`, `created_at`, `updated_at`) VALUES ('1', '5812019362', '/pM0VsuGkf1FOLn9LOW7', 'Agus Cahyo Nugroho, S.Kom., M.T.', 'agus.nugroho@unika.ac.id', 'dosen', '1', NULL, NULL, '', 'gemini-2.5-flash', '2026-08-16 11:20:50', '2026-08-16 21:48:01');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `prodi_id`, `google_id`, `avatar_url`, `gemini_api_key`, `gemini_model`, `created_at`, `updated_at`) VALUES ('2', '5811994158', '$2y$10$aTpRl84y932U6bzDBxS9h.EAQtb7DvfYypl5AUYM2u1uOevgtE8RG', 'Prof Bernardinus Harnadi, S.T., M.T. PhD', 'bharnadi@unika.ac.id', 'dosen', '1', NULL, NULL, '', 'gemini-2.5-flash', '2026-08-16 11:20:50', '2026-08-16 21:48:01');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `prodi_id`, `google_id`, `avatar_url`, `gemini_api_key`, `gemini_model`, `created_at`, `updated_at`) VALUES ('3', '5812015296', '$2y$10$aTpRl84y932U6bzDBxS9h.EAQtb7DvfYypl5AUYM2u1uOevgtE8RG', 'Dr. Albertus Dwiyoga Widiantoro, S.Kom., M.Kom.', 'yoga@unika.ac.id', 'kaprodi', '1', NULL, NULL, '', 'gemini-2.5-flash', '2026-08-16 11:20:50', '2026-08-19 10:36:39');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `prodi_id`, `google_id`, `avatar_url`, `gemini_api_key`, `gemini_model`, `created_at`, `updated_at`) VALUES ('4', '5811997206', '$2y$10$aTpRl84y932U6bzDBxS9h.EAQtb7DvfYypl5AUYM2u1uOevgtE8RG', 'Fx. Hendra Prasetya, S.T., M.T.', 'hendra@unika.ac.id', 'dosen', '1', NULL, NULL, '', 'gemini-2.5-flash', '2026-08-16 11:20:50', '2026-08-16 21:48:01');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `prodi_id`, `google_id`, `avatar_url`, `gemini_api_key`, `gemini_model`, `created_at`, `updated_at`) VALUES ('5', '5812002254', '$2y$10$aTpRl84y932U6bzDBxS9h.EAQtb7DvfYypl5AUYM2u1uOevgtE8RG', 'Erdhi Widyarto Nugroho, S.T., M.T.', 'erdhi@unika.ac.id', 'dosen', '1', NULL, NULL, '', 'gemini-2.5-flash-lite', '2026-08-16 11:20:50', '2026-08-16 21:48:01');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `prodi_id`, `google_id`, `avatar_url`, `gemini_api_key`, `gemini_model`, `created_at`, `updated_at`) VALUES ('6', '5811995177', '$2y$10$aTpRl84y932U6bzDBxS9h.EAQtb7DvfYypl5AUYM2u1uOevgtE8RG', 'Dr. Ir. T. Brenda Ch, S.T., M.T.', 'brenda@unika.ac.id', 'dosen', '1', NULL, NULL, '', 'gemini-2.5-flash', '2026-08-16 11:20:50', '2026-08-16 21:48:01');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `prodi_id`, `google_id`, `avatar_url`, `gemini_api_key`, `gemini_model`, `created_at`, `updated_at`) VALUES ('7', '5812023424', '$2y$10$aTpRl84y932U6bzDBxS9h.EAQtb7DvfYypl5AUYM2u1uOevgtE8RG', 'Stephani Inggrit Swastini Dewi, S.Kom., MBA', 'stephaniinggrit@unika.ac.id', 'dosen', '1', NULL, NULL, '', 'gemini-2.5-flash-lite', '2026-08-16 11:20:50', '2026-08-16 21:48:01');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `prodi_id`, `google_id`, `avatar_url`, `gemini_api_key`, `gemini_model`, `created_at`, `updated_at`) VALUES ('8', '5812021403', '$2y$10$aTpRl84y932U6bzDBxS9h.EAQtb7DvfYypl5AUYM2u1uOevgtE8RG', 'Ir. Andre Kurniawan Pamudji, S.Kom., M.Ling', 'andre.kurniawan@unika.ac.id', 'dosen', '1', NULL, NULL, '', 'gemini-2.5-flash', '2026-08-16 11:20:50', '2026-08-16 21:48:01');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `prodi_id`, `google_id`, `avatar_url`, `gemini_api_key`, `gemini_model`, `created_at`, `updated_at`) VALUES ('9', '5812002255', '$2y$10$aTpRl84y932U6bzDBxS9h.EAQtb7DvfYypl5AUYM2u1uOevgtE8RG', 'Prof Dr. Ridwan Sanjaya, S.Kom., MS.IEC', 'ridwan@unika.ac.id', 'dekan', '1', NULL, NULL, '', 'gemini-2.5-flash-lite', '2026-08-16 11:20:50', '2026-08-16 21:48:01');
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `prodi_id`, `google_id`, `avatar_url`, `gemini_api_key`, `gemini_model`, `created_at`, `updated_at`) VALUES ('10', '5852021299', '$2y$10$x7Ql5Tis/ibYVaxL0y4SuuaP3pXRlh3sHATaSENTt/PVfu4vhF1vG', 'Agustina Alam Anggitasari, SE., M.M.', 'agustinalam@unika.ac.id', 'dosen', '1', NULL, NULL, '', 'gemini-2.5-flash', '2026-08-16 11:20:50', '2026-08-16 21:48:01');

-- --------------------------------------------------------
-- Struktur tabel `prodi`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `prodi`;
CREATE TABLE `prodi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kode singkat, misal: SI, TI, MI',
  `nama` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama lengkap program studi',
  `jenjang` enum('D3','S1','S2','S3') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'S1',
  `kaprodi_id` int DEFAULT NULL COMMENT 'FK ke users (role kaprodi)',
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`),
  KEY `kaprodi_id` (`kaprodi_id`),
  CONSTRAINT `prodi_kaprodi_fk` FOREIGN KEY (`kaprodi_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data untuk tabel `prodi`

INSERT INTO `prodi` (`id`, `kode`, `nama`, `jenjang`, `kaprodi_id`, `deskripsi`, `aktif`, `created_at`, `updated_at`) VALUES ('1', 'SI', 'Sistem Informasi', 'S1', '3', 'Program Studi Sistem Informasi Fakultas Ilmu Komputer', '1', '2026-08-16 21:48:01', '2026-08-16 21:48:01');

-- --------------------------------------------------------
-- Struktur tabel `tahun_ajaran`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `tahun_ajaran`;
CREATE TABLE `tahun_ajaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_aktif` (`aktif`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data untuk tabel `tahun_ajaran`

INSERT INTO `tahun_ajaran` (`id`, `nama`, `semester`, `aktif`, `created_at`) VALUES ('1', '2024/2025', NULL, '0', '2026-07-29 19:55:46');
INSERT INTO `tahun_ajaran` (`id`, `nama`, `semester`, `aktif`, `created_at`) VALUES ('4', '2025/2026', NULL, '0', '2026-07-29 19:55:46');
INSERT INTO `tahun_ajaran` (`id`, `nama`, `semester`, `aktif`, `created_at`) VALUES ('7', '2026/2027', NULL, '1', '2026-08-03 14:27:09');
INSERT INTO `tahun_ajaran` (`id`, `nama`, `semester`, `aktif`, `created_at`) VALUES ('9', '2027/2028', NULL, '0', '2026-08-03 14:27:09');
INSERT INTO `tahun_ajaran` (`id`, `nama`, `semester`, `aktif`, `created_at`) VALUES ('11', '2028/2029', NULL, '0', '2026-08-03 14:27:09');
INSERT INTO `tahun_ajaran` (`id`, `nama`, `semester`, `aktif`, `created_at`) VALUES ('13', '2029/2030', NULL, '0', '2026-08-03 14:27:09');
INSERT INTO `tahun_ajaran` (`id`, `nama`, `semester`, `aktif`, `created_at`) VALUES ('15', '2030/2031', NULL, '0', '2026-08-03 14:27:09');
INSERT INTO `tahun_ajaran` (`id`, `nama`, `semester`, `aktif`, `created_at`) VALUES ('17', '2031/2032', NULL, '0', '2026-08-03 14:27:09');
INSERT INTO `tahun_ajaran` (`id`, `nama`, `semester`, `aktif`, `created_at`) VALUES ('19', '2032/2033', NULL, '0', '2026-08-03 14:27:09');
INSERT INTO `tahun_ajaran` (`id`, `nama`, `semester`, `aktif`, `created_at`) VALUES ('21', '2033/2034', NULL, '0', '2026-08-03 14:27:09');
INSERT INTO `tahun_ajaran` (`id`, `nama`, `semester`, `aktif`, `created_at`) VALUES ('23', '2034/2035', NULL, '0', '2026-08-03 14:27:09');
INSERT INTO `tahun_ajaran` (`id`, `nama`, `semester`, `aktif`, `created_at`) VALUES ('25', '2035/2036', NULL, '0', '2026-08-03 14:27:09');

-- --------------------------------------------------------
-- Struktur tabel `kriteria`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `kriteria`;
CREATE TABLE `kriteria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `prodi_id` int DEFAULT NULL,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `urutan` int DEFAULT '0',
  `aktif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `kriteria_prodi_id` (`prodi_id`),
  CONSTRAINT `kriteria_prodi_fk` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data untuk tabel `kriteria`

INSERT INTO `kriteria` (`id`, `prodi_id`, `kode`, `nama`, `deskripsi`, `urutan`, `aktif`, `created_at`, `updated_at`) VALUES ('1', '1', 'C1', 'Visi, Misi, Tujuan, dan Strategi', 'Penetapan visi, misi, tujuan dan strategi program studi', '1', '1', '2026-07-29 19:55:46', '2026-08-16 22:16:08');
INSERT INTO `kriteria` (`id`, `prodi_id`, `kode`, `nama`, `deskripsi`, `urutan`, `aktif`, `created_at`, `updated_at`) VALUES ('2', '1', 'C2', 'Tata Pamong, Tata Kelola, dan Kerjasama', 'Penetapan sistem tata pamong dan tata kelola PS', '2', '1', '2026-07-29 19:55:46', '2026-08-16 22:16:08');
INSERT INTO `kriteria` (`id`, `prodi_id`, `kode`, `nama`, `deskripsi`, `urutan`, `aktif`, `created_at`, `updated_at`) VALUES ('3', '1', 'C3', 'Mahasiswa', 'Penetapan standar seleksi, penerimaan, dan layanan mahasiswa', '3', '1', '2026-07-29 19:55:46', '2026-08-16 22:16:08');
INSERT INTO `kriteria` (`id`, `prodi_id`, `kode`, `nama`, `deskripsi`, `urutan`, `aktif`, `created_at`, `updated_at`) VALUES ('4', '1', 'C4', 'Sumber Daya Manusia', 'Penetapan standar dosen dan tenaga kependidikan', '4', '1', '2026-07-29 19:55:46', '2026-08-16 22:16:08');
INSERT INTO `kriteria` (`id`, `prodi_id`, `kode`, `nama`, `deskripsi`, `urutan`, `aktif`, `created_at`, `updated_at`) VALUES ('5', '1', 'C5', 'Keuangan, Sarana, dan Prasarana', 'Penetapan standar pembiayaan dan sarana prasarana', '5', '1', '2026-07-29 19:55:46', '2026-08-16 22:16:08');
INSERT INTO `kriteria` (`id`, `prodi_id`, `kode`, `nama`, `deskripsi`, `urutan`, `aktif`, `created_at`, `updated_at`) VALUES ('6', '1', 'C6', 'Pendidikan', 'Penetapan kurikulum, pembelajaran, dan suasana akademik', '6', '1', '2026-07-29 19:55:46', '2026-08-16 22:16:08');
INSERT INTO `kriteria` (`id`, `prodi_id`, `kode`, `nama`, `deskripsi`, `urutan`, `aktif`, `created_at`, `updated_at`) VALUES ('7', '1', 'C7', 'Penelitian', 'Penetapan standar penelitian dosen dan mahasiswa', '7', '1', '2026-07-29 19:55:46', '2026-08-16 22:16:08');
INSERT INTO `kriteria` (`id`, `prodi_id`, `kode`, `nama`, `deskripsi`, `urutan`, `aktif`, `created_at`, `updated_at`) VALUES ('8', '1', 'C8', 'Pengabdian kepada Masyarakat', 'Penetapan standar PkM', '8', '1', '2026-07-29 19:55:46', '2026-08-16 22:16:08');
INSERT INTO `kriteria` (`id`, `prodi_id`, `kode`, `nama`, `deskripsi`, `urutan`, `aktif`, `created_at`, `updated_at`) VALUES ('9', '1', 'C9', 'Luaran dan Capaian Tridharma', 'Penetapan standar luaran dan capaian pembelajaran', '9', '1', '2026-07-29 19:55:46', '2026-08-16 22:16:08');

-- --------------------------------------------------------
-- Struktur tabel `ppepp_project`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `ppepp_project`;
CREATE TABLE `ppepp_project` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `prodi_id` int DEFAULT NULL,
  `tahun_ajaran_id` int NOT NULL,
  `judul` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `status` enum('aktif','nonaktif','selesai','arsip') COLLATE utf8mb4_unicode_ci DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_ta` (`user_id`,`tahun_ajaran_id`),
  KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  KEY `project_prodi_id` (`prodi_id`),
  CONSTRAINT `ppepp_project_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ppepp_project_ibfk_2` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_prodi_fk` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `penetapan`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `penetapan`;
CREATE TABLE `penetapan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `tahun_ajaran_id` int NOT NULL,
  `judul` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','final') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `visibility_status` enum('aktif','nonaktif','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `step_current` tinyint DEFAULT '1',
  `kriteria_ids` text COLLATE utf8mb4_unicode_ci,
  `berkas_sk` longtext COLLATE utf8mb4_unicode_ci,
  `ppepp_project_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  KEY `fk_penetapan_project` (`ppepp_project_id`),
  CONSTRAINT `fk_penetapan_project` FOREIGN KEY (`ppepp_project_id`) REFERENCES `ppepp_project` (`id`) ON DELETE SET NULL,
  CONSTRAINT `penetapan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penetapan_ibfk_2` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `penetapan_detail`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `penetapan_detail`;
CREATE TABLE `penetapan_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `penetapan_id` int NOT NULL,
  `kriteria_id` int NOT NULL,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_capaian` text COLLATE utf8mb4_unicode_ci,
  `indikator` text COLLATE utf8mb4_unicode_ci,
  `strategi` text COLLATE utf8mb4_unicode_ci,
  `sumber_daya` text COLLATE utf8mb4_unicode_ci,
  `ai_analisis` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `penetapan_id` (`penetapan_id`),
  KEY `kriteria_id` (`kriteria_id`),
  CONSTRAINT `penetapan_detail_ibfk_1` FOREIGN KEY (`penetapan_id`) REFERENCES `penetapan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penetapan_detail_ibfk_2` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `referensi`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `referensi`;
CREATE TABLE `referensi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `penetapan_id` int DEFAULT NULL,
  `penetapan_detail_id` int DEFAULT NULL,
  `kriteria_id` int DEFAULT NULL,
  `nama_file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_asli` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_file` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ukuran` int DEFAULT NULL,
  `konten_teks` longtext COLLATE utf8mb4_unicode_ci,
  `ai_hasil` longtext COLLATE utf8mb4_unicode_ci,
  `ai_perbandingan` longtext COLLATE utf8mb4_unicode_ci,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis` enum('umum','spmi','turunan') COLLATE utf8mb4_unicode_ci DEFAULT 'umum',
  `step_session_key` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `penetapan_id` (`penetapan_id`),
  KEY `kriteria_id` (`kriteria_id`),
  KEY `fk_ref_pen_detail` (`penetapan_detail_id`),
  CONSTRAINT `fk_ref_pen_detail` FOREIGN KEY (`penetapan_detail_id`) REFERENCES `penetapan_detail` (`id`) ON DELETE SET NULL,
  CONSTRAINT `referensi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `referensi_ibfk_2` FOREIGN KEY (`penetapan_id`) REFERENCES `penetapan` (`id`) ON DELETE SET NULL,
  CONSTRAINT `referensi_ibfk_3` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `pelaksanaan`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `pelaksanaan`;
CREATE TABLE `pelaksanaan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `penetapan_id` int NOT NULL,
  `judul` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','final') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `visibility_status` enum('aktif','nonaktif','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ppepp_project_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `penetapan_id` (`penetapan_id`),
  KEY `fk_pelaksanaan_project` (`ppepp_project_id`),
  CONSTRAINT `fk_pelaksanaan_project` FOREIGN KEY (`ppepp_project_id`) REFERENCES `ppepp_project` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pelaksanaan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pelaksanaan_ibfk_2` FOREIGN KEY (`penetapan_id`) REFERENCES `penetapan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `pelaksanaan_detail`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `pelaksanaan_detail`;
CREATE TABLE `pelaksanaan_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pelaksanaan_id` int NOT NULL,
  `kriteria_id` int NOT NULL,
  `penetapan_detail_id` int DEFAULT NULL,
  `status_pelaksanaan` enum('belum','proses','terlaksana') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum',
  `capaian_angka` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_pelaksanaan` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pel_pd` (`penetapan_detail_id`),
  KEY `idx_pelaksanaan_id` (`pelaksanaan_id`),
  KEY `idx_kriteria_id` (`kriteria_id`),
  CONSTRAINT `fk_pldet_kriteria` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pelaksanaan_detail_ibfk_1` FOREIGN KEY (`pelaksanaan_id`) REFERENCES `pelaksanaan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `pelaksanaan_bukti`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `pelaksanaan_bukti`;
CREATE TABLE `pelaksanaan_bukti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pelaksanaan_id` int NOT NULL,
  `kriteria_id` int NOT NULL,
  `penetapan_detail_id` int DEFAULT NULL,
  `judul_bukti` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_link` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pelaksanaan_id` (`pelaksanaan_id`),
  KEY `kriteria_id` (`kriteria_id`),
  KEY `idx_bukti_pd` (`penetapan_detail_id`),
  CONSTRAINT `fk_plbukti_pen_detail` FOREIGN KEY (`penetapan_detail_id`) REFERENCES `penetapan_detail` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pelaksanaan_bukti_ibfk_1` FOREIGN KEY (`pelaksanaan_id`) REFERENCES `pelaksanaan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pelaksanaan_bukti_ibfk_2` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `evaluasi`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `evaluasi`;
CREATE TABLE `evaluasi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `penetapan_id` int NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `jenis` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `status` enum('draft','final') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `visibility_status` enum('aktif','nonaktif','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `catatan_umum` text COLLATE utf8mb4_unicode_ci,
  `undangan_file` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `undangan_nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notulensi` longtext COLLATE utf8mb4_unicode_ci,
  `absensi` longtext COLLATE utf8mb4_unicode_ci,
  `gambar_kegiatan` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ppepp_project_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `penetapan_id` (`penetapan_id`),
  KEY `fk_evaluasi_project` (`ppepp_project_id`),
  CONSTRAINT `evaluasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evaluasi_ibfk_2` FOREIGN KEY (`penetapan_id`) REFERENCES `penetapan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_evaluasi_project` FOREIGN KEY (`ppepp_project_id`) REFERENCES `ppepp_project` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `evaluasi_detail`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `evaluasi_detail`;
CREATE TABLE `evaluasi_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `evaluasi_id` int NOT NULL,
  `kriteria_id` int NOT NULL,
  `penetapan_detail_id` int DEFAULT NULL,
  `hasil_aktual` text COLLATE utf8mb4_unicode_ci,
  `analisis_gap` text COLLATE utf8mb4_unicode_ci,
  `evaluasi_teks` longtext COLLATE utf8mb4_unicode_ci,
  `ai_evaluasi` longtext COLLATE utf8mb4_unicode_ci,
  `status_capaian` enum('tercapai','sebagian','belum_tercapai') COLLATE utf8mb4_unicode_ci DEFAULT 'belum_tercapai',
  `capaian_angka` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `evaluasi_id` (`evaluasi_id`),
  KEY `idx_kriteria_id` (`kriteria_id`),
  KEY `fk_evdet_pen_detail` (`penetapan_detail_id`),
  CONSTRAINT `evaluasi_detail_ibfk_1` FOREIGN KEY (`evaluasi_id`) REFERENCES `evaluasi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_evdet_pen_detail` FOREIGN KEY (`penetapan_detail_id`) REFERENCES `penetapan_detail` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `evaluasi_dokumen`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `evaluasi_dokumen`;
CREATE TABLE `evaluasi_dokumen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `evaluasi_id` int NOT NULL,
  `jenis` enum('ami','asik','notulensi','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT 'lainnya',
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_asli` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `evaluasi_id` (`evaluasi_id`),
  CONSTRAINT `evaluasi_dokumen_ibfk_1` FOREIGN KEY (`evaluasi_id`) REFERENCES `evaluasi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `pengendalian`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `pengendalian`;
CREATE TABLE `pengendalian` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `evaluasi_id` int NOT NULL,
  `penetapan_id` int DEFAULT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','final') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `visibility_status` enum('aktif','nonaktif','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `catatan_umum` text COLLATE utf8mb4_unicode_ci,
  `berkas_koreksi` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ppepp_project_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `evaluasi_id` (`evaluasi_id`),
  KEY `fk_pengendalian_project` (`ppepp_project_id`),
  KEY `idx_penetapan_id` (`penetapan_id`),
  CONSTRAINT `fk_pengendalian_penetapan` FOREIGN KEY (`penetapan_id`) REFERENCES `penetapan` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pengendalian_project` FOREIGN KEY (`ppepp_project_id`) REFERENCES `ppepp_project` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pengendalian_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pengendalian_ibfk_2` FOREIGN KEY (`evaluasi_id`) REFERENCES `evaluasi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `pengendalian_detail`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `pengendalian_detail`;
CREATE TABLE `pengendalian_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pengendalian_id` int NOT NULL,
  `kriteria_id` int NOT NULL,
  `penetapan_detail_id` int DEFAULT NULL,
  `notulensi_id` int DEFAULT NULL,
  `rencana_tindak_lanjut` text COLLATE utf8mb4_unicode_ci,
  `akar_masalah` text COLLATE utf8mb4_unicode_ci,
  `koreksi_standar` text COLLATE utf8mb4_unicode_ci,
  `penanggung_jawab` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_waktu` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_tindakan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum',
  `indikator_sukses` text COLLATE utf8mb4_unicode_ci,
  `ai_ekstrak` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pengendalian_id` (`pengendalian_id`),
  KEY `notulensi_id` (`notulensi_id`),
  KEY `idx_kriteria_id` (`kriteria_id`),
  KEY `fk_pgdet_pen_detail` (`penetapan_detail_id`),
  CONSTRAINT `fk_pgdet_kriteria` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pgdet_pen_detail` FOREIGN KEY (`penetapan_detail_id`) REFERENCES `penetapan_detail` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pengendalian_detail_ibfk_1` FOREIGN KEY (`pengendalian_id`) REFERENCES `pengendalian` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pengendalian_detail_ibfk_2` FOREIGN KEY (`notulensi_id`) REFERENCES `pengendalian_notulensi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `pengendalian_notulensi`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `pengendalian_notulensi`;
CREATE TABLE `pengendalian_notulensi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pengendalian_id` int NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_asli` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `konten_teks` longtext COLLATE utf8mb4_unicode_ci,
  `tipe_file` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ukuran` int DEFAULT '0',
  `ai_hasil` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pengendalian_id` (`pengendalian_id`),
  CONSTRAINT `pengendalian_notulensi_ibfk_1` FOREIGN KEY (`pengendalian_id`) REFERENCES `pengendalian` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `peningkatan`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `peningkatan`;
CREATE TABLE `peningkatan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `penetapan_id` int DEFAULT NULL,
  `pengendalian_id` int DEFAULT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','final') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `visibility_status` enum('aktif','nonaktif','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `catatan_umum` text COLLATE utf8mb4_unicode_ci,
  `berkas_sk` longtext COLLATE utf8mb4_unicode_ci,
  `sk_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sk_nomor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sk_tanggal` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ppepp_project_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `pengendalian_id` (`pengendalian_id`),
  KEY `fk_peningkatan_project` (`ppepp_project_id`),
  KEY `idx_peningkatan_penetapan` (`penetapan_id`),
  CONSTRAINT `fk_peningkatan_penetapan` FOREIGN KEY (`penetapan_id`) REFERENCES `penetapan` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_peningkatan_project` FOREIGN KEY (`ppepp_project_id`) REFERENCES `ppepp_project` (`id`) ON DELETE SET NULL,
  CONSTRAINT `peningkatan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `peningkatan_ibfk_2` FOREIGN KEY (`pengendalian_id`) REFERENCES `pengendalian` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `peningkatan_detail`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `peningkatan_detail`;
CREATE TABLE `peningkatan_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `peningkatan_id` int NOT NULL,
  `kriteria_id` int NOT NULL,
  `penetapan_detail_id` int DEFAULT NULL,
  `status_indikator` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ditingkatkan',
  `alasan_peningkatan` text COLLATE utf8mb4_unicode_ci,
  `indikator_baru` text COLLATE utf8mb4_unicode_ci,
  `target_baru` text COLLATE utf8mb4_unicode_ci,
  `strategi_baru` text COLLATE utf8mb4_unicode_ci,
  `nilai_kenaikan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dasar_kebijakan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_sk` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_sk` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `peningkatan_id` (`peningkatan_id`),
  KEY `idx_kriteria_id` (`kriteria_id`),
  KEY `fk_pkdet_pen_detail` (`penetapan_detail_id`),
  CONSTRAINT `fk_pkdet_kriteria` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pkdet_pen_detail` FOREIGN KEY (`penetapan_detail_id`) REFERENCES `penetapan_detail` (`id`) ON DELETE SET NULL,
  CONSTRAINT `peningkatan_detail_ibfk_1` FOREIGN KEY (`peningkatan_id`) REFERENCES `peningkatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur tabel `peningkatan_dokumen`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `peningkatan_dokumen`;
CREATE TABLE `peningkatan_dokumen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `peningkatan_id` int NOT NULL,
  `jenis` enum('sk','kebijakan','notulensi','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT 'sk',
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_asli` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_dokumen` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `peningkatan_id` (`peningkatan_id`),
  CONSTRAINT `peningkatan_dokumen_ibfk_1` FOREIGN KEY (`peningkatan_id`) REFERENCES `peningkatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
