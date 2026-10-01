<?php
/**
 * Controller: Peningkatan (Peningkatan Standar Mutu & SK Standar Baru)
 * Platform PPEPP Fakultas — Tahap P ketiga (Siklus Pembaruan Standar)
 */

class PeningkatanController extends Controller
{
    private Peningkatan  $model;
    private Pengendalian $pengendalianModel;
    private Penetapan    $penetapanModel;
    private Evaluasi     $evaluasiModel;

    public function __construct()
    {
        parent::__construct();
        $this->model              = new Peningkatan();
        $this->pengendalianModel  = new Pengendalian();
        $this->penetapanModel     = new Penetapan();
        $this->evaluasiModel      = new Evaluasi();
    }

    // ============================================================
    // DAFTAR PENINGKATAN
    // ============================================================
    public function index(): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $peningkatans = $this->model->findByUser($user['id']);
        $stats        = $this->model->statsByUser($user['id']);

        $this->render('peningkatan/index', compact('user', 'peningkatans', 'stats', 'flash'));
    }

    // ============================================================
    // FORM BUAT PENINGKATAN BARU
    // ============================================================
    public function create(): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $projectId = (int) ($this->get('project_id', 0) ?: $this->get('ppepp_project_id', 0));
        $db = $this->penetapanModel->getDb();
        $selectedProject = null;

        if ($projectId > 0) {
            $stmtPr = $db->prepare("SELECT pr.*, ta.nama AS ta_nama FROM ppepp_project pr JOIN tahun_ajaran ta ON ta.id = pr.tahun_ajaran_id WHERE pr.id = ? AND pr.user_id = ? LIMIT 1");
            $stmtPr->execute([$projectId, $user['id']]);
            $selectedProject = $stmtPr->fetch(PDO::FETCH_ASSOC);

            $stmt = $db->prepare(
                "SELECT p.*, ta.nama AS ta_nama, ta.semester,
                        (SELECT id FROM pengendalian WHERE penetapan_id = p.id AND user_id = ? ORDER BY created_at DESC LIMIT 1) AS pengendalian_id,
                        (SELECT judul FROM pengendalian WHERE penetapan_id = p.id AND user_id = ? ORDER BY created_at DESC LIMIT 1) AS pengendalian_judul,
                        (SELECT id FROM evaluasi WHERE penetapan_id = p.id AND user_id = ? ORDER BY created_at DESC LIMIT 1) AS evaluasi_id
                 FROM penetapan p
                 JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                 WHERE p.user_id = ? AND (p.ppepp_project_id = ? OR (p.ppepp_project_id IS NULL AND p.tahun_ajaran_id = (SELECT tahun_ajaran_id FROM ppepp_project WHERE id = ? AND user_id = ?)))
                 ORDER BY p.created_at DESC"
            );
            $stmt->execute([$user['id'], $user['id'], $user['id'], $user['id'], $projectId, $projectId, $user['id']]);
        } else {
            $stmt = $db->prepare(
                "SELECT p.*, ta.nama AS ta_nama, ta.semester,
                        (SELECT id FROM pengendalian WHERE penetapan_id = p.id AND user_id = ? ORDER BY created_at DESC LIMIT 1) AS pengendalian_id,
                        (SELECT judul FROM pengendalian WHERE penetapan_id = p.id AND user_id = ? ORDER BY created_at DESC LIMIT 1) AS pengendalian_judul,
                        (SELECT id FROM evaluasi WHERE penetapan_id = p.id AND user_id = ? ORDER BY created_at DESC LIMIT 1) AS evaluasi_id
                 FROM penetapan p
                 JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                 WHERE p.user_id = ?
                 ORDER BY p.created_at DESC"
            );
            $stmt->execute([$user['id'], $user['id'], $user['id'], $user['id']]);
        }
        $penetapans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('peningkatan/create', compact('user', 'penetapans', 'flash', 'projectId', 'selectedProject'));
    }

    // ============================================================
    // SIMPAN PENINGKATAN BARU
    // ============================================================
    public function store(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $penetapanId = (int) $this->post('penetapan_id', 0);
        $judul       = trim($this->post('judul', ''));
        $deskripsi   = trim($this->post('deskripsi', ''));

        if (!$penetapanId || empty($judul)) {
            $this->flash('error', 'Penetapan acuan dan judul peningkatan wajib diisi.');
            $this->redirect('/peningkatan/create');
            return;
        }

        $penetapan = $this->penetapanModel->find($penetapanId);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->flash('error', 'Penetapan tidak ditemukan.');
            $this->redirect('/peningkatan/create');
            return;
        }

        // Cari pengendalian terkait jika ada
        $db = $this->model->getDb();
        $stmtPg = $db->prepare("SELECT id FROM pengendalian WHERE penetapan_id = ? AND user_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmtPg->execute([$penetapanId, $user['id']]);
        $pengendalianId = (int)$stmtPg->fetchColumn();

        $pkId = $this->model->insert([
            'user_id'          => $user['id'],
            'ppepp_project_id' => $penetapan['ppepp_project_id'] ?? null,
            'penetapan_id'     => $penetapanId,
            'pengendalian_id'  => $pengendalianId > 0 ? $pengendalianId : null,
            'judul'            => $judul,
            'deskripsi'        => $deskripsi,
            'status'           => 'draft',
        ]);

        // Inisialisasi peningkatan_detail untuk setiap indikator di penetapan_detail
        $stmtK = $db->prepare("SELECT id, kriteria_id FROM penetapan_detail WHERE penetapan_id = ? ORDER BY id ASC");
        $stmtK->execute([$penetapanId]);
        $details = $stmtK->fetchAll(PDO::FETCH_ASSOC);

        foreach ($details as $d) {
            $this->model->saveDetail($pkId, (int)$d['kriteria_id'], [
                'status_indikator'    => 'ditingkatkan',
                'alasan_peningkatan'  => '',
                'target_baru'         => '',
                'indikator_baru'      => '',
                'strategi_baru'       => '',
                'nilai_kenaikan'      => '',
                'nomor_sk'            => '',
                'tanggal_sk'          => null,
            ], (int)$d['id']);
        }

        $this->flash('success', 'Dokumen Peningkatan berhasil dibuat. Silakan analisis standar yang terpenuhi untuk ditingkatkan.');
        $this->redirect('/peningkatan/' . $pkId . '/edit');
    }

    // ============================================================
    // FORM EDIT PENINGKATAN
    // ============================================================
    public function edit(int $id): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $peningkatan = $this->model->findWithDetails($id, $user['id']);
        if (!$peningkatan) {
            $this->flash('error', 'Peningkatan tidak ditemukan.');
            $this->redirect('/peningkatan');
            return;
        }

        // Ambil daftar Tahun Ajaran untuk opsi ekspor ke Penetapan baru
        $taModel = new TahunAjaran();
        $tahunAjarans = $taModel->findAll();

        $this->render('peningkatan/edit', compact('user', 'peningkatan', 'tahunAjarans', 'flash'));
    }

    // ============================================================
    // AJAX: Auto-Save field peningkatan_detail
    // ============================================================
    public function saveDetail(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $pkId              = (int) $this->post('peningkatan_id', 0);
        $kriteriaId        = (int) $this->post('kriteria_id', 0);
        $penetapanDetailId = (int) $this->post('penetapan_detail_id', 0);
        $field             = $this->post('field', '');
        $value             = $this->post('value', '');

        $pk = $this->model->find($pkId);
        if (!$pk || $pk['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Peningkatan tidak ditemukan.']);
            return;
        }

        $ok = $this->model->saveDetailField($pkId, $kriteriaId, $field, $value, $penetapanDetailId ?: null);
        $this->model->update($pkId, ['updated_at' => date('Y-m-d H:i:s')]);
        $this->json(['success' => $ok, 'saved_at' => date('H:i:s')]);
    }

    // ============================================================
    // AJAX: Generate AI Peningkatan Single Indikator / Kriteria
    // ============================================================
    public function generateAIPeningkatan(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $pkId              = (int) $this->post('peningkatan_id', 0);
        $kriteriaId        = (int) $this->post('kriteria_id', 0);
        $penetapanDetailId = (int) $this->post('penetapan_detail_id', 0);

        $pk = $this->model->findWithDetails($pkId, $user['id']);
        if (!$pk) {
            $this->json(['success' => false, 'message' => 'Peningkatan tidak ditemukan.']);
            return;
        }

        $targetKriteria = null;
        foreach ($pk['details'] as $d) {
            if ($penetapanDetailId > 0 && $d['id'] == $penetapanDetailId) {
                $targetKriteria = $d;
                break;
            } elseif ($d['kriteria_id'] == $kriteriaId) {
                $targetKriteria = $d;
                break;
            }
        }

        if (!$targetKriteria) {
            $this->json(['success' => false, 'message' => 'Indikator / kriteria tidak ditemukan.']);
            return;
        }

        $prompt = $this->buildPeningkatanPrompt($targetKriteria, $pk);

        $userModel   = new User();
        $userData    = $userModel->find($user['id']);
        $userApiKey  = $userData['gemini_api_key'] ?? '';
        $geminiModel = $userData['gemini_model'] ?? DEFAULT_GEMINI_MODEL;

        $result = $this->callGeminiAPI($prompt, $userApiKey, $geminiModel);

        if ($result['success']) {
            $parsed = $this->parsePeningkatanResponse($result['text']);

            if (!empty($parsed['alasan'])) {
                $this->model->saveDetailField($pkId, $kriteriaId, 'alasan_peningkatan', $parsed['alasan'], $penetapanDetailId ?: null);
            }
            if (!empty($parsed['target_baru'])) {
                $this->model->saveDetailField($pkId, $kriteriaId, 'target_baru', $parsed['target_baru'], $penetapanDetailId ?: null);
            }
            if (!empty($parsed['indikator_baru'])) {
                $this->model->saveDetailField($pkId, $kriteriaId, 'indikator_baru', $parsed['indikator_baru'], $penetapanDetailId ?: null);
            }
            if (!empty($parsed['strategi_baru'])) {
                $this->model->saveDetailField($pkId, $kriteriaId, 'strategi_baru', $parsed['strategi_baru'], $penetapanDetailId ?: null);
            }
            if (!empty($parsed['nilai_kenaikan'])) {
                $this->model->saveDetailField($pkId, $kriteriaId, 'nilai_kenaikan', $parsed['nilai_kenaikan'], $penetapanDetailId ?: null);
            }
            $this->json([
                'success'             => true,
                'penetapan_detail_id' => $penetapanDetailId,
                'alasan'              => $parsed['alasan'],
                'target_baru'         => $parsed['target_baru'],
                'indikator_baru'      => $parsed['indikator_baru'],
                'strategi_baru'       => $parsed['strategi_baru'],
                'nilai_kenaikan'      => $parsed['nilai_kenaikan'],
                'message'             => 'Rekomendasi Peningkatan Standar berhasil disusun oleh AI.',
            ]);
        } else {
            $this->json([
                'success'    => false,
                'message'    => $result['error'],
                'error_type' => $result['error_type'] ?? 'error',
            ]);
        }
    }

    // ============================================================
    // AJAX: Generate Semua AI Peningkatan untuk Standar Terpenuhi
    // ============================================================
    public function generateAllAIPeningkatan(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $pkId = (int) $this->post('peningkatan_id', 0);
        $pk   = $this->model->findWithDetails($pkId, $user['id']);

        if (!$pk || empty($pk['details'])) {
            $this->json(['success' => false, 'message' => 'Peningkatan tidak ditemukan.']);
            return;
        }

        $userModel   = new User();
        $userData    = $userModel->find($user['id']);
        $userApiKey  = $userData['gemini_api_key'] ?? '';
        $geminiModel = $userData['gemini_model'] ?? DEFAULT_GEMINI_MODEL;

        if (empty(trim($userApiKey))) {
            $this->json([
                'success'    => false,
                'error_type' => 'missing_key',
                'message'    => '🔑 API Key Gemini belum terpasang/diisi. Silakan masukkan API Key Gemini Anda di menu Pengaturan terlebih dahulu.',
            ]);
            return;
        }

        $results   = [];
        $skipped   = 0;
        $lastError = null;

        foreach ($pk['details'] as $d) {
            $kid  = (int)$d['kriteria_id'];
            $pdId = (int)$d['id'];
            $stCapaian = $d['status_capaian'] ?? 'belum_tercapai';

            // Hanya proses standar yang terpenuhi / terlampaui
            if ($stCapaian !== 'tercapai') {
                $skipped++;
                continue;
            }

            $prompt = $this->buildPeningkatanPrompt($d, $pk);
            $res = $this->callGeminiAPI($prompt, $userApiKey, $geminiModel);

            if ($res['success']) {
                $parsed = $this->parsePeningkatanResponse($res['text']);

                if (!empty($parsed['alasan'])) {
                    $this->model->saveDetailField($pkId, $kid, 'alasan_peningkatan', $parsed['alasan'], $pdId);
                }
                if (!empty($parsed['target_baru'])) {
                    $this->model->saveDetailField($pkId, $kid, 'target_baru', $parsed['target_baru'], $pdId);
                }
                if (!empty($parsed['indikator_baru'])) {
                    $this->model->saveDetailField($pkId, $kid, 'indikator_baru', $parsed['indikator_baru'], $pdId);
                }
                if (!empty($parsed['strategi_baru'])) {
                    $this->model->saveDetailField($pkId, $kid, 'strategi_baru', $parsed['strategi_baru'], $pdId);
                }
                if (!empty($parsed['nilai_kenaikan'])) {
                    $this->model->saveDetailField($pkId, $kid, 'nilai_kenaikan', $parsed['nilai_kenaikan'], $pdId);
                }

                $results[$pdId] = $parsed;
            } else {
                $lastError = $res;
            }
        }

        if (empty($results) && $lastError) {
            $this->json([
                'success'    => false,
                'error_type' => $lastError['error_type'] ?? 'error',
                'message'    => $lastError['error'] ?? 'Gagal generate Peningkatan AI.',
            ]);
            return;
        }

        $this->json([
            'success'   => true,
            'generated' => $results,
            'total'     => count($results),
            'skipped'   => $skipped,
            'message'   => 'Berhasil men-generate Peningkatan AI untuk ' . count($results) . ' standar yang terpenuhi.',
        ]);
    }

    /**
     * Bangun prompt AI Peningkatan Standar Mutu
     */
    private function buildPeningkatanPrompt(array $k, array $pk): string
    {
        $kode   = $k['kriteria_kode'] ?? $k['kode'] ?? 'KTR';
        $nama   = $k['kriteria_nama'] ?? '';
        $target = $k['target_capaian'] ?? '';
        $ind    = $k['indikator'] ?? '';
        $aturan = $k['strategi'] ?? '';
        $eval   = $k['evaluasi_teks'] ?? '';

        $prompt  = "Anda adalah Tim Peningkatan Mutu SPMI Perguruan Tinggi yang bertugas merumuskan peningkatan standar SPMI (Tahap P ketiga dalam siklus PPEPP).\n\n";
        $prompt .= "Kriteria ini telah TERCAPAI PENUH / TERPENUHI pada siklus berjalan. Tugas Anda adalah menganalisis potensi peningkatan dan merumuskan standar baru yang dinaikkan untuk siklus Penetapan tahun depan.\n\n";
        $prompt .= "ATURAN PENULISAN WAJIB:\n";
        $prompt .= "1. Jangan gunakan kalimat pengantar pembuka/penutup.\n";
        $prompt .= "2. DILARANG menggunakan karakter simbol (*, panah ->, emoji). Tulis teks baku dan profesional.\n";
        $prompt .= "3. Format output wajib persis seperti di bawah ini:\n\n";
        $prompt .= "---ALASAN PENINGKATAN---\n[Analisis mendalam mengapa standar ini layak dan siap ditingkatkan mutu/targetnya]\n\n";
        $prompt .= "---TARGET STANDAR BARU---\n[Rumusan pernyataan standar baru yang dinaikkan secara terukur dan berkualitas lebih tinggi]\n\n";
        $prompt .= "---INDIKATOR BARU---\n[Rumusan indikator ketercapaian baru yang lebih tinggi untuk mengukur standar baru tersebut]\n\n";
        $prompt .= "---STRATEGI PENCAPAIAN---\n[Program kerja atau strategi baru untuk mencapai standar yang ditingkatkan]\n\n";
        $prompt .= "---NILAI KENAIKAN---\n[Keterangan singkat besaran kenaikan target, misal: Naik dari 80% menjadi 95% atau Akreditasi Unggul]\n\n";
        $prompt .= "DATA STANDAR SAAT INI (TERPENUHI):\n";
        $prompt .= "- Kriteria: [{$kode}] {$nama}\n";
        if ($aturan) $prompt .= "- Dasar Aturan Saat Ini: {$aturan}\n";
        $prompt .= "- Target Standar Lama: {$target}\n";
        $prompt .= "- Indikator Lama: {$ind}\n";
        if ($eval)   $prompt .= "- Catatan Evaluasi: {$eval}\n";

        return $prompt;
    }

    /**
     * Parsing respons teks AI Peningkatan
     */
    private function parsePeningkatanResponse(string $rawText): array
    {
        $data = [
            'alasan'         => '',
            'target_baru'    => '',
            'indikator_baru' => '',
            'strategi_baru'  => '',
            'nilai_kenaikan' => 'Ditingkatkan 15-20%',
        ];

        if (preg_match('/---ALASAN PENINGKATAN---\s*(.*?)(?=---TARGET STANDAR BARU---|$)/si', $rawText, $m)) {
            $data['alasan'] = trim($m[1]);
        }
        if (preg_match('/---TARGET STANDAR BARU---\s*(.*?)(?=---INDIKATOR BARU---|$)/si', $rawText, $m)) {
            $data['target_baru'] = trim($m[1]);
        }
        if (preg_match('/---INDIKATOR BARU---\s*(.*?)(?=---STRATEGI PENCAPAIAN---|$)/si', $rawText, $m)) {
            $data['indikator_baru'] = trim($m[1]);
        }
        if (preg_match('/---STRATEGI PENCAPAIAN---\s*(.*?)(?=---NILAI KENAIKAN---|$)/si', $rawText, $m)) {
            $data['strategi_baru'] = trim($m[1]);
        }
        if (preg_match('/---NILAI KENAIKAN---\s*(.*?)$/si', $rawText, $m)) {
            $data['nilai_kenaikan'] = trim($m[1]);
        }

        if (empty($data['target_baru'])) {
            $data['target_baru'] = trim($rawText);
        }

        // Bersihkan seluruh simbol markdown (*, **, #, -, `, _) dari tiap field
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $v = str_replace(['**', '__', '~~', '```', '`', '#'], '', $v);
                $v = preg_replace('/^\s*[\-\*•\+]\s+/m', '', $v);
                $data[$k] = trim($v);
            }
        }

        return $data;
    }

    // ============================================================
    // AJAX: Upload Berkas SK Penetapan Standar Baru / Kebijakan
    // ============================================================
    public function uploadBerkasSk(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $pkId     = (int) $this->post('peningkatan_id', 0);
        $nomorSk  = trim($this->post('nomor_sk', ''));
        $judulSk  = trim($this->post('judul_sk', ''));
        $tglSk    = trim($this->post('tanggal_sk', ''));
        $ket      = trim($this->post('keterangan', ''));

        $pk = $this->model->find($pkId);
        if (!$pk || $pk['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Peningkatan tidak ditemukan.']);
            return;
        }

        if (!isset($_FILES['berkas_sk_file']) || $_FILES['berkas_sk_file']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'File SK wajib dipilih.']);
            return;
        }

        $file     = $_FILES['berkas_sk_file'];
        $origName = $file['name'];
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed  = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            $this->json(['success' => false, 'message' => 'Format file tidak diizinkan. Gunakan PDF, Word, atau Gambar.']);
            return;
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/peningkatan/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $safeName = 'sk_' . $pkId . '_' . time() . '_' . uniqid() . '.' . $ext;
        $destPath = $uploadDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->json(['success' => false, 'message' => 'Gagal menyimpan file SK.']);
            return;
        }

        $relPath  = 'uploads/peningkatan/' . $safeName;
        $currList = [];
        if (!empty($pk['berkas_sk'])) {
            $parsed = json_decode($pk['berkas_sk'], true);
            if (is_array($parsed)) $currList = $parsed;
        }

        $newItem = [
            'id'         => uniqid(),
            'file_path'  => $relPath,
            'file_name'  => $origName,
            'nomor_sk'   => $nomorSk ?: '—',
            'judul_sk'   => $judulSk ?: $origName,
            'tanggal_sk' => $tglSk ?: date('Y-m-d'),
            'keterangan' => $ket,
            'url'        => BASE_URL . '/' . $relPath,
            'time'       => date('d M Y H:i'),
        ];
        $currList[] = $newItem;

        $db = $this->model->getDb();
        $stmt = $db->prepare("UPDATE peningkatan SET berkas_sk = ?, sk_file = ?, sk_nomor = ?, sk_tanggal = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([
            json_encode($currList),
            $relPath,
            $nomorSk,
            !empty($tglSk) ? $tglSk : null,
            $pkId
        ]);

        $this->json([
            'success' => true,
            'berkas'  => $newItem,
            'total'   => count($currList),
            'message' => 'Berkas SK Peningkatan Standar berhasil diunggah.',
        ]);
    }

    // ============================================================
    // AJAX: Hapus Berkas SK
    // ============================================================
    public function deleteBerkasSk(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $pkId     = (int) $this->post('peningkatan_id', 0);
        $berkasId = $this->post('berkas_id', '');

        $pk = $this->model->find($pkId);
        if (!$pk || $pk['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Peningkatan tidak ditemukan.']);
            return;
        }

        $currList = [];
        if (!empty($pk['berkas_sk'])) {
            $parsed = json_decode($pk['berkas_sk'], true);
            if (is_array($parsed)) $currList = $parsed;
        }

        $updatedList = [];
        foreach ($currList as $item) {
            if ($item['id'] === $berkasId || $item['file_path'] === $berkasId) {
                $fullPath = dirname(__DIR__, 2) . '/public/' . $item['file_path'];
                if (file_exists($fullPath)) @unlink($fullPath);
            } else {
                $updatedList[] = $item;
            }
        }

        $db = $this->model->getDb();
        $stmt = $db->prepare("UPDATE peningkatan SET berkas_sk = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([json_encode($updatedList), $pkId]);

        $this->json([
            'success' => true,
            'total'   => count($updatedList),
            'message' => 'Berkas SK berhasil dihapus.',
        ]);
    }

    // ============================================================
    // GENERATE / EXPORT KE PENETAPAN SIKLUS TAHUN BERIKUTNYA
    // Sesuai permintaan user:
    // "Indikator yang masuk Peningkatan akan masuk ke Penetapan tahun berikutnya karena itu hasil peningkatannya.
    // Sedangkan indikator di pengendalian tetap masuk ke tahun berikutnya tanpa perubahan karena tidak terpenuhi (tapi bisa diedit)"
    // ============================================================
    public function exportToPenetapan(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $pk = $this->model->findWithDetails($id, $user['id']);
        if (!$pk) {
            $this->flash('error', 'Peningkatan tidak ditemukan.');
            $this->redirect('/peningkatan');
            return;
        }

        $targetTaId = (int) $this->post('target_ta_id', 0);
        $newTaFlag  = (int) $this->post('new_ta', 0);
        $newTaNama  = trim($this->post('new_ta_nama', ''));
        $judulBaru  = trim($this->post('judul_penetapan_baru', ''));

        $taModel = new TahunAjaran();
        $db = $this->penetapanModel->getDb();

        // Support penambahan Tahun Ajaran Baru jika diinput manual
        if ($newTaFlag === 1 && !empty($newTaNama)) {
            $stmtTa = $db->prepare("SELECT id FROM tahun_ajaran WHERE nama = ? LIMIT 1");
            $stmtTa->execute([$newTaNama]);
            $existingTaId = $stmtTa->fetchColumn();
            if ($existingTaId) {
                $targetTaId = (int)$existingTaId;
            } else {
                $stmtInsTa = $db->prepare("INSERT INTO tahun_ajaran (nama, semester, aktif, created_at, updated_at) VALUES (?, 'ganjil', 0, NOW(), NOW())");
                $stmtInsTa->execute([$newTaNama]);
                $targetTaId = (int)$db->lastInsertId();
            }
        }

        if ($targetTaId <= 0) {
            $this->flash('error', 'Pilih atau input Tahun Ajaran baru untuk Penetapan siklus berikutnya.');
            $this->redirect('/peningkatan/' . $id);
            return;
        }

        $ta = $taModel->find($targetTaId);
        if (!$ta) {
            $this->flash('error', 'Tahun Ajaran tujuan tidak valid.');
            $this->redirect('/peningkatan/' . $id);
            return;
        }

        if (empty($judulBaru)) {
            $judulBaru = 'Penetapan Standar Mutu ' . $ta['nama'] . ' (Hasil Peningkatan & Pengendalian)';
        }

        // 1. Cari atau Otomatis Buat Project PPEPP Baru untuk Tahun Ajaran ini
        $projModel = new PpeppProject();
        $existingProj = $projModel->queryOne(
            "SELECT * FROM ppepp_project WHERE user_id = ? AND tahun_ajaran_id = ? LIMIT 1",
            [$user['id'], $targetTaId]
        );

        if ($existingProj) {
            $targetProjectId = (int)$existingProj['id'];
        } else {
            // Otomatis buat folder project baru untuk tahun ajaran tujuan
            $targetProjectId = $projModel->insert([
                'user_id'         => $user['id'],
                'tahun_ajaran_id' => $targetTaId,
                'judul'           => 'PPEPP ' . $ta['nama'],
                'deskripsi'       => 'Siklus PPEPP baru hasil peningkatan dari Tahun Ajaran ' . ($pk['penetapan']['ta_nama'] ?? '') . '.',
                'status'          => 'aktif',
            ]);
        }

        // 2. Buat Dokumen Penetapan Baru terikat ke Project PPEPP Baru
        $stmtNewPen = $db->prepare(
            "INSERT INTO penetapan (user_id, ppepp_project_id, tahun_ajaran_id, judul, deskripsi, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, 'draft', NOW(), NOW())"
        );
        $stmtNewPen->execute([
            $user['id'],
            $targetProjectId,
            $targetTaId,
            $judulBaru,
            'Dokumen Penetapan Standar hasil Peningkatan & Pengendalian dari siklus T.A. ' . ($pk['penetapan']['ta_nama'] ?? '') . '.',
        ]);
        $newPenetapanId = (int) $db->lastInsertId();

        // 3. Loop semua kriteria dari penetapan lama
        // Jika kriteria ada di Peningkatan (status_capaian == 'tercapai' dan ada target_baru) -> gunakan target_baru & indikator_baru
        // Jika kriteria di Pengendalian (tidak terpenuhi) -> tetap bawa target_capaian & indikator lama (dapat diedit di penetapan baru)
        $stmtInsertDetail = $db->prepare(
            "INSERT INTO penetapan_detail (penetapan_id, kriteria_id, kode, target_capaian, indikator, strategi, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );

        $kriteriaIds = [];
        $ditingkatkanCount = 0;
        $tetapCount = 0;

        foreach ($pk['details'] as $d) {
            $kid       = (int) $d['kriteria_id'];
            $kriteriaIds[] = $kid;
            $kode      = $d['kriteria_kode'] ?? $d['kode'] ?? '';
            $stCapaian = $d['status_capaian'] ?? 'belum_tercapai';

            if ($stCapaian === 'tercapai' && !empty(trim($d['target_baru'] ?? ''))) {
                // HASIL PENINGKATAN MUTU
                $targetCapaianBaru = $d['target_baru'];
                $indikatorBaru     = !empty(trim($d['indikator_baru'] ?? '')) ? $d['indikator_baru'] : $d['indikator'];
                $strategiBaru      = !empty(trim($d['strategi_baru'] ?? '')) ? $d['strategi_baru'] : $d['strategi'];
                $ditingkatkanCount++;
            } else {
                // TETAP / DARI PENGENDALIAN (TETAP MASUK, BISA DIEDIT)
                $targetCapaianBaru = $d['target_capaian'];
                $indikatorBaru     = $d['indikator'];
                $strategiBaru      = $d['strategi'];
                $tetapCount++;
            }

            $stmtInsertDetail->execute([
                $newPenetapanId,
                $kid,
                $kode,
                $targetCapaianBaru,
                $indikatorBaru,
                $strategiBaru,
            ]);
        }

        // Update kriteria_ids di penetapan baru
        $db->prepare("UPDATE penetapan SET kriteria_ids = ? WHERE id = ?")->execute([
            json_encode(array_values(array_unique($kriteriaIds))),
            $newPenetapanId
        ]);

        $this->flash(
            'success',
            "🎉 Berhasil memulai siklus baru! Project PPEPP & Dokumen Penetapan Baru T.A. {$ta['nama']} telah otomatis dibuat ({$ditingkatkanCount} standar hasil peningkatan, {$tetapCount} standar dilanjutkan)."
        );
        $this->redirect('/ppepp/' . $targetProjectId);
    }

    // ============================================================
    // DETAIL / SHOW PENINGKATAN
    // ============================================================
    public function show(int $id): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $peningkatan = $this->model->findWithDetails($id, $user['id']);
        if (!$peningkatan) {
            $this->flash('error', 'Peningkatan tidak ditemukan.');
            $this->redirect('/peningkatan');
            return;
        }

        $taModel = new TahunAjaran();
        $tahunAjarans = $taModel->findAll();

        $this->render('peningkatan/show', compact('user', 'peningkatan', 'tahunAjarans', 'flash'));
    }

    // ============================================================
    // FINALISASI PENINGKATAN
    // ============================================================
    public function finalize(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $pk = $this->model->find($id);
        if (!$pk || $pk['user_id'] != $user['id']) {
            $this->flash('error', 'Peningkatan tidak ditemukan.');
            $this->redirect('/peningkatan');
            return;
        }

        $this->model->update($id, [
            'status'     => 'final',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->flash('success', 'Dokumen Peningkatan berhasil difinalisasi!');
        $this->redirect('/peningkatan/' . $id);
    }

    // ============================================================
    // PENINGKATAN - TOGGLE STATUS / AKTIFKAN / NONAKTIFKAN / HIDE
    // ============================================================
    public function toggleStatus(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $pk = $this->model->find($id);
        if (!$pk || $pk['user_id'] != $user['id']) {
            $this->flash('error', 'Peningkatan tidak ditemukan.');
            $target = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/peningkatan';
            $this->redirect($target);
            return;
        }

        $targetVis = trim($_POST['visibility_status'] ?? $_POST['status'] ?? '');
        $allowed   = ['aktif', 'nonaktif', 'hidden'];

        if (!in_array($targetVis, $allowed)) {
            if (isset($_POST['is_active'])) {
                $targetVis = ((int)$_POST['is_active'] === 1) ? 'aktif' : 'nonaktif';
            } else {
                $currentVis = $pk['visibility_status'] ?? 'aktif';
                $targetVis  = ($currentVis === 'aktif') ? 'nonaktif' : 'aktif';
            }
        }

        $isActiveVal = ($targetVis === 'aktif') ? 1 : 0;

        $db = $this->model->getDb();
        $db->prepare("UPDATE peningkatan SET visibility_status = ? WHERE id = ? AND user_id = ?")
           ->execute([$targetVis, $id, $user['id']]);

        $msgMap = [
            'aktif'    => 'Dokumen Peningkatan berhasil diaktifkan kembali dan tampil di daftar utama.',
            'nonaktif' => 'Dokumen Peningkatan berhasil dinonaktifkan (tetap terlihat di daftar dokumen).',
            'hidden'   => 'Dokumen Peningkatan berhasil disembunyikan (Hide). Anda dapat melihat dan mengaktifkannya kembali di menu Dokumen Tersembunyi.',
        ];

        $this->flash('success', $msgMap[$targetVis] ?? 'Status visibilitas dokumen berhasil diperbarui.');
        $target = !empty($_POST['return_url']) ? $_POST['return_url'] : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/peningkatan/' . $id);
        $this->redirect($target);
    }

    // ============================================================
    // HAPUS / NONAKTIFKAN PENINGKATAN (Soft-Hide)
    // ============================================================
    public function delete(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $pk = $this->model->find($id);
        if (!$pk || $pk['user_id'] != $user['id']) {
            $this->flash('error', 'Peningkatan tidak ditemukan.');
            $this->redirect('/peningkatan');
            return;
        }

        // Soft-hide: sembunyikan dokumen tanpa menghapus datanya
        $db = $this->model->getDb();
        $db->prepare("UPDATE peningkatan SET visibility_status = 'hidden' WHERE id = ? AND user_id = ?")->execute([$id, $user['id']]);

        $this->flash('success', 'Dokumen Peningkatan berhasil disembunyikan (Hide). Anda dapat melihat dan mengaktifkannya kembali di menu Dokumen Tersembunyi.');
        $target = !empty($_POST['return_url']) ? $_POST['return_url'] : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/peningkatan');
        $this->redirect($target);
    }



    /**
     * Memanggil Google Gemini API
     */
    private function callGeminiAPI(string $prompt, string $userApiKey = '', string $geminiModel = DEFAULT_GEMINI_MODEL): array
    {
        $apiKey = trim(!empty($userApiKey) ? $userApiKey : (defined('GEMINI_API_KEY') ? GEMINI_API_KEY : ''));

        if (empty($apiKey) || $apiKey === 'YOUR_GEMINI_API_KEY_HERE') {
            return [
                'success'    => false,
                'error_type' => 'missing_key',
                'error'      => '🔑 API Key Gemini belum terpasang. Silakan masukkan API Key Gemini Anda di menu Pengaturan (Setting) atau Profile Akun.',
            ];
        }

        $modelsToTry = array_unique([
            $geminiModel,
            'gemini-2.0-flash',
            'gemini-2.0-flash-lite',
            'gemini-1.5-flash',
            'gemini-1.5-pro',
        ]);

        $lastError     = '';
        $lastErrorType = 'api_error';

        foreach ($modelsToTry as $modelName) {
            $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/' . $modelName . ':generateContent?key=' . $apiKey;

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature'     => 0.3,
                    'maxOutputTokens' => 2048,
                ]
            ];

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error    = curl_error($ch);
            curl_close($ch);

            if ($error) return ['success' => false, 'error_type' => 'network_error', 'error' => 'Koneksi Error: ' . $error];

            $data = json_decode($response, true);

            if ($httpCode === 200 && isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $rawText = $data['candidates'][0]['content']['parts'][0]['text'];
                return [
                    'success' => true,
                    'text'    => trim($rawText),
                ];
            }

            $errMsg   = $data['error']['message'] ?? ('HTTP Status ' . $httpCode);
            $errLower = strtolower($errMsg);

            if ($httpCode === 429 || strpos($errLower, 'quota') !== false || strpos($errLower, 'resource_exhausted') !== false || strpos($errLower, 'rate limit') !== false || strpos($errLower, 'limit reached') !== false) {
                return [
                    'success'    => false,
                    'error_type' => 'quota_exceeded',
                    'error'      => '⚠️ Kuota API Key Gemini Anda telah habis atau melebihi batas penggunaan (Quota Exceeded / Rate Limit). Silakan tunggu beberapa menit, ganti model AI di Pengaturan, atau gunakan API Key Gemini baru.',
                ];
            }

            if ($httpCode === 400 || $httpCode === 403 || strpos($errLower, 'invalid') !== false || strpos($errLower, 'api_key_invalid') !== false) {
                return [
                    'success'    => false,
                    'error_type' => 'invalid_key',
                    'error'      => '🔑 API Key Gemini Anda tidak valid atau ditolak oleh Google. Silakan periksa kembali API Key Anda di menu Pengaturan (Setting).',
                ];
            }

            $lastError = $errMsg;
            if (strpos($errLower, 'not found') !== false || strpos($errLower, 'not supported') !== false) {
                continue;
            }
            break;
        }

        return ['success' => false, 'error_type' => $lastErrorType, 'error' => 'Gagal menghubungi Gemini AI: ' . ($lastError ?: 'Respons tidak valid.')];
    }
}
