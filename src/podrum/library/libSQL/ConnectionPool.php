<?php

declare(strict_types=1);

namespace podrum\library\libSQL;

use Closure;
use podrum\library\libSQL\exception\SQLException;
use podrum\library\libSQL\query\SQLQuery;
use podrum\library\libSQL\thread\MySQLThread;
use podrum\library\libSQL\thread\SQLiteThread;
use podrum\library\libSQL\thread\SQLThread;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use function bin2hex;
use function count;
use function implode;
use function json_decode;
use function microtime;
use function spl_object_hash;
use function usort;

final class ConnectionPool
{
    use SingletonTrait {
        reset as protected;
        setInstance as protected;
    }

    /**
     * @var array<string, array{Closure, Closure}>
     */
    protected array $completionHandlers = [];

    /**
     * @var array<int, SQLThread>
     */
    protected array $threads = [];

    public function __construct(protected PluginBase $plugin, array $configuration)
    {
        self::setInstance($this);

        $isMySQL = $configuration["provider"] === "mysql";

        for ($i = 0; $i < $configuration["threads"]; $i++) {
            $thread = $isMySQL ?
                new MySQLThread(... $configuration["mysql"]) :
                new SQLiteThread($plugin->getDataFolder() . $configuration["sqlite"]["path"])
            ;

            $sleeperHandlerEntry = $this->plugin->getServer()->getTickSleeper()->addNotifier(
                function () use ($thread): void {
                    /**
                     * @var SQLQuery|null $query
                     */
                    $query = $thread->getCompleteQueries()->shift();

                    if ($query === null) {
                        return;
                    }

                    $error = $query->getError() !== null ? json_decode($query->getError(), true) : null;
                    $exception = $error !== null ? SQLException::fromArray($error) : null;

                    [$successHandler, $errorHandler] = $this->completionHandlers[$query->getIdentifier()] ?? [null, null];

                    match (true) {
                        $exception === null && $successHandler !== null => $successHandler($query->getResult()),

                        $exception !== null && $errorHandler !== null => $errorHandler($exception),
                        $exception !== null => $this->plugin->getLogger()->logException($exception),

                        default => null,
                    };

                    if (isset($this->completionHandlers[$query->getIdentifier()])) {
                        unset($this->completionHandlers[$query->getIdentifier()]);
                    }
                }
            );

            $thread->setSleeperHandlerEntry($sleeperHandlerEntry);
            $thread->start();
            $this->threads[] = $thread;
        }
    }

    public function submit(SQLQuery $query, ?Closure $onSuccess = null, ?Closure $onFail = null): void
    {
        $identifier = [
            spl_object_hash($query),
            microtime(),
            count($this->threads),
            count($this->completionHandlers),
        ];

        $query->setIdentifier(bin2hex(implode("", $identifier)));
        $this->completionHandlers[$query->getIdentifier()] = [$onSuccess, $onFail];
        $this->getLeastBusyThread()->addQuery($query);
    }

    protected function getLeastBusyThread(): SQLThread
    {
        $threads = $this->threads;
        usort($threads, static fn (SQLThread $a, SQLThread $b) => $a->getQueries()->count() <=> $b->getQueries()->count());
        return $threads[0];
    }
}
