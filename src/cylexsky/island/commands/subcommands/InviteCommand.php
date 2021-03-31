<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use cylexsky\session\SessionManager;
use cylexsky\ui\island\IslandUIHandler;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class InviteCommand extends BaseSubCommand{

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player){
            return;
        }
        $session = SessionManager::getSession($sender->getXuid());
        if ($session->getIsland() === null){
            $session->sendNotification("You're not in an island!");
            return;
        }
        IslandUIHandler::sendInviteUI($session);
    }
}