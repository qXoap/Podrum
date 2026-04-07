<?php

declare(strict_types=1);

namespace podrum\library\libSQL\query;

use Closure;
use podrum\library\libSQL\ConnectionPool;
use podrum\library\libSQL\exception\SQLException;
use podrum\library\libSQL\thread\SQLThread;
use pmmp\thread\Thread as NativeThread;
use pmmp\thread\ThreadSafe;
use Throwable;
use function assert;
use function igbinary_serialize;
use function igbinary_unserialize;
use function is_scalar;

abstract class SQLQuery extends ThreadSafe
{
    protected string $identifier = "";

    protected ?string $error = null;

    protected mixed $result = null;
    protected bool $resultSerialized = false;

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    final public function run(): void
    {
        try {
            $this->onRun($this->getThread()->getConnection());
        } catch (Throwable $throwable) {
            $this->error = json_encode([
                "message" => $throwable->getMessage(),
                "code" => $throwable->getCode(),
                "trace" => $throwable->getTrace(),
                "trace_string" => $throwable->getTraceAsString(),
                "file" => $throwable->getFile(),
                "line" => $throwable->getLine(),
                "class" => $throwable instanceof SQLException ? $throwable::class : null
            ]);
        }
    }

    final public function getResult(): mixed
    {
        return $this->resultSerialized ? igbinary_unserialize($this->result) : $this->result;
    }

    final protected function setResult(mixed $result): void
    {
        $this->resultSerialized = !is_scalar($result) && !$result instanceof ThreadSafe;
        $this->result = $this->resultSerialized ? igbinary_serialize($result) : $result;
    }

    final public function getError(): ?string
    {
        return $this->error;
    }

    public function getThread(): SQLThread
    {
        $worker = NativeThread::getCurrentThread();
        assert($worker instanceof SQLThread);

        return $worker;
    }

    final public function execute(?Closure $onSuccess = null, ?Closure $onFail = null): void
    {
        ConnectionPool::getInstance()->submit($this, $onSuccess, $onFail);
    }
}
