<?php
/**
 * Model: Pelaksanaan
 * Platform PPEPP Fakultas
 */

class Pelaksanaan extends Model
{
    protected string $table = 'pelaksanaan';

    /**
     * Ambil semua pelaksanaan milik user beserta info penetapan
     */
    public function findByUser(int $userId): array
    {
        $sql = "SELECT pl.*, p.judul AS penetapan_judul, ta.nama AS tahun_ajaran_nama, ta.semester
                FROM pelaksanaan pl
                JOIN penetapan p ON p.id = pl.penetapan_id
                JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                WHERE pl.user_id = :uid
                ORDER BY pl.created_at DESC";
        return $this->raw($sql, [':uid' => $userId]);
    }

    /**
     * Ambil detail pelaksanaan beserta daftar bukti dan status checklist per kriteria
     */
    public function findWithDetail(int $id, int $userId): ?array
    {
        $pl = $this->find($id);
        if (!$pl || $pl['user_id'] != $userId) return null;

        // Info penetapan
        $db = $this->getDb();
        $stmtP = $db->prepare("SELECT p.*, ta.nama AS ta_nama, ta.semester FROM penetapan p JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id WHERE p.id = ?");
        $stmtP->execute([$pl['penetapan_id']]);
        $pl['penetapan'] = $stmtP->fetch(PDO::FETCH_ASSOC);

        // Kriteria dari penetapan_detail + status_pelaksanaan dari pelaksanaan_detail
        $stmtK = $db->prepare(
            "SELECT pd.*, 
                    COALESCE(NULLIF(pd.kode, ''), k.kode) AS kriteria_kode, 
                    k.nama AS kriteria_nama, 
                    k.deskripsi AS kriteria_deskripsi,
                    COALESCE(pld.status_pelaksanaan, 'belum') AS status_pelaksanaan,
                    pld.catatan_pelaksanaan,
                    pld.capaian_angka
             FROM penetapan_detail pd
             JOIN kriteria k ON k.id = pd.kriteria_id
             LEFT JOIN pelaksanaan_detail pld ON pld.pelaksanaan_id = ? AND pld.penetapan_detail_id = pd.id
             WHERE pd.penetapan_id = ?
             ORDER BY k.urutan ASC, pd.id ASC"
        );
        $stmtK->execute([$id, $pl['penetapan_id']]);
        $details = $stmtK->fetchAll(PDO::FETCH_ASSOC);

        // Bukti per indikator / standar target (penetapan_detail)
        $stmtB = $db->prepare(
            "SELECT * FROM pelaksanaan_bukti 
             WHERE pelaksanaan_id = ? 
               AND (penetapan_detail_id = ? OR (penetapan_detail_id IS NULL AND kriteria_id = ?)) 
             ORDER BY created_at DESC"
        );
        foreach ($details as &$d) {
            $stmtB->execute([$id, $d['id'], $d['kriteria_id']]);
            $d['bukti'] = $stmtB->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($d);

        $pl['details'] = $details;
        $pl['checklist_stats'] = $this->getChecklistStats($id, $pl['penetapan_id']);
        return $pl;
    }

    /**
     * Hitung statistik checklist pelaksanaan (Terlaksana, Proses, Belum)
     */
    public function getChecklistStats(int $pelaksanaanId, int $penetapanId): array
    {
        $db = $this->getDb();
        $stmt = $db->prepare(
            "SELECT pd.id, pd.kriteria_id, COALESCE(pld.status_pelaksanaan, 'belum') AS status_pelaksanaan
             FROM penetapan_detail pd
             LEFT JOIN pelaksanaan_detail pld ON pld.pelaksanaan_id = ? AND pld.penetapan_detail_id = pd.id
             WHERE pd.penetapan_id = ?"
        );
        $stmt->execute([$pelaksanaanId, $penetapanId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stats = ['total' => count($rows), 'terlaksana' => 0, 'proses' => 0, 'belum' => 0, 'pct' => 0];
        foreach ($rows as $r) {
            $st = $r['status_pelaksanaan'] ?? 'belum';
            if (isset($stats[$st])) {
                $stats[$st]++;
            } else {
                $stats['belum']++;
            }
        }
        if ($stats['total'] > 0) {
            $stats['pct'] = round(($stats['terlaksana'] / $stats['total']) * 100);
        }
        return $stats;
    }

    /**
     * Simpan status & catatan checklist pelaksanaan per indikator / standar
     */
    public function saveDetailStatus(int $pelaksanaanId, int $kriteriaId, string $status, ?string $catatan = null, ?int $penetapanDetailId = null, ?string $capaianAngka = null): bool
    {
        if (!in_array($status, ['belum', 'proses', 'terlaksana'])) {
            $status = 'belum';
        }
        $db = $this->getDb();

        if ($penetapanDetailId && $penetapanDetailId > 0) {
            $stmt = $db->prepare(
                "SELECT id FROM pelaksanaan_detail WHERE pelaksanaan_id = ? AND penetapan_detail_id = ? LIMIT 1"
            );
            $stmt->execute([$pelaksanaanId, $penetapanDetailId]);
            $existingId = $stmt->fetchColumn();

            if ($existingId) {
                $stmtUp = $db->prepare(
                    "UPDATE pelaksanaan_detail 
                     SET status_pelaksanaan = ?, 
                         catatan_pelaksanaan = COALESCE(?, catatan_pelaksanaan),
                         capaian_angka = ?,
                         updated_at = NOW()
                     WHERE id = ?"
                );
                return $stmtUp->execute([$status, $catatan, $capaianAngka, $existingId]);
            } else {
                $stmtIn = $db->prepare(
                    "INSERT INTO pelaksanaan_detail (pelaksanaan_id, kriteria_id, penetapan_detail_id, status_pelaksanaan, catatan_pelaksanaan, capaian_angka, updated_at)
                     VALUES (?, ?, ?, ?, ?, ?, NOW())"
                );
                return $stmtIn->execute([$pelaksanaanId, $kriteriaId, $penetapanDetailId, $status, $catatan, $capaianAngka]);
            }
        }

        // Fallback jika penetapanDetailId tidak diberikan
        $stmt = $db->prepare(
            "SELECT id FROM pelaksanaan_detail WHERE pelaksanaan_id = ? AND kriteria_id = ? LIMIT 1"
        );
        $stmt->execute([$pelaksanaanId, $kriteriaId]);
        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            $stmtUp = $db->prepare(
                "UPDATE pelaksanaan_detail 
                 SET status_pelaksanaan = ?, 
                     catatan_pelaksanaan = COALESCE(?, catatan_pelaksanaan),
                     capaian_angka = ?,
                     updated_at = NOW()
                 WHERE id = ?"
            );
            return $stmtUp->execute([$status, $catatan, $capaianAngka, $existingId]);
        } else {
            $stmtIn = $db->prepare(
                "INSERT INTO pelaksanaan_detail (pelaksanaan_id, kriteria_id, status_pelaksanaan, catatan_pelaksanaan, capaian_angka, updated_at)
                 VALUES (?, ?, ?, ?, ?, NOW())"
            );
            return $stmtIn->execute([$pelaksanaanId, $kriteriaId, $status, $catatan, $capaianAngka]);
        }
    }


    /**
     * Stats untuk dashboard
     */
    public function statsByUser(int $userId): array
    {
        $rows = $this->raw(
            "SELECT status, COUNT(*) AS cnt FROM pelaksanaan WHERE user_id = ? GROUP BY status",
            [$userId]
        );
        $stats = ['total' => 0, 'draft' => 0, 'final' => 0];
        foreach ($rows as $r) {
            $stats[$r['status']] = (int)$r['cnt'];
            $stats['total'] += (int)$r['cnt'];
        }
        return $stats;
    }

    /**
     * Tambah bukti link per indikator / standar target
     */
    public function addBukti(int $pelaksanaanId, int $kriteriaId, string $judul, string $url, string $keterangan = '', ?int $penetapanDetailId = null): int|false
    {
        $db = $this->getDb();
        $stmt = $db->prepare(
            "INSERT INTO pelaksanaan_bukti (pelaksanaan_id, kriteria_id, penetapan_detail_id, judul_bukti, url_link, keterangan, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );
        $ok = $stmt->execute([$pelaksanaanId, $kriteriaId, $penetapanDetailId, $judul, $url, $keterangan]);
        return $ok ? (int)$db->lastInsertId() : false;
    }

    /**
     * Hapus bukti
     */
    public function deleteBukti(int $buktiId, int $pelaksanaanId): bool
    {
        $db = $this->getDb();
        $stmt = $db->prepare("DELETE FROM pelaksanaan_bukti WHERE id = ? AND pelaksanaan_id = ?");
        return $stmt->execute([$buktiId, $pelaksanaanId]);
    }

    /**
     * Finalisasi pelaksanaan
     */
    public function finalize(int $id): bool
    {
        return $this->update($id, ['status' => 'final']);
    }
}
