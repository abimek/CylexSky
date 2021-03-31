<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\main\text\TextFormat;
use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\session\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AcceptCommand extends BaseSubCommand{

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
            $session->sendCommandParameters("/is accept (player)");
            return;
        }
        if ($session->getIsland() !== null){
            $session->sendNotification("You're already in an island!");
            return;
        }
        $name = $args["name"];
        if (!$session->getRequestModule()->isIslandRequested($name)){
            $session->sendNotification("You have not received an island invite from " . TextFormat::GOLD . $name . TextFormat::RED . "!");
            return;
        }
        $island = $session->getRequestModule()->getIsland($name);
        $session->getRequestModule()->removeIslandRequest($name);
        $island->getMembersModule()->addMember($session, $sender->getName(), $sender->getXuid());
    }
}