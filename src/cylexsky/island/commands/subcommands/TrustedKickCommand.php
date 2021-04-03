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

class TrustedKickCommand extends BaseSubCommand{

    public const USAGE = "/is trustedkick <name>";

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
        self::kick($sender, $name);
    }

    public static function kick(Player $sender, string $name){
        $session = SessionManager::getSession($sender->getXuid());
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
            if (!$island->getTrustedModule()->isTrustedName($name)){
                $session->sendCommandNotification("The player " . TextFormat::GOLD . $name . PlayerSession::SEND_COMMAND_NOTIFICATION_COLOR . " is not a trusted!");
                return;
            }
            if ($island->getTrustedModule()->isTrusted($sender->getXuid())){
                $session->sendNotification("Trusted cant kick trusted silly!");
                return;
            }
            $island->getTrustedModule()->kick($name);
            return;
        }else{
            $session->sendNotification("You do not have permission to kick a trusted from your island!");
            return;
        }
    }
}