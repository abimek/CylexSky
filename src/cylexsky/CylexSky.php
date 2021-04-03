<?php
declare(strict_types=1);
namespace cylexsky;

use core\database\DatabaseManager;
use cylexsky\island\creation\IslandGenerator;
use cylexsky\main\ManagerLoader;
use pocketmine\command\defaults\TeleportCommand;
use pocketmine\command\defaults\TimingsCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\generator\GeneratorManager;

class CylexSky extends PluginBase {
    private static $instance;
    private $reset = false;

    protected function onLoad(): void
    {
    }

    protected function onEnable(): void
    {
        self::$instance = $this;
        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        if ($config->get("RESET") === true){
            $config->set("RESET", false);
            $config->save();
            $this->reset = true;
        }
        ManagerLoader::init();
        GeneratorManager::getInstance()->addGenerator(IslandGenerator::class, "gen");
        $this->getServer()->getWorldManager()->generateWorld("NormalIsland", null, IslandGenerator::class);
        Server::getInstance()->getCommandMap()->register("tp", new TeleportCommand("tp"));
        Server::getInstance()->getCommandMap()->register("timings", new TimingsCommand("timings"));
    }

    protected function onDisable(): void
    {
        ManagerLoader::close();
        DatabaseManager::realClose();
    }

    public function shouldReset(): bool {
        return $this->reset;
    }

    public static function getInstance(): CylexSky{
        return self::$instance;
    }
}