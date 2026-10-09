<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?><?= APP_NAME ?></title>
  <meta name="description" content="Platform PPEPP untuk Penjaminan Mutu Internal dan Laporan Evaluasi Diri (LED) Program Studi">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --primary: #173B67;
      --primary-hover: #122d50;
      --primary-light: #2563EB;
      --primary-subtle: #F0F5FA;
      --bg: #F7F8FA;
      --card: #FFFFFF;
      --text-main: #1F2937;
      --text-muted: #64748B;
      --text-light: #94A3B8;
      --border: #E2E8F0;
      --border-subtle: #F1F5F9;
      --success: #23845B;
      --success-bg: #ECFDF5;
      --success-border: #A7F3D0;
      --warning: #B7791F;
      --warning-bg: #FFFBEB;
      --warning-border: #FDE68A;
      --danger: #B42318;
      --danger-bg: #FEF2F2;
      --danger-border: #FECACA;
      --info: #2563EB;
      --info-bg: #EFF6FF;
      --info-border: #BFDBFE;
      --radius-sm: 6px;
      --radius: 8px;
      --radius-lg: 10px;
      --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.05);
      --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
      --shadow-lg: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
      --transition: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    }
    html { scroll-behavior: smooth; }
    body {
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--bg);
      color: var(--text-main);
      font-size: 14px;
      line-height: 1.5;
      min-height: 100vh;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    a { color: var(--primary); text-decoration: none; transition: color var(--transition); }
    a:hover { color: var(--primary-hover); }
    img { max-width: 100%; }
    ul { list-style: none; }

    /* AUTH WRAPPER */
    .auth-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #0c1829;
      background-image: 
        radial-gradient(at 0% 0%, rgba(23, 59, 103, 0.5) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(23, 59, 103, 0.35) 0px, transparent 50%);
      padding: 24px 16px;
      position: relative;
    }
    .auth-card {
      background: #FFFFFF;
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 40px 36px;
      width: 100%;
      max-width: 440px;
      box-shadow: 0 12px 36px -4px rgba(0, 0, 0, 0.18), 0 4px 12px -2px rgba(0, 0, 0, 0.08);
      position: relative;
      z-index: 1;
    }
    .auth-logo {
      text-align: center;
      margin-bottom: 28px;
    }
    .logo-icon {
      width: 48px;
      height: 48px;
      background: var(--primary);
      border-radius: var(--radius);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 14px;
      box-shadow: var(--shadow-sm);
    }
    .logo-icon svg { width: 24px; height: 24px; color: #fff; fill: currentColor; flex-shrink: 0; }
    .auth-logo h1 {
      font-size: 20px;
      font-weight: 700;
      color: var(--text-main);
      letter-spacing: -0.02em;
    }
    .auth-logo p {
      font-size: 13px;
      color: var(--text-muted);
      margin-top: 4px;
      font-weight: 500;
    }
    .auth-subtitle {
      color: var(--text-muted);
      font-size: 13px;
      margin-bottom: 24px;
      line-height: 1.5;
    }
    h2 {
      font-size: 17px;
      font-weight: 700;
      color: var(--text-main);
      margin-bottom: 4px;
      letter-spacing: -0.01em;
    }

    /* FORM */
    .form-group { margin-bottom: 18px; }
    .form-group label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-main);
      margin-bottom: 6px;
    }
    .form-control {
      width: 100%;
      padding: 10px 14px;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      font-size: 13.5px;
      font-family: inherit;
      color: var(--text-main);
      background: #FFFFFF;
      transition: border-color var(--transition), box-shadow var(--transition);
      outline: none;
    }
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(23, 59, 103, 0.12);
    }
    .form-control::placeholder {
      color: var(--text-light);
    }
    textarea.form-control { resize: vertical; min-height: 80px; }
    select.form-control {
      appearance: none;
      cursor: pointer;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 36px;
    }
    .input-icon-wrap { position: relative; }
    .input-icon-wrap .icon {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      display: flex;
      align-items: center;
      pointer-events: none;
    }
    .input-icon-wrap .form-control { padding-left: 38px; }

    /* BUTTONS */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 9px 16px;
      border-radius: var(--radius-sm);
      font-size: 13.5px;
      font-weight: 600;
      font-family: inherit;
      cursor: pointer;
      border: 1px solid transparent;
      transition: all var(--transition);
      text-decoration: none;
      white-space: nowrap;
      justify-content: center;
    }
    .btn svg { flex-shrink: 0; width: 16px; height: 16px; }
    .btn-primary {
      background: var(--primary);
      color: #FFFFFF;
      border-color: var(--primary);
      box-shadow: var(--shadow-xs);
    }
    .btn-primary:hover {
      background: var(--primary-hover);
      border-color: var(--primary-hover);
      color: #FFFFFF;
    }
    .btn-primary:focus-visible {
      outline: 2px solid var(--primary);
      outline-offset: 2px;
    }
    .btn-outline {
      background: #FFFFFF;
      color: var(--text-main);
      border-color: var(--border);
    }
    .btn-outline:hover {
      background: #F8FAFC;
      border-color: #CBD5E1;
      color: var(--text-main);
    }
    .btn-success { background: var(--success); color: #fff; }
    .btn-success:hover { filter: brightness(0.95); color: #fff; }
    .btn-danger { background: var(--danger); color: #fff; }
    .btn-danger:hover { filter: brightness(0.95); color: #fff; }
    .btn-lg {
      padding: 11px 20px;
      font-size: 14px;
      width: 100%;
      border-radius: var(--radius-sm);
    }
    .btn-sm {
      padding: 5px 10px;
      font-size: 12px;
      border-radius: var(--radius-sm);
    }
    .btn-sm svg { width: 13px; height: 13px; }

    /* ALERTS */
    .alert {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 12px 14px;
      border-radius: var(--radius-sm);
      margin-bottom: 20px;
      font-size: 13px;
      line-height: 1.5;
      border: 1px solid transparent;
    }
    .alert svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px; }
    .alert-success { background: var(--success-bg); border-color: var(--success-border); color: #166534; }
    .alert-error { background: var(--danger-bg); border-color: var(--danger-border); color: var(--danger); }
    .alert-info { background: var(--info-bg); border-color: var(--info-border); color: var(--info); }
    .alert-warning { background: var(--warning-bg); border-color: var(--warning-border); color: var(--warning); }

    /* DEMO ACCOUNTS */
    .demo-accounts {
      margin-top: 24px;
      padding: 14px;
      background: #F8FAFC;
      border-radius: var(--radius-sm);
      border: 1px solid var(--border);
    }
    .demo-accounts .label {
      font-size: 11px;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 8px;
    }
    .demo-card {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 8px 12px;
      background: #FFFFFF;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all var(--transition);
      margin-bottom: 6px;
    }
    .demo-card:last-child { margin-bottom: 0; }
    .demo-card:hover {
      border-color: #CBD5E1;
      background: #F1F5F9;
    }
    .demo-card .prodi-name { font-size: 12.5px; font-weight: 600; color: var(--text-main); }
    .demo-card .prodi-cred { font-size: 11px; color: var(--text-muted); margin-top: 1px; }
    .demo-card svg { color: var(--text-muted); width: 14px; height: 14px; flex-shrink: 0; }
  </style>
</head>
<body>

<div class="auth-wrapper">
  <?= $content ?>
</div>

</body>
</html>
