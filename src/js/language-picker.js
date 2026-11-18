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
  // Only when focus moves to another element (Tab). A mouse press on something that can't take focus
  // (the dropdown's edge, a greyed-out language) blurs to nothing; closing the <details> during that
  // mousedown crashes the tab in Chrome. Clicks outside are handled by the click listener above.
  languagePicker.addEventListener("focusout", (event) => {
    if (event.relatedTarget && !languagePicker.contains(event.relatedTarget)) closeLanguagePicker();
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
