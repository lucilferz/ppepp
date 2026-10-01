<?php
/**
 * Controller: Prodi (Program Studi)
 * Platform PPEPP Fakultas
 * Hanya diakses oleh kaprodi / dekan
 */

class ProdiController extends Controller
{
    private Prodi   $prodiModel;
    private User    $userModel;
    private Kriteria $kriteriaModel;

    public function __construct()
    {
        parent::__construct();
        $this->prodiModel    = new Prodi();
        $this->userModel     = new User();
        $this->kriteriaModel = new Kriteria();
    }

    /**
     * Daftar semua prodi
     */
    public function index(): void
    {
        $this->requireRole('kaprodi', 'dekan');
        $flash  = $this->getFlash();
        $prodis = $this->prodiModel->getAllWithStats();
        $this->render('prodi/index', compact('prodis', 'flash'));
    }

    /**
     * Form buat prodi baru
     */
    public function create(): void
    {
        $this->requireRole('kaprodi', 'dekan');
        $flash      = $this->getFlash();
        $allDosen   = $this->prodiModel->getAllDosen();
        $this->render('prodi/create', compact('flash', 'allDosen'));
    }

    /**
     * Simpan prodi baru
     */
    public function store(): void
    {
        $this->requireRole('kaprodi', 'dekan');

        $kode      = strtoupper(trim($this->post('kode', '')));
        $nama      = trim($this->post('nama', ''));
        $jenjang   = $this->post('jenjang', 'S1');
        $kaprodiId = (int)$this->post('kaprodi_id', 0) ?: null;
        $deskripsi = trim($this->post('deskripsi', ''));

        if (empty($kode) || empty($nama)) {
            $this->flash('error', 'Kode dan Nama Prodi wajib diisi.');
            $this->redirect('/prodi/create');
            return;
        }

        if (!in_array($jenjang, ['D3', 'S1', 'S2', 'S3'])) {
            $jenjang = 'S1';
        }

        // Cek duplikat kode
        if ($this->prodiModel->findByKode($kode)) {
            $this->flash('error', "Kode prodi '$kode' sudah digunakan.");
            $this->redirect('/prodi/create');
            return;
        }

        $prodiId = $this->prodiModel->insert([
            'kode'       => $kode,
            'nama'       => $nama,
            'jenjang'    => $jenjang,
            'kaprodi_id' => $kaprodiId,
            'deskripsi'  => $deskripsi,
            'aktif'      => 1,
        ]);

        // Update kaprodi_id di user juga
        if ($kaprodiId) {
            $this->prodiModel->addDosen($prodiId, $kaprodiId);
        }

        // AUTO-SEED 9 kriteria default untuk prodi baru
        $jumlahKriteria = $this->kriteriaModel->seedForProdi($prodiId);

        $this->flash('success', "Prodi '$nama' berhasil dibuat dengan $jumlahKriteria kriteria standar (C1–C9).");
        $this->redirect('/prodi/' . $prodiId);
    }

    /**
     * Detail prodi + daftar dosen
     */
    public function show(int $id): void
    {
        $this->requireRole('kaprodi', 'dekan');
        $flash      = $this->getFlash();
        $prodi      = $this->prodiModel->getWithDosen($id);
        $allDosen   = $this->prodiModel->getAllDosen();

        if (!$prodi) {
            $this->flash('error', 'Prodi tidak ditemukan.');
            $this->redirect('/prodi');
            return;
        }

        $this->render('prodi/show', compact('prodi', 'flash', 'allDosen'));
    }

    /**
     * Form edit prodi
     */
    public function edit(int $id): void
    {
        $this->requireRole('kaprodi', 'dekan');
        $flash    = $this->getFlash();
        $prodi    = $this->prodiModel->find($id);
        $allDosen = $this->prodiModel->getAllDosen();

        if (!$prodi) {
            $this->flash('error', 'Prodi tidak ditemukan.');
            $this->redirect('/prodi');
            return;
        }

        $this->render('prodi/edit', compact('prodi', 'flash', 'allDosen'));
    }

    /**
     * Update prodi
     */
    public function update(int $id): void
    {
        $this->requireRole('kaprodi', 'dekan');

        $prodi = $this->prodiModel->find($id);
        if (!$prodi) {
            $this->flash('error', 'Prodi tidak ditemukan.');
            $this->redirect('/prodi');
            return;
        }

        $kode      = strtoupper(trim($this->post('kode', $prodi['kode'])));
        $nama      = trim($this->post('nama', $prodi['nama']));
        $jenjang   = $this->post('jenjang', $prodi['jenjang']);
        $kaprodiId = (int)$this->post('kaprodi_id', 0) ?: null;
        $deskripsi = trim($this->post('deskripsi', ''));
        $aktif     = $this->post('aktif', '1') === '1' ? 1 : 0;

        $this->prodiModel->update($id, [
            'kode'       => $kode,
            'nama'       => $nama,
            'jenjang'    => $jenjang,
            'kaprodi_id' => $kaprodiId,
            'deskripsi'  => $deskripsi,
            'aktif'      => $aktif,
        ]);

        $this->flash('success', "Prodi '$nama' berhasil diperbarui.");
        $this->redirect('/prodi/' . $id);
    }

    /**
     * Tambah dosen ke prodi
     */
    public function addDosen(int $id): void
    {
        $this->requireRole('kaprodi', 'dekan');

        $userId = (int)$this->post('user_id', 0);
        if (!$userId) {
            $this->flash('error', 'Pilih dosen yang ingin ditambahkan.');
            $this->redirect('/prodi/' . $id);
            return;
        }

        $prodi = $this->prodiModel->find($id);
        $user  = $this->userModel->find($userId);

        if (!$prodi || !$user) {
            $this->flash('error', 'Data tidak valid.');
            $this->redirect('/prodi/' . $id);
            return;
        }

        $this->prodiModel->addDosen($id, $userId);
        $this->flash('success', "Dosen '{$user['nama_lengkap']}' berhasil ditambahkan ke prodi '{$prodi['nama']}'.");
        $this->redirect('/prodi/' . $id);
    }

    /**
     * Lepas dosen dari prodi
     */
    public function removeDosen(int $id): void
    {
        $this->requireRole('kaprodi', 'dekan');

        $userId = (int)$this->post('user_id', 0);
        if (!$userId) {
            $this->redirect('/prodi/' . $id);
            return;
        }

        $user = $this->userModel->find($userId);
        if ($user) {
            $this->prodiModel->removeDosen($userId);
            $this->flash('success', "Dosen '{$user['nama_lengkap']}' dilepas dari prodi.");
        }
        $this->redirect('/prodi/' . $id);
    }

    /**
     * Hapus prodi
     */
    public function delete(int $id): void
    {
        $this->requireRole('kaprodi', 'dekan');

        $prodi = $this->prodiModel->find($id);
        if ($prodi) {
            // Set prodi_id = NULL untuk semua dosen di prodi ini
            $this->prodiModel->exec("UPDATE users SET prodi_id = NULL WHERE prodi_id = ?", [$id]);
            $this->prodiModel->delete($id);
            $this->flash('success', "Prodi '{$prodi['nama']}' berhasil dihapus.");
        }
        $this->redirect('/prodi');
    }
}
