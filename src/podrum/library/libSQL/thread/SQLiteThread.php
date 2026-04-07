<?php

declare(strict_types=1);

namespace podrum\library\libSQL\thread;

use SQLite3;

final class SQLiteThread extends SQLThread
{
    protected static ?SQLite3 $connection = null;

    public function __construct(protected string $databasePath)
    {
        parent::__construct();
    }

    public function reconnect(): void
    {
        self::$connection = new SQLite3($this->databasePath);
        self::$connection->busyTimeout(60000);
    }

    protected function onRun(): void
    {
        if (self::$connection === null) {
            $this->reconnect();
        }

        parent::onRun();
    }

    public function getConnection(): SQLite3
    {
        if (self::$connection === null) {
            $this->reconnect();
        }

        return self::$connection;
    }
}
