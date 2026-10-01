<?php
/**
 * Model: Referensi (File Upload)
 * Platform PPEPP Fakultas
 */

class Referensi extends Model
{
    protected string $table = 'referensi';

    /**
     * Ambil semua referensi milik user (opsional filter per penetapan)
     */
    public function findByUser(int $userId, ?int $penetapanId = null): array
    {
        $sql = "SELECT r.*, k.kode as kriteria_kode, k.nama as kriteria_nama
                FROM referensi r
                LEFT JOIN kriteria k ON k.id = r.kriteria_id
                WHERE r.user_id = ?";
        $params = [$userId];

        if ($penetapanId) {
            $sql .= " AND r.penetapan_id = ?";
            $params[] = $penetapanId;
        }

        $sql .= " ORDER BY r.created_at DESC";
        return $this->query($sql, $params);
    }

    /**
     * Ambil referensi yang belum terikat penetapan (pool)
     */
    public function findPool(int $userId): array
    {
        return $this->query(
            "SELECT * FROM referensi WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
    }

    /**
     * Format ukuran file
     */
    public static function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    /**
     * Ekstrak teks dari file
     * Mendukung: txt, (pdf via pdftotext jika tersedia)
     */
    public static function extractText(string $filePath, string $extension): string
    {
        $text = '';
        switch (strtolower($extension)) {
            case 'txt':
                $text = file_get_contents($filePath);
                break;
            case 'pdf':
                // 1. Coba pdftotext jika tersedia
                $output = [];
                @exec("pdftotext " . escapeshellarg($filePath) . " -", $output);
                if (!empty($output)) {
                    $text = implode("\n", $output);
                } else {
                    // 2. Fallback: Coba Python (pypdf dengan UTF-8 output)
                    $pyScript = "import sys; sys.stdout.reconfigure(encoding='utf-8'); from pypdf import PdfReader; reader = PdfReader(sys.argv[1]); text = '\\n'.join([p.extract_text() for p in reader.pages if p.extract_text()]); print(text)";
                    $pyOutput = [];
                    @exec("python -c " . escapeshellarg($pyScript) . " " . escapeshellarg($filePath), $pyOutput);
                    if (!empty($pyOutput)) {
                        $text = implode("\n", $pyOutput);
                    } else {
                        $text = '[PDF - ekstraksi teks tidak dapat diproses. Pastikan file PDF berbasis teks bukan hasil scan.]';
                    }
                }
                break;
            case 'docx':
                // Baca docx sebagai zip, ambil word/document.xml
                try {
                    $zip = new ZipArchive();
                    if ($zip->open($filePath) === true) {
                        $xml = $zip->getFromName('word/document.xml');
                        $zip->close();
                        if ($xml) {
                            $text = strip_tags(str_replace(['</w:p>', '<w:br/>'], "\n", $xml));
                            $text = preg_replace('/\s+/', ' ', $text);
                        }
                    }
                } catch (Exception $e) {
                    $text = '[Gagal membaca DOCX: ' . $e->getMessage() . ']';
                }
                break;
            default:
                $text = '[Tipe file tidak didukung untuk ekstraksi teks]';
        }
        return mb_substr(trim($text), 0, 50000); // Batasi 50k karakter
    }
}
