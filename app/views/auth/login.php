<?php $pageTitle = 'Login — PPEPP Fakultas'; ?>

<div class="auth-card">
  <!-- Logo -->
  <div class="auth-logo">
    <div class="logo-icon">
      <svg viewBox="0 0 24 24" width="30" height="30" style="width:30px;height:30px;fill:white;flex-shrink:0;">
        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
        <path d="M2 17l10 5 10-5" fill="none" stroke="white" stroke-width="2"/>
        <path d="M2 12l10 5 10-5" fill="none" stroke="white" stroke-width="2"/>
      </svg>
    </div>
    <h1><?= APP_NAME ?></h1>
    <p><?= APP_INSTITUTION ?></p>
  </div>

  <!-- Flash -->
  <?php if ($flash): ?>
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
    <span><?= htmlspecialchars($flash['message']) ?></span>
  </div>
  <?php endif; ?>

  <h2>Masuk ke Sistem</h2>
  <p class="auth-subtitle">Gunakan NIP / Email dosen @unika.ac.id Anda</p>

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
               placeholder="Masukkan password" autocomplete="current-password" required>
      </div>
    </div>

    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18" style="flex-shrink:0;">
        <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
        <polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
      </svg>
      Masuk
    </button>
  </form>

  <!-- Divider -->
  <div style="display:flex;align-items:center;gap:12px;margin:20px 0;">
    <div style="flex:1;height:1px;background:#e2e8f0;"></div>
    <span style="font-size:12px;color:#94a3b8;font-weight:600;white-space:nowrap;">atau masuk dengan</span>
    <div style="flex:1;height:1px;background:#e2e8f0;"></div>
  </div>

  <!-- Google Login Button -->
  <a href="<?= BASE_URL ?>/auth/google" id="googleLoginBtn"
     style="display:flex;align-items:center;justify-content:center;gap:12px;width:100%;padding:11px 20px;background:#fff;border:1.5px solid #dadce0;border-radius:10px;font-size:14px;font-weight:700;color:#3c4043;text-decoration:none;transition:all 0.2s;box-shadow:0 1px 4px rgba(0,0,0,0.06);"
     onmouseover="this.style.background='#f8f9fa';this.style.boxShadow='0 3px 10px rgba(0,0,0,0.1)'"
     onmouseout="this.style.background='#fff';this.style.boxShadow='0 1px 4px rgba(0,0,0,0.06)'">
    <!-- Google G icon -->
    <svg width="20" height="20" viewBox="0 0 48 48" style="flex-shrink:0;">
      <path fill="#4285F4" d="M46.145 24.5c0-1.56-.14-3.06-.4-4.5H24v8.51h12.44c-.54 2.9-2.18 5.36-4.64 7.01l7.5 5.82C43.73 37.44 46.145 31.42 46.145 24.5z"/>
      <path fill="#34A853" d="M24 47c6.48 0 11.92-2.15 15.89-5.82l-7.5-5.82C30.27 36.56 27.27 37.5 24 37.5c-6.26 0-11.57-4.23-13.47-9.91l-7.74 5.97C6.63 42.57 14.79 47 24 47z"/>
      <path fill="#FBBC05" d="M10.53 27.59A13.96 13.96 0 0110 24c0-1.25.17-2.46.47-3.61l-7.74-5.97A23.95 23.95 0 000 24c0 3.86.93 7.51 2.56 10.74l7.97-7.15z"/>
      <path fill="#EA4335" d="M24 10.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 3.27 30.47 1 24 1 14.79 1 6.63 5.43 2.56 13.26l7.97 7.14C12.43 14.73 17.74 10.5 24 10.5z"/>
    </svg>
    Masuk dengan Akun Google @unika.ac.id
  </a>

  <p style="text-align:center;font-size:11.5px;color:#94a3b8;margin-top:16px;line-height:1.5;">
    Sistem PPEPP hanya dapat diakses oleh dosen yang terdaftar.<br>
    Hubungi Kaprodi/Dekan jika akun belum tersedia.
  </p>
</div>
