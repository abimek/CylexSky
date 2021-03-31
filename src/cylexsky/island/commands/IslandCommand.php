<?php
declare(strict_types=1);

namespace cylexsky\island\commands;

use CortexPE\Commando\args\BaseArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use cylexsky\island\commands\subcommands\AcceptCommand;
use cylexsky\island\commands\subcommands\CreateCommand;
use cylexsky\island\commands\subcommands\DeleteCommand;
use cylexsky\island\commands\subcommands\DemoteCommand;
use cylexsky\island\commands\subcommands\GoCommand;
use cylexsky\island\commands\subcommands\InviteCommand;
use cylexsky\island\commands\subcommands\KickCommand;
use cylexsky\island\commands\subcommands\LeaveCommand;
use cylexsky\island\commands\subcommands\PromoteCommand;
use cylexsky\island\commands\subcommands\VisitCommand;
use cylexsky\session\SessionManager;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class IslandCommand extends BaseCommand{

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
        $this->setErrorFormat(self::ERR_INVALID_ARG_VALUE, Glyphs::BOX_EXCLAMATION . TextFormat::RED . "Invalid value '{value}' for argument #{position}");
        $this->setErrorFormat(self::ERR_TOO_MANY_ARGUMENTS, Glyphs::BOX_EXCLAMATION . TextFormat::RED . "Too many arguments given");
        $this->setErrorFormat(self::ERR_INSUFFICIENT_ARGUMENTS, Glyphs::BOX_EXCLAMATION . TextFormat::RED . "Insufficient number of arguments given");
        $this->setErrorFormat(self::ERR_NO_ARGUMENTS, Glyphs::BOX_EXCLAMATION . TextFormat::RED . "No arguments given!");
        $this->setAliases(["island", "islands"]);
        $this->registerSubCommand(new CreateCommand("create"));
        $this->registerSubCommand(new InviteCommand("invite"));
        $this->registerSubCommand(new AcceptCommand("accept"));
        $this->registerSubCommand(new DemoteCommand("demote"));
        $this->registerSubCommand(new PromoteCommand("promote"));
        $this->registerSubCommand(new GoCommand("go"));
        $this->registerSubCommand(new KickCommand("kick"));
        $this->registerSubCommand(new DeleteCommand("delete"));
        $this->registerSubCommand(new LeaveCommand("leave"));
        $this->registerSubCommand(new VisitCommand("visit"));
        $this->registerArgument(0, new RawStringArgument("create", true));
        $this->registerArgument(0, new RawStringArgument("invite", true));
        $this->registerArgument(0, new RawStringArgument("accept", true));
     //   $this->registerArgument(0, new RawStringArgument("help"));
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param BaseArgument[] $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player){
            return;
        }
        $session = SessionManager::getSession($sender->getXuid());
        if (count($args) === 0 && $session->getIsland() === null){
            IslandUIHandler::sendWithoutIsland($session);
            return;
        }else{
            IslandUIHandler::sendIslandUI($session);
        }
    }
}