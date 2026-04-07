<?php

declare(strict_types=1);

namespace podrum\library\libSQL\thread;

use podrum\library\libSQL\query\SQLQuery;
use pmmp\thread\ThreadSafeArray;
use pocketmine\snooze\SleeperHandlerEntry;
use pocketmine\thread\Thread;

abstract class SQLThread extends Thread
{
    protected SleeperHandlerEntry $sleeperHandlerEntry;

    protected ThreadSafeArray $queries;
    protected ThreadSafeArray $completeQueries;

    protected bool $running = false;

    public function __construct()
    {
        $this->queries = new ThreadSafeArray();
        $this->completeQueries = new ThreadSafeArray();
    }

    protected function onRun(): void
    {
        $this->running = true;

        $notifier = $this->sleeperHandlerEntry->createNotifier();

        while ($this->running) {
            $this->synchronized(
                function (): void {
                    if ($this->running && $this->queries->count() === 0 && $this->completeQueries->count() === 0) {
                        $this->wait();
                    }
                }
            );

            if ($this->completeQueries->count() !== 0) {
                $notifier->wakeupSleeper();
            }

            /**
             * @var SQLQuery|null $query
             */
            $query = $this->queries->shift();

            if ($query === null) {
                continue;
            }

            $query->run();

            $this->completeQueries[] = $query;
        }
    }

    public function quit(): void
    {
        $this->synchronized(
            function (): void {
                $this->running = false;
                $this->notify();
            }
        );

        parent::quit();
    }

    public function setSleeperHandlerEntry(SleeperHandlerEntry $sleeperHandlerEntry): void
    {
        $this->sleeperHandlerEntry = $sleeperHandlerEntry;
    }

    public function addQuery(SQLQuery $query): void
    {
        $this->synchronized(
            function () use ($query): void {
                $this->queries[] = $query;
                $this->notify();
            }
        );
    }

    /**
     * @return ThreadSafeArray<SQLQuery>
     */
    public function getQueries(): ThreadSafeArray
    {
        return $this->queries;
    }

    /**
     * @return ThreadSafeArray<SQLQuery>
     */
    public function getCompleteQueries(): ThreadSafeArray
    {
        return $this->completeQueries;
    }
}
