/**
 * Number spinner — enhances plain inputs marked with data-number-spinner.
 *
 * Example:
 *   <input type="text" class="form-control" data-number-spinner
 *          data-integer="1" min="0" max="100" step="1" value="10">
 *
 * Display follows JeffAdminNumberConfig.locale (hu: 1 234,56 / en: 1,234.56).
 * PHP value and POST always use canonical form: 1234.56 (period, no grouping).
 */
(function (window, document) {
  const instances = [];

  const cfg = () => window.JeffAdminNumberConfig || {};

  const localePack = () => {
    const c = cfg();
    const code = c.locale || 'hu';
    return (c.locales && (c.locales[code] || c.locales.hu)) || {
      decimal: ',',
      thousand: ' ',
      grouping: 3,
    };
  };

  /** Parse display or canonical string → number | null */
  const parseNum = (raw) => {
    if (raw == null || String(raw).trim() === '') return null;
    let s = String(raw).trim().replace(/\u00a0/g, ' ');
    const loc = localePack();

    // Strip thousand separators (locale + common variants)
    if (loc.thousand) {
      s = s.split(loc.thousand).join('');
    }
    s = s.replace(/ /g, '');

    // Locale decimal → period; if both . and , present, treat last as decimal
    const dec = loc.decimal || ',';
    if (dec !== '.') {
      if (s.includes(dec) && s.includes('.')) {
        // e.g. 1.234,56 → remove dots then comma→period
        s = s.split('.').join('').replace(dec, '.');
      } else {
        s = s.replace(dec, '.');
      }
    } else {
      // en: remove commas as thousands
      if ((s.match(/,/g) || []).length && s.includes('.')) {
        s = s.split(',').join('');
      }
    }

    const n = Number(s);
    return Number.isFinite(n) ? n : null;
  };

  const stepDecimals = (step) => {
    const s = String(step);
    const i = s.indexOf('.');
    return i === -1 ? 0 : s.length - i - 1;
  };

  const roundToStep = (value, step) => {
    const d = stepDecimals(step);
    const f = 10 ** d;
    return Math.round(value * f) / f;
  };

  const clamp = (value, min, max) => {
    let v = value;
    if (min != null && v < min) v = min;
    if (max != null && v > max) v = max;
    return v;
  };

  const groupInteger = (intStr, thousand, grouping) => {
    if (!thousand || !grouping || grouping < 1) return intStr;
    const neg = intStr.startsWith('-');
    let digits = neg ? intStr.slice(1) : intStr;
    const parts = [];
    while (digits.length > grouping) {
      parts.unshift(digits.slice(-grouping));
      digits = digits.slice(0, -grouping);
    }
    if (digits.length) parts.unshift(digits);
    return (neg ? '-' : '') + parts.join(thousand);
  };

  /** Visible value per active locale. */
  const formatDisplay = (value, integer, step) => {
    const loc = localePack();
    const decPlaces = integer ? 0 : stepDecimals(step);
    const fixed = integer
      ? String(Math.trunc(value))
      : decPlaces > 0
        ? value.toFixed(decPlaces)
        : String(value);

    const neg = fixed.startsWith('-');
    const body = neg ? fixed.slice(1) : fixed;
    const [intPart, fracPart] = body.split('.');
    const grouped = groupInteger(intPart, loc.thousand, loc.grouping || 3);
    let out = (neg ? '-' : '') + grouped;
    if (!integer && fracPart != null) {
      out += (loc.decimal || ',') + fracPart;
    }
    return out;
  };

  /** POST / PHP canonical: only digits, optional minus, optional period decimal — never thousand sep. */
  const formatSubmit = (value, integer, step) => {
    if (integer) return String(Math.trunc(value));
    const d = stepDecimals(step);
    return d > 0 ? value.toFixed(d) : String(value);
  };

  const destroyAll = () => {
    while (instances.length) {
      const meta = instances.pop();
      try {
        const { input, wrap, hidden } = meta;
        if (hidden && !input.name) {
          input.name = hidden.name;
          input.value = hidden.value;
          hidden.remove();
        }
        if (wrap && wrap.parentNode) {
          wrap.parentNode.insertBefore(input, wrap);
          wrap.remove();
        }
        input.classList.remove('number-spinner__input');
        delete input._numberSpinner;
      } catch (_e) {
        /* ignore */
      }
    }
  };

  const enhance = (input) => {
    if (input._numberSpinner || input.closest('.number-spinner')) return;

    const integer =
      input.getAttribute('data-integer') === '1' ||
      input.getAttribute('data-integer') === 'true';

    const step =
      parseNum(input.getAttribute('data-step') ?? input.getAttribute('step')) ??
      (integer ? 1 : 0.1);
    const min = parseNum(input.getAttribute('data-min') ?? input.getAttribute('min'));
    const max = parseNum(input.getAttribute('data-max') ?? input.getAttribute('max'));

    input.setAttribute('step', String(step));
    if (min != null) input.setAttribute('min', String(min));
    if (max != null) input.setAttribute('max', String(max));
    input.setAttribute('inputmode', integer ? 'numeric' : 'decimal');
    input.classList.add('number-spinner__input');

    const wrap = document.createElement('div');
    wrap.className = 'number-spinner';
    wrap.setAttribute('data-number-spinner-ready', '1');

    const btnDec = document.createElement('button');
    btnDec.type = 'button';
    btnDec.className = 'number-spinner__btn';
    btnDec.setAttribute('data-spinner-dec', '');
    btnDec.setAttribute('aria-label', 'Csökkentés');
    btnDec.title = 'Csökkentés';
    btnDec.innerHTML = '<i class="fa-solid fa-minus" aria-hidden="true"></i>';

    const btnInc = document.createElement('button');
    btnInc.type = 'button';
    btnInc.className = 'number-spinner__btn';
    btnInc.setAttribute('data-spinner-inc', '');
    btnInc.setAttribute('aria-label', 'Növelés');
    btnInc.title = 'Növelés';
    btnInc.innerHTML = '<i class="fa-solid fa-plus" aria-hidden="true"></i>';

    // Named hidden field always carries canonical POST value
    let hidden = null;
    if (input.name) {
      hidden = document.createElement('input');
      hidden.type = 'hidden';
      hidden.name = input.name;
      input.removeAttribute('name');
      input.setAttribute('data-spinner-display', '1');
    }

    input.parentNode.insertBefore(wrap, input);
    wrap.appendChild(btnDec);
    wrap.appendChild(input);
    wrap.appendChild(btnInc);
    if (hidden) wrap.appendChild(hidden);

    const syncSubmit = (numericOrNull) => {
      if (!hidden) return;
      if (numericOrNull == null) {
        hidden.value = '';
        return;
      }
      // Always canonical: 1234.56 — never space/comma thousand separators
      hidden.value = formatSubmit(numericOrNull, integer, step);
    };

    const read = () => parseNum(input.value);

    const write = (value) => {
      const next = clamp(integer ? Math.round(value) : roundToStep(value, step), min, max);
      input.value = formatDisplay(next, integer, step);
      syncSubmit(next);
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    };

    const nudge = (dir) => {
      const current = read();
      const base = current == null ? (min != null ? min : 0) : current;
      write(base + dir * step);
    };

    const initial = parseNum(input.value);
    if (initial != null) {
      const next = clamp(integer ? Math.round(initial) : roundToStep(initial, step), min, max);
      input.value = formatDisplay(next, integer, step);
      syncSubmit(next);
    } else {
      syncSubmit(null);
    }

    btnDec.addEventListener('click', () => nudge(-1));
    btnInc.addEventListener('click', () => nudge(1));

    input.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowUp') {
        e.preventDefault();
        nudge(1);
      } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        nudge(-1);
      }
    });

    input.addEventListener('blur', () => {
      const raw = String(input.value).trim();
      if (raw === '') {
        input.value = '';
        syncSubmit(null);
        return;
      }
      let n = parseNum(raw);
      if (n == null) {
        input.value = '';
        syncSubmit(null);
        return;
      }
      if (integer) n = Math.round(n);
      else n = roundToStep(n, step);
      write(n);
    });

    if (hidden) {
      input.addEventListener('input', () => {
        syncSubmit(read());
      });

      const form = input.closest('form');
      if (form && !form._numberSpinnerSubmitGuard) {
        form._numberSpinnerSubmitGuard = true;
        form.addEventListener('submit', () => {
          form.querySelectorAll('.number-spinner').forEach((box) => {
            const h = box.querySelector('input[type="hidden"][name]');
            const display = box.querySelector('input[data-spinner-display]');
            if (!h) return;
            const meta = display && display._numberSpinner;
            const n = parseNum(display ? display.value : h.value);
            if (n == null) {
              h.value = '';
              return;
            }
            const asInt = meta ? meta.integer : Number.isInteger(n);
            const st = meta ? meta.step : 1;
            // POST: only - digits and optional . fraction — never thousand separators
            h.value = formatSubmit(n, asInt, st);
          });
        });
      }
    }

    input.addEventListener('beforeinput', (e) => {
      if (e.inputType && e.inputType.startsWith('delete')) return;
      if (e.data == null) return;
      const loc = localePack();
      const allowed = new Set(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-']);
      if (loc.decimal) allowed.add(loc.decimal);
      if (loc.thousand) allowed.add(loc.thousand);
      // Allow period as alternate decimal while typing (normalized on blur)
      if (loc.decimal !== '.') allowed.add('.');
      for (const ch of e.data) {
        if (!allowed.has(ch) && ch !== ' ') {
          e.preventDefault();
          return;
        }
      }
    });

    const meta = { input, wrap, hidden, step, min, max, integer };
    input._numberSpinner = meta;
    instances.push(meta);
  };

  const init = (root) => {
    (root || document).querySelectorAll('input[data-number-spinner]').forEach(enhance);
  };

  const setLocale = (localeCode) => {
    const c = cfg();
    c.locale = localeCode;
    // Preserve canonical values, rebuild UI
    const snapshot = instances.map(({ input, hidden, integer }) => ({
      el: input,
      value: hidden ? hidden.value : input.value,
      integer,
    }));
    destroyAll();
    snapshot.forEach(({ el, value }) => {
      if (value != null && value !== '') el.value = value;
    });
    init();
  };

  window.JeffAdminNumberSpinner = {
    init,
    setLocale,
    getLocale: () => cfg().locale || 'hu',
    getConfig: cfg,
  };

  ready(() => init());
})(window, document);
