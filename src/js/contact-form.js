// Contact form: sent in the background with fetch, so the visitor stays on the page. Without JS the
// form posts normally and the server redirects back to #contact (plugins/tess-contact).
export function initContactForm() {
  document
    .querySelector("[data-contact-form]")
    ?.addEventListener("submit", async (event) => {
      event.preventDefault();
      const form = event.currentTarget;
      if (!form.reportValidity()) return;
      const copy = JSON.parse(form.dataset.contactCopy);
      const status = form.querySelector("[data-contact-status]");
      const button = form.querySelector('button[type="submit"]');
      button.disabled = true;
      status.textContent = copy.sending;
      try {
        const response = await fetch(form.action, {
          method: "POST",
          body: new FormData(form),
          headers: { Accept: "application/json" },
        });
        const result = await response.json();
        status.textContent = result.message;
        if (result.ok) form.reset();
      } catch {
        status.textContent = copy.error;
      }
      button.disabled = false;
    });
}
