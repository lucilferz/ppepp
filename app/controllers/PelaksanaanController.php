<?php
/**
 * Controller: Pelaksanaan
 * Platform PPEPP Fakultas
 */

class PelaksanaanController extends Controller
{
    private Pelaksanaan $model;
    private Penetapan   $penetapanModel;
    private Kriteria    $kriteriaModel;

    public function __construct()
    {
        parent::__construct();
        $this->model          = new Pelaksanaan();
        $this->penetapanModel = new Penetapan();
        $this->kriteriaModel  = new Kriteria();
    }

    // ============================================================
    // DAFTAR PELAKSANAAN
    // ============================================================
    public function index(): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $projectId = (int) ($this->get('project_id', 0) ?: $this->get('ppepp_project_id', 0));

        $db = (new Model())->getDb();
        if ($projectId > 0) {
            $stmt = $db->prepare(
                "SELECT pl.*, p.judul AS penetapan_judul, ta.nama AS tahun_ajaran_nama, ta.semester
                 FROM pelaksanaan pl
                 JOIN penetapan p ON p.id = pl.penetapan_id
                 JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                 WHERE pl.user_id = ? AND (pl.ppepp_project_id = ? OR (pl.ppepp_project_id IS NULL AND p.ppepp_project_id = ?))
                 ORDER BY pl.created_at DESC"
            );
            $stmt->execute([$user['id'], $projectId, $projectId]);
            $pelaksanaans = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmtStats = $db->prepare(
                "SELECT pl.status, COUNT(*) AS cnt 
                 FROM pelaksanaan pl
                 JOIN penetapan p ON p.id = pl.penetapan_id
                 WHERE pl.user_id = ? AND (pl.ppepp_project_id = ? OR (pl.ppepp_project_id IS NULL AND p.ppepp_project_id = ?))
                 GROUP BY pl.status"
            );
            $stmtStats->execute([$user['id'], $projectId, $projectId]);
            $rows = $stmtStats->fetchAll(PDO::FETCH_ASSOC);
            $stats = ['total' => 0, 'draft' => 0, 'final' => 0];
            foreach ($rows as $r) {
                $stats[$r['status']] = (int)$r['cnt'];
                $stats['total'] += (int)$r['cnt'];
            }
        } else {
            $pelaksanaans = $this->model->findByUser($user['id']);
            $stats        = $this->model->statsByUser($user['id']);
        }

