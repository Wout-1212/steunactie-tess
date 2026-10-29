<?php

use Kirby\Http\Response;

/**
 * GET /dev/mail/donation: the thank-you mail with dummy data, rendered by the real templates and
 * tessDonationMailData(), without sending anything. Only with 'debug' on (the local host config);
 * everywhere else the route answers 404. /dev/mail lists the useful cases.
 *
 * ?lang=nl|fr|en  ?name=Wout+Peeters (empty for no name)  ?milestones=none|reached
 * ?message=0|held  ?method=bancontact  ?format=text
 */
function tessDonationMailPreview(string|null $type): Response|null
{
    if (option('debug') !== true) {
        return null;
    }

    if ($type === null) {
        $links = [];
        foreach (kirby()->languages()->codes() as $code) {
            foreach (['' => 'standaard', '&name=' => 'zonder naam', '&milestones=none' => 'zonder mijlpalen', '&milestones=reached' => 'alle mijlpalen bereikt', '&message=held' => 'bericht wacht op nazicht', '&message=0' => 'zonder bericht', '&format=text' => 'tekstversie'] as $query => $label) {
                $href = url('dev/mail/donation') . '?lang=' . $code . $query;
                $links[] = '<li><a href="' . esc($href, 'attr') . '">' . strtoupper($code) . ': ' . $label . '</a></li>';
            }
        }
        return new Response('<!DOCTYPE html><meta charset="utf-8"><title>Mailvoorbeelden</title><ul style="font-family:sans-serif;line-height:1.8">' . implode('', $links) . '</ul>');
    }

    $code = in_array(get('lang'), kirby()->languages()->codes(), true) ? get('lang') : kirby()->defaultLanguage()->code();
    $donation = (object) [
        'reference' => 'preview',
        'payment_id' => 'tr_Pr3v13wD0n4t',
        'amount_cents' => 10000,
        'name' => trim((string) get('name', 'Wout Peeters')),
        'email' => 'preview@example.com',
        'message' => get('message') === '0' ? '' : "Heel veel sterkte, Tess!\nWe denken aan je.",
        'message_status' => get('message') === 'held' ? 'held' : 'visible',
        'method' => get('method', 'bancontact'),
        'language' => $code,
        'paid_at' => tessDonationsNow(),
        'created_at' => tessDonationsNow(),
    ];
    $milestones = match (get('milestones')) {
        'none' => [],
        'reached' => [['amount' => 25000.0, 'label' => 'Behandeling', 'reached' => true]],
        default => null,
    };

    $data = tessDonationMailData($donation, $milestones);
    $text = get('format') === 'text';
    $body = kirby()->template('emails/donation', $text ? 'text' : 'html', 'text')->render($data);

    return $text
        ? new Response('Onderwerp: ' . t('donation.mail-subject', null, $code) . "\n\n" . $body, 'text/plain')
        : new Response($body, 'text/html');
}
