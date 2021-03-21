<?php
declare(strict_types=1);

namespace cylexsky\worlds\worlds;

use cylexsky\worlds\BaseWorld;
use pocketmine\entity\Location;
use pocketmine\Server;

class MainWorld extends BaseWorld{

    public function init()
    {
        self::setWorld(Server::getInstance()->getWorldManager()->getWorldByName("world"));
        self::setSpawnPoint(new Location(0, 0, 0, 0, 0, self::getWorld()));
    }

    public static function getName(): string
    {
        return "world";
    }


}