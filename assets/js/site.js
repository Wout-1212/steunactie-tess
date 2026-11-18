(() => {
  // src/js/language-picker.js
  function initLanguagePicker() {
    const languagePicker = document.querySelector("[data-language-picker]");
    if (!languagePicker) return;
    const closeLanguagePicker = () => {
      languagePicker.open = false;
    };
    document.addEventListener("click", (event) => {
      if (!languagePicker.contains(event.target)) closeLanguagePicker();
    });
    languagePicker.addEventListener("focusout", (event) => {
      if (event.relatedTarget && !languagePicker.contains(event.relatedTarget)) closeLanguagePicker();
    });
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

  // src/js/site-header.js
  function initSiteHeader() {
    const siteHeader = document.querySelector("[data-site-header]");
    if (!siteHeader) return;
    const headerContent = [
      ...document.querySelectorAll(
        "main h1, main h2, main p, main img, main [data-photo-placeholder], main [data-update-card]"
      )
    ];
    const updateHeader = () => {
      const contentTop = headerContent.reduce((first, element) => {
        if (!element.getClientRects().length) return first;
        return Math.min(
          first,
          element.getBoundingClientRect().top + window.scrollY
        );
      }, Infinity);
      const headerBottom = siteHeader.getBoundingClientRect().bottom;
      const scrolled = Number.isFinite(contentTop) ? window.scrollY > 0 && contentTop <= window.scrollY + headerBottom + 2 : window.scrollY > 12;
      if (siteHeader.dataset.scrolled !== String(scrolled)) {
        siteHeader.dataset.scrolled = String(scrolled);
      }
    };
    const sizeHeader = () => {
      document.documentElement.style.setProperty(
        "--header-height",
        `${siteHeader.offsetHeight}px`
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

  // src/js/mobile-menu.js
  function initMobileMenu() {
    const siteHeader = document.querySelector("[data-site-header]");
    const toggle = document.querySelector("[data-menu-toggle]");
    const nav = document.querySelector("#navigation");
    if (!toggle || !nav || !siteHeader) return;
    const desktop = window.matchMedia("(min-width: 681px)");
    const icon = toggle.querySelector("[data-menu-icon]");
    const background = [
      document.querySelector("main"),
      document.querySelector("footer")
    ];
    let previousOverflow = "";
    const setMenu = (open, restoreFocus = false) => {
      open = open && !desktop.matches;
      const wasOpen = toggle.getAttribute("aria-expanded") === "true";
      toggle.setAttribute("aria-expanded", String(open));
      nav.classList.toggle("hidden", !open);
      if (icon) icon.textContent = open ? "\xD7" : "\u2630";
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
    toggle.addEventListener(
      "click",
      () => setMenu(toggle.getAttribute("aria-expanded") !== "true")
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
          ...siteHeader.querySelectorAll("a[href], button, summary")
        ].filter((element) => element.getClientRects().length);
        const first = controls[0], last = controls[controls.length - 1];
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

  // src/js/donation-amount.js
  function initDonationAmount() {
    const amountInput = document.querySelector("#amount");
    const amountButtons = document.querySelectorAll("[data-amount]");
    amountButtons.forEach(
      (button) => button.addEventListener("click", () => {
        amountInput.value = button.dataset.amount;
        amountButtons.forEach(
          (other) => other.setAttribute("aria-pressed", String(other === button))
        );
      })
    );
    amountInput?.addEventListener(
      "input",
      () => amountButtons.forEach(
        (button) => button.setAttribute(
          "aria-pressed",
          String(button.dataset.amount === amountInput.value)
        )
      )
    );
  }

  // src/js/donation-form.js
  function initDonationForm() {
    document.querySelector("[data-error-summary]")?.focus();
    const donationForm = document.querySelector("[data-donation-form]");
    if (!donationForm) return;
    const message = donationForm.querySelector("textarea[name=message]");
    const counter = donationForm.querySelector("[data-donation-counter]");
    const count = () => counter.textContent = `${message.value.length} / ${counter.dataset.max}`;
    counter.hidden = false;
    message.addEventListener("input", count);
    count();
    donationForm.addEventListener("submit", () => {
      const button = donationForm.querySelector('button[type="submit"]');
      button.disabled = true;
      donationForm.querySelector("[data-donation-status]").textContent = donationForm.dataset.sending;
    });
    window.addEventListener("pageshow", () => {
      donationForm.querySelector('button[type="submit"]').disabled = false;
    });
  }

  // src/js/donation-wall.js
  function initDonationWall() {
    const donationsOpen = document.querySelector("[data-donations-open]");
    const donationsDialog = document.querySelector("[data-donations-dialog]");
    if (!donationsOpen || !donationsDialog?.showModal) return;
    const list = donationsDialog.querySelector("[data-donations-list]");
    const more = donationsDialog.querySelector("[data-donations-more]");
    const status = donationsDialog.querySelector("[data-donations-status]");
    let next = 1;
    const load = async () => {
      more.disabled = true;
      try {
        const response = await fetch(`${donationsDialog.dataset.url}?page=${next}`, {
          headers: { Accept: "application/json" }
        });
        const result = await response.json();
        list.insertAdjacentHTML("beforeend", result.html);
        next = result.next;
        status.textContent = "";
      } catch {
        status.textContent = status.dataset.error;
      }
      more.hidden = !next;
      more.disabled = false;
    };
    donationsOpen.hidden = false;
    donationsOpen.addEventListener("click", () => {
      donationsDialog.showModal();
      if (next === 1) load();
    });
    more.addEventListener("click", load);
    donationsDialog.addEventListener("click", (event) => {
      if (event.target === donationsDialog) donationsDialog.close();
    });
  }

  // src/js/share.js
  function initShare() {
    const shareButton = document.querySelector("[data-share]");
    const shareDialog = document.querySelector("[data-share-dialog]");
    shareButton?.addEventListener("click", async () => {
      if (navigator.share) {
        try {
          await navigator.share({
            title: shareButton.dataset.shareTitle,
            url: shareButton.dataset.shareUrl
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
    shareDialog?.querySelector("[data-share-copy]").addEventListener("click", async () => {
      const input = shareDialog.querySelector("#share-link");
      const status = shareDialog.querySelector("[data-share-status]");
      input.select();
      try {
        await navigator.clipboard.writeText(input.value);
      } catch {
        document.execCommand("copy");
      }
      status.textContent = status.dataset.copied;
    });
  }

  // src/js/lightbox.js
  function initLightbox() {
    const lightbox = document.querySelector("[data-lightbox]");
    if (!lightbox) return;
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
    items.forEach(
      (item, index) => item.addEventListener("click", (event) => {
        event.preventDefault();
        show(index);
        lightbox.showModal();
      })
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

  // src/js/contact-form.js
  function initContactForm() {
    document.querySelector("[data-contact-form]")?.addEventListener("submit", async (event) => {
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
          headers: { Accept: "application/json" }
        });
        const result = await response.json();
        status.textContent = result.message;
        if (result.ok) form.reset();
      } catch {
        status.textContent = copy.error;
      }
      button.disabled = false;
    });
  }

  // src/js/copy-iban.js
  function initCopyIban() {
    document.querySelectorAll("[data-copy-iban]").forEach((button) => {
      button.addEventListener("click", async () => {
        const status = button.closest("[data-bank-account]").querySelector("[data-bank-status]");
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
  }

  // src/js/step-track.js
  function initStepTracks() {
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
        const atEnd = scroller.scrollLeft + scroller.clientWidth >= scroller.scrollWidth - 1;
        nav.hidden = !scrolls;
        prev.disabled = atStart;
        next.disabled = atEnd;
        if (scrolls) scroller.tabIndex = 0;
        else scroller.removeAttribute("tabindex");
        const more = !scrolls ? null : atStart ? "end" : atEnd ? "start" : "both";
        if (more) scroller.dataset.more = more;
        else delete scroller.dataset.more;
      };
      const page = (direction) => {
        const stride = steps.length > 1 ? steps[1].getBoundingClientRect().left - steps[0].getBoundingClientRect().left : scroller.clientWidth;
        const visible = Math.max(1, Math.floor(scroller.clientWidth / stride));
        scroller.scrollBy({
          left: direction * Math.max(1, visible - 1) * stride,
          behavior: window.matchMedia("(prefers-reduced-motion: reduce)").matches ? "auto" : "smooth"
        });
      };
      prev.addEventListener("click", () => page(-1));
      next.addEventListener("click", () => page(1));
      scroller.addEventListener("scroll", update, { passive: true });
      new ResizeObserver(update).observe(scroller);
      const current = scroller.querySelector('[aria-current="step"]');
      const box = scroller.getBoundingClientRect();
      if (current && overflowing() && current.getBoundingClientRect().right > box.right) {
        const first = current.previousElementSibling ?? current;
        scroller.scrollLeft += first.getBoundingClientRect().left - box.left;
      }
      update();
    });
  }

  // src/js/motion.js
  var motionAllowed = window.matchMedia("(prefers-reduced-motion: no-preference)").matches && "IntersectionObserver" in window;
  var animate = (onFrame) => {
    const start = performance.now();
    const step = (now) => {
      const progress = Math.min((now - start) / 1800, 1);
      onFrame(1 - Math.pow(1 - progress, 3));
      if (progress < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };

  // src/js/reveal.js
  function initReveal() {
    if (!motionAllowed) return;
    const targets = [
      ...document.querySelectorAll(
        "main > section:not([data-hero]) > div > :not([data-reveal-group], [data-no-reveal])"
      ),
      ...document.querySelectorAll(
        "[data-reveal-group] > :not([data-reveal-group])"
      )
    ].filter(
      (element) => element.getBoundingClientRect().top > window.innerHeight * 0.92
    );
    const reveal = new IntersectionObserver(
      (entries) => {
        entries.filter((entry) => entry.isIntersecting).forEach((entry, index) => {
          const element = entry.target;
          element.style.setProperty(
            "--reveal-delay",
            `${Math.min(index, 4) * 70}ms`
          );
          element.classList.add("is-visible");
          reveal.unobserve(element);
          const cleanUp = (event) => {
            if (event && (event.target !== element || event.propertyName !== "opacity"))
              return;
            element.classList.remove("reveal", "is-visible");
            element.style.removeProperty("--reveal-delay");
            element.removeEventListener("transitionend", cleanUp);
          };
          element.addEventListener("transitionend", cleanUp);
          setTimeout(cleanUp, 1200);
        });
      },
      { rootMargin: "0px 0px -8% 0px" }
    );
    targets.forEach((element) => {
      element.classList.add("reveal");
      reveal.observe(element);
    });
  }

  // src/js/count-up.js
  function initCountUp() {
    if (!motionAllowed) return;
    const counters = /* @__PURE__ */ new Map();
    document.querySelectorAll("[data-count-up]").forEach((element) => {
      const text = element.textContent;
      const match = text.match(/\d+(?:[^\w]\d{3})*/);
      if (!match) return;
      const target = Number(match[0].replace(/\D/g, ""));
      const separator = match[0].match(/\D/)?.[0] ?? "";
      const render = (value) => {
        const digits = String(Math.round(value)).replace(
          /\B(?=(\d{3})+$)/g,
          separator
        );
        element.textContent = text.slice(0, match.index) + digits + text.slice(match.index + match[0].length);
      };
      render(0);
      counters.set(element, () => {
        counters.delete(element);
        countUp.unobserve(element);
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
      (entries) => entries.filter((entry) => entry.isIntersecting).forEach((entry) => counters.get(entry.target)?.()),
      { rootMargin: "0px 0px -8% 0px" }
    );
    counters.forEach((_, element) => countUp.observe(element));
    const donationBar = document.querySelector("[data-donation-bar]");
    if (!donationBar) return;
    const fill = donationBar.matches("progress") ? null : donationBar.querySelector("span");
    const finalValue = fill ? null : donationBar.value;
    if (fill) fill.style.width = "0%";
    else donationBar.value = 0;
    const section = donationBar.closest("section");
    const sectionCounters = [...counters.keys()].filter(
      (element) => section?.contains(element)
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
        animate((eased) => donationBar.value = finalValue * eased);
      },
      { threshold: 0.6 }
    );
    fillBar.observe(donationBar);
  }

  // src/js/medical-scale.js
  function initMedicalScale() {
    if (!motionAllowed) return;
    const scale = document.querySelector("[data-scale]");
    if (!scale || !Number(scale.dataset.scale)) return;
    const tilt = Number(scale.dataset.scale);
    const length = Number(scale.dataset.scaleBeam);
    const [beam, done, planned] = ["beam", "done", "planned"].map(
      (part) => scale.querySelector(`[data-scale-part="${part}"]`)
    );
    const position = (angle) => {
      const radians = angle * Math.PI / 180;
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
      { threshold: 0.6 }
    );
    tiltScale.observe(scale);
  }

  // src/js/donation-star.js
  var maxLaps = 2;
  var trails = Array.from({ length: 7 }, (_, i) => [50 - i * 6, 1 + i * 0.4]);
  var easing = { calcMode: "spline", keyTimes: "0;1", keySplines: "0.5 0 0.5 1" };
  var starShape = "M 0 -9.5 L 2.28 -2.66 L 9.5 0 L 2.28 2.66 L 0 9.5 L -2.28 2.66 L -9.5 0 L -2.28 -2.66 Z";
  var make = (name, attrs = {}) => {
    const node = document.createElementNS("http://www.w3.org/2000/svg", name);
    Object.entries(attrs).forEach(([key, value]) => node.setAttribute(key, value));
    return node;
  };
  var random = (min, max) => min + Math.random() * (max - min);
  function initDonationStars() {
    if (!motionAllowed) return;
    const motion = window.matchMedia("(prefers-reduced-motion: reduce)");
    const orbits = [];
    const onScreen = new IntersectionObserver(
      (entries) => entries.forEach(
        ({ target, isIntersecting }) => isIntersecting ? target.unpauseAnimations() : target.pauseAnimations()
      )
    );
    document.querySelectorAll(".donation-light").forEach((button, index) => {
      const svg = make("svg", { class: "donation-orbit", "aria-hidden": "true", focusable: "false" });
      button.append(svg);
      onScreen.observe(svg);
      const lap = random(4, 4.8);
      let current = null;
      const running = () => current !== null && svg.getCurrentTime() - current.start < lap;
      orbits.push({ lapping: () => running() && !svg.animationsPaused() });
      let previous = "";
      const draw = () => {
        const style = getComputedStyle(button);
        const box = getComputedStyle(svg);
        const width = parseFloat(box.width);
        const height = parseFloat(box.height);
        const left = -parseFloat(style.borderLeftWidth) / 2;
        const top = -parseFloat(style.borderTopWidth) / 2;
        const right = width + parseFloat(style.borderRightWidth) / 2;
        const bottom = height + parseFloat(style.borderBottomWidth) / 2;
        const r = Math.min(
          Math.max(0, parseFloat(style.borderTopLeftRadius) + top),
          (bottom - top) / 2,
          (right - left) / 2
        );
        const signature = [width, height, left, top, right, bottom, r, motion.matches, current?.start].join();
        if (signature === previous) return;
        previous = signature;
        svg.replaceChildren();
        if (motion.matches || !width || !height || !current) return;
        svg.setAttribute("viewBox", `0 0 ${width} ${height}`);
        const points = [
          [left + r, top],
          [right - r, top],
          [right, top + r],
          [right, bottom - r],
          [right - r, bottom],
          [left + r, bottom],
          [left, bottom - r],
          [left, top + r]
        ];
        const { start, corner, clockwise } = current;
        let d = `M ${points[corner]}`;
        for (let i = 1; i <= 8; i++) {
          const to = (corner + (clockwise ? i : -i) + 8) % 8;
          d += to % 2 === 0 === clockwise ? ` A ${r} ${r} 0 0 ${+clockwise} ${points[to]}` : ` L ${points[to]}`;
        }
        d += " Z";
        const length = 2 * (right - left + bottom - top) - 8 * r + 2 * Math.PI * r;
        const size = Math.min(1, Math.max(0.6, (bottom - top) / 54));
        const starSize = size * 1.25;
        const timing = { begin: `${start}s`, dur: `${lap}s` };
        const clipId = `donation-trail-clip-${index}`;
        const clip = make("clipPath", { id: clipId, clipPathUnits: "userSpaceOnUse" });
        clip.append(make("rect", { x: left - 2, y: top - 2, width: right - left + 4, height: bottom - top + 4, rx: r + 2 }));
        const defs = make("defs");
        defs.append(clip);
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
        shape.style.filter = `drop-shadow(0 0 ${3 * size}px color-mix(in srgb, currentColor 67%, transparent))`;
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
          if (motion.matches || running() || svg.animationsPaused() || orbits.filter((orbit) => orbit.lapping()).length >= maxLaps) {
            return schedule(random(1, 3));
          }
          startLap();
        }, seconds * 1e3);
      };
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

  // src/js/confetti.js
  var colours = ["#C43B75", "#F5D7E6", "#FFD84D", "#C8EEF3", "#E86FA3"];
  var random2 = (min, max) => min + Math.random() * (max - min);
  function initConfetti() {
    const host = document.querySelector("[data-confetti]");
    if (!host) return;
    let seen = false;
    const key = `tess-confetti-${host.dataset.confetti}`;
    try {
      seen = sessionStorage.getItem(key) !== null;
      sessionStorage.setItem(key, "1");
    } catch {
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
    const launch = 13 + innerHeight * 0.0185;
    const pieces = Array.from({ length: 240 }, (_, index) => {
      const left = index % 2 === 0;
      const angle = (left ? random2(-84, -58) : random2(-122, -96)) * (Math.PI / 180);
      const speed = launch * random2(0.7, 1.05);
      return {
        x: left ? 0 : innerWidth,
        y: innerHeight * 0.95,
        vx: Math.cos(angle) * speed,
        vy: Math.sin(angle) * speed,
        size: random2(10, 18),
        colour: colours[index % colours.length],
        spin: random2(0, Math.PI * 2),
        spinSpeed: random2(-0.25, 0.25),
        heart: index % 9 === 0
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

  // src/js/main.js
  initLanguagePicker();
  initSiteHeader();
  initMobileMenu();
  initDonationAmount();
  initDonationForm();
  initDonationWall();
  initShare();
  initLightbox();
  initContactForm();
  initCopyIban();
  initStepTracks();
  initReveal();
  initCountUp();
  initMedicalScale();
  initDonationStars();
  initConfetti();
})();
