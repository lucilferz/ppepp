<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?><?= APP_NAME ?></title>
  <meta name="description" content="Platform PPEPP untuk Sistem Informasi Penjaminan Mutu Internal (SIPMI) Perguruan Tinggi">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=3.0">
</head>
<body style="background:var(--bg);display:flex;align-items:center;justify-content:center;min-height:100vh;">

<div class="auth-wrapper">
  <?= $content ?>
</div>

<script src="<?= BASE_URL ?>/js/app.js?v=3.0"></script>
</body>
</html>
