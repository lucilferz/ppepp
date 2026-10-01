<?php
/**
 * Model: Penetapan
 * Platform PPEPP Fakultas
 */

class Penetapan extends Model
{
    protected string $table = 'penetapan';

    /**
     * Ambil penetapan beserta info tahun ajaran
     */
    public function findByUser(int $userId): array
    {
        $sql = "SELECT p.*, ta.nama as tahun_ajaran_nama, ta.semester
                FROM penetapan p
                JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                WHERE p.user_id = ?
                ORDER BY p.created_at DESC";
        return $this->query($sql, [$userId]);
    }

    /**
     * Ambil satu penetapan dengan detail lengkap
     */
    public function findWithDetails(int $id): ?array
    {
        $penetapan = $this->find($id);
        if (!$penetapan) return null;

        // Ambil detail per kriteria
        $sql = "SELECT pd.*, COALESCE(NULLIF(pd.kode, ''), k.kode) as kriteria_kode, k.nama as kriteria_nama
                FROM penetapan_detail pd
                JOIN kriteria k ON k.id = pd.kriteria_id
                WHERE pd.penetapan_id = ?
                ORDER BY k.urutan ASC, pd.id ASC";
        $penetapan['details'] = $this->query($sql, [$id]);

        // Ambil tahun ajaran
        $taModel = new class extends Model { protected string $table = 'tahun_ajaran'; };
        $penetapan['tahun_ajaran'] = $taModel->find($penetapan['tahun_ajaran_id']);

        // Parse list berkas SK
        $penetapan['berkas_list'] = [];
        if (!empty($penetapan['berkas_sk'])) {
            $parsed = json_decode($penetapan['berkas_sk'], true);
            if (is_array($parsed)) {
                $penetapan['berkas_list'] = $parsed;
            }
        }

        return $penetapan;
    }

    /**
     * Simpan / update detail penetapan per standar
     */
    public function saveDetail(int $penetapanId, int $kriteriaId, array $data, ?int $detailId = null): int
    {
        $aiAnalisis    = $data['ai_analisis'] ?? '';
        $kode          = strtoupper(trim($data['kode'] ?? ''));
        $targetCapaian = $data['target_capaian'] ?? '';
        $indikator     = $data['indikator'] ?? '';
        $strategi      = $data['strategi'] ?? '';
        $sumberDaya    = $data['sumber_daya'] ?? '';

        $db = $this->getDb();

        if ($detailId && $detailId > 0) {
            $stmt = $db->prepare(
                "UPDATE penetapan_detail 
                 SET kriteria_id = ?, kode = ?, target_capaian = ?, indikator = ?, strategi = ?, sumber_daya = ?, ai_analisis = ?, updated_at = NOW()
                 WHERE id = ? AND penetapan_id = ?"
            );
            $stmt->execute([$kriteriaId, $kode, $targetCapaian, $indikator, $strategi, $sumberDaya, $aiAnalisis, $detailId, $penetapanId]);
            return $detailId;
        }

        // Cek apakah ada record dengan penetapan_id + kriteria_id + kode yang sama
        if (!empty($kode)) {
            $stmt = $db->prepare("SELECT id FROM penetapan_detail WHERE penetapan_id = ? AND kriteria_id = ? AND kode = ? LIMIT 1");
            $stmt->execute([$penetapanId, $kriteriaId, $kode]);
            $existingId = $stmt->fetchColumn();
            if ($existingId) {
                $stmtUp = $db->prepare(
                    "UPDATE penetapan_detail 
                     SET target_capaian = ?, indikator = ?, strategi = ?, sumber_daya = ?, ai_analisis = ?, updated_at = NOW()
                     WHERE id = ?"
                );
                $stmtUp->execute([$targetCapaian, $indikator, $strategi, $sumberDaya, $aiAnalisis, $existingId]);
                return (int)$existingId;
            }
        }

        // INSERT standar baru
        $stmtIn = $db->prepare(
            "INSERT INTO penetapan_detail (penetapan_id, kriteria_id, kode, target_capaian, indikator, strategi, sumber_daya, ai_analisis, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );
        $stmtIn->execute([$penetapanId, $kriteriaId, $kode, $targetCapaian, $indikator, $strategi, $sumberDaya, $aiAnalisis]);
        return (int)$db->lastInsertId();
    }

    /**
     * Update hasil analisis AI pada detail
     */
    public function updateAIAnalisis(int $detailId, string $aiHasil): void
    {
        $this->exec(
            "UPDATE penetapan_detail SET ai_analisis = ? WHERE id = ?",
            [$aiHasil, $detailId]
        );
    }

    /**
     * Ambil semua tahun ajaran
     */
    public function getTahunAjaran(): array
    {
        return $this->query("SELECT * FROM tahun_ajaran ORDER BY nama ASC, id ASC");
    }

    /**
     * Menghitung dan menyelaraskan Tahun Ajaran Aktif secara realtime berdasarkan kalender
     */
    public function getRealtimeTahunAjaran(): array
    {
        $m = (int)date('n');
        $y = (int)date('Y');
        // Periode akademik baru dimulai bulan Juli (bulan 7)
        $namaRealtime = ($m >= 7) ? "{$y}/" . ($y + 1) : ($y - 1) . "/{$y}";

        $db = $this->getDb();
        
        // Cek apakah namaRealtime sudah ada di DB
        $stmt = $db->prepare("SELECT * FROM tahun_ajaran WHERE nama = ? LIMIT 1");
        $stmt->execute([$namaRealtime]);
        $ta = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ta) {
            $stmtIns = $db->prepare("INSERT INTO tahun_ajaran (nama, aktif) VALUES (?, 1)");
            $stmtIns->execute([$namaRealtime]);
            $id = (int)$db->lastInsertId();
            $ta = ['id' => $id, 'nama' => $namaRealtime, 'aktif' => 1];
        }

        // Reset status aktif di DB agar sinkron dengan kalender realtime
        $db->exec("UPDATE tahun_ajaran SET aktif = 0");
        $stmtAct = $db->prepare("UPDATE tahun_ajaran SET aktif = 1 WHERE id = ?");
        $stmtAct->execute([$ta['id']]);
        $ta['aktif'] = 1;

        return $ta;
    }

    /**
     * Statistik ringkasan penetapan
     */
    public function getStats(int $userId): array
    {
        return [
            'total'  => $this->count(['user_id' => $userId]),
            'draft'  => $this->count(['user_id' => $userId, 'status' => 'draft']),
            'final'  => $this->count(['user_id' => $userId, 'status' => 'final']),
        ];
    }
}
