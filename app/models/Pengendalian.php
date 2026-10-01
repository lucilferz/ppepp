<?php
/**
 * Model: Pengendalian (RTL & Koreksi Standar)
 * Platform PPEPP Fakultas
 */

class Pengendalian extends Model
{
    protected string $table = 'pengendalian';

    /**
     * Ambil semua pengendalian milik user beserta info penetapan + evaluasi
     */
    public function findByUser(int $userId): array
    {
        $sql = "SELECT pg.*,
                       COALESCE(p.judul, pev.judul, '—') AS penetapan_judul,
                       COALESCE(ev.judul, '—') AS evaluasi_judul,
                       COALESCE(ta.nama, ta_ev.nama, '—') AS tahun_ajaran_nama,
                       COALESCE(ta.semester, ta_ev.semester, '') AS semester
                FROM pengendalian pg
                LEFT JOIN penetapan p ON p.id = pg.penetapan_id
                LEFT JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                LEFT JOIN evaluasi ev ON ev.id = pg.evaluasi_id
                LEFT JOIN penetapan pev ON pev.id = ev.penetapan_id
                LEFT JOIN tahun_ajaran ta_ev ON ta_ev.id = pev.tahun_ajaran_id
                WHERE pg.user_id = :uid
                ORDER BY pg.created_at DESC";
        return $this->raw($sql, [':uid' => $userId]);
    }

