<?php
/**
 * Model: Laporan
 * Platform PPEPP Fakultas — Agregasi Data Matriks 5 Tahap PPEPP per Project
 */

class Laporan extends Model
{
    /**
     * Ambil data lengkap siklus PPEPP untuk sebuah Project
     */
    public function getProjectReport(int $projectId, int $userId): ?array
    {
        $db = $this->getDb();

        // 1. Info Project
        $stmtProj = $db->prepare(
            "SELECT p.*, ta.nama AS ta_nama, ta.semester
             FROM ppepp_project p
             JOIN tahun_ajaran ta ON ta.id = p.tahun_ajaran_id
             WHERE p.id = ? AND p.user_id = ?"
        );
        $stmtProj->execute([$projectId, $userId]);
        $project = $stmtProj->fetch(PDO::FETCH_ASSOC);
        if (!$project) return null;

        $taId = (int)$project['tahun_ajaran_id'];

        // 2. Cari dokumen 5 tahap untuk project ini (berdasarkan ppepp_project_id atau tahun_ajaran_id)
        $penetapan = $this->queryOne(
            "SELECT * FROM penetapan WHERE (ppepp_project_id = ? OR tahun_ajaran_id = ?) AND user_id = ? ORDER BY created_at DESC LIMIT 1",
            [$projectId, $taId, $userId]
        ) ?: [];

        $penetapanId = (int)($penetapan['id'] ?? 0);

        $pelaksanaan = $this->queryOne(
            "SELECT * FROM pelaksanaan WHERE (ppepp_project_id = ? OR penetapan_id = ?) AND user_id = ? ORDER BY created_at DESC LIMIT 1",
            [$projectId, $penetapanId, $userId]
        ) ?: [];
        $pelaksanaanId = (int)($pelaksanaan['id'] ?? 0);

        $evaluasi = $this->queryOne(
            "SELECT * FROM evaluasi WHERE (ppepp_project_id = ? OR penetapan_id = ?) AND user_id = ? ORDER BY created_at DESC LIMIT 1",
            [$projectId, $penetapanId, $userId]
        ) ?: [];
        $evaluasiId = (int)($evaluasi['id'] ?? 0);

        $pengendalian = $this->queryOne(
            "SELECT * FROM pengendalian WHERE (ppepp_project_id = ? OR penetapan_id = ? OR evaluasi_id = ?) AND user_id = ? ORDER BY created_at DESC LIMIT 1",
            [$projectId, $penetapanId, $evaluasiId, $userId]
        ) ?: [];
        $pengendalianId = (int)($pengendalian['id'] ?? 0);

        $peningkatan = $this->queryOne(
            "SELECT * FROM peningkatan WHERE (ppepp_project_id = ? OR penetapan_id = ? OR pengendalian_id = ?) AND user_id = ? ORDER BY created_at DESC LIMIT 1",
            [$projectId, $penetapanId, $pengendalianId, $userId]
        ) ?: [];
        $peningkatanId = (int)($peningkatan['id'] ?? 0);

        // 3. Matriks Gabungan Seluruh Standar & Indikator
        $sqlMatriks = "SELECT pd.id AS pd_id, pd.kriteria_id,
                              COALESCE(NULLIF(pd.kode, ''), k.kode) AS kriteria_kode,
                              k.nama AS kriteria_nama,
                              k.deskripsi AS kriteria_deskripsi,
                              pd.target_capaian AS p1_target,
                              pd.indikator AS p1_indikator,
                              pd.strategi AS p1_aturan,
                              COALESCE(pld.status_pelaksanaan, 'belum') AS p2_status,
                              pld.catatan_pelaksanaan AS p2_catatan,
                              COALESCE(ed.status_capaian, 'belum_tercapai') AS e_status,
                              ed.evaluasi_teks AS e_evaluasi,
                              ed.hasil_aktual AS e_hasil,
                              ed.analisis_gap AS e_gap,
                              pgd.akar_masalah AS p4_akar_masalah,
                              pgd.rencana_tindak_lanjut AS p4_rtl,
                              pgd.koreksi_standar AS p4_koreksi_standar,
                              pgd.penanggung_jawab AS p4_pic,
                              pgd.target_waktu AS p4_waktu,
                              COALESCE(pgd.status_tindakan, 'belum') AS p4_status,
                              pkd.alasan_peningkatan AS p5_alasan,
                              pkd.target_baru AS p5_target_baru,
                              pkd.indikator_baru AS p5_indikator_baru,
                              pkd.strategi_baru AS p5_strategi_baru,
                              pkd.nilai_kenaikan AS p5_nilai_kenaikan,
                              COALESCE(pkd.status_indikator, 'ditingkatkan') AS p5_status
                       FROM penetapan_detail pd
                       JOIN kriteria k ON k.id = pd.kriteria_id
                       LEFT JOIN pelaksanaan_detail pld ON pld.pelaksanaan_id = ? AND pld.penetapan_detail_id = pd.id
                       LEFT JOIN evaluasi_detail ed ON ed.evaluasi_id = ? AND (ed.penetapan_detail_id = pd.id OR (ed.penetapan_detail_id IS NULL AND ed.kriteria_id = pd.kriteria_id))
                       LEFT JOIN pengendalian_detail pgd ON pgd.pengendalian_id = ? AND (pgd.penetapan_detail_id = pd.id OR (pgd.penetapan_detail_id IS NULL AND pgd.kriteria_id = pd.kriteria_id))
                       LEFT JOIN peningkatan_detail pkd ON pkd.peningkatan_id = ? AND (pkd.penetapan_detail_id = pd.id OR (pkd.penetapan_detail_id IS NULL AND pkd.kriteria_id = pd.kriteria_id))
                       WHERE pd.penetapan_id = ?
                       ORDER BY k.urutan ASC, pd.id ASC";

        $stmtM = $db->prepare($sqlMatriks);
        $stmtM->execute([$pelaksanaanId, $evaluasiId, $pengendalianId, $peningkatanId, $penetapanId]);
        $matrix = $stmtM->fetchAll(PDO::FETCH_ASSOC);

        // Jika penetapan_detail kosong, ambil fallback dari tabel kriteria
        if (empty($matrix)) {
            $stmtFallback = $db->query("SELECT id AS kriteria_id, kode AS kriteria_kode, nama AS kriteria_nama, deskripsi AS kriteria_deskripsi FROM kriteria ORDER BY urutan ASC");
            $fallbackList = $stmtFallback->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fallbackList as $f) {
                $matrix[] = array_merge($f, [
                    'pd_id' => 0,
                    'p1_target' => '—',
                    'p1_indikator' => '—',
                    'p1_aturan' => '—',
                    'p2_status' => 'belum',
                    'p2_catatan' => '',
                    'p2_bukti' => '',
                    'e_status' => 'belum_tercapai',
                    'e_evaluasi' => '',
                    'e_hasil' => '',
                    'e_gap' => '',
                    'p4_akar_masalah' => '',
                    'p4_rtl' => '',
                    'p4_koreksi_standar' => '',
                    'p4_pic' => '',
                    'p4_waktu' => '',
                    'p4_status' => 'belum',
                    'p5_alasan' => '',
                    'p5_target_baru' => '',
                    'p5_indikator_baru' => '',
                    'p5_strategi_baru' => '',
                    'p5_nilai_kenaikan' => '',
                    'p5_status' => 'ditingkatkan',
                ]);
            }
        }

