<?php
$pageTitle   = '🔑 API Key Gemini Saya';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'API Key Gemini'],
];
$hasKey = !empty($user['gemini_api_key']);
?>

<div style="max-width:680px;margin:0 auto;">

  <?php if (!$hasKey): ?>
  <!-- Warning Banner -->
  <div style="background:#fff7ed;border:1.5px solid #fed7aa;border-radius:14px;padding:18px 22px;margin-bottom:22px;display:flex;gap:14px;align-items:flex-start;">
    <div style="font-size:28px;flex-shrink:0;">⚠️</div>
    <div>
      <div style="font-size:14px;font-weight:800;color:#c2410c;margin-bottom:4px;">API Key Belum Diset</div>
      <div style="font-size:13px;color:#92400e;line-height:1.6;">
        Fitur <strong>AI Gemini</strong> (generate evaluasi, pengendalian, peningkatan) <strong>tidak akan berfungsi</strong> sebelum Anda mengisi API Key.
        Dapatkan API Key gratis di <a href="https://aistudio.google.com/apikey" target="_blank" style="color:#059669;font-weight:700;">aistudio.google.com/apikey</a>.
      </div>
    </div>
  </div>
  <?php else: ?>
  <div style="background:#ecfdf5;border:1.5px solid #a7f3d0;border-radius:14px;padding:14px 18px;margin-bottom:22px;display:flex;align-items:center;gap:10px;">
    <span style="font-size:22px;">✅</span>
    <div style="font-size:13.5px;font-weight:700;color:#065f46;">API Key aktif — Fitur AI Gemini siap digunakan.</div>
  </div>
  <?php endif; ?>

  <?php if (!empty($flash['message'])): ?>
  <div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;<?= $flash['type']==='success' ? 'background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;' : 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;' ?>">
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:18px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,0.04);">
    <div style="background:linear-gradient(135deg,#0f172a,#065f46);padding:22px 28px;">
      <h2 style="font-size:20px;font-weight:900;color:#fff;margin:0 0 4px;">🔑 API Key Gemini Saya</h2>
      <p style="font-size:13px;color:rgba(255,255,255,0.7);margin:0;">
        API Key bersifat pribadi. Setiap dosen menggunakan API Key masing-masing.<br>
        Tidak ada limit kuota yang dibagi — penggunaan Anda tidak mempengaruhi dosen lain.
      </p>
    </div>

    <form action="<?= BASE_URL ?>/users/api-key/save" method="POST" style="padding:28px;display:flex;flex-direction:column;gap:20px;">

      <div>
        <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:8px;">
          🔑 Google Gemini API Key <span style="color:#ef4444;">*</span>
        </label>
        <input type="text" name="gemini_api_key" class="form-control"
               value="<?= htmlspecialchars($user['gemini_api_key'] ?? '') ?>"
               placeholder="Masukkan API Key Gemini Anda (AIza...)"
               style="font-size:13.5px;padding:12px 14px;border-radius:10px;font-family:monospace;letter-spacing:0.5px;">
        <div style="margin-top:8px;font-size:12.5px;color:#64748b;line-height:1.6;">
          Dapatkan API Key gratis di:
          <a href="https://aistudio.google.com/apikey" target="_blank"
             style="color:#059669;font-weight:700;text-decoration:none;">
            🔗 aistudio.google.com/apikey
          </a>
          <br>API Key dimulai dengan "<code style="font-size:11.5px;background:#f8fafc;padding:1px 5px;border-radius:4px;border:1px solid #e2e8f0;">AIza...</code>"
        </div>
      </div>

      <div>
        <label style="font-size:13px;font-weight:800;color:#1e293b;display:block;margin-bottom:8px;">🤖 Model AI Gemini</label>
        <select name="gemini_model" class="form-control" style="font-size:13.5px;padding:12px 14px;border-radius:10px;">
          <?php foreach (AVAILABLE_GEMINI_MODELS as $key => $label): ?>
          <option value="<?= $key ?>" <?= ($user['gemini_model'] ?? DEFAULT_GEMINI_MODEL) === $key ? 'selected' : '' ?>>
            <?= htmlspecialchars($label) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <div style="margin-top:6px;font-size:12px;color:#94a3b8;">
          Rekomendasi: <strong>Gemini 2.0 Flash</strong> — cepat, stabil, dan gratis untuk tier standar.
        </div>
      </div>

      <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px 18px;">
        <div style="font-size:12px;font-weight:800;color:#475569;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;">ℹ️ Info Keamanan</div>
        <ul style="font-size:12.5px;color:#64748b;margin:0;padding-left:16px;line-height:1.8;">
          <li>API Key Anda disimpan terenkripsi di server lokal kampus</li>
          <li>Tidak dibagikan ke user lain atau pihak ketiga</li>
          <li>Digunakan hanya untuk memanggil API Gemini atas nama Anda</li>
          <li>Anda bisa menghapusnya kapan saja dengan mengosongkan field di atas</li>
        </ul>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid #f1f5f9;">
        <a href="<?= BASE_URL ?>/dashboard" style="display:inline-flex;align-items:center;padding:10px 20px;background:#f1f5f9;color:#334155;font-weight:700;font-size:13px;border-radius:10px;text-decoration:none;">
          Batal
        </a>
        <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#059669,#047857);border:none;font-size:13.5px;padding:11px 28px;border-radius:10px;font-weight:800;box-shadow:0 4px 14px rgba(5,150,105,0.3);">
          💾 Simpan API Key
        </button>
      </div>
    </form>
  </div>
</div>
