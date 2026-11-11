<?php

use Kirby\Cms\App;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Protection shared by the contact and donation forms (plugins/tess-contact): the rate limit per
 * visitor and the check for forms posted from another site.
 */
final class FormProtectionTest extends TestCase
{
    private App $kirby;

    protected function setUp(): void
    {
        $this->kirby = kirby();
        $this->kirby->cache('tess.contact')->flush();
    }

    protected function tearDown(): void
    {
        App::instance($this->kirby);
    }

    // Kirby reads the request from its environment, so each case gets a copy of the app with its own headers.
    private function request(array $headers): void
    {
        $server = ['REMOTE_ADDR' => '203.0.113.7', 'REQUEST_METHOD' => 'POST'];
        foreach ($headers as $name => $value) {
            $server['HTTP_' . strtoupper(str_replace('-', '_', $name))] = $value;
        }
        $this->kirby->clone(['server' => $server]);
    }

    public function testRateLimitAllowsFiveMessagesPerHour(): void
    {
        $this->request([]);
        for ($i = 1; $i <= 5; $i++) {
            $this->assertTrue(tessAllow('contact'), "Message $i should be allowed.");
        }
        $this->assertFalse(tessAllow('contact'));
    }

    // Regression: an entry the cache kept a few seconds past its window used to be stored again with
    // expiry 0, which Kirby reads as "never", so that visitor stayed blocked until the cache was cleared.
    public function testAnExpiredWindowStartsOver(): void
    {
        $this->request([]);
        $cache = kirby()->cache('tess.contact');
        $cache->set(tessVisitorHash(), ['count' => 5, 'until' => time() - 5], 1);

        $this->assertTrue(tessAllow('contact'));
        $entry = $cache->get(tessVisitorHash());
        $this->assertSame(1, $entry['count']);
        $this->assertGreaterThan(time(), $cache->expires(tessVisitorHash()));
    }

    public function testTheCacheKeyIsNotThePlainIpAddress(): void
    {
        $this->request([]);
        $this->assertNotSame(hash('sha256', '203.0.113.7'), tessVisitorHash());
        $this->assertStringNotContainsString('203.0.113.7', tessVisitorHash());
    }

    public static function origins(): array
    {
        return [
            'no browser headers (curl, server)' => [[], false],
            'same origin (Fetch Metadata)' => [['Sec-Fetch-Site' => 'same-origin'], false],
            'started by the visitor' => [['Sec-Fetch-Site' => 'none'], false],
            'other site (Fetch Metadata)' => [['Sec-Fetch-Site' => 'cross-site'], true],
            'subdomain (Fetch Metadata)' => [['Sec-Fetch-Site' => 'same-site'], true],
            'same origin (older browser)' => [['Origin' => 'https://samenvoortess.test'], false],
            'other site (older browser)' => [['Origin' => 'https://evil.example'], true],
            'Fetch Metadata wins over Origin' => [['Sec-Fetch-Site' => 'cross-site', 'Origin' => 'https://samenvoortess.test'], true],
        ];
    }

    #[DataProvider('origins')]
    public function testFormsFromOtherSitesAreRefused(array $headers, bool $foreign): void
    {
        $this->request($headers);
        $this->assertSame($foreign, tessForeignOrigin());
    }
}
