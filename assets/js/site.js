const toggle = document.querySelector('.menu-toggle');
const nav = document.querySelector('#navigation');
if (toggle && nav) {
  toggle.hidden = false;
  nav.classList.add('hidden');
  toggle.addEventListener('click', () => {
    const open = toggle.getAttribute('aria-expanded') !== 'true';
    toggle.setAttribute('aria-expanded', String(open));
    nav.classList.toggle('hidden', !open);
  });
  nav.addEventListener('click', event => {
    if (event.target.closest('a')) { toggle.setAttribute('aria-expanded', 'false'); nav.classList.add('hidden'); }
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') { toggle.setAttribute('aria-expanded', 'false'); nav.classList.add('hidden'); toggle.focus(); }
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
