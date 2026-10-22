<?php
$pageTitle   = 'Kelola Kriteria';
$breadcrumbs = [
  ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
  ['label' => 'Penetapan', 'url' => BASE_URL . '/penetapan'],
  ['label' => 'Kelola Kriteria'],
];
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h2>Kelola Kriteria Penetapan</h2>
    <p class="page-desc">
      Kriteria untuk Program Studi <strong><?= htmlspecialchars($user['nama_prodi']) ?></strong>.
      Tiap prodi dapat memiliki kriteria yang berbeda-beda.
    </p>
  </div>
  <div class="page-actions">
    <button class="btn btn-primary" data-modal-open="modalTambahKriteria">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
      </svg>
      Tambah Kriteria
    </button>
  </div>
</div>

<!-- Info Banner -->
<div class="alert alert-info mb-4">
  <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
  </svg>
  <div>
    <strong>Catatan:</strong> Kriteria yang ditetapkan di sini akan menjadi dasar pengisian setiap tahap PPEPP.
    Pastikan kriteria sesuai dengan standar akreditasi yang berlaku untuk prodi Anda.
  </div>
</div>

<!-- Daftar Kriteria -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
      </svg>
      Daftar Kriteria
    </h3>
    <span class="badge badge-primary"><?= count($kriteria) ?> kriteria</span>
  </div>

  <?php if (empty($kriteria)): ?>
  <div class="empty-state">
    <div class="empty-icon">
      <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
      </svg>
    </div>
    <h3>Belum ada kriteria</h3>
    <p>Tambahkan kriteria yang akan digunakan sebagai standar penilaian penetapan</p>
    <button class="btn btn-primary" data-modal-open="modalTambahKriteria">Tambah Kriteria Pertama</button>
  </div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Urutan</th>
          <th>Kode</th>
          <th>Nama Kriteria</th>
          <th>Deskripsi</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($kriteria as $k): ?>
        <tr>
          <td class="td-muted" style="text-align:center;"><?= $k['urutan'] ?: '-' ?></td>
          <td>
            <?php if (!empty($k['kode'])): ?>
            <span style="display:inline-flex;align-items:center;justify-content:center;padding:4px 10px;background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff;border-radius:8px;font-weight:700;font-size:12px;">
              <?= htmlspecialchars($k['kode']) ?>
            </span>
            <?php else: ?>
            <span style="color:#94a3b8;font-size:12px;font-style:italic;">(Diisi saat penetapan)</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-weight:600;"><?= htmlspecialchars($k['nama']) ?></div>
          </td>
          <td class="td-muted" style="max-width:300px;">
            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
              <?= $k['deskripsi'] ? htmlspecialchars($k['deskripsi']) : '<em>—</em>' ?>
            </div>
          </td>
          <td>
            <?php if ($k['aktif']): ?>
            <span class="badge badge-success badge-dot">Aktif</span>
            <?php else: ?>
            <span class="badge badge-gray badge-dot">Nonaktif</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="display:flex;gap:6px;">
              <button class="btn btn-outline btn-sm"
                data-edit-kriteria
                data-id="<?= $k['id'] ?>"
                data-kode="<?= htmlspecialchars($k['kode'], ENT_QUOTES) ?>"
                data-nama="<?= htmlspecialchars($k['nama'], ENT_QUOTES) ?>"
                data-deskripsi="<?= htmlspecialchars($k['deskripsi'] ?? '', ENT_QUOTES) ?>"
                data-urutan="<?= $k['urutan'] ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
                  <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                  <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit
              </button>
              <?php if ($k['aktif']): ?>
              <button class="btn btn-danger btn-sm"
                data-delete-kriteria="<?= $k['id'] ?>"
                data-nama="<?= htmlspecialchars($k['nama'], ENT_QUOTES) ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
                  <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
                  <path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/>
                </svg>
                Nonaktifkan
              </button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<!-- Info tentang kriteria standar -->
<div class="card mt-4">
  <div class="card-header">
    <h3 class="card-title">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      Kriteria Akreditasi BAN-PT (Referensi)
    </h3>
  </div>
  <div class="card-body">
    <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
      Berikut adalah 9 kriteria standar akreditasi BAN-PT yang dapat dijadikan referensi. Anda dapat menyesuaikan dengan kebutuhan prodi.
    </p>
    <?php
    $kriteriaRef = [
      'C1' => 'Visi, Misi, Tujuan, dan Strategi',
      'C2' => 'Tata Pamong, Tata Kelola, dan Kerjasama',
      'C3' => 'Mahasiswa',
      'C4' => 'Sumber Daya Manusia',
      'C5' => 'Keuangan, Sarana, dan Prasarana',
      'C6' => 'Pendidikan',
      'C7' => 'Penelitian',
      'C8' => 'Pengabdian kepada Masyarakat',
      'C9' => 'Luaran dan Capaian Tridharma',
    ];
    ?>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <?php foreach ($kriteriaRef as $kode => $nama): ?>
      <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
        <span style="width:28px;height:28px;background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff;border-radius:6px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:11px;flex-shrink:0;">
          <?= $kode ?>
        </span>
        <span><?= $nama ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ============================================================
     MODAL: Tambah Kriteria
     ============================================================ -->
