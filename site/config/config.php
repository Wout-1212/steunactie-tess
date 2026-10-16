<?php

return [
    'debug' => true,
    'languages' => true,

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
];
