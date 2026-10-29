<?php
use Kirby\Toolkit\Str;

// The donor returns here from Mollie with ?ref=…. The webhook normally has the status already;
// if not (yet), it is fetched once here, so the page is right without waiting for the webhook.
$reference = get('ref');
$donation = is_string($reference) && preg_match('/^[a-f0-9]{20}$/', $reference) === 1 && $site->mollieEnabled()
    ? tessDonationFind($reference)
    : null;
if ($donation) {
    $donation = tessDonationRefresh($donation);
}
$state = match ($donation?->payment_status) {
    'paid' => 'paid',
    'failed', 'canceled', 'expired' => 'failed',
    default => 'pending',
};

$locale = strtok($kirby->language()?->locale(LC_ALL) ?? 'nl_BE', '.');
$euro = new NumberFormatter($locale, NumberFormatter::CURRENCY);
$euro->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
$amount = $donation ? $euro->formatCurrency($donation->amount_cents / 100, 'EUR') : '';
// Placeholders are filled in after escaping, so nothing the donor typed is ever read as HTML.
$fill = fn (string $text) => Str::template(esc($text), [
    'name' => esc($donation?->name ?? ''),
    'amount' => $amount,
]);
// Only a successful payment keeps "Bedankt" in the browser tab; otherwise the tab shows the state's title.
// Plain text here, the meta snippet escapes it.
$tabTitle = $state === 'paid' ? null : Str::template($page->content()->get($state . '_title')->value(), [
    'name' => $donation?->name ?? '',
    'amount' => $amount,
]);
// Leaving Mollie's checkout with the back arrow returns here while the payment is still open (it only
// expires about 15 minutes later), so an open donation also gets the retry button.
$retry = $state === 'failed' || ($state === 'pending' && $donation);
?>
<?php snippet('layout/header', ['noindex' => true, 'title' => $tabTitle]) ?>
<section class="min-h-[45vh] py-[42px] md:py-[72px]">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[760px] text-center motion-safe:animate-fade-up"<?= $state === 'paid' ? ' data-confetti="' . esc($donation->reference, 'attr') . '"' : '' ?>>
        <?php if ($state === 'paid'): ?><?php snippet('components/icon', ['name' => 'heart', 'class' => 'mx-auto mb-4 size-10 text-pink']) ?><?php endif ?>
        <h1 class="text-[clamp(30px,4vw,48px)]"><?= $fill($page->content()->get($state . '_title')->value()) ?></h1>
        <p class="mx-auto max-w-[56ch] text-[16px] leading-[1.7] md:text-[17px]"><?= $fill($page->content()->get($state . '_text')->value()) ?></p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <?php if ($retry): ?>
                <?php snippet('components/button', ['donation' => true, 'href' => page('doneren')?->url() . ($donation ? '?amount=' . (int) ($donation->amount_cents / 100) : ''), 'text' => t('donation.try-again')]) ?>
            <?php endif ?>
            <?php snippet('components/button', ['href' => page('home')->url(), 'text' => t('donation.back-home'), 'variant' => $retry ? 'outline' : 'solid']) ?>
        </div>
    </div>
</section>
<?php snippet('layout/footer') ?>
