<?php
/**
 * Model: Peningkatan (Peningkatan Standar Mutu & SK Standar Baru)
 * Platform PPEPP Fakultas — Tahap P ketiga (Siklus Pembaruan Standar)
 */

class Peningkatan extends Model
{
    protected string $table = 'peningkatan';

    /**
     * Semua peningkatan milik user
     */
    public function findByUser(int $userId): array
    {
        $sql = "SELECT pk.*,
                       COALESCE(p.judul, '—') AS penetapan_judul,
                       COALESCE(ta.nama, '—') AS tahun_ajaran_nama,
                       COALESCE(ta.semester, '') AS semester,
                       COALESCE(pg.judul, '—') AS pengendalian_judul,
                       COALESCE(ev.judul, '—') AS evaluasi_judul
                FROM peningkatan pk
                LEFT JOIN penetapan p ON p.id = pk.penetapan_id
                LEFT JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                LEFT JOIN pengendalian pg ON pg.id = pk.pengendalian_id
                LEFT JOIN evaluasi ev ON ev.id = pg.evaluasi_id
                WHERE pk.user_id = :uid
                ORDER BY pk.created_at DESC";
        return $this->raw($sql, [':uid' => $userId]);
    }

    /**
     * Satu peningkatan dengan detail lengkap
     */
    public function findWithDetails(int $id, int $userId): ?array
    {
        $pk = $this->find($id);
        if (!$pk || $pk['user_id'] != $userId) return null;

        $db = $this->getDb();

        $penetapanId    = (int)($pk['penetapan_id'] ?? 0);
        $pengendalianId = (int)($pk['pengendalian_id'] ?? 0);

        // Jika penetapan_id belum tersimpan, cari dari pengendalian -> evaluasi -> penetapan
        if ($penetapanId <= 0 && $pengendalianId > 0) {
            $stmtGetPen = $db->prepare(
                "SELECT ev.penetapan_id 
                 FROM pengendalian pg 
                 JOIN evaluasi ev ON ev.id = pg.evaluasi_id 
                 WHERE pg.id = ?"
            );
            $stmtGetPen->execute([$pengendalianId]);
            $penetapanId = (int)$stmtGetPen->fetchColumn();
            if ($penetapanId > 0) {
                $db->prepare("UPDATE peningkatan SET penetapan_id = ? WHERE id = ?")->execute([$penetapanId, $id]);
                $pk['penetapan_id'] = $penetapanId;
            }
        }

        // Info Penetapan & Tahun Ajaran
        $stmtP = $db->prepare(
            "SELECT p.*, ta.nama AS ta_nama, ta.semester
             FROM penetapan p
             JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
             WHERE p.id = ?"
        );
        $stmtP->execute([$penetapanId]);
        $pk['penetapan'] = $stmtP->fetch(PDO::FETCH_ASSOC) ?: [];

        // Info Evaluasi & Pengendalian Terkait
        $stmtEv = $db->prepare("SELECT id FROM evaluasi WHERE penetapan_id = ? AND user_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmtEv->execute([$penetapanId, $userId]);
        $evaluasiId = (int)$stmtEv->fetchColumn();

        if ($pengendalianId <= 0 && $penetapanId > 0) {
            $stmtPg = $db->prepare("SELECT id FROM pengendalian WHERE penetapan_id = ? AND user_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmtPg->execute([$penetapanId, $userId]);
            $pengendalianId = (int)$stmtPg->fetchColumn();
            if ($pengendalianId > 0) {
                $db->prepare("UPDATE peningkatan SET pengendalian_id = ? WHERE id = ?")->execute([$pengendalianId, $id]);
                $pk['pengendalian_id'] = $pengendalianId;
            }
        }

        // Ambil semua kriteria dari Penetapan beserta Evaluasi, Pengendalian RTL, dan Peningkatan
        $stmtK = $db->prepare(
            "SELECT pd.*,
                    COALESCE(NULLIF(pd.kode,''), k.kode) AS kriteria_kode,
                    k.nama AS kriteria_nama,
                    k.deskripsi AS kriteria_deskripsi,
                    COALESCE(ed.status_capaian, 'belum_tercapai') AS status_capaian,
                    ed.evaluasi_teks,
                    ed.hasil_aktual,
                    ed.analisis_gap,
                    pgd.rencana_tindak_lanjut,
                    pgd.akar_masalah,
                    pgd.koreksi_standar,
                    pkd.id AS pk_detail_id,
                    COALESCE(pkd.status_indikator, 'ditingkatkan') AS status_indikator,
                    COALESCE(pkd.alasan_peningkatan, '') AS alasan_peningkatan,
                    COALESCE(pkd.target_baru, '') AS target_baru,
                    COALESCE(pkd.indikator_baru, '') AS indikator_baru,
                    pkd.strategi_baru,
                    pkd.nilai_kenaikan,
                    pkd.nomor_sk,
                    pkd.tanggal_sk
             FROM penetapan_detail pd
             JOIN kriteria k ON k.id = pd.kriteria_id
             LEFT JOIN evaluasi_detail ed ON ed.evaluasi_id = ? AND (ed.penetapan_detail_id = pd.id OR (ed.penetapan_detail_id IS NULL AND ed.kriteria_id = pd.kriteria_id))
             LEFT JOIN pengendalian_detail pgd ON pgd.pengendalian_id = ? AND (pgd.penetapan_detail_id = pd.id OR (pgd.penetapan_detail_id IS NULL AND pgd.kriteria_id = pd.kriteria_id))
             LEFT JOIN peningkatan_detail pkd ON pkd.peningkatan_id = ? AND (pkd.penetapan_detail_id = pd.id OR (pkd.penetapan_detail_id IS NULL AND pkd.kriteria_id = pd.kriteria_id))
             WHERE pd.penetapan_id = ?
             ORDER BY k.urutan ASC, pd.id ASC"
        );
        $stmtK->execute([$evaluasiId, $pengendalianId, $id, $penetapanId]);
        $pk['details'] = $stmtK->fetchAll(PDO::FETCH_ASSOC);

        // Parse Berkas SK List
        if (!empty($pk['berkas_sk'])) {
            $parsed = json_decode($pk['berkas_sk'], true);
            $pk['berkas_list'] = is_array($parsed) ? $parsed : [];
        } else {
            $pk['berkas_list'] = [];
        }

        return $pk;
    }

    /**
     * Save/update field per kriteria / indikator di peningkatan_detail via AJAX
     */
    public function saveDetailField(int $pkId, int $kriteriaId, string $field, mixed $value, ?int $penetapanDetailId = null): bool
    {
        $allowed = [
            'status_indikator',
            'alasan_peningkatan',
            'target_baru',
            'indikator_baru',
            'strategi_baru',
            'nilai_kenaikan',
            'nomor_sk',
            'tanggal_sk',
        ];
        if (!in_array($field, $allowed)) return false;

        $db = $this->getDb();

        if ($penetapanDetailId && $penetapanDetailId > 0) {
            $existing = $this->queryOne(
                "SELECT id FROM peningkatan_detail WHERE peningkatan_id = ? AND penetapan_detail_id = ? LIMIT 1",
                [$pkId, $penetapanDetailId]
            );

            if ($existing) {
                $stmt = $db->prepare("UPDATE peningkatan_detail SET {$field} = ?, updated_at = NOW() WHERE peningkatan_id = ? AND penetapan_detail_id = ?");
                return $stmt->execute([$value, $pkId, $penetapanDetailId]);
            } else {
                $stmt = $db->prepare("INSERT INTO peningkatan_detail (peningkatan_id, kriteria_id, penetapan_detail_id, {$field}, created_at, updated_at) VALUES (?,?,?,?, NOW(), NOW())");
                return $stmt->execute([$pkId, $kriteriaId, $penetapanDetailId, $value]);
            }
        }

        $existing = $this->queryOne(
            "SELECT id FROM peningkatan_detail WHERE peningkatan_id = ? AND kriteria_id = ? LIMIT 1",
            [$pkId, $kriteriaId]
        );

        if ($existing) {
            $stmt = $db->prepare("UPDATE peningkatan_detail SET {$field} = ?, updated_at = NOW() WHERE peningkatan_id = ? AND kriteria_id = ?");
            return $stmt->execute([$value, $pkId, $kriteriaId]);
        } else {
            $stmt = $db->prepare("INSERT INTO peningkatan_detail (peningkatan_id, kriteria_id, {$field}, created_at, updated_at) VALUES (?,?,?, NOW(), NOW())");
            return $stmt->execute([$pkId, $kriteriaId, $value]);
        }
    }

    /**
     * Save/update seluruh detail kriteria / indikator
     */
    public function saveDetail(int $pkId, int $kriteriaId, array $data, ?int $penetapanDetailId = null): int
    {
        $allowed = [
            'status_indikator',
            'alasan_peningkatan',
            'target_baru',
            'indikator_baru',
            'strategi_baru',
            'nilai_kenaikan',
            'nomor_sk',
            'tanggal_sk'
        ];

        if ($penetapanDetailId && $penetapanDetailId > 0) {
            $existing = $this->queryOne(
                "SELECT id FROM peningkatan_detail WHERE peningkatan_id = ? AND penetapan_detail_id = ? LIMIT 1",
                [$pkId, $penetapanDetailId]
            );

            if ($existing) {
                $set = [];
                $params = [];
                foreach ($allowed as $f) {
                    if (array_key_exists($f, $data)) {
                        $set[]    = "{$f} = ?";
                        $params[] = $data[$f];
                    }
                }
                if (!empty($set)) {
                    $set[]    = "updated_at = NOW()";
                    $params[] = $pkId;
                    $params[] = $penetapanDetailId;
                    $this->exec("UPDATE peningkatan_detail SET " . implode(', ', $set) . " WHERE peningkatan_id = ? AND penetapan_detail_id = ?", $params);
                }
                return (int) $existing['id'];
            } else {
                $this->exec(
                    "INSERT INTO peningkatan_detail (peningkatan_id, kriteria_id, penetapan_detail_id, status_indikator, alasan_peningkatan, target_baru, indikator_baru, strategi_baru, nilai_kenaikan, nomor_sk, tanggal_sk, created_at, updated_at)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?, NOW(), NOW())",
                    [
                        $pkId,
                        $kriteriaId,
                        $penetapanDetailId,
                        $data['status_indikator']    ?? 'ditingkatkan',
                        $data['alasan_peningkatan']  ?? '',
                        $data['target_baru']         ?? '',
                        $data['indikator_baru']      ?? '',
                        $data['strategi_baru']       ?? '',
                        $data['nilai_kenaikan']      ?? '',
                        $data['nomor_sk']            ?? '',
                        $data['tanggal_sk']          ?? null,
                    ]
                );
                return (int) $this->getDb()->lastInsertId();
            }
        }

        $existing = $this->queryOne(
            "SELECT id FROM peningkatan_detail WHERE peningkatan_id = ? AND kriteria_id = ? LIMIT 1",
            [$pkId, $kriteriaId]
        );

        if ($existing) {
            $set = [];
            $params = [];
            foreach ($allowed as $f) {
                if (array_key_exists($f, $data)) {
                    $set[]    = "{$f} = ?";
                    $params[] = $data[$f];
                }
            }
            if (!empty($set)) {
                $set[]    = "updated_at = NOW()";
                $params[] = $pkId;
                $params[] = $kriteriaId;
                $this->exec("UPDATE peningkatan_detail SET " . implode(', ', $set) . " WHERE peningkatan_id = ? AND kriteria_id = ?", $params);
            }
            return (int) $existing['id'];
        } else {
            $this->exec(
                "INSERT INTO peningkatan_detail (peningkatan_id, kriteria_id, status_indikator, alasan_peningkatan, target_baru, indikator_baru, strategi_baru, nilai_kenaikan, nomor_sk, tanggal_sk, created_at, updated_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?, NOW(), NOW())",
                [
                    $pkId,
                    $kriteriaId,
                    $data['status_indikator']    ?? 'ditingkatkan',
                    $data['alasan_peningkatan']  ?? '',
                    $data['target_baru']         ?? '',
                    $data['indikator_baru']      ?? '',
                    $data['strategi_baru']       ?? '',
                    $data['nilai_kenaikan']      ?? '',
                    $data['nomor_sk']            ?? '',
                    $data['tanggal_sk']          ?? null,
                ]
            );
            return (int) $this->getDb()->lastInsertId();
        }
    }

    /**
     * Stats untuk dashboard
     */
    public function statsByUser(int $userId): array
    {
        $rows = $this->raw(
            "SELECT status, COUNT(*) AS cnt FROM peningkatan WHERE user_id = ? GROUP BY status",
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
