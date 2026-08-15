/* =========================================================
   Impulso Estratégico CX & AI — scripts compartidos
   ========================================================= */
(function () {
  "use strict";

  /* ---------- Analítica (no-op si GA4 no está configurado) ---------- */
  function track(name, params) {
    if (typeof window.gtag === "function") window.gtag("event", name, params || {});
  }

  /* ---------- Tema claro / oscuro ---------- */
  var root = document.documentElement;
  function setTheme(t) {
    if (t === "dark") root.classList.add("dark");
    else root.classList.remove("dark");
    try { localStorage.setItem("impulso-theme", t); } catch (e) {}
  }
  window.toggleTheme = function () {
    setTheme(root.classList.contains("dark") ? "light" : "dark");
  };
  document.querySelectorAll("[data-theme-toggle]").forEach(function (b) {
    b.addEventListener("click", window.toggleTheme);
  });

  /* ---------- Menú móvil ---------- */
  var navBtn = document.querySelector("[data-nav-toggle]");
  var navMobile = document.querySelector("[data-nav-mobile]");
  if (navBtn && navMobile) {
    navBtn.addEventListener("click", function () {
      var open = navMobile.classList.toggle("open");
      navBtn.setAttribute("aria-expanded", open ? "true" : "false");
    });
    navMobile.querySelectorAll("a").forEach(function (a) {
      a.addEventListener("click", function () {
        navMobile.classList.remove("open");
        navBtn.setAttribute("aria-expanded", "false");
      });
    });
  }

  /* ---------- Toast ---------- */
  function toast(msg, kind) {
    var el = document.createElement("div");
    el.className = "toast " + (kind || "");
    var icon = kind === "ok"
      ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>'
      : kind === "err"
      ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>'
      : "";
    el.innerHTML = icon + "<span></span>";
    el.querySelector("span").textContent = msg;
    document.body.appendChild(el);
    requestAnimationFrame(function () { el.classList.add("show"); });
    setTimeout(function () {
      el.classList.remove("show");
      setTimeout(function () { el.remove(); }, 300);
    }, 4200);
  }
  window.impulsoToast = toast;

  /* ---------- Eventos de conversión ---------- */
  document.querySelectorAll('a[href^="https://wa.me/"]').forEach(function (a) {
    a.addEventListener("click", function () { track("whatsapp_click", { location: document.title }); });
  });
  document.querySelectorAll('a[href^="mailto:"]').forEach(function (a) {
    a.addEventListener("click", function () { track("email_click", { email: a.getAttribute("href").replace("mailto:", "") }); });
  });
  document.querySelectorAll('a[href^="tel:"]').forEach(function (a) {
    a.addEventListener("click", function () { track("phone_click", {}); });
  });
  document.querySelectorAll("[data-cta]").forEach(function (a) {
    a.addEventListener("click", function () { track("cta_click", { cta: a.getAttribute("data-cta") }); });
  });

  /* ---------- Formulario de contacto (Resend vía /api/contacto.php) ---------- */
  var form = document.querySelector("[data-contact-form]");
  if (form) {
    var btn = form.querySelector("[data-submit]");
    var btnLabel = btn ? btn.querySelector("[data-label]") : null;
    var defaultLabel = btnLabel ? btnLabel.textContent : "Enviar";

    form.addEventListener("submit", function (e) {
      e.preventDefault();

      var name = (form.name.value || "").trim();
      var company = (form.company.value || "").trim();
      var email = (form.email.value || "").trim();
      var message = (form.message.value || "").trim();
      var emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

      if (!name) return toast("Tu nombre es requerido", "err");
      if (!company) return toast("El nombre de la empresa es requerido", "err");
      if (!emailOk) return toast("Ingresa un email válido", "err");
      if (message.length < 10) return toast("Cuéntanos un poco más sobre tu caso", "err");

      var code = form.phone_code ? form.phone_code.value : "";
      var num = form.phone_number ? form.phone_number.value.trim() : "";
      var phoneField = form.querySelector('input[name="phone"]');
      if (phoneField) phoneField.value = num ? code + " " + num : "";

      if (btn) btn.disabled = true;
      if (btnLabel) btnLabel.textContent = "Enviando…";

      fetch(form.getAttribute("action") || "api/contacto.php", {
        method: "POST",
        headers: { "Accept": "application/json" },
        body: new FormData(form)
      })
        .then(function (r) { return r.json().catch(function () { return { ok: false }; }); })
        .then(function (data) {
          if (data && data.ok) {
            track("generate_lead", { form: "contacto" });
            toast("¡Recibido! Te contactaremos en menos de 24 h.", "ok");
            form.reset();
          } else {
            toast((data && data.error) || "No pudimos enviar tu mensaje. Intenta de nuevo.", "err");
          }
        })
        .catch(function () {
          toast("No pudimos enviar tu mensaje. Revisa tu conexión.", "err");
        })
        .finally(function () {
          if (btn) btn.disabled = false;
          if (btnLabel) btnLabel.textContent = defaultLabel;
        });
    });
  }

  /* ---------- Año dinámico en footer ---------- */
  var y = document.querySelector("[data-year]");
  if (y) y.textContent = new Date().getFullYear();
})();
