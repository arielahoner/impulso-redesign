/* =========================================================
   Google Analytics 4 — Impulso Estratégico CX & AI

   ▸ PASO ÚNICO: pega tu Measurement ID entre las comillas.
     Lo encuentras en Analytics → Administrar → Flujos de datos → Web.
     Tiene el formato G-XXXXXXXXXX.

   Mientras esté vacío, el sitio NO carga Google Analytics
   (no se envía ningún dato y no aparecen cookies de terceros).
   ========================================================= */
(function () {
  "use strict";

  var GA4_ID = "";

  if (!GA4_ID || GA4_ID.indexOf("G-") !== 0) return;

  var s = document.createElement("script");
  s.async = true;
  s.src = "https://www.googletagmanager.com/gtag/js?id=" + encodeURIComponent(GA4_ID);
  document.head.appendChild(s);

  window.dataLayer = window.dataLayer || [];
  function gtag() { window.dataLayer.push(arguments); }
  window.gtag = gtag;

  gtag("js", new Date());
  gtag("config", GA4_ID, { anonymize_ip: true });
})();
