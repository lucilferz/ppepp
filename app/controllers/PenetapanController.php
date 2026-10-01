<?php
/**
 * Controller: Penetapan
 * Platform PPEPP Fakultas
 */

class PenetapanController extends Controller
{
    private Penetapan $penetapanModel;
    private Kriteria  $kriteriaModel;
    private Referensi $referensiModel;

    public function __construct()
    {
        parent::__construct();
        $this->penetapanModel = new Penetapan();
        $this->kriteriaModel  = new Kriteria();
        $this->referensiModel = new Referensi();
    }

    // ============================================================
    // PENETAPAN - LIST
    // ============================================================
    public function index(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $penetapans  = $this->penetapanModel->findByUser($user['id']);
        $tahunAjaran = $this->penetapanModel->getTahunAjaran();
        $stats       = $this->penetapanModel->getStats($user['id']);
        $flash       = $this->getFlash();

        // Add implementation progress for each penetapan
        $pelaksanaanModel = new Pelaksanaan();
        $db = $this->penetapanModel->getDb();
        foreach ($penetapans as &$p) {
            // Get latest pelaksanaan for this penetapan and user
            $stmt = $db->prepare("SELECT id FROM pelaksanaan WHERE penetapan_id = ? AND user_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$p['id'], $user['id']]);
            $latestPelaksanaan = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($latestPelaksanaan) {
                $checklistStats = $pelaksanaanModel->getChecklistStats($latestPelaksanaan['id'], $p['id']);
                $p['pelaksanaan_progress'] = $checklistStats['pct'];
            } else {
                $p['pelaksanaan_progress'] = 0;
            }
        }

        $this->render('penetapan/index', compact('user', 'penetapans', 'tahunAjaran', 'stats', 'flash'));
    }

    // ============================================================
    // EXCEL: Download Template Standar Penetapan
    // ============================================================
    public function downloadTemplateExcel(): void
    {
        $this->requireAuth();
        $format = strtolower($this->get('format', 'xlsx'));
        ExcelHelper::downloadTemplate($format);
    }

    // ============================================================
    // EXCEL: Upload & Parse File Excel / CSV (AJAX)
    // ============================================================
    public function uploadExcel(): void
    {
        $this->requireAuth();
        $file = $_FILES['file'] ?? $_FILES['excel_file'] ?? (isset($_FILES) && count($_FILES) > 0 ? reset($_FILES) : null);

        if (!$file || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            $errCode = $file['error'] ?? 'tidak ada file';
            $this->json(['success' => false, 'message' => 'File tidak valid atau gagal diupload (Kode: ' . $errCode . ').']);
            return;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ['xlsx', 'xls', 'csv', 'txt'])) {
            $this->json(['success' => false, 'message' => 'Format file tidak didukung (' . $ext . '). Gunakan .xlsx, .xls, atau .csv.']);
            return;
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            $this->json(['success' => false, 'message' => 'Ukuran file melebihi batas maksimal (10 MB).']);
            return;
        }

        $parsed = ExcelHelper::parseUploadedFile($file['tmp_name'], $ext);

        $rows = $parsed['rows'] ?? (is_array($parsed) ? $parsed : []);
        $sheets = $parsed['sheets'] ?? [];

        if (empty($rows) && empty($sheets)) {
            $this->json(['success' => false, 'message' => 'Tidak dapat membaca data dari file Excel/CSV. Pastikan kolom sesuai template (No, Aturan, Pernyataan Standar, Indikator).']);
            return;
        }

        $this->json([
            'success'       => true,
            'is_multisheet' => !empty($parsed['is_multisheet']),
            'sheets'        => $sheets,
            'total'         => count($rows),
            'rows'          => $rows,
            'message'       => 'Berhasil membaca ' . count($rows) . ' baris standar' . (!empty($sheets) ? ' dari ' . count($sheets) . ' sheet' : '') . '.',
        ]);
    }

    // ============================================================
    // EXCEL: Terapkan Data Excel yang Dipecah ke Penetapan Detail
    // ============================================================
    public function applyExcelToPenetapan(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        header('Content-Type: application/json');

        $penetapanId = (int) $this->post('penetapan_id', 0);
        $itemsJson   = $this->post('items', '[]');
        $items       = json_decode($itemsJson, true);

        if (!$penetapanId || empty($items) || !is_array($items)) {
            $this->json(['success' => false, 'message' => 'Data import tidak valid.']);
            return;
        }

        $penetapan = $this->penetapanModel->find($penetapanId);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Penetapan tidak ditemukan.']);
            return;
        }

        $db = $this->penetapanModel->getDb();

