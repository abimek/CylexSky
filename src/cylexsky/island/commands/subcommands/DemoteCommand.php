<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\main\text\TextFormat;
use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class DemoteCommand extends BaseSubCommand{

    public const USAGE = "/is demote <name>";

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
            $session->sendCommandParameters(self::USAGE);
            return;
        }
        $name = $args["name"];
        if ($session->getIsland() === null){
            $session->sendNotification("You're not in an island!");
            return;
        }
        $island = $session->getIslandObject();
        if ($island->getOwnerName() === $session->getObject()->getUsername()){
            if($island->getOwnerName() === $name){
                $session->sendNotification("You can't demote the owner of the island!");
                return;
            }
            if (!$island->getMembersModule()->isMemberUsername($name)){
                $session->sendCommandNotification("The player " . TextFormat::GOLD . $name . PlayerSession::SEND_COMMAND_NOTIFICATION_COLOR . " is not a member of your island!");
                return;
            }
            if($island->getMembersModule()->isTrueMemberUsername($name)){
                $session->sendCommandNotification("The player " . TextFormat::GOLD . $name . PlayerSession::SEND_COMMAND_NOTIFICATION_COLOR . " is already a member!");
                return;
            }
            if ($island->getMembersModule()->isCoOwnerUsername($name)){
                $session->sendGoodNotification("Successfully demoted " . TextFormat::GOLD . $name . PlayerSession::GOOD_NOTIFICATION_COLOR . " to officer!");
            }
            if ($island->getMembersModule()->isOfficerUsername($name)){
                $session->sendGoodNotification("Successfully demoted " . TextFormat::GOLD . $name . PlayerSession::GOOD_NOTIFICATION_COLOR . " to member!");
            }
            $island->getMembersModule()->demoteName($name);
            return;
        }else{
            $session->sendNotification("Only island " . TextFormat::GOLD . "owners " . TextFormat::RED . "can do this command!");
            return;
        }

    }
}