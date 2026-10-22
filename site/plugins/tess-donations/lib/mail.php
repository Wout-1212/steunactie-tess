<?php

/**
 * Thank-you mail to the donor via Brevo (transport in site/config), in the language they donated in.
 * Called once per paid donation from tessDonationSync(), which has already claimed it by filling
 * in mailed_at; on failure mailed_at is cleared again so a later sync or the Panel can retry.
 */
function tessDonationMail(object $donation): bool
{
    $language = kirby()->language($donation->language) ?? kirby()->defaultLanguage();
    $code = $language->code();
    $locale = strtok($language->locale(LC_ALL) ?? 'nl_BE', '.');

    $euro = new NumberFormatter($locale, NumberFormatter::CURRENCY);
    $euro->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
    $date = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::SHORT, 'Europe/Brussels');

    try {
        kirby()->email([
            'template' => 'donation',
            'from' => option('tess.contact.from'),
            'fromName' => option('tess.contact.fromName'),
            'to' => $donation->email,
            'replyTo' => site()->contactemail()->or(option('tess.contact.from'))->value(),
            'subject' => t('donation.mail-subject', null, $code),
            'data' => [
                'language' => $code,
                'name' => $donation->name,
                'amount' => $euro->formatCurrency($donation->amount_cents / 100, 'EUR'),
                'date' => $date->format(new DateTime($donation->paid_at ?? 'now', new DateTimeZone('UTC'))),
                'reference' => $donation->payment_id,
                'message' => $donation->message,
                'held' => $donation->message_status === 'held',
                'url' => $language->url(),
            ],
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
