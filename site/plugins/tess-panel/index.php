<?php

/**
 * Overview tab in the Panel (blueprints/tabs/overview.yml): the numbers editors check most,
 * each linking to the tab where they are changed.
 */
Kirby::plugin('tess/panel', [
    'siteMethods' => [
        'overviewReports' => function (): array {
            $euro = fn(float $amount) => '€ ' . number_format($amount, 0, ',', '.');
            $panel = kirby()->url('panel');

            $raised = $this->raisedTotal();
            $goal = $this->goal()->toFloat();
            $donations = $this->onlineDonations();
            $reports = [[
                'label' => 'Opgehaald',
                'value' => $euro($raised),
                'info' => $goal > 0
                    ? (int) min(100, floor($raised / $goal * 100)) . '% van ' . $euro($goal)
                    : 'Nog geen doelbedrag ingevuld',
                'theme' => $goal > 0 && $raised >= $goal ? 'positive' : null,
                'link' => $panel . '/site?tab=donations',
            ]];

            if ($donations !== null) {
                $reports[] = [
                    'label' => 'Online donaties',
                    'value' => number_format($donations, 0, ',', '.'),
                    'info' => 'Steunactie en GoFundMe samen',
                    'link' => $panel . '/site?tab=donations',
                ];
            }

            $updates = $this->find('updates')?->children()->listed();
            $latest = $updates?->sortBy('date', 'desc')->first();
            $reports[] = [
                'label' => 'Gepubliceerde updates',
                'value' => (string) ($updates?->count() ?? 0),
                'info' => $latest ? 'Laatste: ' . $latest->date()->toDate('d-m-Y') : 'Nog geen updates',
                'link' => $latest ? $latest->panel()->url() : $panel . '/site?tab=updates',
            ];

            $current = $this->journey_steps()->toStructure()->findBy('status', 'current');
            $reports[] = [
                'label' => 'Traject nu',
                'value' => $current ? $current->heading()->value() : 'Geen stap op Nu bezig',
                'theme' => $current ? null : 'notice',
                'link' => $panel . '/site?tab=journey',
            ];

            return $reports;
        },
    ],
]);
