<?php

use Kirby\Toolkit\Str;

/**
 * Thank-you mail to the donor via Brevo (transport in site/config), in the language they donated in.
 * Called once per paid donation from tessDonationSync(), which has already claimed it by filling
 * in mailed_at; on failure mailed_at is cleared again so a later sync or the Panel can retry.
 */
function tessDonationMail(object $donation): bool
{
    $data = tessDonationMailData($donation);

    try {
        kirby()->email([
            'template' => 'donation',
            'from' => option('tess.contact.from'),
            'fromName' => option('tess.contact.fromName'),
            'to' => $donation->email,
            'replyTo' => $data['contact'],
            'subject' => t('donation.mail-subject', null, $data['language']),
            'data' => $data,
        ]);
    } catch (Throwable $exception) {
        error_log('Donatie: bedankmail voor ' . $donation->reference . ' mislukt: ' . $exception->getMessage());
        tessDonationsDb()->table('donations')->update(
            ['mailed_at' => null, 'mail_error' => mb_substr($exception->getMessage(), 0, 500), 'updated_at' => tessDonationsNow()],
            ['reference' => $donation->reference]
        );
        return false;
    }

    tessDonationsDb()->table('donations')->update(['mail_error' => null], ['reference' => $donation->reference]);
    return true;
}

/**
 * Everything the mail templates show, as plain text in the donor's language (the templates escape it).
 * Also used by the preview route, so the preview is exactly what donors get. $milestones replaces the
 * milestones from the site, for previewing the cases without them.
 */
function tessDonationMailData(object $donation, array|null $milestones = null): array
{
    $language = kirby()->language($donation->language) ?? kirby()->defaultLanguage();
    $code = $language->code();
    $site = site();
    $home = page('home')?->url($code) ?? $language->url();
    $disclaimer = page('disclaimer')?->url($code);
    $instagram = $site->instagram()->or('https://www.instagram.com/samen_voor_tess/')->value();

    $dates = new IntlDateFormatter(strtok($language->locale(LC_ALL) ?? 'nl_BE', '.'), IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Europe/Brussels');
    $paid = new DateTimeImmutable(($donation->paid_at ?? $donation->created_at) . ' UTC');

    // The first milestone the total hasn't reached yet; none when there are none or all are reached.
    $next = array_find($milestones ?? $site->milestoneProgress($code), fn (array $milestone) => !$milestone['reached']);

    $shareMessage = tt('donation.mail-share-message', null, ['url' => $home], $code);

    return [
        'language' => $code,
        // "Wout" from "Wout Peeters"; the greeting has no name when this is empty.
        'firstName' => explode(' ', $donation->name)[0],
        'amount' => tessDonationEuro($donation->amount_cents / 100, $code),
        'date' => $dates->format($paid),
        'method' => tessDonationMethod($donation->method, $code),
        'reference' => $donation->payment_id ?? $donation->reference,
        'message' => $donation->message,
        'held' => $donation->message_status === 'held',
        'milestone' => $next ? ['amount' => tessDonationEuro($next['amount'], $code, 0), 'label' => $next['label']] : null,
        'progressUrl' => rtrim($home, '/') . '/#donatievoortgang',
        'url' => $home,
        // "samenvoortess.be", from the site URL so test servers show their own address.
        'domain' => preg_replace('/^www\./', '', parse_url($site->url(), PHP_URL_HOST) ?? ''),
        'share' => [
            'whatsapp' => 'https://wa.me/?text=' . rawurlencode($shareMessage),
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($home),
            'email' => 'mailto:?subject=' . rawurlencode($site->title()->value()) . '&body=' . rawurlencode($shareMessage),
        ],
        'instagram' => $instagram,
        'instagramHandle' => '@' . basename(rtrim(parse_url($instagram, PHP_URL_PATH) ?? '', '/')),
        'contact' => $site->contactemail()->or(option('tess.contact.from'))->value(),
        'privacyUrl' => $disclaimer && page('privacy') ? $disclaimer . '#privacy' : null,
        'donationsUrl' => $disclaimer ? $disclaimer . '#donaties' : null,
    ];
}

// "€ 100,00" in Dutch and French (Belgian style for both), "€100.00" in English.
function tessDonationEuro(float $amount, string $language, int $decimals = 2): string
{
    return $language === 'en'
        ? '€' . number_format($amount, $decimals, '.', ',')
        : "€\u{00A0}" . number_format($amount, $decimals, ',', '.');
}

// Mollie's method code as a label ("creditcard" → "Kredietkaart"); unknown codes get a capital letter.
function tessDonationMethod(string|null $method, string $language): string
{
    if ($method === null || $method === '') {
        return t('donation.method-other', null, $language);
    }
    return t('donation.method-' . $method, null, $language) ?? Str::ucfirst(str_replace('_', ' ', $method));
}
