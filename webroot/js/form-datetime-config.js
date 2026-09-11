/**
 * JeffAdmin date/time field config (flatpickr).
 *
 * - displayFormats[locale]: what the user sees / types
 * - submitFormats: always posted to the server (locale-independent)
 * - locale: active UI language (change via JeffAdminDateTime.setLocale)
 *
 * Datetime: UI shows date + hour:minute; POST includes seconds (default :00).
 */
window.JeffAdminDateTimeConfig = {
  locale: 'hu',

  /** Visible formats per locale (flatpickr tokens). */
  displayFormats: {
    hu: {
      date: 'Y.m.d.',
      time: 'H:i',
      datetime: 'Y.m.d. H:i',
    },
    en: {
      date: 'Y-m-d',
      time: 'H:i',
      datetime: 'Y-m-d H:i',
    },
  },

  /** Placeholders per locale (optional). */
  placeholders: {
    hu: {
      date: 'ÉÉÉÉ.HH.NN.',
      time: 'ÓÓ:PP',
      datetime: 'ÉÉÉÉ.HH.NN. ÓÓ:PP',
    },
    en: {
      date: 'YYYY-MM-DD',
      time: 'HH:MM',
      datetime: 'YYYY-MM-DD HH:MM',
    },
  },

  /**
   * Values written into the named input (form POST).
   * Independent of locale — do not change without backend agreement.
   */
  submitFormats: {
    date: 'Y-m-d',
    time: 'H:i',
    datetime: 'Y-m-d H:i:S',
  },

  /**
   * JS fallback, ha az input value üres.
   * Elsődleges forrás: PHP value attribútum (submit formátumban).
   * Általában null — az induló értéket a PHP adja.
   * Keywords: 'today' | 'now' | null | submit string
   * Mezőnként: data-fp-default="today"
   */
  defaults: {
    date: null,
    time: null,
    datetime: null,
  },

  /** Common flatpickr options (overridable). */
  flatpickr: {
    allowInput: true,
    disableMobile: true,
    time_24hr: true,
    /** Seconds in POST for datetime, but not shown in the picker UI. */
    enableSeconds: false,
  },
};
