<?php
/**
 * Konfigurasi Aplikasi
 * Platform PPEPP Fakultas
 */

// Auto-detect base URL path for CSS/JS assets and routes
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$reqUri = str_replace('\\', '/', $_SERVER['REQUEST_URI'] ?? '');

if (str_contains($reqUri, '/ppepp/public') || str_contains($scriptName, '/ppepp/public')) {
    $baseUrl = '/ppepp/public';
} elseif (str_contains($reqUri, '/ppepp') || str_contains($scriptName, '/ppepp')) {
    $baseUrl = '/ppepp/public';
} else {
    $baseUrl = '';
}

define('BASE_URL', $baseUrl);
define('BASE_PATH', dirname(__DIR__));

// Informasi Aplikasi
define('APP_NAME', 'PPEPP Fakultas');
define('APP_VERSION', '1.0.0');
define('APP_INSTITUTION', 'Fakultas Ilmu Komputer');

// Upload
define('UPLOAD_DIR', BASE_PATH . '/public/uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10 MB
define('ALLOWED_TYPES', ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx']);

// AI (Gemini API)
define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY_HERE');
define('DEFAULT_GEMINI_MODEL', 'gemini-2.0-flash');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/' . DEFAULT_GEMINI_MODEL . ':generateContent');
define('AVAILABLE_GEMINI_MODELS', [
    'gemini-3.6-flash' => 'Gemini 3.6 Flash (Performa & Penalaran Generasi Terbaru)',
    'gemini-3.5-flash' => 'Gemini 3.5 Flash',
    'gemini-3.5-flash-lite' => 'Gemini 3.5 Flash-Lite',
    'gemini-3.1-flash-lite' => 'Gemini 3.1 Flash-Lite',
    'gemini-3-flash' => 'Gemini 3.0 Flash',
    'gemini-2.5-flash' => 'Gemini 2.5 Flash',
    'gemini-2.5-flash-lite' => 'Gemini 2.5 Flash-Lite',
    'gemini-2.0-flash' => 'Gemini 2.0 Flash (Default — Cepat, Responsif & Stabil)',
    'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash-Lite (Super Cepat & Kuota Lebih Tinggi)',
    'gemini-1.5-flash' => 'Gemini 1.5 Flash (Versi 1.5 Standar)',
    'gemini-1.5-flash-8b' => 'Gemini 1.5 Flash-8B (Model Hemat Kuota 8B)',
    'gemini-1.5-pro' => 'Gemini 1.5 Pro (Analisis Penalaran & Dokumen Kompleks)',
    'gemini-2.0-pro-exp-02-05' => 'Gemini 2.0 Pro Exp (Model Eksperimental Pro)',
    'gemini-2.0-flash-thinking-exp-01-21' => 'Gemini 2.0 Flash Thinking (Model Penalaran Mendalam)',
]);

// Session
define('SESSION_NAME', 'ppepp_session');

// Google OAuth 2.0
// Isi dengan credentials dari https://console.cloud.google.com
define('GOOGLE_CLIENT_ID',     getenv('GOOGLE_CLIENT_ID')     ?: 'YOUR_GOOGLE_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: 'YOUR_GOOGLE_CLIENT_SECRET_HERE');
define('GOOGLE_REDIRECT_URI',  getenv('GOOGLE_REDIRECT_URI')  ?: 'http://ppepp.test:8080/ppepp/public/auth/google/callback');

// Timezone
date_default_timezone_set('Asia/Jakarta');
