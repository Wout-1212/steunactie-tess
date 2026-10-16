<?php

return [
    'debug' => true,
    'languages' => true,
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
