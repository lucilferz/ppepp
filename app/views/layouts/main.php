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
    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      /* Brand & Core Palette (SIPMI Standards) */
      --primary: #173B67;
      --primary-hover: #112D50;
      --primary-light: #2563EB;
      --primary-subtle: #F0F5FA;
      
      --bg: #F7F8FA;
      --card: #FFFFFF;
      --surface: #FFFFFF;
      
      --text-main: #1F2937;
      --text-muted: #64748B;
      --text-light: #94A3B8;
      
      --border: #E2E8F0;
      --border-subtle: #F1F5F9;
      
      /* Semantic Status Tokens */
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

      /* Sidebar & Layout Dimensions */
      --sidebar-bg: #112338;
      --sidebar-border: rgba(255, 255, 255, 0.08);
      --sidebar-width: 256px;
      --header-height: 58px;

      /* Radii (6-10px modern standard) */
      --radius-sm: 6px;
      --radius: 8px;
      --radius-lg: 10px;

      /* Subtle Enterprise Shadows */
      --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.05);
      --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.06), 0 4px 6px -4px rgba(0, 0, 0, 0.03);
      --transition: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--bg);
      color: var(--text-main);
      font-size: 13.5px;
      line-height: 1.5;
      min-height: 100vh;
      display: flex;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    a {
      color: var(--primary);
      text-decoration: none;
      transition: color var(--transition);
    }

    a:hover {
      color: var(--primary-hover);
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
      border-right: 1px solid var(--sidebar-border);
    }

    .sidebar-header {
      padding: 18px 20px 14px;
      border-bottom: 1px solid var(--sidebar-border);
    }

    .sidebar-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
    }

    .brand-logo {
      width: 36px;
      height: 36px;
      background: #173B67;
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      flex-shrink: 0;
      box-shadow: var(--shadow-xs);
    }

    .brand-logo svg {
      width: 20px;
      height: 20px;
      color: #ffffff;
      stroke: #ffffff;
      fill: none;
    }

    .brand-title {
      font-size: 14.5px;
      font-weight: 700;
      color: #ffffff !important;
      letter-spacing: -0.01em;
      line-height: 1.2;
    }

    .brand-sub {
      font-size: 11px;
      font-weight: 500;
      color: rgba(255, 255, 255, 0.65) !important;
      margin-top: 2px;
    }

    .sidebar-profile {
      padding: 10px 12px;
      margin: 12px 14px 6px;
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid var(--sidebar-border);
      border-radius: var(--radius-sm);
    }

    .sidebar-nav {
      flex: 1;
      overflow-y: auto;
      padding: 6px 0 16px;
      scrollbar-width: thin;
      scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
    }

    .sidebar-nav::-webkit-scrollbar {
      width: 4px;
    }
    .sidebar-nav::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 4px;
    }

    .nav-section-label {
      font-size: 10.5px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: rgba(255, 255, 255, 0.45);
      padding: 14px 20px 6px;
    }

    .nav-item {
      padding: 2px 10px;
    }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 12px;
      border-radius: var(--radius-sm);
      color: rgba(255, 255, 255, 0.72);
      font-size: 13px;
      font-weight: 500;
      transition: all var(--transition);
      cursor: pointer;
    }

    .nav-link:hover {
      background: rgba(255, 255, 255, 0.06);
      color: #ffffff;
    }

    .nav-link.active {
      background: rgba(255, 255, 255, 0.12);
      color: #ffffff;
      font-weight: 600;
    }

    .nav-icon {
      width: 17px;
      height: 17px;
      flex-shrink: 0;
      opacity: 0.85;
    }

    .nav-link:hover .nav-icon,
    .nav-link.active .nav-icon {
      opacity: 1;
    }

    .nav-icon svg {
      width: 17px;
      height: 17px;
    }

    /* PPEPP Steps (in Sidebar) */
    .ppepp-steps {
      padding: 4px 10px 4px;
      display: flex;
      flex-direction: column;
      gap: 3px;
    }

    .ppepp-step {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 7px 12px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all var(--transition);
      text-decoration: none;
      color: rgba(255, 255, 255, 0.75);
    }

    .ppepp-step:hover {
      background: rgba(255, 255, 255, 0.06);
      color: #ffffff;
    }

    .ppepp-step.active {
      background: rgba(255, 255, 255, 0.14);
      color: #ffffff;
      font-weight: 600;
    }

    .step-num {
      width: 24px;
      height: 24px;
      background: rgba(255, 255, 255, 0.1);
      color: rgba(255, 255, 255, 0.85);
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: 700;
      flex-shrink: 0;
      transition: all var(--transition);
    }

    .ppepp-step.active .step-num {
      background: #2563EB;
      color: #ffffff;
    }

    .ppepp-step.disabled-nav {
      opacity: 0.45;
      pointer-events: none;
    }

    .sidebar-footer {
      padding: 12px 14px;
      border-top: 1px solid var(--sidebar-border);
      background: rgba(0, 0, 0, 0.1);
    }

    .logout-btn {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 12px;
      border-radius: var(--radius-sm);
      color: rgba(255, 255, 255, 0.65);
      font-size: 12.5px;
      font-weight: 500;
      transition: all var(--transition);
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid var(--sidebar-border);
    }

    .logout-btn:hover {
      background: rgba(220, 38, 38, 0.15);
      border-color: rgba(220, 38, 38, 0.3);
      color: #FCA5A5;
    }

    .logout-btn svg {
      width: 15px;
      height: 15px;
    }

    /* ======= MAIN CONTENT ======= */
    .main-content {
      flex: 1;
      margin-left: var(--sidebar-width);
      min-width: 0;
      display: flex;
      flex-direction: column;
      background: var(--bg);
    }

    /* TOP BAR */
    .topbar {
      height: var(--header-height);
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 28px;
      position: sticky;
      top: 0;
      z-index: 50;
      box-shadow: var(--shadow-xs);
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .page-title {
      font-size: 16px;
      font-weight: 700;
      color: var(--text-main);
      letter-spacing: -0.01em;
    }

    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 6px;
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
      color: var(--primary);
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
      width: 32px;
      height: 32px;
      background: var(--primary);
      color: #ffffff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 700;
      flex-shrink: 0;
      box-shadow: var(--shadow-xs);
    }

    .user-name {
      font-size: 13px;
      font-weight: 600;
      color: var(--text-main);
      line-height: 1.3;
    }

    .user-role {
      font-size: 11px;
      color: var(--text-muted);
      font-weight: 500;
    }

    /* CONTENT AREA */
    .content-area {
      flex: 1;
      padding: 24px 28px;
      overflow-y: auto;
    }

    /* ======= CARDS ======= */
    .card {
      background: var(--card);
      border-radius: var(--radius);
      border: 1px solid var(--border);
      box-shadow: var(--shadow-xs);
      overflow: hidden;
      margin-bottom: 20px;
      transition: border-color var(--transition);
    }

    .card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 20px;
      border-bottom: 1px solid var(--border);
      background: #FFFFFF;
      gap: 12px;
    }

    .card-title {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      font-weight: 700;
      color: var(--text-main);
      letter-spacing: -0.01em;
    }

    .card-title svg {
      width: 17px;
      height: 17px;
      color: var(--primary);
      stroke: currentColor;
      fill: none;
      flex-shrink: 0;
    }

    .card-body {
      padding: 20px;
    }

    .card-footer {
      padding: 12px 20px;
      border-top: 1px solid var(--border);
      background: #F8FAFC;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    /* ======= STATS ======= */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 14px;
      margin-bottom: 20px;
    }

    .stat-card {
      background: var(--card);
      border-radius: var(--radius);
      border: 1px solid var(--border);
      padding: 16px 18px;
      display: flex;
      align-items: center;
      gap: 14px;
      box-shadow: var(--shadow-xs);
      transition: border-color var(--transition);
    }

    .stat-card:hover {
      border-color: #CBD5E1;
    }

    .stat-icon {
      width: 40px;
      height: 40px;
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .stat-icon svg {
      width: 20px;
      height: 20px;
      fill: none;
      stroke: currentColor;
    }

    .stat-icon.blue {
      background: #EFF6FF;
      color: #2563EB;
    }

    .stat-icon.green {
      background: #ECFDF5;
      color: #23845B;
    }

    .stat-icon.gold {
      background: #FFFBEB;
      color: #B7791F;
    }

    .stat-icon.red {
      background: #FEF2F2;
      color: #B42318;
    }

    .stat-icon.purple {
      background: #F5F3FF;
      color: #6D28D9;
    }

    .stat-value {
      font-size: 22px;
      font-weight: 700;
      color: var(--text-main);
      letter-spacing: -0.02em;
      line-height: 1.1;
    }

    .stat-label {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 4px;
      font-weight: 500;
    }

    /* ======= FORM ======= */
    .form-group {
      margin-bottom: 16px;
    }

    .form-group label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-main);
      margin-bottom: 6px;
    }

    .form-control {
      width: 100%;
      padding: 8px 12px;
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
      padding-left: 36px;
    }

    .grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    @media (max-width: 640px) {
      .grid-2 {
        grid-template-columns: 1fr;
      }
    }

    /* ======= BUTTONS ======= */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      padding: 8px 14px;
      border-radius: var(--radius-sm);
      font-size: 13px;
      font-weight: 600;
      font-family: inherit;
      cursor: pointer;
      border: 1px solid transparent;
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

    .btn-outline {
      background: #FFFFFF;
      color: var(--text-main);
      border-color: var(--border);
      box-shadow: var(--shadow-xs);
    }

    .btn-outline:hover {
      background: #F8FAFC;
      border-color: #CBD5E1;
      color: var(--text-main);
    }

    .btn-success {
      background: var(--success);
      color: #FFFFFF;
      border-color: var(--success);
    }

    .btn-success:hover {
      filter: brightness(0.95);
      color: #FFFFFF;
    }

    .btn-danger {
      background: var(--danger);
      color: #FFFFFF;
      border-color: var(--danger);
    }

    .btn-danger:hover {
      filter: brightness(0.95);
      color: #FFFFFF;
    }

    .btn-accent {
      background: var(--warning);
      color: #FFFFFF;
      border-color: var(--warning);
    }

    .btn-accent:hover {
      filter: brightness(0.95);
      color: #FFFFFF;
    }

    .btn-lg {
      padding: 10px 18px;
      font-size: 14px;
    }

    .btn-sm {
      padding: 5px 10px;
      font-size: 12px;
      border-radius: var(--radius-sm);
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
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .page-header h2 {
      font-size: 18px;
      font-weight: 700;
      color: var(--text-main);
      letter-spacing: -0.01em;
    }

    .page-desc {
      color: var(--text-muted);
      font-size: 13px;
      margin-top: 3px;
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
      padding: 12px 14px;
      border-radius: var(--radius-sm);
      margin-bottom: 20px;
      font-size: 13px;
      line-height: 1.5;
      border: 1px solid transparent;
    }

    .alert svg {
      width: 16px;
      height: 16px;
      flex-shrink: 0;
      margin-top: 2px;
    }

    .alert-success {
      background: var(--success-bg);
      border-color: var(--success-border);
      color: #166534;
    }

    .alert-error {
      background: var(--danger-bg);
      border-color: var(--danger-border);
      color: var(--danger);
    }

    .alert-info {
      background: var(--info-bg);
      border-color: var(--info-border);
      color: var(--info);
    }

    .alert-warning {
      background: var(--warning-bg);
      border-color: var(--warning-border);
      color: var(--warning);
    }

    /* BADGES (shadcn / Jira Style) */
    .badge {
      display: inline-flex;
      align-items: center;
      padding: 2.5px 8px;
      border-radius: 4px;
      font-size: 11.5px;
      font-weight: 600;
      line-height: 1.3;
      border: 1px solid transparent;
    }

    .badge-primary {
      background: var(--primary-subtle);
      color: var(--primary);
      border-color: #D3E0F0;
    }

    .badge-success {
      background: var(--success-bg);
      color: #166534;
      border-color: var(--success-border);
    }

    .badge-warning {
      background: var(--warning-bg);
      color: #854D0E;
      border-color: var(--warning-border);
    }

    .badge-info {
      background: var(--info-bg);
      color: #1E40AF;
      border-color: var(--info-border);
    }

    .badge-gray {
      background: #F1F5F9;
      color: #475569;
      border-color: #E2E8F0;
    }

    .badge-dot::before {
      content: '•';
      margin-right: 4px;
      font-size: 13px;
      line-height: 0;
    }

    /* TABLE (Clean Enterprise Style) */
    .table-wrap {
      overflow-x: auto;
      border-radius: var(--radius);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }

    thead th {
      background: #F8FAFC;
      font-size: 11.5px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: #475569;
      padding: 10px 14px;
      text-align: left;
      border-bottom: 1px solid var(--border);
      white-space: nowrap;
    }

    tbody tr {
      border-bottom: 1px solid #F1F5F9;
      transition: background var(--transition);
    }

    tbody tr:hover {
      background: #F8FAFC;
    }

    tbody td {
      padding: 11px 14px;
      color: var(--text-main);
      vertical-align: middle;
    }

    .td-muted {
      color: var(--text-muted);
    }

    /* EMPTY STATE */
    .empty-state {
      text-align: center;
      padding: 48px 20px;
    }

    .empty-icon {
      width: 52px;
      height: 52px;
      background: #F1F5F9;
      border-radius: var(--radius);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 14px;
    }

    .empty-icon svg {
      width: 24px;
      height: 24px;
      color: var(--text-muted);
      stroke: currentColor;
      fill: none;
    }

    .empty-state h3 {
      font-size: 15px;
      font-weight: 700;
      color: var(--text-main);
      margin-bottom: 6px;
    }

    .empty-state p {
      font-size: 13px;
      color: var(--text-muted);
      margin-bottom: 18px;
      line-height: 1.5;
    }

    /* MODALS */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.45);
      z-index: 9999;
      align-items: center;
      justify-content: center;
      padding: 20px;
      backdrop-filter: blur(2px);
    }

    .modal-overlay.open,
    .modal-overlay.show {
      display: flex !important;
    }

    .modal {
      background: #FFFFFF;
      border-radius: var(--radius-lg);
      border: 1px solid var(--border);
      width: 100%;
      max-width: 520px;
      max-height: 90vh;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      box-shadow: var(--shadow-lg);
      animation: modalFade 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes modalFade {
      from {
        opacity: 0;
        transform: scale(0.97);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .modal-lg {
      max-width: 680px;
    }

    .modal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      flex-shrink: 0;
      background: #FFFFFF;
    }

    .modal-header h3 {
      font-size: 15px;
      font-weight: 700;
      color: var(--text-main);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .modal-header h3 svg {
      flex-shrink: 0;
    }

    .modal-close {
      background: none;
      border: none;
      cursor: pointer;
      color: var(--text-muted);
      width: 28px;
      height: 28px;
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      transition: all var(--transition);
    }

    .modal-close:hover {
      background: #F1F5F9;
      color: var(--text-main);
    }

    .modal-body {
      padding: 20px;
      overflow-y: auto;
      flex: 1;
    }

    .modal-footer {
      padding: 12px 20px;
      border-top: 1px solid var(--border);
      display: flex;
      justify-content: flex-end;
      gap: 8px;
      flex-shrink: 0;
      background: #F8FAFC;
    }

    /* AI PANEL (Clean Enterprise Variant) */
    .ai-panel {
      background: #FFFFFF;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 18px 20px;
      margin-bottom: 20px;
      box-shadow: var(--shadow-xs);
    }

    .ai-panel-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 8px;
    }

    .ai-icon {
      width: 36px;
      height: 36px;
      background: var(--primary-subtle);
      border: 1px solid #D3E0F0;
      color: var(--primary);
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .ai-icon svg {
      width: 18px;
      height: 18px;
      stroke: currentColor;
      fill: none;
    }

    .ai-panel h3 {
      font-size: 14.5px;
      font-weight: 700;
      color: var(--text-main);
    }

    .ai-panel p {
      font-size: 12.5px;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .ai-result,
    .ai-result-card {
      font-size: 13px;
      color: var(--text-main);
      line-height: 1.6;
    }

    .ai-table {
      width: 100%;
      border-collapse: collapse;
      margin: 12px 0;
      font-size: 12.5px;
      background: #FFFFFF;
      color: var(--text-main);
      border-radius: var(--radius-sm);
      overflow: hidden;
      border: 1px solid var(--border);
    }

    .ai-table th {
      background: #F8FAFC;
      color: #334155;
      font-weight: 600;
      padding: 8px 12px;
      text-align: left;
      border: 1px solid var(--border);
    }

    .ai-table td {
      padding: 8px 12px;
      color: #334155;
      border: 1px solid var(--border);
      vertical-align: top;
    }

    .ai-table tr:nth-child(even) td {
      background: #F8FAFC;
    }

    .ai-h4 {
      color: var(--text-main);
      font-size: 14.5px;
      font-weight: 700;
      border-bottom: 1px solid var(--border);
      padding-bottom: 6px;
      margin: 16px 0 10px;
    }

    .ai-loading {
      display: none;
      align-items: center;
      gap: 10px;
      color: var(--text-muted);
      font-size: 13px;
    }

    .spinner {
      width: 16px;
      height: 16px;
      border: 2px solid #E2E8F0;
      border-top-color: var(--primary);
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
      gap: 12px;
      padding: 12px 0;
      border-bottom: 1px solid var(--border);
    }

    .file-list-item:last-child {
      border-bottom: none;
    }

    .file-icon {
      width: 34px;
      height: 34px;
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10.5px;
      font-weight: 700;
      flex-shrink: 0;
      color: #FFFFFF;
    }

    .file-icon.pdf { background: #DC2626; }
    .file-icon.docx, .file-icon.doc { background: #2563EB; }
    .file-icon.txt { background: #475569; }
    .file-icon.xls, .file-icon.xlsx { background: #166534; }

    .file-info {
      flex: 1;
      min-width: 0;
    }

    .file-name {
      font-size: 13.5px;
      font-weight: 600;
      color: var(--text-main);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .file-meta {
      font-size: 11.5px;
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
      gap: 6px;
      flex-shrink: 0;
    }

    /* DROPZONE */
    .dropzone {
      border: 1.5px dashed var(--border);
      border-radius: var(--radius);
      padding: 32px 20px;
      text-align: center;
      cursor: pointer;
      transition: all var(--transition);
      background: #F8FAFC;
      position: relative;
    }

    .dropzone:hover {
      border-color: var(--primary);
      background: #F0F5FA;
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
      width: 44px;
      height: 44px;
      background: var(--primary-subtle);
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 12px;
    }

    .dropzone-icon svg {
      width: 22px;
      height: 22px;
      color: var(--primary);
      fill: none;
      stroke: currentColor;
    }

    .dropzone h4 {
      font-size: 14px;
      font-weight: 600;
      color: var(--text-main);
      margin-bottom: 4px;
    }

    .dropzone p {
      font-size: 12px;
      color: var(--text-muted);
    }

    .file-types {
      display: flex;
      gap: 6px;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 10px;
    }

    .file-type-badge {
      background: #FFFFFF;
      border: 1px solid var(--border);
      border-radius: 4px;
      padding: 2px 8px;
      font-size: 11px;
      font-weight: 600;
      color: var(--text-muted);
    }

    /* ACCORDION */
    .kriteria-accordion {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .ka-item {
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      overflow: hidden;
      transition: border-color var(--transition);
      background: #FFFFFF;
    }

    .ka-item:focus-within,
    .ka-item:hover {
      border-color: #CBD5E1;
    }

    .ka-header {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      cursor: pointer;
      background: #FFFFFF;
      user-select: none;
      transition: background var(--transition);
    }

    .ka-header:hover {
      background: #F8FAFC;
    }

    .ka-code {
      width: 32px;
      height: 32px;
      background: var(--primary-subtle);
      border: 1px solid #D3E0F0;
      color: var(--primary);
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11.5px;
      font-weight: 700;
      flex-shrink: 0;
    }

    .ka-title {
      flex: 1;
    }

    .ka-title h4 {
      font-size: 13.5px;
      font-weight: 600;
      color: var(--text-main);
    }

    .ka-title p {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 1px;
    }

    .ka-toggle {
      color: var(--text-muted);
      transition: transform var(--transition);
    }

    .ka-body {
      display: none;
      padding: 16px;
      background: #FFFFFF;
      border-top: 1px solid var(--border-subtle);
    }

    .ka-body.show {
      display: block;
    }

    .ka-body .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    @media (max-width: 640px) {
      .ka-body .form-row {
        grid-template-columns: 1fr;
      }
    }

    /* PPEPP PROGRESS BAR */
    .ppepp-progress {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }

    .pp-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;
    }

    .pp-step-inner {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;
    }

    .pp-step-circle {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: #E2E8F0;
      color: #64748B;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12.5px;
      font-weight: 700;
      transition: all var(--transition);
      border: 2px solid transparent;
    }

    .pp-step.active .pp-step-circle {
      background: var(--primary);
      color: #FFFFFF;
      border-color: #173B67;
      box-shadow: 0 0 0 3px rgba(23, 59, 103, 0.15);
    }

    .pp-step.done .pp-step-circle {
      background: var(--success);
      color: #FFFFFF;
    }

    .pp-step-label {
      font-size: 11.5px;
      font-weight: 500;
      color: var(--text-muted);
    }

    .pp-step.active .pp-step-label {
      color: var(--primary);
      font-weight: 600;
    }

    .pp-connector {
      flex: 1;
      height: 2px;
      background: #E2E8F0;
      min-width: 32px;
      max-width: 64px;
    }

    .pp-connector.done {
      background: var(--success);
    }

    /* Common Utility Helpers */
    .mt-1 { margin-top: 8px; }
    .mt-2 { margin-top: 12px; }
    .mt-3 { margin-top: 16px; }
    .mt-4 { margin-top: 20px; }
    .mb-1 { margin-bottom: 8px; }
    .mb-2 { margin-bottom: 12px; }
    .mb-3 { margin-bottom: 16px; }
    .mb-4 { margin-bottom: 20px; }

    code {
      background: #F1F5F9;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 12px;
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
      color: var(--text-main);
    }

    /* Responsive Queries */
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
        overlay.classList.add('show', 'open');
        document.body.style.overflow = 'hidden';
      }
    };

    window.closeModal = function (id) {
      var overlay = document.getElementById(id);
      if (overlay) {
        overlay.classList.remove('show', 'open');
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
  <div id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.5);backdrop-filter:blur(2px);z-index:99;"
    onclick="document.querySelector('.sidebar').classList.remove('open');this.style.display='none';"></div>

  <!-- Sidebar -->
  <aside class="sidebar">
    <!-- Brand Header -->
    <div class="sidebar-header">
      <a href="<?= BASE_URL ?>/ppepp" class="sidebar-brand">
        <div class="brand-logo">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2L2 7l10 5 10-5-10-5z" />
            <path d="M2 17l10 5 10-5" />
            <path d="M2 12l10 5 10-5" />
          </svg>
        </div>
        <div>
          <div class="brand-title">SIPMI PPEPP</div>
          <div class="brand-sub">Penjaminan Mutu Internal</div>
        </div>
      </a>
    </div>

    <!-- User Profile Widget in Sidebar -->
    <?php
    $roleLabel = [
      'kaprodi' => 'Ketua Program Studi',
      'dekan' => 'Dekan / Pimpinan',
      'dosen' => 'Dosen / Auditor',
    ];
    $userRole = $currentUser['role'] ?? 'dosen';
    ?>
    <div class="sidebar-profile">
      <div style="display:flex;align-items:center;gap:9px;">
        <?php if (!empty($currentUser['avatar'])): ?>
          <img src="<?= htmlspecialchars($currentUser['avatar']) ?>" alt="Avatar"
            style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:1.5px solid rgba(255,255,255,0.2);flex-shrink:0;">
        <?php else: ?>
          <div
            style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,0.12);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:11px;flex-shrink:0;">
            <?= strtoupper(mb_substr(explode(' ', $currentUser['nama_lengkap'] ?: $currentUser['username'] ?: 'U')[0], 0, 1)) ?>
          </div>
        <?php endif; ?>
        <div style="min-width:0;">
          <div
            style="font-size:10px;font-weight:600;color:rgba(255,255,255,0.45);text-transform:uppercase;letter-spacing:0.04em;">
            <?= $roleLabel[$userRole] ?? 'Dosen' ?>
          </div>
          <div
            style="font-size:12px;font-weight:600;color:#fff;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:145px;">
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
            class="nav-link <?= str_starts_with($appRelPath, '/pending') ? 'active' : '' ?>">
            <span class="nav-icon">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </span>
            Dokumen Pending
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
            class="nav-link <?= str_starts_with($appRelPath, '/users/api-key') ? 'active' : '' ?>">
            <span class="nav-icon">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path
                  d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4" />
              </svg>
            </span>
            <?= empty($currentUser['gemini_api_key']) ? 'Konfigurasi' : 'API Key Gemini' ?>
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
      $isProjActive = ($appRelPath === '/ppepp' || str_starts_with($appRelPath, '/ppepp/') || str_starts_with($appRelPath, '/ppepp'));
      $inPpeppProject = str_starts_with($appRelPath, '/ppepp/') && preg_match('#/ppepp/(\d+)#', $appRelPath, $m);
      $activeProjId = $inPpeppProject ? (int) $m[1] : 0;
      ?>
      <ul>
        <li class="nav-item">
          <a href="<?= BASE_URL ?>/ppepp" class="nav-link <?= $isProjActive ? 'active' : '' ?>">
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
        <div class="ppepp-steps">
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
                <div style="font-size:12.5px;"><?= $nama ?></div>
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
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
          style="display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:var(--radius-sm);color:<?= $isAdminActive ? '#fff' : 'rgba(255,255,255,0.7)' ?>;background:<?= $isAdminActive ? '#2563EB' : 'rgba(255,255,255,0.06)' ?>;border:1px solid <?= $isAdminActive ? '#2563EB' : 'rgba(255,255,255,0.1)' ?>;transition:all 0.15s ease;text-decoration:none;flex-shrink:0;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
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
          style="display:none;background:none;border:none;cursor:pointer;padding:6px;border-radius:6px;color:var(--text-muted);"
          onclick="document.querySelector('.sidebar').classList.toggle('open');document.getElementById('sidebarOverlay').style.display='block';">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                <?php if ($i > 0): ?><span class="sep">/</span><?php endif; ?>
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
        <div class="user-badge">
          <?php if (!empty($currentUser['avatar_url'])): ?>
            <img src="<?= htmlspecialchars($currentUser['avatar_url']) ?>" alt="" width="32" height="32"
              style="border-radius:50%;object-fit:cover;border:1px solid #e2e8f0;flex-shrink:0;">
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
              <?= ['dekan' => 'Dekan', 'kaprodi' => 'Kaprodi', 'dosen' => 'Dosen'][$currentUser['role'] ?? 'dosen'] ?? 'Dosen' ?>
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
          <div><?= htmlspecialchars($flash['message']) ?></div>
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
          if (modal) { modal.classList.add('open', 'show'); }
        });
      });
      document.querySelectorAll('[data-modal-close]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var id = btn.getAttribute('data-modal-close');
          var modal = document.getElementById(id);
          if (modal) { modal.classList.remove('open', 'show'); }
        });
      });
      document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
          if (e.target === overlay) overlay.classList.remove('open', 'show');
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
          modal.classList.add('open', 'show');
        });
      });

      // DELETE KRITERIA
      document.querySelectorAll('[data-delete-kriteria]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var modal = document.getElementById('modalDeleteKriteria');
          if (!modal) return;
          modal.querySelector('#deleteKriteriaId').value = btn.dataset.deleteKriteria;
          modal.querySelector('#deleteKriteriaName').textContent = btn.dataset.nama;
          modal.classList.add('open', 'show');
        });
      });

      // DELETE REF
      document.querySelectorAll('[data-delete-ref]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var modal = document.getElementById('modalDeleteRef');
          if (!modal) return;
          modal.querySelector('#deleteRefId').value = btn.dataset.deleteRef;
          modal.querySelector('#deleteRefName').textContent = btn.dataset.nama;
          modal.classList.add('open', 'show');
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
                result.innerHTML = data.success ? (data.analisis || '').replace(/\n/g, '<br>') : '<span style="color:#b42318;">' + (data.message || 'Error') + '</span>';
              }
            })
            .catch(function () {
              if (loading) loading.style.display = 'none';
              if (result) { result.style.display = 'block'; result.innerHTML = '<span style="color:#b42318;">Gagal menghubungi server.</span>'; }
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
          overlay.classList.add('show', 'open');
          document.body.style.overflow = 'hidden';
        }
      }
      function closeModal(id) {
        var overlay = document.getElementById(id);
        if (overlay) {
          overlay.classList.remove('show', 'open');
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
          document.querySelectorAll('.modal-overlay.show, .modal-overlay.open').forEach(function (m) { closeModal(m.id); });
        }
      });
      window.openModal = openModal;
      window.closeModal = closeModal;

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
          header.style.background = '#b7791f';
          icon.innerHTML = '<svg width="20" height="20" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
          title.textContent = 'Kuota API Key Gemini Habis';
          message.innerHTML = msg || 'API Key Gemini Anda telah mencapai batas kuota gratis atau rate limit panggilan dari Google (HTTP 429 / Resource Exhausted).';
          tips.innerHTML = '<strong>Solusi:</strong><br>1. Tunggu 1–2 menit sebelum mencoba lagi.<br>2. Ganti model AI ke <code>gemini-2.0-flash-lite</code> di menu Pengaturan.<br>3. Gunakan API Key Gemini yang lain.';
          btnAction.textContent = 'Buka Pengaturan Model AI';
          btnAction.href = '<?= BASE_URL ?>/setting';
        } else if (type === 'missing_key' || type === 'invalid_key') {
          header.style.background = '#b42318';
          icon.innerHTML = '<svg width="20" height="20" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>';
          title.textContent = (type === 'invalid_key') ? 'API Key Gemini Tidak Valid' : 'API Key Gemini Belum Terpasang';
          message.innerHTML = msg || 'Fitur AI Generate memerlukan API Key Gemini aktif.';
          tips.innerHTML = '<strong>Petunjuk:</strong> Anda dapat membuat Gemini API Key gratis di <strong>Google AI Studio</strong> (aistudio.google.com) lalu memasukkannya di menu Pengaturan.';
          btnAction.textContent = 'Buka Pengaturan API Key';
          btnAction.href = '<?= BASE_URL ?>/setting';
        } else {
          header.style.background = '#173B67';
          icon.innerHTML = '<svg width="20" height="20" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
          title.textContent = 'Peringatan Fitur AI';
          message.innerHTML = msg || 'Terjadi hambatan saat menghubungi layanan Google Gemini AI.';
          tips.innerHTML = 'Silakan periksa koneksi internet atau coba beberapa saat lagi.';
          btnAction.textContent = 'Buka Pengaturan';
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
    <div class="modal" style="max-width:480px;">
      <div id="aiModalHeader"
        style="background:#b42318;color:#fff;padding:18px 20px;display:flex;align-items:center;gap:12px;">
        <div id="aiModalIcon"
          style="width:36px;height:36px;background:rgba(255,255,255,0.2);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="20" height="20" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
            <path
              d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4" />
          </svg>
        </div>
        <div>
          <h3 id="aiModalTitle" style="font-size:14.5px;font-weight:700;margin:0 0 2px;color:#fff;">
            API Key Gemini Belum Terpasang</h3>
          <p style="font-size:11.5px;margin:0;color:rgba(255,255,255,0.85);">Peringatan Fitur AI PPEPP</p>
        </div>
      </div>
      <div class="modal-body">
        <div id="aiModalMessage" style="font-size:13px;color:#334155;line-height:1.6;margin-bottom:14px;">
          API Key Gemini belum diisi. Silakan masukkan API Key Gemini Anda di menu Pengaturan.
        </div>
        <div id="aiModalTips"
          style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:12px 14px;font-size:12px;color:#475569;margin-bottom:16px;line-height:1.5;">
          <strong>Petunjuk:</strong> Anda dapat memperoleh Gemini API Key secara gratis dari Google AI Studio (aistudio.google.com).
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close="globalAiWarningModal">Tutup</button>
        <a href="<?= BASE_URL ?>/setting" id="aiModalBtnAction" class="btn btn-primary">Buka Pengaturan</a>
      </div>
    </div>
  </div>

</body>

</html>