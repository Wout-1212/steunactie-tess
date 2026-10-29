<?php

return function ($site, $kirby) {
    // Totals come from plugins/tess-fundraisers, so the Panel overview shows the same numbers.
    $raised = $site->raisedTotal();
    $donations = $site->onlineDonations();
    $goal = $site->goal()->toFloat();
    $hasGoal = $goal > 0;

    $locale = strtok($kirby->language()?->locale(LC_ALL) ?? 'nl_BE', '.');
    $euro = new NumberFormatter($locale, NumberFormatter::CURRENCY);
    $euro->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
    $format = fn(float $amount) => $euro->formatCurrency($amount, 'EUR');

    $milestones = array_map(
        fn (array $milestone) => array_replace($milestone, ['amount' => $format($milestone['amount'])]),
        $site->milestoneProgress()
    );

    return [
        'progress' => [
            'hasGoal' => $hasGoal,
            'raised' => $format($raised),
            'goal' => $format($goal),
            // Round down so the bar never shows 100% before the goal is actually reached.
            'percent' => $hasGoal ? (int) min(100, floor($raised / $goal * 100)) : 0,
            'milestones' => $milestones,
            'donations' => $donations !== null
                ? (new NumberFormatter($locale, NumberFormatter::DECIMAL))->format($donations)
                : null,
        ],
    ];
};
