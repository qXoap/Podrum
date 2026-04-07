<?php

declare(strict_types=1);

namespace podrum\library\libSQL\thread;

use mysqli;
use RuntimeException;

final class MySQLThread extends SQLThread
{
    protected static ?mysqli $connection = null;

    public function __construct(
        protected string $host,
        protected string $username,
        protected string $password,
        protected string $database,

        protected int $port,
    ) {
        parent::__construct();
    }

    protected function reconnect(): void
    {
        self::$connection = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

        if (self::$connection->connect_error) {
            throw new RuntimeException(self::$connection->connect_error, self::$connection->connect_errno);
        }
    }

    protected function onRun(): void
    {
        if (self::$connection === null || !self::$connection->ping()) {
            $this->reconnect();
        }

        parent::onRun();
    }

    public function getConnection(): mysqli
    {
        if (self::$connection === null || !self::$connection->ping()) {
            $this->reconnect();
        }

        return self::$connection;
    }
}
