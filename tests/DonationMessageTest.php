<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Messages on the donation wall: cleaning what the donor typed, and holding messages with contact
 * details for approval in the Panel (plugins/tess-donations/lib/payment.php).
 */
final class DonationMessageTest extends TestCase
{
    public static function heldMessages(): array
    {
        return [
            'link' => ['Kijk op https://voorbeeld.be/actie'],
            'www without scheme' => ['Surf naar www.voorbeeld.com'],
            'bare domain' => ['Meer op mijnwinkel.shop'],
            'email address' => ['Mail me: jan.peeters@voorbeeld.be'],
            'phone with spaces' => ['Bel 0470 12 34 56'],
            'international phone' => ['+32 470 12 34 56'],
            'phone with dots and slashes' => ['0470/12.34.56'],
            // Eight digits in a row look like a phone number. Holding one message too many is the safer mistake.
            'date (false positive)' => ['Tot 11-11-2026!'],
        ];
    }

    public static function visibleMessages(): array
    {
        return [
            'plain wish' => ['Veel sterkte Tess, we denken aan je!'],
            'amount' => ['Ik geef € 50 en mijn collega’s ook'],
            'short number' => ['Groetjes van huisnummer 12345'],
            'emoji and line break' => ["Hou vol 💪\nDe buren"],
            'empty' => [''],
        ];
    }

    #[DataProvider('heldMessages')]
    public function testMessagesWithContactDetailsWaitForApproval(string $message): void
    {
        $this->assertTrue(tessDonationNeedsReview($message));
    }

    #[DataProvider('visibleMessages')]
    public function testOtherMessagesAreShownAtOnce(string $message): void
    {
        $this->assertFalse(tessDonationNeedsReview($message));
    }

    public static function messagesToClean(): array
    {
        return [
            'Windows line breaks' => ["Hallo\r\nTess", "Hallo\nTess"],
            'tab becomes a space' => ["Hallo\tTess", 'Hallo Tess'],
            'control characters removed' => ["Hal\x00lo\x07 Tess\x1B", 'Hallo Tess'],
            'at most one empty line' => ["Hallo\n\n\n\n\nTess", "Hallo\n\nTess"],
            'trimmed' => ["  \n Hallo Tess \n ", 'Hallo Tess'],
            'unicode kept' => ['Beterschap, Zoë ❤️', 'Beterschap, Zoë ❤️'],
        ];
    }

    #[DataProvider('messagesToClean')]
    public function testMessagesAreCleaned(string $input, string $expected): void
    {
        $this->assertSame($expected, tessDonationCleanMessage($input));
    }
}
