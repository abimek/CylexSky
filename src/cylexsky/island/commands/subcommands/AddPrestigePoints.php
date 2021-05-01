<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\main\text\TextFormat;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\island\IslandManager;
use cylexsky\island\modules\SettingsModule;
use cylexsky\session\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AddPrestigePoints extends BaseSubCommand{

    public const USAGE = "/is addprestigepoints <island_owner> <amount>";

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name", false));
        $this->registerArgument(1, new IntegerArgument("amount", false));
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
        $session->sendAdminMessage("Successfully added " . TextFormat::RED . $amount . TextFormat::GRAY . "Prestige points to " . TextFormat::RED . $name . TextFormat::GRAY ."'s island!");
        $island->addPrestigePoints($amount);
    }
}