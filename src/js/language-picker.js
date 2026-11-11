// Language picker in the header: a <details> disclosure that closes when focus or a click leaves it.
export function initLanguagePicker() {
  const languagePicker = document.querySelector("[data-language-picker]");
  if (!languagePicker) return;

  const closeLanguagePicker = () => {
    languagePicker.open = false;
  };
  document.addEventListener("click", (event) => {
    if (!languagePicker.contains(event.target)) closeLanguagePicker();
  });
  languagePicker.addEventListener("focusout", (event) => {
    if (!languagePicker.contains(event.relatedTarget)) closeLanguagePicker();
  });
  // Close only the language disclosure before Escape reaches the mobile menu.
  languagePicker.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && languagePicker.open) {
      event.preventDefault();
      event.stopPropagation();
      closeLanguagePicker();
      languagePicker.querySelector("summary").focus();
    }
  });
  window.addEventListener("resize", closeLanguagePicker);
  window.addEventListener("pageshow", closeLanguagePicker);
}
