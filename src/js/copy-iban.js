// IBAN copy is progressive enhancement; the number is always visible/selectable.
export function initCopyIban() {
  document.querySelectorAll("[data-copy-iban]").forEach((button) => {
    button.addEventListener("click", async () => {
      const status = button
        .closest("[data-bank-account]")
        .querySelector("[data-bank-status]");
      try {
        if (navigator.clipboard?.writeText)
          await navigator.clipboard.writeText(button.dataset.copyIban);
        else {
          const input = document.createElement("textarea");
          input.value = button.dataset.copyIban;
          input.style.position = "fixed";
          input.style.opacity = "0";
          document.body.append(input);
          input.select();
          let copied;
          try {
            copied = document.execCommand("copy");
          } finally {
            input.remove();
            button.focus();
          }
          if (!copied) throw new Error("Copy unavailable");
        }
        status.textContent = button.dataset.copied;
      } catch {
        status.textContent = button.dataset.copyFailed;
      }
    });
  });
}
