<?php
/**
 * NotulensiApiService
 * Jembatan API & Sinkronisasi Notulensi antara PPEPP dan Notulensi App (C:\laragon\www\notulensi)
 */

class NotulensiApiService
{
    const DEFAULT_API_URL = 'http://notulensi.test:8080/api/save_notulensi';
    const ALT_API_URL     = 'http://localhost:8080/notulensi/public/index.php?url=api/save_notulensi';
    const NOTULENSI_DIR   = 'C:/laragon/www/notulensi';

    /**
     * Sinkronisasi data rapat & notulensi ke sistem Notulensi
     */
    public static function sync(array $payload): array
    {
        // 1. Upayakan via REST API HTTP cURL
        $apiUrls = [self::DEFAULT_API_URL, self::ALT_API_URL];
        foreach ($apiUrls as $url) {
            $resp = self::callHttpApi($url, $payload);
            if ($resp['success']) {
                return $resp;
            }
        }

        // 2. Jika koneksi HTTP gagal (misal port webserver beda / offline),
        // gunakan Direct Database Bridge ke notulensi_db secara transparan.
        return self::directDatabaseBridge($payload);
    }

    /**
     * Eksekusi HTTP Request via cURL
     */
    private static function callHttpApi(string $url, array $payload): array
    {
        if (!function_exists('curl_init')) {
            return ['success' => false, 'message' => 'cURL extension tidak aktif'];
        }

        $ch = curl_init($url);
        $jsonPayload = json_encode($payload);

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonPayload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Content-Length: ' . strlen($jsonPayload)
            ],
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr || $httpCode < 200 || $httpCode >= 300) {
            return [
                'success' => false,
                'message' => 'HTTP Error: ' . ($curlErr ?: "Code $httpCode"),
                'raw'     => $response
            ];
        }

        $json = json_decode($response, true);
        if (!$json || ($json['status'] ?? '') !== 'success') {
            return [
                'success' => false,
                'message' => $json['message'] ?? 'Respon API tidak valid',
                'raw'     => $response
            ];
        }

        return [
            'success'   => true,
            'mode'      => 'api_http',
            'message'   => $json['message'],
            'data'      => $json['data'] ?? [],
            'rapat_id'  => $json['data']['rapat_id'] ?? null,
            'view_url'  => $json['data']['view_url'] ?? null,
            'pdf_url'   => $json['data']['pdf_url'] ?? null
        ];
    }

    /**
     * Direct Database Bridge (Fallback jika HTTP API terhalang port/network)
     */
    private static function directDatabaseBridge(array $payload): array
    {
        try {
            $dbConfigPath = self::NOTULENSI_DIR . '/config/database.php';
            if (!file_exists($dbConfigPath)) {
                return ['success' => false, 'message' => 'File database notulensi tidak ditemukan.'];
            }

            // Hubungkan langsung ke MySQL notulensi_db
            $mysqli = new mysqli('localhost', 'root', '', 'notulensi_db');
            if ($mysqli->connect_error) {
                return ['success' => false, 'message' => 'Gagal koneksi ke notulensi_db: ' . $mysqli->connect_error];
            }
            $mysqli->set_charset("utf8mb4");

            // 1. Siapkan field
            $rapatId     = !empty($payload['rapat_id']) ? (int)$payload['rapat_id'] : 0;
            $topik       = trim($payload['topik'] ?? ($payload['judul'] ?? 'Rapat Evaluasi PPEPP'));
            $semester    = in_array($payload['semester'] ?? '', ['Ganjil', 'Genap']) ? $payload['semester'] : 'Genap';
            $tahunAjaran = trim($payload['tahun_ajaran'] ?? (date('Y') . '/' . (date('Y') + 1)));
            $jenis       = trim($payload['jenis'] ?? 'Rapat');
            $kategori    = in_array($payload['kategori'] ?? '', ['Fakultas', 'Program Studi', 'RTM']) ? $payload['kategori'] : null;
            $tempat      = trim($payload['tempat'] ?? 'Ruang Rapat');
            $tanggal     = !empty($payload['tanggal']) ? $payload['tanggal'] : date('Y-m-d');
            
            $jamMulai    = !empty($payload['jam_mulai']) ? $payload['jam_mulai'] : '09:00:00';
            $jamSelesai  = !empty($payload['jam_selesai']) ? $payload['jam_selesai'] : '11:00:00';
            if (strlen($jamMulai) === 5) $jamMulai .= ':00';
            if (strlen($jamSelesai) === 5) $jamSelesai .= ':00';

            $lampiranLink = !empty(trim($payload['lampiran_link'] ?? '')) ? trim($payload['lampiran_link']) : null;
            $lampiranName = !empty($payload['lampiran']) ? trim($payload['lampiran']) : null;

            // 2. Ambil list users notulensi untuk mapping
            $allUsers = [];
            $uRes = $mysqli->query("SELECT id, nama_lengkap FROM users");
            while ($u = $uRes->fetch_assoc()) {
                $allUsers[] = $u;
            }

            $ketuaId = self::resolveUserIdDirect($payload['ketua_id'] ?? null, $payload['ketua_nama'] ?? '', $allUsers);
            $notulisId = self::resolveUserIdDirect($payload['notulis_id'] ?? null, $payload['notulis_nama'] ?? '', $allUsers);

            if (!$ketuaId && !empty($allUsers)) $ketuaId = (int)$allUsers[0]['id'];
            if (!$notulisId && !empty($allUsers)) $notulisId = (int)($allUsers[1]['id'] ?? $allUsers[0]['id']);

            $kriteriaRaw = $payload['kriteria'] ?? [];
            $kriteria = is_array($kriteriaRaw) ? implode(', ', $kriteriaRaw) : trim((string)$kriteriaRaw);
            $siklusPpepp = trim($payload['siklus_ppepp'] ?? 'Evaluasi');
            $notulensi = trim($payload['notulensi'] ?? '');

            $pesertaInput = $payload['peserta'] ?? ($payload['absensi'] ?? []);
            if (is_string($pesertaInput)) {
                $decoded = json_decode($pesertaInput, true);
                if (is_array($decoded)) $pesertaInput = $decoded;
            }

            // 3. Simpan / Update Rapat
            $mysqli->begin_transaction();

            $ppeppEvaluasiId = !empty($payload['evaluasi_id']) ? (int)$payload['evaluasi_id'] : null;

            $isUpdate = false;
            if ($rapatId > 0) {
                $chk = $mysqli->query("SELECT id FROM rapat WHERE id = $rapatId");
                if ($chk && $chk->num_rows > 0) {
                    $isUpdate = true;
                }
            }

            // Fallback jika rapat_id belum tersimpan di PPEPP, cari rapat yang sudah terkait evaluasi_id ini
            if (!$isUpdate && $ppeppEvaluasiId > 0) {
                $chkEval = $mysqli->query("SELECT id FROM rapat WHERE ppepp_evaluasi_id = $ppeppEvaluasiId ORDER BY id ASC LIMIT 1");
                if ($chkEval && $chkEval->num_rows > 0) {
                    $rowEval = $chkEval->fetch_assoc();
                    $rapatId = (int)$rowEval['id'];
                    $isUpdate = true;
                }
            }

            if ($isUpdate) {
                // UPDATE RAPAT YANG ADA (JANGAN BUAT BARU)
                $stmt = $mysqli->prepare("UPDATE rapat SET ppepp_evaluasi_id=?, topik=?, semester=?, tahun_ajaran=?, jenis=?, kategori=?, tanggal=?, jam_mulai=?, jam_selesai=?, tempat=?, lampiran_link=?, ketua_id=?, notulis_id=?, updated_at=NOW() WHERE id=?");
                $stmt->bind_param("issssssssssiii", $ppeppEvaluasiId, $topik, $semester, $tahunAjaran, $jenis, $kategori, $tanggal, $jamMulai, $jamSelesai, $tempat, $lampiranLink, $ketuaId, $notulisId, $rapatId);
                $stmt->execute();
                $stmt->close();
            } else {
                // INSERT RAPAT BARU HANYA JIKA BELUM PERNAH ADA
                $shareToken = bin2hex(random_bytes(16));
                $stmt = $mysqli->prepare("INSERT INTO rapat (ppepp_evaluasi_id, topik, semester, tahun_ajaran, jenis, kategori, tanggal, jam_mulai, jam_selesai, tempat, lampiran, lampiran_link, ketua_id, notulis_id, share_token) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssssssssssiss", $ppeppEvaluasiId, $topik, $semester, $tahunAjaran, $jenis, $kategori, $tanggal, $jamMulai, $jamSelesai, $tempat, $lampiranName, $lampiranLink, $ketuaId, $notulisId, $shareToken);
                $stmt->execute();
                $rapatId = $stmt->insert_id;
                $stmt->close();
            }

            // 4. Simpan / Update notulensi_detail
            $chkNd = $mysqli->query("SELECT id FROM notulensi_detail WHERE rapat_id = $rapatId");
            if ($chkNd && $chkNd->num_rows > 0) {
                $stmtNd = $mysqli->prepare("UPDATE notulensi_detail SET kriteria=?, siklus_ppepp=?, notulensi=? WHERE rapat_id=?");
                $stmtNd->bind_param("sssi", $kriteria, $siklusPpepp, $notulensi, $rapatId);
                $stmtNd->execute();
                $stmtNd->close();
            } else {
                $stmtNd = $mysqli->prepare("INSERT INTO notulensi_detail (rapat_id, kriteria, siklus_ppepp, notulensi) VALUES (?, ?, ?, ?)");
                $stmtNd->bind_param("isss", $rapatId, $kriteria, $siklusPpepp, $notulensi);
                $stmtNd->execute();
                $stmtNd->close();
            }

            // 5. Peserta Rapat — simpan user yang teridentifikasi di rapat_peserta,
            //    peserta dengan nama bebas (tanpa user_id di Notulensi) tetap tersimpan via absensi JSON di PPEPP
            $mysqli->query("DELETE FROM rapat_peserta WHERE rapat_id = $rapatId");
            if (!empty($pesertaInput) && is_array($pesertaInput)) {
                $stmtP = $mysqli->prepare("INSERT INTO rapat_peserta (rapat_id, user_id, kehadiran) VALUES (?, ?, ?)");
                foreach ($pesertaInput as $pItem) {
                    $uId = null;
                    $statusHadir = 'Hadir';

                    if (is_numeric($pItem)) {
                        $uId = (int)$pItem;
                    } elseif (is_array($pItem)) {
                        $uId = self::resolveUserIdDirect($pItem['user_id'] ?? null, $pItem['nama'] ?? '', $allUsers);
                        $st = strtoupper(trim($pItem['kehadiran'] ?? ($pItem['status'] ?? '')));
                        $statusHadir = in_array($st, ['HADIR', 'HADIR / ON-SITE', 'ONLINE', 'IZIN']) ? 'Hadir' : 'Tidak Hadir';
                    }

                    if ($uId) {
                        $stmtP->bind_param("iis", $rapatId, $uId, $statusHadir);
                        $stmtP->execute();
                    }
                }
                $stmtP->close();
            }

            // 6. Sinkronisasi Foto Dokumentasi ke Notulensi
            $gambarPayload = $payload['gambar_kegiatan'] ?? null;
            if (!empty($gambarPayload)) {
                // Decode JSON jika masih berupa string
                if (is_string($gambarPayload)) {
                    $gambarPayload = json_decode($gambarPayload, true);
                }
                if (is_array($gambarPayload) && !empty($gambarPayload)) {
                    $ppeppRoot   = dirname(dirname(__DIR__)) . '/public';
                    $notulUploads = self::NOTULENSI_DIR . '/public/uploads/dokumentasi/';
                    if (!is_dir($notulUploads)) @mkdir($notulUploads, 0755, true);

                    $syncedImages = [];
                    foreach ($gambarPayload as $img) {
                        $origPath = $img['file_path'] ?? '';
                        // Normalkan path relatif (hilangkan prefix public/ atau ppepp/public/)
                        $cleanPath = ltrim(str_replace('\\', '/', $origPath), '/');
                        if (str_starts_with($cleanPath, 'ppepp/public/')) $cleanPath = substr($cleanPath, 13);
                        elseif (str_starts_with($cleanPath, 'ppepp/')) $cleanPath = substr($cleanPath, 6);
                        elseif (str_starts_with($cleanPath, 'public/')) $cleanPath = substr($cleanPath, 7);

                        $srcFile = $ppeppRoot . '/' . $cleanPath;
                        if (!file_exists($srcFile)) continue;

                        $destFileName = 'ppepp_sync_' . $rapatId . '_' . basename($srcFile);
                        $destFile     = $notulUploads . $destFileName;
                        @copy($srcFile, $destFile);

                        $syncedImages[] = [
                            'id'        => $img['id'] ?? uniqid(),
                            'file_path' => 'uploads/dokumentasi/' . $destFileName,
                            'file_name' => $img['file_name'] ?? basename($srcFile),
                            'url'       => 'http://notulensi.test:8080/uploads/dokumentasi/' . $destFileName,
                            'time'      => $img['time'] ?? date('d M Y H:i'),
                        ];
                    }

                    if (!empty($syncedImages)) {
                        $gambarJson = $mysqli->real_escape_string(json_encode($syncedImages));
                        $mysqli->query("UPDATE rapat SET gambar_kegiatan = '$gambarJson' WHERE id = $rapatId");
                    }
                }
            }

            $mysqli->commit();
            $mysqli->close();

            $baseUrl = 'http://notulensi.test:8080';
            $viewUrl = $baseUrl . '/rapat/detail/' . $rapatId;
            $pdfUrl  = $baseUrl . '/pdf/view/' . $rapatId;

            return [
                'success'   => true,
                'mode'      => 'direct_db',
                'message'   => $isUpdate ? 'Notulensi berhasil diupdate di sistem Notulensi' : 'Notulensi berhasil disimpan di sistem Notulensi',
                'rapat_id'  => $rapatId,
                'view_url'  => $viewUrl,
                'pdf_url'   => $pdfUrl,
                'synced_at' => date('Y-m-d H:i:s')
            ];

        } catch (Exception $e) {
            if (isset($mysqli) && $mysqli instanceof mysqli) {
                $mysqli->rollback();
            }
            return [
                'success' => false,
                'message' => 'Gagal sinkronisasi DB: ' . $e->getMessage()
            ];
        }
    }

    private static function resolveUserIdDirect($id, string $nama, array $allUsers)
    {
        if (!empty($id)) {
            $idInt = (int)$id;
            foreach ($allUsers as $u) {
                if ((int)$u['id'] === $idInt) return $idInt;
            }
        }

        if (!empty($nama)) {
            $cleanSearch = self::normalizeName($nama);
            foreach ($allUsers as $u) {
                $cleanUser = self::normalizeName($u['nama_lengkap']);
                if ($cleanSearch === $cleanUser || str_contains($cleanUser, $cleanSearch) || str_contains($cleanSearch, $cleanUser)) {
                    return (int)$u['id'];
                }
            }
        }

        return null;
    }

    private static function normalizeName(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/(prof|dr|ir|s\.kom|m\.t|m\.kom|phd|m\.ba|se|m\.m|m\.ling|ms\.iec|\.)/i', '', $name);
        $name = preg_replace('/[^a-z0-9]/i', '', $name);
        return trim($name);
    }

    /**
     * Sinkronisasi langsung foto dokumentasi ke tabel rapat Notulensi
     */
    public static function syncGambar(int $rapatId, array $gambarList): bool
    {
        if ($rapatId <= 0) return false;

        try {
            $mysqli = new mysqli('localhost', 'root', '', 'notulensi_db');
            if ($mysqli->connect_error) return false;
            $mysqli->set_charset("utf8mb4");

            $notulensiDocsDir = self::NOTULENSI_DIR . '/public/uploads/dokumentasi/';
            if (!is_dir($notulensiDocsDir)) {
                @mkdir($notulensiDocsDir, 0777, true);
            }

            $ppeppPublicDir = dirname(__DIR__, 2) . '/public/';
            $syncedImages = [];

            foreach ($gambarList as $g) {
                $fPath = $g['file_path'] ?? '';
                $fName = $g['file_name'] ?? basename($fPath);
                $baseFileName = basename($fPath);

                $srcFile = $ppeppPublicDir . ltrim(str_replace('\\', '/', $fPath), '/');
                $destFile = $notulensiDocsDir . $baseFileName;

                if (file_exists($srcFile) && !file_exists($destFile)) {
                    @copy($srcFile, $destFile);
                }

                $relNotulensiPath = 'uploads/dokumentasi/' . $baseFileName;
                $syncedImages[] = [
                    'id'        => $g['id'] ?? uniqid(),
                    'file_path' => $relNotulensiPath,
                    'file_name' => $fName,
                    'url'       => '/' . $relNotulensiPath,
                    'time'      => $g['time'] ?? date('d M Y H:i')
                ];
            }

            $gambarJson = !empty($syncedImages) ? json_encode($syncedImages) : null;
            $stmt = $mysqli->prepare("UPDATE rapat SET gambar_kegiatan = ? WHERE id = ?");
            $stmt->bind_param("si", $gambarJson, $rapatId);
            $res = $stmt->execute();
            $stmt->close();
            $mysqli->close();

            return $res;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
