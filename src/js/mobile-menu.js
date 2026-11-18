// Mobile menu: without JS the navigation is simply always visible. With JS it becomes a toggle that
// keeps keyboard focus inside the header while open and makes the rest of the page inert.
export function initMobileMenu() {
  const siteHeader = document.querySelector("[data-site-header]");
  const toggle = document.querySelector("[data-menu-toggle]");
  const nav = document.querySelector("#navigation");
  if (!toggle || !nav || !siteHeader) return;

  const desktop = window.matchMedia("(min-width: 681px)");
  const icon = toggle.querySelector("[data-menu-icon]");
  const background = [
    document.querySelector("main"),
    document.querySelector("footer"),
  ];
  let previousOverflow = "";
  const setMenu = (open, restoreFocus = false) => {
    open = open && !desktop.matches;
    const wasOpen = toggle.getAttribute("aria-expanded") === "true";
    toggle.setAttribute("aria-expanded", String(open));
    nav.classList.toggle("hidden", !open);
    if (icon) icon.textContent = open ? "×" : "☰";
    background.forEach((element) => {
      if (element) element.inert = open;
    });
    if (open && !wasOpen) {
      previousOverflow = document.body.style.overflow;
      document.body.style.overflow = "hidden";
    } else if (!open && wasOpen) {
      document.body.style.overflow = previousOverflow;
    }
    if (restoreFocus) toggle.focus();
  };
  nav.dataset.enhanced = "true";
  toggle.hidden = false;
  setMenu(false);
  toggle.addEventListener("click", () =>
    setMenu(toggle.getAttribute("aria-expanded") !== "true"),
  );
  nav.addEventListener("click", (event) => {
    if (event.target.closest("a[href]")) setMenu(false);
  });
  desktop.addEventListener("change", () => setMenu(false));
  window.addEventListener("pageshow", () => setMenu(false));
  document.addEventListener("keydown", (event) => {
    if (toggle.getAttribute("aria-expanded") !== "true") return;
    if (event.key === "Escape") setMenu(false, true);
    if (event.key === "Tab") {
      const controls = [
        ...siteHeader.querySelectorAll("a[href], button, summary"),
      ].filter((element) => element.getClientRects().length);
      const first = controls[0],
        last = controls[controls.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }
  });
}
