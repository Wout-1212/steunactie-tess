<?php

use Kirby\Database\Database;
use PHPUnit\Framework\TestCase;

/**
 * Schema migrations of the donations database (tessDonationsMigrate in lib/database.php), each run
 * on a fresh in-memory SQLite database.
 */
final class DatabaseMigrationTest extends TestCase
{
    private Database $db;

    protected function setUp(): void
    {
        $this->db = new Database(['type' => 'sqlite', 'database' => ':memory:']);
        $this->db->fail();
    }

    private function version(): int
    {
        return (int) $this->db->query('PRAGMA user_version')->first()->user_version;
    }

    public function testCreatesTheSchemaAndRemembersTheVersion(): void
    {
        tessDonationsMigrate($this->db);

        $this->assertSame(1, $this->version());
        $this->assertTrue($this->db->validateTable('donations'));
    }

    public function testRunningAgainChangesNothing(): void
    {
        tessDonationsMigrate($this->db);
        tessDonationsMigrate($this->db);

        $this->assertSame(1, $this->version());
    }

    public function testDefaultsAndUniqueReference(): void
    {
        tessDonationsMigrate($this->db);
        $row = ['reference' => 'abc', 'amount_cents' => 2500, 'name' => 'Jan', 'email' => 'jan@voorbeeld.be',
            'language' => 'nl', 'created_at' => tessDonationsNow(), 'updated_at' => tessDonationsNow()];
        $this->db->table('donations')->insert($row);

        $donation = $this->db->table('donations')->where(['reference' => 'abc'])->first();
        $this->assertSame('open', $donation->payment_status);
        $this->assertSame('visible', $donation->message_status);
        $this->assertSame('', $donation->message);

        $this->expectException(PDOException::class);
        $this->db->table('donations')->insert($row);
    }

    // The migration's second statement fails (the index name is taken), so the table it already created
    // is rolled back as well, and the version stays at 0 so it runs again once the problem is fixed.
    public function testAFailedMigrationLeavesNothingBehind(): void
    {
        $this->db->execute('CREATE TABLE donations_wall (id INTEGER)');

        try {
            tessDonationsMigrate($this->db);
            $this->fail('The migration should have failed.');
        } catch (PDOException) {
        }

        $this->assertSame(0, $this->version());
        $this->assertFalse($this->db->validateTable('donations'));
    }
}
