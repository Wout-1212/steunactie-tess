<?php

use Kirby\Http\Response;
use Mollie\Api\Exceptions\NotFoundException;
use Mollie\Api\Resources\Payment;

/**
 * Payment status comes from Mollie only: the webhook (and, as a fallback, the thank-you page) fetches
 * the payment from the API and copies its status here. Running it twice changes nothing.
 */

function tessDonationFind(string $reference): object|null
{
    return tessDonationsDb()->table('donations')->where(['reference' => $reference])->first() ?: null;
}

// POST /mollie/webhook. Mollie only posts the payment id and does not sign the request, so the
// payment is always fetched from the API. 200 = handled (also for unknown ids), 500 = Mollie retries later.
function tessDonationWebhook(): Response
{
    $id = tessField('id');
    if (!site()->mollieEnabled() || preg_match('/^tr_\w+$/', $id) !== 1) {
        return new Response('', 'text/plain', 200);
    }

    try {
        $payment = tessMollie()->payments->get($id);
        $donation = tessDonationFind((string) ($payment->metadata->reference ?? ''));
        if ($donation && $donation->payment_id === $payment->id) {
            tessDonationSync($donation->reference, $payment);
        }
    } catch (NotFoundException) {
        // A payment Mollie doesn't know: made up, or from another account. Handled, nothing to retry.
        return new Response('', 'text/plain', 200);
    } catch (Throwable $exception) {
        error_log('Donatie: webhook voor ' . $id . ' mislukt: ' . $exception->getMessage());
        return new Response('', 'text/plain', 500);
    }

    return new Response('', 'text/plain', 200);
}

// Fetches the payment and syncs it; used by the thank-you page. Returns the up-to-date row.
function tessDonationRefresh(object $donation): object
{
    if ($donation->payment_id && in_array($donation->payment_status, ['open', 'paid'], true)) {
        try {
            tessDonationSync($donation->reference, tessMollie()->payments->get($donation->payment_id));
        } catch (Throwable $exception) {
            error_log('Donatie: status ophalen voor ' . $donation->reference . ' mislukt: ' . $exception->getMessage());
        }
    }
    return tessDonationFind($donation->reference);
}

function tessDonationSync(string $reference, Payment $payment): void
{
    $db = tessDonationsDb();
    $pdo = $db->connection();

    // IMMEDIATE takes the write lock at once, so a webhook and the thank-you page never both decide
    // to send the mail: whoever comes second sees mailed_at already filled in.
    $pdo->exec('BEGIN IMMEDIATE');
    try {
        $donation = tessDonationFind($reference);
        $status = tessDonationStatus($payment);
        // A failed mail resets mailed_at (see tessDonationMail), so the next sync simply tries again.
        $sendMail = $status === 'paid' && $donation->mailed_at === null;

        $changes = array_filter([
            'payment_status' => $status,
            'method' => $payment->method,
            'paid_at' => $payment->paidAt ? gmdate('Y-m-d H:i:s', strtotime($payment->paidAt)) : null,
            'mailed_at' => $sendMail ? tessDonationsNow() : null,
        ], fn ($value) => $value !== null);

        $changed = $status !== $donation->payment_status;
        $db->table('donations')->update($changes + ['updated_at' => tessDonationsNow()], ['reference' => $reference]);
        $pdo->exec('COMMIT');
    } catch (Throwable $exception) {
        $pdo->exec('ROLLBACK');
        throw $exception;
    }

    if ($changed) {
        kirby()->cache('tess.donations')->remove('totals');
    }
    if ($sendMail) {
        tessDonationMail(tessDonationFind($reference));
    }
}

// Mollie status → ours. A fully refunded or charged back payment no longer counts.
function tessDonationStatus(Payment $payment): string
{
    if ($payment->isPaid()) {
        $returned = $payment->getAmountRefunded() + $payment->getAmountChargedBack();
        return $returned >= (float) $payment->amount->value ? 'refunded' : 'paid';
    }
    return match ($payment->status) {
        'failed', 'canceled', 'expired' => $payment->status,
        default => 'open',
    };
}
