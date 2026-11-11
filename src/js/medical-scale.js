import { animate, motionAllowed } from "./motion.js";

// The medical scale starts level and tilts to its position once in view. The pans hang from the beam
// ends, so they move with them; treatment.php renders the end position with the same numbers.
export function initMedicalScale() {
  if (!motionAllowed) return;

  const scale = document.querySelector("[data-scale]");
  if (!scale || !Number(scale.dataset.scale)) return;

  const tilt = Number(scale.dataset.scale);
  const length = Number(scale.dataset.scaleBeam);
  const [beam, done, planned] = ["beam", "done", "planned"].map((part) =>
    scale.querySelector(`[data-scale-part="${part}"]`),
  );
  const position = (angle) => {
    const radians = (angle * Math.PI) / 180;
    const x = length * (1 - Math.cos(radians));
    const y = length * Math.sin(radians);
    beam.setAttribute("transform", `rotate(${angle} ${scale.dataset.scalePivot})`);
    done.setAttribute("transform", `translate(${x} ${-y})`);
    planned.setAttribute("transform", `translate(${-x} ${y})`);
  };
  position(0);
  const tiltScale = new IntersectionObserver(
    (entries) => {
      if (!entries.some((entry) => entry.isIntersecting)) return;
      tiltScale.disconnect();
      animate((eased) => position(tilt * eased));
    },
    { threshold: 0.6 },
  );
  tiltScale.observe(scale);
}
