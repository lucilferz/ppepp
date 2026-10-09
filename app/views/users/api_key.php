<?php
$pageTitle   = 'API Key Gemini Saya';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'API Key Gemini'],
];
$hasKey = !empty($user['gemini_api_key']);
?>

<div style="max-width:680px;margin:0 auto;">

  <?php if (!$hasKey): ?>
  <!-- Warning Banner -->
  <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:18px 22px;margin-bottom:22px;display:flex;gap:14px;align-items:flex-start;">
    <div style="color:var(--warning);flex-shrink:0;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    </div>
    <div>
      <div style="font-size:14px;font-weight:700;color:#c2410c;margin-bottom:4px;">API Key Belum Diset</div>
      <div style="font-size:13px;color:#92400e;line-height:1.6;">
        Fitur <strong>AI Gemini</strong> (generate evaluasi, pengendalian, peningkatan) <strong>tidak akan berfungsi</strong> sebelum Anda mengisi API Key.
        Dapatkan API Key gratis di <a href="https://aistudio.google.com/apikey" target="_blank" style="color:#059669;font-weight:700;">aistudio.google.com/apikey</a>.
      </div>
    </div>
  </div>
  <?php else: ?>
  <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;padding:14px 18px;margin-bottom:22px;display:flex;align-items:center;gap:10px;">
    <span style="color:#059669;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    </span>
    <div style="font-size:13.5px;font-weight:700;color:#065f46;">API Key aktif — Fitur AI Gemini siap digunakan.</div>
  </div>
  <?php endif; ?>

  <?php if (!empty($flash['message'])): ?>
  <div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13.5px;font-weight:600;<?= $flash['type']==='success' ? 'background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;' : 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;' ?>">
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <div style="background:#fff;border:1.5px solid var(--border);border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
    <div style="background:var(--primary);padding:22px 28px;color:#fff;">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/></svg>
        <h2 style="font-size:18px;font-weight:800;color:#fff;margin:0;">API Key Gemini Saya</h2>
      </div>
      <p style="font-size:13px;color:rgba(255,255,255,0.75);margin:0;">
        API Key bersifat pribadi. Setiap dosen menggunakan API Key masing-masing.<br>
        Tidak ada limit kuota yang dibagi — penggunaan Anda tidak mempengaruhi dosen lain.
      </p>
    </div>

    <form action="<?= BASE_URL ?>/users/api-key/save" method="POST" style="padding:28px;display:flex;flex-direction:column;gap:20px;">

      <div>
        <label style="font-size:13px;font-weight:700;color:var(--text-main);display:block;margin-bottom:8px;">
          Google Gemini API Key <span style="color:#ef4444;">*</span>
        </label>
        <input type="text" name="gemini_api_key" class="form-control"
               value="<?= htmlspecialchars($user['gemini_api_key'] ?? '') ?>"
               placeholder="Masukkan API Key Gemini Anda (AIza...)"
               style="font-size:13.5px;padding:12px 14px;border-radius:8px;font-family:monospace;letter-spacing:0.5px;">
        <div style="margin-top:8px;font-size:12.5px;color:var(--text-muted);line-height:1.6;">
          Dapatkan API Key gratis di:
          <a href="https://aistudio.google.com/apikey" target="_blank"
             style="color:var(--primary);font-weight:600;text-decoration:none;">
            aistudio.google.com/apikey ↗
          </a>
          <br>API Key dimulai dengan "<code style="font-size:11.5px;background:#f8fafc;padding:1px 5px;border-radius:4px;border:1px solid #e2e8f0;">AIza...</code>"
        </div>
      </div>

      <div>
        <label style="font-size:13px;font-weight:700;color:var(--text-main);display:block;margin-bottom:8px;">Model AI Gemini</label>
        <select name="gemini_model" class="form-control" style="font-size:13.5px;padding:12px 14px;border-radius:8px;">
          <?php foreach (AVAILABLE_GEMINI_MODELS as $key => $label): ?>
          <option value="<?= $key ?>" <?= ($user['gemini_model'] ?? DEFAULT_GEMINI_MODEL) === $key ? 'selected' : '' ?>>
            <?= htmlspecialchars($label) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <div style="margin-top:6px;font-size:12px;color:var(--text-muted);">
          Rekomendasi: <strong>Gemini 2.0 Flash</strong> — cepat, stabil, dan gratis untuk tier standar.
        </div>
      </div>

      <div style="background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:16px 18px;">
        <div style="font-size:12px;font-weight:700;color:#475569;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;display:flex;align-items:center;gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          Info Keamanan
        </div>
        <ul style="font-size:12.5px;color:var(--text-muted);margin:0;padding-left:16px;line-height:1.8;">
          <li>API Key Anda disimpan terenkripsi di server lokal kampus</li>
          <li>Tidak dibagikan ke user lain atau pihak ketiga</li>
          <li>Digunakan hanya untuk memanggil API Gemini atas nama Anda</li>
          <li>Anda bisa menghapusnya kapan saja dengan mengosongkan field di atas</li>
        </ul>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid #f1f5f9;">
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline" style="padding:10px 20px;font-weight:600;font-size:13px;border-radius:8px;text-decoration:none;">
          Batal
        </a>
        <button type="submit" class="btn btn-primary" style="font-size:13px;padding:11px 24px;border-radius:8px;font-weight:700;">
          Simpan API Key
        </button>
      </div>
    </form>
  </div>
</div>
