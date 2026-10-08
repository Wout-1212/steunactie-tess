<?php

return function ($site) {
    $goal = $site->goal()->toFloat();
    $raised = max(0, $site->raised()->toFloat());
    $hasTotals = $goal > 0 && $site->raised()->isNotEmpty();

    return [
        'progress' => [
            'goal' => $goal,
            'raised' => $raised,
            'hasTotals' => $hasTotals,
            'percent' => $hasTotals ? min(100, round($raised / $goal * 100)) : 14,
        ],
    ];
};
