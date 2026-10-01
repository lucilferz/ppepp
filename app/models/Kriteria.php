<?php
/**
 * Model: Kriteria
 * Platform PPEPP Fakultas
 * Kriteria sekarang berbasis prodi_id (bukan user_id)
 */

class Kriteria extends Model
{
    protected string $table = 'kriteria';

    /**
     * 9 Kriteria default BAN-PT untuk prodi baru
     */
    public const DEFAULT_KRITERIA = [
        ['kode' => 'C1', 'nama' => 'Visi, Misi, Tujuan, dan Strategi',         'urutan' => 1],
        ['kode' => 'C2', 'nama' => 'Tata Pamong, Tata Kelola, dan Kerjasama',  'urutan' => 2],
        ['kode' => 'C3', 'nama' => 'Mahasiswa',                                 'urutan' => 3],
        ['kode' => 'C4', 'nama' => 'Sumber Daya Manusia',                       'urutan' => 4],
        ['kode' => 'C5', 'nama' => 'Keuangan, Sarana, dan Prasarana',           'urutan' => 5],
        ['kode' => 'C6', 'nama' => 'Pendidikan',                                'urutan' => 6],
        ['kode' => 'C7', 'nama' => 'Penelitian',                                'urutan' => 7],
        ['kode' => 'C8', 'nama' => 'Pengabdian kepada Masyarakat',              'urutan' => 8],
        ['kode' => 'C9', 'nama' => 'Luaran dan Capaian Tridharma',              'urutan' => 9],
    ];

    /**
     * Ambil semua kriteria milik prodi tertentu
     * (Primary method — berbasis prodi_id)
     */
    public function findByProdi(int $prodiId, bool $activeOnly = true): array
    {
        $sql = "SELECT * FROM kriteria WHERE prodi_id = ?";
        if ($activeOnly) {
            $sql .= " AND aktif = 1";
        }
        $sql .= " ORDER BY urutan ASC, id ASC";
        return $this->query($sql, [$prodiId]);
    }

    /**
     * [Compat] findByUser sekarang mengambil kriteria berdasarkan prodi user.
     * Jika user tidak punya prodi_id, fallback ke array kosong.
     */
    public function findByUser(int $userId, bool $activeOnly = true): array
    {
        // Ambil prodi_id dari users
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT prodi_id FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || empty($user['prodi_id'])) {
            return [];
        }
        return $this->findByProdi((int)$user['prodi_id'], $activeOnly);
    }

    /**
     * Cek apakah kode kriteria sudah ada untuk prodi ini
     */
    public function kodeExists(int $prodiId, string $kode, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM kriteria WHERE prodi_id = ? AND kode = ?";
        $params = [$prodiId, $kode];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        return $this->queryOne($sql, $params) !== null;
    }

    /**
     * Jumlah kriteria aktif per prodi
     */
    public function countByProdi(int $prodiId): int
    {
        return $this->count(['prodi_id' => $prodiId, 'aktif' => 1]);
    }

    /**
     * [Compat] Alias untuk backward compat
     */
    public function countByUser(int $userId): int
    {
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT prodi_id FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user || empty($user['prodi_id'])) return 0;
        return $this->countByProdi((int)$user['prodi_id']);
    }

    /**
     * Buat 9 kriteria default untuk prodi baru
     */
    public function seedForProdi(int $prodiId): int
    {
        $count = 0;
        foreach (self::DEFAULT_KRITERIA as $k) {
            // Cek sudah ada belum
            $existing = $this->queryOne(
                "SELECT id FROM kriteria WHERE prodi_id = ? AND kode = ? LIMIT 1",
                [$prodiId, $k['kode']]
            );
            if (!$existing) {
                $this->insert([
                    'prodi_id' => $prodiId,
                    'kode'     => $k['kode'],
                    'nama'     => $k['nama'],
                    'urutan'   => $k['urutan'],
                    'aktif'    => 1,
                ]);
                $count++;
            }
        }
        return $count;
    }
}
