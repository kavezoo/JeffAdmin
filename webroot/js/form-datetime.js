/**
 * Flatpickr date / time / datetime init.
 * Display = locale config; POST value = submitFormats (hidden real input via altInput).
 */
(function (window, document) {
  const instances = [];

  const cfg = () => window.JeffAdminDateTimeConfig || {};

  const resolveLocale = (code) => {
    if (typeof flatpickr === 'undefined') return 'default';
    const l10n = flatpickr.l10ns || {};
    if (code && l10n[code]) return l10n[code];
    return l10n.default || 'default';
  };

  const displayOf = (kind) => {
    const c = cfg();
    const locale = c.locale || 'hu';
    const pack = (c.displayFormats && c.displayFormats[locale]) || (c.displayFormats && c.displayFormats.hu) || {};
    return pack[kind] || c.submitFormats?.[kind];
  };

  const placeholderOf = (kind) => {
    const c = cfg();
    const locale = c.locale || 'hu';
    const pack = (c.placeholders && c.placeholders[locale]) || {};
    return pack[kind] || '';
  };

  const submitOf = (kind) => {
    const c = cfg();
    return (c.submitFormats && c.submitFormats[kind]) || displayOf(kind);
  };

  const sizeClass = (kind) => {
    if (kind === 'time') return 'fp-field--time';
    if (kind === 'datetime') return 'fp-field--datetime';
    return 'fp-field--date';
  };

  /**
   * Resolve defaultDate for flatpickr.
   * Priority: PHP value attribute → data-fp-default → config.defaults[kind]
   * PHP value must be in submitFormats (Y-m-d / H:i / Y-m-d H:i:S).
   */
  const resolveDefault = (el, kind) => {
    if (el.value && String(el.value).trim() !== '') {
      return el.value.trim();
    }

    const c = cfg();
    const attr = el.getAttribute('data-fp-default');
    let raw = attr !== null ? attr : c.defaults?.[kind];

    if (raw == null || raw === '' || raw === 'none' || raw === 'null') {
      return undefined;
    }

    if (raw === 'today' || raw === 'now') {
      return new Date();
    }

    return raw;
  };

  const buildOptions = (el, kind) => {
    const c = cfg();
    const common = { ...(c.flatpickr || {}) };
    const localeCode = c.locale || 'hu';
    const defaultDate = resolveDefault(el, kind);

    const options = {
      ...common,
      locale: resolveLocale(localeCode),
      altInput: true,
      altFormat: displayOf(kind),
      dateFormat: submitOf(kind),
      altInputClass: `form-control fp-field ${sizeClass(kind)}`,
      onReady(_selected, _dateStr, fp) {
        const ph = placeholderOf(kind);
        if (ph && fp.altInput) fp.altInput.setAttribute('placeholder', ph);
        // Keep label[for] on the visible field
        if (el.id && fp.altInput) {
          fp.altInput.id = el.id;
          el.removeAttribute('id');
        }
      },
    };

    if (defaultDate !== undefined) {
      options.defaultDate = defaultDate;
    }

    if (kind === 'time') {
      options.enableTime = true;
      options.noCalendar = true;
    }

    if (kind === 'datetime') {
      options.enableTime = true;
      // Seconds go to POST (dateFormat has S); picker UI stays hour+minute
      options.enableSeconds = false;
    }

    return options;
  };

  const destroyAll = () => {
    while (instances.length) {
      const fp = instances.pop();
      try {
        if (fp.altInput?.id && fp.input && !fp.input.id) {
          fp.input.id = fp.altInput.id;
        }
        fp.destroy();
      } catch (_e) {
        /* ignore */
      }
    }
  };

  const init = (root) => {
    if (typeof flatpickr === 'undefined') return;
    const scope = root || document;

    destroyAll();

    scope.querySelectorAll('[data-fp]').forEach((el) => {
      const kind = el.getAttribute('data-fp');
      if (!kind || !['date', 'time', 'datetime'].includes(kind)) return;

      const fp = flatpickr(el, buildOptions(el, kind));
      instances.push(fp);
    });
  };

  const setLocale = (localeCode) => {
    const c = cfg();
    c.locale = localeCode;
    init();
  };

  window.JeffAdminDateTime = {
    init,
    setLocale,
    getLocale: () => cfg().locale || 'hu',
    getConfig: cfg,
  };

  ready(() => {
    init();
  });
})(window, document);
