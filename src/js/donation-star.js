import { motionAllowed } from "./motion.js";

// Donation buttons get a star that laps their rounded border, trailing light behind it.
// SMIL runs each lap natively. JavaScript decides when laps start (once by itself when the button
// is first in view, then only on hover/focus) and rebuilds the SVG for each new lap or resize,
// from a random corner in a random direction. Movement that starts by itself stays under five
// seconds per button, so it needs no pause button (WCAG 2.2.2).

// At most this many stars lap on screen at once, so they never compete for attention.
const maxLaps = 2;
// [length, width] per trail layer. Each layer is shorter and wider than the one beneath,
// so the stack tapers to a point and brightens towards the star.
const trails = Array.from({ length: 7 }, (_, i) => [50 - i * 6, 1 + i * 0.4]);
// Ease in and out of each lap, like a shooting star.
const easing = { calcMode: "spline", keyTimes: "0;1", keySplines: "0.5 0 0.5 1" };
const starShape = "M 0 -9.5 L 2.28 -2.66 L 9.5 0 L 2.28 2.66 L 0 9.5 L -2.28 2.66 L -9.5 0 L -2.28 -2.66 Z";

const make = (name, attrs = {}) => {
  const node = document.createElementNS("http://www.w3.org/2000/svg", name);
  Object.entries(attrs).forEach(([key, value]) => node.setAttribute(key, value));
  return node;
};
const random = (min, max) => min + Math.random() * (max - min);

