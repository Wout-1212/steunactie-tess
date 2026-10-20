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

  const donationBar = document.querySelector("[data-donation-bar]");
  if (donationBar) {
    const fill = donationBar.matches("progress")
      ? null
      : donationBar.querySelector("span");
    const finalValue = fill ? null : donationBar.value;
    if (fill) fill.style.width = "0%";
    else donationBar.value = 0;
    const fillBar = new IntersectionObserver(
      (entries) => {
        if (!entries.some((entry) => entry.isIntersecting)) return;
        fillBar.disconnect();
        if (fill) {
          requestAnimationFrame(() => fill.style.removeProperty("width"));
          return;
        }
        const start = performance.now();
        const step = (now) => {
          const progress = Math.min((now - start) / 1200, 1);
          donationBar.value = finalValue * (1 - Math.pow(1 - progress, 3));
          if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
      },
      { threshold: 0.6 },
    );
    fillBar.observe(donationBar);
  }
}
