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

  ready(() => init());
})(window, document);
