<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?><?= APP_NAME ?></title>
  <meta name="description" content="Platform PPEPP untuk penyusunan Laporan Evaluasi Diri (LED) Program Studi">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">
  <style>
    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --primary-dark: #0f172a;
      --primary: #1a237e;
      --primary-mid: #283593;
      --primary-light: #3f51b5;
      --primary-glow: #5c6bc0;
      --accent: #f59e0b;
      --accent-light: #fbbf24;
      --danger: #ef4444;
      --success: #10b981;
      --warning: #f59e0b;
      --info: #3b82f6;
      --text-main: #1e293b;
      --text-muted: #64748b;
      --text-light: #94a3b8;
      --bg: #f0f4ff;
      --card: #ffffff;
      --border: #e2e8f0;
      --sidebar-bg: linear-gradient(180deg, #0f172a 0%, #1a237e 60%, #283593 100%);
      --sidebar-width: 255px;
      --header-height: 62px;
      --radius: 14px;
      --radius-sm: 10px;
      --shadow: 0 2px 16px rgba(26, 35, 126, 0.09);
      --shadow-lg: 0 8px 32px rgba(26, 35, 126, 0.16);
      --transition: 0.18s ease;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: var(--bg);
      color: var(--text-main);
      font-size: 14px;
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
    }

    a {
      color: var(--primary-light);
      text-decoration: none;
    }

    a:hover {
      color: var(--accent);
    }

    ul {
      list-style: none;
    }

    svg {
      display: block;
    }

    /* ======= SIDEBAR ======= */
    .sidebar {
      width: var(--sidebar-width);
      min-height: 100vh;
      background: var(--sidebar-bg);
      display: flex;
      flex-direction: column;
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      z-index: 100;
      transition: transform var(--transition);
      overflow: hidden;
    }

    .sidebar-header {
      padding: 22px 20px 16px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    }

    .sidebar-brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand-logo {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #f59e0b, #d97706);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
      flex-shrink: 0;
    }

    .brand-logo svg {
      width: 22px;
      height: 22px;
      color: #ffffff;
      stroke: #ffffff;
      fill: none;
    }

    .brand-title {
      font-size: 15px;
      font-weight: 900;
      color: #ffffff !important;
      letter-spacing: -0.3px;
      line-height: 1.2;
    }

    .brand-sub {
      font-size: 11px;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.75) !important;
      margin-top: 2px;
    }

    .sidebar-prodi {
      margin: 14px 16px;
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      padding: 10px 14px;
    }

    .prodi-label {
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.7px;
      color: rgba(255, 255, 255, 0.4);
      margin-bottom: 3px;
    }

    .prodi-name {
      font-size: 13px;
      font-weight: 700;
      color: #fff;
    }

    .sidebar-nav {
      flex: 1;
      overflow-y: auto;
      padding: 8px 0 16px;
      scrollbar-width: none;
    }

    .sidebar-nav::-webkit-scrollbar {
      display: none;
    }

    .nav-section-label {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: rgba(255, 255, 255, 0.3);
      padding: 14px 20px 6px;
    }

    .nav-item {
      padding: 2px 10px;
    }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 12px;
      border-radius: 8px;
      color: rgba(255, 255, 255, 0.6);
      font-size: 13px;
      font-weight: 500;
      transition: all var(--transition);
      cursor: pointer;
    }

    .nav-link:hover {
      background: rgba(255, 255, 255, 0.08);
      color: #fff;
    }

    .nav-link.active {
      background: rgba(255, 255, 255, 0.14);
      color: #fff;
      font-weight: 600;
    }

    .nav-icon {
      width: 18px;
      height: 18px;
      flex-shrink: 0;
    }

    .nav-icon svg {
      width: 18px;
      height: 18px;
    }

    /* PPEPP Steps */
    .ppepp-steps {
      padding: 4px 10px 4px;
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .ppepp-step {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 12px;
      border-radius: 8px;
      cursor: pointer;
      transition: all var(--transition);
      text-decoration: none;
    }

    .ppepp-step:hover {
      background: rgba(255, 255, 255, 0.08);
    }

    .ppepp-step.active {
      background: linear-gradient(135deg, rgba(245, 158, 11, 0.25), rgba(245, 158, 11, 0.1));
      border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .step-num {
      width: 30px;
      height: 30px;
      background: rgba(255, 255, 255, 0.15);
      color: rgba(255, 255, 255, 0.7);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 700;
      flex-shrink: 0;
      transition: all var(--transition);
    }

    .ppepp-step.active .step-num {
      background: linear-gradient(135deg, var(--accent), #d97706);
      color: #fff;
      font-weight: 800;
      box-shadow: 0 2px 8px rgba(245, 158, 11, 0.4);
    }

    .ppepp-step.disabled-nav {
      opacity: 0.45;
      pointer-events: none;
    }

    .sidebar-footer {
      padding: 12px 16px 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.07);
    }

    .logout-btn {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 9px 14px;
      border-radius: 8px;
      color: rgba(255, 255, 255, 0.55);
      font-size: 13px;
      font-weight: 500;
      transition: all var(--transition);
    }

    .logout-btn:hover {
      background: rgba(239, 68, 68, 0.12);
      color: #f87171;
    }

    .logout-btn svg {
      width: 16px;
      height: 16px;
    }

    /* ======= MAIN CONTENT ======= */
    .main-content {
      flex: 1;
      margin-left: var(--sidebar-width);
      min-width: 0;
      display: flex;
      flex-direction: column;
    }

    /* TOP BAR */
    .topbar {
      height: var(--header-height);
      background: #fff;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 28px;
      position: sticky;
      top: 0;
      z-index: 50;
      box-shadow: 0 1px 6px rgba(26, 35, 126, 0.07);
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .page-title {
      font-size: 16px;
      font-weight: 700;
      color: var(--text-main);
    }

    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .breadcrumb .sep {
      color: var(--text-light);
    }

    .breadcrumb a {
      color: var(--text-muted);
    }

    .breadcrumb a:hover {
      color: var(--primary-light);
    }

    .topbar-right {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .user-badge {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .user-avatar {
      width: 36px;
      height: 36px;
      background: linear-gradient(135deg, var(--primary), var(--primary-light));
      color: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 700;
      flex-shrink: 0;
    }

    .user-name {
      font-size: 13px;
      font-weight: 600;
      color: var(--text-main);
    }

    .user-role {
      font-size: 11px;
      color: var(--text-muted);
    }

    /* CONTENT AREA */
    .content-area {
      flex: 1;
      padding: 28px 30px;
      overflow-y: auto;
    }

    /* ======= CARDS ======= */
    .card {
      background: var(--card);
      border-radius: var(--radius);
      border: 1px solid var(--border);
      box-shadow: var(--shadow);
      overflow: hidden;
      margin-bottom: 20px;
    }

    .card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 22px;
      border-bottom: 1px solid var(--border);
      background: #fafbff;
    }

    .card-title {
      display: flex;
      align-items: center;
      gap: 9px;
      font-size: 14.5px;
      font-weight: 700;
      color: var(--text-main);
    }

    .card-title svg {
      width: 18px;
      height: 18px;
      color: var(--primary-light);
      stroke: currentColor;
      fill: none;
    }

    .card-body {
      padding: 22px;
    }

    .card-footer {
      padding: 14px 22px;
      border-top: 1px solid var(--border);
      background: #fafbff;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    /* ======= STATS ======= */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 16px;
      margin-bottom: 22px;
    }

    .stat-card {
      background: var(--card);
      border-radius: var(--radius);
      border: 1px solid var(--border);
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 16px;
      box-shadow: var(--shadow);
      transition: transform var(--transition), box-shadow var(--transition);
    }

    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .stat-icon svg {
      width: 22px;
      height: 22px;
      fill: none;
      stroke: currentColor;
    }

    .stat-icon.blue {
      background: #eff6ff;
      color: #2563eb;
    }

    .stat-icon.green {
      background: #ecfdf5;
      color: #059669;
    }

    .stat-icon.gold {
      background: #fffbeb;
      color: #d97706;
    }

    .stat-icon.purple {
      background: #f5f3ff;
      color: #7c3aed;
    }

    .stat-value {
      font-size: 26px;
      font-weight: 800;
      color: var(--text-main);
      letter-spacing: -1px;
      line-height: 1;
    }

    .stat-label {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 4px;
      font-weight: 500;
    }

    /* ======= FORM ======= */
    .form-group {
      margin-bottom: 18px;
    }

    .form-group label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-main);
      margin-bottom: 7px;
    }

    .form-control {
      width: 100%;
      padding: 10px 14px;
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      font-size: 13.5px;
      font-family: inherit;
      color: var(--text-main);
      background: #f8faff;
      transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
      outline: none;
    }

    .form-control:focus {
      border-color: var(--primary-light);
      background: #fff;
      box-shadow: 0 0 0 3px rgba(63, 81, 181, 0.1);
    }

    textarea.form-control {
      resize: vertical;
      min-height: 80px;
    }

    select.form-control {
      appearance: none;
      cursor: pointer;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 36px;
    }

    .input-icon-wrap {
      position: relative;
    }

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

    .input-icon-wrap .form-control {
      padding-left: 38px;
    }

    .grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    @media (max-width: 600px) {
      .grid-2 {
        grid-template-columns: 1fr;
      }
    }

    /* ======= BUTTONS ======= */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 9px 16px;
      border-radius: var(--radius-sm);
      font-size: 13px;
      font-weight: 600;
      font-family: inherit;
      cursor: pointer;
      border: 1.5px solid transparent;
      transition: all var(--transition);
      text-decoration: none;
      white-space: nowrap;
    }

    .btn svg {
      flex-shrink: 0;
      width: 15px;
      height: 15px;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary), var(--primary-light));
      color: #fff;
      box-shadow: 0 3px 10px rgba(26, 35, 126, 0.22);
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, var(--primary-mid), var(--primary-glow));
      color: #fff;
      transform: translateY(-1px);
      box-shadow: 0 5px 16px rgba(26, 35, 126, 0.3);
    }

    .btn-outline {
      background: transparent;
      color: var(--primary-light);
      border-color: var(--border);
    }

    .btn-outline:hover {
      background: #eef2ff;
      border-color: var(--primary-light);
      color: var(--primary);
    }

    .btn-success {
      background: linear-gradient(135deg, #059669, #10b981);
      color: #fff;
    }

    .btn-success:hover {
      filter: brightness(1.08);
      color: #fff;
    }

    .btn-danger {
      background: linear-gradient(135deg, #dc2626, #ef4444);
      color: #fff;
    }

    .btn-danger:hover {
      filter: brightness(1.08);
      color: #fff;
    }

    .btn-accent {
      background: linear-gradient(135deg, #d97706, var(--accent));
      color: #fff;
    }

    .btn-accent:hover {
      filter: brightness(1.08);
      color: #fff;
    }

    .btn-lg {
      padding: 12px 20px;
      font-size: 14px;
    }

    .btn-sm {
      padding: 6px 11px;
      font-size: 12px;
      border-radius: 7px;
    }

    .btn-sm svg {
      width: 13px;
      height: 13px;
    }

    /* PAGE HEADER */
    .page-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 22px;
      flex-wrap: wrap;
    }

    .page-header h2 {
      font-size: 20px;
      font-weight: 800;
      color: var(--text-main);
      letter-spacing: -0.4px;
    }

    .page-desc {
      color: var(--text-muted);
      font-size: 13.5px;
      margin-top: 4px;
    }

    .page-actions {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    /* ALERTS */
    .alert {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 13px 16px;
      border-radius: var(--radius-sm);
      margin-bottom: 20px;
      font-size: 13.5px;
      line-height: 1.5;
    }

    .alert svg {
      width: 17px;
      height: 17px;
      flex-shrink: 0;
      margin-top: 1px;
    }

    .alert-success {
      background: #ecfdf5;
      border: 1.5px solid #a7f3d0;
      color: #065f46;
    }

    .alert-error {
      background: #fef2f2;
      border: 1.5px solid #fecaca;
      color: #991b1b;
    }

    .alert-info {
      background: #eff6ff;
      border: 1.5px solid #bfdbfe;
      color: #1e40af;
    }

    .alert-warning {
      background: #fffbeb;
      border: 1.5px solid #fde68a;
      color: #92400e;
    }

    /* BADGES */
    .badge {
      display: inline-flex;
      align-items: center;
      padding: 3px 9px;
      border-radius: 20px;
      font-size: 11.5px;
      font-weight: 600;
    }

    .badge-primary {
      background: #eef2ff;
      color: var(--primary-light);
    }

    .badge-success {
      background: #ecfdf5;
      color: #059669;
    }

    .badge-warning {
      background: #fffbeb;
      color: #d97706;
    }

    .badge-info {
      background: #eff6ff;
      color: #2563eb;
    }

    .badge-gray {
      background: #f1f5f9;
      color: #475569;
    }

    .badge-dot::before {
      content: '•';
      margin-right: 4px;
    }

    /* TABLE */
    .table-wrap {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13.5px;
    }

    thead th {
      background: #fafbff;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-muted);
      padding: 10px 14px;
      text-align: left;
      border-bottom: 1.5px solid var(--border);
    }

    tbody tr {
      border-bottom: 1px solid #f1f5f9;
      transition: background var(--transition);
    }

    tbody tr:hover {
      background: #f8faff;
    }

    tbody td {
      padding: 12px 14px;
      color: var(--text-main);
      vertical-align: middle;
    }

    .td-muted {
      color: var(--text-muted);
    }

    /* EMPTY STATE */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
    }

    .empty-icon {
      width: 64px;
      height: 64px;
      background: #f1f5f9;
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
    }

    .empty-icon svg {
      width: 28px;
      height: 28px;
      color: var(--text-light);
      stroke: currentColor;
      fill: none;
    }

    .empty-state h3 {
      font-size: 16px;
      font-weight: 700;
      color: var(--text-main);
      margin-bottom: 8px;
    }

    .empty-state p {
      font-size: 13.5px;
      color: var(--text-muted);
      margin-bottom: 20px;
      line-height: 1.6;
    }

    /* MODALS */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.6);
      z-index: 200;
      align-items: center;
      justify-content: center;
      padding: 20px;
      backdrop-filter: blur(4px);
    }

    .modal-overlay.open {
      display: flex;
    }

    .modal {
      background: #fff;
      border-radius: 18px;
      width: 100%;
      max-width: 560px;
      max-height: 90vh;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      box-shadow: 0 24px 80px rgba(0, 0, 0, 0.3);
      animation: modalIn 0.2s ease;
    }

    @keyframes modalIn {
      from {
        opacity: 0;
        transform: translateY(20px) scale(0.97);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .modal-lg {
      max-width: 680px;
    }

    .modal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 18px 22px;
      border-bottom: 1px solid var(--border);
      flex-shrink: 0;
    }

    .modal-header h3 {
      font-size: 15.5px;
      font-weight: 700;
      color: var(--text-main);
      display: flex;
      align-items: center;
      gap: 9px;
    }

    .modal-header h3 svg {
      flex-shrink: 0;
    }

    .modal-close {
      background: none;
      border: none;
      cursor: pointer;
      color: var(--text-muted);
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      transition: all var(--transition);
    }

    .modal-close:hover {
      background: #f1f5f9;
      color: var(--text-main);
    }

    .modal-body {
      padding: 22px;
      overflow-y: auto;
      flex: 1;
    }

    .modal-footer {
      padding: 14px 22px;
      border-top: 1px solid var(--border);
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      flex-shrink: 0;
      background: #fafbff;
    }

    /* AI PANEL */
    .ai-panel {
      background: linear-gradient(135deg, #0f172a 0%, #1a237e 60%, #283593 100%);
      border-radius: var(--radius);
      padding: 22px 24px;
      margin-bottom: 20px;
    }

    .ai-panel-header {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 8px;
    }

    .ai-icon {
      width: 42px;
      height: 42px;
      background: linear-gradient(135deg, var(--accent), #d97706);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .ai-icon svg {
      width: 20px;
      height: 20px;
      color: #fff;
      stroke: #fff;
      fill: none;
    }

    .ai-panel h3 {
      font-size: 15px;
      font-weight: 700;
      color: #fff;
    }

    .ai-panel p {
      font-size: 12.5px;
      color: rgba(255, 255, 255, 0.55);
      margin-top: 2px;
    }

    /* AI RESULT & TABLE STYLING FOR LED REPORTS */
    .ai-result {
      font-size: 13.5px;
      color: rgba(255, 255, 255, 0.92);
      line-height: 1.7;
    }

    .ai-result-card {
      font-size: 13.5px;
      color: var(--text-main);
      line-height: 1.7;
    }

    .ai-table {
      width: 100%;
      border-collapse: collapse;
      margin: 14px 0;
      font-size: 13px;
      background: #ffffff;
      color: #1e293b;
      border-radius: 8px;
      overflow: hidden;
      border: 1px solid #cbd5e1;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .ai-table th {
      background: #f1f5f9;
      color: #0f172a;
      font-weight: 700;
      padding: 10px 14px;
      text-align: left;
      border: 1px solid #cbd5e1;
    }

    .ai-table td {
      padding: 9px 14px;
      color: #334155;
      border: 1px solid #e2e8f0;
      vertical-align: top;
    }

    .ai-table tr:nth-child(even) td {
      background: #f8fafc;
    }

    .ai-result ul,
    .ai-result-card ul {
      margin: 8px 0 12px;
      padding-left: 22px;
    }

    .ai-result li,
    .ai-result-card li {
      margin-bottom: 4px;
    }

    .ai-h4 {
      color: #fff;
      font-size: 16px;
      font-weight: 700;
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
      padding-bottom: 6px;
      margin: 18px 0 10px;
    }

    .card .ai-h4,
    .ai-result-card .ai-h4 {
      color: #0f172a;
      border-bottom-color: #cbd5e1;
    }

    .ai-loading {
      display: none;
      align-items: center;
      gap: 10px;
      color: rgba(255, 255, 255, 0.65);
      font-size: 13px;
    }

    .spinner {
      width: 16px;
      height: 16px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    /* FILE LIST */
    .file-list-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px 0;
      border-bottom: 1px solid var(--border);
    }

    .file-list-item:last-child {
      border-bottom: none;
    }

    .file-icon {
      width: 38px;
      height: 38px;
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: 800;
      flex-shrink: 0;
      color: #fff;
    }

    .file-icon.pdf {
      background: linear-gradient(135deg, #dc2626, #ef4444);
    }

    .file-icon.docx,
    .file-icon.doc {
      background: linear-gradient(135deg, #1d4ed8, #3b82f6);
    }

    .file-icon.txt {
      background: linear-gradient(135deg, #475569, #64748b);
    }

    .file-icon.xls,
    .file-icon.xlsx {
      background: linear-gradient(135deg, #059669, #10b981);
    }

    .file-info {
      flex: 1;
      min-width: 0;
    }

    .file-name {
      font-size: 14px;
      font-weight: 600;
      color: var(--text-main);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .file-meta {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 2px;
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      align-items: center;
    }

    .file-actions {
      display: flex;
      align-items: center;
      gap: 7px;
      flex-shrink: 0;
    }

    /* DROPZONE */
    .dropzone {
      border: 2px dashed var(--border);
      border-radius: var(--radius);
      padding: 40px 20px;
      text-align: center;
      cursor: pointer;
      transition: all var(--transition);
      background: #f8faff;
      position: relative;
    }

    .dropzone:hover {
      border-color: var(--primary-light);
      background: #eef2ff;
    }

    .dropzone input[type="file"] {
      position: absolute;
      inset: 0;
      opacity: 0;
      cursor: pointer;
      width: 100%;
      height: 100%;
    }

    .dropzone-icon {
      width: 52px;
      height: 52px;
      background: #eef2ff;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 14px;
    }

    .dropzone-icon svg {
      width: 24px;
      height: 24px;
      color: var(--primary-light);
      fill: none;
      stroke: currentColor;
    }

    .dropzone h4 {
      font-size: 15px;
      font-weight: 700;
      color: var(--text-main);
      margin-bottom: 6px;
    }

    .dropzone p {
      font-size: 12.5px;
      color: var(--text-muted);
    }

    .file-types {
      display: flex;
      gap: 6px;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 12px;
    }

    .file-type-badge {
      background: #fff;
      border: 1.5px solid var(--border);
      border-radius: 6px;
      padding: 3px 10px;
      font-size: 11.5px;
      font-weight: 700;
      color: var(--text-muted);
    }

    /* ACCORDION */
    .kriteria-accordion {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .ka-item {
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      overflow: hidden;
      transition: border-color var(--transition);
    }

    .ka-item:focus-within,
    .ka-item:hover {
      border-color: rgba(63, 81, 181, 0.3);
    }

    .ka-header {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px 18px;
      cursor: pointer;
      background: #fafbff;
      user-select: none;
    }

    .ka-header:hover {
      background: #f0f4ff;
    }

    .ka-code {
      width: 36px;
      height: 36px;
      background: linear-gradient(135deg, var(--primary), var(--primary-light));
      color: #fff;
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 800;
      flex-shrink: 0;
    }

    .ka-title {
      flex: 1;
    }

    .ka-title h4 {
      font-size: 14px;
      font-weight: 700;
      color: var(--text-main);
    }

    .ka-title p {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .ka-toggle {
      color: var(--text-muted);
      transition: transform var(--transition);
    }

    .ka-body {
      display: none;
      padding: 18px;
      background: #fff;
    }

    .ka-body.show {
      display: block;
    }

    .ka-body .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
    }

    @media (max-width: 600px) {
      .ka-body .form-row {
        grid-template-columns: 1fr;
      }
    }

    .mt-2 {
      margin-top: 14px;
    }

    .mt-3 {
      margin-top: 18px;
    }

    .mt-4 {
      margin-top: 22px;
    }

    .mb-3 {
      margin-bottom: 14px;
    }

    .mb-4 {
      margin-bottom: 22px;
    }

    /* PPEPP PROGRESS BAR */
    .ppepp-progress {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0;
      margin-bottom: 28px;
      flex-wrap: wrap;
    }

    .pp-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
    }

    .pp-step-inner {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
    }

    .pp-step-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #e2e8f0;
      color: var(--text-muted);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      font-weight: 700;
      transition: all var(--transition);
    }

    .pp-step.active .pp-step-circle {
      background: linear-gradient(135deg, var(--primary), var(--primary-light));
      color: #fff;
      box-shadow: 0 4px 14px rgba(26, 35, 126, 0.3);
    }

    .pp-step.done .pp-step-circle {
      background: linear-gradient(135deg, #059669, #10b981);
      color: #fff;
    }

    .pp-step-label {
      font-size: 11.5px;
      font-weight: 600;
      color: var(--text-muted);
    }

    .pp-step.active .pp-step-label {
      color: var(--primary-light);
      font-weight: 700;
    }

    .pp-connector {
      flex: 1;
      height: 2px;
      background: #e2e8f0;
      min-width: 40px;
      max-width: 70px;
    }

    .pp-connector.done {
      background: #10b981;
    }

    /* MODAL STYLES */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.6);
      backdrop-filter: blur(4px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      padding: 20px;
    }

    .modal-overlay.show {
      display: flex !important;
    }

    .modal {
      background: #ffffff;
      border-radius: 16px;
      width: 100%;
      max-width: 520px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      overflow: hidden;
      animation: modalFade 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes modalFade {
      from {
        opacity: 0;
        transform: scale(0.96);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .modal-header {
      padding: 18px 24px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .modal-header h3 {
      font-size: 16px;
      font-weight: 700;
      color: var(--text-main);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 18px;
      cursor: pointer;
      color: var(--text-muted);
      padding: 4px 8px;
      border-radius: 6px;
    }

    .modal-close:hover {
      background: #f1f5f9;
      color: var(--text-main);
    }

    .modal-body {
      padding: 24px;
    }

    .modal-footer {
      padding: 14px 24px;
      background: #f8faff;
      border-top: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 10px;
    }

    .grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
    }

    code {
      background: #f1f5f9;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 12px;
      font-family: 'Courier New', monospace;
      color: var(--text-main);
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.open {
        transform: translateX(0);
      }

      .main-content {
        margin-left: 0;
      }

      #menuToggle {
        display: flex !important;
      }

      .topbar {
        padding: 0 16px;
      }

      .content-area {
        padding: 16px;
      }
    }
  </style>
  <script>
    window.PPEPP_BASE_URL = '<?= BASE_URL ?>';
    window.BASE_URL = '<?= BASE_URL ?>';
    window.BASE_APP = '<?= BASE_URL ?>';

    window.openModal = function (id) {
      var overlay = document.getElementById(id);
      if (overlay) {
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
      }
    };

    window.closeModal = function (id) {
      var overlay = document.getElementById(id);
      if (overlay) {
        overlay.classList.remove('show');
        document.body.style.overflow = '';
      }
    };
  </script>
</head>
<body<?php
$currentUser = $_SESSION['user'] ?? [];
$basePub = BASE_URL;
$basePubPath = parse_url(BASE_URL, PHP_URL_PATH) ?? '/ppepp/public';
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '';
$appRelPath = '/' . ltrim(substr($currentPath, strlen($basePubPath)), '/');

function isActive(string $path, string $current, string $base = ''): bool
{
  $stripped = $base ? substr($current, strlen($base)) : $current;
  return strpos($stripped, $path) === 0 && $path !== '/';
}
?> <!-- Sidebar Overlay (mobile) -->
  <div id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:99;"
    onclick="document.querySelector('.sidebar').classList.remove('open');this.style.display='none';"></div>

  <!-- Sidebar -->
  <aside class="sidebar">
    <!-- Brand Header -->
    <div class="sidebar-header">
      <a href="<?= BASE_URL ?>/ppepp" class="sidebar-brand"
        style="text-decoration:none;color:#ffffff;display:flex;align-items:center;gap:12px;">
        <div class="brand-logo">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="22" height="22">
            <path d="M12 2L2 7l10 5 10-5-10-5z" />
            <path d="M2 17l10 5 10-5" />
            <path d="M2 12l10 5 10-5" />
          </svg>
        </div>
        <div>
          <div class="brand-title"
            style="font-size:15px;font-weight:900;color:#ffffff !important;letter-spacing:-0.3px;line-height:1.2;">PPEPP
            FAKULTAS</div>
          <div class="brand-sub"
            style="font-size:11px;font-weight:600;color:rgba(255,255,255,0.75) !important;margin-top:2px;">Penjaminan
            Mutu Internal</div>
        </div>
      </a>
    </div>

    <!-- User Profile Widget -->
    <?php
    $roleLabel = [
      'kaprodi' => '👑 Ketua Program Studi',
      'dekan' => '🏛️ Dekan / Pimpinan',
      'dosen' => '🧑‍🏫 Dosen / Auditor',
    ];
    $roleColors = [
      'kaprodi' => '#fbbf24',
      'dekan' => '#60a5fa',
      'dosen' => '#34d399',
    ];
    $userRole = $currentUser['role'] ?? 'dosen';
    ?>
    <div class="sidebar-profile"
      style="padding:14px 16px;margin:12px 14px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:14px;">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
        <?php if (!empty($currentUser['avatar'])): ?>
          <img src="<?= htmlspecialchars($currentUser['avatar']) ?>" alt="Avatar"
            style="width:30px;height:30px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.3);flex-shrink:0;">
        <?php else: ?>
          <div
            style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,0.2);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:12px;flex-shrink:0;">
            <?= strtoupper(mb_substr(explode(' ', $currentUser['nama_lengkap'] ?: $currentUser['username'] ?: 'U')[0], 0, 1)) ?>
          </div>
        <?php endif; ?>
        <div>
          <div class="prodi-label"
            style="font-size:11px;font-weight:700;color:<?= $roleColors[$userRole] ?? '#fff' ?>;">
            <?= $roleLabel[$userRole] ?? 'Dosen' ?>
          </div>
          <div class="prodi-name"
            style="font-size:12px;color:#fff;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;">
            <?= htmlspecialchars($currentUser['nama_lengkap'] ?: $currentUser['username']) ?>
          </div>
        </div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-label">Menu Utama</div>
      <ul>
        <li class="nav-item">
          <a href="<?= BASE_URL ?>/dashboard"
            class="nav-link <?= ($appRelPath === '/dashboard' || $appRelPath === '/') ? 'active' : '' ?>">
            <span class="nav-icon">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="14" width="7" height="7" rx="1" />
                <rect x="3" y="14" width="7" height="7" rx="1" />
              </svg>
            </span>
            Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= BASE_URL ?>/pending"
            class="nav-link <?= str_starts_with($appRelPath, '/pending') ? 'active' : '' ?>" style="color:#fca5a5;">
            <span class="nav-icon" style="color:#fca5a5;">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </span>
            📌 Dokumen Belum Dikerjakan
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= BASE_URL ?>/penetapan/kriteria"
            class="nav-link <?= str_starts_with($appRelPath, '/penetapan/kriteria') ? 'active' : '' ?>">
            <span class="nav-icon">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
            </span>
            Kelola Kriteria
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= BASE_URL ?>/users/api-key"
            class="nav-link <?= str_starts_with($appRelPath, '/users/api-key') ? 'active' : '' ?>"
            style="<?= empty($currentUser['gemini_api_key']) ? 'color:#fcd34d;' : '' ?>">
            <span class="nav-icon"><?= empty($currentUser['gemini_api_key']) ? '⚠️' : '🔑' ?></span>
            <?= empty($currentUser['gemini_api_key']) ? 'Configuration' : 'API Key Gemini' ?>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= BASE_URL ?>/laporan"
            class="nav-link <?= str_starts_with($appRelPath, '/laporan') ? 'active' : '' ?>">
            <span class="nav-icon">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path
                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </span>
            Laporan PPEPP
          </a>
        </li>
      </ul>

      <!-- PPEPP Project Navigation -->
      <div class="nav-section-label" style="margin-top:14px;">Siklus PPEPP</div>
      <?php
      // Deteksi apakah sedang di dalam sebuah project ppepp (hanya aktif bila berkunjung ke /ppepp atau /ppepp/{id})
      $isProjActive = ($appRelPath === '/ppepp' || str_starts_with($appRelPath, '/ppepp/') || str_starts_with($appRelPath, '/ppepp'));
      $inPpeppProject = str_starts_with($appRelPath, '/ppepp/') && preg_match('#/ppepp/(\d+)#', $appRelPath, $m);
      $activeProjId = $inPpeppProject ? (int) $m[1] : 0;
      ?>
      <ul>
        <li class="nav-item">
          <a href="<?= BASE_URL ?>/ppepp" class="nav-link <?= $isProjActive ? 'active' : '' ?>"
            style="<?= $isProjActive ? 'background:rgba(245,158,11,0.15);color:#fbbf24;font-weight:700;' : '' ?>">
            <span class="nav-icon">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                <path d="M3 7l9 6 9-6" />
              </svg>
            </span>
            Project PPEPP
          </a>
        </li>
      </ul>

      <?php if ($activeProjId): ?>
        <!-- Sub-menu 5 tahap saat dalam project -->
        <div class="ppepp-steps" style="padding:2px 10px 4px;">
          <?php
          $ppSteps = [
            ['P', 'Penetapan', 'penetapan?project_id=' . $activeProjId],
            ['P', 'Pelaksanaan', 'pelaksanaan?project_id=' . $activeProjId],
            ['E', 'Evaluasi', 'evaluasi?project_id=' . $activeProjId],
            ['P', 'Pengendalian', 'pengendalian?project_id=' . $activeProjId],
            ['P', 'Peningkatan', 'peningkatan?project_id=' . $activeProjId],
          ];
          foreach ($ppSteps as [$letter, $nama, $targetRoute]):
            $isActive = str_contains($appRelPath, '/' . explode('?', $targetRoute)[0]);
            ?>
            <a href="<?= BASE_URL ?>/<?= $targetRoute ?>" class="ppepp-step <?= $isActive ? 'active' : '' ?>">
              <div class="step-num"><?= $letter ?></div>
              <div>
                <div style="font-size:13px;font-weight:600;color:#fff;"><?= $nama ?></div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (str_starts_with($appRelPath, '/pelaksanaan')): ?>
        <div class="nav-section-label" style="margin-top:14px;">Menu Pelaksanaan</div>
        <ul>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/pelaksanaan"
              class="nav-link <?= ($appRelPath === '/pelaksanaan' || $appRelPath === '/pelaksanaan/') ? 'active' : '' ?>">
              <span class="nav-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                  <polyline points="14 2 14 8 20 8" />
                </svg>
              </span>
              Daftar Pelaksanaan
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/pelaksanaan/create"
              class="nav-link <?= str_starts_with($appRelPath, '/pelaksanaan/create') ? 'active' : '' ?>">
              <span class="nav-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="12" y1="8" x2="12" y2="16" />
                  <line x1="8" y1="12" x2="16" y2="12" />
                </svg>
              </span>
              Buat Pelaksanaan
            </a>
          </li>
        </ul>
      <?php endif; ?>

      <?php if (str_starts_with($appRelPath, '/peningkatan')): ?>
        <div class="nav-section-label" style="margin-top:14px;">Menu Peningkatan</div>
        <ul>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/peningkatan"
              class="nav-link <?= ($appRelPath === '/peningkatan' || $appRelPath === '/peningkatan/') ? 'active' : '' ?>">
              <span class="nav-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                  <polyline points="17 6 23 6 23 12" />
                </svg>
              </span>
              Daftar Peningkatan
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/peningkatan/create"
              class="nav-link <?= str_starts_with($appRelPath, '/peningkatan/create') ? 'active' : '' ?>">
              <span class="nav-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="12" y1="8" x2="12" y2="16" />
                  <line x1="8" y1="12" x2="16" y2="12" />
                </svg>
              </span>
              Buat Peningkatan
            </a>
          </li>
        </ul>
      <?php endif; ?>
      <?php if (str_starts_with($appRelPath, '/pengendalian')): ?>
        <div class="nav-section-label" style="margin-top:14px;">Menu Pengendalian</div>
        <ul>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/pengendalian"
              class="nav-link <?= ($appRelPath === '/pengendalian' || $appRelPath === '/pengendalian/') ? 'active' : '' ?>">
              <span class="nav-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M12 20h9" />
                  <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                </svg>
              </span>
              Daftar Pengendalian
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/pengendalian/create"
              class="nav-link <?= str_starts_with($appRelPath, '/pengendalian/create') ? 'active' : '' ?>">
              <span class="nav-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="12" y1="8" x2="12" y2="16" />
                  <line x1="8" y1="12" x2="16" y2="12" />
                </svg>
              </span>
              Buat Pengendalian
            </a>
          </li>
        </ul>
      <?php endif; ?>
      <?php if (str_starts_with($appRelPath, '/evaluasi')): ?>
        <div class="nav-section-label" style="margin-top:14px;">Menu Evaluasi</div>
        <ul>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/evaluasi"
              class="nav-link <?= ($appRelPath === '/evaluasi' || $appRelPath === '/evaluasi/') ? 'active' : '' ?>">
              <span class="nav-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M9 11l3 3L22 4" />
                  <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                </svg>
              </span>
              Daftar Evaluasi
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/evaluasi/create"
              class="nav-link <?= str_starts_with($appRelPath, '/evaluasi/create') ? 'active' : '' ?>">
              <span class="nav-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="12" y1="8" x2="12" y2="16" />
                  <line x1="8" y1="12" x2="16" y2="12" />
                </svg>
              </span>
              Buat Evaluasi
            </a>
          </li>
        </ul>
      <?php endif; ?>
      <?php if (str_starts_with($appRelPath, '/penetapan')): ?>
        <div class="nav-section-label" style="margin-top:14px;">Menu Penetapan</div>
        <ul>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/penetapan"
              class="nav-link <?= ($appRelPath === '/penetapan' || $appRelPath === '/penetapan/') ? 'active' : '' ?>">
              <span class="nav-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M9 11l3 3L22 4" />
                  <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                </svg>
              </span>
              Daftar Penetapan
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/penetapan/kriteria"
              class="nav-link <?= str_starts_with($appRelPath, '/penetapan/kriteria') ? 'active' : '' ?>">
              <span class="nav-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
              </span>
              Kelola Kriteria
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/penetapan/referensi"
              class="nav-link <?= str_starts_with($appRelPath, '/penetapan/referensi') ? 'active' : '' ?>">
              <span class="nav-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                </svg>
              </span>
              Referensi & AI
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/penetapan/create"
              class="nav-link <?= str_starts_with($appRelPath, '/penetapan/create') ? 'active' : '' ?>">
              <span class="nav-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="12" y1="8" x2="12" y2="16" />
                  <line x1="8" y1="12" x2="16" y2="12" />
                </svg>
              </span>
              Buat Penetapan
            </a>
          </li>
        </ul>
      <?php endif; ?>
    </nav>

    <!-- Footer Sidebar -->
    <div class="sidebar-footer" style="display:flex;align-items:center;gap:8px;">
      <a href="<?= BASE_URL ?>/auth/logout" class="logout-btn" style="flex:1;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
          <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
          <polyline points="16 17 21 12 16 7" />
          <line x1="21" y1="12" x2="9" y2="12" />
        </svg>
        Keluar
      </a>

      <?php if (in_array($currentUser['role'] ?? '', ['kaprodi', 'dekan'])):
        $isAdminActive = ($appRelPath === '/admin' || str_starts_with($appRelPath, '/admin') || (str_starts_with($appRelPath, '/users') && !str_starts_with($appRelPath, '/users/api-key')) || str_starts_with($appRelPath, '/prodi'));
        ?>
        <a href="<?= BASE_URL ?>/admin" class="admin-gear-btn" title="Halaman Administrasi (Pengguna & Program Studi)"
          style="display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:var(--radius-sm);color:<?= $isAdminActive ? '#fff' : 'rgba(255,255,255,0.7)' ?>;background:<?= $isAdminActive ? 'linear-gradient(135deg,#4f46e5,#6366f1)' : 'rgba(255,255,255,0.08)' ?>;border:1px solid <?= $isAdminActive ? '#6366f1' : 'rgba(255,255,255,0.15)' ?>;transition:all 0.2s;text-decoration:none;flex-shrink:0;box-shadow:<?= $isAdminActive ? '0 2px 8px rgba(99,102,241,0.4)' : 'none' ?>;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="17" height="17">
            <circle cx="12" cy="12" r="3" />
            <path
              d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2 2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" />
          </svg>
        </a>
      <?php endif; ?>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <div class="main-content">

    <!-- Top Bar -->
    <header class="topbar">
      <div class="topbar-left">
        <button id="menuToggle"
          style="display:none;background:none;border:none;cursor:pointer;padding:6px;border-radius:8px;color:var(--text-muted);"
          onclick="document.querySelector('.sidebar').classList.toggle('open');document.getElementById('sidebarOverlay').style.display='block';">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="18" x2="21" y2="18" />
          </svg>
        </button>
        <div>
          <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
          <?php if (!empty($breadcrumbs)): ?>
            <nav class="breadcrumb">
              <?php foreach ($breadcrumbs as $i => $crumb): ?>
                <?php if ($i > 0): ?><span class="sep">›</span><?php endif; ?>
                <?php if (!empty($crumb['url'])): ?>
                  <a href="<?= $crumb['url'] ?>"><?= htmlspecialchars($crumb['label']) ?></a>
                <?php else: ?>
                  <span><?= htmlspecialchars($crumb['label']) ?></span>
                <?php endif; ?>
              <?php endforeach; ?>
            </nav>
          <?php endif; ?>
        </div>
      </div>
      <div class="topbar-right">
        <?php if (empty($currentUser['gemini_api_key'])): ?>
          <a href="<?= BASE_URL ?>/users/api-key"
            style="display:inline-flex;align-items:center;gap:6px;background:#fff7ed;border:1px solid #fed7aa;color:#c2410c;font-size:12px;font-weight:700;padding:6px 12px;border-radius:8px;text-decoration:none;">
            ⚠️ Set API Key
          </a>
        <?php endif; ?>
        <div class="user-badge">
          <?php if (!empty($currentUser['avatar_url'])): ?>
            <img src="<?= htmlspecialchars($currentUser['avatar_url']) ?>" alt="" width="36" height="36"
              style="border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;flex-shrink:0;">
          <?php else: ?>
            <div class="user-avatar">
              <?= strtoupper(mb_substr(explode(' ', $currentUser['nama_lengkap'] ?: $currentUser['username'] ?: 'U')[0], 0, 1)) ?>
            </div>
          <?php endif; ?>
          <div>
            <div class="user-name">
              <?= htmlspecialchars($currentUser['nama_prodi'] ?: $currentUser['nama_lengkap'] ?: '') ?>
            </div>
            <div class="user-role">
              <?= ['dekan' => '👑 Dekan', 'kaprodi' => '🎓 Kaprodi', 'dosen' => '🧑‍🏫 Dosen'][$currentUser['role'] ?? 'dosen'] ?? 'Dosen' ?>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Content -->
    <main class="content-area">
      <?php if (isset($flash) && $flash): ?>
        <div
          class="alert alert-<?= $flash['type'] === 'error' ? 'error' : ($flash['type'] === 'warning' ? 'warning' : ($flash['type'] === 'info' ? 'info' : 'success')) ?>">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <?php if ($flash['type'] === 'success'): ?>
              <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
              <polyline points="22 4 12 14.01 9 11.01" />
            <?php elseif ($flash['type'] === 'error'): ?>
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            <?php elseif ($flash['type'] === 'warning'): ?>
              <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
              <line x1="12" y1="9" x2="12" y2="13" />
              <line x1="12" y1="17" x2="12.01" y2="17" />
            <?php else: ?>
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="16" x2="12" y2="12" />
              <line x1="12" y1="8" x2="12.01" y2="8" />
            <?php endif; ?>
          </svg>
          <?= htmlspecialchars($flash['message']) ?>
        </div>
      <?php endif; ?>

      <?= $content ?>
    </main>
  </div>

  <!-- JS inline - modal, accordion, dropdown handlers -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // MODAL
      document.querySelectorAll('[data-modal-open]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var id = btn.getAttribute('data-modal-open');
          var modal = document.getElementById(id);
          if (modal) { modal.classList.add('open'); }
        });
      });
      document.querySelectorAll('[data-modal-close]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var id = btn.getAttribute('data-modal-close');
          var modal = document.getElementById(id);
          if (modal) { modal.classList.remove('open'); }
        });
      });
      document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
          if (e.target === overlay) overlay.classList.remove('open');
        });
      });

      // ACCORDION
      document.querySelectorAll('.ka-header').forEach(function (header) {
        if (header.hasAttribute('onclick')) return;
        header.addEventListener('click', function () {
          var item = header.closest('.ka-item');
          if (!item) return;
          var body = item.querySelector('.ka-body');
          if (!body) return;
          var icon = header.querySelector('.ka-toggle, svg[id^="toggle_"], svg');
          var isOpen = body.classList.contains('show') || (body.style.display !== 'none' && body.style.display !== '');
          if (!isOpen) {
            body.classList.add('show');
            body.style.display = 'block';
            if (icon) icon.style.transform = 'rotate(180deg)';
          } else {
            body.classList.remove('show');
            body.style.display = 'none';
            if (icon) icon.style.transform = 'rotate(0deg)';
          }
        });
      });

      // EDIT KRITERIA
      document.querySelectorAll('[data-edit-kriteria]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var modal = document.getElementById('modalEditKriteria');
          if (!modal) return;
          modal.querySelector('input[name="id"]').value = btn.dataset.id;
          modal.querySelector('#editKode').value = btn.dataset.kode;
          modal.querySelector('#editNama').value = btn.dataset.nama;
          modal.querySelector('#editDeskripsi').value = btn.dataset.deskripsi || '';
          modal.querySelector('#editUrutan').value = btn.dataset.urutan || 0;
          modal.classList.add('open');
        });
      });

      // DELETE KRITERIA
      document.querySelectorAll('[data-delete-kriteria]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var modal = document.getElementById('modalDeleteKriteria');
          if (!modal) return;
          modal.querySelector('#deleteKriteriaId').value = btn.dataset.deleteKriteria;
          modal.querySelector('#deleteKriteriaName').textContent = btn.dataset.nama;
          modal.classList.add('open');
        });
      });

      // DELETE REF
      document.querySelectorAll('[data-delete-ref]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var modal = document.getElementById('modalDeleteRef');
          if (!modal) return;
          modal.querySelector('#deleteRefId').value = btn.dataset.deleteRef;
          modal.querySelector('#deleteRefName').textContent = btn.dataset.nama;
          modal.classList.add('open');
        });
      });

      // ANALYZE AI
      document.querySelectorAll('[data-analyze-btn]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var refId = btn.dataset.analyzeBtn;
          var panel = document.getElementById('aiPanel_' + refId);
          var loading = document.getElementById('aiLoading_' + refId);
          var result = document.getElementById('aiResult_' + refId);
          if (!panel) return;
          panel.style.display = 'block';
          if (loading) { loading.style.display = 'flex'; }
          if (result) { result.style.display = 'none'; }

          var kriteriaIds = [];
          document.querySelectorAll('input[name="kriteria_ids[]"]:checked').forEach(function (cb) {
            kriteriaIds.push(cb.value);
          });
          var pertanyaan = document.getElementById('aiPertanyaan') ? document.getElementById('aiPertanyaan').value : '';

          fetch(window.PPEPP_BASE_URL + '/penetapan/referensi/analyze', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'referensi_id=' + refId + '&kriteria_ids%5B%5D=' + kriteriaIds.join('&kriteria_ids%5B%5D=') + '&pertanyaan=' + encodeURIComponent(pertanyaan)
          })
            .then(function (r) { return r.json(); })
            .then(function (data) {
              if (loading) loading.style.display = 'none';
              if (result) {
                result.style.display = 'block';
                result.innerHTML = data.success ? (data.analisis || '').replace(/\n/g, '<br>') : '<span style="color:#f87171;">' + (data.message || 'Error') + '</span>';
              }
            })
            .catch(function () {
              if (loading) loading.style.display = 'none';
              if (result) { result.style.display = 'block'; result.innerHTML = '<span style="color:#f87171;">Gagal menghubungi server.</span>'; }
            });
        });
      });

      // SELECT ALL KRITERIA
      var btnAll = document.getElementById('btnSelectAllKriteria');
      var btnNone = document.getElementById('btnSelectNoneKriteria');
      if (btnAll) btnAll.addEventListener('click', function () {
        document.querySelectorAll('input[name="kriteria_ids[]"]').forEach(function (cb) { cb.checked = true; });
      });
      if (btnNone) btnNone.addEventListener('click', function () {
        document.querySelectorAll('input[name="kriteria_ids[]"]').forEach(function (cb) { cb.checked = false; });
      });

      // ============================================================
      // GLOBAL MODAL SYSTEM & KRITERIA HANDLERS
      // ============================================================
      function openModal(id) {
        var overlay = document.getElementById(id);
        if (overlay) {
          overlay.classList.add('show');
          document.body.style.overflow = 'hidden';
        }
      }
      function closeModal(id) {
        var overlay = document.getElementById(id);
        if (overlay) {
          overlay.classList.remove('show');
          document.body.style.overflow = '';
        }
      }
      document.querySelectorAll('[data-modal-open]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          openModal(btn.dataset.modalOpen);
        });
      });
      document.querySelectorAll('[data-modal-close]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          closeModal(btn.dataset.modalClose);
        });
      });
      document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
          if (e.target === overlay) closeModal(overlay.id);
        });
      });
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
          document.querySelectorAll('.modal-overlay.show').forEach(function (m) { closeModal(m.id); });
        }
      });
      window.openModal = openModal;
      window.closeModal = closeModal;

      // Edit Kriteria Modal
      document.querySelectorAll('[data-edit-kriteria]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var data = btn.dataset;
          var form = document.getElementById('formEditKriteria');
          if (!form) return;
          form.querySelector('[name="id"]').value = data.id;
          form.querySelector('[name="kode"]').value = data.kode;
          form.querySelector('[name="nama"]').value = data.nama;
          form.querySelector('[name="deskripsi"]').value = data.deskripsi || '';
          form.querySelector('[name="urutan"]').value = data.urutan || 0;
          openModal('modalEditKriteria');
        });
      });

      // Delete/Nonaktifkan Kriteria Modal
      document.querySelectorAll('[data-delete-kriteria]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var id = btn.dataset.deleteKriteria;
          var nama = btn.dataset.nama;
          var elId = document.getElementById('deleteKriteriaId');
          var elNm = document.getElementById('deleteKriteriaName');
          if (elId) elId.value = id;
          if (elNm) elNm.textContent = nama;
          openModal('modalDeleteKriteria');
        });
      });

      // Global AI Warning Helper
      window.USER_HAS_API_KEY = <?= !empty($_SESSION['user']['gemini_api_key']) ? 'true' : 'false' ?>;

      window.showAiWarningModal = function (msg, errorType) {
        var modal = document.getElementById('globalAiWarningModal');
        var header = document.getElementById('aiModalHeader');
        var title = document.getElementById('aiModalTitle');
        var icon = document.getElementById('aiModalIcon');
        var message = document.getElementById('aiModalMessage');
        var tips = document.getElementById('aiModalTips');
        var btnAction = document.getElementById('aiModalBtnAction');

        if (!modal) {
          alert((msg || 'Peringatan AI') + '\n\nSilakan isi API Key Anda di Pengaturan.');
          return;
        }

        var type = errorType || '';
        var str = (msg || '').toLowerCase();

        if (!type) {
          if (str.indexOf('kuota') !== -1 || str.indexOf('quota') !== -1 || str.indexOf('429') !== -1 || str.indexOf('rate limit') !== -1 || str.indexOf('limit reached') !== -1 || str.indexOf('resource_exhausted') !== -1) {
            type = 'quota_exceeded';
          } else if (str.indexOf('belum terpasang') !== -1 || str.indexOf('belum diisi') !== -1 || str.indexOf('api key') !== -1 || str.indexOf('invalid') !== -1) {
            type = 'missing_key';
          }
        }

        if (type === 'quota_exceeded') {
          header.style.background = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
          icon.innerHTML = '⚠️';
          title.textContent = 'Kuota API Key Gemini Habis / Rate Limit Exceeded';
          message.innerHTML = msg || 'API Key Gemini Anda telah mencapai batas kuota gratis atau rate limit panggilan dari Google (HTTP 429 / Resource Exhausted).';
          tips.innerHTML = '⏳ <strong>Solusi cepat:</strong><br>1. Tunggu 1–2 menit sebelum mencoba lagi.<br>2. Ganti model AI ke <code>gemini-2.0-flash-lite</code> di menu Pengaturan.<br>3. Gunakan API Key Gemini yang lain.';
          btnAction.textContent = '⚙️ Buka Pengaturan Model AI';
          btnAction.href = '<?= BASE_URL ?>/setting';
        } else if (type === 'missing_key' || type === 'invalid_key') {
          header.style.background = 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)';
          icon.innerHTML = '🔑';
          title.textContent = (type === 'invalid_key') ? 'API Key Gemini Tidak Valid' : 'API Key Gemini Belum Terpasang';
          message.innerHTML = msg || 'Fitur AI Generate memerlukan API Key Gemini milik Anda yang aktif.';
          tips.innerHTML = '💡 <strong>Petunjuk:</strong> Anda dapat membuat Gemini API Key gratis di <strong>Google AI Studio</strong> (aistudio.google.com) lalu memasukkannya di Pengaturan.';
          btnAction.textContent = '⚙️ Buka Pengaturan API Key';
          btnAction.href = '<?= BASE_URL ?>/setting';
        } else {
          header.style.background = 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)';
          icon.innerHTML = '🤖';
          title.textContent = 'Peringatan Fitur AI';
          message.innerHTML = msg || 'Terjadi hambatan saat menghubungi layanan Google Gemini AI.';
          tips.innerHTML = ' Silakan periksa koneksi internet atau coba beberapa saat lagi.';
          btnAction.textContent = '⚙️ Buka Pengaturan';
          btnAction.href = '<?= BASE_URL ?>/setting';
        }

        if (window.openModal) {
          window.openModal('globalAiWarningModal');
        }
      };
    });
  </script>

  <!-- GLOBAL AI WARNING MODAL -->
  <div class="modal-overlay" id="globalAiWarningModal" style="z-index:99999;">
    <div class="modal-card"
      style="max-width:500px;border-radius:18px;box-shadow:0 24px 48px rgba(0,0,0,0.3);overflow:hidden;border:none;padding:0;">
      <div id="aiModalHeader"
        style="background:linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);color:#fff;padding:22px 24px;display:flex;align-items:center;gap:14px;">
        <div id="aiModalIcon"
          style="width:44px;height:44px;background:rgba(255,255,255,0.22);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">
          🔑
        </div>
        <div>
          <h3 id="aiModalTitle" style="font-size:17px;font-weight:800;margin:0 0 2px;color:#fff;letter-spacing:-0.2px;">
            API Key Gemini Belum Terpasang</h3>
          <p style="font-size:12px;margin:0;opacity:0.9;color:#fff;">Peringatan Fitur AI PPEPP</p>
        </div>
      </div>
      <div class="modal-body" style="padding:22px 24px;background:#fff;">
        <div id="aiModalMessage"
          style="font-size:13.5px;color:#334155;line-height:1.65;margin-bottom:18px;font-weight:500;">
          API Key Gemini belum diisi. Silakan masukkan API Key Gemini Anda di menu Pengaturan.
        </div>
        <div id="aiModalTips"
          style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;font-size:12.5px;color:#475569;margin-bottom:22px;line-height:1.6;">
          💡 <strong>Petunjuk:</strong> Anda dapat memperoleh Gemini API Key secara gratis dari Google AI Studio
          (aistudio.google.com).
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;align-items:center;">
          <button type="button" class="btn btn-outline" data-modal-close="globalAiWarningModal"
            style="padding:9px 18px;font-weight:600;border-radius:10px;">
            Tutup
          </button>
          <a href="<?= BASE_URL ?>/setting" id="aiModalBtnAction" class="btn btn-primary"
            style="padding:9px 20px;font-weight:700;border-radius:10px;display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#2563eb,#1d4ed8);border:none;color:#fff;text-decoration:none;">
            ⚙️ Buka Pengaturan API Key
          </a>
        </div>
      </div>
    </div>
  </div>

  </body>

</html>