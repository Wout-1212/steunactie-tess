import { motionAllowed } from "./motion.js";

// Content below the fold fades in on scroll. Section content fades in as a whole; children of a
// [data-reveal-group] fade in one by one. Content already in view on load is left alone.
export function initReveal() {
  if (!motionAllowed) return;

  const targets = [
    ...document.querySelectorAll(
      "main > section:not([data-hero]) > div > :not([data-reveal-group], [data-no-reveal])",
    ),
    ...document.querySelectorAll(
      "[data-reveal-group] > :not([data-reveal-group])",
    ),
  ].filter(
    (element) =>
      element.getBoundingClientRect().top > window.innerHeight * 0.92,
  );
  const reveal = new IntersectionObserver(
    (entries) => {
      entries
        .filter((entry) => entry.isIntersecting)
        .forEach((entry, index) => {
          const element = entry.target;
          // Items entering together appear one after another.
          element.style.setProperty(
            "--reveal-delay",
            `${Math.min(index, 4) * 70}ms`,
          );
          element.classList.add("is-visible");
          reveal.unobserve(element);
          const cleanUp = (event) => {
            if (
              event &&
              (event.target !== element || event.propertyName !== "opacity")
            )
              return;
            element.classList.remove("reveal", "is-visible");
            element.style.removeProperty("--reveal-delay");
            element.removeEventListener("transitionend", cleanUp);
          };
          element.addEventListener("transitionend", cleanUp);
          setTimeout(cleanUp, 1200);
        });
    },
    { rootMargin: "0px 0px -8% 0px" },
  );
  targets.forEach((element) => {
    element.classList.add("reveal");
    reveal.observe(element);
  });
}
