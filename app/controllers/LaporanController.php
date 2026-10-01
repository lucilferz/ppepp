<?php
/**
 * Controller: Laporan Eksekutif PPEPP
 * Platform PPEPP Fakultas — Ringkasan & Matriks Komprehensif 5 Tahap per Project
 */

class LaporanController extends Controller
{
    private Laporan      $model;
    private PpeppProject $projectModel;

    public function __construct()
    {
        parent::__construct();
        $this->model        = new Laporan();
        $this->projectModel = new PpeppProject();
    }

    // ============================================================
    // HALAMAN UTAMA LAPORAN PPEPP PER PROJECT
    // ============================================================
    public function index(): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $projects = $this->projectModel->findByUser($user['id']);

        $selectedProjectId = (int) $this->get('project_id', 0);
        if ($selectedProjectId <= 0 && !empty($projects)) {
            $selectedProjectId = (int) $projects[0]['id'];
        }

        $reportData = null;
        if ($selectedProjectId > 0) {
            $reportData = $this->model->getProjectReport($selectedProjectId, $user['id']);
        }

        $this->render('laporan/index', compact('user', 'projects', 'selectedProjectId', 'reportData', 'flash'));
    }

    // ============================================================
    // HALAMAN CETAK / PDF PRINT-READY
    // ============================================================
    public function cetak(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $projectId = (int) $this->get('project_id', 0);
        $projects  = $this->projectModel->findByUser($user['id']);

        if ($projectId <= 0 && !empty($projects)) {
            $projectId = (int) $projects[0]['id'];
        }

        $reportData = $this->model->getProjectReport($projectId, $user['id']);
        if (!$reportData) {
            $this->flash('error', 'Data laporan project tidak ditemukan.');
            $this->redirect('/laporan');
            return;
        }

        $this->render('laporan/cetak', compact('user', 'reportData'));
    }

    // ============================================================
    // EXPORT CSV / EXCEL MATRIX
    // ============================================================
    public function exportCsv(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $projectId = (int) $this->get('project_id', 0);
        $reportData = $this->model->getProjectReport($projectId, $user['id']);

        if (!$reportData) {
            $this->redirect('/laporan');
            return;
        }

        $filename = 'Laporan_Matriks_PPEPP_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $reportData['project']['ta_nama']) . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM for Excel
        fputs($out, "\xEF\xBB\xBF");

        // Title
        fputcsv($out, ['LAPORAN MATRIKS SIKLUS PPEPP FAKULTAS']);
        fputcsv($out, ['Project', $reportData['project']['judul']]);
        fputcsv($out, ['Tahun Ajaran', $reportData['project']['ta_nama'] . ' (' . ucfirst($reportData['project']['semester'] ?? '') . ')']);
        fputcsv($out, ['Tanggal Unduh', date('d/m/Y H:i')]);
        fputcsv($out, []);

        // Headers
        fputcsv($out, [
            'No',
            'Kode Standar',
            'Nama Kriteria Standar',
            '[P] Aturan / Dasar Hukum',
            '[P] Target Standar Awal',
            '[P] Indikator Ketercapaian',
            '[P] Status Pelaksanaan',
            '[P] Catatan Bukti Realisasi',
            '[E] Status Capaian Evaluasi',
            '[E] Analisis Evaluasi Mutu',
            '[P] Akar Masalah (Jika Belum Tercapai)',
            '[P] Rencana Tindak Lanjut (RTL)',
            '[P] PIC & Target Waktu RTL',
            '[P] Target Standar Baru (Peningkatan)',
            '[P] Indikator Baru (Peningkatan)',
            '[P] Program / Strategi Baru',
        ]);

        foreach ($reportData['matrix'] as $i => $m) {
            fputcsv($out, [
                $i + 1,
                $m['kriteria_kode'],
                $m['kriteria_nama'],
                $m['p1_aturan'],
                $m['p1_target'],
                $m['p1_indikator'],
                strtoupper($m['p2_status']),
                $m['p2_catatan'],
                strtoupper(str_replace('_', ' ', $m['e_status'])),
                $m['e_evaluasi'],
                $m['p4_akar_masalah'],
                $m['p4_rtl'],
                ($m['p4_pic'] ? $m['p4_pic'] . ' / ' . $m['p4_waktu'] : ''),
                $m['p5_target_baru'],
                $m['p5_indikator_baru'],
                $m['p5_strategi_baru'],
            ]);
        }

        fclose($out);
        exit;
    }
}
