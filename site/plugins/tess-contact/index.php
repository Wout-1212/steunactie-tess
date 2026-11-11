<?php

use Kirby\Cms\Language;
use Kirby\Http\Response;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\V;

/**
 * Handles the contact form: validates the message and mails it to the contact address from the site
 * settings via Brevo SMTP (transport in site/config). Nothing is stored apart from a rate-limit counter
 * per hashed IP address. Answers with JSON for the JavaScript submit, or redirects back to #contact.
 */
function tessContactRespond(Language $language, string $status, int|null $code = null): Response
{
    if (kirby()->visitor()->prefersJson()) {
        $codes = ['success' => 200, 'invalid' => 422, 'rate-limited' => 429, 'error' => 500];
        return Response::json(['ok' => $status === 'success', 'message' => t('contact.' . $status)], $code ?? $codes[$status]);
    }
    return Response::redirect($language->url() . '?contact=' . $status . '#contact', 303);
}

// Shared by the contact and donation forms. $plugin is the plugin name ('contact', 'donations'):
// its cache and its limit, window and minSeconds options are used.

// An IP address is personal data, so the rate limit only stores a keyed hash of it. A plain sha256 can be
// reversed by hashing all 4 billion IPv4 addresses; without the secret salt from the host config it can't.
function tessVisitorHash(): string
{
    return hash_hmac('sha256', 'rate-limit:' . kirby()->visitor()->ip(), (string) option('content.salt', kirby()->root('content')));
}

// Counts accepted submissions per IP within a fixed window; returns false once the limit is reached.
function tessAllow(string $plugin): bool
{
    $cache = kirby()->cache('tess.' . $plugin);
    $key = tessVisitorHash();
    $now = time();
    $entry = $cache->get($key);

    // The cache rounds up to whole minutes, so an entry can outlive its window by a few seconds: start a new one.
    if ($entry === null || $entry['until'] <= $now) {
        $entry = ['count' => 0, 'until' => $now + option('tess.' . $plugin . '.window') * 60];
    }
    if ($entry['count'] >= option('tess.' . $plugin . '.limit')) {
        return false;
    }
    $entry['count']++;
    // Expiry is in minutes and 0 means "never", so keep at least one minute.
    $cache->set($key, $entry, max(1, (int) ceil(($entry['until'] - $now) / 60)));
    return true;
}

// A form posted from another site (CSRF). Browsers say where a request comes from in Sec-Fetch-Site
// (Fetch Metadata); older browsers without it still send Origin with every cross-site POST. A request
// with neither doesn't come from a browser form, so it is left to the other checks.
function tessForeignOrigin(): bool
{
    $request = kirby()->request();
    if ($fetchSite = $request->header('Sec-Fetch-Site')) {
        // 'none': the visitor started the request themselves (address bar, bookmark).
        return !in_array($fetchSite, ['same-origin', 'none'], true);
    }
    $origin = $request->header('Origin');
    return $origin !== null && parse_url($origin, PHP_URL_HOST) !== parse_url(kirby()->url(), PHP_URL_HOST);
}

// Honeypot filled in, or submitted faster than a person can.
function tessLooksLikeBot(string $plugin): bool
{
    return get('website') !== null && get('website') !== ''
        || time() - (int) get('started') < option('tess.' . $plugin . '.minSeconds');
}

// A posted text field; arrays (name[]=…) and invalid UTF-8 count as empty, so they fail validation.
function tessField(string $key): string
{
    return is_string($value = get($key)) && mb_check_encoding($value, 'UTF-8') ? $value : '';
}

Kirby::plugin('tess/contact', [
    'options' => [
        'cache' => true,
        'from' => null,
        'fromName' => null,
        // Overrides the recipient from the site settings, e.g. on the test server.
        'to' => null,
        // Messages per IP address per window (minutes).
        'limit' => 5,
        'window' => 60,
        // Bots submit instantly; people need at least a few seconds to fill in the form.
        'minSeconds' => 3,
    ],
    'routes' => [
        [
            'pattern' => 'contact',
            'method' => 'POST',
            'language' => '*',
            'action' => function (Language $language) {
                $kirby = kirby();

                if (tessForeignOrigin()) {
                    return tessContactRespond($language, 'error', 403);
                }

                // Honeypot or too fast: pretend it worked so bots don't learn anything.
                if (tessLooksLikeBot('contact')) {
                    return tessContactRespond($language, 'success');
                }

                // One line without control characters, since the name also ends up in the subject and Reply-To.
                $name = trim(preg_replace('/[\s\p{Cc}]+/u', ' ', tessField('name')));
                $email = trim(tessField('email'));
                // Keep line breaks and tabs, drop other control characters.
                $message = trim(preg_replace('/[^\P{Cc}\n\t]/u', '', str_replace("\r\n", "\n", tessField('message'))));
                $to = (string) (option('tess.contact.to') ?? $kirby->site()->contactemail()->value());

                if ($name === '' || Str::length($name) > 120
                    || !V::email($email) || Str::length($email) > 254
                    || $message === '' || Str::length($message) > 5000
                    || !V::email($to)) {
                    return tessContactRespond($language, 'invalid');
                }

                if (!tessAllow('contact')) {
                    return tessContactRespond($language, 'rate-limited');
                }

                try {
                    $kirby->email([
                        'template' => 'contact',
                        'from' => option('tess.contact.from'),
                        'fromName' => option('tess.contact.fromName'),
                        'to' => $to,
                        'replyTo' => $email,
                        'replyToName' => $name,
                        // The team reads Dutch, whatever language the visitor used.
                        'subject' => t('contact.mail-subject', null, 'nl') . ' ' . $name,
                        'data' => [
                            'name' => $name,
                            'email' => $email,
                            'message' => $message,
                            'language' => $language->name(),
                            'url' => $language->url(),
                        ],
                    ]);
                } catch (Throwable $exception) {
                    error_log('Contactformulier: verzenden mislukt: ' . $exception->getMessage());
                    return tessContactRespond($language, 'error');
                }

                return tessContactRespond($language, 'success');
            },
        ],
    ],
]);
