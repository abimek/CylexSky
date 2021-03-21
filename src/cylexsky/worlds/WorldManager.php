<?php
declare(strict_types=1);

namespace cylexsky\worlds;

use core\main\managers\Manager;
use cylexsky\worlds\worlds\MainWorld;

class WorldManager extends Manager{

    private static $worlds;

    protected function init(): void
    {
        $this->registerWorlds();
    }

    private function registerWorlds(){
        self::$worlds[MainWorld::getName()] = new MainWorld();

    }

    protected function close(): void
    {

    }
}