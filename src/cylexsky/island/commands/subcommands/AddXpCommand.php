<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\main\text\TextFormat;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\island\IslandManager;
use cylexsky\session\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AddXpCommand extends BaseSubCommand{

    public const USAGE = "/is addxp <island_owner> <amount>";

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name", true));
        $this->registerArgument(1, new IntegerArgument("amount", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player){
            return;
        }
        $session = SessionManager::getSession($sender->getXuid());
        if(!$session->isServerOperator()){
            return;
        }
        if (!isset($args["name"]) || !isset($args["amount"])){
            $session->sendAdminMessage(self::USAGE);
            return;
        }
        $name = $args["name"];
        $amount = $args["amount"];
        $island = IslandManager::getIslandByOwner($name);
        if ($island === null){
            $session->sendAdminMessage(TextFormat::RED . $name  . TextFormat::GRAY . " does not own/have an island!");
            return;
        }
        $session->sendAdminMessage("Successfully added " . TextFormat::RED . $amount . TextFormat::GRAY . "xp to " . TextFormat::RED . $name . TextFormat::GRAY ."'s island!");
        $island->getLevelModule()->addXp($amount);
    }
}