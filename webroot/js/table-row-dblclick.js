/**
 * Table row double-click navigation for [data-row-dblclick].
 *
 * Rows with data-dblclick-url navigate on dblclick.
 * Clicks on links, buttons, inputs and labels are ignored.
 */
(function (window, document) {
  const isInteractive = (el) =>
    !!el.closest('a, button, input, label, select, textarea, .table-data-feature');

  const initTable = (table) => {
    const mode = (table.getAttribute('data-row-dblclick') || 'none').toLowerCase();
    if (mode !== 'edit' && mode !== 'view') {
      return;
    }

    table.addEventListener('dblclick', (event) => {
      if (isInteractive(event.target)) {
        return;
      }

      const row = event.target.closest('tbody tr[data-dblclick-url]');
      if (!row || !table.contains(row)) {
        return;
      }

      const url = row.getAttribute('data-dblclick-url');
      if (url) {
        window.location.href = url;
      }
    });
  };

  const init = (root) => {
    (root || document).querySelectorAll('table[data-row-dblclick]').forEach(initTable);
  };

  window.JeffAdminRowDblClick = { init };

  ready(() => {
    init();

    const last = document.querySelector('table.table-data2 tbody tr.is-last-touched');
    if (!last) {
      return;
    }

    const tbody = last.parentElement;
    if (!tbody) {
      return;
    }

    const rows = Array.from(tbody.querySelectorAll(':scope > tr'));
    const index = rows.indexOf(last);
    // Az oldal első 10 sora már látszik — ne görgessünk.
    if (index < 0 || index < 10) {
      return;
    }

    const header = document.querySelector('.header-desktop, .header-mobile, .header-wrap');
    const headerH = header ? header.getBoundingClientRect().height : 64;
    // A sor a viewport felső harmadába kerüljön (header alatt).
    const targetY = Math.max(headerH + 16, window.innerHeight / 3);
    const top = last.getBoundingClientRect().top + window.pageYOffset - targetY;
    window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
  });
})(window, document);
