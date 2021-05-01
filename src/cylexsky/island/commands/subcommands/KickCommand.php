<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\main\text\TextFormat;
use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\island\modules\PermissionModule;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class KickCommand extends BaseSubCommand{

    public const USAGE = "/is kick <name>";

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
        if ($island->getPermissionModule()->playerHasPermission(PermissionModule::PERMISSION_KICK, $session)){
            if($island->getOwnerName() === $name){
                $session->sendNotification("You can't kick the owner of the island!");
                return;
            }
            if (!$island->getMembersModule()->isMemberUsername($name)){
                $session->sendCommandNotification("The player " . TextFormat::GOLD . $name . PlayerSession::SEND_COMMAND_NOTIFICATION_COLOR . " is not a member of your island!");
                return;
            }
            if($island->getMembersModule()->getRank($name) >= $island->getMembersModule()->getRank($sender->getName())){
                $session->sendCommandNotification("The player " . TextFormat::GOLD . $name . PlayerSession::SEND_COMMAND_NOTIFICATION_COLOR . " either outranks you or has the same island-rank!");
                return;
            }
            $island->getMembersModule()->kick($name);
            return;
        }else{
            $session->sendNotification("You do not have permission to kick someone from your island!");
            return;
        }

    }
}