<?php

Kirby::plugin('tess/dates', [
    'fieldMethods' => [
        // Formats a date field in the current language, e.g. "8 oktober 2026" / "8 octobre 2026".
        'toLocalDate' => function ($field) {
            $locale = strtok(kirby()->language()?->locale(LC_ALL) ?? 'nl_BE', '.');
            $formatter = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE);
            return $field->toDate($formatter);
        },
    ],
]);
