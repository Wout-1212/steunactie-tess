<?php

use Kirby\Cms\App;

require dirname(__DIR__) . '/vendor/autoload.php';

// A Kirby instance on this project, so the plugins (and the functions in their lib files) load as on the
// site. Caches live in memory and no Mollie key is set, so tests never touch site/cache, the donations
// database or the Mollie API. Kirby's own error pages stay off, so PHPUnit reports errors itself.
App::$enableWhoops = false;
new App([
    'roots' => ['index' => dirname(__DIR__)],
    'options' => [
        'url' => 'https://samenvoortess.test',
        'tess.mollie.key' => null,
        'tess.contact.cache' => ['type' => 'memory'],
        'tess.donations.cache' => ['type' => 'memory'],
        'tess.fundraisers.cache' => ['type' => 'memory'],
    ],
]);
