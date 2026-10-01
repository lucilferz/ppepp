<?php
/**
 * Controller: PpeppProject
 * Kelola PPEPP per Tahun Ajaran (Project/Folder)
 * Platform PPEPP Fakultas
 */

class PpeppProjectController extends Controller
{
    private PpeppProject $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new PpeppProject();
    }

    // ============================================================
    // DAFTAR PROJECT
    // ============================================================
    public function index(): void
    {
        $this->requireAuth();
        $user    = $this->currentUser();
        $flash   = $this->getFlash();
        $projects = $this->model->findByUser($user['id']);

        // Hitung stats untuk setiap project
        $db = (new Model())->getDb();
        foreach ($projects as &$proj) {
            $projId = $proj['id'];
            $userId = $user['id'];

            $steps = ['penetapan','pelaksanaan','evaluasi','pengendalian','peningkatan'];
            $proj['stats'] = [];
            $done = 0;
            $taId = (int)($proj['tahun_ajaran_id'] ?? 0);
            foreach ($steps as $step) {
                if ($step === 'penetapan') {
                    $stmt = $db->prepare("SELECT COUNT(*) as total, COALESCE(SUM(CASE WHEN status='final' THEN 1 ELSE 0 END),0) as final
                                          FROM `$step` WHERE (ppepp_project_id = ? OR (ppepp_project_id IS NULL AND tahun_ajaran_id = ?)) AND user_id = ?");
                    $stmt->execute([$projId, $taId, $userId]);
                } else {
                    $stmt = $db->prepare("SELECT COUNT(*) as total, COALESCE(SUM(CASE WHEN status='final' THEN 1 ELSE 0 END),0) as final
                                          FROM `$step` WHERE ppepp_project_id = ? AND user_id = ?");
                    $stmt->execute([$projId, $userId]);
                }
                $s = $stmt->fetch(PDO::FETCH_ASSOC);
                $proj['stats'][$step] = $s;
                if ((int)$s['total'] > 0) $done++;
            }
            $proj['steps_started'] = $done;
            $proj['progress_pct']  = (int)(($done / 5) * 100);
        }
        unset($proj);

        $allTa     = $this->model->getAllTahunAjaran();
        $usedTaIds = array_map(fn($p) => (int)$p['tahun_ajaran_id'], $projects);

        $this->render('ppepp_project/index', compact('user', 'projects', 'usedTaIds', 'allTa', 'flash'));
    }

    // ============================================================
    // DETAIL PROJECT (5 TAHAP PPEPP)
    // ============================================================
    public function show(int $id): void
    {
        $this->requireAuth();
        $user    = $this->currentUser();
        $flash   = $this->getFlash();

        $project = $this->model->findWithStats($id, $user['id']);
        if (!$project) {
            $this->flash('error', 'Project PPEPP tidak ditemukan.');
            $this->redirect('/ppepp');
            return;
        }

        // Ambil daftar dokumen per tahap dalam project ini
        $db = (new Model())->getDb();

        $taId = (int)$project['tahun_ajaran_id'];

        $kriteriaModel = new Kriteria();
        $allKriteria   = $kriteriaModel->findByUser($user['id']);

        // Penetapan milik project ini
        $stmt = $db->prepare("SELECT p.*, ta.nama AS ta_nama
                              FROM penetapan p
                              JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                              WHERE (p.ppepp_project_id = ? OR (p.ppepp_project_id IS NULL AND p.tahun_ajaran_id = ?)) AND p.user_id = ?
                              ORDER BY p.created_at DESC");
        $stmt->execute([$id, $taId, $user['id']]);
        $penetapans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($penetapans as &$p) {
            $kIds = [];
            if (!empty($p['kriteria_ids'])) {
                $dec = json_decode($p['kriteria_ids'], true);
                if (is_array($dec)) $kIds = $dec;
            }
            $stmtDet = $db->prepare("SELECT DISTINCT kriteria_id FROM penetapan_detail WHERE penetapan_id = ?");
            $stmtDet->execute([$p['id']]);
            $detKids = $stmtDet->fetchAll(PDO::FETCH_COLUMN);
            $merged = array_unique(array_merge($kIds, array_map('intval', $detKids)));
            $p['kriteria_ids_str'] = implode(',', $merged);
        }
        unset($p);

        // Pelaksanaan milik project ini
        $stmt = $db->prepare("SELECT pl.*, pen.judul AS penetapan_judul, pen.kriteria_ids AS pen_kriteria_ids, pen.id AS pen_id
                              FROM pelaksanaan pl
                              LEFT JOIN penetapan pen ON pen.id = pl.penetapan_id
                              WHERE (pl.ppepp_project_id = ? OR (pl.ppepp_project_id IS NULL AND pen.tahun_ajaran_id = ?)) AND pl.user_id = ?
                              ORDER BY pl.created_at DESC");
        $stmt->execute([$id, $taId, $user['id']]);
        $pelaksanaans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($pelaksanaans as &$pl) {
            $kIds = [];
            if (!empty($pl['pen_kriteria_ids'])) {
                $dec = json_decode($pl['pen_kriteria_ids'], true);
                if (is_array($dec)) $kIds = $dec;
            }
            if (!empty($pl['pen_id'])) {
                $stmtDet = $db->prepare("SELECT DISTINCT kriteria_id FROM penetapan_detail WHERE penetapan_id = ?");
                $stmtDet->execute([$pl['pen_id']]);
                $detKids = $stmtDet->fetchAll(PDO::FETCH_COLUMN);
                $kIds = array_unique(array_merge($kIds, array_map('intval', $detKids)));
            }
            $pl['kriteria_ids_str'] = implode(',', $kIds);
        }
        unset($pl);

        // Evaluasi milik project ini
        $stmt = $db->prepare("SELECT ev.*, pen.judul AS penetapan_judul, pen.kriteria_ids AS pen_kriteria_ids, pen.id AS pen_id
                              FROM evaluasi ev
                              LEFT JOIN penetapan pen ON pen.id = ev.penetapan_id
                              WHERE (ev.ppepp_project_id = ? OR (ev.ppepp_project_id IS NULL AND pen.tahun_ajaran_id = ?)) AND ev.user_id = ?
                              ORDER BY ev.created_at DESC");
        $stmt->execute([$id, $taId, $user['id']]);
        $evaluasis = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($evaluasis as &$ev) {
            $kIds = [];
            if (!empty($ev['pen_kriteria_ids'])) {
                $dec = json_decode($ev['pen_kriteria_ids'], true);
                if (is_array($dec)) $kIds = $dec;
            }
            if (!empty($ev['pen_id'])) {
                $stmtDet = $db->prepare("SELECT DISTINCT kriteria_id FROM penetapan_detail WHERE penetapan_id = ?");
                $stmtDet->execute([$ev['pen_id']]);
                $detKids = $stmtDet->fetchAll(PDO::FETCH_COLUMN);
                $kIds = array_unique(array_merge($kIds, array_map('intval', $detKids)));
            }
            $ev['kriteria_ids_str'] = implode(',', $kIds);
        }
        unset($ev);

        // Pengendalian milik project ini
        $stmt = $db->prepare("SELECT pg.*, ev.judul AS evaluasi_judul, pen.kriteria_ids AS pen_kriteria_ids, pen.id AS pen_id
                              FROM pengendalian pg
                              LEFT JOIN evaluasi ev ON ev.id = pg.evaluasi_id
                              LEFT JOIN penetapan pen ON pen.id = ev.penetapan_id
                              WHERE (pg.ppepp_project_id = ? OR (pg.ppepp_project_id IS NULL AND pen.tahun_ajaran_id = ?)) AND pg.user_id = ?
                              ORDER BY pg.created_at DESC");
        $stmt->execute([$id, $taId, $user['id']]);
        $pengendalians = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($pengendalians as &$pg) {
            $kIds = [];
            if (!empty($pg['pen_kriteria_ids'])) {
                $dec = json_decode($pg['pen_kriteria_ids'], true);
                if (is_array($dec)) $kIds = $dec;
            }
            if (!empty($pg['pen_id'])) {
                $stmtDet = $db->prepare("SELECT DISTINCT kriteria_id FROM penetapan_detail WHERE penetapan_id = ?");
                $stmtDet->execute([$pg['pen_id']]);
                $detKids = $stmtDet->fetchAll(PDO::FETCH_COLUMN);
                $kIds = array_unique(array_merge($kIds, array_map('intval', $detKids)));
            }
            $pg['kriteria_ids_str'] = implode(',', $kIds);
        }
        unset($pg);

        // Peningkatan milik project ini
        $stmt = $db->prepare("SELECT pk.*, pg.judul AS pengendalian_judul, pen.kriteria_ids AS pen_kriteria_ids, pen.id AS pen_id
                              FROM peningkatan pk
                              LEFT JOIN pengendalian pg ON pg.id = pk.pengendalian_id
                              LEFT JOIN evaluasi ev ON ev.id = pg.evaluasi_id
                              LEFT JOIN penetapan pen ON pen.id = ev.penetapan_id
                              WHERE (pk.ppepp_project_id = ? OR (pk.ppepp_project_id IS NULL AND pen.tahun_ajaran_id = ?)) AND pk.user_id = ?
                              ORDER BY pk.created_at DESC");
        $stmt->execute([$id, $taId, $user['id']]);
        $peningkatans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($peningkatans as &$pk) {
            $kIds = [];
            if (!empty($pk['pen_kriteria_ids'])) {
                $dec = json_decode($pk['pen_kriteria_ids'], true);
                if (is_array($dec)) $kIds = $dec;
            }
            if (!empty($pk['pen_id'])) {
                $stmtDet = $db->prepare("SELECT DISTINCT kriteria_id FROM penetapan_detail WHERE penetapan_id = ?");
                $stmtDet->execute([$pk['pen_id']]);
                $detKids = $stmtDet->fetchAll(PDO::FETCH_COLUMN);
                $kIds = array_unique(array_merge($kIds, array_map('intval', $detKids)));
            }
            $pk['kriteria_ids_str'] = implode(',', $kIds);
        }
        unset($pk);

        // Hitung total dokumen tersembunyi
        $totalHiddenCount = 0;
        foreach ([$penetapans, $pelaksanaans, $evaluasis, $pengendalians, $peningkatans] as $docList) {
            if (is_array($docList)) {
                $totalHiddenCount += count(array_filter($docList, fn($x) => ($x['visibility_status'] ?? '') === 'hidden'));
            }
        }

        $this->render('ppepp_project/show', compact(
            'user', 'project', 'flash',
            'penetapans', 'pelaksanaans', 'evaluasis', 'pengendalians', 'peningkatans',
            'totalHiddenCount', 'allKriteria'
        ));
    }

    // ============================================================
    // HALAMAN DOKUMEN TERSEMBUNYI (HIDE) PER PROJECT
    // ============================================================
    public function hiddenItems(int $id): void
    {
        $this->requireAuth();
        $user    = $this->currentUser();
        $flash   = $this->getFlash();
        $project = $this->model->findWithStats($id, $user['id']);
        if (!$project) {
            $this->flash('error', 'Project PPEPP tidak ditemukan.');
            $this->redirect('/ppepp');
            return;
        }

        $db   = (new Model())->getDb();
        $taId = (int)$project['tahun_ajaran_id'];

        $stmt = $db->prepare("SELECT p.*, ta.nama AS ta_nama FROM penetapan p JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id WHERE (p.ppepp_project_id = ? OR (p.ppepp_project_id IS NULL AND p.tahun_ajaran_id = ?)) AND p.user_id = ? ORDER BY p.created_at DESC");
        $stmt->execute([$id, $taId, $user['id']]);
        $penetapans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT pl.*, pen.judul AS penetapan_judul FROM pelaksanaan pl LEFT JOIN penetapan pen ON pen.id = pl.penetapan_id WHERE (pl.ppepp_project_id = ? OR (pl.ppepp_project_id IS NULL AND pen.tahun_ajaran_id = ?)) AND pl.user_id = ? ORDER BY pl.created_at DESC");
        $stmt->execute([$id, $taId, $user['id']]);
        $pelaksanaans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT ev.*, pen.judul AS penetapan_judul FROM evaluasi ev LEFT JOIN penetapan pen ON pen.id = ev.penetapan_id WHERE (ev.ppepp_project_id = ? OR (ev.ppepp_project_id IS NULL AND pen.tahun_ajaran_id = ?)) AND ev.user_id = ? ORDER BY ev.created_at DESC");
        $stmt->execute([$id, $taId, $user['id']]);
        $evaluasis = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT pg.*, ev.judul AS evaluasi_judul FROM pengendalian pg LEFT JOIN evaluasi ev ON ev.id = pg.evaluasi_id LEFT JOIN penetapan pen ON pen.id = ev.penetapan_id WHERE (pg.ppepp_project_id = ? OR (pg.ppepp_project_id IS NULL AND pen.tahun_ajaran_id = ?)) AND pg.user_id = ? ORDER BY pg.created_at DESC");
        $stmt->execute([$id, $taId, $user['id']]);
        $pengendalians = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT pk.*, pg.judul AS pengendalian_judul FROM peningkatan pk LEFT JOIN pengendalian pg ON pg.id = pk.pengendalian_id LEFT JOIN evaluasi ev ON ev.id = pg.evaluasi_id LEFT JOIN penetapan pen ON pen.id = ev.penetapan_id WHERE (pk.ppepp_project_id = ? OR (pk.ppepp_project_id IS NULL AND pen.tahun_ajaran_id = ?)) AND pk.user_id = ? ORDER BY pk.created_at DESC");
        $stmt->execute([$id, $taId, $user['id']]);
        $peningkatans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('ppepp_project/hidden_items', compact(
            'user', 'project', 'flash',
            'penetapans', 'pelaksanaans', 'evaluasis', 'pengendalians', 'peningkatans'
        ));
    }

    // ============================================================
    // MONITORING & CHECKLIST PELAKSANAAN FAKULTAS PER PROJECT
    // ============================================================
    public function monitoring(int $id): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $project = $this->model->findWithStats($id, $user['id']);
        if (!$project) {
            $this->flash('error', 'Project PPEPP tidak ditemukan.');
            $this->redirect('/ppepp');
            return;
        }

        $db   = (new Model())->getDb();
        $taId = (int)$project['tahun_ajaran_id'];

        // Ambil semua Penetapan milik project ini
        $stmt = $db->prepare("SELECT p.*, ta.nama AS ta_nama
                              FROM penetapan p
                              JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                              WHERE (p.ppepp_project_id = ? OR (p.ppepp_project_id IS NULL AND p.tahun_ajaran_id = ?)) AND p.user_id = ?
                              ORDER BY p.created_at DESC");
        $stmt->execute([$id, $taId, $user['id']]);
        $penetapans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pelaksanaanModel = new Pelaksanaan();

        $totalTargets = 0;
        $cntTerlaksana = 0;
        $cntProses = 0;
        $cntBelum = 0;

        foreach ($penetapans as &$pen) {
            $penId = $pen['id'];

            // Cari pelaksanaan terkait penetapan ini (jika ada)
            $stmtPl = $db->prepare("SELECT id FROM pelaksanaan WHERE penetapan_id = ? AND user_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmtPl->execute([$penId, $user['id']]);
            $pelaksanaanId = (int)$stmtPl->fetchColumn();

            // Kriteria & Indikator dari penetapan_detail
            $stmtK = $db->prepare(
                "SELECT pd.*, 
                        COALESCE(NULLIF(pd.kode, ''), k.kode) AS kriteria_kode, 
                        k.nama AS kriteria_nama, 
                        k.deskripsi AS kriteria_deskripsi,
                        COALESCE(pld.status_pelaksanaan, 'belum') AS status_pelaksanaan,
                        pld.catatan_pelaksanaan
                 FROM penetapan_detail pd
                 JOIN kriteria k ON k.id = pd.kriteria_id
                 LEFT JOIN pelaksanaan_detail pld ON pld.pelaksanaan_id = ? AND pld.penetapan_detail_id = pd.id
                 WHERE pd.penetapan_id = ?
                 ORDER BY k.urutan ASC, pd.id ASC"
            );
            $stmtK->execute([$pelaksanaanId, $penId]);
            $details = $stmtK->fetchAll(PDO::FETCH_ASSOC);

            // Bukti per kriteria
            if ($pelaksanaanId > 0) {
                $stmtB = $db->prepare("SELECT * FROM pelaksanaan_bukti WHERE pelaksanaan_id = ? AND kriteria_id = ? ORDER BY created_at DESC");
                foreach ($details as &$d) {
                    $stmtB->execute([$pelaksanaanId, $d['kriteria_id']]);
                    $d['bukti'] = $stmtB->fetchAll(PDO::FETCH_ASSOC);
                }
                unset($d);
            } else {
                foreach ($details as &$d) {
                    $d['bukti'] = [];
                }
                unset($d);
            }

            $pen['pelaksanaan_id'] = $pelaksanaanId;
            $pen['details']        = $details;

            // Hitung stats penetapan ini
            $pStats = ['total' => count($details), 'terlaksana' => 0, 'proses' => 0, 'belum' => 0, 'pct' => 0];
            foreach ($details as $d) {
                $st = $d['status_pelaksanaan'] ?? 'belum';
                if (isset($pStats[$st])) $pStats[$st]++; else $pStats['belum']++;
            }
            if ($pStats['total'] > 0) {
                $pStats['pct'] = round(($pStats['terlaksana'] / $pStats['total']) * 100);
            }
            $pen['stats'] = $pStats;

            $totalTargets  += $pStats['total'];
            $cntTerlaksana += $pStats['terlaksana'];
            $cntProses     += $pStats['proses'];
            $cntBelum      += $pStats['belum'];
        }
        unset($pen);

        $projectStats = [
            'total'      => $totalTargets,
            'terlaksana' => $cntTerlaksana,
            'proses'     => $cntProses,
            'belum'      => $cntBelum,
            'pct'        => $totalTargets > 0 ? round(($cntTerlaksana / $totalTargets) * 100) : 0,
        ];

        $this->render('ppepp_project/monitoring', compact(
            'user', 'project', 'penetapans', 'projectStats', 'flash'
        ));
    }

    // ============================================================
    // AJAX: Simpan Status Checklist Pelaksanaan dari Halaman Monitoring
    // ============================================================
    public function saveMonitoringStatus(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $penetapanId       = (int) $this->post('penetapan_id', 0);
        $penetapanDetailId = (int) $this->post('penetapan_detail_id', 0);
        $kriteriaId        = (int) $this->post('kriteria_id', 0);
        $status            = $this->post('status_pelaksanaan', 'belum');
        $catatan           = $this->post('catatan_pelaksanaan', null);

        $db = (new Model())->getDb();
        $stmtP = $db->prepare("SELECT * FROM penetapan WHERE id = ? AND user_id = ?");
        $stmtP->execute([$penetapanId, $user['id']]);
        $penetapan = $stmtP->fetch(PDO::FETCH_ASSOC);

        if (!$penetapan) {
            $this->json(['success' => false, 'message' => 'Penetapan tidak ditemukan.'], 404);
            return;
        }

        $pelaksanaanModel = new Pelaksanaan();

        // Cari atau buat otomatis dokumen pelaksanaan jika belum ada
        $stmtPl = $db->prepare("SELECT id FROM pelaksanaan WHERE penetapan_id = ? AND user_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmtPl->execute([$penetapanId, $user['id']]);
        $pelaksanaanId = (int)$stmtPl->fetchColumn();

        if ($pelaksanaanId <= 0) {
            $pelaksanaanId = $pelaksanaanModel->create([
                'user_id'          => $user['id'],
                'ppepp_project_id' => $penetapan['ppepp_project_id'],
                'penetapan_id'     => $penetapanId,
                'judul'            => 'Pelaksanaan — ' . $penetapan['judul'],
                'deskripsi'        => 'Dibuat otomatis dari Monitoring Keterlaksanaan Fakultas',
                'status'           => 'draft',
            ]);
        }

        $ok = $pelaksanaanModel->saveDetailStatus($pelaksanaanId, $kriteriaId, $status, $catatan, $penetapanDetailId);
        if ($ok) {
            $penStats = $pelaksanaanModel->getChecklistStats($pelaksanaanId, $penetapanId);
            $this->json([
                'success'        => true,
                'pelaksanaan_id' => $pelaksanaanId,
                'pen_stats'      => $penStats,
                'message'        => 'Status Keterlaksanaan berhasil diperbarui.',
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal memperbarui status keterlaksanaan.'], 500);
        }
    }

    // ============================================================
    // BUAT PROJECT BARU
    // ============================================================
    public function create(): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();
        $allTa = $this->model->getAllTahunAjaran();
        $availableTa = $this->model->getAvailableTahunAjaran($user['id']);

        $this->render('ppepp_project/create', compact('user', 'allTa', 'availableTa', 'flash'));
    }

    public function store(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $tahunAjaranId = (int) $this->post('tahun_ajaran_id', 0);
        $judul         = trim($this->post('judul', ''));
        $deskripsi     = trim($this->post('deskripsi', ''));
        $isNew         = (bool) $this->post('new_ta', false);
        $newTaNama     = trim($this->post('new_ta_nama', ''));

        // Buat Tahun Ajaran baru jika dipilih opsi ini
        if ($isNew && $newTaNama) {
            $db = (new Model())->getDb();
            // Cek apakah TA sudah ada
            $existing = $db->prepare("SELECT id FROM tahun_ajaran WHERE nama = ?");
            $existing->execute([$newTaNama]);
            $existingRow = $existing->fetch(PDO::FETCH_ASSOC);
            if ($existingRow) {
                $tahunAjaranId = (int)$existingRow['id'];
            } else {
                $stmt = $db->prepare("INSERT INTO tahun_ajaran (nama, aktif) VALUES (?, 0)");
                $stmt->execute([$newTaNama]);
                $tahunAjaranId = (int)$db->lastInsertId();
            }
        }

        if (!$tahunAjaranId) {
            $this->flash('error', 'Tahun Ajaran wajib dipilih.');
            $this->redirect('/ppepp/create');
            return;
        }

        // Cek duplikasi
        $existing = (new Model())->queryOne(
            "SELECT id FROM ppepp_project WHERE user_id = ? AND tahun_ajaran_id = ?",
            [$user['id'], $tahunAjaranId]
        );
        if ($existing) {
            $this->flash('error', 'Project PPEPP untuk Tahun Ajaran tersebut sudah ada.');
            $this->redirect('/ppepp/create');
            return;
        }

        $db = (new Model())->getDb();
        // Ambil nama TA untuk judul default
        if (empty($judul)) {
            $ta = $db->prepare("SELECT nama FROM tahun_ajaran WHERE id = ?");
            $ta->execute([$tahunAjaranId]);
            $taNama = $ta->fetchColumn();
            $judul = 'PPEPP ' . $taNama;
        }

        $stmt = $db->prepare("INSERT INTO ppepp_project (user_id, tahun_ajaran_id, judul, deskripsi, status) VALUES (?, ?, ?, ?, 'aktif')");
        $stmt->execute([$user['id'], $tahunAjaranId, $judul, $deskripsi]);
        $newId = (int)$db->lastInsertId();

        $this->flash('success', 'Project PPEPP berhasil dibuat! Mulai isi siklus PPEPP untuk tahun ajaran ini.');
        $this->redirect('/ppepp/' . $newId);
    }

    // ============================================================
    // EDIT PROJECT
    // ============================================================
    public function edit(int $id): void
    {
        $this->requireAuth();
        $user    = $this->currentUser();
        $flash   = $this->getFlash();
        $project = $this->model->findOwned($id, $user['id']);
        if (!$project) {
            $this->flash('error', 'Project tidak ditemukan.');
            $this->redirect('/ppepp');
            return;
        }
        $allTa = $this->model->getAllTahunAjaran();
        $this->render('ppepp_project/edit', compact('user', 'project', 'allTa', 'flash'));
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        $user    = $this->currentUser();
        $project = $this->model->findOwned($id, $user['id']);
        if (!$project) {
            $this->flash('error', 'Project tidak ditemukan.');
            $this->redirect('/ppepp');
            return;
        }

        $judul     = trim($this->post('judul', ''));
        $deskripsi = trim($this->post('deskripsi', ''));
        $status    = $this->post('status', 'aktif');

        $db = (new Model())->getDb();
        $stmt = $db->prepare("UPDATE ppepp_project SET judul = ?, deskripsi = ?, status = ? WHERE id = ?");
        $stmt->execute([$judul, $deskripsi, $status, $id]);

        $this->flash('success', 'Project berhasil diperbarui.');
        $this->redirect('/ppepp/' . $id);
    }

    // ============================================================
    // TOGGLE STATUS / NONAKTIFKAN / AKTIFKAN PROJECT
    // ============================================================
    public function toggleStatus(int $id): void
    {
        $this->requireAuth();
        $user    = $this->currentUser();
        $project = $this->model->findOwned($id, $user['id']);
        if (!$project) {
            $this->flash('error', 'Project tidak ditemukan.');
            $this->redirect('/ppepp');
            return;
        }

        $targetStatus = $this->post('status', '');
        $allowedStatuses = ['aktif', 'nonaktif', 'arsip', 'selesai'];

        if (!in_array($targetStatus, $allowedStatuses)) {
            // Default toggle: jika saat ini aktif -> jadikan nonaktif, selain itu -> jadikan aktif
            $targetStatus = ($project['status'] === 'aktif') ? 'nonaktif' : 'aktif';
        }

        $db = (new Model())->getDb();
        $stmt = $db->prepare("UPDATE ppepp_project SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$targetStatus, $id, $user['id']]);

        $messageMap = [
            'aktif'    => 'Project PPEPP berhasil diaktifkan kembali.',
            'nonaktif' => 'Project PPEPP berhasil dinonaktifkan (disembunyikan). Seluruh data Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan tetap tersimpan aman.',
            'arsip'    => 'Project PPEPP berhasil diarsipkan. Seluruh data tetap tersimpan aman.',
            'selesai'  => 'Project PPEPP ditandai selesai.',
        ];

        $this->flash('success', $messageMap[$targetStatus] ?? 'Status project berhasil diperbarui.');

        $returnUrl = $this->post('return_url', '');
        if ($returnUrl) {
            $this->redirect($returnUrl);
        } else {
            if ($targetStatus === 'nonaktif') {
                $this->redirect('/ppepp');
            } else {
                $this->redirect('/ppepp/' . $id);
            }
        }
    }

    // ============================================================
    // NONAKTIFKAN / SEMBUNYIKAN PROJECT (Pengganti Hapus Permanen)
    // ============================================================
    public function delete(int $id): void
    {
        $this->requireAuth();
        $user    = $this->currentUser();
        $project = $this->model->findOwned($id, $user['id']);
        if (!$project) {
            $this->flash('error', 'Project tidak ditemukan.');
            $this->redirect('/ppepp');
            return;
        }

        // Jangan menghapus baris ppepp_project atau data penetapan dll, cukup ubah status menjadi nonaktif
        $db = (new Model())->getDb();
        $db->prepare("UPDATE ppepp_project SET status = 'nonaktif' WHERE id = ? AND user_id = ?")->execute([$id, $user['id']]);

        $this->flash('success', 'Project PPEPP berhasil dinonaktifkan (disembunyikan). Seluruh data Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan tetap aman tersimpan.');
        $this->redirect('/ppepp');
    }

    // ============================================================
    // HAPUS PERMANEN PROJECT BESERTA SELURUH ISI PPEPP SEKALIGUS
    // ============================================================
    public function destroy(int $id): void
    {
        $this->requireAuth();
        $user    = $this->currentUser();
        $project = $this->model->findOwned($id, $user['id']);

        if (!$project) {
            $this->flash('error', 'Project tidak ditemukan atau Anda tidak memiliki akses.');
            $this->redirect('/ppepp');
            return;
        }

        $db     = (new Model())->getDb();
        $taId   = (int)$project['tahun_ajaran_id'];
        $userId = (int)$user['id'];

        $db->beginTransaction();

        try {
            // 1. Kumpulkan semua Penetapan ID yang terikat ke project ini ATAU tahun ajaran ini
            $stmt = $db->prepare("SELECT id FROM penetapan WHERE (ppepp_project_id = ? OR (ppepp_project_id IS NULL AND tahun_ajaran_id = ?)) AND user_id = ?");
            $stmt->execute([$id, $taId, $userId]);
            $penetapanIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $inP = !empty($penetapanIds) ? implode(',', array_map('intval', $penetapanIds)) : '0';

            // 2. Kumpulkan semua Pelaksanaan ID yang terikat
            $stmt = $db->prepare("SELECT id FROM pelaksanaan WHERE (ppepp_project_id = ? OR penetapan_id IN ($inP)) AND user_id = ?");
            $stmt->execute([$id, $userId]);
            $pelaksanaanIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $inPl = !empty($pelaksanaanIds) ? implode(',', array_map('intval', $pelaksanaanIds)) : '0';

            // 3. Kumpulkan semua Evaluasi ID yang terikat
            $stmt = $db->prepare("SELECT id FROM evaluasi WHERE (ppepp_project_id = ? OR penetapan_id IN ($inP)) AND user_id = ?");
            $stmt->execute([$id, $userId]);
            $evaluasiIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $inE = !empty($evaluasiIds) ? implode(',', array_map('intval', $evaluasiIds)) : '0';

            // 4. Kumpulkan semua Pengendalian ID yang terikat
            $stmt = $db->prepare("SELECT id FROM pengendalian WHERE (ppepp_project_id = ? OR penetapan_id IN ($inP) OR evaluasi_id IN ($inE)) AND user_id = ?");
            $stmt->execute([$id, $userId]);
            $pengendalianIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $inPg = !empty($pengendalianIds) ? implode(',', array_map('intval', $pengendalianIds)) : '0';

            // 5. Kumpulkan semua Peningkatan ID yang terikat
            $stmt = $db->prepare("SELECT id FROM peningkatan WHERE (ppepp_project_id = ? OR penetapan_id IN ($inP) OR pengendalian_id IN ($inPg)) AND user_id = ?");
            $stmt->execute([$id, $userId]);
            $peningkatanIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $inPk = !empty($peningkatanIds) ? implode(',', array_map('intval', $peningkatanIds)) : '0';

            // Hapus Peningkatan & Dokumen & Detail
            if (!empty($peningkatanIds)) {
                $db->exec("DELETE FROM peningkatan_dokumen WHERE peningkatan_id IN ($inPk)");
                $db->exec("DELETE FROM peningkatan_detail WHERE peningkatan_id IN ($inPk)");
                $db->exec("DELETE FROM peningkatan WHERE id IN ($inPk)");
            }

            // Hapus Pengendalian & Detail
            if (!empty($pengendalianIds)) {
                $db->exec("DELETE FROM pengendalian_detail WHERE pengendalian_id IN ($inPg)");
                $db->exec("DELETE FROM pengendalian WHERE id IN ($inPg)");
            }

            // Hapus Evaluasi & Dokumen & Detail
            if (!empty($evaluasiIds)) {
                $db->exec("DELETE FROM evaluasi_dokumen WHERE evaluasi_id IN ($inE)");
                $db->exec("DELETE FROM evaluasi_detail WHERE evaluasi_id IN ($inE)");
                $db->exec("DELETE FROM evaluasi WHERE id IN ($inE)");
            }

            // Hapus Pelaksanaan & Bukti & Detail
            if (!empty($pelaksanaanIds)) {
                $db->exec("DELETE FROM pelaksanaan_bukti WHERE pelaksanaan_id IN ($inPl)");
                $db->exec("DELETE FROM pelaksanaan_detail WHERE pelaksanaan_id IN ($inPl)");
                $db->exec("DELETE FROM pelaksanaan WHERE id IN ($inPl)");
            }

            // Hapus Penetapan & Referensi & Detail
            if (!empty($penetapanIds)) {
                $db->exec("DELETE FROM referensi WHERE penetapan_id IN ($inP)");
                $db->exec("DELETE FROM penetapan_detail WHERE penetapan_id IN ($inP)");
                $db->exec("DELETE FROM penetapan WHERE id IN ($inP)");
            }

            // Hapus Project Utama
            $db->prepare("DELETE FROM ppepp_project WHERE id = ? AND user_id = ?")->execute([$id, $userId]);

            $db->commit();
            $this->flash('success', '🗑️ Project PPEPP "' . htmlspecialchars($project['judul']) . '" beserta SELURUH isi dokumen 5 tahap di dalamnya telah berhasil dihapus secara permanen.');
        } catch (Exception $e) {
            $db->rollBack();
            $this->flash('error', 'Gagal menghapus project: ' . $e->getMessage());
        }

        $this->redirect('/ppepp');
    }
}

