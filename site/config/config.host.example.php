<?php

/**
 * Copy this file to config.{host}.php for each host, e.g. config.samenvoortess.be.php
 * (and config.www.samenvoortess.be.php) on the server, or config.theagency2627-steunactie-tess.test.php
 * locally. Kirby merges it into config.php. These files are gitignored: never commit real credentials.
 *
 * url: the one address of the site. Kirby otherwise trusts the Host header the browser sends, and that
 * address ends up in the Mollie redirect and webhook, the mails and the Panel links.
 * content.salt and cookie.key: long random values, different per server. Generate each with
 * php -r "echo bin2hex(random_bytes(32));"
 * Brevo: Settings → SMTP & API → SMTP tab shows the login and lets you generate an SMTP key.
 * Mollie: Developers → API keys. Use the test_ key on test servers and locally, the live_ key only on
 * the live site. Leave it out to keep online donations switched off.
 * tess.noindex: only on the test server. Keeps the whole site out of search engines; never on the live site.
 */
return [
    'url' => 'https://samenvoortess.be',
    'content' => [
        'salt' => '…64 random hex characters…',
    ],
    'cookie' => [
        'key' => '…64 random hex characters…',
    ],
    'email' => [
        'transport' => [
            'username' => 'xxxxxx@smtp-brevo.com',
            'password' => 'xsmtpsib-...',
        ],
    ],
    'tess.mollie.key' => 'test_...',
    // 'tess.noindex' => true,
];
