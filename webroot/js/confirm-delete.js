/**
 * JeffAdmin — törlés megerősítés SweetAlert2-vel.
 *
 * A CakePHP FormHelper::postLink() confirm opciója `data-confirm-message`
 * attribútumot és natív confirm()-ot tesz a linkre. Capture fázisban
 * elfogjuk a kattintást, és piros Swal dialógust mutatunk helyette.
 */
(function (window, document) {
  const RED = '#dc3545';
  const RED_DARK = '#b02a37';
  const GRAY = '#6c757d';

  const formFromLink = (el) => {
    const onclick = el.getAttribute('onclick') || '';
    const match = onclick.match(/document\.([A-Za-z0-9_]+)\.requestSubmit/);
    if (match && document[match[1]] && typeof document[match[1]].requestSubmit === 'function') {
      return document[match[1]];
    }

    let prev = el.previousElementSibling;
    while (prev) {
      if (prev.tagName === 'FORM') {
        return prev;
      }
      prev = prev.previousElementSibling;
    }

    return null;
  };

  const confirmDelete = (el) => {
    const message = el.getAttribute('data-confirm-message') || '';

    if (typeof Swal === 'undefined') {
      return Promise.resolve(window.confirm(message || 'Biztosan törölni szeretnéd?'));
    }

    return Swal.fire({
      title: 'Biztosan törölni szeretnéd?',
      text: message || 'Ez a művelet nem visszavonható!',
      icon: 'warning',
      iconColor: RED,
      showCancelButton: true,
      focusCancel: true,
      reverseButtons: true,
      confirmButtonText: 'Igen, töröld!',
      cancelButtonText: 'Mégse',
      confirmButtonColor: RED,
      cancelButtonColor: GRAY,
      customClass: {
        popup: 'swal2-jeffadmin-delete',
        confirmButton: 'swal2-jeffadmin-delete__confirm',
        cancelButton: 'swal2-jeffadmin-delete__cancel',
      },
      didOpen: (popup) => {
        const confirmBtn = popup.querySelector('.swal2-confirm');
        if (confirmBtn) {
          confirmBtn.style.backgroundColor = RED;
          confirmBtn.style.borderColor = RED_DARK;
        }
      },
    }).then((result) => result.isConfirmed === true);
  };

  document.addEventListener(
    'click',
    (event) => {
      const el = event.target.closest('[data-confirm-message]');
      if (!el) {
        return;
      }

      event.preventDefault();
      event.stopImmediatePropagation();

      confirmDelete(el).then((ok) => {
        if (!ok) {
          return;
        }
        const form = formFromLink(el);
        if (form) {
          form.requestSubmit();
        }
      });
    },
    true
  );
})(window, document);
