<?php

use Kirby\Exception\InvalidArgumentException;
use Kirby\Exception\NotFoundException;
use Kirby\Toolkit\Str;

/**
 * Panel area "Donaties": a table of donations with filters, search and paging (view component in
 * index.js), a dialog to read and edit a message, and API routes for the quick actions.
 * Amounts, payment details and email addresses are read-only.
 */

const TESS_DONATION_PAYMENT_LABELS = [
    'paid' => 'Betaald', 'open' => 'Open', 'failed' => 'Mislukt',
    'canceled' => 'Geannuleerd', 'expired' => 'Verlopen', 'refunded' => 'Terugbetaald',
];
const TESS_DONATION_MESSAGE_LABELS = ['visible' => 'Zichtbaar', 'held' => 'Te controleren', 'hidden' => 'Verborgen'];

// Filter key → condition. 'paid' is the default: those are the donations that count.
function tessDonationFilters(): array
{
    return [
        'paid' => ['label' => 'Betaald', 'where' => ['payment_status' => 'paid']],
        'held' => ['label' => 'Te controleren', 'where' => ['payment_status' => 'paid', 'message_status' => 'held']],
        'hidden' => ['label' => 'Verborgen', 'where' => ['payment_status' => 'paid', 'message_status' => 'hidden']],
        'other' => ['label' => 'Niet betaald', 'where' => "payment_status != 'paid'"],
    ];
}

