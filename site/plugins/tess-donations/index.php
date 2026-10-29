<?php

/**
 * Online donations via Mollie: donation form on the homepage, payment, webhook, thank-you mail
 * and the Donaties area in the Panel. The Mollie key lives in the host config (config.{host}.php,
 * never in Git) as 'tess.mollie.key'. Without a key, the site keeps working as before.
 * See TASKS.md for the phases.
 */

use Kirby\Cms\Language;
use Mollie\Api\MollieApiClient;

require_once __DIR__ . '/lib/database.php';
require_once __DIR__ . '/lib/payment.php';
require_once __DIR__ . '/lib/sync.php';
require_once __DIR__ . '/lib/mail.php';
require_once __DIR__ . '/lib/panel.php';
require_once __DIR__ . '/lib/preview.php';

function tessMollie(): MollieApiClient
{
    static $client = null;
    return $client ??= (new MollieApiClient())->setApiKey(option('tess.mollie.key'));
}

Kirby::plugin('tess/donations', [
    'options' => [
        'cache' => true,
        // Path of the SQLite file; null = site/storage/donations.sqlite. Outside the web root is safest.
        'database' => null,
        // Whole euros.
        'min' => 1,
        'max' => 10000,
        'messageLength' => 500,
        // Donation attempts per IP address per window (minutes).
        'limit' => 10,
        'window' => 60,
        'minSeconds' => 3,
        // null = {site}/mollie/webhook, left out on local hosts. Set it to a tunnel URL to test webhooks locally.
        'webhookUrl' => null,
    ],
    'routes' => [
        [
            'pattern' => 'doneren',
            'method' => 'POST',
            'language' => '*',
            'action' => fn (Language $language) => tessDonationStart($language),
        ],
        [
            // Batches for the "all donations" popup on the homepage, rendered with the same snippet as the wall.
            'pattern' => 'donaties/lijst',
            'language' => '*',
            'action' => function () {
                $perPage = 20;
                $page = max(1, (int) get('page', 1));
                $donations = site()->recentDonations($perPage + 1, ($page - 1) * $perPage);
                $html = implode('', array_map(
                    fn ($donation) => snippet('components/donation-item', ['donation' => $donation], return: true),
                    array_slice($donations, 0, $perPage)
                ));
                return Kirby\Http\Response::json(['html' => $html, 'next' => count($donations) > $perPage ? $page + 1 : null]);
            },
        ],
        [
            'pattern' => 'mollie/webhook',
            'method' => 'POST',
            'action' => fn () => tessDonationWebhook(),
        ],
        [
            // Local preview of the thank-you mail; 404 unless 'debug' is on (see lib/preview.php).
            'pattern' => ['dev/mail', 'dev/mail/(donation)'],
            'action' => fn (string|null $type = null) => tessDonationMailPreview($type),
        ],
    ],
    // Panel: own menu item with the donations table (see lib/panel.php and index.js).
    'areas' => [
        'donations' => fn () => [
            'label' => 'Donaties',
            'icon' => 'heart',
            'menu' => true,
            'link' => 'donations',
            'views' => [
                ['pattern' => 'donations', 'action' => fn () => tessDonationPanelView()],
            ],
            'dialogs' => [
                'donation.edit' => [
                    'pattern' => 'donations/(:num)/edit',
                    'load' => fn (string $id) => tessDonationEditDialog((int) $id),
                    'submit' => fn (string $id) => tessDonationEditSubmit((int) $id),
                ],
            ],
        ],
    ],
    // Quick actions from the table; API routes only work for logged-in Panel users.
    'api' => [
        'routes' => [
            [
                'pattern' => 'tess/donations/(:num)/(show|hide|approve|resend)',
                'method' => 'POST',
                'action' => fn (string $id, string $action) => tessDonationAction((int) $id, $action),
            ],
        ],
    ],
    'siteMethods' => [
        'mollieEnabled' => function (): bool {
            return is_string($key = option('tess.mollie.key')) && preg_match('/^(test|live)_\w+$/', $key) === 1;
        },
        // Test keys create fake payments; the site and Panel show a banner while this is on.
        'mollieTestMode' => function (): bool {
            return $this->mollieEnabled() && str_starts_with(option('tess.mollie.key'), 'test_');
        },
        // Paid donations through the site. Cached until a payment status changes (tessDonationSync).
        'mollieTotals' => function (): array {
            if (!$this->mollieEnabled()) {
                return ['raised' => 0.0, 'donations' => 0];
            }
            $cache = kirby()->cache('tess.donations');
            return $cache->getOrSet('totals', function () {
                $paid = tessDonationsDb()->table('donations')->where(['payment_status' => 'paid']);
                return ['raised' => (clone $paid)->sum('amount_cents') / 100, 'donations' => $paid->count()];
            });
        },
        // One line for the Donaties tab in the Panel.
        'mollieStatus' => function (): string {
            if (!$this->mollieEnabled()) {
                return 'Online doneren via Mollie staat uit: er is nog geen Mollie-sleutel ingesteld in de serverconfiguratie.';
            }
            $totals = $this->mollieTotals();
            return 'Via de site opgehaald: € ' . number_format($totals['raised'], 0, ',', '.') . ' (' . $totals['donations'] . ' donaties). '
                . 'Dit telt automatisch mee. Berichten en donaties beheer je bij Donaties in het menu links.'
                . ($this->mollieTestMode() ? ' TESTMODUS: dit zijn testbetalingen.' : '');
        },
        // Paid donations for the donation wall, newest first. Name and amount are null when the donor
        // chose not to show them; the message is empty unless it is visible (held or hidden ones stay out).
        'recentDonations' => function (int $limit = 10, int $offset = 0): array {
            if (!$this->mollieEnabled()) {
                return [];
            }
            $rows = tessDonationsDb()->table('donations')
                ->where(['payment_status' => 'paid'])
                ->order('COALESCE(paid_at, created_at) DESC, id DESC')
                ->offset($offset)
                ->limit($limit)
                ->all();
            return array_map(fn ($row) => [
                'name' => $row->show_name ? $row->name : null,
                'amount' => $row->show_amount ? $row->amount_cents / 100 : null,
                'message' => $row->message_status === 'visible' ? $row->message : '',
                'date' => $row->paid_at ?? $row->created_at,
            ], array_values($rows->data()));
        },
        // Messages waiting for approval in the Panel.
        'heldMessages' => function (): int {
            return $this->mollieEnabled()
                ? tessDonationsDb()->table('donations')->where(['payment_status' => 'paid', 'message_status' => 'held'])->count()
                : 0;
        },
    ],
]);
