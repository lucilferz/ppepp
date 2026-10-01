<?php
/**
 * Model: PpeppProject
 * Platform PPEPP Fakultas
 */

class PpeppProject extends Model
{
    protected string $table = 'ppepp_project';

    /**
     * Ambil semua project milik user beserta nama tahun ajaran
     */
    public function findByUser(int $userId): array
    {
        $sql = "SELECT proj.*, ta.nama AS ta_nama, ta.aktif AS ta_aktif
                FROM ppepp_project proj
                JOIN tahun_ajaran ta ON ta.id = proj.tahun_ajaran_id
                WHERE proj.user_id = ?
                ORDER BY ta.nama DESC, proj.created_at DESC";
        return $this->query($sql, [$userId]);
    }

    /**
     * Cari project berdasarkan ID, pastikan milik user
     */
    public function findOwned(int $id, int $userId): ?array
    {
        $sql = "SELECT proj.*, ta.nama AS ta_nama
                FROM ppepp_project proj
                JOIN tahun_ajaran ta ON ta.id = proj.tahun_ajaran_id
                WHERE proj.id = ? AND proj.user_id = ?";
        return $this->queryOne($sql, [$id, $userId]);
    }

    /**
     * Cari atau buat project baru untuk user + tahun ajaran tertentu
     */
    public function findOrCreate(int $userId, int $tahunAjaranId): array
    {
        $existing = $this->queryOne(
            "SELECT * FROM ppepp_project WHERE user_id = ? AND tahun_ajaran_id = ?",
            [$userId, $tahunAjaranId]
        );

        if ($existing) {
            return $existing;
        }

        // Ambil nama TA untuk judul otomatis
        $stmt = $this->getDb()->prepare("SELECT nama FROM tahun_ajaran WHERE id = ?");
        $stmt->execute([$tahunAjaranId]);
        $taName = $stmt->fetchColumn();
        $judul = 'PPEPP ' . ($taName ?: '');

        $this->exec(
            "INSERT INTO ppepp_project (user_id, tahun_ajaran_id, judul, status) VALUES (?, ?, ?, 'aktif')",
            [$userId, $tahunAjaranId, $judul]
        );
        $id = (int) $this->getDb()->lastInsertId();
        return $this->find($id);
    }

    /**
     * Ambil project beserta statistik progres 5 tahap PPEPP
     */
    public function findWithStats(int $id, int $userId): ?array
    {
        $project = $this->findOwned($id, $userId);
        if (!$project) return null;

        $db = $this->getDb();

        // Count per tahap untuk project ini
        $stats = [];

        // Penetapan
        $stmt = $db->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='final' THEN 1 ELSE 0 END) as final
                              FROM penetapan WHERE ppepp_project_id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $stats['penetapan'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Pelaksanaan
        $stmt = $db->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='final' THEN 1 ELSE 0 END) as final
                              FROM pelaksanaan WHERE ppepp_project_id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $stats['pelaksanaan'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Evaluasi
        $stmt = $db->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='final' THEN 1 ELSE 0 END) as final
                              FROM evaluasi WHERE ppepp_project_id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $stats['evaluasi'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Pengendalian
        $stmt = $db->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='final' THEN 1 ELSE 0 END) as final
                              FROM pengendalian WHERE ppepp_project_id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $stats['pengendalian'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Peningkatan
        $stmt = $db->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='final' THEN 1 ELSE 0 END) as final
                              FROM peningkatan WHERE ppepp_project_id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $stats['peningkatan'] = $stmt->fetch(PDO::FETCH_ASSOC);

        $project['stats'] = $stats;

        // Hitung overall progress
        $totalFinal = 0;
        $maxSteps = 5;
        foreach ($stats as $s) {
            if ((int)($s['final'] ?? 0) > 0) $totalFinal++;
        }
        $project['progress_pct'] = (int)(($totalFinal / $maxSteps) * 100);
        $project['steps_done']   = $totalFinal;

        return $project;
    }

    /**
     * Ambil semua tahun ajaran yang belum memiliki project untuk user ini
     */
    public function getAvailableTahunAjaran(int $userId): array
    {
        return $this->query(
            "SELECT ta.* FROM tahun_ajaran ta
             WHERE ta.id NOT IN (
                 SELECT tahun_ajaran_id FROM ppepp_project WHERE user_id = ?
             )
             ORDER BY ta.nama DESC",
            [$userId]
        );
    }

    /**
     * Ambil semua tahun ajaran (untuk dropdown)
     */
    public function getAllTahunAjaran(): array
    {
        return $this->query("SELECT * FROM tahun_ajaran ORDER BY nama DESC");
    }
}
