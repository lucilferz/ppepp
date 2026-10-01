<?php
/**
 * Controller: Pengendalian (Rencana Tindak Lanjut & Koreksi Standar)
 * Platform PPEPP Fakultas
 */

class PengendalianController extends Controller
{
    private Pengendalian $model;
    private Evaluasi     $evaluasiModel;
    private Penetapan    $penetapanModel;

    public function __construct()
    {
        parent::__construct();
        $this->model          = new Pengendalian();
        $this->evaluasiModel  = new Evaluasi();
        $this->penetapanModel = new Penetapan();
    }

    // ============================================================
    // DAFTAR PENGENDALIAN
    // ============================================================
    public function index(): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $pengendalians = $this->model->findByUser($user['id']);
        $stats         = $this->model->statsByUser($user['id']);

        $this->render('pengendalian/index', compact('user', 'pengendalians', 'stats', 'flash'));
    }

    // ============================================================
    // FORM BUAT PENGENDALIAN BARU (PILIH PENETAPAN ACUAN)
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
                        (SELECT id FROM evaluasi WHERE penetapan_id = p.id AND user_id = ? ORDER BY created_at DESC LIMIT 1) AS evaluasi_id,
                        (SELECT judul FROM evaluasi WHERE penetapan_id = p.id AND user_id = ? ORDER BY created_at DESC LIMIT 1) AS evaluasi_judul
                 FROM penetapan p
                 JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                 WHERE p.user_id = ? AND (p.ppepp_project_id = ? OR (p.ppepp_project_id IS NULL AND p.tahun_ajaran_id = (SELECT tahun_ajaran_id FROM ppepp_project WHERE id = ? AND user_id = ?)))
                 ORDER BY p.created_at DESC"
            );
            $stmt->execute([$user['id'], $user['id'], $user['id'], $projectId, $projectId, $user['id']]);
        } else {
            $stmt = $db->prepare(
                "SELECT p.*, ta.nama AS ta_nama, ta.semester,
                        (SELECT id FROM evaluasi WHERE penetapan_id = p.id AND user_id = ? ORDER BY created_at DESC LIMIT 1) AS evaluasi_id,
                        (SELECT judul FROM evaluasi WHERE penetapan_id = p.id AND user_id = ? ORDER BY created_at DESC LIMIT 1) AS evaluasi_judul
                 FROM penetapan p
                 JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
                 WHERE p.user_id = ?
                 ORDER BY p.created_at DESC"
            );
            $stmt->execute([$user['id'], $user['id'], $user['id']]);
        }
        $penetapans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('pengendalian/create', compact('user', 'penetapans', 'flash', 'projectId', 'selectedProject'));
    }

    // ============================================================
    // SIMPAN PENGENDALIAN BARU
    // ============================================================
    public function store(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $penetapanId = (int) $this->post('penetapan_id', 0);
        $judul       = trim($this->post('judul', ''));
        $deskripsi   = trim($this->post('deskripsi', ''));

        if (!$penetapanId || empty($judul)) {
            $this->flash('error', 'Penetapan acuan dan judul pengendalian wajib diisi.');
            $this->redirect('/pengendalian/create');
            return;
        }

        $penetapan = $this->penetapanModel->find($penetapanId);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->flash('error', 'Penetapan tidak ditemukan.');
            $this->redirect('/pengendalian/create');
            return;
        }

        // Cari evaluasi terkait
        $db = $this->model->getDb();
        $stmtEv = $db->prepare("SELECT id FROM evaluasi WHERE penetapan_id = ? AND user_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmtEv->execute([$penetapanId, $user['id']]);
        $evaluasiId = (int)$stmtEv->fetchColumn();

        $pgId = $this->model->insert([
            'user_id'          => $user['id'],
            'ppepp_project_id' => $penetapan['ppepp_project_id'] ?? null,
            'penetapan_id'     => $penetapanId,
            'evaluasi_id'      => $evaluasiId > 0 ? $evaluasiId : null,
            'judul'            => $judul,
            'deskripsi'        => $deskripsi,
            'status'           => 'draft',
        ]);

        // Inisialisasi pengendalian_detail untuk setiap indikator di penetapan_detail
        $stmtK = $db->prepare("SELECT id, kriteria_id FROM penetapan_detail WHERE penetapan_id = ? ORDER BY id ASC");
        $stmtK->execute([$penetapanId]);
        $details = $stmtK->fetchAll(PDO::FETCH_ASSOC);

        foreach ($details as $d) {
            $this->model->saveDetail($pgId, (int)$d['kriteria_id'], [
                'rencana_tindak_lanjut' => '',
                'akar_masalah'          => '',
                'koreksi_standar'       => '',
                'penanggung_jawab'      => '',
                'target_waktu'          => '',
                'status_tindakan'       => 'belum',
            ], (int)$d['id']);
        }

        $this->flash('success', 'Dokumen Pengendalian berhasil dibuat. Silakan rumuskan RTL untuk standar yang belum terpenuhi.');
        $this->redirect('/pengendalian/' . $pgId . '/edit');
    }

    // ============================================================
    // FORM EDIT PENGENDALIAN (RTL & KOREKSI STANDAR)
    // ============================================================
    public function edit(int $id): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $pengendalian = $this->model->findWithDetails($id, $user['id']);
        if (!$pengendalian) {
            $this->flash('error', 'Pengendalian tidak ditemukan.');
            $this->redirect('/pengendalian');
            return;
        }

        $this->render('pengendalian/edit', compact('user', 'pengendalian', 'flash'));
    }

    // ============================================================
    // AJAX: Auto-Save field pengendalian_detail
    // ============================================================
    public function saveDetail(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $pgId              = (int) $this->post('pengendalian_id', 0);
        $kriteriaId        = (int) $this->post('kriteria_id', 0);
        $penetapanDetailId = (int) $this->post('penetapan_detail_id', 0);
        $field             = $this->post('field', '');
        $value             = $this->post('value', '');

        $pg = $this->model->find($pgId);
        if (!$pg || $pg['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Pengendalian tidak ditemukan.']);
            return;
        }

        $ok = $this->model->saveDetailField($pgId, $kriteriaId, $field, $value, $penetapanDetailId ?: null);
        $this->model->update($pgId, ['updated_at' => date('Y-m-d H:i:s')]);
        $this->json(['success' => $ok, 'saved_at' => date('H:i:s')]);
    }

    // ============================================================
    // AJAX: Generate AI RTL untuk Single Indikator / Kriteria
    // ============================================================
    public function generateAIRtl(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $pgId              = (int) $this->post('pengendalian_id', 0);
        $kriteriaId        = (int) $this->post('kriteria_id', 0);
        $penetapanDetailId = (int) $this->post('penetapan_detail_id', 0);

        $pg = $this->model->findWithDetails($pgId, $user['id']);
        if (!$pg) {
            $this->json(['success' => false, 'message' => 'Pengendalian tidak ditemukan.']);
            return;
        }

        $targetKriteria = null;
        foreach ($pg['details'] as $d) {
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

        $prompt = $this->buildRtlPrompt($targetKriteria, $pg);

        $userModel   = new User();
        $userData    = $userModel->find($user['id']);
        $userApiKey  = $userData['gemini_api_key'] ?? '';
        $geminiModel = $userData['gemini_model'] ?? DEFAULT_GEMINI_MODEL;

        $result = $this->callGeminiAPI($prompt, $userApiKey, $geminiModel);

        if ($result['success']) {
            $parsed = $this->parseRtlResponse($result['text']);

            if (!empty($parsed['akar_masalah'])) {
                $this->model->saveDetailField($pgId, $kriteriaId, 'akar_masalah', $parsed['akar_masalah'], $penetapanDetailId ?: null);
            }
            if (!empty($parsed['rtl'])) {
                $this->model->saveDetailField($pgId, $kriteriaId, 'rencana_tindak_lanjut', $parsed['rtl'], $penetapanDetailId ?: null);
            }
            if (!empty($parsed['koreksi_standar'])) {
                $this->model->saveDetailField($pgId, $kriteriaId, 'koreksi_standar', $parsed['koreksi_standar'], $penetapanDetailId ?: null);
            }
            if (!empty($parsed['pic'])) {
                $this->model->saveDetailField($pgId, $kriteriaId, 'penanggung_jawab', $parsed['pic'], $penetapanDetailId ?: null);
            }
            if (!empty($parsed['waktu'])) {
                $this->model->saveDetailField($pgId, $kriteriaId, 'target_waktu', $parsed['waktu'], $penetapanDetailId ?: null);
            }

            $this->json([
                'success'             => true,
                'penetapan_detail_id' => $penetapanDetailId,
                'akar_masalah'        => $parsed['akar_masalah'],
                'rtl'                 => $parsed['rtl'],
                'koreksi_standar'     => $parsed['koreksi_standar'],
                'pic'                 => $parsed['pic'],
                'waktu'               => $parsed['waktu'],
                'message'             => 'Rencana Tindak Lanjut & Usulan Koreksi Standar berhasil disusun oleh AI.',
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
    // AJAX: Generate Semua AI RTL untuk Seluruh Standar Belum Terpenuhi
    // ============================================================
    public function generateAllAIRtl(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $pgId = (int) $this->post('pengendalian_id', 0);
        $pg   = $this->model->findWithDetails($pgId, $user['id']);

        if (!$pg || empty($pg['details'])) {
            $this->json(['success' => false, 'message' => 'Pengendalian tidak ditemukan.']);
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

        foreach ($pg['details'] as $d) {
            $kid  = (int)$d['kriteria_id'];
            $pdId = (int)$d['id'];
            $stCapaian = $d['status_capaian'] ?? 'belum_tercapai';

            // Lewati jika standar sudah terpenuhi
            if ($stCapaian === 'tercapai') {
                $skipped++;
                continue;
            }

            $prompt = $this->buildRtlPrompt($d, $pg);
            $res = $this->callGeminiAPI($prompt, $userApiKey, $geminiModel);

            if ($res['success']) {
                $parsed = $this->parseRtlResponse($res['text']);

                if (!empty($parsed['akar_masalah'])) {
                    $this->model->saveDetailField($pgId, $kid, 'akar_masalah', $parsed['akar_masalah'], $pdId);
                }
                if (!empty($parsed['rtl'])) {
                    $this->model->saveDetailField($pgId, $kid, 'rencana_tindak_lanjut', $parsed['rtl'], $pdId);
                }
                if (!empty($parsed['koreksi_standar'])) {
                    $this->model->saveDetailField($pgId, $kid, 'koreksi_standar', $parsed['koreksi_standar'], $pdId);
                }
                if (!empty($parsed['pic'])) {
                    $this->model->saveDetailField($pgId, $kid, 'penanggung_jawab', $parsed['pic'], $pdId);
                }
                if (!empty($parsed['waktu'])) {
                    $this->model->saveDetailField($pgId, $kid, 'target_waktu', $parsed['waktu'], $pdId);
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
                'message'    => $lastError['error'] ?? 'Gagal generate RTL AI.',
            ]);
            return;
        }

        $this->json([
            'success'   => true,
            'generated' => $results,
            'total'     => count($results),
            'skipped'   => $skipped,
            'message'   => 'Berhasil men-generate RTL untuk ' . count($results) . ' standar yang belum terpenuhi' . ($skipped > 0 ? " ({$skipped} dilewati karena sudah tercapai)." : '.'),
        ]);
    }

    /**
     * Bangun prompt AI Pengendalian & RTL
     */
    private function buildRtlPrompt(array $k, array $pg): string
    {
        $kode   = $k['kriteria_kode'] ?? $k['kode'] ?? 'KTR';
        $nama   = $k['kriteria_nama'] ?? '';
        $target = $k['target_capaian'] ?? '';
        $ind    = $k['indikator'] ?? '';
        $aturan = $k['strategi'] ?? '';
        $eval   = $k['evaluasi_teks'] ?? '';
        $status = $k['status_capaian'] ?? 'belum_tercapai';

        $stText = ($status === 'sebagian') ? 'Tercapai Sebagian (Belum Maksimal)' : 'Belum Tercapai / Tidak Terpenuhi';

        $prompt  = "Anda adalah Tim Pengendalian Mutu SPMI Perguruan Tinggi yang bertugas menyusun Rencana Tindak Lanjut (RTL) dan Usulan Koreksi Standar (Tahap P kedua dalam PPEPP).\n\n";
        $prompt .= "ATURAN PENULISAN WAJIB:\n";
        $prompt .= "1. Jangan gunakan kalimat pengantar pembuka/penutup seperti 'Berikut adalah...', 'Tentu...'.\n";
        $prompt .= "2. DILARANG menggunakan karakter simbol aneh (*, panah ->, emoji). Gunakan format teks baku.\n";
        $prompt .= "3. Tulis jawaban Anda dalam format persis seperti di bawah agar dapat diparsing sistem:\n\n";
        $prompt .= "---AKAR MASALAH---\n[Tulis analisis akar penyebab ketidaktercapaian secara mendalam dan realistis]\n\n";
        $prompt .= "---RENCANA TINDAK LANJUT---\n[Tulis langkah-langkah tindakan koreksi konkret dan terukur (1, 2, 3)]\n\n";
        $prompt .= "---USULAN KOREKSI STANDAR---\n[Tulis rekomendasi penyesuaian atau peningkatan standar untuk siklus Penetapan berikutnya]\n\n";
        $prompt .= "---PIC---\n[Tulis penanggung jawab yang tepat, misal: Ketua Program Studi, Koordinator Lab, Tim Kurikulum]\n\n";
        $prompt .= "---TARGET WAKTU---\n[Tulis target durasi/waktu penyelesaian, misal: 3 Bulan / Semester Depan]\n\n";
        $prompt .= "DATA STANDAR & EVALUASI:\n";
        $prompt .= "- Kriteria: [{$kode}] {$nama}\n";
        if ($aturan) $prompt .= "- Dasar Aturan: {$aturan}\n";
        $prompt .= "- Target Penetapan: {$target}\n";
        $prompt .= "- Indikator Ketercapaian: {$ind}\n";
        $prompt .= "- Status Evaluasi: {$stText}\n";
        if ($eval)   $prompt .= "- Hasil Analisis Evaluasi: {$eval}\n";

        return $prompt;
    }

    /**
     * Parsing respons teks AI menjadi structured data
     */
    private function parseRtlResponse(string $rawText): array
    {
        $data = [
            'akar_masalah'    => '',
            'rtl'             => '',
            'koreksi_standar' => '',
            'pic'             => 'Ketua Program Studi / Tim Mutu',
            'waktu'           => '1 Semester',
        ];

        if (preg_match('/---AKAR MASALAH---\s*(.*?)(?=---RENCANA TINDAK LANJUT---|$)/si', $rawText, $m)) {
            $data['akar_masalah'] = trim($m[1]);
        }
        if (preg_match('/---RENCANA TINDAK LANJUT---\s*(.*?)(?=---USULAN KOREKSI STANDAR---|$)/si', $rawText, $m)) {
            $data['rtl'] = trim($m[1]);
        }
        if (preg_match('/---USULAN KOREKSI STANDAR---\s*(.*?)(?=---PIC---|$)/si', $rawText, $m)) {
            $data['koreksi_standar'] = trim($m[1]);
        }
        if (preg_match('/---PIC---\s*(.*?)(?=---TARGET WAKTU---|$)/si', $rawText, $m)) {
            $data['pic'] = trim($m[1]);
        }
        if (preg_match('/---TARGET WAKTU---\s*(.*?)$/si', $rawText, $m)) {
            $data['waktu'] = trim($m[1]);
        }

        // Fallback jika formatting tidak match
        if (empty($data['rtl'])) {
            $data['rtl'] = trim($rawText);
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
    // AJAX: Upload Berkas Koreksi Standar untuk Penetapan Berikutnya
    // ============================================================
    public function uploadBerkasKoreksi(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $pgId  = (int) $this->post('pengendalian_id', 0);
        $judul = trim($this->post('judul', ''));
        $ket   = trim($this->post('keterangan', ''));

        $pg = $this->model->find($pgId);
        if (!$pg || $pg['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Pengendalian tidak ditemukan.']);
            return;
        }

        if (!isset($_FILES['berkas_file']) || $_FILES['berkas_file']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'File tidak valid.']);
            return;
        }

        $file     = $_FILES['berkas_file'];
        $origName = $file['name'];
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed  = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            $this->json(['success' => false, 'message' => 'Format file tidak diizinkan. Gunakan PDF, Word, Excel, atau Gambar.']);
            return;
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/pengendalian/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $safeName = 'koreksi_' . $pgId . '_' . time() . '_' . uniqid() . '.' . $ext;
        $destPath = $uploadDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->json(['success' => false, 'message' => 'Gagal menyimpan file berkas koreksi.']);
            return;
        }

        $relPath  = 'uploads/pengendalian/' . $safeName;
        $currList = [];
        if (!empty($pg['berkas_koreksi'])) {
            $parsed = json_decode($pg['berkas_koreksi'], true);
            if (is_array($parsed)) $currList = $parsed;
        }

        $newItem = [
            'id'         => uniqid(),
            'file_path'  => $relPath,
            'file_name'  => $origName,
            'judul'      => $judul ?: $origName,
            'keterangan' => $ket,
            'url'        => BASE_URL . '/' . $relPath,
            'time'       => date('d M Y H:i'),
        ];
        $currList[] = $newItem;

        $db = $this->model->getDb();
        $stmt = $db->prepare("UPDATE pengendalian SET berkas_koreksi = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([json_encode($currList), $pgId]);

        $this->json([
            'success' => true,
            'berkas'  => $newItem,
            'total'   => count($currList),
            'message' => 'Berkas koreksi standar berhasil diupload untuk acuan penetapan berikutnya.',
        ]);
    }

    // ============================================================
    // AJAX: Hapus Berkas Koreksi Standar
    // ============================================================
    public function deleteBerkasKoreksi(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $pgId     = (int) $this->post('pengendalian_id', 0);
        $berkasId = $this->post('berkas_id', '');

        $pg = $this->model->find($pgId);
        if (!$pg || $pg['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Pengendalian tidak ditemukan.']);
            return;
        }

        $currList = [];
        if (!empty($pg['berkas_koreksi'])) {
            $parsed = json_decode($pg['berkas_koreksi'], true);
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
        $stmt = $db->prepare("UPDATE pengendalian SET berkas_koreksi = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([json_encode($updatedList), $pgId]);

        $this->json([
            'success' => true,
            'total'   => count($updatedList),
            'message' => 'Berkas koreksi standar berhasil dihapus.',
        ]);
    }

    // ============================================================
    // DETAIL / SHOW PENGENDALIAN
    // ============================================================
    public function show(int $id): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $pengendalian = $this->model->findWithDetails($id, $user['id']);
        if (!$pengendalian) {
            $this->flash('error', 'Pengendalian tidak ditemukan.');
            $this->redirect('/pengendalian');
            return;
        }

        $this->render('pengendalian/show', compact('user', 'pengendalian', 'flash'));
    }

    // ============================================================
    // FINALISASI PENGENDALIAN
    // ============================================================
    public function finalize(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $pg = $this->model->find($id);
        if (!$pg || $pg['user_id'] != $user['id']) {
            $this->flash('error', 'Pengendalian tidak ditemukan.');
            $this->redirect('/pengendalian');
            return;
        }

        $this->model->update($id, [
            'status'     => 'final',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->flash('success', 'Dokumen Pengendalian berhasil difinalisasi!');
        $this->redirect('/pengendalian/' . $id);
    }

    // ============================================================
    // PENGENDALIAN - TOGGLE STATUS / AKTIFKAN / NONAKTIFKAN / HIDE
    // ============================================================
    public function toggleStatus(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $pg = $this->model->find($id);
        if (!$pg || $pg['user_id'] != $user['id']) {
            $this->flash('error', 'Pengendalian tidak ditemukan.');
            $target = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/pengendalian';
            $this->redirect($target);
            return;
        }

        $targetVis = trim($_POST['visibility_status'] ?? $_POST['status'] ?? '');
        $allowed   = ['aktif', 'nonaktif', 'hidden'];

        if (!in_array($targetVis, $allowed)) {
            if (isset($_POST['is_active'])) {
                $targetVis = ((int)$_POST['is_active'] === 1) ? 'aktif' : 'nonaktif';
            } else {
                $currentVis = $pg['visibility_status'] ?? 'aktif';
                $targetVis  = ($currentVis === 'aktif') ? 'nonaktif' : 'aktif';
            }
        }

        $isActiveVal = ($targetVis === 'aktif') ? 1 : 0;

        $db = $this->model->getDb();
        $db->prepare("UPDATE pengendalian SET visibility_status = ? WHERE id = ? AND user_id = ?")
           ->execute([$targetVis, $id, $user['id']]);

        $msgMap = [
            'aktif'    => 'Dokumen Pengendalian berhasil diaktifkan kembali dan tampil di daftar utama.',
            'nonaktif' => 'Dokumen Pengendalian berhasil dinonaktifkan (tetap terlihat di daftar dokumen).',
            'hidden'   => 'Dokumen Pengendalian berhasil disembunyikan (Hide). Anda dapat melihat dan mengaktifkannya kembali di menu Dokumen Tersembunyi.',
        ];

        $this->flash('success', $msgMap[$targetVis] ?? 'Status visibilitas dokumen berhasil diperbarui.');
        $target = !empty($_POST['return_url']) ? $_POST['return_url'] : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/pengendalian/' . $id);
        $this->redirect($target);
    }

    // ============================================================
    // HAPUS / NONAKTIFKAN PENGENDALIAN (Soft-Hide)
    // ============================================================
    public function delete(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $pg = $this->model->find($id);
        if (!$pg || $pg['user_id'] != $user['id']) {
            $this->flash('error', 'Pengendalian tidak ditemukan.');
            $this->redirect('/pengendalian');
            return;
        }

        // Soft-hide: sembunyikan dokumen tanpa menghapus datanya
        $db = $this->model->getDb();
        $db->prepare("UPDATE pengendalian SET visibility_status = 'hidden' WHERE id = ? AND user_id = ?")->execute([$id, $user['id']]);

        $this->flash('success', 'Dokumen Pengendalian berhasil disembunyikan (Hide). Anda dapat melihat dan mengaktifkannya kembali di menu Dokumen Tersembunyi.');
        $target = !empty($_POST['return_url']) ? $_POST['return_url'] : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/pengendalian');
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
