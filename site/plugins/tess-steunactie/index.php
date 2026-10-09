<?php

use Kirby\Http\Remote;

/**
 * Reads the raised amount and donation count from the public Steunactie campaign page (the
 * "Externe steunactie" URL in the site settings). Fetched server-side and cached, so visitors
 * never connect to steunactie.be. When a fetch fails, the last known result is used.
 */
function tessSteunactieFetch(string $url): array|null
{
    try {
        $response = Remote::get($url, [
            'timeout' => 5,
            'agent' => 'SamenVoorTess-website (+' . site()->url() . ')',
        ]);
    } catch (Throwable) {
        return null;
    }
    if ($response->code() !== 200 || !$response->content()) {
        return null;
    }

    $document = new DOMDocument();
    if (!$document->loadHTML($response->content(), LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET)) {
        return null;
    }
    $xpath = new DOMXPath($document);
    $text = fn (string $class) => $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]")->item(0)?->textContent;

    // "€ 30.040" or "€ 30.040,50": drop the cents, keep the digits.
    $raised = preg_replace('/\D/', '', preg_replace('/,\d+\s*$/', '', trim($text('total_donations') ?? '')));
    if ($raised === '') {
        return null;
    }
    $donations = preg_replace('/\D/', '', $text('nr-donations') ?? '');

    return [
        'raised' => (int) $raised,
        'donations' => $donations === '' ? null : (int) $donations,
        'fetched' => time(),
    ];
}

Kirby::plugin('tess/steunactie', [
    'options' => [
        'cache' => true,
        // Minutes between fetches; after a failed fetch we wait 10 minutes before trying again.
        'interval' => 60,
        'retry' => 10,
    ],
    'siteMethods' => [
        // ['raised' => int, 'donations' => int|null, 'fetched' => timestamp], or null when unavailable.
        'steunactie' => function (): array|null {
            $url = $this->fundraiser()->value();
            if (!$url || !str_contains(parse_url($url, PHP_URL_HOST) ?? '', 'steunactie.be')) {
                return null;
            }

            $cache = kirby()->cache('tess.steunactie');
            $key = md5($url);
            $current = $cache->get($key);

            if ($current === null) {
                $current = tessSteunactieFetch($url) ?? [];
                $cache->set($key, $current, option($current ? 'tess.steunactie.interval' : 'tess.steunactie.retry'));
                if ($current) {
                    $cache->set($key . '-last', $current);
                }
            }

            return $current ?: $cache->get($key . '-last');
        },

        // One-line status for the Panel, so editors can see whether the automatic amount works.
        'steunactieStatus' => function (): string {
            if (!$stats = $this->steunactie()) {
                return 'Automatisch ophalen lukt nu niet, dus het bedrag hieronder wordt gebruikt.';
            }
            return 'Automatisch opgehaald: € ' . number_format($stats['raised'], 0, ',', '.')
                . ($stats['donations'] !== null ? ' (' . $stats['donations'] . ' donaties)' : '')
                . ', op ' . (new DateTime('@' . $stats['fetched']))->setTimezone(new DateTimeZone('Europe/Brussels'))->format('d-m-Y H:i')
                . '. Het bedrag hieronder is enkel een reserve.';
        },
    ],
]);
