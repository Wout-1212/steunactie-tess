// "All donations" popup next to the donation block: batches of HTML from the donaties/lijst route.
export function initDonationWall() {
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
        headers: { Accept: "application/json" },
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