        $this->render('pelaksanaan/index', compact('user', 'pelaksanaans', 'stats', 'flash', 'projectId'));
    }

    // ============================================================
    // FORM BUAT PELAKSANAAN
    // ============================================================
    public function create(): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $projectId = (int) ($this->get('project_id', 0) ?: $this->get('ppepp_project_id', 0));

        $db = (new Model())->getDb();
        $selectedProject = null;

        if ($projectId > 0) {
            $stmtPr = $db->prepare("SELECT pr.*, ta.nama AS ta_nama FROM ppepp_project pr JOIN tahun_ajaran ta ON ta.id = pr.tahun_ajaran_id WHERE pr.id = ? AND pr.user_id = ? LIMIT 1");
            $stmtPr->execute([$projectId, $user['id']]);
            $selectedProject = $stmtPr->fetch(PDO::FETCH_ASSOC);

            $stmt = $db->prepare(
                "SELECT p.*, ta.nama AS ta_nama, ta.semester
                 FROM penetapan p
                 JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                 WHERE p.user_id = ? AND (p.ppepp_project_id = ? OR (p.ppepp_project_id IS NULL AND p.tahun_ajaran_id = (SELECT tahun_ajaran_id FROM ppepp_project WHERE id = ? AND user_id = ?)))
                 ORDER BY p.created_at DESC"
            );
            $stmt->execute([$user['id'], $projectId, $projectId, $user['id']]);
        } else {
            $stmt = $db->prepare(
                "SELECT p.*, ta.nama AS ta_nama, ta.semester
                 FROM penetapan p
                 JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                 WHERE p.user_id = ?
                 ORDER BY p.created_at DESC"
            );
            $stmt->execute([$user['id']]);
        }
        $penetapans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('pelaksanaan/create', compact('user', 'penetapans', 'flash', 'projectId', 'selectedProject'));
    }

    // ============================================================
    // SIMPAN PELAKSANAAN BARU
    // ============================================================
    public function store(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $penetapanId = (int) $this->post('penetapan_id', 0);
        $projectId   = (int) $this->post('project_id', 0);
        $judul       = trim($this->post('judul', ''));
        $deskripsi   = trim($this->post('deskripsi', ''));

        if (!$penetapanId || !$judul) {
            $this->flash('error', 'Penetapan dan judul wajib diisi.');
            $this->redirect('/pelaksanaan/create' . ($projectId > 0 ? '?project_id=' . $projectId : ''));
            return;
        }

        // Pastikan penetapan milik user
        $penetapan = $this->penetapanModel->find($penetapanId);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->flash('error', 'Penetapan tidak valid.');
            $this->redirect('/pelaksanaan/create' . ($projectId > 0 ? '?project_id=' . $projectId : ''));
            return;
        }

        $ppeppProjectId = $projectId > 0 ? $projectId : ($penetapan['ppepp_project_id'] ?? null);
        if (!$ppeppProjectId && !empty($penetapan['tahun_ajaran_id'])) {
            $db = (new Model())->getDb();
            $stmtPr = $db->prepare("SELECT id FROM ppepp_project WHERE user_id = ? AND tahun_ajaran_id = ? LIMIT 1");
            $stmtPr->execute([$user['id'], $penetapan['tahun_ajaran_id']]);
            $ppeppProjectId = (int)$stmtPr->fetchColumn() ?: null;
        }

        $id = $this->model->create([
            'user_id'          => $user['id'],
            'ppepp_project_id' => $ppeppProjectId,
            'penetapan_id'     => $penetapanId,
            'judul'            => $judul,
            'deskripsi'        => $deskripsi,
            'status'           => 'draft',
        ]);

        // Inisialisasi pelaksanaan_detail status checklist untuk setiap standar target di penetapan_detail
        $db = $this->model->getDb();
        $stmtK = $db->prepare("SELECT id, kriteria_id FROM penetapan_detail WHERE penetapan_id = ?");
        $stmtK->execute([$penetapanId]);
        $details = $stmtK->fetchAll(PDO::FETCH_ASSOC);

        $stmtIns = $db->prepare("INSERT IGNORE INTO pelaksanaan_detail (pelaksanaan_id, kriteria_id, penetapan_detail_id, status_pelaksanaan) VALUES (?, ?, ?, 'belum')");
        foreach ($details as $d) {
            $stmtIns->execute([$id, $d['kriteria_id'], $d['id']]);
        }

        $this->flash('success', 'Pelaksanaan berhasil dibuat! Anda dapat mencentang status keterlaksanaan dan mengelola link bukti.');
        $this->redirect('/pelaksanaan/' . $id);
    }

    // ============================================================
    // AJAX: Simpan / Update Checklist Status Pelaksanaan
    // ============================================================
    public function saveStatusDetail(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $pelaksanaanId     = (int) $this->post('pelaksanaan_id', 0);
        $penetapanDetailId = (int) $this->post('penetapan_detail_id', 0);
        $kriteriaId        = (int) $this->post('kriteria_id', 0);
        $status            = $this->post('status_pelaksanaan', 'belum');
        $catatan           = $this->post('catatan_pelaksanaan', null);
        $capaianAngka      = $this->post('capaian_angka', null);

        $pl = $this->model->find($pelaksanaanId);
        if (!$pl || $pl['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Pelaksanaan tidak ditemukan.'], 404);
            return;
        }

        $ok = $this->model->saveDetailStatus($pelaksanaanId, $kriteriaId, $status, $catatan, $penetapanDetailId, $capaianAngka);
        if ($ok) {
            $stats = $this->model->getChecklistStats($pelaksanaanId, $pl['penetapan_id']);
            $this->json([
                'success' => true,
                'stats'   => $stats,
                'message' => 'Status checklist berhasil diperbarui.',
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal memperbarui status checklist.'], 500);
        }
    }

    // ============================================================
    // DETAIL PELAKSANAAN + BUKTI
    // ============================================================
    public function show(int $id): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $pelaksanaan = $this->model->findWithDetail($id, $user['id']);
        if (!$pelaksanaan) {
            $this->flash('error', 'Pelaksanaan tidak ditemukan.');
            $this->redirect('/pelaksanaan');
            return;
        }

        $this->render('pelaksanaan/show', compact('user', 'pelaksanaan', 'flash'));
    }

    // ============================================================
    // AJAX: TAMBAH BUKTI LINK
    // ============================================================
    public function addBukti(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $pelaksanaanId     = (int) $this->post('pelaksanaan_id', 0);
        $kriteriaId        = (int) $this->post('kriteria_id', 0);
        $penetapanDetailId = (int) $this->post('penetapan_detail_id', 0);
        $judulBukti        = trim($this->post('judul_bukti', ''));
        $urlLink           = trim($this->post('url_link', ''));
        $keterangan        = trim($this->post('keterangan', ''));

        if (!$pelaksanaanId || !$judulBukti || !$urlLink) {
            $this->json(['success' => false, 'message' => 'Judul dan URL link wajib diisi.']);
            return;
        }

        // Validasi kepemilikan
        $pl = $this->model->find($pelaksanaanId);
        if (!$pl || $pl['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Pelaksanaan tidak ditemukan.']);
            return;
        }

        // Auto-fix protocol jika belum ada https:// atau http://
        if (!preg_match('#^https?://#i', $urlLink)) {
            $urlLink = 'https://' . $urlLink;
        }

        // Validasi URL format
        if (!filter_var($urlLink, FILTER_VALIDATE_URL)) {
            $this->json(['success' => false, 'message' => 'Format URL tidak valid. Contoh: https://drive.google.com/...']);
            return;
        }

        $buktiId = $this->model->addBukti(
            $pelaksanaanId, 
            $kriteriaId, 
            $judulBukti, 
            $urlLink, 
            $keterangan, 
            $penetapanDetailId > 0 ? $penetapanDetailId : null
        );

        if ($buktiId) {
            $this->json([
                'success'             => true,
                'bukti_id'            => $buktiId,
                'penetapan_detail_id' => $penetapanDetailId,
                'kriteria_id'         => $kriteriaId,
                'judul'               => $judulBukti,
                'url'                 => $urlLink,
                'keterangan'          => $keterangan,
                'created'             => date('d M Y H:i'),
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal menyimpan bukti.']);
        }
    }

    // ============================================================
    // AJAX: HAPUS BUKTI
    // ============================================================
    public function deleteBukti(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $buktiId       = (int) $this->post('bukti_id', 0);
        $pelaksanaanId = (int) $this->post('pelaksanaan_id', 0);

        $pl = $this->model->find($pelaksanaanId);
        if (!$pl || $pl['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        $ok = $this->model->deleteBukti($buktiId, $pelaksanaanId);
        $this->json(['success' => $ok]);
    }

    // ============================================================
    // FINALISASI PELAKSANAAN
    // ============================================================
    public function finalize(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $pl = $this->model->find($id);
        if (!$pl || $pl['user_id'] != $user['id']) {
            $this->flash('error', 'Pelaksanaan tidak ditemukan.');
            $this->redirect('/pelaksanaan');
            return;
        }

        $this->model->finalize($id);
        $this->flash('success', 'Pelaksanaan berhasil difinalisasi!');
        $this->redirect('/pelaksanaan/' . $id);
    }

    // ============================================================
    // FORM EDIT PELAKSANAAN
    // ============================================================
    public function edit(int $id): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $pelaksanaan = $this->model->findWithDetail($id, $user['id']);
        if (!$pelaksanaan || $pelaksanaan['user_id'] != $user['id']) {
            $this->flash('error', 'Pelaksanaan tidak ditemukan.');
            $this->redirect('/pelaksanaan');
            return;
        }

        // Ambil daftar penetapan milik user ini
        $db = (new Model())->getDb();
        $stmt = $db->prepare(
            "SELECT p.*, ta.nama AS ta_nama, ta.semester
             FROM penetapan p
             JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
             WHERE p.user_id = ?
             ORDER BY p.created_at DESC"
        );
        $stmt->execute([$user['id']]);
        $penetapans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('pelaksanaan/edit', compact('user', 'pelaksanaan', 'penetapans', 'flash'));
    }

    // ============================================================
    // UPDATE PELAKSANAAN
    // ============================================================
    public function update(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $pelaksanaan = $this->model->find($id);
        if (!$pelaksanaan || $pelaksanaan['user_id'] != $user['id']) {
            $this->flash('error', 'Pelaksanaan tidak ditemukan.');
            $this->redirect('/pelaksanaan');
            return;
        }

        $penetapanId = (int) $this->post('penetapan_id', 0);
        $judul       = trim($this->post('judul', ''));
        $deskripsi   = trim($this->post('deskripsi', ''));

        if (!$penetapanId || !$judul) {
            $this->flash('error', 'Penetapan acuan dan judul wajib diisi.');
            $this->redirect('/pelaksanaan/' . $id . '/edit');
            return;
        }

        // Pastikan penetapan milik user
        $penetapan = $this->penetapanModel->find($penetapanId);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->flash('error', 'Penetapan acuan tidak valid.');
            $this->redirect('/pelaksanaan/' . $id . '/edit');
            return;
        }

        $this->model->update($id, [
            'penetapan_id' => $penetapanId,
            'judul'        => $judul,
            'deskripsi'    => $deskripsi,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        $this->flash('success', 'Pelaksanaan berhasil diperbarui!');
        $this->redirect('/pelaksanaan/' . $id);
    }

    // ============================================================
    // PELAKSANAAN - TOGGLE STATUS / AKTIFKAN / NONAKTIFKAN / HIDE
    // ============================================================
    public function toggleStatus(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $pelaksanaan = $this->model->find($id);
        if (!$pelaksanaan || $pelaksanaan['user_id'] != $user['id']) {
            $this->flash('error', 'Pelaksanaan tidak ditemukan.');
            $target = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/pelaksanaan';
            $this->redirect($target);
            return;
        }

        $targetVis = trim($_POST['visibility_status'] ?? $_POST['status'] ?? '');
        $allowed   = ['aktif', 'nonaktif', 'hidden'];

        if (!in_array($targetVis, $allowed)) {
            if (isset($_POST['is_active'])) {
                $targetVis = ((int)$_POST['is_active'] === 1) ? 'aktif' : 'nonaktif';
            } else {
                $currentVis = $pelaksanaan['visibility_status'] ?? 'aktif';
                $targetVis  = ($currentVis === 'aktif') ? 'nonaktif' : 'aktif';
            }
        }

        $isActiveVal = ($targetVis === 'aktif') ? 1 : 0;

        $db = (new Model())->getDb();
        $db->prepare("UPDATE pelaksanaan SET visibility_status = ? WHERE id = ? AND user_id = ?")
           ->execute([$targetVis, $id, $user['id']]);

        $msgMap = [
            'aktif'    => 'Dokumen Pelaksanaan berhasil diaktifkan kembali dan tampil di daftar utama.',
            'nonaktif' => 'Dokumen Pelaksanaan berhasil dinonaktifkan (tetap terlihat di daftar dokumen).',
            'hidden'   => 'Dokumen Pelaksanaan berhasil disembunyikan (Hide). Anda dapat melihat dan mengaktifkannya kembali di menu Dokumen Tersembunyi.',
        ];

        $this->flash('success', $msgMap[$targetVis] ?? 'Status visibilitas dokumen berhasil diperbarui.');
        $target = !empty($_POST['return_url']) ? $_POST['return_url'] : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/pelaksanaan/' . $id);
        $this->redirect($target);
    }

    // ============================================================
    // HAPUS / NONAKTIFKAN PELAKSANAAN (Soft-Hide)
    // ============================================================
    public function delete(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $pelaksanaan = $this->model->find($id);
        if (!$pelaksanaan || $pelaksanaan['user_id'] != $user['id']) {
            $this->flash('error', 'Pelaksanaan tidak ditemukan.');
            $this->redirect('/pelaksanaan');
            return;
        }

        // Soft-hide: sembunyikan dokumen tanpa menghapus datanya
        $db = (new Model())->getDb();
        $db->prepare("UPDATE pelaksanaan SET visibility_status = 'hidden' WHERE id = ? AND user_id = ?")->execute([$id, $user['id']]);

        $this->flash('success', 'Dokumen Pelaksanaan berhasil disembunyikan (Hide). Anda dapat melihat dan mengaktifkannya kembali di menu Dokumen Tersembunyi.');
        $target = !empty($_POST['return_url']) ? $_POST['return_url'] : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/pelaksanaan');
        $this->redirect($target);
    }
}


