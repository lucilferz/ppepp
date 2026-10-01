<?php
/**
 * Model: Evaluasi
 * Platform PPEPP Fakultas
 */

class Evaluasi extends Model
{
    protected string $table = 'evaluasi';

    /**
     * Ambil semua evaluasi milik user beserta info penetapan
     */
    public function findByUser(int $userId): array
    {
        $sql = "SELECT ev.*, p.judul AS penetapan_judul, p.status AS penetapan_status,
                       ta.nama AS tahun_ajaran_nama, ta.semester
                FROM evaluasi ev
                JOIN penetapan p ON p.id = ev.penetapan_id
                JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                WHERE ev.user_id = :uid
                ORDER BY ev.created_at DESC";
        return $this->raw($sql, [':uid' => $userId]);
    }

    /**
     * Default list absensi dosen / pimpinan rapat
     */
    public static function getDefaultAbsensi(): array
    {
        try {
            $userModel = new User();
            $users = $userModel->all();
            if (!empty($users)) {
                $list = [];
                foreach ($users as $u) {
                    $list[] = [
                        'user_id' => (int)$u['id'],
                        'nama'    => $u['nama_lengkap'],
                        'status'  => 'HADIR'
                    ];
                }
                return $list;
            }
        } catch (\Throwable $e) {}

        return [
            ['nama' => 'Agus Cahyo Nugroho, S.Kom., M.T.', 'status' => 'HADIR'],
            ['nama' => 'Prof Bernardinus Harnadi, S.T., M.T. PhD', 'status' => 'HADIR'],
            ['nama' => 'Dr. Albertus Dwiyoga Widiantoro, S.Kom., M.Kom.', 'status' => 'HADIR'],
            ['nama' => 'Fx. Hendra Prasetya, S.T., M.T.', 'status' => 'HADIR'],
            ['nama' => 'Erdhi Widyarto Nugroho, S.T., M.T.', 'status' => 'HADIR'],
            ['nama' => 'Dr. Ir. T. Brenda Ch, S.T., M.T.', 'status' => 'HADIR'],
            ['nama' => 'Stephani Inggrit Swastini Dewi, S.Kom., MBA', 'status' => 'HADIR'],
            ['nama' => 'Ir. Andre Kurniawan Pamudji, S.Kom., M.Ling', 'status' => 'HADIR'],
            ['nama' => 'Prof Dr. Ridwan Sanjaya, S.Kom., MS.IEC', 'status' => 'HADIR'],
            ['nama' => 'Agustina Alam Anggitasari, SE., M.M.', 'status' => 'HADIR'],
        ];
    }