    /**
     * Ambil satu pengendalian dengan detail lengkap
     */
    public function findWithDetails(int $id, int $userId): ?array
    {
        $pg = $this->find($id);
        if (!$pg || $pg['user_id'] != $userId) return null;

        $db = $this->getDb();

        // 1. Info Penetapan Acuan
        $penetapanId = (int)($pg['penetapan_id'] ?? 0);
        $evaluasiId  = (int)($pg['evaluasi_id'] ?? 0);

        // Jika penetapan_id belum tersimpan langsung, ambil dari evaluasi
        if ($penetapanId <= 0 && $evaluasiId > 0) {
            $stmtGetPen = $db->prepare("SELECT penetapan_id FROM evaluasi WHERE id = ?");
            $stmtGetPen->execute([$evaluasiId]);
            $penetapanId = (int)$stmtGetPen->fetchColumn();
        }

        // Info Penetapan & Tahun Ajaran
        $stmtP = $db->prepare(
            "SELECT p.*, ta.nama AS ta_nama, ta.semester
             FROM penetapan p
             JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
             WHERE p.id = ?"
        );
        $stmtP->execute([$penetapanId]);
        $pg['penetapan'] = $stmtP->fetch(PDO::FETCH_ASSOC) ?: [];

        // 2. Info Evaluasi Terkait (Cari evaluasi yang merujuk penetapan ini jika belum terisi)
        if ($evaluasiId <= 0 && $penetapanId > 0) {
            $stmtFindEv = $db->prepare("SELECT id FROM evaluasi WHERE penetapan_id = ? AND user_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmtFindEv->execute([$penetapanId, $userId]);
            $evaluasiId = (int)$stmtFindEv->fetchColumn();
            if ($evaluasiId > 0) {
                $db->prepare("UPDATE pengendalian SET evaluasi_id = ? WHERE id = ?")->execute([$evaluasiId, $id]);
                $pg['evaluasi_id'] = $evaluasiId;
            }
        }

        $stmtE = $db->prepare("SELECT * FROM evaluasi WHERE id = ?");
        $stmtE->execute([$evaluasiId]);
        $pg['evaluasi'] = $stmtE->fetch(PDO::FETCH_ASSOC) ?: [];

        // 3. Detail per kriteria: Target Penetapan + Status & Evaluasi Indikator + Form RTL
        $stmtK = $db->prepare(
            "SELECT pd.*,
                    COALESCE(NULLIF(pd.kode, ''), k.kode) AS kriteria_kode,
                    k.nama AS kriteria_nama,
                    k.deskripsi AS kriteria_deskripsi,
                    COALESCE(ed.status_capaian, 'belum_tercapai') AS status_capaian,
                    ed.evaluasi_teks,
                    ed.hasil_aktual,
                    ed.analisis_gap,
                    ed.catatan AS eval_catatan,
                    pgd.id AS rtl_id,
                    pgd.rencana_tindak_lanjut,
                    pgd.akar_masalah,
                    pgd.koreksi_standar,
                    pgd.penanggung_jawab,
                    pgd.target_waktu,
                    COALESCE(pgd.status_tindakan, 'belum') AS status_tindakan,
                    pgd.indikator_sukses,
                    pgd.ai_ekstrak
             FROM penetapan_detail pd
             JOIN kriteria k ON k.id = pd.kriteria_id
             LEFT JOIN evaluasi_detail ed ON ed.evaluasi_id = ? AND (ed.penetapan_detail_id = pd.id OR (ed.penetapan_detail_id IS NULL AND ed.kriteria_id = pd.kriteria_id))
             LEFT JOIN pengendalian_detail pgd ON pgd.pengendalian_id = ? AND (pgd.penetapan_detail_id = pd.id OR (pgd.penetapan_detail_id IS NULL AND pgd.kriteria_id = pd.kriteria_id))
             WHERE pd.penetapan_id = ?
             ORDER BY k.urutan ASC, pd.id ASC"
        );
        $stmtK->execute([$evaluasiId, $id, $penetapanId]);
        $pg['details'] = $stmtK->fetchAll(PDO::FETCH_ASSOC);

        // 4. Parse Berkas Koreksi Standar JSON
        if (!empty($pg['berkas_koreksi'])) {
            $parsed = json_decode($pg['berkas_koreksi'], true);
            $pg['berkas_list'] = is_array($parsed) ? $parsed : [];
        } else {
            $pg['berkas_list'] = [];
        }

        // 5. Notulensi / Berkas Tambahan lama
        $stmtN = $db->prepare("SELECT * FROM pengendalian_notulensi WHERE pengendalian_id = ? ORDER BY created_at DESC");
        $stmtN->execute([$id]);
        $pg['notulensi'] = $stmtN->fetchAll(PDO::FETCH_ASSOC);

        return $pg;
    }

    /**
     * Simpan / update RTL per kriteria / indikator
     */
    public function saveDetail(int $pgId, int $kriteriaId, array $data, ?int $penetapanDetailId = null): int
    {
        $allowed = ['rencana_tindak_lanjut', 'akar_masalah', 'koreksi_standar', 'penanggung_jawab', 'target_waktu', 'status_tindakan', 'indikator_sukses', 'ai_ekstrak', 'notulensi_id'];

        if ($penetapanDetailId && $penetapanDetailId > 0) {
            $existing = $this->queryOne(
                "SELECT id FROM pengendalian_detail WHERE pengendalian_id = ? AND penetapan_detail_id = ? LIMIT 1",
                [$pgId, $penetapanDetailId]
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
                    $params[] = $pgId;
                    $params[] = $penetapanDetailId;
                    $this->exec("UPDATE pengendalian_detail SET " . implode(', ', $set) . " WHERE pengendalian_id = ? AND penetapan_detail_id = ?", $params);
                }
                return (int) $existing['id'];
            } else {
                $this->exec(
                    "INSERT INTO pengendalian_detail (pengendalian_id, kriteria_id, penetapan_detail_id, rencana_tindak_lanjut, akar_masalah, koreksi_standar, penanggung_jawab, target_waktu, status_tindakan, indikator_sukses, ai_ekstrak, notulensi_id, created_at, updated_at)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?, NOW(), NOW())",
                    [
                        $pgId,
                        $kriteriaId,
                        $penetapanDetailId,
                        $data['rencana_tindak_lanjut'] ?? '',
                        $data['akar_masalah']          ?? '',
                        $data['koreksi_standar']       ?? '',
                        $data['penanggung_jawab']      ?? '',
                        $data['target_waktu']          ?? '',
                        $data['status_tindakan']       ?? 'belum',
                        $data['indikator_sukses']      ?? '',
                        $data['ai_ekstrak']            ?? '',
                        $data['notulensi_id']          ?? null,
                    ]
                );
                return (int) $this->getDb()->lastInsertId();
            }
        }

        $existing = $this->queryOne(
            "SELECT id FROM pengendalian_detail WHERE pengendalian_id = ? AND kriteria_id = ? LIMIT 1",
            [$pgId, $kriteriaId]
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
                $params[] = $pgId;
                $params[] = $kriteriaId;
                $this->exec("UPDATE pengendalian_detail SET " . implode(', ', $set) . " WHERE pengendalian_id = ? AND kriteria_id = ?", $params);
            }
            return (int) $existing['id'];
        } else {
            $this->exec(
                "INSERT INTO pengendalian_detail (pengendalian_id, kriteria_id, rencana_tindak_lanjut, akar_masalah, koreksi_standar, penanggung_jawab, target_waktu, status_tindakan, indikator_sukses, ai_ekstrak, notulensi_id, created_at, updated_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?, NOW(), NOW())",
                [
                    $pgId,
                    $kriteriaId,
                    $data['rencana_tindak_lanjut'] ?? '',
                    $data['akar_masalah']          ?? '',
                    $data['koreksi_standar']       ?? '',
                    $data['penanggung_jawab']      ?? '',
                    $data['target_waktu']          ?? '',
                    $data['status_tindakan']       ?? 'belum',
                    $data['indikator_sukses']      ?? '',
                    $data['ai_ekstrak']            ?? '',
                    $data['notulensi_id']          ?? null,
                ]
            );
            return (int) $this->getDb()->lastInsertId();
        }
    }

    /**
     * Update single field di pengendalian_detail via AJAX
     */
    public function saveDetailField(int $pgId, int $kriteriaId, string $field, mixed $value, ?int $penetapanDetailId = null): bool
    {
        $allowed = ['rencana_tindak_lanjut', 'akar_masalah', 'koreksi_standar', 'penanggung_jawab', 'target_waktu', 'status_tindakan', 'indikator_sukses', 'ai_ekstrak'];
        if (!in_array($field, $allowed)) return false;

        $db = $this->getDb();

        if ($penetapanDetailId && $penetapanDetailId > 0) {
            $existing = $this->queryOne(
                "SELECT id FROM pengendalian_detail WHERE pengendalian_id = ? AND penetapan_detail_id = ? LIMIT 1",
                [$pgId, $penetapanDetailId]
            );

            if ($existing) {
                $stmt = $db->prepare("UPDATE pengendalian_detail SET {$field} = ?, updated_at = NOW() WHERE pengendalian_id = ? AND penetapan_detail_id = ?");
                return $stmt->execute([$value, $pgId, $penetapanDetailId]);
            } else {
                $stmt = $db->prepare("INSERT INTO pengendalian_detail (pengendalian_id, kriteria_id, penetapan_detail_id, {$field}, created_at, updated_at) VALUES (?,?,?,?, NOW(), NOW())");
                return $stmt->execute([$pgId, $kriteriaId, $penetapanDetailId, $value]);
            }
        }

        $existing = $this->queryOne(
            "SELECT id FROM pengendalian_detail WHERE pengendalian_id = ? AND kriteria_id = ? LIMIT 1",
            [$pgId, $kriteriaId]
        );

        if ($existing) {
            $stmt = $db->prepare("UPDATE pengendalian_detail SET {$field} = ?, updated_at = NOW() WHERE pengendalian_id = ? AND kriteria_id = ?");
            return $stmt->execute([$value, $pgId, $kriteriaId]);
        } else {
            $stmt = $db->prepare("INSERT INTO pengendalian_detail (pengendalian_id, kriteria_id, {$field}, created_at, updated_at) VALUES (?,?,?, NOW(), NOW())");
            return $stmt->execute([$pgId, $kriteriaId, $value]);
        }
    }

    /**
     * Stats untuk dashboard
     */
    public function statsByUser(int $userId): array
    {
        $rows = $this->raw(
            "SELECT status, COUNT(*) AS cnt FROM pengendalian WHERE user_id = ? GROUP BY status",
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
