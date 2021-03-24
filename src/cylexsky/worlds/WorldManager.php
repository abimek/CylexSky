<?php
declare(strict_types=1);

namespace cylexsky\worlds;

use core\main\managers\Manager;
use cylexsky\CylexSky;
use cylexsky\worlds\listener\WorldListener;
use cylexsky\worlds\worlds\MainWorld;
use cylexsky\worlds\worlds\PvPWorld;
use pocketmine\Server;

class WorldManager extends Manager{

    private static $worlds;

    protected function init(): void
    {
        $this->registerWorlds();
        Server::getInstance()->getPluginManager()->registerEvents(new WorldListener(), CylexSky::getInstance());
    }

    private function registerWorlds(){
        self::$worlds[MainWorld::getName()] = new MainWorld();
   //     self::$worlds[PvPWorld::getName()] = new PvPWorld();
    }

    protected function close(): void
    {

    }
}