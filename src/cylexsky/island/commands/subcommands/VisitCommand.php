<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\main\text\TextFormat;
use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\island\modules\SettingsModule;
use cylexsky\session\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class VisitCommand extends BaseSubCommand{

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player){
            return;
        }
        $session = SessionManager::getSession($sender->getXuid());
        if (!isset($args["name"])){
            $session->sendCommandParameters("/is visit (player)");
            return;
        }
        if ($session->getIsland() !== null){
            $session->sendNotification("You're already in an island!");
            return;
        }
        $name = $args["name"];
        if (Server::getInstance()->getPlayerByPrefix($name) === null){
            $session->sendNotification(TextFormat::GRAY . $name . " " . TextFormat::RED . "isn't online!");
            return;
        }
        $s = SessionManager::getSession(Server::getInstance()->getPlayerByPrefix($name)->getXuid());
        if($s->getIslandObject() === null){
            $session->sendNotification(TextFormat::GRAY . $name . TextFormat::RED . " is not in an island!");
            return;
        }
        if($s->getIslandObject()->getSettingsModule()->getSetting(SettingsModule::VISITING) === false){
            $session->sendNotification($name . "'s " . "island has visiting disabled!");
            return;
        }
        if (!$session->getTeleportModule()->canTeleport()){
            $session->sendNotification("Unable to teleport!");
            return;
        }
        $island = $session->getRequestModule()->getIsland($name);
        $island->teleportPlayer($sender);
        $session->sendGoodNotification("Teleporting to " . TextFormat::GOLD . $name . "'s " . TextFormat::GRAY . "island...");
    }
}