<?php
/**
 * Model: Prodi (Program Studi)
 * Platform PPEPP Fakultas
 */

class Prodi extends Model
{
    protected string $table = 'prodi';

    /**
     * Ambil semua prodi beserta info kaprodi dan jumlah dosen
     */
    public function getAllWithStats(): array
    {
        return $this->query("
            SELECT p.*,
                   u.nama_lengkap  AS kaprodi_nama,
                   u.email         AS kaprodi_email,
                   COUNT(DISTINCT m.id) AS jumlah_dosen
            FROM prodi p
            LEFT JOIN users u ON p.kaprodi_id = u.id
            LEFT JOIN users m ON m.prodi_id = p.id
            GROUP BY p.id
            ORDER BY p.nama ASC
        ");
    }

    /**
     * Ambil prodi beserta daftar dosennya
     */
    public function getWithDosen(int $prodiId): ?array
    {
        $prodi = $this->find($prodiId);
        if (!$prodi) return null;

        $dosen = $this->query("
            SELECT u.id, u.username, u.nama_lengkap, u.email, u.role, u.avatar_url, u.gemini_api_key
            FROM users u
            WHERE u.prodi_id = ?
            ORDER BY u.role DESC, u.nama_lengkap ASC
        ", [$prodiId]);

        $prodi['dosen'] = $dosen;
        return $prodi;
    }

    /**
     * Ambil dosen yang belum punya prodi (available untuk ditambah ke prodi baru)
     */
    public function getDosenTanpaProdi(): array
    {
        return $this->query("
            SELECT id, username, nama_lengkap, email, role
            FROM users
            WHERE prodi_id IS NULL
            ORDER BY nama_lengkap ASC
        ");
    }

    /**
     * Ambil semua dosen (untuk dropdown di form kaprodi)
     */
    public function getAllDosen(): array
    {
        return $this->query("
            SELECT id, nama_lengkap, email, role, prodi_id
            FROM users
            ORDER BY nama_lengkap ASC
        ");
    }

    /**
     * Tambah dosen ke prodi
     */
    public function addDosen(int $prodiId, int $userId): bool
    {
        return $this->exec("UPDATE users SET prodi_id = ? WHERE id = ?", [$prodiId, $userId]);
    }

    /**
     * Lepas dosen dari prodi
     */
    public function removeDosen(int $userId): bool
    {
        return $this->exec("UPDATE users SET prodi_id = NULL WHERE id = ?", [$userId]);
    }

    /**
     * Cari prodi berdasarkan kode
     */
    public function findByKode(string $kode): ?array
    {
        return $this->where('kode', strtoupper($kode));
    }
}