    /**
     * Ambil satu evaluasi dengan detail lengkap, indikator penetapan, status pelaksanaan, dan dokumen rapat
     */
    public function findWithDetails(int $id, int $userId): ?array
    {
        $ev = $this->find($id);
        if (!$ev || $ev['user_id'] != $userId) return null;

        $db = $this->getDb();

        // Info penetapan + tahun ajaran
        $stmtP = $db->prepare(
            "SELECT p.*, ta.nama AS ta_nama, ta.semester
             FROM penetapan p
             JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
             WHERE p.id = ?"
        );
        $stmtP->execute([$ev['penetapan_id']]);
        $ev['penetapan'] = $stmtP->fetch(PDO::FETCH_ASSOC);

        // Cari pelaksanaan terkait penetapan ini
        $stmtPl = $db->prepare("SELECT id, judul, status FROM pelaksanaan WHERE penetapan_id = ? AND user_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmtPl->execute([$ev['penetapan_id'], $userId]);
        $pelaksanaan = $stmtPl->fetch(PDO::FETCH_ASSOC);
        $pelaksanaanId = $pelaksanaan ? (int)$pelaksanaan['id'] : 0;
        $ev['pelaksanaan'] = $pelaksanaan;

        // Detail kriteria dari penetapan_detail (target & indikator) + pelaksanaan_detail (status keterlaksanaan) + evaluasi_detail (isian evaluasi)
        $stmtK = $db->prepare(
            "SELECT pd.*,
                    COALESCE(NULLIF(pd.kode, ''), k.kode) AS kriteria_kode,
                    k.nama AS kriteria_nama,
                    k.deskripsi AS kriteria_deskripsi,
                    COALESCE(pld.status_pelaksanaan, 'belum') AS status_pelaksanaan,
                    pld.catatan_pelaksanaan,
                    pld.capaian_angka AS capaian_angka_pelaksanaan,
                    ed.id AS eval_detail_id,
                    ed.evaluasi_teks,
                    ed.ai_evaluasi,
                    ed.hasil_aktual,
                    ed.analisis_gap,
                    ed.status_capaian,
                    ed.catatan AS eval_catatan,
                    ed.capaian_angka AS capaian_angka_evaluasi
             FROM penetapan_detail pd
             JOIN kriteria k ON k.id = pd.kriteria_id
             LEFT JOIN pelaksanaan_detail pld ON pld.pelaksanaan_id = ? AND pld.penetapan_detail_id = pd.id
             LEFT JOIN evaluasi_detail ed ON ed.id = (
                 SELECT id FROM evaluasi_detail 
                 WHERE evaluasi_id = ? AND (penetapan_detail_id = pd.id OR (penetapan_detail_id IS NULL AND kriteria_id = pd.kriteria_id))
                 ORDER BY penetapan_detail_id DESC, id DESC 
                 LIMIT 1
             )
             WHERE pd.penetapan_id = ?
             ORDER BY k.urutan ASC, pd.id ASC"
        );
        $stmtK->execute([$pelaksanaanId, $id, $ev['penetapan_id']]);
        $ev['details'] = $stmtK->fetchAll(PDO::FETCH_ASSOC);

        // Parse Absensi JSON (jika kosong gunakan default)
        if (!empty($ev['absensi'])) {
            $parsedAbsensi = json_decode($ev['absensi'], true);
            $ev['absensi_list'] = (!empty($parsedAbsensi) && is_array($parsedAbsensi)) ? $parsedAbsensi : self::getDefaultAbsensi();
        } else {
            $ev['absensi_list'] = self::getDefaultAbsensi();
        }

        // Parse Gambar Kegiatan JSON
        if (!empty($ev['gambar_kegiatan'])) {
            $parsedImg = json_decode($ev['gambar_kegiatan'], true);
            if (is_array($parsedImg)) {
                foreach ($parsedImg as &$img) {
                    $fp = $img['file_path'] ?? '';
                    $cleanFp = ltrim(str_replace('\\', '/', $fp), '/');
                    if (str_starts_with($cleanFp, 'ppepp/public/')) $cleanFp = substr($cleanFp, 13);
                    elseif (str_starts_with($cleanFp, 'ppepp/')) $cleanFp = substr($cleanFp, 6);
                    elseif (str_starts_with($cleanFp, 'public/')) $cleanFp = substr($cleanFp, 7);

                    $img['file_path'] = $cleanFp;
                    $img['url']       = BASE_URL . '/' . ltrim($cleanFp, '/');
                }
                unset($img);
                $ev['gambar_list'] = $parsedImg;
            } else {
                $ev['gambar_list'] = [];
            }
        } else {
            $ev['gambar_list'] = [];
        }

        // Dokumen tambahan
        $stmtD = $db->prepare("SELECT * FROM evaluasi_dokumen WHERE evaluasi_id = ? ORDER BY created_at DESC");
        $stmtD->execute([$id]);
        $ev['dokumen'] = $stmtD->fetchAll(PDO::FETCH_ASSOC);

        return $ev;
    }

    /**
     * Simpan/update evaluasi_detail per kriteria / indikator (UPSERT)
     */
    public function saveDetail(int $evaluasiId, int $kriteriaId, array $data, ?int $penetapanDetailId = null): int
    {
        $statusCapaian = $data['status_capaian'] ?? 'belum_tercapai';
        if (!in_array($statusCapaian, ['tercapai', 'sebagian', 'belum_tercapai'])) {
            $statusCapaian = 'belum_tercapai';
        }

        if ($penetapanDetailId && $penetapanDetailId > 0) {
            $existing = $this->queryOne(
                "SELECT id FROM evaluasi_detail WHERE evaluasi_id = ? AND (penetapan_detail_id = ? OR (penetapan_detail_id IS NULL AND kriteria_id = ?)) ORDER BY penetapan_detail_id DESC LIMIT 1",
                [$evaluasiId, $penetapanDetailId, $kriteriaId]
            );

            if ($existing) {
                $this->exec(
                    "UPDATE evaluasi_detail
                     SET penetapan_detail_id = ?, evaluasi_teks = ?, ai_evaluasi = ?, hasil_aktual = ?, analisis_gap = ?, status_capaian = ?, catatan = ?, updated_at = NOW()
                     WHERE id = ?",
                    [
                        $penetapanDetailId,
                        $data['evaluasi_teks'] ?? '',
                        $data['ai_evaluasi']   ?? '',
                        $data['hasil_aktual']  ?? '',
                        $data['analisis_gap']  ?? '',
                        $statusCapaian,
                        $data['catatan']       ?? '',
                        $existing['id']
                    ]
                );
                return (int) $existing['id'];
            } else {
                $this->exec(
                    "INSERT INTO evaluasi_detail (evaluasi_id, kriteria_id, penetapan_detail_id, evaluasi_teks, ai_evaluasi, hasil_aktual, analisis_gap, status_capaian, catatan, created_at, updated_at)
                     VALUES (?,?,?,?,?,?,?,?,?, NOW(), NOW())",
                    [
                        $evaluasiId,
                        $kriteriaId,
                        $penetapanDetailId,
                        $data['evaluasi_teks'] ?? '',
                        $data['ai_evaluasi']   ?? '',
                        $data['hasil_aktual']  ?? '',
                        $data['analisis_gap']  ?? '',
                        $statusCapaian,
                        $data['catatan']       ?? ''
                    ]
                );
                return (int) $this->getDb()->lastInsertId();
            }
        }

        $existing = $this->queryOne(
            "SELECT id FROM evaluasi_detail WHERE evaluasi_id = ? AND kriteria_id = ? LIMIT 1",
            [$evaluasiId, $kriteriaId]
        );

        if ($existing) {
            $this->exec(
                "UPDATE evaluasi_detail
                 SET evaluasi_teks = ?, ai_evaluasi = ?, hasil_aktual = ?, analisis_gap = ?, status_capaian = ?, catatan = ?, updated_at = NOW()
                 WHERE evaluasi_id = ? AND kriteria_id = ?",
                [
                    $data['evaluasi_teks'] ?? '',
                    $data['ai_evaluasi']   ?? '',
                    $data['hasil_aktual']  ?? '',
                    $data['analisis_gap']  ?? '',
                    $statusCapaian,
                    $data['catatan']       ?? '',
                    $evaluasiId,
                    $kriteriaId
                ]
            );
            return (int) $existing['id'];
        } else {
            $this->exec(
                "INSERT INTO evaluasi_detail (evaluasi_id, kriteria_id, evaluasi_teks, ai_evaluasi, hasil_aktual, analisis_gap, status_capaian, catatan, created_at, updated_at)
                 VALUES (?,?,?,?,?,?,?,?, NOW(), NOW())",
                [
                    $evaluasiId,
                    $kriteriaId,
                    $data['evaluasi_teks'] ?? '',
                    $data['ai_evaluasi']   ?? '',
                    $data['hasil_aktual']  ?? '',
                    $data['analisis_gap']  ?? '',
                    $statusCapaian,
                    $data['catatan']       ?? ''
                ]
            );
            return (int) $this->getDb()->lastInsertId();
        }
    }

    /**
     * Update single field di evaluasi_detail via AJAX
     */
    public function saveDetailField(int $evaluasiId, int $kriteriaId, string $field, string $value, ?int $penetapanDetailId = null): bool
    {
        $allowed = ['evaluasi_teks', 'ai_evaluasi', 'hasil_aktual', 'analisis_gap', 'status_capaian', 'catatan', 'capaian_angka'];
        if (!in_array($field, $allowed)) return false;

        $db = $this->getDb();

        if ($penetapanDetailId && $penetapanDetailId > 0) {
            $existing = $this->queryOne(
                "SELECT id FROM evaluasi_detail WHERE evaluasi_id = ? AND (penetapan_detail_id = ? OR (penetapan_detail_id IS NULL AND kriteria_id = ?)) ORDER BY penetapan_detail_id DESC LIMIT 1",
                [$evaluasiId, $penetapanDetailId, $kriteriaId]
            );

            if ($existing) {
                $stmt = $db->prepare("UPDATE evaluasi_detail SET {$field} = ?, penetapan_detail_id = ?, updated_at = NOW() WHERE id = ?");
                return $stmt->execute([$value, $penetapanDetailId, $existing['id']]);
            } else {
                $stmt = $db->prepare("INSERT INTO evaluasi_detail (evaluasi_id, kriteria_id, penetapan_detail_id, {$field}, created_at, updated_at) VALUES (?,?,?,?, NOW(), NOW())");
                return $stmt->execute([$evaluasiId, $kriteriaId, $penetapanDetailId, $value]);
            }
        }

        $existing = $this->queryOne(
            "SELECT id FROM evaluasi_detail WHERE evaluasi_id = ? AND kriteria_id = ? LIMIT 1",
            [$evaluasiId, $kriteriaId]
        );

        if ($existing) {
            $stmt = $db->prepare("UPDATE evaluasi_detail SET {$field} = ?, updated_at = NOW() WHERE evaluasi_id = ? AND kriteria_id = ?");
            return $stmt->execute([$value, $evaluasiId, $kriteriaId]);
        } else {
            $stmt = $db->prepare("INSERT INTO evaluasi_detail (evaluasi_id, kriteria_id, {$field}, created_at, updated_at) VALUES (?,?,?, NOW(), NOW())");
            return $stmt->execute([$evaluasiId, $kriteriaId, $value]);
        }
    }

    /**
     * Tambah dokumen (AMI/ASIK/Notulensi)
     */
    public function addDokumen(int $evaluasiId, array $data): int|false
    {
        $db = $this->getDb();
        $stmt = $db->prepare(
            "INSERT INTO evaluasi_dokumen (evaluasi_id, jenis, judul, nama_asli, file_path, url_link, keterangan)
             VALUES (?,?,?,?,?,?,?)"
        );
        $ok = $stmt->execute([
            $evaluasiId,
            $data['jenis']    ?? 'lainnya',
            $data['judul']    ?? '',
            $data['nama_asli'] ?? '',
            $data['file_path'] ?? '',
            $data['url_link']  ?? '',
            $data['keterangan'] ?? ''
        ]);
        return $ok ? (int)$db->lastInsertId() : false;
    }

    /**
     * Hapus dokumen
     */
    public function deleteDokumen(int $dokumenId, int $evaluasiId): bool
    {
        // Ambil file_path dulu sebelum hapus
        $doc = $this->queryOne(
            "SELECT file_path FROM evaluasi_dokumen WHERE id = ? AND evaluasi_id = ?",
            [$dokumenId, $evaluasiId]
        );
        if (!$doc) return false;

        $db = $this->getDb();
        $stmt = $db->prepare("DELETE FROM evaluasi_dokumen WHERE id = ? AND evaluasi_id = ?");
        $ok = $stmt->execute([$dokumenId, $evaluasiId]);

        // Hapus file fisik jika ada
        if ($ok && !empty($doc['file_path']) && file_exists($doc['file_path'])) {
            @unlink($doc['file_path']);
        }
        return $ok;
    }

    /**
     * Stats untuk dashboard
     */
    public function statsByUser(int $userId): array
    {
        $rows = $this->raw(
            "SELECT status, COUNT(*) AS cnt FROM evaluasi WHERE user_id = ? GROUP BY status",
            [$userId]
        );
        $stats = ['total' => 0, 'draft' => 0, 'final' => 0];
        foreach ($rows as $r) {
            $stats[$r['status']] = (int)$r['cnt'];
            $stats['total'] += (int)$r['cnt'];
        }
        return $stats;
    }
}
