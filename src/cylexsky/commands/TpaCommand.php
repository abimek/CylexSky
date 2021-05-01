<?php
declare(strict_types=1);

namespace cylexsky\commands;

use core\main\text\TextFormat;
use cylexsky\session\SessionManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class TpaCommand extends Command{

    public const NAME = "tpa";
    public const DESCRIPTION = "teleport to a player";
    public const USAGE = TextFormat::RED . "/tpa <name>";

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, null, []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player){
            return;
        }
        $session = SessionManager::getSession($sender->getXuid());
        if (!isset($args[0])){
            $session->sendNotification(self::USAGE);
            return;
        }
        $player = Server::getInstance()->getPlayerByPrefix($args[0]);
        if ($player === null){
            $session->sendNotification(TextFormat::RED . $args[0] . TextFormat::GRAY . " is not online!");
            return;
        }
        $s = SessionManager::getSession($player->getXuid());
        if ($s === null){
            $session->sendNotification(TextFormat::RED . $args[0] . TextFormat::GRAY . " is not online!");
            return;
        }
        if (!$s->getTogglesModule()->tpaRequests()){
            $session->sendNotification(TextFormat::RED . $args[0] . TextFormat::GRAY . " has tpa requests toggled off!");
            return;
        }
        $s->getRequestModule()->tpaRequest($session);
        $session->sendGoodNotification("Teleport request sent to " . TextFormat::GOLD . $args[0] . TextFormat::GREEN . "!");
    }

}