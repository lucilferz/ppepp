<?php
/**
 * Model: TahunAjaran
 * Platform PPEPP Fakultas
 */

class TahunAjaran extends Model
{
    protected string $table = 'tahun_ajaran';

    /**
     * Ambil semua tahun ajaran diurutkan
     */
    public function findAll(): array
    {
        return $this->query("SELECT * FROM tahun_ajaran ORDER BY nama ASC, id ASC");
    }

    /**
     * Ambil tahun ajaran yang aktif
     */
    public function getActive(): ?array
    {
        return $this->queryOne("SELECT * FROM tahun_ajaran WHERE aktif = 1 LIMIT 1");
    }
}
