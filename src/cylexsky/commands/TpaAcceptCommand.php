<?php
declare(strict_types=1);

namespace cylexsky\commands;

use core\main\text\TextFormat;
use cylexsky\session\SessionManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class TpaAcceptCommand extends Command{

    public const NAME = "tpaaccept";
    public const DESCRIPTION = "Accept a tpa request";
    public const USAGE = TextFormat::RED . "/tpaaccept <name>";

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, null, []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player){
            return;
        }
        $s = SessionManager::getSession($sender->getXuid());
        if (!isset($args[0])){
            $s->sendNotification(self::USAGE);
            return;
        }
        $p = Server::getInstance()->getPlayerByPrefix($args[0]);
        if ($p === null){
            $s->getRequestModule()->removeTpaRequest($args[0]);
            $s->sendNotification(TextFormat::RED . $args[0] . TextFormat::GRAY . " is not online");
            return;
        }
        $name = $p->getName();
        if (!$s->getRequestModule()->isTpaRequest($name)){
            $s->sendNotification("To tpa request from $name");
            return;
        }
        $s->getRequestModule()->removeTpaRequest($name);
        if (!$s->getTeleportModule()->canTeleport()){
            $s->sendNotification("Unable to teleport!");
            return;
        }
        $session = SessionManager::getSession($p->getXuid());
        $s->getTeleportModule()->teleport($session->getPlayer()->getPosition());
        $s->sendNotification("Teleporting to $name...");
    }

}