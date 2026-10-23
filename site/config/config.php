<?php

return [
    // Never show error details to visitors. The local host config switches this on.
    'debug' => false,
    'languages' => true,
    // Panel login: password plus a code (authenticator app, or by email when none is set up).
    'auth' => [
        'methods' => ['password' => ['2fa' => true]],
    ],

    // Brevo SMTP relay. Login and SMTP key live in the host-specific config (see config.host.example.php).
    'email' => [
        'transport' => [
            'type' => 'smtp',
            'host' => 'smtp-relay.brevo.com',
            'port' => 587,
            'security' => 'tls',
            'auth' => true,
        ],
    ],
    'tess.contact.from' => 'contact@samenvoortess.be',
    'tess.contact.fromName' => 'Samen voor Tess',
    // Keep old URLs working while the original pages remain editable legal sources.
    'routes' => [
        [
            'pattern' => '(privacy|algemene-voorwaarde)',
            'language' => '*',
            'action' => function ($language, $source) {
                $target = page('disclaimer');
                if (!$target) return $this->next();
                $anchor = $source === 'privacy' ? 'privacy' : 'algemene-voorwaarden';
                return Kirby\Http\Response::redirect($target->url($language->code()) . '#' . $anchor, 301);
            },
        ],
    ],
];
