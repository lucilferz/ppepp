<?php
/**
 * Front Controller - Entry Point
 * Platform PPEPP Fakultas
 */

// Load konfigurasi
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';

// Load core
require_once dirname(__DIR__) . '/core/Router.php';
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/core/Model.php';
require_once dirname(__DIR__) . '/core/View.php';

// Load helpers
require_once dirname(__DIR__) . '/app/helpers/markdown.php';
require_once dirname(__DIR__) . '/app/helpers/ExcelHelper.php';
require_once dirname(__DIR__) . '/app/services/NotulensiApiService.php';

// Load models
require_once dirname(__DIR__) . '/app/models/User.php';
require_once dirname(__DIR__) . '/app/models/Prodi.php';

// Load controllers
require_once dirname(__DIR__) . '/app/controllers/UserController.php';
require_once dirname(__DIR__) . '/app/controllers/ProdiController.php';
require_once dirname(__DIR__) . '/app/controllers/AdminController.php';

// Load models
require_once dirname(__DIR__) . '/app/models/Kriteria.php';
require_once dirname(__DIR__) . '/app/models/Penetapan.php';
require_once dirname(__DIR__) . '/app/models/Referensi.php';
require_once dirname(__DIR__) . '/app/models/Pelaksanaan.php';
require_once dirname(__DIR__) . '/app/models/Evaluasi.php';
require_once dirname(__DIR__) . '/app/models/Pengendalian.php';
require_once dirname(__DIR__) . '/app/models/Peningkatan.php';
require_once dirname(__DIR__) . '/app/models/PpeppProject.php';
require_once dirname(__DIR__) . '/app/models/TahunAjaran.php';
require_once dirname(__DIR__) . '/app/models/Laporan.php';

// Mulai session
session_name(SESSION_NAME);
session_start();

// Inisialisasi Router
$basePath = '/ppepp/public';
$router = new Router($basePath);

// ============================================
// ROUTES
// ============================================

// Auth
$router->get('/auth/login',           'AuthController', 'login');
$router->post('/auth/login',          'AuthController', 'doLogin');
$router->get('/auth/logout',          'AuthController', 'logout');
$router->get('/auth/google',          'AuthController', 'googleLogin');
$router->get('/auth/google/callback', 'AuthController', 'googleCallback');

// Admin Panel (kaprodi/dekan only)
$router->get('/admin',              'AdminController', 'index');

// User Management (kaprodi/dekan only)
$router->get('/users',              'UserController', 'index');
$router->get('/users/api-key',      'UserController', 'apiKey');
$router->post('/users/api-key/save','UserController', 'saveApiKey');
$router->get('/users/create',       'UserController', 'create');
$router->post('/users/store',       'UserController', 'store');
$router->get('/users/:id/edit',     'UserController', 'edit');
$router->post('/users/:id/update',  'UserController', 'update');
$router->post('/users/:id/delete',  'UserController', 'delete');

// Prodi Management (kaprodi/dekan only)
$router->get('/prodi',                      'ProdiController', 'index');
$router->get('/prodi/create',               'ProdiController', 'create');
$router->post('/prodi/store',               'ProdiController', 'store');
$router->get('/prodi/:id',                  'ProdiController', 'show');
$router->get('/prodi/:id/edit',             'ProdiController', 'edit');
$router->post('/prodi/:id/update',          'ProdiController', 'update');
$router->post('/prodi/:id/delete',          'ProdiController', 'delete');
$router->post('/prodi/:id/add-dosen',       'ProdiController', 'addDosen');
$router->post('/prodi/:id/remove-dosen',    'ProdiController', 'removeDosen');

// Root redirect (Dashboard is default view)
$router->get('/', 'DashboardController', 'index');
$router->get('/dashboard', 'DashboardController', 'index');
$router->get('/pending', 'DashboardController', 'pending');

// Kriteria (must be above /penetapan/:id)
$router->get('/penetapan/kriteria',            'PenetapanController', 'kriteria');
$router->post('/penetapan/kriteria/save',      'PenetapanController', 'saveKriteria');
$router->post('/penetapan/kriteria/save-ajax', 'PenetapanController', 'saveKriteriaAjax');
$router->post('/penetapan/kriteria/delete',    'PenetapanController', 'deleteKriteria');

// Referensi (must be above /penetapan/:id)
$router->get('/penetapan/referensi',           'PenetapanController', 'referensi');
$router->post('/penetapan/referensi/upload',   'PenetapanController', 'uploadReferensi');
$router->post('/penetapan/referensi/analyze',  'PenetapanController', 'analyzeAI');
$router->post('/penetapan/referensi/delete',   'PenetapanController', 'deleteReferensi');

