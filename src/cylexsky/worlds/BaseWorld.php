<?php
declare(strict_types=1);

namespace cylexsky\worlds;

use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\world\World;

abstract class BaseWorld{

    private static $position;
    private static $world;

    public function __construct()
    {
        $this->init();
        self::getWorld()->setSpawnLocation(self::getSpawnPoint());
    }

    abstract public function init();

    public static function getSpawnPoint(): Location{
        return self::$position;
    }

    protected static function setSpawnPoint(Location $position){
        self::$position = $position;
    }

    protected static function setWorld(World $world){
        self::$world = $world;
    }

    public static function getWorld(): World{
        return self::$world;
    }

    public static function teleport(Player $player){
        $player->teleport(self::getSpawnPoint(), self::getSpawnPoint()->yaw, self::getSpawnPoint()->pitch);
    }


    public static function getName(): string {
        return "";
    }
}