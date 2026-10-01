<?php
/**
 * Base Model - Database Handler (PDO)
 * Platform PPEPP Fakultas
 */

class Model
{
    protected static ?PDO $db = null;
    protected string $table = '';
    protected string $primaryKey = 'id';

    /**
     * Dapatkan koneksi PDO (singleton)
     */
    protected function db(): PDO
    {
        if (self::$db === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$db = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                die('<div style="padding:20px;font-family:monospace;color:red;background:#fff0f0;border:1px solid red;margin:20px;">
                    <b>Database Error:</b> ' . htmlspecialchars($e->getMessage()) . '</div>');
            }
        }
        return self::$db;
    }

    /**
     * Ambil semua data dari tabel
     */
    public function all(string $orderBy = 'id', string $dir = 'ASC'): array
    {
        $stmt = $this->db()->prepare(
            "SELECT * FROM `{$this->table}` ORDER BY `{$orderBy}` {$dir}"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ambil satu data berdasarkan ID
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Ambil satu data dengan kondisi WHERE
     */
    public function where(string $column, mixed $value): ?array
    {
        $stmt = $this->db()->prepare(
            "SELECT * FROM `{$this->table}` WHERE `{$column}` = ? LIMIT 1"
        );
        $stmt->execute([$value]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Ambil banyak data dengan kondisi WHERE
     */
    public function findWhere(array $conditions, string $orderBy = 'id', string $dir = 'ASC'): array
    {
        $wheres = [];
        $values = [];
        foreach ($conditions as $col => $val) {
            $wheres[] = "`{$col}` = ?";
            $values[] = $val;
        }
        $sql = "SELECT * FROM `{$this->table}` WHERE " . implode(' AND ', $wheres) . " ORDER BY `{$orderBy}` {$dir}";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($values);
        return $stmt->fetchAll();
    }

    /**
     * Insert data baru
     */
    public function insert(array $data): int
    {
        $cols   = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db()->prepare(
            "INSERT INTO `{$this->table}` (`{$cols}`) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));
        return (int) $this->db()->lastInsertId();
    }

    /**
     * Update data berdasarkan ID
     */
    public function update(int $id, array $data): bool
    {
        $sets = implode(', ', array_map(fn($col) => "`{$col}` = ?", array_keys($data)));
        $stmt = $this->db()->prepare(
            "UPDATE `{$this->table}` SET {$sets} WHERE `{$this->primaryKey}` = ?"
        );
        return $stmt->execute([...array_values($data), $id]);
    }

    /**
     * Hapus data berdasarkan ID
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db()->prepare(
            "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Query custom
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Query custom (satu baris)
     */
    public function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    /**
     * Jalankan query tanpa return
     */
    public function exec(string $sql, array $params = []): bool
    {
        $stmt = $this->db()->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Count rows
     */
    public function count(array $conditions = []): int
    {
        if (empty($conditions)) {
            $stmt = $this->db()->prepare("SELECT COUNT(*) FROM `{$this->table}`");
            $stmt->execute();
        } else {
            $wheres = [];
            $values = [];
            foreach ($conditions as $col => $val) {
                $wheres[] = "`{$col}` = ?";
                $values[] = $val;
            }
            $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE " . implode(' AND ', $wheres);
            $stmt = $this->db()->prepare($sql);
            $stmt->execute($values);
        }
        return (int) $stmt->fetchColumn();
    }

    /**
     * Alias publik untuk mendapatkan instance PDO
     */
    public function getDb(): PDO
    {
        return $this->db();
    }

    /**
     * Alias untuk query() - raw SQL
     */
    public function raw(string $sql, array $params = []): array
    {
        return $this->query($sql, $params);
    }

    /**
     * Alias create() → insert()
     */
    public function create(array $data): int
    {
        return $this->insert($data);
    }
}
