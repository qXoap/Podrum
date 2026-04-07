<?php

declare(strict_types=1);

namespace podrum\loader\task;

use pocketmine\scheduler\Task;
use podrum\loader\PodrumLoader;

class DatabaseTask extends Task
{
    private PodrumLoader $loader;

    public function __construct(PodrumLoader $loader)
    {
        $this->loader = $loader;
    }

    public function onRun(): void
    {
        // TODO: database peridocialy check
    }
}
