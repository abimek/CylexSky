<?php
declare(strict_types=1);
namespace cylexsky;

use core\database\DatabaseManager;
use cylexsky\main\ManagerLoader;
use muqsit\simplepackethandler\SimplePacketHandler;
use pocketmine\command\defaults\TeleportCommand;
use pocketmine\command\defaults\TimingsCommand;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class CylexSky extends PluginBase {
    public const SHULKER_ID = "cylex:shulkers";

    private static $instance;
    private $reset = false;

    protected function onLoad(): void
    {
    }

    protected function onEnable(): void
    {
        static $send = false;
        SimplePacketHandler::createInterceptor($this)
            ->interceptIncoming(static function(ContainerClosePacket $packet, NetworkSession $session) use(&$send) : bool{
                $send = true;
                $session->sendDataPacket($packet);
                $send = false;
                return true;
            })
            ->interceptOutgoing(static function(ContainerClosePacket $packet, NetworkSession $session) use(&$send) : bool{
                return $send;
            });
        self::$instance = $this;
        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        if ($config->get("RESET") === true){
            $config->set("RESET", false);
            $config->save();
            $this->reset = true;
        }
        ManagerLoader::init();
        Server::getInstance()->getCommandMap()->register("tp", new TeleportCommand("tp"));
        Server::getInstance()->getCommandMap()->register("timings", new TimingsCommand("timings"));
    }

    protected function onDisable(): void
    {
        ManagerLoader::close();
        sleep(2);
        DatabaseManager::realClose();
    }

    public function shouldReset(): bool {
        return $this->reset;
    }

    public static function getInstance(): CylexSky{
        return self::$instance;
    }
}