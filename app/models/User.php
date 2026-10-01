<?php
/**
 * Model: User
 * Platform PPEPP Fakultas — Per-Dosen System
 */

class User extends Model
{
    protected string $table = 'users';

    /**
     * Autentikasi user berdasarkan username & password
     * Mendukung bcrypt (dari notulensi_db), MD5, SHA256, & plain text
     */
    public function authenticate(string $usernameOrEmail, string $password): ?array
    {
        // Support login via username ATAU email
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        $storedPassword = $user['password'];

        // 1. Standard PHP password_verify (Bcrypt — dari notulensi_db)
        if (password_verify($password, $storedPassword)) {
            return $user;
        }

        // 2. MD5 Hash (format lama)
        if (md5($password) === $storedPassword) {
            return $user;
        }

        // 3. SHA256
        if (hash('sha256', $password) === $storedPassword) {
            return $user;
        }

        // 4. SHA1
        if (sha1($password) === $storedPassword) {
            return $user;
        }

        // 5. Plain text (dev/fallback)
        if ($password === $storedPassword) {
            return $user;
        }

        return null;
    }

    /**
     * Cari user berdasarkan email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email);
    }

    /**
     * Cari user berdasarkan Google ID
     */
    public function findByGoogleId(string $googleId): ?array
    {
        return $this->where('google_id', $googleId);
    }

    /**
     * Login/daftar otomatis dari Google OAuth
     * Mencocokkan via google_id atau email, update data Google jika sudah ada
     */
    public function findOrCreateFromGoogle(array $googleUser): ?array
    {
        $db = $this->getDb();

        // 1. Coba cari by google_id dulu
        $stmt = $db->prepare("SELECT * FROM users WHERE google_id = ? LIMIT 1");
        $stmt->execute([$googleUser['sub']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Update avatar jika berubah
            $db->prepare("UPDATE users SET avatar_url = ?, updated_at = NOW() WHERE id = ?")
               ->execute([$googleUser['picture'] ?? null, $user['id']]);
            $user['avatar_url'] = $googleUser['picture'] ?? $user['avatar_url'];
            return $user;
        }

        // 2. Coba cari by email (akun sudah ada tapi belum link Google)
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$googleUser['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Link google_id ke akun yang ada
            $db->prepare("UPDATE users SET google_id = ?, avatar_url = ?, updated_at = NOW() WHERE id = ?")
               ->execute([$googleUser['sub'], $googleUser['picture'] ?? null, $user['id']]);
            $user['google_id']  = $googleUser['sub'];
            $user['avatar_url'] = $googleUser['picture'] ?? null;
            return $user;
        }

        // 3. Email tidak terdaftar — tolak login (sistem closed, hanya dosen terdaftar)
        return null;
    }

    /**
     * Ambil semua user (untuk manajemen user oleh kaprodi/dekan)
     */
    public function getAll(): array
    {
        $db = $this->getDb();
        $stmt = $db->query("SELECT u.id, u.username, u.nama_lengkap, u.email, u.role, u.prodi_id, p.nama AS prodi_nama, p.kode AS prodi_kode, u.avatar_url, u.gemini_api_key, u.gemini_model, u.created_at FROM users u LEFT JOIN prodi p ON u.prodi_id = p.id ORDER BY u.role ASC, u.nama_lengkap ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update API Key & Model AI user
     */
    public function updateApiKey(int $userId, string $apiKey, string $model = 'gemini-2.0-flash'): bool
    {
        return $this->update($userId, [
            'gemini_api_key' => trim($apiKey),
            'gemini_model'   => trim($model),
        ]);
    }

    /**
     * Update role user (hanya boleh dilakukan oleh kaprodi/dekan)
     */
    public function updateRole(int $userId, string $role): bool
    {
        if (!in_array($role, ['dosen', 'kaprodi', 'dekan'])) {
            return false;
        }
        return $this->update($userId, ['role' => $role]);
    }

    /**
     * Update profile user
     */
    public function updateProfile(int $userId, array $data): bool
    {
        $allowed = ['nama_lengkap', 'email', 'password', 'prodi_id'];
        $filtered = array_filter($data, fn($k) => in_array($k, $allowed), ARRAY_FILTER_USE_KEY);
        if (empty($filtered)) return false;
        return $this->update($userId, $filtered);
    }
}
