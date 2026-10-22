<?php
$pageTitle   = 'API Key Gemini Pengguna';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'API Key Gemini'],
];
$hasKey = !empty($user['gemini_api_key']);
?>

<div class="page-header">
  <div>
    <h2>Pengaturan API Key Gemini Personal</h2>
    <p class="page-desc">Konfigurasi kunci akses API Google Gemini pribadi untuk menjalankan fitur AI analitik mutu.</p>
  </div>
</div>

<div style="max-width:700px;margin:0 auto;">

  <?php if (!$hasKey): ?>
  <div class="alert alert-warning mb-4">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <div>
      <strong>API Key Belum Diset.</strong> Fitur AI Gemini (analisis dokumen, rekomendasi pengendalian, dan peningkatan mutu) memerlukan API Key aktif.
      Dapatkan API Key gratis di <a href="https://aistudio.google.com/apikey" target="_blank" style="color:var(--primary);font-weight:700;text-decoration:underline;">aistudio.google.com/apikey</a>.
    </div>
  </div>
  <?php else: ?>
  <div class="alert alert-success mb-4">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    <div>
      <strong>API Key Aktif.</strong> Fitur AI Gemini siap digunakan untuk akun Anda.
    </div>
  </div>
  <?php endif; ?>

  <?php if (!empty($flash['message'])): ?>
  <div class="alert <?= ($flash['type'] ?? '') === 'success' ? 'alert-success' : 'alert-danger' ?> mb-4">
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        Kunci API Google Gemini
      </h3>
    </div>

    <form action="<?= BASE_URL ?>/users/api-key/save" method="POST">
      <div class="card-body">
        <p style="font-size:13px;color:var(--text-secondary);margin-bottom:18px;line-height:1.6;">
          API Key bersifat pribadi dan terikat pada profil Anda. Kuota penggunaan tidak dibagi dengan pengguna lain, menjamin ketersediaan kuota saat audit mutu berlangsung.
        </p>

        <div class="form-group mb-3">
          <label for="gemini_api_key" class="form-label">
            Google Gemini API Key <span style="color:var(--danger);">*</span>
          </label>
          <input type="text" id="gemini_api_key" name="gemini_api_key" class="form-control font-mono"
                 value="<?= htmlspecialchars($user['gemini_api_key'] ?? '') ?>"
                 placeholder="Masukkan API Key (diawali AIzaSy...)">
          <div class="form-text">
            Dapatkan API Key di: <a href="https://aistudio.google.com/apikey" target="_blank" style="color:var(--primary);font-weight:600;">aistudio.google.com/apikey ↗</a>
          </div>
        </div>

        <div class="form-group mb-4">
          <label for="gemini_model" class="form-label">Model AI Gemini</label>
          <select id="gemini_model" name="gemini_model" class="form-control">
            <?php foreach (AVAILABLE_GEMINI_MODELS as $key => $label): ?>
            <option value="<?= $key ?>" <?= ($user['gemini_model'] ?? DEFAULT_GEMINI_MODEL) === $key ? 'selected' : '' ?>>
              <?= htmlspecialchars($label) ?>
            </option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">
            Rekomendasi standar: <strong>Gemini 2.0 Flash</strong> — cepat, stabil, dan memiliki rate limit optimal.
          </div>
        </div>

        <div class="card" style="background:var(--bg-main);border-color:var(--border);">
          <div class="card-body" style="padding:14px 16px;">
            <div style="font-size:12px;font-weight:700;color:var(--text-primary);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px;">
              Informasi Keamanan Kunci
            </div>
            <ul style="font-size:12.5px;color:var(--text-secondary);margin:0;padding-left:18px;line-height:1.7;">
              <li>API Key disimpan terenkripsi pada basis data kampus</li>
              <li>Hanya digunakan saat Anda memicu fitur AI di SIPMI</li>
              <li>Dapat dihapus sewaktu-waktu dengan mengosongkan kolom input di atas</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="card-footer" style="display:flex;justify-content:flex-end;gap:10px;">
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline">
          Batal
        </a>
        <button type="submit" class="btn btn-primary">
          Simpan API Key
        </button>
      </div>
    </form>
  </div>
</div>