// Penetapan
$router->get('/penetapan',                     'PenetapanController', 'index');
$router->get('/penetapan/create',              'PenetapanController', 'create');
$router->get('/penetapan/download-template-excel', 'PenetapanController', 'downloadTemplateExcel');
$router->post('/penetapan/upload-excel',       'PenetapanController', 'uploadExcel');
$router->post('/penetapan/apply-excel',        'PenetapanController', 'applyExcelToPenetapan');
$router->post('/penetapan/step2',              'PenetapanController', 'step2');
$router->post('/penetapan/store',              'PenetapanController', 'store');
$router->post('/penetapan/save-draft-detail',  'PenetapanController', 'saveDraftDetail');
$router->post('/penetapan/add-standard-item',    'PenetapanController', 'addStandardItem');
$router->post('/penetapan/delete-standard-item', 'PenetapanController', 'deleteStandardItem');
$router->post('/penetapan/upload-kriteria',    'PenetapanController', 'uploadPerKriteria');
$router->post('/penetapan/analyze-kriteria',   'PenetapanController', 'analyzePerKriteria');
$router->post('/penetapan/upload-sk',          'PenetapanController', 'uploadBerkasSk');
$router->post('/penetapan/delete-sk',          'PenetapanController', 'deleteBerkasSk');
$router->get('/penetapan/:id',                 'PenetapanController', 'show');
$router->get('/penetapan/:id/edit-step2',      'PenetapanController', 'editStep2');
$router->get('/penetapan/:id/edit',            'PenetapanController', 'edit');
$router->post('/penetapan/:id/update',         'PenetapanController', 'update');
$router->post('/penetapan/:id/finalize',       'PenetapanController', 'finalize');
$router->post('/penetapan/:id/toggle-status',  'PenetapanController', 'toggleStatus');
$router->post('/penetapan/:id/delete',         'PenetapanController', 'delete');

// Evaluasi (must be before /evaluasi/:id)
$router->get('/evaluasi',                     'EvaluasiController', 'index');
$router->get('/evaluasi/create',             'EvaluasiController', 'create');
$router->post('/evaluasi/store',             'EvaluasiController', 'store');
$router->post('/evaluasi/save-detail',       'EvaluasiController', 'saveDetail');
$router->post('/evaluasi/generate-ai',       'EvaluasiController', 'generateAIEvaluasi');
$router->post('/evaluasi/generate-all-ai',   'EvaluasiController', 'generateAllAIEvaluasi');
$router->post('/evaluasi/save-rapat',        'EvaluasiController', 'saveRapat');
$router->post('/evaluasi/upload-undangan',   'EvaluasiController', 'uploadUndangan');
$router->post('/evaluasi/upload-gambar',     'EvaluasiController', 'uploadGambar');
$router->post('/evaluasi/delete-gambar',     'EvaluasiController', 'deleteGambar');
$router->post('/evaluasi/dokumen/add',       'EvaluasiController', 'addDokumen');
$router->post('/evaluasi/dokumen/delete',    'EvaluasiController', 'deleteDokumen');
$router->get('/evaluasi/:id',                'EvaluasiController', 'show');
$router->get('/evaluasi/:id/edit',           'EvaluasiController', 'edit');
$router->post('/evaluasi/:id/finalize',      'EvaluasiController', 'finalize');
$router->post('/evaluasi/:id/toggle-status', 'EvaluasiController', 'toggleStatus');
$router->post('/evaluasi/:id/delete',        'EvaluasiController', 'delete');


// Pengendalian (static routes before :id)
$router->get('/pengendalian',                          'PengendalianController', 'index');
$router->get('/pengendalian/create',                   'PengendalianController', 'create');
$router->post('/pengendalian/store',                   'PengendalianController', 'store');
$router->post('/pengendalian/save-detail',             'PengendalianController', 'saveDetail');
$router->post('/pengendalian/generate-ai',             'PengendalianController', 'generateAIRtl');
$router->post('/pengendalian/generate-all-ai',         'PengendalianController', 'generateAllAIRtl');
$router->post('/pengendalian/upload-berkas-koreksi',   'PengendalianController', 'uploadBerkasKoreksi');
$router->post('/pengendalian/delete-berkas-koreksi',   'PengendalianController', 'deleteBerkasKoreksi');
$router->post('/pengendalian/upload-notulensi',        'PengendalianController', 'uploadNotulensi');
$router->post('/pengendalian/analyze-notulensi',       'PengendalianController', 'analyzeNotulensi');
$router->post('/pengendalian/delete-notulensi',        'PengendalianController', 'deleteNotulensi');
$router->get('/pengendalian/:id',                      'PengendalianController', 'show');
$router->get('/pengendalian/:id/edit',                 'PengendalianController', 'edit');
$router->post('/pengendalian/:id/finalize',            'PengendalianController', 'finalize');
$router->post('/pengendalian/:id/toggle-status',       'PengendalianController', 'toggleStatus');
$router->post('/pengendalian/:id/delete',              'PengendalianController', 'delete');


