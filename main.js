const menuToggle = document.querySelector(".menu-toggle");
const siteNav = document.querySelector(".site-nav");

if (menuToggle && siteNav) {
  menuToggle.addEventListener("click", () => {
    const isOpen = siteNav.classList.toggle("is-open");
    menuToggle.setAttribute("aria-expanded", String(isOpen));
  });

  siteNav.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      siteNav.classList.remove("is-open");
      menuToggle.setAttribute("aria-expanded", "false");
    });
  });
}

const form = document.querySelector("#contact-form");
const message = document.querySelector("#form-message");

if (form && message) {
  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    const button = form.querySelector("button[type='submit']");
    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = "Enviando...";
    message.textContent = "";
    message.className = "form-message";

    try {
      const response = await fetch(form.action, {
        method: "POST",
        body: new FormData(form)
      });
      const data = await response.json();
      if (!response.ok || !data.success) {
        throw new Error(data.message || "No se pudo enviar el mensaje.");
      }
      message.textContent = data.message || "Gracias. Te contactaremos pronto.";
      message.classList.add("success");
      form.reset();
    } catch (error) {
      message.textContent = "No se pudo enviar desde el formulario. Escríbenos a contacto@impulsoestrategicocx.com.";
      message.classList.add("error");
    } finally {
      button.disabled = false;
      button.textContent = originalText;
    }
  });
}
