// Donation page (templates/donate.php). The form is only rendered with a Mollie key.
export function initDonationForm() {
  // After a failed submit, start at the summary of what to fix (it links to each field).
  document.querySelector("[data-error-summary]")?.focus();

  const donationForm = document.querySelector("[data-donation-form]");
  if (!donationForm) return;

  // Message counter.
  const message = donationForm.querySelector("textarea[name=message]");
  const counter = donationForm.querySelector("[data-donation-counter]");
  const count = () =>
    (counter.textContent = `${message.value.length} / ${counter.dataset.max}`);
  counter.hidden = false;
  message.addEventListener("input", count);
  count();

  // One submit at a time, so a double click never creates two payments.
  donationForm.addEventListener("submit", () => {
    const button = donationForm.querySelector('button[type="submit"]');
    button.disabled = true;
    donationForm.querySelector("[data-donation-status]").textContent =
      donationForm.dataset.sending;
  });
  // Coming back with the browser's back button restores the page from cache with the button still disabled.
  window.addEventListener("pageshow", () => {
    donationForm.querySelector('button[type="submit"]').disabled = false;
  });
}
