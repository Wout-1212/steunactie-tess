<?php

return function ($site, $kirby) {
    // Each source gets its own field so scraping/Mollie can fill them separately later.
    // Steunactie is read automatically (plugins/tess-steunactie); the Panel field is the fallback.
    $steunactie = $site->steunactie();
    $raised = ($steunactie['raised'] ?? $site->raised_steunactie()->toFloat())
        + $site->raised_gofundme()->toFloat()
        + $site->raised_other()->toFloat();
    $goal = $site->goal()->toFloat();
    $hasGoal = $goal > 0;

    $locale = strtok($kirby->language()?->locale(LC_ALL) ?? 'nl_BE', '.');
    $euro = new NumberFormatter($locale, NumberFormatter::CURRENCY);
    $euro->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
    $format = fn (float $amount) => $euro->formatCurrency($amount, 'EUR');

    $milestones = [];
    foreach ($site->milestones()->toStructure()->sortBy('amount', 'asc', SORT_NUMERIC) as $milestone) {
        $milestones[] = [
            'amount' => $format($milestone->amount()->toFloat()),
            'label' => $milestone->label()->value(),
            'reached' => $raised >= $milestone->amount()->toFloat(),
        ];
    }

    return [
        'progress' => [
            'hasGoal' => $hasGoal,
            'raised' => $format($raised),
            'goal' => $format($goal),
            // Round down so the bar never shows 100% before the goal is actually reached.
            'percent' => $hasGoal ? (int) min(100, floor($raised / $goal * 100)) : 0,
            'milestones' => $milestones,
            'donations' => isset($steunactie['donations'])
                ? (new NumberFormatter($locale, NumberFormatter::DECIMAL))->format($steunactie['donations'])
                : null,
        ],
    ];
};
