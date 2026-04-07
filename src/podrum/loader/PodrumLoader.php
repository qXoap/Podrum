<?php

namespace podrum\loader;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use podrum\loader\factory\ModuleFactory;
use podrum\moderation\ModerationModule;
use podrum\protection\ProtectionModule;
use podrum\loader\factory\Backend;
use podrum\loader\factory\SessionManager;
use podrum\loader\task\DatabaseTask;

class PodrumLoader extends PluginBase
{
    use SingletonTrait;

    private Backend $backend;
    private SessionManager $sessions;

    protected function onLoad(): void
    {
        self::setInstance($this);
        $this->saveDefaultConfig();
    }

    protected function onEnable(): void
    {
        $this->backend = new Backend($this);
        $this->sessions = new SessionManager($this);

        $this->getServer()->getPluginManager()->registerEvents($this->sessions, $this);

        $this->getScheduler()->scheduleRepeatingTask(new DatabaseTask($this), 20 * 60 * 5);

        ModuleFactory::register(new ModerationModule($this));
        ModuleFactory::register(new ProtectionModule($this));
    }

    public function getBackend(): Backend
    {
        return $this->backend;
    }

    public function getSessions(): SessionManager
    {
        return $this->sessions;
    }

    protected function onDisable(): void
    {
        if (isset($this->backend)) {
            $this->backend->shutdown();
        }
    }
}