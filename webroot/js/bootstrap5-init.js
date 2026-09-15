/**
 * Bootstrap 5 initialization for components that don't auto-init.
 *
 * Modals, dropdowns, tabs, collapses, alerts, and carousels self-instantiate
 * via `data-bs-toggle` / `data-bs-ride` — explicit `new bootstrap.X()` is only
 * needed for tooltips and popovers (which are opt-in for performance).
 */

ready(() => {
  $$('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
  $$('[data-bs-toggle="popover"]').forEach((el) => new bootstrap.Popover(el));

  // Custom file picker — show selected filename
  $$('.file-picker__input').forEach((input) => {
    const nameEl = input.closest('.file-picker')?.querySelector('[data-file-name]');
    if (!nameEl) return;
    const emptyLabel = nameEl.textContent.trim() || 'No file chosen';
    input.addEventListener('change', () => {
      const file = input.files && input.files[0];
      if (file) {
        nameEl.textContent = file.name;
        nameEl.classList.add('is-selected');
      } else {
        nameEl.textContent = emptyLabel;
        nameEl.classList.remove('is-selected');
      }
    });
  });

  // Tom Select — enhance all form selects; ensure "..." action button
  if (typeof TomSelect !== 'undefined') {
    $$('select.form-select').forEach((el) => {
      if (el.tomselect || el.getAttribute('data-tom-select') === 'false') return;

      if (!el.closest('.select-with-action')) {
        const wrap = document.createElement('div');
        wrap.className = 'select-with-action';
        el.parentNode.insertBefore(wrap, el);
        wrap.appendChild(el);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-outline-secondary select-with-action__btn';
        btn.setAttribute('aria-label', 'További lehetőségek');
        btn.title = 'További lehetőségek';
        btn.innerHTML = '<i class="fa-solid fa-ellipsis" aria-hidden="true"></i>';
        wrap.appendChild(btn);
      }

      const isLg = el.classList.contains('form-select-lg');
      const isSm = el.classList.contains('form-select-sm');
      const isMultiple = el.multiple;
      const options = {
        create: false,
        maxOptions: null,
        onInitialize() {
          if (isLg) this.wrapper.classList.add('ts-wrapper--lg');
          if (isSm) this.wrapper.classList.add('ts-wrapper--sm');
        },
      };

      if (isMultiple) {
        options.plugins = {
          remove_button: { title: 'Eltávolítás' },
          clear_button: { title: 'Összes eltávolítása' },
        };
        options.hideSelected = true;
        options.closeAfterSelect = false;
        options.placeholder = el.getAttribute('placeholder') || 'Válassz…';
      } else {
        options.allowEmptyOption = true;
      }

      const ts = new TomSelect(el, options);
      if (el.disabled) {
        ts.disable();
        const actionBtn = el.closest('.select-with-action')?.querySelector('.select-with-action__btn');
        if (actionBtn) actionBtn.disabled = true;
      }
    });
  }

  // Flatpickr init: js/form-datetime-config.js + js/form-datetime.js

  // HugeRTE — WYSIWYG on Megjegyzés tab (init on first show so size is correct)
  const hugerteBaseUrl = (() => {
    const script = document.querySelector('script[src*="hugerte"]');
    if (!script || !script.src) return 'vendor/hugerte';
    return script.src.replace(/\/[^/]*$/, '');
  })();

  const megjegyzesHostHeight = () => {
    const host = document.querySelector('.form-wysiwyg');
    return host ? Math.max(360, host.clientHeight) : 480;
  };

  const resizeMegjegyzesEditor = () => {
    const editor = typeof hugerte !== 'undefined' ? hugerte.get('megjegyzes') : null;
    if (!editor) return;
    const height = megjegyzesHostHeight();
    const container = editor.getContainer();
    if (container) {
      container.style.height = `${height}px`;
    }
    editor.dispatch('ResizeEditor');
  };

  const focusMegjegyzesEditor = () => {
    const editor = typeof hugerte !== 'undefined' ? hugerte.get('megjegyzes') : null;
    if (!editor) return;
    // Defer so Bootstrap tab transition / editor layout finishes first
    requestAnimationFrame(() => {
      try {
        editor.focus();
      } catch (_e) {
        /* ignore */
      }
    });
  };

  const initMegjegyzesEditor = () => {
    if (typeof hugerte === 'undefined') {
      console.error('HugeRTE not loaded');
      return;
    }
    if (hugerte.get('megjegyzes')) {
      resizeMegjegyzesEditor();
      focusMegjegyzesEditor();
      return;
    }
    hugerte.init({
      selector: '#megjegyzes',
      base_url: hugerteBaseUrl,
      suffix: '.min',
      height: megjegyzesHostHeight(),
      min_height: 360,
      resize: false,
      menubar: false,
      branding: false,
      promotion: false,
      plugins: 'lists link table code',
      toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table | code',
      content_style: 'body { font-family: Inter, system-ui, sans-serif; font-size: 14px; }',
      setup(editor) {
        editor.on('init', () => {
          resizeMegjegyzesEditor();
          focusMegjegyzesEditor();
        });
      },
    });
  };

  const megjegyzesTab = document.getElementById('tab-megjegyzes-btn');
  if (megjegyzesTab) {
    megjegyzesTab.addEventListener('shown.bs.tab', initMegjegyzesEditor);
    window.addEventListener('resize', () => {
      if (document.getElementById('tab-megjegyzes')?.classList.contains('active')) {
        resizeMegjegyzesEditor();
      }
    });
  }
});
