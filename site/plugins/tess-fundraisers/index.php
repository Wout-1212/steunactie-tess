<?php

use Kirby\Http\Remote;

/**
 * Reads the raised amount and donation count from the public fundraiser pages linked in the site
 * settings (Steunactie and GoFundMe). Fetched server-side and cached, so visitors never connect to
 * those platforms. When a fetch fails, the last known result is used.
 */
const TESS_FUNDRAISERS = [
    // Site field with the campaign URL, the platform's domain, and the parser for its page.
    'steunactie' => ['field' => 'fundraiser', 'domain' => 'steunactie.be', 'parse' => 'tessParseSteunactie'],
    'gofundme' => ['field' => 'gofundme', 'domain' => 'gofundme.com', 'parse' => 'tessParseGofundme'],
];

function tessFetchPage(string $url): string|null
{
    try {
        $response = Remote::get($url, [
            'timeout' => 5,
            'agent' => 'SamenVoorTess-website (+' . site()->url() . ')',
        ]);
    } catch (Throwable) {
        return null;
    }
    return $response->code() === 200 && $response->content() ? $response->content() : null;
}

function tessXPath(string $html): DOMXPath|null
{
    $document = new DOMDocument();
    return $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET) ? new DOMXPath($document) : null;
}

// Steunactie renders the totals as text, e.g. "€ 30.040" in .total_donations and "245" in .nr-donations.
function tessParseSteunactie(string $html): array|null
{
    if (!$xpath = tessXPath($html)) {
        return null;
    }
    $text = fn (string $class) => $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]")->item(0)?->textContent;

    // "€ 30.040" or "€ 30.040,50": drop the cents, keep the digits.
    $raised = preg_replace('/\D/', '', preg_replace('/,\d+\s*$/', '', trim($text('total_donations') ?? '')));
    if ($raised === '') {
        return null;
    }
    $donations = preg_replace('/\D/', '', $text('nr-donations') ?? '');

    return ['raised' => (int) $raised, 'donations' => $donations === '' ? null : (int) $donations];
}

// GoFundMe is a Next.js app: the campaign data is embedded as JSON in <script id="__NEXT_DATA__">,
// which is far more stable than its generated CSS class names.
function tessParseGofundme(string $html): array|null
{
    $json = tessXPath($html)?->query('//script[@id="__NEXT_DATA__"]')->item(0)?->textContent;
    try {
        $state = json_decode($json ?? '', true, flags: JSON_THROW_ON_ERROR)['props']['pageProps']['__APOLLO_STATE__'] ?? [];
    } catch (JsonException) {
        return null;
    }

    // The root query points at the campaign shown on the page: {"fundraiser:{\"slug\":…}": {"__ref": "Fundraiser:123"}}.
    foreach ($state['ROOT_QUERY'] ?? [] as $query => $result) {
        if (str_starts_with($query, 'fundraiser:') && isset($result['__ref'])) {
            $fundraiser = $state[$result['__ref']] ?? null;
            break;
        }
    }

    // The site counts in euro, so ignore a campaign in another currency rather than mixing them.
    $amount = $fundraiser['currentAmount'] ?? null;
    if (!is_numeric($amount['amount'] ?? null) || ($amount['currencyCode'] ?? null) !== 'EUR') {
        return null;
    }
    $donations = $fundraiser['donationCount'] ?? null;

    return ['raised' => (int) floor($amount['amount']), 'donations' => is_int($donations) ? $donations : null];
}

Kirby::plugin('tess/fundraisers', [
    'options' => [
        'cache' => true,
        // Minutes between fetches; after a failed fetch we wait 10 minutes before trying again.
        'interval' => 60,
        'retry' => 10,
    ],
    'siteMethods' => [
        // ['raised' => int, 'donations' => int|null, 'fetched' => timestamp], or null when unavailable.
        'fundraiserStats' => function (string $source): array|null {
            $fundraiser = TESS_FUNDRAISERS[$source];
            $url = $this->content()->get($fundraiser['field'])->value();
            $host = parse_url($url ?? '', PHP_URL_HOST) ?? '';
            if ($host !== $fundraiser['domain'] && !str_ends_with($host, '.' . $fundraiser['domain'])) {
                return null;
            }

            $cache = kirby()->cache('tess.fundraisers');
            $key = md5($url);
            $current = $cache->get($key);

            if ($current === null) {
                $html = tessFetchPage($url);
                $current = ($html ? $fundraiser['parse']($html) : null) ?? [];
                if ($current) {
                    $current['fetched'] = time();
                    $cache->set($key . '-last', $current);
                }
                $cache->set($key, $current, option($current ? 'tess.fundraisers.interval' : 'tess.fundraisers.retry'));
            }

            return $current ?: $cache->get($key . '-last');
        },

        // One-line status for the Panel, so editors can see whether the automatic amount works.
        'fundraiserStatus' => function (string $source): string {
            if (!$stats = $this->fundraiserStats($source)) {
                return 'Automatisch ophalen lukt nu niet, dus het bedrag hieronder wordt gebruikt.';
            }
            return 'Automatisch opgehaald: € ' . number_format($stats['raised'], 0, ',', '.')
                . ($stats['donations'] !== null ? ' (' . $stats['donations'] . ' donaties)' : '')
                . ', op ' . (new DateTime('@' . $stats['fetched']))->setTimezone(new DateTimeZone('Europe/Brussels'))->format('d-m-Y H:i')
                . '. Het bedrag hieronder is enkel een reserve.';
        },
    ],
]);
