<?php
// One donation on the donation wall and in the "all donations" popup.
// $donation: item from site()->recentDonations() (name/amount null when hidden by the donor)
// Also rendered from the donaties/lijst route, where $kirby is not passed in: hence kirby().
$locale = strtok(kirby()->language()?->locale(LC_ALL) ?? 'nl_BE', '.');
$euro = new NumberFormatter($locale, NumberFormatter::CURRENCY);
$euro->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
$brussels = new DateTimeZone('Europe/Brussels');
$when = new DateTime($donation['date'], new DateTimeZone('UTC'));
$full = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::SHORT, $brussels);

// How long ago, for the first week; after that the date and time.
$seconds = max(0, time() - $when->getTimestamp());
$plural = fn (string $key, int $count) => tt($key . ($count === 1 ? '-one' : ''), ['count' => $count]);
$ago = match (true) {
    $seconds < 60 => t('time.now'),
    $seconds < 3600 => $plural('time.minutes', intdiv($seconds, 60)),
    $seconds < 86400 => $plural('time.hours', intdiv($seconds, 3600)),
    $seconds < 7 * 86400 => $plural('time.days', intdiv($seconds, 86400)),
    default => (new IntlDateFormatter($locale, IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT, $brussels))->format($when),
};
?>
<li class="flex gap-3 border-t border-divider py-3 first:border-t-0 first:pt-0">
    <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-blush text-pink" aria-hidden="true"><?php snippet('components/icon', ['name' => 'heart', 'class' => 'size-4']) ?></span>
    <div class="min-w-0 grow">
        <div class="flex items-start justify-between gap-3">
            <p class="m-0 min-w-0 text-[13px] leading-[1.4]">
                <strong class="block text-ink wrap-break-word"><?= esc($donation['name'] ?? t('donation.anonymous')) ?></strong>
                <time class="text-[12px] text-[#27222ACC]" datetime="<?= $when->format(DATE_ATOM) ?>" title="<?= esc($full->format($when), 'attr') ?>"><?= esc($ago) ?></time>
            </p>
            <?php if ($donation['amount'] !== null): ?><p class="m-0 shrink-0 text-[15px] leading-[1.4] font-bold text-pink tabular-nums"><?= esc($euro->formatCurrency($donation['amount'], 'EUR')) ?></p><?php endif ?>
        </div>
        <?php if ($donation['message'] !== ''): ?><p class="mt-1 mb-0 text-[13px] leading-[1.55] wrap-break-word"><?= nl2br(esc($donation['message'])) ?></p><?php endif ?>
    </div>
</li>