        // 4. Hitung Statistik Capaian PPEPP
        $totalStandar = count($matrix);
        $statPelaksanaan = ['terlaksana' => 0, 'proses' => 0, 'belum' => 0];
        $statEvaluasi    = ['tercapai' => 0, 'sebagian' => 0, 'belum_tercapai' => 0];
        $statPengendalian = ['selesai' => 0, 'proses' => 0, 'belum' => 0, 'total_rtl' => 0];
        $statPeningkatan = ['ditingkatkan' => 0, 'total' => 0];

        foreach ($matrix as $m) {
            // Pelaksanaan
            $p2 = $m['p2_status'] ?? 'belum';
            if (isset($statPelaksanaan[$p2])) $statPelaksanaan[$p2]++;
            else $statPelaksanaan['belum']++;

            // Evaluasi
            $e = $m['e_status'] ?? 'belum_tercapai';
            if (isset($statEvaluasi[$e])) $statEvaluasi[$e]++;
            else $statEvaluasi['belum_tercapai']++;

            // Pengendalian (khusus kriteria belum tercapai)
            if ($e !== 'tercapai') {
                $statPengendalian['total_rtl']++;
                $p4 = $m['p4_status'] ?? 'belum';
                if (isset($statPengendalian[$p4])) $statPengendalian[$p4]++;
                else $statPengendalian['belum']++;
            }

            // Peningkatan (khusus kriteria tercapai)
            if ($e === 'tercapai') {
                $statPeningkatan['total']++;
                if (!empty(trim($m['p5_target_baru'] ?? ''))) {
                    $statPeningkatan['ditingkatkan']++;
                }
            }
        }

        // 5. Lampiran & Dokumen Pendukung
        $evaluasiAbsensi = !empty($evaluasi['absensi']) ? json_decode($evaluasi['absensi'], true) : [];
        $evaluasiGambar  = !empty($evaluasi['gambar_kegiatan']) ? json_decode($evaluasi['gambar_kegiatan'], true) : [];
        $berkasKoreksi   = !empty($pengendalian['berkas_koreksi']) ? json_decode($pengendalian['berkas_koreksi'], true) : [];
        $berkasSk        = !empty($peningkatan['berkas_sk']) ? json_decode($peningkatan['berkas_sk'], true) : [];

        return [
            'project'          => $project,
            'penetapan'        => $penetapan,
            'pelaksanaan'      => $pelaksanaan,
            'evaluasi'         => $evaluasi,
            'pengendalian'     => $pengendalian,
            'peningkatan'      => $peningkatan,
            'matrix'           => $matrix,
            'stats'            => [
                'total_standar'    => $totalStandar,
                'pelaksanaan'      => $statPelaksanaan,
                'evaluasi'         => $statEvaluasi,
                'pengendalian'     => $statPengendalian,
                'peningkatan'      => $statPeningkatan,
                'pct_pelaksanaan'  => $totalStandar > 0 ? round(($statPelaksanaan['terlaksana'] / $totalStandar) * 100) : 0,
                'pct_evaluasi'     => $totalStandar > 0 ? round(($statEvaluasi['tercapai'] / $totalStandar) * 100) : 0,
                'pct_pengendalian' => $statPengendalian['total_rtl'] > 0 ? round(($statPengendalian['selesai'] / $statPengendalian['total_rtl']) * 100) : 100,
            ],
            'dokumen'          => [
                'evaluasi_undangan' => $evaluasi['undangan_file'] ?? null,
                'evaluasi_undangan_nama' => $evaluasi['undangan_nama'] ?? null,
                'evaluasi_notulensi' => $evaluasi['notulensi'] ?? '',
                'evaluasi_absensi'  => is_array($evaluasiAbsensi) ? $evaluasiAbsensi : [],
                'evaluasi_gambar'   => is_array($evaluasiGambar) ? $evaluasiGambar : [],
                'berkas_koreksi'    => is_array($berkasKoreksi) ? $berkasKoreksi : [],
                'berkas_sk'         => is_array($berkasSk) ? $berkasSk : [],
            ],
        ];
    }
}
