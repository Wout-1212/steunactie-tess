// Step tracks scroll sideways on desktop when not every step fits; arrows and edge fades appear only then.
export function initStepTracks() {
  document.querySelectorAll("[data-step-track]").forEach((track) => {
    const scroller = track.querySelector("[data-step-track-scroller]");
    const nav = track.querySelector("[data-step-track-nav]");
    const prev = track.querySelector("[data-step-track-prev]");
    const next = track.querySelector("[data-step-track-next]");
    const steps = [...scroller.querySelectorAll("li")];
    const overflowing = () => scroller.scrollWidth > scroller.clientWidth + 1;
    const update = () => {
      const scrolls = overflowing();
      const atStart = scroller.scrollLeft <= 1;
      const atEnd =
        scroller.scrollLeft + scroller.clientWidth >= scroller.scrollWidth - 1;
      nav.hidden = !scrolls;
      prev.disabled = atStart;
      next.disabled = atEnd;
      // Keyboard users can focus the row and scroll it with the arrow keys.
      if (scrolls) scroller.tabIndex = 0;
      else scroller.removeAttribute("tabindex");
      const more = !scrolls ? null : atStart ? "end" : atEnd ? "start" : "both";
      if (more) scroller.dataset.more = more;
      else delete scroller.dataset.more;
    };
    // Move a screen of steps at a time, keeping the last visible step in view.
    const page = (direction) => {
      const stride =
        steps.length > 1
          ? steps[1].getBoundingClientRect().left -
            steps[0].getBoundingClientRect().left
          : scroller.clientWidth;
      const visible = Math.max(1, Math.floor(scroller.clientWidth / stride));
      scroller.scrollBy({
        left: direction * Math.max(1, visible - 1) * stride,
        behavior: window.matchMedia("(prefers-reduced-motion: reduce)").matches
          ? "auto"
          : "smooth",
      });
    };
    prev.addEventListener("click", () => page(-1));
    next.addEventListener("click", () => page(1));
    scroller.addEventListener("scroll", update, { passive: true });
    new ResizeObserver(update).observe(scroller);

    // Open on the current step, with the step before it still in view.
    const current = scroller.querySelector('[aria-current="step"]');
    const box = scroller.getBoundingClientRect();
    if (
      current &&
      overflowing() &&
      current.getBoundingClientRect().right > box.right
    ) {
      const first = current.previousElementSibling ?? current;
      scroller.scrollLeft += first.getBoundingClientRect().left - box.left;
    }
    update();
  });
}
