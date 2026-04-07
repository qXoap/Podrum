<?php

namespace podrum\loader\factory;

use podrum\loader\object\Module;
use podrum\loader\PodrumLoader;

class ModuleFactory
{
    /** @var Module[] $modules */
    private static array $modules = [];

    public static function register(Module $module): void
    {
        self::$modules[$module->getName()] = $module;
    }

    public static function getModule(string $name): ?Module
    {
        return self::$modules[$name] ?? null;
    }

    public static function getModules(): array
    {
        return self::$modules;
    }

    public static function unregister(string $name): void
    {
        if (isset(self::$modules[$name])) {
            unset(self::$modules[$name]);
        }
    }
}