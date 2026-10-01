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

        $db = (new Penetapan())->getDb();
        $stmtProj = $db->prepare("SELECT proj.*, ta.nama AS ta_nama
                                  FROM ppepp_project proj
                                  JOIN tahun_ajaran ta ON ta.id = proj.tahun_ajaran_id
                                  WHERE proj.user_id = ? AND (proj.status IS NULL OR proj.status != 'arsip')
                                  ORDER BY ta.nama DESC");
        $stmtProj->execute([$user['id']]);
        $projects = $stmtProj->fetchAll(PDO::FETCH_ASSOC);

        $incompleteItems = $this->fetchIncompleteItems($user['id']);

        $this->render('dashboard/pending', [
            'user'            => $user,
            'projects'        => $projects,
            'incompleteItems' => $incompleteItems,
            'flash'           => $flash,
        ]);
    }

    /**
     * Ambil daftar dokumen PPEPP yang belum dikerjakan / masih draft / kosong
     */
    private function fetchIncompleteItems(int $userId): array
    {
        $db = (new Penetapan())->getDb();
        $incompleteItems = [];
        $stmtProj = $db->prepare("SELECT proj.*, ta.nama AS ta_nama
                                  FROM ppepp_project proj
                                  JOIN tahun_ajaran ta ON ta.id = proj.tahun_ajaran_id
                                  WHERE proj.user_id = ? AND (proj.status IS NULL OR proj.status != 'arsip')
                                  ORDER BY ta.nama DESC");
        $stmtProj->execute([$userId]);
        $projects = $stmtProj->fetchAll(PDO::FETCH_ASSOC);

        foreach ($projects as $proj) {
            $projId = (int)$proj['id'];
            $taId   = (int)$proj['tahun_ajaran_id'];
            $taNama = $proj['ta_nama'];

            // 1. Penetapan
            $stmt = $db->prepare("SELECT * FROM penetapan WHERE (ppepp_project_id = ? OR (ppepp_project_id IS NULL AND tahun_ajaran_id = ?)) AND user_id = ? AND (visibility_status IS NULL OR visibility_status != 'hidden') ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$projId, $taId, $userId]);
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
            $stmt = $db->prepare("SELECT * FROM pelaksanaan WHERE (ppepp_project_id = ? OR (ppepp_project_id IS NULL AND penetapan_id IN (SELECT id FROM penetapan WHERE tahun_ajaran_id = ?))) AND user_id = ? AND (visibility_status IS NULL OR visibility_status != 'hidden') ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$projId, $taId, $userId]);
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
            $stmt = $db->prepare("SELECT * FROM evaluasi WHERE (ppepp_project_id = ? OR (ppepp_project_id IS NULL AND penetapan_id IN (SELECT id FROM penetapan WHERE tahun_ajaran_id = ?))) AND user_id = ? AND (visibility_status IS NULL OR visibility_status != 'hidden') ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$projId, $taId, $userId]);
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
            $stmt = $db->prepare("SELECT * FROM pengendalian WHERE (ppepp_project_id = ? OR (ppepp_project_id IS NULL AND evaluasi_id IN (SELECT id FROM evaluasi WHERE penetapan_id IN (SELECT id FROM penetapan WHERE tahun_ajaran_id = ?)))) AND user_id = ? AND (visibility_status IS NULL OR visibility_status != 'hidden') ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$projId, $taId, $userId]);
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
            $stmt = $db->prepare("SELECT * FROM peningkatan WHERE (ppepp_project_id = ? OR (ppepp_project_id IS NULL AND pengendalian_id IN (SELECT id FROM pengendalian WHERE evaluasi_id IN (SELECT id FROM evaluasi WHERE penetapan_id IN (SELECT id FROM penetapan WHERE tahun_ajaran_id = ?))))) AND user_id = ? AND (visibility_status IS NULL OR visibility_status != 'hidden') ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$projId, $taId, $userId]);
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
