<?php

use Kirby\Cms\Language;
use Kirby\Http\Response;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\V;

/**
 * POST /doneren: validates the form on the donation page, stores the donation as 'open' and sends the
 * visitor to the Mollie checkout. Mistakes go back to the donation page with ?donation={status}.
 */
function tessDonationStart(Language $language): Response
{
    $back = fn (string $status) => Response::redirect(
        (page('doneren')?->url($language->code()) ?? $language->url()) . '?' . http_build_query(['donation' => $status, 'amount' => (int) get('amount') ?: null]),
        303
    );

    if (!site()->mollieEnabled() || tessForeignOrigin()) {
        return $back('error');
    }
    // Bots get the same answer as a person who made a typo, without a payment being created.
    if (tessLooksLikeBot('donations')) {
        return $back('invalid');
    }

    $amount = Str::trim(tessField('amount'));
    // One line, since the name also ends up in the mail.
    $name = Str::trim(preg_replace('/[\s\p{Cc}]+/u', ' ', tessField('name')));
    $email = Str::trim(tessField('email'));
    $message = tessDonationCleanMessage(tessField('message'));

    if (preg_match('/^\d{1,6}$/', $amount) !== 1
        || (int) $amount < option('tess.donations.min') || (int) $amount > option('tess.donations.max')
        || $name === '' || Str::length($name) > 120
        || !V::email($email) || Str::length($email) > 254
        || Str::length($message) > option('tess.donations.messageLength')) {
        return $back('invalid');
    }

    if (!tessAllow('donations')) {
        return $back('rate-limited');
    }

    $reference = bin2hex(random_bytes(10));
    $now = tessDonationsNow();
    $db = tessDonationsDb();
    $db->table('donations')->insert([
        'reference' => $reference,
        'amount_cents' => (int) $amount * 100,
        'name' => $name,
        'email' => $email,
        'message' => $message,
        'show_name' => get('show_name') ? 1 : 0,
        'show_amount' => get('show_amount') ? 1 : 0,
        'message_status' => tessDonationNeedsReview($message) ? 'held' : 'visible',
        'language' => $language->code(),
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    try {
        $payment = tessMollie()->payments->create(array_filter([
            'amount' => ['currency' => 'EUR', 'value' => number_format((int) $amount, 2, '.', '')],
            'description' => 'Donatie ' . site()->title()->value(),
            'redirectUrl' => tessDonationThanksUrl($language, $reference),
            'webhookUrl' => tessDonationWebhookUrl(),
            'locale' => ['nl' => 'nl_BE', 'fr' => 'fr_BE', 'en' => 'en_GB'][$language->code()] ?? 'nl_BE',
            'metadata' => ['reference' => $reference],
        ]));
    } catch (Throwable $exception) {
        error_log('Donatie: betaling aanmaken mislukt: ' . $exception->getMessage());
        $db->table('donations')->delete(['reference' => $reference]);
        return $back('error');
    }

    $db->table('donations')->update(['payment_id' => $payment->id, 'updated_at' => tessDonationsNow()], ['reference' => $reference]);
    return Response::redirect($payment->getCheckoutUrl(), 303);
}

// Keeps single line breaks, drops other control characters and runs of empty lines. Also used in the Panel.
function tessDonationCleanMessage(string $message): string
{
    return Str::trim(preg_replace(['/[^\P{Cc}\n]/u', "/\n{3,}/"], ['', "\n\n"], str_replace(["\r\n", "\t"], ["\n", ' '], $message)));
}

// Links, email addresses and phone numbers wait for approval in the Panel; everything else is shown at once.
function tessDonationNeedsReview(string $message): bool
{
    return preg_match('~https?://|www\.|\b[\w-]+\.(?:be|com|nl|net|org|eu|fr|io|info|shop)\b~iu', $message) === 1
        || preg_match('/[^\s@]+@[^\s@]+\.[^\s@]+/u', $message) === 1
        // Eight or more digits, possibly split by spaces, dots, slashes or dashes: 0470 12 34 56, +32 470…
        || preg_match('/\+?\d(?:[\s.\/-]?\d){7,}/u', $message) === 1;
}

function tessDonationThanksUrl(Language $language, string $reference): string
{
    $page = page('bedankt');
    return ($page ? $page->url($language->code()) : rtrim($language->url(), '/') . '/bedankt') . '?ref=' . $reference;
}

// Mollie must be able to reach the webhook, so it is left out on local hosts (the thank-you page
// checks the status itself). 'tess.donations.webhookUrl' overrides it, e.g. for a herd share tunnel.
function tessDonationWebhookUrl(): string|null
{
    if ($url = option('tess.donations.webhookUrl')) {
        return $url;
    }
    $host = parse_url(kirby()->url(), PHP_URL_HOST) ?? '';
    if ($host === 'localhost' || str_ends_with($host, '.test') || filter_var($host, FILTER_VALIDATE_IP)) {
        return null;
    }
    return kirby()->url() . '/mollie/webhook';
}