// Peningkatan (static routes before :id)
$router->get('/peningkatan',                           'PeningkatanController', 'index');
$router->get('/peningkatan/create',                    'PeningkatanController', 'create');
$router->post('/peningkatan/store',                    'PeningkatanController', 'store');
$router->post('/peningkatan/save-detail',              'PeningkatanController', 'saveDetail');
$router->post('/peningkatan/generate-ai',              'PeningkatanController', 'generateAIPeningkatan');
$router->post('/peningkatan/generate-all-ai',          'PeningkatanController', 'generateAllAIPeningkatan');
$router->post('/peningkatan/upload-sk',                'PeningkatanController', 'uploadBerkasSk');
$router->post('/peningkatan/delete-sk',                'PeningkatanController', 'deleteBerkasSk');
$router->post('/peningkatan/upload-dokumen',           'PeningkatanController', 'uploadDokumen');
$router->post('/peningkatan/delete-dokumen',           'PeningkatanController', 'deleteDokumen');
$router->post('/peningkatan/:id/export-to-penetapan',  'PeningkatanController', 'exportToPenetapan');
$router->post('/peningkatan/:id/clone-to-penetapan',   'PeningkatanController', 'cloneToPenetapan');
$router->get('/peningkatan/:id',                       'PeningkatanController', 'show');
$router->get('/peningkatan/:id/edit',                  'PeningkatanController', 'edit');
$router->post('/peningkatan/:id/finalize',             'PeningkatanController', 'finalize');
$router->post('/peningkatan/:id/toggle-status',        'PeningkatanController', 'toggleStatus');
$router->post('/peningkatan/:id/delete',               'PeningkatanController', 'delete');


// Pelaksanaan
$router->get('/pelaksanaan',                   'PelaksanaanController', 'index');
$router->get('/pelaksanaan/create',            'PelaksanaanController', 'create');
$router->post('/pelaksanaan/store',            'PelaksanaanController', 'store');
$router->get('/pelaksanaan/:id',               'PelaksanaanController', 'show');
$router->get('/pelaksanaan/:id/edit',          'PelaksanaanController', 'edit');
$router->post('/pelaksanaan/:id/update',        'PelaksanaanController', 'update');
$router->post('/pelaksanaan/:id/toggle-status', 'PelaksanaanController', 'toggleStatus');
$router->post('/pelaksanaan/:id/delete',        'PelaksanaanController', 'delete');
$router->post('/pelaksanaan/:id/finalize',     'PelaksanaanController', 'finalize');
$router->post('/pelaksanaan/bukti/add',        'PelaksanaanController', 'addBukti');
$router->post('/pelaksanaan/bukti/delete',     'PelaksanaanController', 'deleteBukti');
$router->post('/pelaksanaan/save-status',      'PelaksanaanController', 'saveStatusDetail');

// PPEPP Project (per Tahun Ajaran) — static routes before :id
$router->get('/ppepp',                       'PpeppProjectController', 'index');
$router->get('/ppepp/create',                'PpeppProjectController', 'create');
$router->post('/ppepp/store',                'PpeppProjectController', 'store');
$router->post('/ppepp/save-monitoring-status','PpeppProjectController', 'saveMonitoringStatus');
$router->get('/ppepp/:id/monitoring',        'PpeppProjectController', 'monitoring');
$router->get('/ppepp/:id/hidden',            'PpeppProjectController', 'hiddenItems');
$router->get('/ppepp/:id',                   'PpeppProjectController', 'show');
$router->get('/ppepp/:id/edit',              'PpeppProjectController', 'edit');
$router->post('/ppepp/:id/update',           'PpeppProjectController', 'update');
$router->post('/ppepp/:id/toggle-status',   'PpeppProjectController', 'toggleStatus');
$router->post('/ppepp/:id/delete',           'PpeppProjectController', 'delete');
$router->post('/ppepp/:id/destroy',          'PpeppProjectController', 'destroy');




// Laporan Eksekutif PPEPP per Project
$router->get('/laporan',             'LaporanController', 'index');
$router->get('/laporan/cetak',       'LaporanController', 'cetak');
$router->get('/laporan/export-csv',  'LaporanController', 'exportCsv');

// Pengaturan
$router->get('/setting',        'SettingController', 'index');
$router->post('/setting/update', 'SettingController', 'update');

// Dispatch
$router->dispatch();