function tessDonationLocalTime(string|null $utc): string
{
    return $utc ? (new DateTime($utc, new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Europe/Brussels'))->format('d-m-Y H:i') : '';
}

function tessDonationPanelView(): array
{
    $filters = tessDonationFilters();
    $filter = array_key_exists(get('filter'), $filters) ? get('filter') : 'paid';
    $search = Str::trim((string) get('q'));
    $page = max(1, (int) get('page', 1));
    $limit = 25;

    $query = fn () => tessDonationsDb()->table('donations')->where($filters[$filter]['where']);
    $results = $query();
    if ($search !== '') {
        $like = '%' . addcslashes($search, '%_\\') . '%';
        $results->where("(name LIKE :q1 ESCAPE '\\' OR email LIKE :q2 ESCAPE '\\' OR message LIKE :q3 ESCAPE '\\')", ['q1' => $like, 'q2' => $like, 'q3' => $like]);
    }
    $total = (clone $results)->count();
    $rows = $results->order('COALESCE(paid_at, created_at) DESC')->offset(($page - 1) * $limit)->limit($limit)->all();

    return [
        'component' => 'k-donations-view',
        'title' => 'Donaties',
        'props' => [
            'enabled' => site()->mollieEnabled(),
            'testMode' => site()->mollieTestMode(),
            'filter' => $filter,
            'filters' => array_map(fn ($key) => [
                'value' => $key,
                'text' => $filters[$key]['label'] . ' (' . tessDonationsDb()->table('donations')->where($filters[$key]['where'])->count() . ')',
            ], array_keys($filters)),
            'search' => $search,
            'pagination' => ['page' => $page, 'limit' => $limit, 'total' => $total],
            'rows' => array_map('tessDonationPanelRow', array_values($rows->data())),
        ],
    ];
}

function tessDonationPanelRow(object $donation): array
{
    $paid = $donation->payment_status === 'paid';
    $hasMessage = $donation->message !== '';
    $options = [['click' => 'edit', 'icon' => 'edit', 'text' => $hasMessage ? 'Bekijken / bewerken' : 'Bekijken']];
    if ($paid && $hasMessage) {
        $options[] = match ($donation->message_status) {
            'held' => ['click' => 'approve', 'icon' => 'check', 'text' => 'Goedkeuren'],
            'hidden' => ['click' => 'show', 'icon' => 'preview', 'text' => 'Weer tonen'],
            default => ['click' => 'hide', 'icon' => 'hidden', 'text' => 'Verbergen'],
        };
    }
    if ($paid) {
        $options[] = ['click' => 'resend', 'icon' => 'email', 'text' => 'Bedankmail opnieuw versturen'];
    }

    return [
        'id' => (int) $donation->id,
        'date' => tessDonationLocalTime($donation->paid_at ?? $donation->created_at),
        'name' => $donation->name . ($donation->show_name ? '' : ' (anoniem)'),
        'amount' => '€ ' . number_format($donation->amount_cents / 100, 0, ',', '.') . ($donation->show_amount ? '' : ' (verborgen)'),
        'message' => $hasMessage ? Str::short($donation->message, 80) : '–',
        'wall' => $hasMessage ? TESS_DONATION_MESSAGE_LABELS[$donation->message_status] : '–',
        'payment' => TESS_DONATION_PAYMENT_LABELS[$donation->payment_status] ?? $donation->payment_status,
        'mail' => match (true) {
            $donation->mail_error !== null => 'Mislukt',
            $donation->mailed_at !== null => 'Verstuurd',
            default => '–',
        },
        'options' => $options,
    ];
}

function tessDonationById(int $id): object
{
    return tessDonationsDb()->table('donations')->where(['id' => $id])->first()
        ?: throw new NotFoundException(message: 'Deze donatie bestaat niet (meer).');
}

// Read everything about a donation; only the message can be changed.
function tessDonationEditDialog(int $id): array
{
    $donation = tessDonationById($id);
    $readonly = fn (string $label, string|null $value, string $width = '1/2') => ['type' => 'text', 'label' => $label, 'disabled' => true, 'width' => $width, 'value' => $value];

    return [
        'component' => 'k-form-dialog',
        'props' => [
            'size' => 'large',
            'fields' => [
                'name' => $readonly('Naam', $donation->name . ($donation->show_name ? '' : ' (wil anoniem blijven)')),
                'email' => $readonly('E-mailadres', $donation->email),
                'amount' => $readonly('Bedrag', '€ ' . number_format($donation->amount_cents / 100, 0, ',', '.') . ($donation->show_amount ? '' : ' (niet tonen)')),
                'payment' => $readonly('Betaling', (TESS_DONATION_PAYMENT_LABELS[$donation->payment_status] ?? $donation->payment_status)
                    . ($donation->method ? ' via ' . $donation->method : '') . ' · ' . ($donation->payment_id ?? 'geen Mollie-ID')),
                'date' => $readonly('Betaald op', tessDonationLocalTime($donation->paid_at) ?: '–'),
                'mail' => $readonly('Bedankmail', $donation->mail_error ? 'Mislukt: ' . $donation->mail_error : ($donation->mailed_at ? 'Verstuurd' : 'Nog niet verstuurd')),
                'message' => [
                    'type' => 'textarea',
                    'label' => 'Bericht',
                    'buttons' => false,
                    'maxlength' => option('tess.donations.messageLength'),
                    'help' => 'Haal hier bijvoorbeeld een telefoonnummer of andere persoonlijke gegevens weg. De rest van het bericht blijft staan.',
                ],
            ],
            'value' => ['message' => $donation->message],
            'submitButton' => ['text' => 'Bericht opslaan', 'icon' => 'check'],
        ],
    ];
}

function tessDonationEditSubmit(int $id): bool
{
    tessDonationById($id);
    $message = tessDonationCleanMessage((string) get('message'));
    if (Str::length($message) > option('tess.donations.messageLength')) {
        throw new InvalidArgumentException(message: 'Het bericht is te lang.');
    }
    tessDonationsDb()->table('donations')->update(['message' => $message, 'updated_at' => tessDonationsNow()], ['id' => $id]);
    return true;
}

// Quick actions from the table: show, hide, approve, resend.
function tessDonationAction(int $id, string $action): array
{
    $donation = tessDonationById($id);
    if ($donation->payment_status !== 'paid') {
        throw new InvalidArgumentException(message: 'Dit kan alleen bij een betaalde donatie.');
    }

    if ($action === 'resend') {
        tessDonationsDb()->table('donations')->update(['mailed_at' => tessDonationsNow(), 'updated_at' => tessDonationsNow()], ['id' => $id]);
        if (!tessDonationMail(tessDonationById($id))) {
            throw new InvalidArgumentException(message: 'Versturen is opnieuw mislukt. Controleer de Brevo-instellingen.');
        }
        return ['message' => 'De bedankmail is opnieuw verstuurd.'];
    }

    $status = ['show' => 'visible', 'approve' => 'visible', 'hide' => 'hidden'][$action];
    tessDonationsDb()->table('donations')->update(['message_status' => $status, 'updated_at' => tessDonationsNow()], ['id' => $id]);
    return ['message' => $status === 'visible' ? 'Het bericht staat op de website.' : 'Het bericht is verborgen.'];
}
