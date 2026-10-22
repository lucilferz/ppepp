<?php
/**
 * Model: TugasDokumen
 * Menangani delegasi tugas penyusunan dokumen PPEPP dari Kaprodi ke Dosen
 */

class TugasDokumen extends Model
{
    protected string $table = 'tugas_dokumen';

    /**
     * Tugaskan dokumen ke dosen (create atau update assignment)
     */
    public function assignTask(
        int $kaprodiId,
        int $dosenId,
        int $projectId,
        string $stage,
        ?int $documentId,
        string $judulTugas,
        ?string $instruksi = null,
        ?string $deadline = null
    ): int {
        $db = $this->getDb();

        // Cek apakah sudah ada assignment untuk project & stage ini
        $stmtCheck = $db->prepare("SELECT id FROM tugas_dokumen WHERE project_id = ? AND stage = ? LIMIT 1");
        $stmtCheck->execute([$projectId, $stage]);
        $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $taskId = (int)$existing['id'];
            $stmtUpd = $db->prepare("UPDATE tugas_dokumen SET
                kaprodi_id = ?,
                dosen_id = ?,
                document_id = COALESCE(?, document_id),
                judul_tugas = ?,
                instruksi = ?,
                deadline = ?,
                status = 'assigned',
                catatan_revisi = NULL,
                assigned_at = CURRENT_TIMESTAMP,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?");
            $stmtUpd->execute([
                $kaprodiId,
                $dosenId,
                $documentId,
                $judulTugas,
                $instruksi,
                !empty($deadline) ? $deadline : null,
                $taskId
            ]);
            return $taskId;
        }

        $stmtIns = $db->prepare("INSERT INTO tugas_dokumen 
            (kaprodi_id, dosen_id, project_id, stage, document_id, judul_tugas, instruksi, deadline, status, assigned_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'assigned', CURRENT_TIMESTAMP)");
        $stmtIns->execute([
            $kaprodiId,
            $dosenId,
            $projectId,
            $stage,
            $documentId,
            $judulTugas,
            $instruksi,
            !empty($deadline) ? $deadline : null,
        ]);

        return (int)$db->lastInsertId();
    }

    /**
     * Ambil assignment berdasarkan project_id dan stage
     */
    public function getByProjectAndStage(int $projectId, string $stage): ?array
    {
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT t.*, 
                u_dos.nama_lengkap AS dosen_nama, u_dos.email AS dosen_email, u_dos.username AS dosen_nip,
                u_kap.nama_lengkap AS kaprodi_nama, u_kap.email AS kaprodi_email
            FROM tugas_dokumen t
            JOIN users u_dos ON u_dos.id = t.dosen_id
            JOIN users u_kap ON u_kap.id = t.kaprodi_id
            WHERE t.project_id = ? AND t.stage = ?
            LIMIT 1");
        $stmt->execute([$projectId, $stage]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Ambil semua assignment untuk kumpulan project IDs (dipakai di halaman pending kaprodi)
     */
    public function getMapByProjectIds(array $projectIds): array
    {
        if (empty($projectIds)) {
            return [];
        }
        $db = $this->getDb();
        $placeholders = implode(',', array_fill(0, count($projectIds), '?'));
        $stmt = $db->prepare("SELECT t.*, 
                u_dos.nama_lengkap AS dosen_nama, u_dos.email AS dosen_email, u_dos.username AS dosen_nip,
                u_kap.nama_lengkap AS kaprodi_nama
            FROM tugas_dokumen t
            JOIN users u_dos ON u_dos.id = t.dosen_id
            JOIN users u_kap ON u_kap.id = t.kaprodi_id
            WHERE t.project_id IN ($placeholders)");
        $stmt->execute($projectIds);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $map = [];
        foreach ($rows as $r) {
            $key = $r['project_id'] . '_' . $r['stage'];
            $map[$key] = $r;
        }
        return $map;
    }

    /**
     * Ambil tugas prioritas yang ditugaskan kepada seorang dosen
     */
    public function getPriorityTasksForDosen(int $dosenId): array
    {
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT t.*, 
                u_kap.nama_lengkap AS kaprodi_nama, u_kap.email AS kaprodi_email,
                proj.judul AS project_judul, proj.tahun_ajaran_id,
                ta.nama AS ta_nama
            FROM tugas_dokumen t
            JOIN users u_kap ON u_kap.id = t.kaprodi_id
            JOIN ppepp_project proj ON proj.id = t.project_id
            JOIN tahun_ajaran ta ON ta.id = proj.tahun_ajaran_id
            WHERE t.dosen_id = ?
            ORDER BY 
                CASE 
                    WHEN t.status = 'revision' THEN 1
                    WHEN t.status = 'assigned' THEN 2
                    WHEN t.status = 'submitted' THEN 3
                    WHEN t.status = 'approved' THEN 4
                    ELSE 5
                END ASC,
                t.assigned_at DESC");
        $stmt->execute([$dosenId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Dosen mengirim tugas yang telah dikerjakan ke Kaprodi
     */
    public function submitTask(int $taskId, int $dosenId, ?string $catatanDosen = null): bool
    {
        $db = $this->getDb();
        $stmt = $db->prepare("UPDATE tugas_dokumen SET
            status = 'submitted',
            catatan_dosen = ?,
            submitted_at = CURRENT_TIMESTAMP,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = ? AND dosen_id = ?");
        return $stmt->execute([$catatanDosen, $taskId, $dosenId]);
    }

    /**
     * Kaprodi menyetujui / meng-ACC tugas
     */
    public function approveTask(int $taskId, int $kaprodiId): bool
    {
        $db = $this->getDb();
        $stmt = $db->prepare("UPDATE tugas_dokumen SET
            status = 'approved',
            reviewed_at = CURRENT_TIMESTAMP,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = ? AND (kaprodi_id = ? OR (SELECT role FROM users WHERE id = ?) IN ('kaprodi', 'dekan'))");
        return $stmt->execute([$taskId, $kaprodiId, $kaprodiId]);
    }

    /**
     * Kaprodi meminta revisi beserta note catatan revisi
     */
    public function requestRevision(int $taskId, int $kaprodiId, string $catatanRevisi): bool
    {
        $db = $this->getDb();
        $stmt = $db->prepare("UPDATE tugas_dokumen SET
            status = 'revision',
            catatan_revisi = ?,
            reviewed_at = CURRENT_TIMESTAMP,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = ? AND (kaprodi_id = ? OR (SELECT role FROM users WHERE id = ?) IN ('kaprodi', 'dekan'))");
        return $stmt->execute([$catatanRevisi, $taskId, $kaprodiId, $kaprodiId]);
    }

    /**
     * Kaprodi membatalkan penugasan
     */
    public function cancelTask(int $taskId, int $kaprodiId): bool
    {
        $db = $this->getDb();
        $stmt = $db->prepare("DELETE FROM tugas_dokumen WHERE id = ? AND (kaprodi_id = ? OR (SELECT role FROM users WHERE id = ?) IN ('kaprodi', 'dekan'))");
        return $stmt->execute([$taskId, $kaprodiId, $kaprodiId]);
    }

    /**
     * Hubungkan document_id baru jika dokumen baru saja dibuat oleh dosen/kaprodi
     */
    public function updateDocumentId(int $projectId, string $stage, int $documentId): void
    {
        $db = $this->getDb();
        $stmt = $db->prepare("UPDATE tugas_dokumen SET document_id = ? WHERE project_id = ? AND stage = ? AND (document_id IS NULL OR document_id = 0)");
        $stmt->execute([$documentId, $projectId, $stage]);
    }

    /**
     * Cek apakah user memiliki akses ke dokumen (sebagai pemilik asli ATAU dosen yang ditugaskan)
     */
    public static function canAccessDocument(int $userId, string $stage, ?int $projectId = null, ?int $documentId = null): bool
    {
        $db = (new Penetapan())->getDb();

        // 1. Kaprodi / Dekan selalu memiliki izin akses
        $stmtUser = $db->prepare("SELECT role FROM users WHERE id = ?");
        $stmtUser->execute([$userId]);
        $u = $stmtUser->fetch(PDO::FETCH_ASSOC);
        if ($u && in_array($u['role'], ['kaprodi', 'dekan'])) {
            return true;
        }

        // 2. Cek jika user ditugaskan di tugas_dokumen
        $sql = "SELECT id FROM tugas_dokumen WHERE dosen_id = ? AND stage = ?";
        $params = [$userId, $stage];

        if ($documentId !== null && $documentId > 0) {
            $sql .= " AND (document_id = ? OR project_id = ?)";
            $params[] = $documentId;
            $params[] = $projectId ?? 0;
        } elseif ($projectId !== null && $projectId > 0) {
            $sql .= " AND project_id = ?";
            $params[] = $projectId;
        }

        $stmt = $db->prepare($sql . " LIMIT 1");
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }
}
