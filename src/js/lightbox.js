// Photo lightbox on update pages. Without JS the thumbnails simply link to the large image.
export function initLightbox() {
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
