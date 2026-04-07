<?php

namespace podrum\protection;

use podrum\loader\object\Module;

class ProtectionModule extends Module
{
    protected string $name = 'protection';

    public function load(): void
    {
        /**
         * Load Handlers, Commands and all respective features of this module
         */
    }

    public function save(): void
    {
        // TODO: Implement save() method.
    }

    // Add functions you need in main module class
}