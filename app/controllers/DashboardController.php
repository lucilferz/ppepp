<?php
/**
 * Controller: Dashboard
 * Platform PPEPP Fakultas
 */

class DashboardController extends Controller
{
    private Penetapan $penetapanModel;
    private Kriteria  $kriteriaModel;

    public function __construct()
    {
        parent::__construct();
        $this->penetapanModel = new Penetapan();
        $this->kriteriaModel  = new Kriteria();
    }

    /**
     * Tampilkan dashboard utama
     */
    public function index(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        // Statistik
        $stats = $this->penetapanModel->getStats($user['id']);
        $stats['kriteria'] = $this->kriteriaModel->countByUser($user['id']);

        // Penetapan terbaru
        $recentPenetapan = $this->penetapanModel->findByUser($user['id']);
        $recentPenetapan = array_slice($recentPenetapan, 0, 3);

        // Tahun ajaran aktif realtime berdasarkan kalender
        $tahunAktif = $this->penetapanModel->getRealtimeTahunAjaran();
        // Kriteria prodi
        $kriteria = $this->kriteriaModel->findByUser($user['id'], false);

        $flash = $this->getFlash();

        $db = (new Penetapan())->getDb();

        $incompleteItems = $this->fetchIncompleteItems($user['id']);

        $this->render('dashboard/index', [
            'user'            => $user,
            'stats'           => $stats,
            'recentPenetapan' => $recentPenetapan,
            'tahunAktif'      => $tahunAktif,
            'kriteria'        => $kriteria,
            'incompleteItems' => $incompleteItems,
            'flash'           => $flash,
            'db'              => $db,
        ]);
    }

