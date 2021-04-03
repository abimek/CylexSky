<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\main\text\TextFormat;
use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\session\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TrustedAcceptCommand extends BaseSubCommand{

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
            $session->sendCommandParameters("/is trustedaccept (player)");
            return;
        }
        if ($session->getTrustedModule()->isTrustedLimitReached()){
            $session->sendNotification("You're trusted limit has been reached!");
            return;
        }
        $name = $args["name"];
        if (!$session->getRequestModule()->isIslandTrustedRequested($name)){
            $session->sendNotification("You have not received an island trusted invite from " . TextFormat::GOLD . $name . TextFormat::GRAY . "!");
            return;
        }
        $island = $session->getRequestModule()->getTrustedInvite($name);
        if ($island->getTrustedModule()->isTrusted($sender->getXuid())){
            $session->sendNotification("You are already trusted on that island!");
            return;
        }
        $session->getRequestModule()->removeIslandTrustedRequest($name);
        $island->getTrustedModule()->addTrusted($session, $sender->getName(), $sender->getXuid());
    }
}