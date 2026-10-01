<?php
$pageTitle   = 'Pengaturan API Key';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Pengaturan'],
];
$hasApiKey = !empty($user['gemini_api_key']);
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h2>Pengaturan Program Studi & API Key</h2>
    <p class="page-desc">Kelola preferensi dan API Key AI khusus untuk Program Studi <strong><?= htmlspecialchars($user['nama_prodi']) ?></strong></p>
  </div>
</div>

<div class="grid-2" style="gap:24px;">
  <!-- Form Setting API Key -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
        </svg>
        Gemini AI API Key
      </h3>
      <span class="badge <?= $hasApiKey ? 'badge-success' : 'badge-warning' ?> badge-dot">
        <?= $hasApiKey ? 'Terkonfigurasi' : 'Belum Ada Key' ?>
      </span>
    </div>
    <form action="<?= BASE_URL ?>/setting/update" method="POST">
      <div class="card-body">
        <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:20px;line-height:1.6;">
          Setiap program studi dapat memiliki API Key sendiri untuk melakukan analisis dokumen menggunakan model AI Gemini. API Key Anda disimpan secara aman dan terpisah dari prodi lain.
        </p>

        <div class="form-group">
          <label for="gemini_api_key">Gemini API Key (Prodi <?= htmlspecialchars($user['prodi_kode'] ?? $user['kode_prodi'] ?? 'SI') ?>)</label>
          <div class="input-icon-wrap" style="position:relative;">
            <span class="icon">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0110 0v4"/>
              </svg>
            </span>
            <input type="password" id="gemini_api_key" name="gemini_api_key" class="form-control"
                   value="<?= htmlspecialchars($user['gemini_api_key'] ?? '') ?>"
                   placeholder="AIzaSy..." style="padding-right:45px;" required>
            <button type="button" id="toggleApiKey" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);padding:4px;"
                    title="Tampilkan / Sembunyikan API Key">
              <svg id="eyeIcon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
          <span style="font-size:12px;color:var(--text-muted);margin-top:6px;display:block;">
            Format API Key biasanya diawali dengan <code>AIzaSy...</code>
          </span>
        </div>

        <div class="form-group" style="margin-top:20px;">
          <label for="gemini_model">Model AI Gemini</label>
          <div class="input-icon-wrap" style="position:relative;">
            <span class="icon">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
              </svg>
            </span>
            <select id="gemini_model" name="gemini_model" class="form-control" style="padding-left:42px;">
              <?php 
              $currentModel = $user['gemini_model'] ?? DEFAULT_GEMINI_MODEL;
              $availableModels = defined('AVAILABLE_GEMINI_MODELS') ? AVAILABLE_GEMINI_MODELS : [
                'gemini-3.6-flash'                    => 'Gemini 3.6 Flash (Performa & Penalaran Generasi Terbaru)',
                'gemini-3.5-flash'                    => 'Gemini 3.5 Flash',
                'gemini-3.5-flash-lite'               => 'Gemini 3.5 Flash-Lite',
                'gemini-3.1-flash-lite'               => 'Gemini 3.1 Flash-Lite',
                'gemini-3-flash'                      => 'Gemini 3.0 Flash',
                'gemini-2.5-flash'                    => 'Gemini 2.5 Flash',
                'gemini-2.5-flash-lite'               => 'Gemini 2.5 Flash-Lite',
                'gemini-2.0-flash'                    => 'Gemini 2.0 Flash (Default — Cepat & Responsif)',
                'gemini-2.0-flash-lite'               => 'Gemini 2.0 Flash-Lite (Super Cepat & Kuota Lebih Tinggi)',
                'gemini-1.5-flash'                    => 'Gemini 1.5 Flash (Versi 1.5 Standar)',
                'gemini-1.5-flash-8b'                 => 'Gemini 1.5 Flash-8B (Model Hemat Kuota 8B)',
                'gemini-1.5-pro'                      => 'Gemini 1.5 Pro (Analisis Penalaran & Dokumen Kompleks)',
                'gemini-2.0-pro-exp-02-05'            => 'Gemini 2.0 Pro Exp (Model Eksperimental Pro)',
                'gemini-2.0-flash-thinking-exp-01-21' => 'Gemini 2.0 Flash Thinking (Model Penalaran Mendalam)',
              ];
              foreach ($availableModels as $key => $label): 
              ?>
                <option value="<?= htmlspecialchars($key) ?>" <?= $currentModel === $key ? 'selected' : '' ?>>
                  <?= htmlspecialchars($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <span style="font-size:12px;color:var(--text-muted);margin-top:6px;display:block;line-height:1.5;">
            Pilih model Gemini yang diinginkan. Cek status rate limit & penggunaan kuota akun Anda di 
            <a href="https://aistudio.google.com/app/rate-limit?timeRange=last-28-days" target="_blank" style="color:var(--primary-color, #2563eb);text-decoration:underline;font-weight:500;">
              Google AI Studio Rate Limits
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="12" height="12" style="vertical-align:middle;display:inline-block;">
                <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
              </svg>
            </a>
          </span>
        </div>

        <?php if ($hasApiKey): ?>
        <div class="alert alert-success" style="margin-bottom:0;margin-top:16px;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
          <div>
            <strong>API Key & Model Aktif:</strong> Model <code><?= htmlspecialchars($currentModel) ?></code> siap digunakan untuk analisis AI dengan API Key prodi Anda.
          </div>
        </div>
        <?php else: ?>
        <div class="alert alert-warning" style="margin-bottom:0;margin-top:16px;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
          </svg>
          <div>
            <strong>API Key Belum Diisi:</strong> Analisis AI saat ini akan berjalan dalam mode simulasi/demo. Masukkan API Key Anda untuk mengaktifkan analisis AI secara langsung.
          </div>
        </div>
        <?php endif; ?>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
          </svg>
          Simpan Pengaturan AI
        </button>
      </div>
    </form>
  </div>

  <!-- Panduan Dapatkan API Key -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
        Cara Mendapatkan Gemini API Key Gratis
      </h3>
    </div>
    <div class="card-body">
      <ol style="padding-left:20px;font-size:13.5px;color:var(--text-main);line-height:1.8;">
        <li style="margin-bottom:10px;">
          Buka portal <strong>Google AI Studio</strong>: <br>
          <a href="https://aistudio.google.com/app/apikey" target="_blank" class="btn btn-outline btn-sm mt-1">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
              <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
            Buka Google AI Studio
          </a>
        </li>
        <li style="margin-bottom:10px;">
          Login dengan akun Google/Gmail Anda.
        </li>
        <li style="margin-bottom:10px;">
          Klik tombol <strong>"Create API Key"</strong>.
        </li>
        <li style="margin-bottom:10px;">
          Salin (copy) kode API Key yang dihasilkan.
        </li>
        <li>
          Tempelkan (paste) kode ke kolom form di sebelah kiri lalu klik <strong>Simpan API Key</strong>.
        </li>
      </ol>

      <div class="divider"></div>

      <div style="background:#f8fafc;border:1px solid var(--border);border-radius:var(--radius-sm);padding:14px;font-size:12.5px;color:var(--text-muted);margin-bottom:12px;">
        <strong style="color:var(--text-main);">📊 Cek Kuota & Rate Limit:</strong><br>
        Anda dapat memantau penggunaan RPM (Requests Per Minute) dan RPD (Requests Per Day) untuk setiap model di: <br>
        <a href="https://aistudio.google.com/app/rate-limit?timeRange=last-28-days" target="_blank" class="btn btn-outline btn-sm mt-1" style="margin-top:6px;display:inline-flex;align-items:center;gap:6px;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
          </svg>
          Halaman AI Studio Rate Limits
        </a>
      </div>

      <div style="background:#fafbfc;border:1px solid var(--border);border-radius:var(--radius-sm);padding:14px;font-size:12.5px;color:var(--text-muted);">
        <strong style="color:var(--text-main);">Informasi Akun Prodi:</strong><br>
        • Nama: <?= htmlspecialchars($user['prodi_nama'] ?? $user['nama_prodi'] ?? $user['nama_lengkap'] ?? '-') ?><br>
        • Kode: <?= htmlspecialchars($user['prodi_kode'] ?? $user['kode_prodi'] ?? 'SI') ?><br>
        • Email: <?= htmlspecialchars($user['email'] ?? '-') ?><br>
        • Model AI Aktif: <code><?= htmlspecialchars($user['gemini_model'] ?? DEFAULT_GEMINI_MODEL) ?></code>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggleBtn = document.getElementById('toggleApiKey');
  const apiKeyInput = document.getElementById('gemini_api_key');
  if (toggleBtn && apiKeyInput) {
    toggleBtn.addEventListener('click', function() {
      const type = apiKeyInput.getAttribute('type') === 'password' ? 'text' : 'password';
      apiKeyInput.setAttribute('type', type);
    });
  }
});
</script>
