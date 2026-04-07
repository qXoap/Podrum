<?php

declare(strict_types=1);

namespace podrum\library\libSQL\query;

use podrum\library\libSQL\thread\MySQLThread;
use podrum\library\libSQL\thread\SQLThread;
use mysqli;

abstract class MySQLQuery extends SQLQuery
{
    /**
     * @return MySQLThread
     */
    final public function getThread(): SQLThread
    {
        return parent::getThread();
    }

    abstract public function onRun(mysqli $connection): void;
}
