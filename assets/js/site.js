const languagePicker = document.querySelector("[data-language-picker]");
if (languagePicker) {
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
const siteHeader = document.querySelector("[data-site-header]");
if (siteHeader) {
  // Become opaque as soon as the first visible content reaches the sticky header.
  // Keep it opaque after that boundary to avoid flashing between sections.
  const headerContent = [
    ...document.querySelectorAll(
      "main h1, main h2, main p, main img, main [data-photo-placeholder], main [data-update-card]",
    ),
  ];
  const updateHeader = () => {
    const contentTop = headerContent.reduce((first, element) => {
      if (!element.getClientRects().length) return first;
      return Math.min(
        first,
        element.getBoundingClientRect().top + window.scrollY,
      );
    }, Infinity);
    const headerBottom = siteHeader.getBoundingClientRect().bottom;
    const scrolled = Number.isFinite(contentTop)
      ? window.scrollY > 0 && contentTop <= window.scrollY + headerBottom + 2
      : window.scrollY > 12;
    if (siteHeader.dataset.scrolled !== String(scrolled)) {
      siteHeader.dataset.scrolled = String(scrolled);
    }
  };
  // Exposed on the root so the mobile menu and the hero (which sits behind the header) can use it.
  const sizeHeader = () => {
    document.documentElement.style.setProperty(
      "--header-height",
      `${siteHeader.offsetHeight}px`,
    );
    updateHeader();
  };
  sizeHeader();
  new ResizeObserver(sizeHeader).observe(siteHeader);
  updateHeader();
  window.addEventListener("scroll", updateHeader, { passive: true });
  window.addEventListener("resize", updateHeader);
  window.addEventListener("pageshow", updateHeader);
  window.addEventListener("load", updateHeader);
  document.fonts?.ready.then(updateHeader);
}
const toggle = document.querySelector("[data-menu-toggle]");
const nav = document.querySelector("#navigation");
if (toggle && nav && siteHeader) {
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
    if (event.target.closest("a")) setMenu(false);
  });
  desktop.addEventListener("change", () => setMenu(false));
  window.addEventListener("pageshow", () => setMenu(false));
  document.addEventListener("keydown", (event) => {
    if (toggle.getAttribute("aria-expanded") !== "true") return;
    if (event.key === "Escape") setMenu(false, true);
    if (event.key === "Tab") {
      const controls = [
        ...siteHeader.querySelectorAll("a, button, summary"),
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
const amountInput = document.querySelector("#amount");
const amountButtons = document.querySelectorAll("[data-amount]");
amountButtons.forEach((button) =>
  button.addEventListener("click", () => {
    amountInput.value = button.dataset.amount;
    amountButtons.forEach((other) =>
      other.setAttribute("aria-pressed", String(other === button)),
    );
  }),
);
amountInput?.addEventListener("input", () =>
  amountButtons.forEach((button) =>
    button.setAttribute(
      "aria-pressed",
      String(button.dataset.amount === amountInput.value),
    ),
  ),
);
// Use the native share sheet where available (mostly phones), otherwise open the share dialog.
const shareButton = document.querySelector("[data-share]");
const shareDialog = document.querySelector("[data-share-dialog]");
shareButton?.addEventListener("click", async () => {
  if (navigator.share) {
    try {
      await navigator.share({
        title: shareButton.dataset.shareTitle,
        url: shareButton.dataset.shareUrl,
      });
      return;
    } catch (error) {
      if (error.name === "AbortError") return;
    }
  }
  shareDialog.querySelector("[data-share-status]").textContent = "";
  shareDialog.showModal();
});
shareDialog?.addEventListener("click", (event) => {
  if (event.target === shareDialog) shareDialog.close();
});
shareDialog
  ?.querySelector("[data-share-copy]")
  .addEventListener("click", async () => {
    const input = shareDialog.querySelector("#share-link");
    const status = shareDialog.querySelector("[data-share-status]");
    input.select();
    // The Clipboard API only exists on https/localhost; fall back to the selected input elsewhere.
    try {
      await navigator.clipboard.writeText(input.value);
    } catch {
      document.execCommand("copy");
    }
    status.textContent = status.dataset.copied;
  });

// Photo lightbox on update pages. Without JS the thumbnails simply link to the large image.
const lightbox = document.querySelector("[data-lightbox]");
if (lightbox) {
  const items = [...document.querySelectorAll("[data-lightbox-item]")];
  const image = lightbox.querySelector("[data-lightbox-image]");
  const prev = lightbox.querySelector("[data-lightbox-prev]");
  const next = lightbox.querySelector("[data-lightbox-next]");
  let current = 0;
  const show = (index) => {
    current = (index + items.length) % items.length;
    image.src = items[current].href;
    image.alt = items[current].querySelector("img").alt;
  };
  items.forEach((item, index) =>
    item.addEventListener("click", (event) => {
      event.preventDefault();
      show(index);
      lightbox.showModal();
    }),
  );
  prev.hidden = next.hidden = items.length < 2;
  prev.addEventListener("click", () => show(current - 1));
  next.addEventListener("click", () => show(current + 1));
  lightbox.addEventListener("keydown", (event) => {
    if (event.key === "ArrowLeft") show(current - 1);
    if (event.key === "ArrowRight") show(current + 1);
  });
  lightbox.addEventListener("click", (event) => {
    if (event.target === lightbox) lightbox.close();
  });
}

document
  .querySelector("[data-contact-form]")
  ?.addEventListener("submit", async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    if (!form.reportValidity()) return;
    const copy = JSON.parse(form.dataset.contactCopy);
    const status = form.querySelector("[data-contact-status]");
    const button = form.querySelector('button[type="submit"]');
    button.disabled = true;
    status.textContent = copy.sending;
    try {
      const response = await fetch(form.action, {
        method: "POST",
        body: new FormData(form),
        headers: { Accept: "application/json" },
      });
      const result = await response.json();
      status.textContent = result.message;
      if (result.ok) form.reset();
    } catch {
      status.textContent = copy.error;
    }
    button.disabled = false;
  });

// IBAN copy is progressive enhancement; the number is always visible/selectable.
document.querySelectorAll("[data-copy-iban]").forEach((button) => {
  button.addEventListener("click", async () => {
    const status = button
      .closest("[data-bank-account]")
      .querySelector("[data-bank-status]");
    try {
      if (navigator.clipboard?.writeText)
        await navigator.clipboard.writeText(button.dataset.copyIban);
      else {
        const input = document.createElement("textarea");
        input.value = button.dataset.copyIban;
        input.style.position = "fixed";
        input.style.opacity = "0";
        document.body.append(input);
        input.select();
        let copied;
        try {
          copied = document.execCommand("copy");
        } finally {
          input.remove();
          button.focus();
        }
        if (!copied) throw new Error("Copy unavailable");
      }
      status.textContent = button.dataset.copied;
    } catch {
      status.textContent = button.dataset.copyFailed;
    }
  });
});

// Step tracks scroll sideways on desktop when not every step fits; arrows and edge fades appear only then.
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

// Subtle motion: content below the fold fades in on scroll, the donation bar fills once.
const motionAllowed =
  window.matchMedia("(prefers-reduced-motion: no-preference)").matches &&
  "IntersectionObserver" in window;
if (motionAllowed) {
  // Section content fades in as a whole; children of a [data-reveal-group] fade in one by one.
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

  // Calls back with an eased 0→1 value on every frame for 1.8s.
  const animate = (onFrame) => {
    const start = performance.now();
    const step = (now) => {
      const progress = Math.min((now - start) / 1800, 1);
      onFrame(1 - Math.pow(1 - progress, 3));
      if (progress < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };

  // Numbers count up from zero. The server renders the final formatted value (currency, separators,
  // "%"), so only its digits are swapped and the result ends on exactly the original text.
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
  if (donationBar) {
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

  // Donation buttons get a star that laps their rounded border, trailing light behind it.
  // SMIL runs each lap natively. JavaScript decides when laps start (at random, or on hover/focus)
  // and rebuilds the SVG for each new lap or resize, from a random corner in a random direction.
  {
    const motion = window.matchMedia("(prefers-reduced-motion: reduce)");
    const make = (name, attrs = {}) => {
      const node = document.createElementNS("http://www.w3.org/2000/svg", name);
      Object.entries(attrs).forEach(([key, value]) => node.setAttribute(key, value));
      return node;
    };
    const random = (min, max) => min + Math.random() * (max - min);
    const rest = () => random(9, 16);
    // At most this many stars lap on screen at once, so they never compete for attention.
    const maxLaps = 2;
    // [length, width] per trail layer. Each layer is shorter and wider than the one beneath,
    // so the stack tapers to a point and brightens towards the star.
    const trails = Array.from({ length: 7 }, (_, i) => [50 - i * 6, 1 + i * 0.4]);
    // Ease in and out of each lap, like a shooting star.
    const easing = { calcMode: "spline", keyTimes: "0;1", keySplines: "0.5 0 0.5 1" };
    const starShape = "M 0 -9.5 L 2.28 -2.66 L 9.5 0 L 2.28 2.66 L 0 9.5 L -2.28 2.66 L -9.5 0 L -2.28 -2.66 Z";
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
      const lap = random(6.5, 8.5);
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
        const shape = make("path", { d: starShape, fill: "currentColor", style: `filter:drop-shadow(0 0 ${3 * size}px color-mix(in srgb, currentColor 67%, transparent))` });
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
      const schedule = (seconds) => {
        clearTimeout(timer);
        timer = setTimeout(() => {
          // Try again shortly if the star is mid-lap, unseen, or enough stars are already out.
          if (motion.matches || running() || svg.animationsPaused() || orbits.filter((orbit) => orbit.lapping()).length >= maxLaps) {
            return schedule(random(1, 3));
          }
          startLap();
          schedule(lap + rest());
        }, seconds * 1000);
      };
      // Hovering or focusing a button sends its star round straight away.
      const greet = () => {
        if (motion.matches || running()) return;
        startLap();
        schedule(lap + rest());
      };
      button.addEventListener("mouseenter", greet);
      button.addEventListener("focus", greet);
      new ResizeObserver(draw).observe(button);
      motion.addEventListener("change", draw);
      schedule(random(1, 10));
    });
  }
}
