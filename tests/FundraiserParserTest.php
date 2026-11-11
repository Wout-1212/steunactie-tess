<?php

use PHPUnit\Framework\TestCase;

/**
 * Reading the totals from the public Steunactie and GoFundMe pages (plugins/tess-fundraisers).
 * The fixtures are trimmed copies of the parts of those pages the parsers rely on.
 */
final class FundraiserParserTest extends TestCase
{
    private static function steunactie(string $total, string $count = '245'): string
    {
        return <<<HTML
            <!doctype html><html><head><meta charset="utf-8"></head><body>
            <div class="campaign-stats">
                <span class="total_donations">{$total}</span>
                <span class="total_donations_goal">€ 50.000</span>
                <span class="nr-donations">{$count}</span>
            </div>
            </body></html>
            HTML;
    }

    public function testSteunactieTotalAndCount(): void
    {
        $this->assertSame(['raised' => 30040, 'donations' => 245], tessParseSteunactie(self::steunactie('€ 30.040')));
    }

    public function testSteunactieDropsTheCents(): void
    {
        $this->assertSame(30040, tessParseSteunactie(self::steunactie('€ 30.040,50'))['raised']);
    }

    public function testSteunactieWithoutCountStillGivesTheTotal(): void
    {
        $this->assertSame(['raised' => 1200, 'donations' => null], tessParseSteunactie(self::steunactie('€ 1.200', '')));
    }

    public function testSteunactiePageWithoutTotalGivesNull(): void
    {
        $this->assertNull(tessParseSteunactie('<html><body><p>Onderhoud</p></body></html>'));
    }

    private static function gofundme(array $amount = ['amount' => 12345.67, 'currencyCode' => 'EUR'], int $count = 321): string
    {
        $data = json_encode(['props' => ['pageProps' => ['__APOLLO_STATE__' => [
            'ROOT_QUERY' => [
                'fundraiser:{"slug":"samen-voor-tess"}' => ['__ref' => 'Fundraiser:123'],
            ],
            'Fundraiser:123' => ['currentAmount' => $amount, 'donationCount' => $count],
            // Another campaign on the same page (e.g. "similar fundraisers") must not be picked.
            'Fundraiser:999' => ['currentAmount' => ['amount' => 99999, 'currencyCode' => 'EUR'], 'donationCount' => 9],
        ]]]]);

        return '<!doctype html><html><body><div id="__next"></div><script id="__NEXT_DATA__" type="application/json">'
            . $data . '</script></body></html>';
    }

    public function testGofundmeReadsTheCampaignOnThePage(): void
    {
        $this->assertSame(['raised' => 12345, 'donations' => 321], tessParseGofundme(self::gofundme()));
    }

    public function testGofundmeInAnotherCurrencyIsIgnored(): void
    {
        $this->assertNull(tessParseGofundme(self::gofundme(['amount' => 500, 'currencyCode' => 'USD'])));
    }

    public function testGofundmeWithBrokenJsonGivesNull(): void
    {
        $this->assertNull(tessParseGofundme('<html><script id="__NEXT_DATA__">{"props": </script></html>'));
    }

    public function testGofundmeWithoutDataGivesNull(): void
    {
        $this->assertNull(tessParseGofundme('<html><body>Page not found</body></html>'));
    }
}
