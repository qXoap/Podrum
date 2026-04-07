<?php

declare(strict_types=1);

namespace podrum\library\libSQL\query;

use podrum\library\libSQL\thread\SQLiteThread;
use podrum\library\libSQL\thread\SQLThread;
use SQLite3;

abstract class SQLiteQuery extends SQLQuery
{
    /**
     * @return SQLiteThread
     */
    final public function getThread(): SQLThread
    {
        return parent::getThread();
    }

    abstract public function onRun(SQLite3 $connection): void;
}
