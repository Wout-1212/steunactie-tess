<?php

/**
 * Copy this file to config.{host}.php for each host that sends mail, e.g. config.samenvoortess.be.php
 * (and config.www.samenvoortess.be.php) on the server, or config.theagency2627-steunactie-tess.test.php
 * locally. Kirby merges it into config.php. These files are gitignored: never commit real credentials.
 *
 * Brevo: Settings → SMTP & API → SMTP tab shows the login and lets you generate an SMTP key.
 * Mollie: Developers → API keys. Use the test_ key on test servers and locally, the live_ key only on
 * the live site. Leave it out to keep online donations switched off.
 */
return [
    'email' => [
        'transport' => [
            'username' => 'xxxxxx@smtp-brevo.com',
            'password' => 'xsmtpsib-...',
        ],
    ],
    'tess.mollie.key' => 'test_...',
];
