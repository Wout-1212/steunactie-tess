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
                    'info' => $this->mollieEnabled() ? 'Steunactie, GoFundMe en de site samen' : 'Steunactie en GoFundMe samen',
                    'link' => $panel . '/site?tab=donations',
                ];
            }

            if ($held = $this->heldMessages()) {
                $reports[] = [
                    'label' => 'Berichten te controleren',
                    'value' => (string) $held,
                    'info' => 'Met een link, e-mailadres of telefoonnummer',
                    'theme' => 'notice',
                    'link' => $panel . '/donations?filter=held',
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

            ['done' => $done, 'total' => $total] = $this->medicalProgress();
            $reports[] = [
                'label' => 'Weegschaal',
                'value' => $total ? "$done van $total stappen" : 'Nog geen stappen',
                'info' => $total ? 'Behandelstappen gedaan, los van de donaties' : 'Voeg ze toe bij Traject',
                'theme' => $total ? null : 'notice',
                'link' => $panel . '/site?tab=journey',
            ];

            return $reports;
        },

        // Steps on the medical scale (Traject tab): done, and done plus planned. The homepage scale counts with this too,
        // so the Panel and the site always show the same numbers.
        'medicalProgress' => function (): array {
            $steps = $this->medical_steps()->toStructure();
            $done = $steps->filterBy('status', 'done')->count();
            return ['done' => $done, 'total' => $done + $steps->filterBy('status', 'planned')->count()];
        },

        // One-line status under the scale steps in the Panel, so editors see what the homepage shows.
        'medicalStatus' => function (): string {
            ['done' => $done, 'total' => $total] = $this->medicalProgress();
            return $total
                ? "Nu op de homepage: $done van $total stappen gedaan."
                : 'Nog geen stappen. Op de homepage staat dat ze volgen zodra ze bevestigd zijn.';
        },
    ],
]);
