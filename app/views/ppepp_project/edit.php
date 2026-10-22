<?php
$pageTitle   = 'Edit Project PPEPP';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Project PPEPP', 'url' => BASE_URL . '/ppepp'],
  ['label' => htmlspecialchars($project['ta_nama'] ?? ''), 'url' => BASE_URL . '/ppepp/' . $project['id']],
  ['label' => 'Edit'],
];
?>
<div class="page-header">
  <div>
    <h2>Edit Project PPEPP</h2>
    <p class="page-desc">Perbarui judul, deskripsi, atau status project untuk Tahun Ajaran <?= htmlspecialchars($project['ta_nama']) ?></p>
  </div>
  <div class="page-actions">
    <a href="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>" class="btn btn-outline btn-sm">
      &larr; Kembali ke Detail Project
    </a>
  </div>
</div>

<div class="card" style="max-width:580px;">
  <div class="card-header">
    <h3 class="card-title">Informasi Project PPEPP</h3>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>/update">
      <div class="form-group">
        <label class="form-label">Tahun Ajaran</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($project['ta_nama']) ?>" disabled style="background:var(--surface-hover);color:var(--text-muted);">
      </div>

      <div class="form-group">
        <label for="judul" class="form-label">Judul Project <span class="required-star">*</span></label>
        <input type="text" id="judul" name="judul" class="form-control" required
               value="<?= htmlspecialchars($project['judul'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($project['deskripsi'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label for="status" class="form-label">Status Project</label>
        <select id="status" name="status" class="form-control">
          <option value="aktif"    <?= $project['status'] === 'aktif'    ? 'selected' : '' ?>>Aktif</option>
          <option value="nonaktif" <?= $project['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif / Tersembunyi</option>
          <option value="selesai"  <?= $project['status'] === 'selesai'  ? 'selected' : '' ?>>Selesai</option>
          <option value="arsip"    <?= $project['status'] === 'arsip'    ? 'selected' : '' ?>>Arsip</option>
        </select>
      </div>

      <div style="display:flex;gap:10px;padding-top:14px;border-top:1px solid var(--border-subtle);margin-top:20px;">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="<?= BASE_URL ?>/ppepp/<?= $project['id'] ?>" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
</div>
