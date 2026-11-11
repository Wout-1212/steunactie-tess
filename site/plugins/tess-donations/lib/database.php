<?php

use Kirby\Database\Database;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;

/**
 * SQLite storage for online donations: one file, by default site/storage/donations.sqlite (kept out
 * of Git). No database server is needed. Amounts are stored in cents, times in UTC.
 * The root .htaccess blocks /site on Apache, but nginx ignores .htaccess: there, deny /site in the
 * server config or point 'tess.donations.database' to a path outside the web root.
 */
function tessDonationsDb(): Database
{
    static $db = null;
    if ($db !== null) {
        return $db;
    }

    $file = option('tess.donations.database') ?? kirby()->root('site') . '/storage/donations.sqlite';
    $folder = dirname($file);
    if (!is_dir($folder)) {
        Dir::make($folder);
        // Second lock on Apache, in case the folder ever ends up somewhere the root rules don't cover.
        F::write($folder . '/.htaccess', "Require all denied\n");
    }

    $db = new Database(['type' => 'sqlite', 'database' => $file]);
    // Throw on errors instead of failing silently, so a broken query never looks like "no donations".
    $db->fail();
    // WAL lets the homepage read while a webhook writes; wait instead of failing when the file is busy.
    $db->execute('PRAGMA journal_mode = WAL');
    $db->execute('PRAGMA busy_timeout = 5000');

    tessDonationsMigrate($db);
    return $db;
}

/**
 * Schema changes, applied once each in order. PRAGMA user_version remembers how many have run,
 * so a later change is a new entry at the end of the list, never an edit of an old one.
 */
function tessDonationsMigrate(Database $db): void
{
    $migrations = [
        1 => [
            'CREATE TABLE donations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                reference TEXT NOT NULL UNIQUE,
                payment_id TEXT UNIQUE,
                amount_cents INTEGER NOT NULL,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                message TEXT NOT NULL DEFAULT \'\',
                show_name INTEGER NOT NULL DEFAULT 1,
                show_amount INTEGER NOT NULL DEFAULT 1,
                message_status TEXT NOT NULL DEFAULT \'visible\',
                payment_status TEXT NOT NULL DEFAULT \'open\',
                method TEXT,
                language TEXT NOT NULL,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL,
                paid_at TEXT,
                mailed_at TEXT,
                mail_error TEXT
            )',
            'CREATE INDEX donations_wall ON donations (payment_status, message_status, paid_at)',
        ],
    ];

    $version = (int) $db->query('PRAGMA user_version')->first()->user_version;
    foreach ($migrations as $target => $statements) {
        if ($target <= $version) {
            continue;
        }
        // All of a migration or nothing: SQLite can roll back CREATE and ALTER too, so a failed
        // migration leaves the old schema and the old version, and simply runs again next time.
        $pdo = $db->connection();
        $pdo->beginTransaction();
        try {
            foreach ($statements as $statement) {
                $db->execute($statement);
            }
            $db->execute('PRAGMA user_version = ' . $target);
            $pdo->commit();
        } catch (Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }
}

// Current time in UTC, the format every *_at column uses.
function tessDonationsNow(): string
{
    return gmdate('Y-m-d H:i:s');
}
