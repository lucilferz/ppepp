/**
 * app.js - Client-Side Interactive Engine Platform SIPMI / PPEPP
 * Enterprise Grade, Modern UX (shadcn/ui + Linear minimalism)
 */

document.addEventListener('DOMContentLoaded', function () {

  // ============================================================
  // Flash Message Auto-dismiss
  // ============================================================
  const alerts = document.querySelectorAll('.alert[data-autohide]');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-4px)';
      alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
      setTimeout(() => alert.remove(), 300);
    }, 4500);
  });

  // ============================================================
  // Modal / Dialog System (shadcn style)
  // ============================================================
  function openModal(id) {
    const overlay = document.getElementById(id);
    if (overlay) {
      overlay.classList.add('open');
      overlay.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeModal(id) {
    const overlay = document.getElementById(id);
    if (overlay) {
      overlay.classList.remove('open');
      overlay.classList.remove('show');
      document.body.style.overflow = '';
    }
  }

  // Open triggers
  document.querySelectorAll('[data-modal-open]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      openModal(btn.dataset.modalOpen);
    });
  });

  // Close triggers
  document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      closeModal(btn.dataset.modalClose);
    });
  });

  // Click outside backdrop
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
      if (e.target === overlay) {
        closeModal(overlay.id);
      }
    });
  });

  // ESC key listener
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay.open, .modal-overlay.show').forEach(m => {
        closeModal(m.id);
      });
    }
  });

  // Expose globally
  window.openModal  = openModal;
  window.closeModal = closeModal;

  // ============================================================
  // Accordion (Kriteria di Form Penetapan / Standar)
  // ============================================================
  document.querySelectorAll('.ka-header').forEach(header => {
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
  // Edit & Delete Kriteria Handlers
  // ============================================================
  document.querySelectorAll('[data-edit-kriteria]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const data = btn.dataset;
      const form = document.getElementById('formEditKriteria') || document.querySelector('#modalEditKriteria form');
      if (!form) return;

      const inputId        = form.querySelector('[name="id"]');
      const inputKode      = form.querySelector('[name="kode"]') || form.querySelector('#editKode');
      const inputNama      = form.querySelector('[name="nama"]') || form.querySelector('#editNama');
      const inputDeskripsi = form.querySelector('[name="deskripsi"]') || form.querySelector('#editDeskripsi');
      const inputUrutan    = form.querySelector('[name="urutan"]') || form.querySelector('#editUrutan');

      if (inputId) inputId.value = data.id || '';
      if (inputKode) inputKode.value = data.kode || '';
      if (inputNama) inputNama.value = data.nama || '';
      if (inputDeskripsi) inputDeskripsi.value = data.deskripsi || '';
      if (inputUrutan) inputUrutan.value = data.urutan || 0;

      openModal('modalEditKriteria');
    });
  });

  document.querySelectorAll('[data-delete-kriteria]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const id   = btn.dataset.deleteKriteria;
      const nama = btn.dataset.nama;
      const elId = document.getElementById('deleteKriteriaId');
      const elNm = document.getElementById('deleteKriteriaName');
      if (elId) elId.value = id;
      if (elNm) elNm.textContent = nama;
      openModal('modalDeleteKriteria');
    });
  });

  // Delete Referensi
  document.querySelectorAll('[data-delete-ref]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const id   = btn.dataset.deleteRef;
      const nama = btn.dataset.nama;
      const elId = document.getElementById('deleteRefId');
      const elNm = document.getElementById('deleteRefName');
      if (elId) elId.value = id;
      if (elNm) elNm.textContent = nama;
      openModal('modalDeleteRef');
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
        label.style.color = 'var(--primary)';
        dropzone.appendChild(label);
      }
      label.textContent = 'Berkas terpilih: ' + name;
    }
  }

  // ============================================================
  // Checkbox Kriteria & AI Analisis
  // ============================================================
  document.querySelectorAll('.checkbox-item input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', () => {
      cb.closest('.checkbox-item')?.classList.toggle('checked', cb.checked);
    });
    cb.closest('.checkbox-item')?.classList.toggle('checked', cb.checked);
  });

  const btnSelectAll  = document.getElementById('btnSelectAllKriteria');
  const btnSelectNone = document.getElementById('btnSelectNoneKriteria');
  if (btnSelectAll) {
    btnSelectAll.addEventListener('click', () => {
      document.querySelectorAll('.checkbox-item input[type="checkbox"], input[name="kriteria_ids[]"]').forEach(cb => {
        cb.checked = true;
        cb.closest('.checkbox-item')?.classList.add('checked');
      });
    });
  }
  if (btnSelectNone) {
    btnSelectNone.addEventListener('click', () => {
      document.querySelectorAll('.checkbox-item input[type="checkbox"], input[name="kriteria_ids[]"]').forEach(cb => {
        cb.checked = false;
        cb.closest('.checkbox-item')?.classList.remove('checked');
      });
    });
  }

  // AI Analyze Button
  document.querySelectorAll('[data-analyze-btn]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const referensiId = btn.dataset.analyzeBtn;
      const resultEl    = document.getElementById('aiResult_' + referensiId);
      const loadingEl   = document.getElementById('aiLoading_' + referensiId);
      const panelEl     = document.getElementById('aiPanel_' + referensiId);

      const checkedBoxes = document.querySelectorAll('input[name="kriteria_ids[]"]:checked');
      const kriteriaIds  = Array.from(checkedBoxes).map(cb => cb.value);
      const pertanyaan   = document.getElementById('aiPertanyaan')?.value || '';

      if (kriteriaIds.length === 0) {
        alert('Pilih minimal 1 kriteria untuk dianalisis.');
        return;
      }

      if (panelEl) panelEl.style.display = 'block';
      if (loadingEl) loadingEl.style.display = 'flex';
      if (resultEl) resultEl.style.display = 'none';
      btn.disabled = true;
      btn.textContent = 'Menganalisis...';

      const appBase = window.PPEPP_BASE_URL || window.BASE_URL || '';

      try {
        const formData = new FormData();
        formData.append('referensi_id', referensiId);
        formData.append('pertanyaan', pertanyaan);
        kriteriaIds.forEach(id => formData.append('kriteria_ids[]', id));

        const response = await fetch(appBase + '/penetapan/referensi/analyze', {
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
            resultEl.innerHTML = '<span style="color:var(--danger);font-weight:600;">Error: ' + (data.message || 'Gagal memproses.') + '</span>';
          }
        }
      } catch (err) {
        if (resultEl) {
          resultEl.style.display = 'block';
          resultEl.innerHTML = '<span style="color:var(--danger);font-weight:600;">Gagal menghubungi server.</span>';
        }
      } finally {
        if (loadingEl) loadingEl.style.display = 'none';
        btn.disabled = false;
        btn.textContent = 'Analisis Ulang';
      }
    });
  });

  function formatAIText(text) {
    if (!text) return '';
    return text
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/\*(.*?)\*/g, '<em>$1</em>')
      .replace(/^#{1,3}\s+(.+)$/gm, '<h4 style="margin:8px 0 4px;font-size:14px;color:var(--primary);">$1</h4>')
      .replace(/^-\s+(.+)$/gm, '&bull; $1')
      .replace(/\n/g, '<br>');
  }

  // Mobile sidebar toggle
  const menuToggle = document.getElementById('menuToggle');
  const sidebar    = document.querySelector('.sidebar');
  const overlay    = document.getElementById('sidebarOverlay');

  if (menuToggle && sidebar) {
    menuToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      if (overlay) overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
    });
  }

  if (overlay) {
    overlay.addEventListener('click', () => {
      if (sidebar) sidebar.classList.remove('open');
      overlay.style.display = 'none';
    });
  }

  // Finalize confirm
  const finalizeBtn = document.getElementById('finalizeBtn');
  if (finalizeBtn) {
    finalizeBtn.addEventListener('click', (e) => {
      if (!confirm('Yakin ingin memfinalisasi penetapan ini? Status akan berubah menjadi FINAL dan tidak dapat diedit.')) {
        e.preventDefault();
      }
    });
  }

  // AI Warning Modal Helper
  window.showAiWarningModal = function (msg, errorType) {
    const modal = document.getElementById('globalAiWarningModal');
    const header = document.getElementById('aiModalHeader');
    const title = document.getElementById('aiModalTitle');
    const message = document.getElementById('aiModalMessage');
    const tips = document.getElementById('aiModalTips');
    const btnAction = document.getElementById('aiModalBtnAction');

    if (!modal) {
      alert((msg || 'Peringatan AI') + '\n\nSilakan isi API Key Anda di Pengaturan.');
      return;
    }

    const type = errorType || '';
    const str = (msg || '').toLowerCase();

    if (type === 'quota_exceeded' || str.includes('kuota') || str.includes('429')) {
      if (header) header.style.background = 'var(--warning)';
      if (title) title.textContent = 'Kuota API Key Gemini Habis';
      if (message) message.textContent = msg || 'Batas kuota gratis atau rate limit telah tercapai.';
      if (tips) tips.innerHTML = '<strong>Solusi:</strong><br>1. Tunggu 1–2 menit.<br>2. Ganti model AI di Pengaturan.<br>3. Gunakan API Key Gemini alternatif.';
    } else {
      if (header) header.style.background = 'var(--danger)';
      if (title) title.textContent = 'API Key Gemini Diperlukan';
      if (message) message.textContent = msg || 'Fitur AI memerlukan API Key Gemini yang aktif.';
      if (tips) tips.innerHTML = 'Dapatkan Gemini API Key gratis di <strong>Google AI Studio</strong> (aistudio.google.com).';
    }

    openModal('globalAiWarningModal');
  };
});

