<?php

declare(strict_types=1);

namespace podrum\loader\factory;

use podrum\library\libSQL\ConnectionPool;
use podrum\loader\PodrumLoader;
use InvalidArgumentException;

class Backend
{
    private PodrumLoader $loader;
    private ConnectionPool $connector;

    public function __construct(PodrumLoader $loader)
    {
        $this->loader = $loader;

        $dbConfig = $this->loader->getConfig()->get('database');

        if (!isset($dbConfig['provider'])) {
            throw new InvalidArgumentException('Database provider is not defined in config.yml');
        }

        $provider = $dbConfig['provider'];
        $settings = $dbConfig[$provider] ?? null;

        if ($settings === null) {
            throw new InvalidArgumentException("Settings for provider '{$provider}' are missing");
        }

        $this->connector = new ConnectionPool($this->loader, [
            'provider' => $provider,
            'threads' => $settings['threads'] ?? 1,
            $provider => $settings
        ]);

        $this->prepareSchema();
    }

    private function prepareSchema(): void
    {
        // TODO: impelemnt schemas
    }

    public function getConnector(): ConnectionPool
    {
        return $this->connector;
    }

    public function shutdown(): void
    {
        $this->connector->close();
    }
}
