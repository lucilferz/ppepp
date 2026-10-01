<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?><?= APP_NAME ?></title>
  <meta name="description" content="Platform PPEPP untuk penyusunan Laporan Evaluasi Diri (LED) Program Studi">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --primary: #1a237e;
      --primary-mid: #283593;
      --primary-light: #3f51b5;
      --primary-glow: #5c6bc0;
      --accent: #f59e0b;
      --accent-light: #fbbf24;
      --danger: #ef4444;
      --success: #10b981;
      --text-main: #1e293b;
      --text-muted: #64748b;
      --bg: #f0f4ff;
      --card: #ffffff;
      --border: #e2e8f0;
      --radius: 16px;
      --radius-sm: 10px;
      --shadow: 0 4px 24px rgba(26,35,126,0.10);
      --shadow-lg: 0 8px 40px rgba(26,35,126,0.18);
    }
    html { scroll-behavior: smooth; }
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: var(--bg);
      color: var(--text-main);
      font-size: 14px;
      line-height: 1.6;
      min-height: 100vh;
    }
    a { color: var(--primary-light); text-decoration: none; }
    a:hover { color: var(--accent); }
    img { max-width: 100%; }
    ul { list-style: none; }

    /* AUTH WRAPPER */
    .auth-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #0f172a 0%, #1a237e 50%, #283593 100%);
      padding: 20px;
      position: relative;
      overflow: hidden;
    }
    .auth-wrapper::before {
      content: '';
      position: absolute;
      width: 700px; height: 700px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(245,158,11,0.12) 0%, transparent 70%);
      top: -200px; right: -200px;
      pointer-events: none;
    }
    .auth-wrapper::after {
      content: '';
      position: absolute;
      width: 500px; height: 500px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(63,81,181,0.2) 0%, transparent 70%);
      bottom: -150px; left: -150px;
      pointer-events: none;
    }
    .auth-card {
      background: rgba(255,255,255,0.98);
      border-radius: 24px;
      padding: 48px 44px;
      width: 100%;
      max-width: 460px;
      box-shadow: 0 24px 80px rgba(0,0,0,0.3);
      position: relative;
      z-index: 1;
      backdrop-filter: blur(10px);
    }
    .auth-logo {
      text-align: center;
      margin-bottom: 32px;
    }
    .logo-icon {
      width: 64px; height: 64px;
      background: linear-gradient(135deg, var(--primary), var(--primary-light));
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
      box-shadow: 0 8px 24px rgba(26,35,126,0.35);
    }
    .logo-icon svg { width: 30px; height: 30px; color: #fff; fill: currentColor; flex-shrink: 0; }
    .auth-logo h1 { font-size: 22px; font-weight: 800; color: var(--text-main); letter-spacing: -0.5px; }
    .auth-logo p { font-size: 13px; color: var(--text-muted); margin-top: 4px; }
    .auth-subtitle { color: var(--text-muted); font-size: 13px; margin-bottom: 28px; }
    h2 { font-size: 20px; font-weight: 700; color: var(--text-main); margin-bottom: 6px; }

    /* FORM */
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 7px; }
    .form-control {
      width: 100%;
      padding: 11px 14px;
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      font-size: 14px;
      font-family: inherit;
      color: var(--text-main);
      background: #f8faff;
      transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
      outline: none;
    }
    .form-control:focus { border-color: var(--primary-light); background: #fff; box-shadow: 0 0 0 3px rgba(63,81,181,0.12); }
    textarea.form-control { resize: vertical; min-height: 80px; }
    select.form-control { appearance: none; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; }
    .input-icon-wrap { position: relative; }
    .input-icon-wrap .icon { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--text-muted); display: flex; align-items: center; pointer-events: none; }
    .input-icon-wrap .form-control { padding-left: 40px; }

    /* BUTTONS */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 10px 18px;
      border-radius: var(--radius-sm);
      font-size: 13.5px;
      font-weight: 600;
      font-family: inherit;
      cursor: pointer;
      border: 1.5px solid transparent;
      transition: all 0.2s ease;
      text-decoration: none;
      white-space: nowrap;
    }
    .btn svg { flex-shrink: 0; width: 16px; height: 16px; }
    .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; box-shadow: 0 4px 14px rgba(26,35,126,0.25); }
    .btn-primary:hover { background: linear-gradient(135deg, var(--primary-mid), var(--primary-glow)); color: #fff; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(26,35,126,0.35); }
    .btn-outline { background: transparent; color: var(--primary-light); border-color: var(--border); }
    .btn-outline:hover { background: #eef2ff; border-color: var(--primary-light); color: var(--primary); }
    .btn-success { background: linear-gradient(135deg, #059669, #10b981); color: #fff; }
    .btn-success:hover { filter: brightness(1.1); color: #fff; }
    .btn-danger { background: linear-gradient(135deg, #dc2626, #ef4444); color: #fff; }
    .btn-danger:hover { filter: brightness(1.1); color: #fff; }
    .btn-accent { background: linear-gradient(135deg, #d97706, var(--accent)); color: #fff; }
    .btn-accent:hover { filter: brightness(1.1); color: #fff; }
    .btn-lg { padding: 13px 22px; font-size: 14.5px; width: 100%; justify-content: center; border-radius: 12px; }
    .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }
    .btn-sm svg { width: 13px; height: 13px; }

    /* ALERTS */
    .alert {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 14px 16px;
      border-radius: var(--radius-sm);
      margin-bottom: 20px;
      font-size: 13.5px;
      line-height: 1.5;
    }
    .alert svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px; }
    .alert-success { background: #ecfdf5; border: 1.5px solid #a7f3d0; color: #065f46; }
    .alert-error { background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b; }
    .alert-info { background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1e40af; }
    .alert-warning { background: #fffbeb; border: 1.5px solid #fde68a; color: #92400e; }

    /* DEMO ACCOUNTS */
    .demo-accounts { margin-top: 24px; padding: 16px; background: #f8faff; border-radius: 12px; border: 1.5px solid var(--border); }
    .demo-accounts .label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 10px; }
    .demo-card {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 14px;
      background: #fff;
      border: 1.5px solid var(--border);
      border-radius: 10px;
      cursor: pointer;
      transition: all 0.2s;
      margin-bottom: 8px;
    }
    .demo-card:last-child { margin-bottom: 0; }
    .demo-card:hover { border-color: var(--primary-light); background: #eef2ff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(63,81,181,0.1); }
    .demo-card .prodi-name { font-size: 13px; font-weight: 600; color: var(--text-main); }
    .demo-card .prodi-cred { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
    .demo-card svg { color: var(--primary-light); width: 16px; height: 16px; flex-shrink: 0; }
  </style>
</head>
<body>

<div class="auth-wrapper">
  <?= $content ?>
</div>

</body>
</html>
