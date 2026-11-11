<?php

use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Cms\Site;
use Kirby\Toolkit\Str;

/**
 * Thank-you page. The donor returns here from Mollie with ?ref=…. The webhook normally has the status
 * already; if not (yet), it is fetched once here, so the page is right without waiting for the webhook.
 */
return function (Page $page, Site $site, App $kirby) {
    $reference = get('ref');
    $donation = is_string($reference) && preg_match('/^[a-f0-9]{20}$/', $reference) === 1 && $site->mollieEnabled()
        ? tessDonationFind($reference)
        : null;
    if ($donation) {
        $donation = tessDonationRefresh($donation);
    }

    // Only paid counts as paid. Open is still in progress when Mollie sent the donor back through the
    // redirectUrl (checkout done, result not final yet), but not through the cancelUrl (see lib/payment.php):
    // that payment only expires later. Everything else, including an unknown reference, has failed.
    $state = match (true) {
        $donation?->payment_status === 'paid' => 'paid',
        $donation?->payment_status === 'open' && !get('canceled') => 'pending',
        default => 'failed',
    };

    $euro = new NumberFormatter(strtok($kirby->language()?->locale(LC_ALL) ?? 'nl_BE', '.'), NumberFormatter::CURRENCY);
    $euro->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
    $placeholders = [
        'name' => $donation?->name ?? '',
        'amount' => $donation ? $euro->formatCurrency($donation->amount_cents / 100, 'EUR') : '',
    ];
    // Each state has its own title and text in the Panel, e.g. "Bedankt, {{ name }}!".
    $title = $page->content()->get($state . '_title')->value();
    $text = $page->content()->get($state . '_text')->value();
    // HTML: the text is escaped first and the placeholders are filled in after, escaped too, so nothing
    // the donor typed is ever read as HTML.
    $html = fn (string|null $template) => Str::template(esc($template ?? ''), array_map('esc', $placeholders));

    return [
        'state' => $state,
        'donation' => $donation,
        'heading' => $html($title),
        'text' => $html($text),
        // Plain text for the browser tab (the meta snippet escapes it). Only a successful payment keeps
        // the page title "Bedankt"; otherwise the tab shows the state's title.
        'tabTitle' => $state === 'paid' ? null : Str::template($title ?? '', $placeholders),
    ];
};
