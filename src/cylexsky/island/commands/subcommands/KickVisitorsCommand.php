<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\main\text\TextFormat;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\session\SessionManager;
use cylexsky\worlds\worlds\MainWorld;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class KickVisitorsCommand extends BaseSubCommand{

    public const USAGE = "/is kickvisitors";

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
        if ($island->getOwnerName() === $session->getObject()->getUsername() || $island->getMembersModule()->isCoOwnerUsername($sender->getName())){
            if ($island->getWorld() !== null){
                foreach ($island->getWorld()->getPlayers() as $player){
                    $xuid = $player->getXuid();
                    if ($xuid !== $island->getOwner() && !$island->getMembersModule()->isMemberXUID($xuid)){
                        $session = SessionManager::getSession($player->getXuid());
                        $session->sendNotification("The owner or a coowner of the island which you were at kicked everyone off!");
                        MainWorld::teleport($player);
                    }
                }
            }
            return;
        }else{
            $session->sendNotification("Only island " . TextFormat::GOLD . "owners and coowners " . TextFormat::GRAY . "can do this command!");
            return;
        }

    }
}