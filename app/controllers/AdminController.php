<?php
/**
 * Controller: Admin (Halaman Admin Utama)
 * Platform PPEPP Fakultas
 * Hanya dapat diakses oleh Dosen dengan Role: Kaprodi & Dekan
 */

class AdminController extends Controller
{
    private User  $userModel;
    private Prodi $prodiModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel  = new User();
        $this->prodiModel = new Prodi();
    }

    /**
     * Halaman Utama Admin (Manajemen Pengguna & Program Studi)
     */
    public function index(): void
    {
        $this->requireRole('kaprodi', 'dekan');

        $flash       = $this->getFlash();
        $currentUser = $this->currentUser();
        $users       = $this->userModel->getAll();
        $prodis      = $this->prodiModel->getAllWithStats();

        // Data statistik ringkas
        $totalUsers   = count($users);
        $totalDekan   = count(array_filter($users, fn($u) => $u['role'] === 'dekan'));
        $totalKaprodi = count(array_filter($users, fn($u) => $u['role'] === 'kaprodi'));
        $totalDosen   = count(array_filter($users, fn($u) => $u['role'] === 'dosen'));
        $totalProdi   = count($prodis);

        $this->render('admin/index', compact(
            'currentUser', 'users', 'prodis', 'flash',
            'totalUsers', 'totalDekan', 'totalKaprodi', 'totalDosen', 'totalProdi'
        ));
    }
}