        // Cek apakah ada detail kosong sebelumnya (placeholder dari create step 1)
        $stmtCheck = $db->prepare(
            "SELECT id, target_capaian, indikator, strategi FROM penetapan_detail WHERE penetapan_id = ?"
        );
        $stmtCheck->execute([$penetapanId]);
        $currentDetails = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);

        $allEmpty = true;
        foreach ($currentDetails as $cd) {
            if (!empty(trim($cd['target_capaian'] ?? '')) || !empty(trim($cd['indikator'] ?? '')) || !empty(trim($cd['strategi'] ?? ''))) {
                $allEmpty = false;
                break;
            }
        }

        if ($allEmpty && !empty($currentDetails)) {
            // Hapus placeholder kosong dari step 1 agar data Excel masuk bersih
            $db->prepare("DELETE FROM penetapan_detail WHERE penetapan_id = ?")->execute([$penetapanId]);
        }

        $applied = 0;
        $kriteriaIds = [];

        foreach ($items as $idx => $item) {
            $kriteriaId = (int)($item['kriteria_id'] ?? 0);
            if (!$kriteriaId) continue;

            $kode       = strtoupper(trim($item['kode'] ?? ''));
            $target     = trim($item['pernyataan_standar'] ?? '');
            $indikator  = trim($item['indikator'] ?? '');
            $aturan     = trim($item['aturan'] ?? '');

            if (empty($kode)) {
                $kode = 'STD-' . str_pad($applied + 1, 2, '0', STR_PAD_LEFT);
            }

            // Simpan sebagai standard item tersendiri
            $this->penetapanModel->saveDetail($penetapanId, $kriteriaId, [
                'kode'           => $kode,
                'target_capaian' => $target,
                'indikator'      => $indikator,
                'strategi'       => $aturan,
                'sumber_daya'    => '',
                'ai_analisis'    => '',
            ]);
            $kriteriaIds[] = $kriteriaId;
            $applied++;
        }

        // Pastikan kriteria_ids di penetapan mencakup semua kriteria dari item yang diimport
        if (!empty($kriteriaIds)) {
            $existingKriteriaIds = !empty($penetapan['kriteria_ids']) ? json_decode($penetapan['kriteria_ids'], true) : [];
            $mergedKriteriaIds   = array_values(array_unique(array_merge(is_array($existingKriteriaIds) ? $existingKriteriaIds : [], $kriteriaIds)));
            $db->prepare("UPDATE penetapan SET kriteria_ids = ?, updated_at = NOW() WHERE id = ?")
               ->execute([json_encode($mergedKriteriaIds), $penetapanId]);
        }

        $this->json([
            'success' => true,
            'applied' => $applied,
            'message' => 'Berhasil menerapkan ' . $applied . ' standar ke dokumen penetapan.',
        ]);
    }

    // ============================================================
    // PENETAPAN - STEP 1: Pilih Kriteria
    // ============================================================
    public function create(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $kriteria    = $this->kriteriaModel->findByUser($user['id']);
        $tahunAjaran = $this->penetapanModel->getTahunAjaran();
        $flash       = $this->getFlash();

        if (empty($kriteria)) {
            $this->flash('warning', 'Silakan tambahkan kriteria prodi terlebih dahulu sebelum membuat penetapan.');
            $this->redirect('/penetapan/kriteria');
            return;
        }

        // Ambil project_id dari URL jika ada
        $projectId = (int) $this->get('project_id', $this->get('ppepp_project_id', 0));

        // Bersihkan session step lama
        unset($_SESSION['penetapan_step']);

        // Load project PPEPP milik user/prodi
        $projectModel = new PpeppProject();
        $userProjects = $projectModel->findByUser($user['id']);
        $selectedProject = null;

        if ($projectId > 0) {
            foreach ($userProjects as $p) {
                if ($p['id'] == $projectId) {
                    $selectedProject = $p;
                    break;
                }
            }
        } elseif (count($userProjects) === 1) {
            $selectedProject = $userProjects[0];
            $projectId = (int)$selectedProject['id'];
        }

        $this->render('penetapan/create_step1', compact('user', 'kriteria', 'tahunAjaran', 'flash', 'projectId', 'userProjects', 'selectedProject'));
    }

    // ============================================================
    // PENETAPAN - STEP 2: Buat Draft & Tampilkan Form
    // ============================================================
    public function step2(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $judul          = trim($this->post('judul', ''));
        $deskripsi      = trim($this->post('deskripsi', ''));
        $tahunAjaranId  = (int) $this->post('tahun_ajaran_id', 0);
        $ppeppProjectId = (int) $this->post('ppepp_project_id', 0);
        $kriteriaIds    = array_map('intval', $_POST['kriteria_ids'] ?? []);
        $kriteriaKodes  = $_POST['kriteria_kodes'] ?? [];

        // Auto-resolve tahun_ajaran_id dari ppepp_project jika ppepp_project_id dikirim
        if ($ppeppProjectId > 0 && $tahunAjaranId <= 0) {
            $db = $this->penetapanModel->getDb();
            $stmt = $db->prepare("SELECT tahun_ajaran_id FROM ppepp_project WHERE id = ? LIMIT 1");
            $stmt->execute([$ppeppProjectId]);
            $tahunAjaranId = (int) $stmt->fetchColumn();
        }

        // Auto-resolve ppepp_project_id dari tahun_ajaran_id jika tidak dikirim
        if ($ppeppProjectId <= 0 && $tahunAjaranId > 0) {
            $db = $this->penetapanModel->getDb();
            $stmt = $db->prepare("SELECT id FROM ppepp_project WHERE user_id = ? AND tahun_ajaran_id = ? LIMIT 1");
            $stmt->execute([$user['id'], $tahunAjaranId]);
            $ppeppProjectId = (int) $stmt->fetchColumn();
        }

        if (empty($judul) || !$tahunAjaranId) {
            $this->flash('error', 'Judul dan Folder Project PPEPP wajib diisi.');
            $this->redirect('/penetapan/create');
            return;
        }

        // Jika kriteria_ids kosong (user memilih opsi Langsung Import Excel), gunakan seluruh kriteria prodi yang tersedia
        if (empty($kriteriaIds)) {
            $allProdiKriteria = $this->kriteriaModel->findByUser($user['id']);
            $kriteriaIds = array_map('intval', array_column($allProdiKriteria, 'id'));
            foreach ($allProdiKriteria as $ak) {
                $kriteriaKodes[$ak['id']] = $ak['kode'];
            }
        }

        // Cari ppepp_project_id otomatis jika tidak dikirim dari form
        if ($ppeppProjectId <= 0 && $tahunAjaranId > 0) {
            $db = $this->penetapanModel->getDb();
            $stmt = $db->prepare("SELECT id FROM ppepp_project WHERE user_id = ? AND tahun_ajaran_id = ? LIMIT 1");
            $stmt->execute([$user['id'], $tahunAjaranId]);
            $ppeppProjectId = (int) $stmt->fetchColumn();
        }

        // *** Langsung simpan sebagai draft di database ***
        $penetapanId = $this->penetapanModel->insert([
            'user_id'          => $user['id'],
            'ppepp_project_id' => $ppeppProjectId > 0 ? $ppeppProjectId : null,
            'tahun_ajaran_id'  => $tahunAjaranId,
            'judul'            => $judul,
            'deskripsi'        => $deskripsi,
            'status'           => 'draft',
            'step_current'     => 2,
            'kriteria_ids'     => json_encode($kriteriaIds),
        ]);

        // Buat detail standar awal untuk setiap kriteria yang dipilih pada Langkah 1
        if (!empty($kriteriaIds)) {
            $counter = 1;
            foreach ($kriteriaIds as $kId) {
                $kodeStr = 'STD-' . str_pad($counter++, 2, '0', STR_PAD_LEFT);
                $this->penetapanModel->saveDetail($penetapanId, (int)$kId, [
                    'kode'           => $kodeStr,
                    'target_capaian' => '',
                    'indikator'      => '',
                    'strategi'       => '',
                    'sumber_daya'    => '',
                    'ai_analisis'    => '',
                ]);
            }
        }

        $this->flash('success', 'Draft penetapan berhasil dibuat! Silakan lengkapi setiap kriteria.');
        $this->redirect('/penetapan/' . $penetapanId . '/edit-step2');
    }

    // ============================================================
    // PENETAPAN - Edit Step 2 (lanjut draft atau buka kembali)
    // ============================================================
    public function editStep2(string $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $penetapan = $this->penetapanModel->findWithDetails((int)$id);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->flash('error', 'Penetapan tidak ditemukan.');
            $this->redirect('/penetapan');
            return;
        }

        // Ambil kriteria terpilih dari DB (berdasarkan penetapan_detail atau kriteria_ids)
        $selectedKriteria = [];
        $loadedKIds = [];
        foreach ($penetapan['details'] as $d) {
            $k = $this->kriteriaModel->find($d['kriteria_id']);
            if ($k && !in_array((int)$k['id'], $loadedKIds)) {
                $k['detail'] = $d; // sertakan data yang sudah diisi
                $selectedKriteria[] = $k;
                $loadedKIds[] = (int)$k['id'];
            }
        }

        // Jikalau ada kriteria_ids tersimpan yang belum masuk detail
        $savedKIds = json_decode($penetapan['kriteria_ids'] ?? '[]', true) ?: [];
        foreach ($savedKIds as $skid) {
            $skid = (int)$skid;
            if ($skid > 0 && !in_array($skid, $loadedKIds)) {
                $k = $this->kriteriaModel->find($skid);
                if ($k) {
                    $selectedKriteria[] = $k;
                    $loadedKIds[] = $skid;
                }
            }
        }

        // Ambil referensi yang sudah diupload per kriteria
        $db = (new \Model())->getDb();
        $stmtR = $db->prepare("SELECT * FROM referensi WHERE penetapan_id = ? AND kriteria_id = ? ORDER BY created_at DESC LIMIT 1");
        foreach ($selectedKriteria as &$k) {
            $stmtR->execute([$penetapan['id'], $k['id']]);
            $k['referensi'] = $stmtR->fetch(\PDO::FETCH_ASSOC) ?: null;
        }
        unset($k);

        // Ambil seluruh kriteria prodi user untuk auto-mapping Excel
        $allKriteria = $this->kriteriaModel->findByUser($user['id']);

        $flash = $this->getFlash();
        $this->render('penetapan/create_step2', compact(
            'user', 'selectedKriteria', 'allKriteria', 'penetapan', 'flash'
        ));
    }

    // ============================================================
    // PENETAPAN - AJAX: Auto-Save Draft Detail per Kriteria
    // ============================================================
    public function saveDraftDetail(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $penetapanId = (int) $this->post('penetapan_id', 0);
        $detailId    = (int) $this->post('detail_id', 0);
        $kriteriaId  = (int) $this->post('kriteria_id', 0);
        $field       = $this->post('field', '');
        $value       = $this->post('value', '');

        $allowed = ['kode', 'kriteria_id', 'target_capaian', 'indikator', 'strategi', 'sumber_daya', 'ai_analisis'];
        if (!in_array($field, $allowed)) {
            $this->json(['success' => false, 'message' => 'Field tidak valid.']);
            return;
        }

        $penetapan = $this->penetapanModel->find($penetapanId);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Penetapan tidak ditemukan.']);
            return;
        }

        // Update field spesifik di penetapan_detail
        $db = $this->penetapanModel->getDb();
        if ($detailId > 0) {
            $stmt = $db->prepare(
                "UPDATE penetapan_detail SET {$field} = ?, updated_at = NOW()
                 WHERE id = ? AND penetapan_id = ?"
            );
            $ok = $stmt->execute([$value, $detailId, $penetapanId]);
        } else {
            $stmt = $db->prepare(
                "UPDATE penetapan_detail SET {$field} = ?, updated_at = NOW()
                 WHERE penetapan_id = ? AND kriteria_id = ?"
            );
            $ok = $stmt->execute([$value, $penetapanId, $kriteriaId]);
        }

        // Update timestamp penetapan
        $this->penetapanModel->update($penetapanId, ['updated_at' => date('Y-m-d H:i:s')]);

        $this->json(['success' => $ok, 'saved_at' => date('H:i:s')]);
    }

    // ============================================================
    // PENETAPAN - AJAX: Tambah Baris Standar Baru
    // ============================================================
    public function addStandardItem(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        header('Content-Type: application/json');

        $penetapanId = (int) $this->post('penetapan_id', 0);
        $kriteriaId  = (int) $this->post('kriteria_id', 0);

        $penetapan = $this->penetapanModel->find($penetapanId);
        if (!$penetapan || $penetapan['user_id'] != $user['id'] || !$kriteriaId) {
            $this->json(['success' => false, 'message' => 'Data tidak valid.']);
            return;
        }

        $kriteria = $this->kriteriaModel->find($kriteriaId);
        if (!$kriteria) {
            $this->json(['success' => false, 'message' => 'Kriteria tidak ditemukan.']);
            return;
        }

        $db = $this->penetapanModel->getDb();
        $countExisting = (int)$db->query("SELECT COUNT(*) FROM penetapan_detail WHERE penetapan_id = " . (int)$penetapanId)->fetchColumn();
        $newKode = 'STD-' . str_pad($countExisting + 1, 2, '0', STR_PAD_LEFT);

        $newDetailId = $this->penetapanModel->saveDetail($penetapanId, $kriteriaId, [
            'kode'           => $newKode,
            'target_capaian' => '',
            'indikator'      => '',
            'strategi'       => '',
            'sumber_daya'    => '',
            'ai_analisis'    => '',
        ]);

        $this->json([
            'success'   => true,
            'detail_id' => $newDetailId,
            'kode'      => $newKode,
            'kriteria'  => $kriteria,
            'message'   => 'Standar baru berhasil ditambahkan.',
        ]);
    }

    // ============================================================
    // PENETAPAN - AJAX: Hapus Baris Standar
    // ============================================================
    public function deleteStandardItem(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        header('Content-Type: application/json');

        $penetapanId = (int) $this->post('penetapan_id', 0);
        $detailId    = (int) $this->post('detail_id', 0);

        $penetapan = $this->penetapanModel->find($penetapanId);
        if (!$penetapan || $penetapan['user_id'] != $user['id'] || !$detailId) {
            $this->json(['success' => false, 'message' => 'Data tidak valid.']);
            return;
        }

        $db = $this->penetapanModel->getDb();
        $stmt = $db->prepare("DELETE FROM penetapan_detail WHERE id = ? AND penetapan_id = ?");
        $stmt->execute([$detailId, $penetapanId]);

        $this->json(['success' => true, 'message' => 'Standar berhasil dihapus.']);
    }

    // ============================================================
    // PENETAPAN - AJAX: Upload PDF per Kriteria
    // ============================================================
    public function uploadPerKriteria(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'File tidak valid atau gagal diupload.']);
            return;
        }

        $kriteriaId = (int) $this->post('kriteria_id', 0);
        $file       = $_FILES['file'];

        if (!$kriteriaId) {
            $this->json(['success' => false, 'message' => 'Kriteria tidak valid.']);
            return;
        }

        // Validasi ukuran
        if ($file['size'] > MAX_FILE_SIZE) {
            $this->json(['success' => false, 'message' => 'File terlalu besar (maks 10 MB).']);
            return;
        }

        // Validasi ekstensi
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_TYPES)) {
            $this->json(['success' => false, 'message' => 'Tipe file tidak diizinkan (' . $ext . '). Gunakan: ' . implode(', ', ALLOWED_TYPES)]);
            return;
        }

        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

        $newName  = 'pen_' . $user['id'] . '_k' . $kriteriaId . '_' . time() . '.' . $ext;
        $destPath = UPLOAD_DIR . $newName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->json(['success' => false, 'message' => 'Gagal menyimpan file ke server.']);
            return;
        }

        // Ekstrak teks
        $kontenTeks = Referensi::extractText($destPath, $ext);

        // Simpan ke DB sebagai referensi sementara (dengan session key)
        $sessionKey = session_id();
        $kriteria   = $this->kriteriaModel->find($kriteriaId);

        $refId = $this->referensiModel->insert([
            'user_id'          => $user['id'],
            'kriteria_id'      => $kriteriaId,
            'nama_file'        => $newName,
            'nama_asli'        => $file['name'],
            'tipe_file'        => $ext,
            'ukuran'           => $file['size'],
            'konten_teks'      => $kontenTeks,
            'step_session_key' => $sessionKey,
            'deskripsi'        => 'Upload Step Penetapan - ' . ($kriteria['kode'] ?? ''),
        ]);

        // Simpan info ke session
        if (!isset($_SESSION['penetapan_uploads'])) $_SESSION['penetapan_uploads'] = [];
        $_SESSION['penetapan_uploads'][$kriteriaId] = [
            'referensi_id'    => $refId,
            'nama_asli'       => $file['name'],
            'ukuran'          => $file['size'],
            'ext'             => $ext,
            'punya_teks'      => !empty($kontenTeks),
        ];

        $this->json([
            'success'      => true,
            'referensi_id' => $refId,
            'nama_asli'    => $file['name'],
            'punya_teks'   => !empty($kontenTeks),
            'ukuran_kb'    => round($file['size'] / 1024, 1),
        ]);
    }

    // ============================================================
    // PENETAPAN - AJAX: Analisis AI per Kriteria (SPMI vs Turunan)
    // ============================================================
    public function analyzePerKriteria(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        header('Content-Type: application/json');

        $spmiId      = (int) $this->post('spmi_id', 0);
        $referensiId = (int) $this->post('referensi_id', 0);
        $kriteriaId  = (int) $this->post('kriteria_id', 0);
        $onlySpmi    = (bool) $this->post('only_spmi', false); // hanya analisis SPMI tanpa turunan

        $kriteria = $this->kriteriaModel->find($kriteriaId);
        if (!$kriteria || $kriteria['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Kriteria tidak ditemukan.']);
            return;
        }

        // Ambil referensi jika ada
        $turunanTeks = '';
        $turunanNama = '';
        if ($referensiId > 0) {
            $referensi = $this->referensiModel->find($referensiId);
            if ($referensi && $referensi['user_id'] == $user['id']) {
                if (!empty($referensi['konten_teks'])) {
                    $turunanTeks = mb_substr($referensi['konten_teks'], 0, 18000);
                    $turunanNama = $referensi['nama_asli'];
                }
            }
        }

        // Pastikan ada sumber dokumen
        if (empty($turunanTeks)) {
            $this->json(['success' => false, 'message' => 'Tidak ada dokumen untuk dianalisis. Silakan upload dokumen referensi.']);
            return;
        }

        // Bangun prompt sesuai kasus
        $userModel  = new User();
        $userData   = $userModel->find($user['id']);
        $userApiKey = $userData['gemini_api_key'] ?? '';
        $geminiModel = $userData['gemini_model'] ?? DEFAULT_GEMINI_MODEL;

        $prompt = $this->buildDualDocPrompt($kriteria, '', '', $turunanTeks, $turunanNama);

        $result = $this->callGeminiAPI($prompt, $userApiKey, $geminiModel);

        if ($result['success']) {
            if ($referensiId > 0) {
                $this->referensiModel->update($referensiId, ['ai_perbandingan' => $result['text']]);
            }
            $this->json(['success' => true, 'analisis' => $result['text']]);
        } else {
            $this->json(['success' => false, 'message' => $result['error']]);
        }
    }

    /**
     * Bangun prompt dual dokumen SPMI vs Turunan
     */
    private function buildDualDocPrompt(array $kriteria, string $spmiTeks, string $spmiJudul, string $turunanTeks, string $turunanNama): string
    {
        $kode = $kriteria['kode'];
        $nama = $kriteria['nama'];
        $desc = $kriteria['deskripsi'] ?? '';

        $hasBoth  = !empty($spmiTeks) && !empty($turunanTeks);
        $hasSpmi  = !empty($spmiTeks);
        $hasTurun = !empty($turunanTeks);

        $prompt  = "Anda adalah Tim Penjaminan Mutu SPMI Perguruan Tinggi yang menyusun Laporan Penetapan Standar.\n\n";
        $prompt .= "ATURAN PENULISAN WAJIB:\n";
        $prompt .= "1. Jangan gunakan kalimat pengantar AI sama sekali ('Berikut adalah...', 'Tentu...', 'Baik...', dll.). Langsung mulai laporan.\n";
        $prompt .= "2. DILARANG menggunakan simbol-simbol tidak baku: bintang (*), tanda panah (->), centang (checkmark unicode), atau simbol dekorasi lainnya. Gunakan hanya tanda baca standar (, . : ; - () []).\n";
        $prompt .= "3. JIKA mengutip teks dari dokumen SPMI atau Turunan, KUTIP PERSIS KATA PER KATA sesuai dokumen aslinya. Jangan ubah, jangan parafrase, jangan ringkas kutipan. Tandai kutipan dengan tanda kutip ganda.\n";
        $prompt .= "4. Tulis secara formal, akademik, dan siap dijadikan laporan PDF Laporan Evaluasi Diri (LED).\n";
        $prompt .= "5. DILARANG menggunakan garis tabel markdown mentah (| :--- | :--- |). Jika perlu tabel, gunakan format naratif atau daftar berpoin.\n\n";
        $prompt .= "KRITERIA TARGET: [{$kode}] {$nama}\n";
        if ($desc) $prompt .= "Fokus: {$desc}\n";
        $prompt .= "\n";

        if ($hasSpmi) {
            $prompt .= "===== STANDAR UTAMA: SPMI" . ($spmiJudul ? " ({$spmiJudul})" : "") . " =====\n";
            $prompt .= $spmiTeks . "\n\n";
        }

        if ($hasTurun) {
            $prompt .= "===== DOKUMEN TURUNAN: {$turunanNama} =====\n";
            $prompt .= $turunanTeks . "\n\n";
        }

        $prompt .= "===== FORMAT LAPORAN YANG HARUS DIIKUTI =====\n\n";

        if ($hasBoth) {
            // Kasus lengkap: SPMI + Turunan
            $prompt .= "### 1. Temuan Standar SPMI untuk {$kode}\n";
            $prompt .= "(Kutip verbatim poin/pasal SPMI yang relevan. Sebutkan nomor pasal/standar jika ada.)\n\n";
            $prompt .= "### 2. Kondisi dalam Dokumen Turunan\n";
            $prompt .= "(Kutip verbatim bagian dokumen turunan yang berkaitan dengan kriteria ini.)\n\n";
            $prompt .= "### 3. Analisis Kesesuaian\n";
            $prompt .= "(Uraikan apakah dokumen turunan sudah memenuhi/sesuai dengan standar SPMI untuk kriteria {$kode}. Jelaskan celah/gap jika ada, tanpa simbol aneh.)\n\n";
            $prompt .= "### 4. Rekomendasi Penetapan Standar\n";
            $prompt .= "- Target Capaian: (Target terukur berdasarkan standar SPMI)\n";
            $prompt .= "- Indikator Keberhasilan: (Kriteria ketercapaian yang dapat diukur)\n";
            $prompt .= "- Strategi Pemenuhan: (Langkah-langkah untuk memenuhi standar)\n";
            $prompt .= "- Sumber Daya dan Penanggung Jawab: (Pelaksana dan sumber daya yang diperlukan)\n";
        } elseif ($hasSpmi) {
            // Hanya SPMI
            $prompt .= "### 1. Temuan Standar SPMI untuk {$kode}\n";
            $prompt .= "(Kutip verbatim poin/pasal SPMI yang relevan dengan kriteria ini.)\n\n";
            $prompt .= "### 2. Rekomendasi Penetapan Standar\n";
            $prompt .= "- Target Capaian: (Target terukur berdasarkan standar SPMI)\n";
            $prompt .= "- Indikator Keberhasilan:\n";
            $prompt .= "- Strategi Pemenuhan:\n";
            $prompt .= "- Sumber Daya dan Penanggung Jawab:\n";
        } else {
            // Hanya Turunan
            $prompt .= "### 1. Temuan dalam Dokumen\n";
            $prompt .= "(Kutip verbatim bagian dokumen yang relevan dengan kriteria {$kode}.)\n\n";
            $prompt .= "### 2. Rekomendasi Penetapan Standar\n";
            $prompt .= "- Target Capaian:\n";
            $prompt .= "- Indikator Keberhasilan:\n";
            $prompt .= "- Strategi Pemenuhan:\n";
            $prompt .= "- Sumber Daya dan Penanggung Jawab:\n";
        }

        return $prompt;
    }

    // ============================================================
    // PENETAPAN - STORE: Update draft jadi final atau simpan
    // Dipanggil dari tombol di edit-step2 view
    // ============================================================
    public function store(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $penetapanId = (int) $this->post('penetapan_id', 0);
        $action      = $this->post('action', 'save'); // 'save' atau 'finalize'

        $penetapan = $this->penetapanModel->find($penetapanId);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->flash('error', 'Penetapan tidak ditemukan.');
            $this->redirect('/penetapan');
            return;
        }

        // Simpan data form yang dikirim
        $kriteriaIds   = json_decode($penetapan['kriteria_ids'] ?? '[]', true) ?: [];
        $kriteriaKodes = $_POST['kriteria_kodes'] ?? [];
        $targetCapaian = $_POST['target_capaian'] ?? [];
        $indikator     = $_POST['indikator'] ?? [];
        $strategi      = $_POST['strategi'] ?? [];
        $sumberDaya    = $_POST['sumber_daya'] ?? [];
        $aiAnalisis    = $_POST['ai_analisis'] ?? [];

        foreach ($kriteriaIds as $kriteriaId) {
            $detailId = $this->penetapanModel->saveDetail($penetapanId, $kriteriaId, [
                'kode'           => $kriteriaKodes[$kriteriaId] ?? '',
                'target_capaian' => $targetCapaian[$kriteriaId] ?? '',
                'indikator'      => $indikator[$kriteriaId] ?? '',
                'strategi'       => $strategi[$kriteriaId] ?? '',
                'sumber_daya'    => $sumberDaya[$kriteriaId] ?? '',
                'ai_analisis'    => $aiAnalisis[$kriteriaId] ?? '',
            ]);

            // Kaitkan referensi jika ada yang belum terkait
            $db = $this->referensiModel->getDb();
            $stmt = $db->prepare(
                "UPDATE referensi SET penetapan_id = ?, penetapan_detail_id = ?, step_session_key = NULL
                 WHERE kriteria_id = ? AND penetapan_id IS NULL AND user_id = ? LIMIT 1"
            );
            $stmt->execute([$penetapanId, $detailId, $kriteriaId, $user['id']]);
        }

        if ($action === 'finalize') {
            $this->penetapanModel->update($penetapanId, [
                'status'       => 'final',
                'step_current' => 3,
            ]);
            $this->flash('success', 'Penetapan berhasil difinalisasi!');
        } else {
            $this->penetapanModel->update($penetapanId, ['step_current' => 2]);
            $this->flash('success', 'Draft penetapan berhasil disimpan!');
        }

        $this->redirect('/penetapan/' . $penetapanId);
    }

    // ============================================================
    // PENETAPAN - DETAIL / SHOW
    // ============================================================
    public function show(string $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $penetapan = $this->penetapanModel->findWithDetails((int)$id);

        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->flash('error', 'Penetapan tidak ditemukan.');
            $this->redirect('/penetapan');
            return;
        }

        $referensi = $this->referensiModel->findByUser($user['id'], (int)$id);
        $flash     = $this->getFlash();

        $this->render('penetapan/show', compact('user', 'penetapan', 'referensi', 'flash'));
    }

    // ============================================================
    // PENETAPAN - EDIT
    // ============================================================
    public function edit(string $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $penetapan = $this->penetapanModel->findWithDetails((int)$id);

        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->flash('error', 'Penetapan tidak ditemukan.');
            $this->redirect('/penetapan');
            return;
        }

        $kriteria    = $this->kriteriaModel->findByUser($user['id']);
        $tahunAjaran = $this->penetapanModel->getTahunAjaran();
        $referensi   = $this->referensiModel->findByUser($user['id'], (int)$id);
        $flash       = $this->getFlash();

        $this->render('penetapan/edit', compact('user', 'penetapan', 'kriteria', 'tahunAjaran', 'referensi', 'flash'));
    }

    // ============================================================
    // PENETAPAN - UPDATE
    // ============================================================
    public function update(string $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $penetapan = $this->penetapanModel->find((int)$id);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->flash('error', 'Penetapan tidak ditemukan.');
            $this->redirect('/penetapan');
            return;
        }

        $judul         = $this->post('judul', '');
        $deskripsi     = $this->post('deskripsi', '');
        $tahunAjaranId = (int) $this->post('tahun_ajaran_id', 0);

        $this->penetapanModel->update((int)$id, [
            'judul'           => $judul,
            'deskripsi'       => $deskripsi,
            'tahun_ajaran_id' => $tahunAjaranId,
        ]);

        // Update detail
        $targetCapaian = $_POST['target_capaian'] ?? [];
        $indikator     = $_POST['indikator'] ?? [];
        $strategi      = $_POST['strategi'] ?? [];
        $sumberDaya    = $_POST['sumber_daya'] ?? [];

        foreach ($targetCapaian as $kriteriaId => $target) {
            $this->penetapanModel->saveDetail((int)$id, (int)$kriteriaId, [
                'target_capaian' => $target,
                'indikator'      => $indikator[$kriteriaId] ?? '',
                'strategi'       => $strategi[$kriteriaId] ?? '',
                'sumber_daya'    => $sumberDaya[$kriteriaId] ?? '',
            ]);
        }

        $this->flash('success', 'Penetapan berhasil diperbarui!');
        $this->redirect('/penetapan/' . $id);
    }

    // ============================================================
    // PENETAPAN - FINALISASI
    // ============================================================
    public function finalize(string $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $penetapan = $this->penetapanModel->find((int)$id);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Not found'], 404);
            return;
        }

        $this->penetapanModel->update((int)$id, ['status' => 'final']);
        $this->flash('success', 'Penetapan telah difinalisasi!');
        $this->redirect('/penetapan/' . $id);
    }

    // ============================================================
    // PENETAPAN - TOGGLE STATUS / AKTIFKAN / NONAKTIFKAN / HIDE
    // ============================================================
    public function toggleStatus(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $penetapan = $this->penetapanModel->find($id);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->flash('error', 'Penetapan tidak ditemukan.');
            $target = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/penetapan';
            $this->redirect($target);
            return;
        }

        $targetVis = trim($_POST['visibility_status'] ?? $_POST['status'] ?? '');
        $allowed   = ['aktif', 'nonaktif', 'hidden'];

        if (!in_array($targetVis, $allowed)) {
            if (isset($_POST['is_active'])) {
                $targetVis = ((int)$_POST['is_active'] === 1) ? 'aktif' : 'nonaktif';
            } else {
                $currentVis = $penetapan['visibility_status'] ?? 'aktif';
                $targetVis  = ($currentVis === 'aktif') ? 'nonaktif' : 'aktif';
            }
        }

        $isActiveVal = ($targetVis === 'aktif') ? 1 : 0;

        $db = $this->penetapanModel->getDb();
        $db->prepare("UPDATE penetapan SET visibility_status = ? WHERE id = ? AND user_id = ?")
           ->execute([$targetVis, $id, $user['id']]);

        $msgMap = [
            'aktif'    => 'Dokumen Penetapan berhasil diaktifkan kembali dan tampil di daftar utama.',
            'nonaktif' => 'Dokumen Penetapan berhasil dinonaktifkan (tetap terlihat di daftar dokumen).',
            'hidden'   => 'Dokumen Penetapan berhasil disembunyikan (Hide). Anda dapat melihat dan mengaktifkannya kembali di menu Dokumen Tersembunyi.',
        ];

        $this->flash('success', $msgMap[$targetVis] ?? 'Status visibilitas dokumen berhasil diperbarui.');
        $target = !empty($_POST['return_url']) ? $_POST['return_url'] : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/penetapan/' . $id);
        $this->redirect($target);
    }

    // ============================================================
    // PENETAPAN - NONAKTIFKAN / SEMBUNYIKAN (Pengganti Hapus)
    // ============================================================
    public function delete(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $penetapan = $this->penetapanModel->find($id);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->flash('error', 'Penetapan tidak ditemukan.');
            $target = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/penetapan';
            $this->redirect($target);
            return;
        }

        // Soft-hide: sembunyikan dokumen tanpa menghapus datanya
        $db = $this->penetapanModel->getDb();
        $db->prepare("UPDATE penetapan SET visibility_status = 'hidden' WHERE id = ? AND user_id = ?")->execute([$id, $user['id']]);

        $this->flash('success', 'Dokumen Penetapan berhasil disembunyikan (Hide). Anda dapat melihat dan mengaktifkannya kembali di menu Dokumen Tersembunyi.');
        $target = !empty($_POST['return_url']) ? $_POST['return_url'] : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/penetapan');
        $this->redirect($target);
    }



    // ============================================================
    // KRITERIA - LIST & KELOLA
    // ============================================================
    public function kriteria(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $kriteria = $this->kriteriaModel->findByUser($user['id'], false);
        $flash    = $this->getFlash();

        $this->render('penetapan/kriteria', compact('user', 'kriteria', 'flash'));
    }

    // ============================================================
    // KRITERIA - SIMPAN (tambah/edit)
    // ============================================================
    public function saveKriteria(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $id          = (int) $this->post('id', 0);
        $kode        = strtoupper($this->post('kode', ''));
        $nama        = $this->post('nama', '');
        $deskripsi   = $this->post('deskripsi', '');
        $urutan      = (int) $this->post('urutan', 0);

        if (empty($nama)) {
            $this->flash('error', 'Nama kriteria wajib diisi.');
            $this->redirect('/penetapan/kriteria');
            return;
        }

        // Cek duplikat kode jika kode diisi
        if (!empty($kode) && $this->kriteriaModel->kodeExists($user['prodi_id'] ?? 0, $kode, $id ?: null)) {
            $this->flash('error', "Kode kriteria '{$kode}' sudah digunakan.");
            $this->redirect('/penetapan/kriteria');
            return;
        }

        if ($id > 0) {
            // Update
            $kriteria = $this->kriteriaModel->find($id);
            if (!$kriteria || $kriteria['prodi_id'] != ($user['prodi_id'] ?? null)) {
                $this->flash('error', 'Kriteria tidak ditemukan.');
                $this->redirect('/penetapan/kriteria');
                return;
            }
            $this->kriteriaModel->update($id, compact('kode', 'nama', 'deskripsi', 'urutan'));
            $this->flash('success', 'Kriteria berhasil diperbarui!');
        } else {
            // Insert
            $this->kriteriaModel->insert([
                'prodi_id'  => $user['prodi_id'] ?? null,
                'kode'      => $kode,
                'nama'      => $nama,
                'deskripsi' => $deskripsi,
                'urutan'    => $urutan,
                'aktif'     => 1,
            ]);
            $this->flash('success', 'Kriteria berhasil ditambahkan!');
        }

        $targetUrl = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/penetapan/kriteria';
        $this->redirect($targetUrl);
    }

    // ============================================================
    // KRITERIA - SIMPAN AJAX (dari modal Step 1)
    // ============================================================
    public function saveKriteriaAjax(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $kode      = strtoupper(trim($this->post('kode', '')));
        $nama      = trim($this->post('nama', ''));
        $deskripsi = trim($this->post('deskripsi', ''));
        $urutan    = (int) $this->post('urutan', 0);

        if (empty($nama)) {
            $this->json(['success' => false, 'message' => 'Nama kriteria wajib diisi.']);
            return;
        }

        if (!empty($kode) && $this->kriteriaModel->kodeExists($user['prodi_id'] ?? 0, $kode)) {
            $this->json(['success' => false, 'message' => "Kode kriteria '{$kode}' sudah digunakan."]);
            return;
        }

        $id = $this->kriteriaModel->insert([
            'prodi_id'  => $user['prodi_id'] ?? null,
            'kode'      => $kode,
            'nama'      => $nama,
            'deskripsi' => $deskripsi,
            'urutan'    => $urutan,
            'aktif'     => 1,
        ]);

        $this->json([
            'success'  => true,
            'kriteria' => [
                'id'        => $id,
                'kode'      => $kode,
                'nama'      => $nama,
                'deskripsi' => $deskripsi,
            ]
        ]);
    }

    // ============================================================
    // KRITERIA - HAPUS
    // ============================================================
    public function deleteKriteria(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $id = (int) $this->post('id', 0);
        $kriteria = $this->kriteriaModel->find($id);

        if (!$kriteria || $kriteria['user_id'] != $user['id']) {
            $this->flash('error', 'Kriteria tidak ditemukan.');
            $this->redirect('/penetapan/kriteria');
            return;
        }

        // Soft delete (nonaktifkan)
        $this->kriteriaModel->update($id, ['aktif' => 0]);
        $this->flash('success', 'Kriteria berhasil dinonaktifkan.');
        $targetUrl = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/penetapan/kriteria';
        $this->redirect($targetUrl);
    }

    // ============================================================
    // REFERENSI - HALAMAN UTAMA
    // ============================================================
    public function referensi(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $referensi = $this->referensiModel->findPool($user['id']);
        $kriteria  = $this->kriteriaModel->findByUser($user['id']);
        $flash     = $this->getFlash();

        $this->render('penetapan/referensi', compact('user', 'referensi', 'kriteria', 'flash'));
    }

    // ============================================================
    // REFERENSI - UPLOAD FILE
    // ============================================================
    public function uploadReferensi(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Gagal mengupload file. Pastikan file valid.');
            $this->redirect('/penetapan/referensi');
            return;
        }

        $file      = $_FILES['file'];
        $deskripsi = $this->post('deskripsi', '');
        $kriteriaId = (int) $this->post('kriteria_id', 0);

        // Validasi ukuran
        if ($file['size'] > MAX_FILE_SIZE) {
            $this->flash('error', 'Ukuran file melebihi batas maksimal 10 MB.');
            $this->redirect('/penetapan/referensi');
            return;
        }

        // Validasi ekstensi
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_TYPES)) {
            $this->flash('error', 'Tipe file tidak diizinkan. Gunakan: ' . implode(', ', ALLOWED_TYPES));
            $this->redirect('/penetapan/referensi');
            return;
        }

        // Buat direktori upload jika belum ada
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        // Generate nama file unik
        $newName = 'ref_' . $user['id'] . '_' . time() . '_' . uniqid() . '.' . $ext;
        $destPath = UPLOAD_DIR . $newName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->flash('error', 'Gagal menyimpan file.');
            $this->redirect('/penetapan/referensi');
            return;
        }

        // Ekstrak teks dari file
        $kontenTeks = Referensi::extractText($destPath, $ext);

        // Simpan ke database
        $this->referensiModel->insert([
            'user_id'      => $user['id'],
            'penetapan_id' => null,
            'kriteria_id'  => $kriteriaId ?: null,
            'nama_file'    => $newName,
            'nama_asli'    => $file['name'],
            'tipe_file'    => $ext,
            'ukuran'       => $file['size'],
            'konten_teks'  => $kontenTeks,
            'deskripsi'    => $deskripsi,
        ]);

        $this->flash('success', 'File "' . htmlspecialchars($file['name']) . '" berhasil diupload!');
        $this->redirect('/penetapan/referensi');
    }

    // ============================================================
    // REFERENSI - ANALISIS AI
    // ============================================================
    public function analyzeAI(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $referensiId = (int) $this->post('referensi_id', 0);
        $kriteriaIds = $_POST['kriteria_ids'] ?? [];
        $pertanyaan  = $this->post('pertanyaan', '');

        $referensi = $this->referensiModel->find($referensiId);
        if (!$referensi || $referensi['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Referensi tidak ditemukan.'], 404);
            return;
        }

        if (empty($referensi['konten_teks'])) {
            $this->json(['success' => false, 'message' => 'File tidak memiliki konten teks yang dapat dianalisis.']);
            return;
        }

        // Ambil kriteria yang dipilih
        $kriteriaList = [];
        foreach ($kriteriaIds as $kid) {
            $k = $this->kriteriaModel->find((int)$kid);
            if ($k && $k['user_id'] == $user['id']) {
                $kriteriaList[] = $k;
            }
        }

        // Bangun prompt untuk Gemini
        $kriteriaText = '';
        foreach ($kriteriaList as $k) {
            $kriteriaText .= "- [{$k['kode']}] {$k['nama']}: {$k['deskripsi']}\n";
        }

        $prompt = "Anda adalah Tim Penjaminan Mutu Perguruan Tinggi yang menyusun Laporan Evaluasi Diri (LED) Program Studi.\n\n";
        $prompt .= "PETUNJUK PENULISAN (PENTING):\n";
        $prompt .= "- Tulis langsung laporan analisis secara formal, rapi, dan akademik.\n";
        $prompt .= "- DILARANG keras menggunakan kata pengantar AI seperti 'Berikut adalah data spesifik yang berhasil diekstraksi dari dokumen:', 'Tentu, berikut...', atau kalimat sejenisnya.\n";
        $prompt .= "- DILARANG menggunakan garis pemisah tabel markdown mentah (seperti | :--- | :--- |).\n";
        $prompt .= "- Gunakan judul sub-bab (###), poin-poin (-), serta teks tebal (**Teks**) yang rapi agar siap dicetak langsung sebagai Laporan Evaluasi Diri (LED).\n\n";
        $prompt .= "KRITERIA TARGET:\n{$kriteriaText}\n\n";

        if (!empty($pertanyaan)) {
            $prompt .= "PERTANYAAN KHUSUS: {$pertanyaan}\n\n";
        }

        $prompt .= "ISI DOKUMEN REFERENSI:\n" . mb_substr($referensi['konten_teks'], 0, 15000) . "\n\n";
        $prompt .= "FORMAT LAPORAN ANALYSIS LED:\n";
        $prompt .= "### 1. Temuan dan Evaluasi per Kriteria\n";
        $prompt .= "(Uraikan temuan dari dokumentasi untuk setiap kriteria)\n\n";
        $prompt .= "### 2. Rekomendasi Penetapan Standar LED\n";
        $prompt .= "(Uraikan rekomendasi target terukur)\n\n";
        $prompt .= "### 3. Ringkasan Kunci\n";

        // Panggil Gemini API dengan user API Key
        $userModel = new User();
        $userData  = $userModel->find($user['id']);
        $userApiKey = $userData['gemini_api_key'] ?? '';
        $userModel  = $userData['gemini_model'] ?? DEFAULT_GEMINI_MODEL;

        $result = $this->callGeminiAPI($prompt, $userApiKey, $userModel);

        if ($result['success']) {
            // Simpan hasil analisis
            $this->referensiModel->update($referensiId, ['ai_hasil' => $result['text']]);
            $this->json(['success' => true, 'analisis' => $result['text']]);
        } else {
            $this->json(['success' => false, 'message' => $result['error']]);
        }
    }

    // ============================================================
    // REFERENSI - HAPUS
    // ============================================================
    public function deleteReferensi(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $id = (int) $this->post('id', 0);
        $ref = $this->referensiModel->find($id);

        if (!$ref || $ref['user_id'] != $user['id']) {
            $this->flash('error', 'Referensi tidak ditemukan.');
            $this->redirect('/penetapan/referensi');
            return;
        }

        // Hapus file fisik
        $filePath = UPLOAD_DIR . $ref['nama_file'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->referensiModel->delete($id);
        $this->flash('success', 'Referensi berhasil dihapus.');
        $this->redirect('/penetapan/referensi');
    }

    // ============================================================
    // HELPER: Panggil Gemini API
    // ============================================================
    private function callGeminiAPI(string $prompt, string $apiKey = '', string $model = ''): array
    {
        $apiKey = trim($apiKey);
        if (empty($apiKey) || $apiKey === 'YOUR_GEMINI_API_KEY_HERE') {
            return [
                'success'    => false,
                'error_type' => 'missing_key',
                'error'      => '🔑 API Key Gemini belum terpasang. Silakan masukkan API Key Gemini Anda di menu Pengaturan (Setting) atau Profile Akun.',
            ];
        }

        $payload = [
            'contents' => [[
                'parts' => [['text' => $prompt]]
            ]]
        ];

        $selectedModel = !empty($model) ? trim($model) : DEFAULT_GEMINI_MODEL;
        $primaryUrl    = 'https://generativelanguage.googleapis.com/v1beta/models/' . $selectedModel . ':generateContent';

        // Daftar URL kandidat endpoint Gemini AI (primaryUrl dahulu, kemudian fallback)
        $urls = [
            $primaryUrl,
            GEMINI_API_URL,
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent',
            'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent',
        ];
        $urls = array_values(array_unique($urls));

        $lastError = '';

        foreach ($urls as $baseUrl) {
            $ch = curl_init($baseUrl . '?key=' . $apiKey);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT        => 60,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error    = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return ['success' => false, 'error_type' => 'network_error', 'error' => 'cURL Error: ' . $error];
            }

            $data = json_decode($response, true);

            if ($httpCode === 200 && isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $rawText   = $data['candidates'][0]['content']['parts'][0]['text'];
                $cleanText = $this->cleanAiResponse($rawText);
                return [
                    'success' => true,
                    'text'    => $cleanText,
                ];
            }

            $errMsg   = $data['error']['message'] ?? ('Gagal mendapatkan respons dari AI (HTTP ' . $httpCode . ').');
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

            // Jika error dikarenakan model tidak ditemukan / tidak didukung untuk versi API ini, coba endpoint berikutnya
            if (strpos($errLower, 'not found') !== false || strpos($errLower, 'not supported') !== false) {
                continue;
            }

            break;
        }

        return ['success' => false, 'error_type' => 'api_error', 'error' => $lastError ?: 'Gagal mendapatkan respons dari AI.'];
    }

    /**
     * Membersihkan teks respons AI dari kalimat pengantar robotik (meta-intro filler)
     */
    private function cleanAiResponse(string $text): string
    {
        $text = trim($text);
        $patterns = [
            '/^Berikut\s+adalah\s+(data\s+spesifik\s+yang\s+berhasil\s+diekstraksi[^\n]*|hasil[^\n]*|analisis[^\n]*|temuan[^\n]*)\s*:\s*\n*/iu',
            '/^(Tentu|Baik),?\s*(berikut|saya)\s+[^\n]*\n*/iu',
            '/^Berikut\s+(ini\s+)?adalah\s+[^\n]*\n*/iu',
            '/^Berdasarkan\s+(dokumen|notulensi)\s+yang\s+(diberikan|diupload),?\s*berikut[^\n]*\n*/iu',
        ];
        foreach ($patterns as $pattern) {
            $text = preg_replace($pattern, '', $text);
        }
        // Hapus garis pembatas tabel markdown mentah terpisah (| :--- | :--- |)
        $text = preg_replace('/^\s*\|(\s*:?-+:?\s*\|)+\s*$/m', '', $text);
        return trim($text);
    }

    /**
     * Demo response ketika API key belum dikonfigurasi
     */
    private function demoAIResponse(string $prompt): string
    {
        return "**���📌 CATATAN: Mode Demo (API Key Akun Prodi Belum Dikonfigurasi)**\n\n" .
               "---\n\n" .
               "**Hasil Analisis Dokumen (Simulasi)**\n\n" .
               "**C1 - Visi, Misi, Tujuan, dan Strategi**\n" .
               "- **Temuan:** Dokumen memuat informasi mengenai arah pengembangan prodi yang selaras dengan visi fakultas.\n" .
               "- **Rekomendasi:** Tetapkan target capaian visi jangka pendek (1 tahun), menengah (2-3 tahun), dan panjang (4-5 tahun).\n\n" .
               "**C2 - Tata Pamong dan Kerjasama**\n" .
               "- **Temuan:** Terdapat struktur organisasi yang jelas dengan pembagian tugas.\n" .
               "- **Rekomendasi:** Perkuat mekanisme monitoring dan evaluasi tata pamong secara berkala.\n\n" .
               "**Ringkasan Umum**\n" .
               "Dokumen yang dianalisis memberikan gambaran umum kondisi program studi. " .
               "Untuk penetapan yang komprehensif, disarankan melengkapi dengan data kuantitatif capaian tahun sebelumnya.\n\n" .
               "---\n" .
               "*Untuk mengaktifkan analisis AI sesungguhnya, masukkan Gemini API Key di menu* **[Pengaturan API Key](" . BASE_URL . "/setting)**";
    }

    // ============================================================
    // AJAX: Upload Berkas SK Penetapan (Multi-upload support)
    // ============================================================
    public function uploadBerkasSk(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $penetapanId = (int) $this->post('penetapan_id', 0);
        $nomorSk     = trim($this->post('nomor_sk', ''));
        $judulSk     = trim($this->post('judul_sk', ''));
        $tglSk       = trim($this->post('tanggal_sk', ''));
        $ket         = trim($this->post('keterangan', ''));

        $penetapan = $this->penetapanModel->find($penetapanId);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Dokumen Penetapan tidak ditemukan.']);
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

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/penetapan/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $safeName = 'sk_pen_' . $penetapanId . '_' . time() . '_' . uniqid() . '.' . $ext;
        $destPath = $uploadDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->json(['success' => false, 'message' => 'Gagal menyimpan file SK.']);
            return;
        }

        $relPath  = 'uploads/penetapan/' . $safeName;
        $currList = [];
        if (!empty($penetapan['berkas_sk'])) {
            $parsed = json_decode($penetapan['berkas_sk'], true);
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

        $db = $this->penetapanModel->getDb();
        $stmt = $db->prepare("UPDATE penetapan SET berkas_sk = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([
            json_encode($currList),
            $penetapanId
        ]);

        $this->json([
            'success' => true,
            'berkas'  => $newItem,
            'total'   => count($currList),
            'message' => 'Berkas SK Penetapan berhasil diunggah.',
        ]);
    }

    // ============================================================
    // AJAX: Hapus Berkas SK Penetapan
    // ============================================================
    public function deleteBerkasSk(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $penetapanId = (int) $this->post('penetapan_id', 0);
        $berkasId    = $this->post('berkas_id', '');

        $penetapan = $this->penetapanModel->find($penetapanId);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Dokumen Penetapan tidak ditemukan.']);
            return;
        }

        $currList = [];
        if (!empty($penetapan['berkas_sk'])) {
            $parsed = json_decode($penetapan['berkas_sk'], true);
            if (is_array($parsed)) $currList = $parsed;
        }

        $newList = [];
        $deletedFile = null;
        foreach ($currList as $item) {
            if (($item['id'] ?? '') === $berkasId || ($item['file_path'] ?? '') === $berkasId) {
                $deletedFile = $item['file_path'] ?? null;
            } else {
                $newList[] = $item;
            }
        }

        if ($deletedFile) {
            $fullPath = dirname(__DIR__, 2) . '/public/' . ltrim($deletedFile, '/');
            if (file_exists($fullPath)) @unlink($fullPath);
        }

        $db = $this->penetapanModel->getDb();
        $stmt = $db->prepare("UPDATE penetapan SET berkas_sk = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([
            json_encode(array_values($newList)),
            $penetapanId
        ]);

        $this->json([
            'success' => true,
            'total'   => count($newList),
            'message' => 'Berkas SK berhasil dihapus.',
        ]);
    }
}