import { animate, motionAllowed } from "./motion.js";

// Numbers count up from zero once they scroll into view, and the donation bar fills once.
export function initCountUp() {
  if (!motionAllowed) return;

  // The server renders the final formatted value (currency, separators, "%"), so only its digits are
  // swapped and the result ends on exactly the original text.
  const counters = new Map();
  document.querySelectorAll("[data-count-up]").forEach((element) => {
    const text = element.textContent;
    const match = text.match(/\d+(?:[^\w]\d{3})*/);
    if (!match) return;
    const target = Number(match[0].replace(/\D/g, ""));
    const separator = match[0].match(/\D/)?.[0] ?? "";
    const render = (value) => {
      const digits = String(Math.round(value)).replace(
        /\B(?=(\d{3})+$)/g,
        separator,
      );
      element.textContent =
        text.slice(0, match.index) +
        digits +
        text.slice(match.index + match[0].length);
    };
    render(0);
    counters.set(element, () => {
      counters.delete(element);
      countUp.unobserve(element);
      // Reserve the final width so surrounding text does not shift while counting.
      element.textContent = text;
      element.style.minWidth = `${element.getBoundingClientRect().width}px`;
      animate((eased) => {
        render(target * eased);
        if (eased === 1) {
          element.textContent = text;
          element.style.removeProperty("min-width");
        }
      });
    });
  });
  const countUp = new IntersectionObserver(
    (entries) =>
      entries
        .filter((entry) => entry.isIntersecting)
        .forEach((entry) => counters.get(entry.target)?.()),
    { rootMargin: "0px 0px -8% 0px" },
  );
  counters.forEach((_, element) => countUp.observe(element));

  const donationBar = document.querySelector("[data-donation-bar]");
  if (!donationBar) return;

  const fill = donationBar.matches("progress")
    ? null
    : donationBar.querySelector("span");
  const finalValue = fill ? null : donationBar.value;
  if (fill) fill.style.width = "0%";
  else donationBar.value = 0;
  // The amounts above the bar count up together with it.
  const section = donationBar.closest("section");
  const sectionCounters = [...counters.keys()].filter((element) =>
    section?.contains(element),
  );
  sectionCounters.forEach((element) => countUp.unobserve(element));
  const fillBar = new IntersectionObserver(
    (entries) => {
      if (!entries.some((entry) => entry.isIntersecting)) return;
      fillBar.disconnect();
      sectionCounters.forEach((element) => counters.get(element)?.());
      if (fill) {
        requestAnimationFrame(() => fill.style.removeProperty("width"));
        return;
      }
      animate((eased) => (donationBar.value = finalValue * eased));
    },
    { threshold: 0.6 },
  );
  fillBar.observe(donationBar);
}
