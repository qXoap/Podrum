<?php

declare(strict_types=1);

namespace podrum\loader\factory;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use podrum\loader\object\Session;
use podrum\loader\PodrumLoader;

final class SessionManager implements Listener
{
    private array $sessions = [];
    private PodrumLoader $loader;

    public function __construct(PodrumLoader $loader)
    {
        $this->loader = $loader;
    }

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $this->sessions[$player->getName()] = new Session($player);
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        $name = $event->getPlayer()->getName();
        if (isset($this->sessions[$name])) {
            unset($this->sessions[$name]);
        }
    }

    public function getSession(Player $player): ?Session
    {
        return $this->sessions[$player->getName()] ?? null;
    }

    public function getSessions(): array
    {
        return $this->sessions;
    }
}
