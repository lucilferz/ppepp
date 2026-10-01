<?php
/**
 * Controller: Evaluasi
 * Platform PPEPP Fakultas
 */

class EvaluasiController extends Controller
{
    private Evaluasi  $model;
    private Penetapan $penetapanModel;

    public function __construct()
    {
        parent::__construct();
        $this->model          = new Evaluasi();
        $this->penetapanModel = new Penetapan();
    }

    // ============================================================
    // DAFTAR EVALUASI
    // ============================================================
    public function index(): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $evaluasis = $this->model->findByUser($user['id']);
        $stats     = $this->model->statsByUser($user['id']);

        $this->render('evaluasi/index', compact('user', 'evaluasis', 'stats', 'flash'));
    }

    // ============================================================
    // FORM BUAT EVALUASI BARU
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

        $this->render('evaluasi/create', compact('user', 'penetapans', 'flash', 'projectId', 'selectedProject'));
    }

    // ============================================================
    // SIMPAN EVALUASI BARU
    // ============================================================
    public function store(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $penetapanId = (int) $this->post('penetapan_id', 0);
        $judul       = trim($this->post('judul', ''));
        $deskripsi   = trim($this->post('deskripsi', ''));
        $jenis       = trim($this->post('jenis', 'internal'));
        $jenisCustom = trim($this->post('jenis_custom', ''));

        if ($jenis === 'lainnya' && !empty($jenisCustom)) {
            $jenis = $jenisCustom;
        } elseif (empty($jenis)) {
            $jenis = 'internal';
        }

        if (!$penetapanId || empty($judul)) {
            $this->flash('error', 'Penetapan dan judul evaluasi wajib diisi.');
            $this->redirect('/evaluasi/create');
            return;
        }

        // Verifikasi penetapan milik user ini
        $penetapan = $this->penetapanModel->find($penetapanId);
        if (!$penetapan || $penetapan['user_id'] != $user['id']) {
            $this->flash('error', 'Penetapan tidak ditemukan.');
            $this->redirect('/evaluasi/create');
            return;
        }

        $evaluasiId = $this->model->insert([
            'user_id'          => $user['id'],
            'ppepp_project_id' => $penetapan['ppepp_project_id'] ?? null,
            'penetapan_id'     => $penetapanId,
            'judul'            => $judul,
            'deskripsi'        => $deskripsi,
            'jenis'            => $jenis,
            'status'           => 'draft',
        ]);

        // Inisialisasi evaluasi_detail placeholder untuk setiap indikator di penetapan_detail
        $db = $this->model->getDb();
        $stmt = $db->prepare(
            "SELECT id, kriteria_id FROM penetapan_detail WHERE penetapan_id = ? ORDER BY id ASC"
        );
        $stmt->execute([$penetapanId]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($details as $d) {
            $this->model->saveDetail($evaluasiId, (int)$d['kriteria_id'], [
                'hasil_aktual'  => '',
                'analisis_gap'  => '',
                'status_capaian'=> 'belum_tercapai',
                'catatan'       => '',
            ], (int)$d['id']);
        }

        $this->flash('success', 'Evaluasi berhasil dibuat. Silakan isi hasil evaluasi per kriteria.');
        $this->redirect('/evaluasi/' . $evaluasiId . '/edit');
    }

    // ============================================================
    // FORM EDIT EVALUASI (PER KRITERIA + DOKUMEN)
    // ============================================================
    public function edit(int $id): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $evaluasi = $this->model->findWithDetails($id, $user['id']);
        if (!$evaluasi) {
            $this->flash('error', 'Evaluasi tidak ditemukan.');
            $this->redirect('/evaluasi');
            return;
        }


        $userModel = new User();
        $allUsers  = $userModel->all();

        $taModel   = new TahunAjaran();
        $taList    = $taModel->all();

        $kriteriaModel = new Kriteria();
        $kriteriaList  = $kriteriaModel->all();

        $this->render('evaluasi/edit', compact('user', 'evaluasi', 'flash', 'allUsers', 'taList', 'kriteriaList'));
    }

    // ============================================================
    // AJAX: Auto-Save field evaluasi_detail
    // ============================================================
    public function saveDetail(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $evaluasiId        = (int) $this->post('evaluasi_id', 0);
        $kriteriaId        = (int) $this->post('kriteria_id', 0);
        $penetapanDetailId = (int) $this->post('penetapan_detail_id', 0);
        $field             = $this->post('field', '');
        $value             = $this->post('value', '');

        $evaluasi = $this->model->find($evaluasiId);
        if (!$evaluasi || $evaluasi['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Tidak ditemukan.']);
            return;
        }

        $ok = $this->model->saveDetailField($evaluasiId, $kriteriaId, $field, $value, $penetapanDetailId ?: null);
        $this->model->update($evaluasiId, ['updated_at' => date('Y-m-d H:i:s')]);
        $this->json(['success' => $ok, 'saved_at' => date('H:i:s')]);
    }

    // ============================================================
    // AJAX: AI Generate Evaluasi per Indikator / Kriteria
    // ============================================================
    public function generateAIEvaluasi(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $evaluasiId        = (int) $this->post('evaluasi_id', 0);
        $kriteriaId        = (int) $this->post('kriteria_id', 0);
        $penetapanDetailId = (int) $this->post('penetapan_detail_id', 0);

        $evaluasi = $this->model->findWithDetails($evaluasiId, $user['id']);
        if (!$evaluasi) {
            $this->json(['success' => false, 'message' => 'Evaluasi tidak ditemukan.']);
            return;
        }

        // Cari detail kriteria target
        $targetKriteria = null;
        foreach ($evaluasi['details'] as $d) {
            if ($penetapanDetailId > 0 && $d['id'] == $penetapanDetailId) {
                $targetKriteria = $d;
                break;
            } elseif ($d['kriteria_id'] == $kriteriaId) {
                $targetKriteria = $d;
                break;
            }
        }

        if (!$targetKriteria) {
            $this->json(['success' => false, 'message' => 'Indikator / kriteria tidak ditemukan pada penetapan ini.']);
            return;
        }

        $prompt = $this->buildEvaluasiPrompt($targetKriteria, $evaluasi);

        $userModel  = new User();
        $userData   = $userModel->find($user['id']);
        $userApiKey = $userData['gemini_api_key'] ?? '';
        $geminiModel = $userData['gemini_model'] ?? DEFAULT_GEMINI_MODEL;

        $result = $this->callGeminiAPI($prompt, $userApiKey, $geminiModel);

        if ($result['success']) {
            $aiText = $result['text'];
            $this->model->saveDetailField($evaluasiId, $kriteriaId, 'evaluasi_teks', $aiText, $penetapanDetailId ?: null);
            $this->model->saveDetailField($evaluasiId, $kriteriaId, 'ai_evaluasi', $aiText, $penetapanDetailId ?: null);
            
            // Set status capaian otomatis berdasarkan status pelaksanaan
            $stPel = $targetKriteria['status_pelaksanaan'] ?? 'belum';
            $stCapaian = ($stPel === 'terlaksana') ? 'tercapai' : (($stPel === 'proses') ? 'sebagian' : 'belum_tercapai');
            $this->model->saveDetailField($evaluasiId, $kriteriaId, 'status_capaian', $stCapaian, $penetapanDetailId ?: null);

            $this->json([
                'success'             => true,
                'penetapan_detail_id' => $penetapanDetailId,
                'evaluasi_teks'       => $aiText,
                'status_capaian'      => $stCapaian,
                'message'             => 'Evaluasi berhasil di-generate secara otomatis oleh AI.',
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
    // AJAX: AI Generate Seluruh Evaluasi Sekaligus (1-Klik)
    // ============================================================
    public function generateAllAIEvaluasi(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $evaluasiId = (int) $this->post('evaluasi_id', 0);
        $evaluasi   = $this->model->findWithDetails($evaluasiId, $user['id']);

        if (!$evaluasi || empty($evaluasi['details'])) {
            $this->json(['success' => false, 'message' => 'Evaluasi tidak ditemukan atau tidak memiliki indikator.']);
            return;
        }

        $userModel  = new User();
        $userData   = $userModel->find($user['id']);
        $userApiKey = $userData['gemini_api_key'] ?? '';
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
        $lastError = null;
        foreach ($evaluasi['details'] as $d) {
            $kid  = (int)$d['kriteria_id'];
            $pdId = (int)$d['id'];
            $prompt = $this->buildEvaluasiPrompt($d, $evaluasi);
            $res = $this->callGeminiAPI($prompt, $userApiKey, $geminiModel);

            if ($res['success']) {
                $aiText = $res['text'];
                $this->model->saveDetailField($evaluasiId, $kid, 'evaluasi_teks', $aiText, $pdId);
                $this->model->saveDetailField($evaluasiId, $kid, 'ai_evaluasi', $aiText, $pdId);

                $stPel = $d['status_pelaksanaan'] ?? 'belum';
                $stCapaian = ($stPel === 'terlaksana') ? 'tercapai' : (($stPel === 'proses') ? 'sebagian' : 'belum_tercapai');
                $this->model->saveDetailField($evaluasiId, $kid, 'status_capaian', $stCapaian, $pdId);

                $results[$pdId] = [
                    'kriteria_id'         => $kid,
                    'penetapan_detail_id' => $pdId,
                    'evaluasi_teks'       => $aiText,
                    'status_capaian'      => $stCapaian,
                ];
            } else {
                $lastError = $res;
            }
        }

        if (empty($results) && $lastError) {
            $this->json([
                'success'    => false,
                'error_type' => $lastError['error_type'] ?? 'error',
                'message'    => $lastError['error'] ?? 'Gagal generate evaluasi AI.',
            ]);
            return;
        }

        $this->json([
            'success'   => true,
            'generated' => $results,
            'total'     => count($results),
            'message'   => 'Berhasil men-generate evaluasi untuk ' . count($results) . ' indikator standar.',
        ]);
    }

    /**
     * Bangun prompt AI Evaluasi berdasarkan Penetapan dan Realisasi Pelaksanaan
     */
    private function buildEvaluasiPrompt(array $k, array $evaluasi): string
    {
        $kode   = $k['kriteria_kode'] ?? $k['kode'] ?? 'KTR';
        $nama   = $k['kriteria_nama'] ?? '';
        $target = $k['target_capaian'] ?? '';
        $ind    = $k['indikator'] ?? '';
        $aturan = $k['strategi'] ?? '';
        $stPel  = $k['status_pelaksanaan'] ?? 'belum';
        $catatan= $k['catatan_pelaksanaan'] ?? '';

        $stText = ($stPel === 'terlaksana') ? 'Sudah Terlaksana / Tercapai Penuh' : (($stPel === 'proses') ? 'Sedang Dalam Proses Pelaksanaan' : 'Belum Dilaksanakan / Belum Tercapai');

        $prompt  = "Anda adalah Tim Auditor Mutu SPMI Perguruan Tinggi / Asesor Penjaminan Mutu yang menyusun Laporan Evaluasi Pelaksanaan Standar (Tahap E dalam PPEPP).\n\n";
        $prompt .= "ATURAN PENULISAN WAJIB (SANGAT PENTING):\n";
        $prompt .= "1. JANGAN gunakan kalimat pengantar AI seperti 'Berikut adalah...', 'Tentu...', 'Baik...'. Langsung mulai teks evaluasi.\n";
        $prompt .= "2. DILARANG KERAS menggunakan simbol/karakter markdown apapun (DILARANG menggunakan bintang *, cetak tebal **, tanda pagar #, strip/bullet -, underscore _, backtick `, atau garis |).\n";
        $prompt .= "3. Gunakan hanya teks paragraf bersih bersambung dan nomor urut standar (1., 2., 3.).\n";
        $prompt .= "4. Tulis secara objektif, evaluatif, komprehensif, dan lugas sesuai kaidah Laporan Evaluasi Diri (LED) Akreditasi BAN-PT/LAM.\n\n";
        $prompt .= "DATA STANDAR & PELAKSANAAN FAKULTAS:\n";
        $prompt .= "- Kriteria: [{$kode}] {$nama}\n";
        if ($aturan) $prompt .= "- Dasar Aturan / Kebijakan: {$aturan}\n";
        $prompt .= "- Target Penetapan: {$target}\n";
        $prompt .= "- Indikator Ketercapaian: {$ind}\n";
        $prompt .= "- Status Keterlaksanaan Fakultas: {$stText}\n";
        if ($catatan) $prompt .= "- Realisasi / Fakta Lapangan: {$catatan}\n";
        $prompt .= "\n";
        $prompt .= "TUGAS EVALUASI:\n";
        $prompt .= "Susun narasi evaluasi lengkap yang mencakup:\n";
        $prompt .= "1. Analisis Ketercapaian Indikator (evaluasi apakah target telah terpenuhi berdasarkan fakta pelaksanaan).\n";
        $prompt .= "2. Faktor Pendukung Keberhasilan atau Akar Masalah/Kendala yang dihadapi.\n";
        $prompt .= "3. Rekomendasi Tindak Lanjut untuk Pengendalian Mutu dan Peningkatan Berkelanjutan.\n";

        return $prompt;
    }

    // ============================================================
    // AJAX: Simpan Notulensi, Rapat RTM & Sinkronisasi ke Notulensi App
    // ============================================================
    public function saveRapat(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $evaluasiId = (int) $this->post('evaluasi_id', 0);
        $evaluasi   = $this->model->find($evaluasiId);
        if (!$evaluasi || $evaluasi['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Evaluasi tidak ditemukan.']);
            return;
        }

        // Ambil data input rapat
        $topik        = trim($this->post('topik', '') ?: ($evaluasi['rapat_topik'] ?? '') ?: $evaluasi['judul']);
        $tahunAjaran  = trim($this->post('tahun_ajaran', '') ?: ($evaluasi['rapat_tahun_ajaran'] ?? '') ?: (date('Y') . '/' . (date('Y') + 1)));
        $semester     = trim($this->post('semester', '') ?: ($evaluasi['rapat_semester'] ?? '') ?: 'Genap');
        $jenis        = trim($this->post('jenis', '') ?: ($evaluasi['rapat_jenis'] ?? '') ?: 'Rapat');
        $kategori     = trim($this->post('kategori', '') ?: ($evaluasi['rapat_kategori'] ?? '') ?: 'Fakultas');
        $tempat       = trim($this->post('tempat', '') ?: ($evaluasi['rapat_tempat'] ?? '') ?: 'Ruang Rapat Dekanat');
        $tanggal      = trim($this->post('tanggal', '') ?: ($evaluasi['rapat_tanggal'] ?? '') ?: date('Y-m-d'));
        $jamMulai     = trim($this->post('jam_mulai', '') ?: ($evaluasi['rapat_jam_mulai'] ?? '') ?: '09:00:00');
        $jamSelesai   = trim($this->post('jam_selesai', '') ?: ($evaluasi['rapat_jam_selesai'] ?? '') ?: '11:00:00');
        if (strlen($jamMulai) === 5) $jamMulai .= ':00';
        if (strlen($jamSelesai) === 5) $jamSelesai .= ':00';

        $ketuaId      = (int) $this->post('ketua_id', $evaluasi['rapat_ketua_id'] ?? 0);
        $notulisId    = (int) $this->post('notulis_id', $evaluasi['rapat_notulis_id'] ?? 0);
        $lampiranLink = trim($this->post('lampiran_link', '') ?: ($evaluasi['rapat_lampiran_link'] ?? ''));

        $kriteriaRaw  = $this->post('kriteria', $evaluasi['rapat_kriteria'] ?? '');
        $kriteriaStr  = is_array($kriteriaRaw) ? implode(', ', $kriteriaRaw) : trim((string)$kriteriaRaw);

        $notulensi    = $this->post('notulensi', $evaluasi['notulensi'] ?? '');
        $absensi      = $this->post('absensi', $evaluasi['absensi'] ?? '');

        // Cari nama ketua & notulis untuk kemudahan mapping di Notulensi App
        $userModel = new User();
        $ketuaUser = $ketuaId ? $userModel->find($ketuaId) : null;
        $notulisUser = $notulisId ? $userModel->find($notulisId) : null;
        $ketuaNama = $ketuaUser['nama_lengkap'] ?? '';
        $notulisNama = $notulisUser['nama_lengkap'] ?? '';

        // Simpan ke database evaluasi PPEPP
        $db = $this->model->getDb();
        $stmt = $db->prepare(
            "UPDATE evaluasi SET 
                rapat_topik = ?,
                rapat_tahun_ajaran = ?,
                rapat_semester = ?,
                rapat_jenis = ?,
                rapat_kategori = ?,
                rapat_tempat = ?,
                rapat_tanggal = ?,
                rapat_jam_mulai = ?,
                rapat_jam_selesai = ?,
                rapat_ketua_id = ?,
                rapat_notulis_id = ?,
                rapat_kriteria = ?,
                rapat_lampiran_link = ?,
                notulensi = ?,
                absensi = ?,
                updated_at = NOW()
             WHERE id = ?"
        );
        $stmt->execute([
            $topik,
            $tahunAjaran,
            $semester,
            $jenis,
            $kategori,
            $tempat,
            $tanggal,
            $jamMulai,
            $jamSelesai,
            $ketuaId ?: null,
            $notulisId ?: null,
            $kriteriaStr,
            $lampiranLink,
            $notulensi,
            $absensi,
            $evaluasiId
        ]);

        // Siapkan payload sinkronisasi ke sistem Notulensi
        $currentRapatId = (int)($evaluasi['notulensi_rapat_id'] ?? 0);

        $syncPayload = [
            'rapat_id'         => $currentRapatId,
            'evaluasi_id'      => $evaluasiId,
            'topik'            => $topik,
            'tahun_ajaran'     => $tahunAjaran,
            'semester'         => $semester,
            'jenis'            => $jenis,
            'kategori'         => $kategori,
            'tempat'           => $tempat,
            'tanggal'          => $tanggal,
            'jam_mulai'        => $jamMulai,
            'jam_selesai'      => $jamSelesai,
            'ketua_id'         => $ketuaId,
            'ketua_nama'       => $ketuaNama,
            'notulis_id'       => $notulisId,
            'notulis_nama'     => $notulisNama,
            'kriteria'         => $kriteriaStr,
            'siklus_ppepp'     => 'Evaluasi',
            'lampiran_link'    => $lampiranLink,
            'notulensi'        => $notulensi,
            'absensi'          => $absensi,
            // Sertakan foto dokumentasi agar tersinkron ke Notulensi
            'gambar_kegiatan'  => $evaluasi['gambar_kegiatan'] ?? null,
        ];

        // Eksekusi sinkronisasi API
        $syncResult = NotulensiApiService::sync($syncPayload);

        $syncedRapatId = $evaluasi['notulensi_rapat_id'] ?? null;
        if (!empty($syncResult['success']) && !empty($syncResult['rapat_id'])) {
            $syncedRapatId = (int)$syncResult['rapat_id'];
            $stmtSync = $db->prepare(
                "UPDATE evaluasi SET notulensi_rapat_id = ?, notulensi_sync_status = 'synced', notulensi_sync_time = NOW() WHERE id = ?"
            );
            $stmtSync->execute([$syncedRapatId, $evaluasiId]);
        }

        $this->json([
            'success'   => true,
            'message'   => 'Notulensi rapat evaluasi berhasil disimpan & tersinkron ke sistem Notulensi.',
            'sync'      => $syncResult,
            'rapat_id'  => $syncedRapatId,
            'view_url'  => $syncResult['view_url'] ?? ("http://notulensi.test:8080/rapat/detail/" . $syncedRapatId),
            'pdf_url'   => $syncResult['pdf_url'] ?? ("http://notulensi.test:8080/pdf/view/" . $syncedRapatId),
            'saved_at'  => date('H:i:s')
        ]);
    }

    // ============================================================
    // AJAX: Upload Undangan Rapat Evaluasi
    // ============================================================
    public function uploadUndangan(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $evaluasiId = (int) $this->post('evaluasi_id', 0);
        $evaluasi   = $this->model->find($evaluasiId);

        if (!$evaluasi || $evaluasi['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Evaluasi tidak ditemukan.']);
            return;
        }

        if (!isset($_FILES['undangan_file']) || $_FILES['undangan_file']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'File undangan tidak valid.']);
            return;
        }

        $file     = $_FILES['undangan_file'];
        $origName = $file['name'];
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed  = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            $this->json(['success' => false, 'message' => 'Format file tidak diizinkan. Gunakan PDF, Word, atau gambar.']);
            return;
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/evaluasi/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $safeName = 'undangan_' . $evaluasiId . '_' . time() . '.' . $ext;
        $destPath = $uploadDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->json(['success' => false, 'message' => 'Gagal menyimpan file undangan.']);
            return;
        }

        $relPath = 'uploads/evaluasi/' . $safeName;
        $db = $this->model->getDb();
        $stmt = $db->prepare("UPDATE evaluasi SET undangan_file = ?, undangan_nama = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$relPath, $origName, $evaluasiId]);

        $this->json([
            'success'   => true,
            'file_path' => $relPath,
            'file_name' => $origName,
            'file_url'  => BASE_URL . '/' . $relPath,
            'message'   => 'Undangan rapat berhasil diupload.',
        ]);
    }

    // ============================================================
    // AJAX: Upload Foto Dokumentasi Kegiatan Rapat Evaluasi
    // ============================================================
    public function uploadGambar(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $evaluasiId = (int) $this->post('evaluasi_id', 0);
        $evaluasi   = $this->model->find($evaluasiId);

        if (!$evaluasi || $evaluasi['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Evaluasi tidak ditemukan.']);
            return;
        }

        if (!isset($_FILES['gambar_file']) || $_FILES['gambar_file']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'File gambar tidak valid.']);
            return;
        }

        $file     = $_FILES['gambar_file'];
        $origName = $file['name'];
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($ext, $allowed)) {
            $this->json(['success' => false, 'message' => 'Format file harus berupa gambar (JPG, PNG, WEBP).']);
            return;
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/evaluasi/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $safeName = 'img_' . $evaluasiId . '_' . time() . '_' . uniqid() . '.' . $ext;
        $destPath = $uploadDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->json(['success' => false, 'message' => 'Gagal menyimpan foto kegiatan.']);
            return;
        }

        $relPath = 'uploads/evaluasi/' . $safeName;
        $currList = [];
        if (!empty($evaluasi['gambar_kegiatan'])) {
            $parsed = json_decode($evaluasi['gambar_kegiatan'], true);
            if (is_array($parsed)) {
                foreach ($parsed as $item) {
                    $fp = $item['file_path'] ?? '';
                    $cleanFp = ltrim(str_replace('\\', '/', $fp), '/');
                    if (str_starts_with($cleanFp, 'ppepp/public/')) $cleanFp = substr($cleanFp, 13);
                    elseif (str_starts_with($cleanFp, 'ppepp/')) $cleanFp = substr($cleanFp, 6);
                    elseif (str_starts_with($cleanFp, 'public/')) $cleanFp = substr($cleanFp, 7);
                    $item['file_path'] = $cleanFp;
                    $item['url']       = BASE_URL . '/' . ltrim($cleanFp, '/');
                    $currList[]        = $item;
                }
            }
        }

        $newImg = [
            'id'        => uniqid(),
            'file_path' => $relPath,
            'file_name' => $origName,
            'url'       => BASE_URL . '/' . $relPath,
            'time'      => date('d M Y H:i'),
        ];
        $currList[] = $newImg;

        $db = $this->model->getDb();
        $stmt = $db->prepare("UPDATE evaluasi SET gambar_kegiatan = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([json_encode($currList), $evaluasiId]);

        // Auto-sync foto ke sistem Notulensi jika sudah pernah tersinkron
        if (!empty($evaluasi['notulensi_rapat_id'])) {
            NotulensiApiService::syncGambar((int)$evaluasi['notulensi_rapat_id'], $currList);
        }

        $this->json([
            'success' => true,
            'image'   => $newImg,
            'total'   => count($currList),
            'message' => 'Foto kegiatan berhasil diupload.',
        ]);
    }

    // ============================================================
    // AJAX: Hapus Foto Dokumentasi
    // ============================================================
    public function deleteGambar(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $evaluasiId = (int) $this->post('evaluasi_id', 0);
        $imgId      = $this->post('image_id', '');

        $evaluasi = $this->model->find($evaluasiId);
        if (!$evaluasi || $evaluasi['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Evaluasi tidak ditemukan.']);
            return;
        }

        $currList = [];
        if (!empty($evaluasi['gambar_kegiatan'])) {
            $parsed = json_decode($evaluasi['gambar_kegiatan'], true);
            if (is_array($parsed)) $currList = $parsed;
        }

        $updatedList = [];
        foreach ($currList as $img) {
            if ($img['id'] === $imgId || $img['file_path'] === $imgId) {
                $fullPath = dirname(__DIR__, 2) . '/public/' . $img['file_path'];
                if (file_exists($fullPath)) @unlink($fullPath);
            } else {
                $updatedList[] = $img;
            }
        }

        $db = $this->model->getDb();
        $stmt = $db->prepare("UPDATE evaluasi SET gambar_kegiatan = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([json_encode($updatedList), $evaluasiId]);

        // Auto-sync hapus foto ke sistem Notulensi jika sudah pernah tersinkron
        if (!empty($evaluasi['notulensi_rapat_id'])) {
            NotulensiApiService::syncGambar((int)$evaluasi['notulensi_rapat_id'], $updatedList);
        }

        $this->json([
            'success' => true,
            'total'   => count($updatedList),
            'message' => 'Foto kegiatan berhasil dihapus.',
        ]);
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
                $rawText   = $data['candidates'][0]['content']['parts'][0]['text'];
                $cleanText = $this->cleanAiResponse($rawText);
                return [
                    'success' => true,
                    'text'    => $cleanText,
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

    /**
     * Membersihkan teks AI dari kalimat pengantar dan seluruh simbol markdown (*, #, -, _, `, |, dsb)
     */
    private function cleanAiResponse(string $text): string
    {
        $text = trim($text);

        // 1. Hapus intro robotik AI
        $introPatterns = [
            '/^(Tentu|Baik),?\s*(berikut|saya)\s+[^\n]*\n*/iu',
            '/^Berikut\s+(ini\s+)?adalah\s+[^\n]*\n*/iu',
            '/^Berdasarkan\s+(data|dokumen|fakta)[^\n]*berikut[^\n]*\n*/iu',
        ];
        foreach ($introPatterns as $pattern) {
            $text = preg_replace($pattern, '', $text);
        }

        // 2. Hapus simbol markdown heading (#, ##, ###) di awal baris
        $text = preg_replace('/^\s*#{1,6}\s*/m', '', $text);

        // 3. Hapus simbol bold/italic/strikethrough (*, **, _, __, ~~)
        $text = str_replace(['**', '__', '~~', '***'], '', $text);
        $text = preg_replace('/(?<=\s|^)\*([^\*\n]+)\*(?=\s|$)/u', '$1', $text);
        $text = preg_replace('/(?<=\s|^)_([^_\n]+)_(?=\s|$)/u', '$1', $text);

        // 4. Hapus simbol bullet (-, *, •, +) di awal baris
        $text = preg_replace('/^\s*[\-\*•\+]\s+/m', '', $text);

        // 5. Hapus blockquote (> )
        $text = preg_replace('/^\s*>\s*/m', '', $text);

        // 6. Hapus backticks (``` atau `)
        $text = str_replace(['```', '`'], '', $text);

        // 7. Hapus pembatas tabel markdown (| --- |) dan pipa
        $text = preg_replace('/^\s*\|?(\s*:?-+:?\s*\|?)+\s*$/m', '', $text);
        $text = str_replace('|', ' ', $text);

        // 8. Rapikan spasi dan baris baru berlebih
        $lines = explode("\n", $text);
        $cleanLines = array_map('trim', $lines);
        $text = implode("\n", $cleanLines);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }


    // ============================================================
    // AJAX/POST: Tambah Dokumen (AMI/ASIK/Notulensi upload PDF)
    // ============================================================
    public function addDokumen(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        header('Content-Type: application/json');

        $evaluasiId = (int) $this->post('evaluasi_id', 0);
        $evaluasi   = $this->model->find($evaluasiId);

        if (!$evaluasi || $evaluasi['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Evaluasi tidak ditemukan.']);
            return;
        }

        $jenis      = $this->post('jenis', 'lainnya');
        $judul      = trim($this->post('judul', ''));
        $urlLink    = trim($this->post('url_link', ''));
        $keterangan = trim($this->post('keterangan', ''));

        if (empty($judul)) {
            $this->json(['success' => false, 'message' => 'Judul dokumen wajib diisi.']);
            return;
        }

        $filePath = '';
        $namaAsli = '';

        // Handle file upload jika ada
        if (!empty($_FILES['file']['name'])) {
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/evaluasi/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $namaAsli = $_FILES['file']['name'];
            $ext      = strtolower(pathinfo($namaAsli, PATHINFO_EXTENSION));
            $allowed  = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];

            if (!in_array($ext, $allowed)) {
                $this->json(['success' => false, 'message' => 'Format file tidak diizinkan. Gunakan PDF, Word, Excel, atau gambar.']);
                return;
            }

            $safeName = uniqid('ev_', true) . '.' . $ext;
            $destPath = $uploadDir . $safeName;

            if (!move_uploaded_file($_FILES['file']['tmp_name'], $destPath)) {
                $this->json(['success' => false, 'message' => 'Gagal upload file.']);
                return;
            }

            $filePath = 'uploads/evaluasi/' . $safeName;
        }

        $dokId = $this->model->addDokumen($evaluasiId, [
            'jenis'      => $jenis,
            'judul'      => $judul,
            'nama_asli'  => $namaAsli,
            'file_path'  => $filePath,
            'url_link'   => $urlLink,
            'keterangan' => $keterangan,
        ]);

        if (!$dokId) {
            $this->json(['success' => false, 'message' => 'Gagal menyimpan.']);
            return;
        }

        $this->json([
            'success'    => true,
            'dokumen_id' => $dokId,
            'jenis'      => $jenis,
            'judul'      => $judul,
            'nama_asli'  => $namaAsli ?: '—',
            'url_link'   => $urlLink,
            'file_path'  => $filePath,
            'keterangan' => $keterangan,
            'created'    => date('d M Y H:i'),
        ]);
    }

    // ============================================================
    // AJAX/POST: Hapus Dokumen
    // ============================================================
    public function deleteDokumen(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        header('Content-Type: application/json');

        $dokumenId  = (int) $this->post('dokumen_id', 0);
        $evaluasiId = (int) $this->post('evaluasi_id', 0);

        $evaluasi = $this->model->find($evaluasiId);
        if (!$evaluasi || $evaluasi['user_id'] != $user['id']) {
            $this->json(['success' => false, 'message' => 'Tidak ditemukan.']);
            return;
        }

        $ok = $this->model->deleteDokumen($dokumenId, $evaluasiId);
        $this->json(['success' => $ok]);
    }

    // ============================================================
    // DETAIL / AUDIT VIEW
    // ============================================================
    public function show(int $id): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $flash = $this->getFlash();

        $evaluasi = $this->model->findWithDetails($id, $user['id']);
        if (!$evaluasi) {
            $this->flash('error', 'Evaluasi tidak ditemukan.');
            $this->redirect('/evaluasi');
            return;
        }

        // Bukti pelaksanaan untuk perbandingan (jika ada pelaksanaan terkait)
        $db = $this->model->getDb();
        $stmtPl = $db->prepare(
            "SELECT pl.id, pl.judul, pl.status FROM pelaksanaan pl
             WHERE pl.penetapan_id = ? AND pl.user_id = ?
             ORDER BY pl.created_at DESC LIMIT 1"
        );
        $stmtPl->execute([$evaluasi['penetapan_id'], $user['id']]);
        $pelaksanaan = $stmtPl->fetch(PDO::FETCH_ASSOC) ?: null;

        $this->render('evaluasi/show', compact('user', 'evaluasi', 'pelaksanaan', 'flash'));
    }

    // ============================================================
    // FINALISASI
    // ============================================================
    public function finalize(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $evaluasi = $this->model->find($id);
        if (!$evaluasi || $evaluasi['user_id'] != $user['id']) {
            $this->flash('error', 'Evaluasi tidak ditemukan.');
            $this->redirect('/evaluasi');
            return;
        }

        $this->model->update($id, ['status' => 'final']);
        $this->flash('success', 'Evaluasi berhasil difinalisasi!');
        $this->redirect('/evaluasi/' . $id);
    }

    // ============================================================
    // EVALUASI - TOGGLE STATUS / AKTIFKAN / NONAKTIFKAN / HIDE
    // ============================================================
    public function toggleStatus(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $evaluasi = $this->model->find($id);
        if (!$evaluasi || $evaluasi['user_id'] != $user['id']) {
            $this->flash('error', 'Evaluasi tidak ditemukan.');
            $target = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/evaluasi';
            $this->redirect($target);
            return;
        }

        $targetVis = trim($_POST['visibility_status'] ?? $_POST['status'] ?? '');
        $allowed   = ['aktif', 'nonaktif', 'hidden'];

        if (!in_array($targetVis, $allowed)) {
            if (isset($_POST['is_active'])) {
                $targetVis = ((int)$_POST['is_active'] === 1) ? 'aktif' : 'nonaktif';
            } else {
                $currentVis = $evaluasi['visibility_status'] ?? 'aktif';
                $targetVis  = ($currentVis === 'aktif') ? 'nonaktif' : 'aktif';
            }
        }

        $isActiveVal = ($targetVis === 'aktif') ? 1 : 0;

        $db = $this->model->getDb();
        $db->prepare("UPDATE evaluasi SET visibility_status = ? WHERE id = ? AND user_id = ?")
           ->execute([$targetVis, $id, $user['id']]);

        $msgMap = [
            'aktif'    => 'Dokumen Evaluasi berhasil diaktifkan kembali dan tampil di daftar utama.',
            'nonaktif' => 'Dokumen Evaluasi berhasil dinonaktifkan (tetap terlihat di daftar dokumen).',
            'hidden'   => 'Dokumen Evaluasi berhasil disembunyikan (Hide). Anda dapat melihat dan mengaktifkannya kembali di menu Dokumen Tersembunyi.',
        ];

        $this->flash('success', $msgMap[$targetVis] ?? 'Status visibilitas dokumen berhasil diperbarui.');
        $target = !empty($_POST['return_url']) ? $_POST['return_url'] : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/evaluasi/' . $id);
        $this->redirect($target);
    }

    // ============================================================
    // HAPUS / NONAKTIFKAN EVALUASI (Soft-Hide)
    // ============================================================
    public function delete(int $id): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        $evaluasi = $this->model->find($id);
        if (!$evaluasi || $evaluasi['user_id'] != $user['id']) {
            $this->flash('error', 'Evaluasi tidak ditemukan.');
            $this->redirect('/evaluasi');
            return;
        }

        // Soft-hide: sembunyikan dokumen tanpa menghapus datanya
        $db = $this->model->getDb();
        $db->prepare("UPDATE evaluasi SET visibility_status = 'hidden' WHERE id = ? AND user_id = ?")->execute([$id, $user['id']]);

        $this->flash('success', 'Dokumen Evaluasi berhasil disembunyikan (Hide). Anda dapat melihat dan mengaktifkannya kembali di menu Dokumen Tersembunyi.');
        $target = !empty($_POST['return_url']) ? $_POST['return_url'] : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/evaluasi');
        $this->redirect($target);
    }
}


