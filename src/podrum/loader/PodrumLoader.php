<?php

namespace podrum\loader;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use podrum\loader\factory\ModuleFactory;
use podrum\moderation\ModerationModule;
use podrum\protection\ProtectionModule;

class PodrumLoader extends PluginBase
{
    use SingletonTrait {
        setInstance as private;
        reset as private;
    }

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        ModuleFactory::register(new ModerationModule($this));
        ModuleFactory::register(new ProtectionModule($this));
    }

    protected function onDisable(): void
    {

    }
}