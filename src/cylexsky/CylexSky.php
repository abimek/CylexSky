<?php
declare(strict_types=1);
namespace cylexsky;

use cylexsky\island\creation\IslandGenerator;
use cylexsky\main\ManagerLoader;
use pocketmine\command\defaults\TeleportCommand;
use pocketmine\command\defaults\TimingsCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\world\generator\GeneratorManager;

class CylexSky extends PluginBase {
    private static $instance;
    protected function onEnable(): void
    {
        self::$instance = $this;
        ManagerLoader::init();
        GeneratorManager::getInstance()->addGenerator(IslandGenerator::class, "gen");
        $this->getServer()->getWorldManager()->generateWorld("NormalIsland", null, IslandGenerator::class);
        Server::getInstance()->getCommandMap()->register("tp", new TeleportCommand("tp"));
        Server::getInstance()->getCommandMap()->register("timings", new TimingsCommand("timings"));
    }
    public static function getInstance(): CylexSky{
        return self::$instance;
    }
}