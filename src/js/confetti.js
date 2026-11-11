// Confetti on the thank-you page after a paid donation: once per donation (not again on reload),
// in the site colours, and not at all for visitors who prefer reduced motion.
const colours = ["#C43B75", "#F5D7E6", "#FFD84D", "#C8EEF3", "#E86FA3"];
const random = (min, max) => min + Math.random() * (max - min);

export function initConfetti() {
  const host = document.querySelector("[data-confetti]");
  if (!host) return;

  let seen = false;
  const key = `tess-confetti-${host.dataset.confetti}`;
  try {
    seen = sessionStorage.getItem(key) !== null;
    sessionStorage.setItem(key, "1");
  } catch {
    // Storage blocked: the confetti simply plays on every visit.
  }
  if (seen || window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  const canvas = document.createElement("canvas");
  canvas.setAttribute("aria-hidden", "true");
  canvas.style.cssText = "position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:60";
  document.body.append(canvas);
  const context = canvas.getContext("2d");
  const ratio = window.devicePixelRatio || 1;
  const resize = () => {
    canvas.width = innerWidth * ratio;
    canvas.height = innerHeight * ratio;
    context.setTransform(ratio, 0, 0, ratio, 0, 0);
  };
  resize();
  window.addEventListener("resize", resize);

  // Two bursts from the lower corners, aimed up and towards the middle. The launch speed grows with
  // the window height, so the fastest pieces reach close to the top on any screen.
  const launch = 13 + innerHeight * 0.0185;
  const pieces = Array.from({ length: 240 }, (_, index) => {
    const left = index % 2 === 0;
    const angle = (left ? random(-84, -58) : random(-122, -96)) * (Math.PI / 180);
    const speed = launch * random(0.7, 1.05);
    return {
      x: left ? 0 : innerWidth,
      y: innerHeight * 0.95,
      vx: Math.cos(angle) * speed,
      vy: Math.sin(angle) * speed,
      size: random(10, 18),
      colour: colours[index % colours.length],
      spin: random(0, Math.PI * 2),
      spinSpeed: random(-0.25, 0.25),
      heart: index % 9 === 0,
    };
  });

  const heart = (size) => {
    const s = size / 2;
    context.beginPath();
    context.moveTo(0, s * 0.6);
    context.bezierCurveTo(-s * 1.4, -s * 0.3, -s * 0.5, -s * 1.3, 0, -s * 0.4);
    context.bezierCurveTo(s * 0.5, -s * 1.3, s * 1.4, -s * 0.3, 0, s * 0.6);
    context.fill();
  };

  const start = performance.now();
  const frame = (now) => {
    const elapsed = now - start;
    context.clearRect(0, 0, innerWidth, innerHeight);
    // Fade out over the last 1.5 seconds, then clean up.
    context.globalAlpha = Math.min(1, Math.max(0, (6500 - elapsed) / 1500));
    pieces.forEach((piece) => {
      piece.vx *= 0.985;
      piece.vy = piece.vy * 0.985 + 0.28;
      piece.x += piece.vx;
      piece.y += piece.vy;
      piece.spin += piece.spinSpeed;
      context.save();
      context.translate(piece.x, piece.y);
      context.rotate(piece.spin);
      context.fillStyle = piece.colour;
      if (piece.heart) heart(piece.size * 1.8);
      else context.fillRect(-piece.size / 2, -piece.size / 4, piece.size, piece.size / 2);
      context.restore();
    });
    if (elapsed < 6500) requestAnimationFrame(frame);
    else {
      canvas.remove();
      window.removeEventListener("resize", resize);
    }
  };
  requestAnimationFrame(frame);
}
