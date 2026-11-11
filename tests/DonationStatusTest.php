<?php

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Mollie's payment status → the status in the donations table (tessDonationStatus in lib/sync.php).
 * Only 'paid' counts towards the total and appears on the donation wall.
 */
final class DonationStatusTest extends TestCase
{
    // A payment as the Mollie API returns it, without calling the API.
    private static function payment(string $status, string|null $refunded = null, string|null $chargedBack = null): Payment
    {
        $payment = new Payment(new MollieApiClient());
        $payment->status = $status;
        $payment->amount = (object) ['currency' => 'EUR', 'value' => '25.00'];
        $payment->paidAt = $status === 'paid' ? '2026-10-29T14:30:00+00:00' : null;
        $payment->amountRefunded = $refunded ? (object) ['currency' => 'EUR', 'value' => $refunded] : null;
        $payment->amountChargedBack = $chargedBack ? (object) ['currency' => 'EUR', 'value' => $chargedBack] : null;
        return $payment;
    }

    public static function statuses(): array
    {
        return [
            'paid' => [self::payment('paid'), 'paid'],
            'partly refunded still counts' => [self::payment('paid', refunded: '10.00'), 'paid'],
            'fully refunded' => [self::payment('paid', refunded: '25.00'), 'refunded'],
            'charged back' => [self::payment('paid', chargedBack: '25.00'), 'refunded'],
            'refund and chargeback together' => [self::payment('paid', refunded: '15.00', chargedBack: '10.00'), 'refunded'],
            'failed' => [self::payment('failed'), 'failed'],
            'canceled' => [self::payment('canceled'), 'canceled'],
            'expired' => [self::payment('expired'), 'expired'],
            'open' => [self::payment('open'), 'open'],
            'pending (bank transfer, card check)' => [self::payment('pending'), 'open'],
            'authorized' => [self::payment('authorized'), 'open'],
        ];
    }

    #[DataProvider('statuses')]
    public function testMollieStatusMapsToOurs(Payment $payment, string $expected): void
    {
        $this->assertSame($expected, tessDonationStatus($payment));
    }
}