// Helper filters for Project page
window.filterProjects = function (status, btnEl) {
  document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
  if (btnEl) btnEl.classList.add('active');

  const cards = document.querySelectorAll('.project-card, [data-project-status]');
  cards.forEach(card => {
    const cardStatus = card.dataset.projectStatus || 'aktif';
    if (status === 'all' || cardStatus === status) {
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }
  });
};

// Helper for Admin Tabs
window.switchAdminTab = function (tab) {
  const tabBtnUsers = document.getElementById('tabBtnUsers');
  const tabBtnProdi = document.getElementById('tabBtnProdi');
  const sectionUsers = document.getElementById('adminSectionUsers');
  const sectionProdi = document.getElementById('adminSectionProdi');

  if (tab === 'users') {
    if (tabBtnUsers) {
      tabBtnUsers.style.color = 'var(--primary)';
      tabBtnUsers.style.borderBottomColor = 'var(--primary)';
    }
    if (tabBtnProdi) {
      tabBtnProdi.style.color = 'var(--text-muted)';
      tabBtnProdi.style.borderBottomColor = 'transparent';
    }
    if (sectionUsers) sectionUsers.style.display = 'block';
    if (sectionProdi) sectionProdi.style.display = 'none';
  } else {
    if (tabBtnProdi) {
      tabBtnProdi.style.color = 'var(--primary)';
      tabBtnProdi.style.borderBottomColor = 'var(--primary)';
    }
    if (tabBtnUsers) {
      tabBtnUsers.style.color = 'var(--text-muted)';
      tabBtnUsers.style.borderBottomColor = 'transparent';
    }
    if (sectionProdi) sectionProdi.style.display = 'block';
    if (sectionUsers) sectionUsers.style.display = 'none';
  }
};