export function initDonationStars() {
  if (!motionAllowed) return;

  const motion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const orbits = [];
  // Stars outside the viewport (or in hidden buttons) pause instead of repainting unseen.
  const onScreen = new IntersectionObserver((entries) =>
    entries.forEach(({ target, isIntersecting }) =>
      isIntersecting ? target.unpauseAnimations() : target.pauseAnimations(),
    ),
  );

  document.querySelectorAll(".donation-light").forEach((button, index) => {
    const svg = make("svg", { class: "donation-orbit", "aria-hidden": "true", focusable: "false" });
    button.append(svg);
    onScreen.observe(svg);
    // Fade in and out included, a lap stays under five seconds.
    const lap = random(4, 4.8);
    // The latest lap, timed on this SVG's own clock, which stops while the star is off screen.
    let current = null;
    const running = () => current !== null && svg.getCurrentTime() - current.start < lap;
    orbits.push({ lapping: () => running() && !svg.animationsPaused() });

    let previous = "";
    const draw = () => {
      const style = getComputedStyle(button);
      const box = getComputedStyle(svg);
      const width = parseFloat(box.width);
      const height = parseFloat(box.height);
      // The SVG origin is the padding edge; the border centre lies half a border outside it.
      const left = -parseFloat(style.borderLeftWidth) / 2;
      const top = -parseFloat(style.borderTopWidth) / 2;
      const right = width + parseFloat(style.borderRightWidth) / 2;
      const bottom = height + parseFloat(style.borderBottomWidth) / 2;
      const r = Math.min(
        Math.max(0, parseFloat(style.borderTopLeftRadius) + top),
        (bottom - top) / 2,
        (right - left) / 2,
      );
      const signature = [width, height, left, top, right, bottom, r, motion.matches, current?.start].join();
      if (signature === previous) return;
      previous = signature;
      svg.replaceChildren();
      if (motion.matches || !width || !height || !current) return;
      svg.setAttribute("viewBox", `0 0 ${width} ${height}`);

      // Where each straight edge meets a corner arc, clockwise from the top-left.
      const points = [
        [left + r, top], [right - r, top], [right, top + r], [right, bottom - r],
        [right - r, bottom], [left + r, bottom], [left, bottom - r], [left, top + r],
      ];
      const { start, corner, clockwise } = current;
      let d = `M ${points[corner]}`;
      for (let i = 1; i <= 8; i++) {
        const to = (corner + (clockwise ? i : -i) + 8) % 8;
        // Clockwise, every segment ending on an even point is an arc; anticlockwise, an odd one.
        d += (to % 2 === 0) === clockwise ? ` A ${r} ${r} 0 0 ${+clockwise} ${points[to]}` : ` L ${points[to]}`;
      }
      d += " Z";
      const length = 2 * (right - left + bottom - top) - 8 * r + 2 * Math.PI * r;
      // The star is drawn for a 54px button; smaller buttons get a smaller star and trail.
      const size = Math.min(1, Math.max(0.6, (bottom - top) / 54));
      const starSize = size * 1.25;
      const timing = { begin: `${start}s`, dur: `${lap}s` };

      // Allow the trail to straddle the border; never clip the star or its glow.
      const clipId = `donation-trail-clip-${index}`;
      const clip = make("clipPath", { id: clipId, clipPathUnits: "userSpaceOnUse" });
      clip.append(make("rect", { x: left - 2, y: top - 2, width: right - left + 4, height: bottom - top + 4, rx: r + 2 }));
      const defs = make("defs");
      defs.append(clip);
      // Hidden between laps; fades in as a lap starts and out as it ends.
      const group = make("g", { opacity: 0 });
      group.append(make("animate", { ...timing, attributeName: "opacity", values: "0;1;1;0", keyTimes: "0;0.08;0.9;1" }));
      const trail = make("g", { "clip-path": `url(#${clipId})` });
      trails.forEach(([tail, strokeWidth]) => {
        const dash = tail * size;
        const path = make("path", { d, fill: "none", stroke: "currentColor", "stroke-width": strokeWidth * size, "stroke-linecap": "round", "stroke-opacity": 0.25, "stroke-dasharray": `${dash} ${length - dash}`, "stroke-dashoffset": dash });
        path.append(make("animate", { ...timing, ...easing, attributeName: "stroke-dashoffset", values: `${dash};${dash - length}` }));
        trail.append(path);
      });
      const star = make("g");
      const shape = make("path", { d: starShape, fill: "currentColor" });
      // Set through the style property, not a style attribute: the Content Security Policy blocks those.
      shape.style.filter = `drop-shadow(0 0 ${3 * size}px color-mix(in srgb, currentColor 67%, transparent))`;
      // Twinkle constantly, and turn a quarter in the direction of travel over each lap.
      shape.append(make("animateTransform", { attributeName: "transform", type: "scale", values: `${starSize};${starSize * 0.85};${starSize}`, dur: "0.8s", repeatCount: "indefinite" }));
      shape.append(make("animateTransform", { ...timing, attributeName: "transform", type: "rotate", from: 0, to: clockwise ? 90 : -90, additive: "sum" }));
      star.append(shape, make("animateMotion", { ...timing, ...easing, path: d, keyPoints: "0;1" }));
      group.append(trail, star);
      svg.append(defs, group);
    };

    const startLap = () => {
      current = { start: svg.getCurrentTime(), corner: 2 * Math.floor(random(0, 4)), clockwise: Math.random() < 0.5 };
      draw();
    };
    let timer;
    // The one lap that starts by itself.
    const schedule = (seconds) => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        // Try again shortly if the star is mid-lap, unseen, or enough stars are already out.
        if (motion.matches || running() || svg.animationsPaused() || orbits.filter((orbit) => orbit.lapping()).length >= maxLaps) {
          return schedule(random(1, 3));
        }
        startLap();
      }, seconds * 1000);
    };
    // Hovering or focusing a button sends its star round straight away (instead of the automatic lap).
    const greet = () => {
      if (motion.matches || running()) return;
      clearTimeout(timer);
      startLap();
    };
    button.addEventListener("mouseenter", greet);
    button.addEventListener("focus", greet);
    new ResizeObserver(draw).observe(button);
    motion.addEventListener("change", draw);
    schedule(random(1, 10));
  });
}
