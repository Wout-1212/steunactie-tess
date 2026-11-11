// Amount buttons in the donation block on the homepage fill in the amount field, and show which one is picked.
export function initDonationAmount() {
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
}
