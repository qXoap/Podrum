<?php

namespace podrum\loader\object;

use pocketmine\utils\TextFormat;
use podrum\loader\PodrumLoader;

abstract class Module
{
    protected string $name;

    public function __construct(
        private readonly PodrumLoader $loader
    )
    {
        $this->load();

        $this->loader->getLogger()->info(TextFormat::colorize(
            "&8(&cPodrum&8) &e" . $this->name . "&7 Module successfully loaded!"
        ));
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function load(): void;

    abstract public function save(): void;
}