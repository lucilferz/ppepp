/**
 * app.js - JavaScript Utama Platform PPEPP
 */

document.addEventListener('DOMContentLoaded', function () {

  // ============================================================
  // Flash Message Auto-hide
  // ============================================================
  const alerts = document.querySelectorAll('.alert[data-autohide]');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-6px)';
      alert.style.transition = 'opacity 0.4s, transform 0.4s';
      setTimeout(() => alert.remove(), 400);
    }, 4000);
  });

  // ============================================================
  // Modal System
  // ============================================================
  function openModal(id) {
    const overlay = document.getElementById(id);
    if (overlay) {
      overlay.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeModal(id) {
    const overlay = document.getElementById(id);
    if (overlay) {
      overlay.classList.remove('show');
      document.body.style.overflow = '';
    }
  }

  // Tombol open modal
  document.querySelectorAll('[data-modal-open]').forEach(btn => {
    btn.addEventListener('click', () => openModal(btn.dataset.modalOpen));
  });

  // Tombol close modal
  document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => closeModal(btn.dataset.modalClose));
  });

  // Klik di luar modal
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
      if (e.target === overlay) closeModal(overlay.id);
    });
  });

  // ESC key
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay.show').forEach(m => {
        closeModal(m.id);
      });
    }
  });

  // Expose globally
  window.openModal  = openModal;
  window.closeModal = closeModal;

  // ============================================================
  // Edit Kriteria - isi form modal dari data row
  // ============================================================
  document.querySelectorAll('[data-edit-kriteria]').forEach(btn => {
    btn.addEventListener('click', () => {
      const data = btn.dataset;
      const form = document.getElementById('formEditKriteria');
      if (!form) return;

      form.querySelector('[name="id"]').value        = data.id;
      form.querySelector('[name="kode"]').value      = data.kode;
      form.querySelector('[name="nama"]').value      = data.nama;
      form.querySelector('[name="deskripsi"]').value = data.deskripsi;
      form.querySelector('[name="urutan"]').value    = data.urutan;

      openModal('modalEditKriteria');
    });
  });

  // ============================================================
  // Hapus Kriteria - konfirmasi & isi id
  // ============================================================
  document.querySelectorAll('[data-delete-kriteria]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id   = btn.dataset.deleteKriteria;
      const nama = btn.dataset.nama;
      document.getElementById('deleteKriteriaId').value   = id;
      document.getElementById('deleteKriteriaName').textContent = nama;
      openModal('modalDeleteKriteria');
    });
  });

  // ============================================================
  // Accordion Kriteria di Form Penetapan
  // ============================================================
  document.querySelectorAll('.ka-header').forEach(header => {
    // If header has inline onclick handler, do not bind duplicate click listener
    if (header.hasAttribute('onclick')) return;

    header.addEventListener('click', () => {
      const item   = header.closest('.ka-item');
      if (!item) return;
      const body   = item.querySelector('.ka-body');
      if (!body) return;
      const toggle = header.querySelector('.ka-toggle, svg[id^="toggle_"], svg');
      const isOpen = body.classList.contains('show') || (body.style.display !== 'none' && body.style.display !== '');

      if (!isOpen) {
        body.classList.add('show');
        body.style.display = 'block';
        if (toggle) {
          toggle.classList.add('open');
          toggle.style.transform = 'rotate(180deg)';
        }
      } else {
        body.classList.remove('show');
        body.style.display = 'none';
        if (toggle) {
          toggle.classList.remove('open');
          toggle.style.transform = 'rotate(0deg)';
        }
      }
    });
  });

  // ============================================================
  // Drag & Drop File Upload
  // ============================================================
  const dropzone = document.querySelector('.dropzone');
  if (dropzone) {
    ['dragenter', 'dragover'].forEach(evt => {
      dropzone.addEventListener(evt, e => {
        e.preventDefault();
        dropzone.classList.add('drag-over');
      });
    });
    ['dragleave', 'drop'].forEach(evt => {
      dropzone.addEventListener(evt, e => {
        e.preventDefault();
        dropzone.classList.remove('drag-over');
      });
    });
    dropzone.addEventListener('drop', e => {
      const files = e.dataTransfer.files;
      const input = dropzone.querySelector('input[type="file"]');
      if (input && files.length > 0) {
        const dt = new DataTransfer();
        dt.items.add(files[0]);
        input.files = dt.files;
        showFileName(files[0].name);
      }
    });

    const fileInput = dropzone.querySelector('input[type="file"]');
    if (fileInput) {
      fileInput.addEventListener('change', () => {
        if (fileInput.files[0]) showFileName(fileInput.files[0].name);
      });
    }

    function showFileName(name) {
      let label = dropzone.querySelector('.selected-file');
      if (!label) {
        label = document.createElement('p');
        label.className = 'selected-file mt-2 fw-600';
        label.style.color = 'var(--primary-light)';
        dropzone.appendChild(label);
      }
      label.textContent = '📎 ' + name;
    }
  }

  // ============================================================
  // Checkbox Kriteria untuk AI Analisis
  // ============================================================
  document.querySelectorAll('.checkbox-item input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', () => {
      cb.closest('.checkbox-item').classList.toggle('checked', cb.checked);
    });
    // Init state
    cb.closest('.checkbox-item').classList.toggle('checked', cb.checked);
  });

  // Select All / None
  const btnSelectAll  = document.getElementById('btnSelectAllKriteria');
  const btnSelectNone = document.getElementById('btnSelectNoneKriteria');
  if (btnSelectAll) {
    btnSelectAll.addEventListener('click', () => {
      document.querySelectorAll('.checkbox-item input[type="checkbox"]').forEach(cb => {
        cb.checked = true;
        cb.closest('.checkbox-item').classList.add('checked');
      });
    });
  }
  if (btnSelectNone) {
    btnSelectNone.addEventListener('click', () => {
      document.querySelectorAll('.checkbox-item input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
        cb.closest('.checkbox-item').classList.remove('checked');
      });
    });
  }

  // ============================================================
  // AI Analyze Button
  // ============================================================
  document.querySelectorAll('[data-analyze-btn]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const referensiId = btn.dataset.analyzeBtn;
      const resultEl    = document.getElementById('aiResult_' + referensiId);
      const loadingEl   = document.getElementById('aiLoading_' + referensiId);
      const panelEl     = document.getElementById('aiPanel_' + referensiId);

      // Kumpulkan kriteria yang dipilih
      const checkedBoxes = document.querySelectorAll('input[name="kriteria_ids[]"]:checked');
      const kriteriaIds  = Array.from(checkedBoxes).map(cb => cb.value);
      const pertanyaan   = document.getElementById('aiPertanyaan')?.value || '';

      if (kriteriaIds.length === 0) {
        alert('Pilih minimal 1 kriteria untuk dianalisis.');
        return;
      }

      // Tampilkan panel & loading
      if (panelEl) panelEl.style.display = 'block';
      if (loadingEl) loadingEl.style.display = 'flex';
      if (resultEl) resultEl.style.display = 'none';
      btn.disabled = true;
      btn.textContent = 'Menganalisis...';

      try {
        const formData = new FormData();
        formData.append('referensi_id', referensiId);
        formData.append('pertanyaan', pertanyaan);
        kriteriaIds.forEach(id => formData.append('kriteria_ids[]', id));

        const response = await fetch(BASE_URL + '/penetapan/referensi/analyze', {
          method: 'POST',
          body: formData,
        });

        const data = await response.json();

        if (data.success) {
          if (resultEl) {
            resultEl.style.display = 'block';
            resultEl.innerHTML = formatAIText(data.analisis);
          }
        } else {
          if (resultEl) {
            resultEl.style.display = 'block';
            resultEl.innerHTML = '<span style="color:#ff8a7a;">⚠️ Error: ' + data.message + '</span>';
          }
        }
      } catch (err) {
        if (resultEl) {
          resultEl.style.display = 'block';
          resultEl.innerHTML = '<span style="color:#ff8a7a;">⚠️ Gagal terhubung ke server.</span>';
        }
      } finally {
        if (loadingEl) loadingEl.style.display = 'none';
        btn.disabled = false;
        btn.textContent = '🔍 Analisis Ulang';
      }
    });
  });

  // Format teks AI (markdown sederhana)
  function formatAIText(text) {
    return text
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/\*(.*?)\*/g, '<em>$1</em>')
      .replace(/^#{1,3}\s+(.+)$/gm, '<strong style="color:var(--accent-light);font-size:15px;">$1</strong>')
      .replace(/^-\s+(.+)$/gm, '&bull; $1')
      .replace(/\n/g, '<br>');
  }

  // ============================================================
  // Sidebar toggle mobile
  // ============================================================
  const menuToggle = document.getElementById('menuToggle');
  const sidebar    = document.querySelector('.sidebar');
  const overlay    = document.getElementById('sidebarOverlay');

  if (menuToggle && sidebar) {
    menuToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      if (overlay) overlay.classList.toggle('show');
    });
    if (overlay) {
      overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
      });
    }
  }

  // ============================================================
  // Confirm delete file referensi
  // ============================================================
  document.querySelectorAll('[data-delete-ref]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id   = btn.dataset.deleteRef;
      const nama = btn.dataset.nama;
      document.getElementById('deleteRefId').value = id;
      document.getElementById('deleteRefName').textContent = nama;
      openModal('modalDeleteRef');
    });
  });

  // ============================================================
  // Form Penetapan - Finalize confirm
  // ============================================================
  const finalizeBtn = document.getElementById('finalizeBtn');
  if (finalizeBtn) {
    finalizeBtn.addEventListener('click', (e) => {
      if (!confirm('Yakin ingin memfinalisasi penetapan ini? Status akan berubah menjadi FINAL dan tidak bisa diedit.')) {
        e.preventDefault();
      }
    });
  }

});

// Base URL untuk AJAX (diset dari PHP di layout)
let BASE_URL = window.PPEPP_BASE_URL || '';
