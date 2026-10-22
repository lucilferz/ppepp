<?php $pageTitle = 'Masuk ke Sistem — SIPMI PPEPP'; ?>

<div class="auth-card">
  <!-- Logo & Title -->
  <div class="auth-logo">
    <div class="logo-icon">
      <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2">
        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
        <path d="M2 17l10 5 10-5"/>
        <path d="M2 12l10 5 10-5"/>
      </svg>
    </div>
    <h1>SIPMI PPEPP</h1>
    <p><?= APP_INSTITUTION ?> &bull; Penjaminan Mutu Internal</p>
  </div>

  <!-- Flash Message -->
  <?php if (!empty($flash)): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : ($flash['type'] === 'warning' ? 'warning' : 'success') ?>">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <?php if ($flash['type'] === 'error'): ?>
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
      <?php elseif ($flash['type'] === 'warning'): ?>
        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
      <?php else: ?>
        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
      <?php endif; ?>
    </svg>
    <div><?= htmlspecialchars($flash['message']) ?></div>
  </div>
  <?php endif; ?>

  <h2 style="font-size:18px;font-weight:700;color:var(--text-main);margin-bottom:4px;text-align:center;">Masuk ke Akun Anda</h2>
  <p class="auth-subtitle">Gunakan NIP, username, atau email resmi Anda</p>

  <!-- Login Form -->
  <form action="<?= BASE_URL ?>/auth/login" method="POST" id="loginForm">
    <div class="form-group">
      <label for="username">NIP / Username / Email</label>
      <div class="input-icon-wrap">
        <span class="icon">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
          </svg>
        </span>
        <input type="text" id="username" name="username" class="form-control"
               placeholder="Masukkan NIP atau email" autocomplete="username" required>
      </div>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <div class="input-icon-wrap">
        <span class="icon">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
          </svg>
        </span>
        <input type="password" id="password" name="password" class="form-control"
               placeholder="Masukkan kata sandi" autocomplete="current-password" required>
      </div>
    </div>

    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" style="width:100%;margin-top:10px;">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
        <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
        <polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
      </svg>
      Masuk ke Sistem
    </button>
  </form>

  <!-- Divider -->
  <div style="display:flex;align-items:center;gap:12px;margin:22px 0;">
    <div style="flex:1;height:1px;background:var(--border);"></div>
    <span style="font-size:11.5px;color:var(--text-muted);font-weight:500;white-space:nowrap;">atau</span>
    <div style="flex:1;height:1px;background:var(--border);"></div>
  </div>

  <!-- Google Login Button -->
  <a href="<?= BASE_URL ?>/auth/google" id="googleLoginBtn" class="btn btn-outline"
     style="width:100%;padding:10px 16px;border-color:var(--border);background:#fff;justify-content:center;gap:10px;font-weight:600;font-size:13px;">
    <svg width="18" height="18" viewBox="0 0 48 48">
      <path fill="#4285F4" d="M46.145 24.5c0-1.56-.14-3.06-.4-4.5H24v8.51h12.44c-.54 2.9-2.18 5.36-4.64 7.01l7.5 5.82C43.73 37.44 46.145 31.42 46.145 24.5z"/>
      <path fill="#34A853" d="M24 47c6.48 0 11.92-2.15 15.89-5.82l-7.5-5.82C30.27 36.56 27.27 37.5 24 37.5c-6.26 0-11.57-4.23-13.47-9.91l-7.74 5.97C6.63 42.57 14.79 47 24 47z"/>
      <path fill="#FBBC05" d="M10.53 27.59A13.96 13.96 0 0110 24c0-1.25.17-2.46.47-3.61l-7.74-5.97A23.95 23.95 0 000 24c0 3.86.93 7.51 2.56 10.74l7.97-7.15z"/>
      <path fill="#EA4335" d="M24 10.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 3.27 30.47 1 24 1 14.79 1 6.63 5.43 2.56 13.26l7.97 7.14C12.43 14.73 17.74 10.5 24 10.5z"/>
    </svg>
    Masuk dengan Akun Google Institusi
  </a>

  <p style="text-align:center;font-size:11.5px;color:var(--text-muted);margin-top:20px;line-height:1.5;">
    Platform Sistem Informasi Penjaminan Mutu Internal (SIPMI)<br>
    Bila akun belum terdaftar, silakan menghubungi Ketua Program Studi atau Dekan.
  </p>
</div>
