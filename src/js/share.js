// Use the native share sheet where available (mostly phones), otherwise open the share dialog.
export function initShare() {
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
}