    /**
     * Halaman khusus Dokumen Belum Dikerjakan & Monitoring Draft
     */
    public function pending(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        $flash = $this->getFlash();

        $isKaprodi = in_array($user['role'] ?? '', ['kaprodi', 'dekan']);
        $tugasModel = new TugasDokumen();
        $userModel = new User();

        $db = (new Penetapan())->getDb();
        $prodiId = (int)($user['prodi_id'] ?? 0);

        if ($prodiId > 0) {
            $stmtProj = $db->prepare("SELECT proj.*, ta.nama AS ta_nama
                                      FROM ppepp_project proj
                                      JOIN tahun_ajaran ta ON ta.id = proj.tahun_ajaran_id
                                      JOIN users u ON u.id = proj.user_id
                                      WHERE (proj.user_id = ? 
                                             OR u.prodi_id = ?
                                             OR EXISTS (SELECT 1 FROM tugas_dokumen td WHERE td.project_id = proj.id AND td.dosen_id = ?))
                                        AND (proj.status IS NULL OR proj.status != 'arsip')
                                      ORDER BY ta.nama DESC");
            $stmtProj->execute([$user['id'], $prodiId, $user['id']]);
        } else {
            $stmtProj = $db->prepare("SELECT proj.*, ta.nama AS ta_nama
                                      FROM ppepp_project proj
                                      JOIN tahun_ajaran ta ON ta.id = proj.tahun_ajaran_id
                                      WHERE (proj.user_id = ? 
                                             OR EXISTS (SELECT 1 FROM tugas_dokumen td WHERE td.project_id = proj.id AND td.dosen_id = ?))
                                        AND (proj.status IS NULL OR proj.status != 'arsip')
                                      ORDER BY ta.nama DESC");
            $stmtProj->execute([$user['id'], $user['id']]);
        }
        $projects = $stmtProj->fetchAll(PDO::FETCH_ASSOC);

        $incompleteItems = $this->fetchIncompleteItems($user['id']);

        $dosenList = [];
        $priorityTasks = [];

        $projectIds = array_column($projects, 'id');
        $taskMap = !empty($projectIds) ? $tugasModel->getMapByProjectIds($projectIds) : [];

        // Pasang data task pada masing-masing incomplete item (untuk Kaprodi maupun Dosen)
        foreach ($incompleteItems as &$item) {
            $key = $item['project_id'] . '_' . $item['stage'];
            $item['task'] = $taskMap[$key] ?? null;
        }
        unset($item);

        if ($isKaprodi) {
            // Kaprodi dapat menugaskan item ke dosen
            $dosenList = $userModel->getDosenList($user['id']);
        } else {
            // Dosen: Ambil tugas prioritas yang didelegasikan oleh Kaprodi
            $priorityTasks = $this->fetchPriorityTasksForDosen($user['id']);
        }

        $this->render('dashboard/pending', [
            'user'            => $user,
            'isKaprodi'       => $isKaprodi,
            'projects'        => $projects,
            'incompleteItems' => $incompleteItems,
            'priorityTasks'   => $priorityTasks,
            'dosenList'       => $dosenList,
            'flash'           => $flash,
        ]);
    }

    /**
     * Kaprodi menugaskan dokumen ke Dosen
     */
    public function assignTask(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        if (!in_array($user['role'] ?? '', ['kaprodi', 'dekan'])) {
            $this->flash('error', 'Hanya Kaprodi atau Dekan yang dapat menugaskan dokumen.');
            $this->redirect('/pending');
            return;
        }

        $projectId  = (int)$this->post('project_id', 0);
        $stage      = trim($this->post('stage', ''));
        $dosenId    = (int)$this->post('dosen_id', 0);
        $judulTugas = trim($this->post('judul_tugas', ''));
        $instruksi  = trim($this->post('instruksi', ''));
        $deadline   = trim($this->post('deadline', ''));
        $docId      = (int)$this->post('document_id', 0) ?: null;

        if (!$projectId || !$stage || !$dosenId) {
            $this->flash('error', 'Silakan pilih dosen dan tentukan item tugas dengan benar.');
            $this->redirect('/pending');
            return;
        }

        if (empty($judulTugas)) {
            $stageNames = [
                'penetapan'    => 'Penetapan Standar',
                'pelaksanaan'  => 'Pelaksanaan Standar',
                'evaluasi'     => 'Evaluasi Diri',
                'pengendalian' => 'Pengendalian (RTL)',
                'peningkatan'  => 'Peningkatan Standar',
            ];
            $judulTugas = 'Penyusunan ' . ($stageNames[$stage] ?? ucfirst($stage));
        }

        $tugasModel = new TugasDokumen();
        $tugasModel->assignTask($user['id'], $dosenId, $projectId, $stage, $docId, $judulTugas, $instruksi, $deadline);

        // Ambil nama dosen untuk feedback flash message
        $db = (new Penetapan())->getDb();
        $stmtU = $db->prepare("SELECT nama_lengkap FROM users WHERE id = ?");
        $stmtU->execute([$dosenId]);
        $dosenNama = $stmtU->fetchColumn() ?: 'dosen terpilih';

        $this->flash('success', "Tugas berhasil didelegasikan kepada {$dosenNama}! Penanda tugas prioritas telah dikirimkan ke akun dosen tersebut.");
        $this->redirect('/pending');
    }

    /**
     * Dosen menandai tugas selesai dan mengirim ke Kaprodi untuk dicek
     */
    public function submitTask(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        $taskId = (int)$this->post('task_id', 0);
        $catatanDosen = trim($this->post('catatan_dosen', ''));

        if (!$taskId) {
            $this->flash('error', 'Data tugas tidak valid.');
            $this->redirect('/pending');
            return;
        }

        $tugasModel = new TugasDokumen();
        $ok = $tugasModel->submitTask($taskId, $user['id'], $catatanDosen);

        if ($ok) {
            $this->flash('success', 'Tugas berhasil ditandai selesai dan diajukan ke Kaprodi untuk dicek & di-ACC!');
        } else {
            $this->flash('error', 'Gagal mengajukan tugas. Pastikan tugas ini ditugaskan kepada akun Anda.');
        }
        $this->redirect('/pending');
    }

    /**
     * Kaprodi menyetujui / meng-ACC tugas yang telah dikerjakan Dosen
     */
    public function accTask(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        if (!in_array($user['role'] ?? '', ['kaprodi', 'dekan'])) {
            $this->flash('error', 'Hanya Kaprodi atau Dekan yang dapat meng-ACC tugas.');
            $this->redirect('/pending');
            return;
        }

        $taskId = (int)$this->post('task_id', 0);
        if (!$taskId) {
            $this->flash('error', 'Data tugas tidak valid.');
            $this->redirect('/pending');
            return;
        }

        $tugasModel = new TugasDokumen();
        $ok = $tugasModel->approveTask($taskId, $user['id']);

        if ($ok) {
            $this->flash('success', 'Tugas berhasil di-ACC / disetujui! Status tugas kini telah selesai.');
        } else {
            $this->flash('error', 'Gagal menyetujui tugas.');
        }
        $this->redirect('/pending');
    }

    /**
     * Kaprodi meminta revisi ke Dosen dengan menambahkan catatan revisi
     */
    public function revisionTask(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        if (!in_array($user['role'] ?? '', ['kaprodi', 'dekan'])) {
            $this->flash('error', 'Hanya Kaprodi atau Dekan yang dapat meminta revisi.');
            $this->redirect('/pending');
            return;
        }

        $taskId = (int)$this->post('task_id', 0);
        $catatanRevisi = trim($this->post('catatan_revisi', ''));

        if (!$taskId || empty($catatanRevisi)) {
            $this->flash('error', 'Catatan revisi wajib diisi agar dosen dapat memperbaiki dokumen dengan tepat.');
            $this->redirect('/pending');
            return;
        }

        $tugasModel = new TugasDokumen();
        $ok = $tugasModel->requestRevision($taskId, $user['id'], $catatanRevisi);

        if ($ok) {
            $this->flash('warning', 'Catatan revisi berhasil dikirim! Status tugas diperbarui menjadi "Perlu Revisi" di akun dosen.');
        } else {
            $this->flash('error', 'Gagal mengirim catatan revisi.');
        }
        $this->redirect('/pending');
    }

    /**
     * Kaprodi membatalkan penugasan
     */
    public function cancelTask(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        if (!in_array($user['role'] ?? '', ['kaprodi', 'dekan'])) {
            $this->flash('error', 'Akses ditolak.');
            $this->redirect('/pending');
            return;
        }

        $taskId = (int)$this->post('task_id', 0);
        if ($taskId) {
            (new TugasDokumen())->cancelTask($taskId, $user['id']);
            $this->flash('info', 'Penugasan telah dibatalkan.');
        }
        $this->redirect('/pending');
    }

    /**
     * Ambil tugas prioritas yang didelegasikan oleh Kaprodi kepada Dosen
     */
    private function fetchPriorityTasksForDosen(int $dosenId): array
    {
        $tugasModel = new TugasDokumen();
        $rawTasks = $tugasModel->getPriorityTasksForDosen($dosenId);

        $results = [];
        $stageNames = [
            'penetapan'    => 'Penetapan Standar',
            'pelaksanaan'  => 'Pelaksanaan Standar',
            'evaluasi'     => 'Evaluasi Diri',
            'pengendalian' => 'Pengendalian (RTL)',
            'peningkatan'  => 'Peningkatan Standar',
        ];

        foreach ($rawTasks as $t) {
            $projId = (int)$t['project_id'];
            $stage  = $t['stage'];
            $taNama = $t['ta_nama'];

            // Resolusi dokumen aktual di DB
            $docInfo = $this->resolveDocumentForStage($projId, $stage);

            if (!$docInfo) {
                $status = 'empty';
                $statusLabel = 'Belum Dibuat';
                $actionUrl = BASE_URL . "/{$stage}/create?project_id={$projId}&task_id={$t['id']}";
                $actionLabel = '+ Kerjakan Dokumen Sekarang';
            } elseif ($docInfo['status'] === 'draft') {
                $status = 'draft';
                $statusLabel = 'Draft / Sedang Dikerjakan';
                if ($stage === 'penetapan') {
                    $actionUrl = BASE_URL . "/penetapan/{$docInfo['id']}/edit-step2";
                } else {
                    $actionUrl = BASE_URL . "/{$stage}/{$docInfo['id']}/edit";
                }
                $actionLabel = '✍️ Lanjutkan Mengerjakan';
            } else {
                $status = 'final';
                $statusLabel = 'Dokumen Selesai';
                $actionUrl = BASE_URL . "/{$stage}/{$docInfo['id']}";
                $actionLabel = '👁️ Lihat Hasil Dokumen';
            }

            $results[] = [
                'task_id'        => (int)$t['id'],
                'project_id'     => $projId,
                'stage'          => $stage,
                'stage_name'     => $stageNames[$stage] ?? ucfirst($stage),
                'ta_nama'        => $taNama,
                'project_judul'  => $t['project_judul'],
                'title'          => $t['judul_tugas'],
                'instruksi'      => $t['instruksi'],
                'deadline'       => $t['deadline'],
                'task_status'    => $t['status'], // assigned, submitted, revision, approved
                'catatan_dosen'  => $t['catatan_dosen'],
                'catatan_revisi' => $t['catatan_revisi'],
                'kaprodi_nama'   => $t['kaprodi_nama'],
                'kaprodi_email'  => $t['kaprodi_email'],
                'assigned_at'    => $t['assigned_at'],
                'submitted_at'   => $t['submitted_at'],
                'reviewed_at'    => $t['reviewed_at'],
                'doc_id'         => $docInfo['id'] ?? null,
                'status'         => $status,
                'status_label'   => $statusLabel,
                'action_url'     => $actionUrl,
                'action_label'   => $actionLabel,
                'is_priority'    => true,
            ];
        }

        return $results;
    }

    /**
     * Resolusi dokumen spesifik berdasarkan project_id dan tahapan PPEPP
     */
    private function resolveDocumentForStage(int $projectId, string $stage): ?array
    {
        $db = (new Penetapan())->getDb();
        $allowed = ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'];
        if (!in_array($stage, $allowed)) {
            return null;
        }

        $stmt = $db->prepare("SELECT id, judul, status FROM `{$stage}` WHERE ppepp_project_id = ? AND (visibility_status IS NULL OR visibility_status != 'hidden') ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$projectId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row && $stage !== 'penetapan') {
            $stmtP = $db->prepare("SELECT id FROM penetapan WHERE ppepp_project_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmtP->execute([$projectId]);
            $p = $stmtP->fetch(PDO::FETCH_ASSOC);
            if ($p) {
                $stmtDoc = $db->prepare("SELECT id, judul, status FROM `{$stage}` WHERE penetapan_id = ? AND (visibility_status IS NULL OR visibility_status != 'hidden') ORDER BY created_at DESC LIMIT 1");
                $stmtDoc->execute([$p['id']]);
                $row = $stmtDoc->fetch(PDO::FETCH_ASSOC);
            }
        }

        return $row ?: null;
    }

    /**
     * Ambil daftar dokumen PPEPP yang belum dikerjakan / masih draft / kosong
     */
    private function fetchIncompleteItems(int $userId): array
    {
        $db = (new Penetapan())->getDb();
        $incompleteItems = [];
        $stmtUser = $db->prepare("SELECT role, prodi_id FROM users WHERE id = ?");
        $stmtUser->execute([$userId]);
        $uInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);
        $prodiId = (int)($uInfo['prodi_id'] ?? 0);

        if ($prodiId > 0) {
            $stmtProj = $db->prepare("SELECT proj.*, ta.nama AS ta_nama
                                      FROM ppepp_project proj
                                      JOIN tahun_ajaran ta ON ta.id = proj.tahun_ajaran_id
                                      JOIN users u ON u.id = proj.user_id
                                      WHERE (proj.user_id = ? 
                                             OR u.prodi_id = ?
                                             OR EXISTS (SELECT 1 FROM tugas_dokumen td WHERE td.project_id = proj.id AND td.dosen_id = ?))
                                        AND (proj.status IS NULL OR proj.status != 'arsip')
                                      ORDER BY ta.nama DESC");
            $stmtProj->execute([$userId, $prodiId, $userId]);
        } else {
            $stmtProj = $db->prepare("SELECT proj.*, ta.nama AS ta_nama
                                      FROM ppepp_project proj
                                      JOIN tahun_ajaran ta ON ta.id = proj.tahun_ajaran_id
                                      WHERE (proj.user_id = ? 
                                             OR EXISTS (SELECT 1 FROM tugas_dokumen td WHERE td.project_id = proj.id AND td.dosen_id = ?))
                                        AND (proj.status IS NULL OR proj.status != 'arsip')
                                      ORDER BY ta.nama DESC");
            $stmtProj->execute([$userId, $userId]);
        }
        $projects = $stmtProj->fetchAll(PDO::FETCH_ASSOC);

        foreach ($projects as $proj) {
            $projId = (int)$proj['id'];
            $taId   = (int)$proj['tahun_ajaran_id'];
            $taNama = $proj['ta_nama'];

            // 1. Penetapan
            $stmt = $db->prepare("SELECT * FROM penetapan WHERE (ppepp_project_id = ? OR (ppepp_project_id IS NULL AND tahun_ajaran_id = ?)) AND (visibility_status IS NULL OR visibility_status != 'hidden') ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$projId, $taId]);
            $p = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$p) {
                $incompleteItems[] = [
                    'project_id'  => $projId,
                    'stage'       => 'penetapan',
                    'stage_name'  => 'Penetapan Standar',
                    'ta_nama'     => $taNama,
                    'title'       => 'Penetapan Standar TA ' . $taNama,
                    'status'      => 'empty',
                    'status_label'=> 'Belum Dibuat',
                    'desc'        => 'Dokumen Penetapan Standar belum ada untuk tahun ajaran ini.',
                    'action_url'  => BASE_URL . '/penetapan/create?project_id=' . $projId,
                    'action_label'=> '+ Buat Penetapan Sekarang',
                    'badge_bg'    => '#fef2f2',
                    'badge_text'  => '#dc2626',
                    'badge_border'=>'#fca5a5',
                ];
            } elseif ($p['status'] === 'draft') {
                $incompleteItems[] = [
                    'project_id'  => $projId,
                    'stage'       => 'penetapan',
                    'stage_name'  => 'Penetapan Standar',
                    'ta_nama'     => $taNama,
                    'title'       => $p['judul'],
                    'status'      => 'draft',
                    'status_label'=> 'Draft / Belum Final',
                    'desc'        => 'Dokumen Penetapan masih berstatus draft dan perlu dilengkapi.',
                    'action_url'  => BASE_URL . '/penetapan/' . $p['id'] . '/edit-step2',
                    'action_label'=> '✍️ Langsung Kerjakan',
                    'badge_bg'    => '#fffbeb',
                    'badge_text'  => '#d97706',
                    'badge_border'=>'#fde68a',
                ];
            }

            // 2. Pelaksanaan
            $stmt = $db->prepare("SELECT * FROM pelaksanaan WHERE (ppepp_project_id = ? OR (ppepp_project_id IS NULL AND penetapan_id IN (SELECT id FROM penetapan WHERE ppepp_project_id = ? OR tahun_ajaran_id = ?))) AND (visibility_status IS NULL OR visibility_status != 'hidden') ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$projId, $projId, $taId]);
            $pl = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pl) {
                $incompleteItems[] = [
                    'project_id'  => $projId,
                    'stage'       => 'pelaksanaan',
                    'stage_name'  => 'Pelaksanaan Standar',
                    'ta_nama'     => $taNama,
                    'title'       => 'Pelaksanaan Standar TA ' . $taNama,
                    'status'      => 'empty',
                    'status_label'=> 'Belum Dibuat',
                    'desc'        => 'Pelaksanaan & pemenuhan bukti belum dimulai.',
                    'action_url'  => BASE_URL . '/pelaksanaan/create?project_id=' . $projId,
                    'action_label'=> '⚡ Buat Pelaksanaan & Bukti',
                    'badge_bg'    => '#fef2f2',
                    'badge_text'  => '#dc2626',
                    'badge_border'=>'#fca5a5',
                ];
            } elseif ($pl['status'] === 'draft') {
                $incompleteItems[] = [
                    'project_id'  => $projId,
                    'stage'       => 'pelaksanaan',
                    'stage_name'  => 'Pelaksanaan Standar',
                    'ta_nama'     => $taNama,
                    'title'       => $pl['judul'],
                    'status'      => 'draft',
                    'status_label'=> 'Draft / Bukti Belum Lengkap',
                    'desc'        => 'Bukti pelaksanaan masih dalam proses pengunggahan.',
                    'action_url'  => BASE_URL . '/pelaksanaan/' . $pl['id'] . '/edit',
                    'action_label'=> '✍️ Edit & Kelola Bukti',
                    'badge_bg'    => '#fffbeb',
                    'badge_text'  => '#d97706',
                    'badge_border'=>'#fde68a',
                ];
            }

            // 3. Evaluasi
            $stmt = $db->prepare("SELECT * FROM evaluasi WHERE (ppepp_project_id = ? OR (ppepp_project_id IS NULL AND penetapan_id IN (SELECT id FROM penetapan WHERE ppepp_project_id = ? OR tahun_ajaran_id = ?))) AND (visibility_status IS NULL OR visibility_status != 'hidden') ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$projId, $projId, $taId]);
            $ev = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ev) {
                $incompleteItems[] = [
                    'project_id'  => $projId,
                    'stage'       => 'evaluasi',
                    'stage_name'  => 'Evaluasi Diri',
                    'ta_nama'     => $taNama,
                    'title'       => 'Evaluasi Capaian TA ' . $taNama,
                    'status'      => 'empty',
                    'status_label'=> 'Belum Dibuat',
                    'desc'        => 'Laporan Evaluasi Diri (Audit) belum dibuat.',
                    'action_url'  => BASE_URL . '/evaluasi/create?project_id=' . $projId,
                    'action_label'=> '+ Buat Evaluasi Diri',
                    'badge_bg'    => '#fef2f2',
                    'badge_text'  => '#dc2626',
                    'badge_border'=>'#fca5a5',
                ];
            } elseif ($ev['status'] === 'draft') {
                $incompleteItems[] = [
                    'project_id'  => $projId,
                    'stage'       => 'evaluasi',
                    'stage_name'  => 'Evaluasi Diri',
                    'ta_nama'     => $taNama,
                    'title'       => $ev['judul'],
                    'status'      => 'draft',
                    'status_label'=> 'Draft / Belum Diisi',
                    'desc'        => 'Analisis evaluasi & berita acara rapat belum final.',
                    'action_url'  => BASE_URL . '/evaluasi/' . $ev['id'] . '/edit',
                    'action_label'=> '✍️ Langsung Kerjakan',
                    'badge_bg'    => '#fffbeb',
                    'badge_text'  => '#d97706',
                    'badge_border'=>'#fde68a',
                ];
            }

            // 4. Pengendalian
            $stmt = $db->prepare("SELECT * FROM pengendalian WHERE (ppepp_project_id = ? OR (ppepp_project_id IS NULL AND evaluasi_id IN (SELECT id FROM evaluasi WHERE penetapan_id IN (SELECT id FROM penetapan WHERE ppepp_project_id = ? OR tahun_ajaran_id = ?)))) AND (visibility_status IS NULL OR visibility_status != 'hidden') ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$projId, $projId, $taId]);
            $pg = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pg) {
                $incompleteItems[] = [
                    'project_id'  => $projId,
                    'stage'       => 'pengendalian',
                    'stage_name'  => 'Pengendalian (RTL)',
                    'ta_nama'     => $taNama,
                    'title'       => 'Rencana Tindak Lanjut TA ' . $taNama,
                    'status'      => 'empty',
                    'status_label'=> 'Belum Dibuat',
                    'desc'        => 'Dokumen Pengendalian (RTL) belum disusun.',
                    'action_url'  => BASE_URL . '/pengendalian/create?project_id=' . $projId,
                    'action_label'=> '+ Buat RTL Sekarang',
                    'badge_bg'    => '#fef2f2',
                    'badge_text'  => '#dc2626',
                    'badge_border'=>'#fca5a5',
                ];
            } elseif ($pg['status'] === 'draft') {
                $incompleteItems[] = [
                    'project_id'  => $projId,
                    'stage'       => 'pengendalian',
                    'stage_name'  => 'Pengendalian (RTL)',
                    'ta_nama'     => $taNama,
                    'title'       => $pg['judul'],
                    'status'      => 'draft',
                    'status_label'=> 'Draft / RTL Kosong',
                    'desc'        => 'Tindakan koreksi & Notulensi rapat belum lengkap.',
                    'action_url'  => BASE_URL . '/pengendalian/' . $pg['id'] . '/edit',
                    'action_label'=> '✍️ Langsung Kerjakan',
                    'badge_bg'    => '#fffbeb',
                    'badge_text'  => '#d97706',
                    'badge_border'=>'#fde68a',
                ];
            }

            // 5. Peningkatan
            $stmt = $db->prepare("SELECT * FROM peningkatan WHERE (ppepp_project_id = ? OR (ppepp_project_id IS NULL AND pengendalian_id IN (SELECT id FROM pengendalian WHERE evaluasi_id IN (SELECT id FROM evaluasi WHERE penetapan_id IN (SELECT id FROM penetapan WHERE ppepp_project_id = ? OR tahun_ajaran_id = ?))))) AND (visibility_status IS NULL OR visibility_status != 'hidden') ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$projId, $projId, $taId]);
            $pk = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pk) {
                $incompleteItems[] = [
                    'project_id'  => $projId,
                    'stage'       => 'peningkatan',
                    'stage_name'  => 'Peningkatan Standar',
                    'ta_nama'     => $taNama,
                    'title'       => 'Peningkatan Standar Baru TA ' . $taNama,
                    'status'      => 'empty',
                    'status_label'=> 'Belum Dibuat',
                    'desc'        => 'Rencana Peningkatan Standar Baru belum dibuat.',
                    'action_url'  => BASE_URL . '/peningkatan/create?project_id=' . $projId,
                    'action_label'=> '+ Buat Peningkatan Standar',
                    'badge_bg'    => '#fef2f2',
                    'badge_text'  => '#dc2626',
                    'badge_border'=>'#fca5a5',
                ];
            } elseif ($pk['status'] === 'draft') {
                $incompleteItems[] = [
                    'project_id'  => $projId,
                    'stage'       => 'peningkatan',
                    'stage_name'  => 'Peningkatan Standar',
                    'ta_nama'     => $taNama,
                    'title'       => $pk['judul'],
                    'status'      => 'draft',
                    'status_label'=> 'Draft / Belum Final',
                    'desc'        => 'Revisi target standar & SK belum selesai.',
                    'action_url'  => BASE_URL . '/peningkatan/' . $pk['id'] . '/edit',
                    'action_label'=> '✍️ Langsung Kerjakan',
                    'badge_bg'    => '#fffbeb',
                    'badge_text'  => '#d97706',
                    'badge_border'=>'#fde68a',
                ];
            }
        }

        return $incompleteItems;
    }
}
