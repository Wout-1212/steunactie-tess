<?php

use Kirby\Cms\Page;
use Kirby\Http\Response;

/**
 * Search engines and languages: robots.txt, and on the test server ('tess.noindex' in its host config)
 * a noindex header on every response, so the test site never shows up in search results.
 *
 * Dutch is the main language. A page is only offered in French or English once it has been translated
 * in the Panel; until then that address redirects to the Dutch page, so no page mixes languages.
 */
Kirby::plugin('tess/seo', [
    'options' => [
        'noindex' => false,
    ],
    'hooks' => [
        'route:before' => function () {
            if (option('tess.noindex')) {
                kirby()->response()->header('X-Robots-Tag', 'noindex, nofollow');
            }
        },
        'page.render:before' => function (string $contentType, array $data, Page $page) {
            $kirby = kirby();
            $language = $kirby->language();
            if ($contentType === 'html' && $language?->isDefault() === false && $kirby->request()->method() === 'GET'
                && !$page->isErrorPage() && !$page->translation($language->code())->exists()) {
                $query = $kirby->request()->query()->toString();
                go($page->url($kirby->defaultLanguage()->code()) . ($query !== '' ? '?' . $query : ''), 302);
            }
            return $data;
        },
    ],
    'pageMethods' => [
        // The languages this page exists in: the main language always, the others once translated.
        'translatedLanguages' => function () {
            return kirby()->languages()->filter(
                fn ($language) => $language->isDefault() || $this->translation($language->code())->exists()
            );
        },
    ],
    'routes' => [
        [
            // Every public page in every language it exists in. Left out: the donation and thank-you pages
            // (noindex), the error page, the old legal addresses (they redirect) and unpublished updates.
            // lastmod only for updates, from their date: file dates change with every upload.
            'pattern' => 'sitemap.xml',
            'action' => function () {
                $xml = fn (string $value) => htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $pages = site()->index()->filter(fn ($page) =>
                    !in_array($page->intendedTemplate()->name(), ['donate', 'thanks', 'error', 'legal'], true)
                    && ($page->parent()?->id() !== 'updates' || $page->isListed()));

                $entries = '';
                foreach ($pages as $page) {
                    $languages = $page->translatedLanguages();
                    foreach ($languages as $language) {
                        $entries .= "  <url>\n    <loc>" . $xml($page->url($language->code())) . "</loc>\n";
                        if ($page->intendedTemplate()->name() === 'update' && $page->date()->isNotEmpty()) {
                            $entries .= '    <lastmod>' . $page->date()->toDate('Y-m-d') . "</lastmod>\n";
                        }
                        if ($languages->count() > 1) {
                            foreach ($languages as $alternate) {
                                $entries .= '    <xhtml:link rel="alternate" hreflang="' . $alternate->code() . '" href="' . $xml($page->url($alternate->code())) . "\"/>\n";
                            }
                        }
                        $entries .= "  </url>\n";
                    }
                }

                return new Response(
                    "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xhtml=\"http://www.w3.org/1999/xhtml\">\n" . $entries . "</urlset>\n",
                    'application/xml'
                );
            },
        ],
        [
            // Where to report a security problem (RFC 9116): the contact address from the site settings.
            'pattern' => '.well-known/security.txt',
            'action' => function () {
                $email = site()->contactemail()->value();
                if (!$email) {
                    return false;
                }
                return new Response(
                    "Contact: mailto:$email\nExpires: " . gmdate('Y-m-d\TH:i:s\Z', strtotime('+1 year'))
                    . "\nPreferred-Languages: nl, en\nCanonical: " . url('.well-known/security.txt') . "\n",
                    'text/plain'
                );
            },
        ],
        [
            'pattern' => 'robots.txt',
            'action' => function () {
                // Pages with noindex (donation, thank-you) stay allowed: a blocked page can't show its noindex.
                $rules = option('tess.noindex')
                    ? "User-agent: *\nDisallow: /\n"
                    : "User-agent: *\nAllow: /\n\nSitemap: " . url('sitemap.xml') . "\n";
                return new Response($rules, 'text/plain');
            },
        ],
    ],
]);