<div class="modal-overlay" id="modalTambahKriteria">
  <div class="modal">
    <div class="modal-header">
      <h3>
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Tambah Kriteria Baru
      </h3>
      <button class="modal-close" data-modal-close="modalTambahKriteria">✕</button>
    </div>
    <form action="<?= BASE_URL ?>/penetapan/kriteria/save" method="POST">
      <input type="hidden" name="id" value="0">
      <div class="modal-body">
        <div class="grid-2 mb-3">
          <div class="form-group" style="margin-bottom:0;">
            <label for="addKode">Kode Kriteria *</label>
            <input type="text" id="addKode" name="kode" class="form-control"
                   placeholder="Contoh: C1, K1, STD-01" value="" maxlength="20" required>
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label for="addUrutan">Urutan</label>
            <input type="number" id="addUrutan" name="urutan" class="form-control"
                   placeholder="1, 2, 3 ..." min="0" value="0">
          </div>
        </div>
        <div class="form-group">
          <label for="addNama">Nama Kriteria *</label>
          <input type="text" id="addNama" name="nama" class="form-control"
                 placeholder="Contoh: Visi, Misi, Tujuan, dan Strategi" required>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label for="addDeskripsi">Deskripsi</label>
          <textarea id="addDeskripsi" name="deskripsi" class="form-control"
                    placeholder="Deskripsi singkat tentang kriteria ini..." rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close="modalTambahKriteria">Batal</button>
        <button type="submit" class="btn btn-primary">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
          </svg>
          Simpan Kriteria
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================================
     MODAL: Edit Kriteria
     ============================================================ -->
<div class="modal-overlay" id="modalEditKriteria">
  <div class="modal">
    <div class="modal-header">
      <h3>
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
          <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Edit Kriteria
      </h3>
      <button class="modal-close" data-modal-close="modalEditKriteria">✕</button>
    </div>
    <form action="<?= BASE_URL ?>/penetapan/kriteria/save" method="POST" id="formEditKriteria">
      <input type="hidden" name="id" value="">
      <div class="modal-body">
        <div class="grid-2 mb-3">
          <div class="form-group" style="margin-bottom:0;">
            <label for="editKode">Kode Kriteria *</label>
            <input type="text" id="editKode" name="kode" class="form-control" maxlength="20" required>
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label for="editUrutan">Urutan</label>
            <input type="number" id="editUrutan" name="urutan" class="form-control" min="0">
          </div>
        </div>
        <div class="form-group">
          <label for="editNama">Nama Kriteria *</label>
          <input type="text" id="editNama" name="nama" class="form-control" required>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label for="editDeskripsi">Deskripsi</label>
          <textarea id="editDeskripsi" name="deskripsi" class="form-control" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close="modalEditKriteria">Batal</button>
        <button type="submit" class="btn btn-primary">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
          </svg>
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================================
     MODAL: Konfirmasi Hapus
     ============================================================ -->
<div class="modal-overlay" id="modalDeleteKriteria">
  <div class="modal" style="max-width:420px;">
    <div class="modal-header">
      <h3 style="color:var(--danger);">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
          <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        Nonaktifkan Kriteria
      </h3>
      <button class="modal-close" data-modal-close="modalDeleteKriteria">✕</button>
    </div>
    <div class="modal-body">
      <p style="font-size:14px;color:var(--text-main);">
        Apakah Anda yakin ingin menonaktifkan kriteria
        <strong id="deleteKriteriaName"></strong>?
      </p>
      <p style="font-size:13px;color:var(--text-muted);margin-top:10px;">
        Kriteria yang dinonaktifkan tidak akan muncul pada form penetapan baru, namun data historis tetap terjaga.
      </p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline" data-modal-close="modalDeleteKriteria">Batal</button>
      <form action="<?= BASE_URL ?>/penetapan/kriteria/delete" method="POST" style="display:inline;">
        <input type="hidden" name="id" id="deleteKriteriaId">
        <button type="submit" class="btn btn-danger">Nonaktifkan</button>
      </form>
    </div>
  </div>
</div>
