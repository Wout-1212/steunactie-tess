  const siteHeader = document.querySelector('[data-site-header]');
  if (siteHeader) {
    // Over a hero the header stays transparent until the hero has scrolled out from under it.
    const hero = document.querySelector('[data-hero]');
    const updateHeader = () => {
      const scrolled = hero ? hero.getBoundingClientRect().bottom <= siteHeader.offsetHeight : window.scrollY > 12;
      if (siteHeader.dataset.scrolled !== String(scrolled)) {
        siteHeader.dataset.scrolled = String(scrolled);
      }
    };
    // Exposed on the root so the mobile menu and the hero (which sits behind the header) can use it.
    const sizeHeader = () => document.documentElement.style.setProperty('--header-height', `${siteHeader.offsetHeight}px`);
    sizeHeader();
    new ResizeObserver(sizeHeader).observe(siteHeader);
    updateHeader();
    window.addEventListener('scroll', updateHeader, { passive: true });
    window.addEventListener('resize', updateHeader);
    window.addEventListener('pageshow', updateHeader);
  }
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('#navigation');
  if (toggle && nav && siteHeader) {
    const desktop = window.matchMedia('(min-width: 681px)');
    const icon = toggle.querySelector('[data-menu-icon]');
    const background = [document.querySelector('main'), document.querySelector('footer')];
    let previousOverflow = '';
    const setMenu = (open, restoreFocus = false) => {
      open = open && !desktop.matches;
      const wasOpen = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(open));
      nav.classList.toggle('hidden', !open);
      if (icon) icon.textContent = open ? '×' : '☰';
      background.forEach(element => { if (element) element.inert = open; });
      if (open && !wasOpen) {
        previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
      } else if (!open && wasOpen) {
        document.body.style.overflow = previousOverflow;
      }
      if (restoreFocus) toggle.focus();
    };
    nav.dataset.enhanced = 'true';
    toggle.hidden = false;
    setMenu(false);
    toggle.addEventListener('click', () => setMenu(toggle.getAttribute('aria-expanded') !== 'true'));
    nav.addEventListener('click', event => {
      if (event.target.closest('a')) setMenu(false);
    });
    desktop.addEventListener('change', () => setMenu(false));
    window.addEventListener('pageshow', () => setMenu(false));
    document.addEventListener('keydown', event => {
      if (toggle.getAttribute('aria-expanded') !== 'true') return;
      if (event.key === 'Escape') setMenu(false, true);
      if (event.key === 'Tab') {
        const controls = [...siteHeader.querySelectorAll('a, button')].filter(element => element.getClientRects().length);
        const first = controls[0], last = controls[controls.length - 1];
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
      }
    });
  }
  const amountInput = document.querySelector('#amount');
  const amountButtons = document.querySelectorAll('[data-amount]');
  amountButtons.forEach(button => button.addEventListener('click', () => {
    amountInput.value = button.dataset.amount;
    amountButtons.forEach(other => other.setAttribute('aria-pressed', String(other === button)));
  }));
  amountInput?.addEventListener('input', () => amountButtons.forEach(button => button.setAttribute('aria-pressed', String(button.dataset.amount === amountInput.value))));
  // Use the native share sheet where available (mostly phones), otherwise open the share dialog.
  const shareButton = document.querySelector('[data-share]');
  const shareDialog = document.querySelector('[data-share-dialog]');
  shareButton?.addEventListener('click', async () => {
    if (navigator.share) {
      try { await navigator.share({ title: shareButton.dataset.shareTitle, url: shareButton.dataset.shareUrl }); return; }
      catch (error) { if (error.name === 'AbortError') return; }
    }
    shareDialog.querySelector('[data-share-status]').textContent = '';
    shareDialog.showModal();
  });
  shareDialog?.addEventListener('click', event => { if (event.target === shareDialog) shareDialog.close(); });
  shareDialog?.querySelector('[data-share-copy]').addEventListener('click', async () => {
    const input = shareDialog.querySelector('#share-link');
    const status = shareDialog.querySelector('[data-share-status]');
    input.select();
    // The Clipboard API only exists on https/localhost; fall back to the selected input elsewhere.
    try { await navigator.clipboard.writeText(input.value); } catch { document.execCommand('copy'); }
    status.textContent = status.dataset.copied;
  });

  // Photo lightbox on update pages. Without JS the thumbnails simply link to the large image.
  const lightbox = document.querySelector('[data-lightbox]');
  if (lightbox) {
    const items = [...document.querySelectorAll('[data-lightbox-item]')];
    const image = lightbox.querySelector('[data-lightbox-image]');
    const prev = lightbox.querySelector('[data-lightbox-prev]');
    const next = lightbox.querySelector('[data-lightbox-next]');
    let current = 0;
    const show = index => {
      current = (index + items.length) % items.length;
      image.src = items[current].href;
      image.alt = items[current].querySelector('img').alt;
    };
    items.forEach((item, index) => item.addEventListener('click', event => {
      event.preventDefault();
      show(index);
      lightbox.showModal();
    }));
    prev.hidden = next.hidden = items.length < 2;
    prev.addEventListener('click', () => show(current - 1));
    next.addEventListener('click', () => show(current + 1));
    lightbox.addEventListener('keydown', event => {
      if (event.key === 'ArrowLeft') show(current - 1);
      if (event.key === 'ArrowRight') show(current + 1);
    });
    lightbox.addEventListener('click', event => { if (event.target === lightbox) lightbox.close(); });
  }

  document.querySelector('[data-contact-email]')?.addEventListener('submit', event => {
    event.preventDefault();
    const form = event.currentTarget;
    if (!form.dataset.contactEmail || !form.reportValidity()) return;
    const data = new FormData(form);
    const copy = JSON.parse(form.dataset.contactCopy);
    const subject = copy.subject + ' ' + data.get('name');
    const body = copy.name + ': ' + data.get('name') + '\n' + copy.email + ': ' + data.get('email') + '\n\n' + data.get('message');
    location.href = 'mailto:' + form.dataset.contactEmail + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);
    form.querySelector('[data-contact-status]').textContent = copy.status;
  });
