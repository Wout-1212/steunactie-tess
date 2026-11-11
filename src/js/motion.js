// Shared by the scroll animations. Motion only runs for visitors who haven't asked their system for
// reduced motion, and only in browsers that can tell when something scrolls into view.
export const motionAllowed =
  window.matchMedia("(prefers-reduced-motion: no-preference)").matches &&
  "IntersectionObserver" in window;

// Calls back with an eased 0→1 value on every frame for 1.8s.
export const animate = (onFrame) => {
  const start = performance.now();
  const step = (now) => {
    const progress = Math.min((now - start) / 1800, 1);
    onFrame(1 - Math.pow(1 - progress, 3));
    if (progress < 1) requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
};
