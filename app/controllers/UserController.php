<?php
/**
 * Controller: User Management
 * Platform PPEPP Fakultas
 * Hanya dapat diakses oleh role: kaprodi, dekan
 */

class UserController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Daftar semua users (kaprodi/dekan only)
     */
    public function index(): void
    {
        $this->requireRole('kaprodi', 'dekan');
        $flash  = $this->getFlash();
        $users  = $this->userModel->getAll();
        $currentUser = $this->currentUser();

        $this->render('users/index', compact('users', 'flash', 'currentUser'));
    }

    /**
     * Form tambah user baru
     */
    public function create(): void
    {
        $this->requireRole('kaprodi', 'dekan');
        $flash = $this->getFlash();
        $this->render('users/create', compact('flash'));
    }

    /**
     * Simpan user baru
     */
    public function store(): void
    {
        $this->requireRole('kaprodi', 'dekan');

        $nama     = trim($this->post('nama_lengkap', ''));
        $username = trim($this->post('username', ''));
        $email    = trim($this->post('email', ''));
        $password = $this->post('password', '');
        $role     = $this->post('role', 'dosen');

        if (empty($nama) || empty($username) || empty($email) || empty($password)) {
            $this->flash('error', 'Semua field wajib diisi.');
            $this->redirect('/users/create');
            return;
        }

        if (!in_array($role, ['dosen', 'kaprodi', 'dekan'])) {
            $role = 'dosen';
        }

        // Cek duplikat username/email
        $existing = $this->userModel->findByEmail($email);
        if ($existing) {
            $this->flash('error', 'Email sudah terdaftar di sistem.');
            $this->redirect('/users/create');
            return;
        }

        $db = $this->userModel->getDb();
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $this->flash('error', 'Username sudah digunakan.');
            $this->redirect('/users/create');
            return;
        }

        // Hash password dengan bcrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Buat nama pendek
        $parts     = explode(',', $nama);
        $namaShort = trim($parts[0]);
        $namaShort = preg_replace('/^(Prof\s+Dr\.|Prof\s+Ir\.|Prof\.|Dr\.|Ir\.|Fx\.|Drs\.)\s+/i', '', $namaShort);
        $namaShort = trim(implode(' ', array_slice(explode(' ', $namaShort), 0, 2)));

        $prodiId = (int)$this->post('prodi_id', $user['prodi_id'] ?? 1);

        $db->prepare("
            INSERT INTO users (username, password, nama_lengkap, email, role, prodi_id, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ")->execute([$username, $hashedPassword, $nama, $email, $role, $prodiId > 0 ? $prodiId : 1]);

        $this->flash('success', "User baru '$nama' berhasil ditambahkan.");
        $this->redirect('/users');
    }

    /**
     * Form edit user
     */
    public function edit(int $id): void
    {
        $this->requireRole('kaprodi', 'dekan');
        $flash = $this->getFlash();
        $user  = $this->userModel->find($id);

        if (!$user) {
            $this->flash('error', 'User tidak ditemukan.');
            $this->redirect('/users');
            return;
        }

        $this->render('users/edit', compact('user', 'flash'));
    }

    /**
     * Update user
     */
    public function update(int $id): void
    {
        $this->requireRole('kaprodi', 'dekan');

        $user = $this->userModel->find($id);
        if (!$user) {
            $this->flash('error', 'User tidak ditemukan.');
            $this->redirect('/users');
            return;
        }

        $nama     = trim($this->post('nama_lengkap', $user['nama_lengkap']));
        $email    = trim($this->post('email', $user['email']));
        $role     = $this->post('role', $user['role']);
        $prodiId  = (int) $this->post('prodi_id', $user['prodi_id'] ?? 1);
        $password = $this->post('password', '');

        if (!in_array($role, ['dosen', 'kaprodi', 'dekan'])) {
            $role = 'dosen';
        }

        $updateData = [
            'nama_lengkap' => $nama,
            'email'        => $email,
            'role'         => $role,
            'prodi_id'     => $prodiId > 0 ? $prodiId : 1,
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $updateData);

        $this->flash('success', "Data user '$nama' berhasil diperbarui.");
        $this->redirect('/users');
    }

    /**
     * Hapus user
     */
    public function delete(int $id): void
    {
        $this->requireRole('kaprodi', 'dekan');

        $currentUser = $this->currentUser();
        if ($currentUser['id'] === $id) {
            $this->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            $this->redirect('/users');
            return;
        }

        $user = $this->userModel->find($id);
        if ($user) {
            $this->userModel->delete($id);
            $this->flash('success', "User '{$user['nama_lengkap']}' berhasil dihapus.");
        }
        $this->redirect('/users');
    }

    /**
     * Halaman set API Key milik sendiri (semua role)
     */
    public function apiKey(): void
    {
        $this->requireAuth();
        $userSession = $this->currentUser();
        $flash       = $this->getFlash();
        $user        = $this->userModel->find($userSession['id']);

        $this->render('users/api_key', compact('user', 'flash'));
    }

    /**
     * Simpan API Key
     */
    public function saveApiKey(): void
    {
        $this->requireAuth();
        $userSession = $this->currentUser();

        $apiKey = trim($this->post('gemini_api_key', ''));
        $model  = $this->post('gemini_model', DEFAULT_GEMINI_MODEL);

        if (!array_key_exists($model, AVAILABLE_GEMINI_MODELS)) {
            $model = DEFAULT_GEMINI_MODEL;
        }

        $this->userModel->updateApiKey($userSession['id'], $apiKey, $model);

        // Update session
        $_SESSION['user']['gemini_api_key'] = $apiKey;
        $_SESSION['user']['gemini_model']   = $model;

        $this->flash('success', 'API Key Gemini berhasil disimpan! Fitur AI kini aktif untuk akun Anda.');
        $this->redirect('/users/api-key');
    }
}
