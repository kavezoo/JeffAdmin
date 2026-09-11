/**
 * Table row select-all / indeterminate for [data-table-select].
 */
(function (window, document) {
  const syncHeader = (table) => {
    const master = table.querySelector('[data-select-all]');
    if (!master) return;
    const rows = [...table.querySelectorAll('[data-select-row]')];
    const total = rows.length;
    const checked = rows.filter((el) => el.checked).length;

    master.indeterminate = checked > 0 && checked < total;
    master.checked = total > 0 && checked === total;
  };

  const initTable = (table) => {
    const master = table.querySelector('[data-select-all]');
    if (!master) return;

    master.addEventListener('change', () => {
      const on = master.checked;
      master.indeterminate = false;
      table.querySelectorAll('[data-select-row]').forEach((el) => {
        el.checked = on;
      });
    });

    table.querySelectorAll('[data-select-row]').forEach((el) => {
      el.addEventListener('change', () => syncHeader(table));
    });

    syncHeader(table);
  };

  const init = (root) => {
    (root || document).querySelectorAll('[data-table-select]').forEach(initTable);
  };

  window.JeffAdminTableSelect = { init };

  ready(() => init());
})(window, document);
