// Sticky header: transparent over the top of the page, opaque once content scrolls underneath it.
export function initSiteHeader() {
  const siteHeader = document.querySelector("[data-site-header]");
  if (!siteHeader) return;

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
