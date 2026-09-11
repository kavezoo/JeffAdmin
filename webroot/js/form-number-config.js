/**
 * JeffAdmin number field locale config (spinner / numeric inputs).
 *
 * - locale: active UI region (change via JeffAdminNumberSpinner.setLocale)
 * - locales[code]: thousand + decimal separators for display
 * - POST / PHP values always use period decimal, no thousand grouping (e.g. 1234.56)
 *   Ezres elválasztó soha nem kerül a POST-ba — csak számjegyek és opcionális tizedespont.
 */
window.JeffAdminNumberConfig = {
  locale: 'hu',

  locales: {
    hu: {
      decimal: ',',
      thousand: ' ',
      grouping: 3,
    },
    en: {
      decimal: '.',
      thousand: ',',
      grouping: 3,
    },
    de: {
      decimal: ',',
      thousand: '.',
      grouping: 3,
    },
  },
};
