(function () {
  const storageKey = "kioskLanguage";
  const defaultLanguage = "en";
  const resources = window.kioskTranslations;

  function readLanguage() {
    try {
      const language = sessionStorage.getItem(storageKey);
      return Object.hasOwn(resources, language) ? language : defaultLanguage;
    } catch {
      return defaultLanguage;
    }
  }

  const i18n = window.i18next;
  if (i18n) {
    i18n.init({
      resources,
      lng: readLanguage(),
      fallbackLng: defaultLanguage,
      supportedLngs: Object.keys(resources),
      initAsync: false,
      interpolation: { escapeValue: false },
    });
  }

  window.kioskT = function (key, values = {}) {
    if (i18n) return i18n.t(key, values);

    const translations = resources.en.translation;
    const pluralKey = values.count === 1 ? `${key}_one` : `${key}_other`;
    const text = (values.count === undefined ? undefined : translations[pluralKey]) ?? translations[key] ?? key;
    return text.replace(/{{(\w+)}}/g, (match, name) => values[name] ?? match);
  };

  function renderLanguage() {
    document.documentElement.lang = i18n.resolvedLanguage;
    document.querySelectorAll("[data-i18n]").forEach((element) => {
      element.textContent = kioskT(element.dataset.i18n);
    });
    for (const attribute of ["aria-label", "placeholder"]) {
      document.querySelectorAll(`[data-i18n-${attribute}]`).forEach((element) => {
        element.setAttribute(attribute, kioskT(element.getAttribute(`data-i18n-${attribute}`)));
      });
    }
    document.querySelectorAll("[data-kiosk-language]").forEach((button) => {
      const active = button.dataset.kioskLanguage === i18n.resolvedLanguage;
      button.setAttribute("aria-pressed", String(active));
    });
  }

  if (i18n) {
    document.addEventListener("DOMContentLoaded", () => {
      renderLanguage();
      document.querySelectorAll("[data-kiosk-language]").forEach((button) => {
        button.disabled = false;
        button.addEventListener("click", async () => {
          const language = button.dataset.kioskLanguage;
          try {
            sessionStorage.setItem(storageKey, language);
          } catch {
            // Switching still works on the current page when storage is unavailable.
          }
          await i18n.changeLanguage(language);
          renderLanguage();
        });
      });
    });

    window.addEventListener("pageshow", async (event) => {
      if (!event.persisted || readLanguage() === i18n.resolvedLanguage) return;
      await i18n.changeLanguage(readLanguage());
      renderLanguage();
    });
  }
})();
