<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\main\text\TextFormat;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\island\modules\SettingsModule;
use cylexsky\session\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class LockCommand extends BaseSubCommand{

    public const USAGE = "/is lock";

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
        $island = $session->getIslandObject();
        if ($island->getOwnerName() === $session->getObject()->getUsername()){
            $island->getSettingsModule()->setSetting(SettingsModule::VISITING, false);
            $session->sendGoodNotification("Successfully " . TextFormat::RED . "locked " . TextFormat::GREEN ."island visiting!");
            return;
        }else{
            $session->sendNotification("Only island " . TextFormat::GOLD . "owners " . TextFormat::GRAY . "can do this command!");
            return;
        }
    }
}