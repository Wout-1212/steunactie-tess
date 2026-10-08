const siteHeader = document.querySelector('[data-site-header]');
if (siteHeader) {
  const updateHeader = () => {
    const scrolled = window.scrollY > 12;
    if (siteHeader.dataset.scrolled !== String(scrolled)) {
      siteHeader.dataset.scrolled = String(scrolled);
    }
  };
  updateHeader();
  window.addEventListener('scroll', updateHeader, { passive: true });
  window.addEventListener('pageshow', updateHeader);
}
const toggle = document.querySelector('.menu-toggle');
const nav = document.querySelector('#navigation');
if (toggle && nav && siteHeader) {
  const desktop = window.matchMedia('(min-width: 681px)');
  const icon = toggle.querySelector('[data-menu-icon]');
  const background = [document.querySelector('main'), document.querySelector('footer')];
  let previousOverflow = '';
  const sizeMenu = () => siteHeader.style.setProperty('--header-height', `${siteHeader.offsetHeight}px`);
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
      sizeMenu();
    } else if (!open && wasOpen) {
      document.body.style.overflow = previousOverflow;
    }
    if (restoreFocus) toggle.focus();
  };
  nav.dataset.enhanced = 'true';
  toggle.hidden = false;
  sizeMenu();
  new ResizeObserver(sizeMenu).observe(siteHeader);
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
document.querySelector('[data-share]')?.addEventListener('click', async () => {
  const status = document.querySelector('[data-share-status]');
  const copy = document.querySelector('[data-share]').dataset;
  try {
    if (navigator.share) { await navigator.share({title: copy.shareTitle, url: location.href.split('#')[0]}); }
    else if (navigator.clipboard) { await navigator.clipboard.writeText(location.href.split('#')[0]); status.textContent = copy.shareCopied; }
    else { status.textContent = copy.shareFallback + ' ' + location.href.split('#')[0]; }
  } catch (error) { if (error.name !== 'AbortError') status.textContent = copy.shareFallback + ' ' + location.href.split('#')[0]; }
});

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
