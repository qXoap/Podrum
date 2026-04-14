<?php

declare(strict_types=1);

namespace podrum\loader\object;

use pocketmine\player\Player;

class Session
{
    private Player $player;
    private array $context = [];
    private bool $loaded = false;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->lazyLoad();
    }

    private function lazyLoad(): void
    {
        // TODO: wait 4 ModerationModule tables
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function setLoaded(bool $loaded): void
    {
        $this->loaded = $loaded;
    }

    public function get(string $key): mixed
    {
        return $this->context[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->context[$key] = $value;
    }
}
