<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\main\text\TextFormat;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\island\IslandManager;
use cylexsky\island\modules\SettingsModule;
use cylexsky\session\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ForcePrestige extends BaseSubCommand{

    public const USAGE = "/is forceprestige <island_owner>";

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name", false));
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
        if (!isset($args["name"])){
            $session->sendAdminMessage(self::USAGE);
            return;
        }
        $name = $args["name"];
        $island = IslandManager::getIslandByOwner($name);
        if ($island === null){
            $session->sendAdminMessage(TextFormat::RED . $name  . TextFormat::GRAY . " does not own/have an island!");
            return;
        }
        $session->sendAdminMessage("Successfully " .TextFormat::GRAY . "Force Prestiged " . TextFormat::RED . $name . TextFormat::GRAY ."'s island!");
        $island->getLevelModule()->prestige();
    }
}