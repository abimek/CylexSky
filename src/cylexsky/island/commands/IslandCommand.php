<?php
declare(strict_types=1);

namespace cylexsky\island\commands;

use CortexPE\Commando\args\BaseArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use cylexsky\island\commands\subcommands\AcceptCommand;
use cylexsky\island\commands\subcommands\AddPrestigePoints;
use cylexsky\island\commands\subcommands\AddXpCommand;
use cylexsky\island\commands\subcommands\CreateCommand;
use cylexsky\island\commands\subcommands\DeleteCommand;
use cylexsky\island\commands\subcommands\DemoteCommand;
use cylexsky\island\commands\subcommands\ForcePrestige;
use cylexsky\island\commands\subcommands\GivePrestigeShards;
use cylexsky\island\commands\subcommands\GoCommand;
use cylexsky\island\commands\subcommands\InviteCommand;
use cylexsky\island\commands\subcommands\KickCommand;
use cylexsky\island\commands\subcommands\KickVisitorsCommand;
use cylexsky\island\commands\subcommands\LeaveCommand;
use cylexsky\island\commands\subcommands\LockCommand;
use cylexsky\island\commands\subcommands\MakeOwnerCommand;
use cylexsky\island\commands\subcommands\MembersCommand;
use cylexsky\island\commands\subcommands\PermissionsCommand;
use cylexsky\island\commands\subcommands\PromoteCommand;
use cylexsky\island\commands\subcommands\RestartCommand;
use cylexsky\island\commands\subcommands\SettingsCommand;
use cylexsky\island\commands\subcommands\TopCommand;
use cylexsky\island\commands\subcommands\TrustedAcceptCommand;
use cylexsky\island\commands\subcommands\TrustedCommand;
use cylexsky\island\commands\subcommands\TrustedInviteCommand;
use cylexsky\island\commands\subcommands\TrustedKickCommand;
use cylexsky\island\commands\subcommands\UnLockCommand;
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
        $this->registerSubCommand(new LockCommand("lock"));
        $this->registerSubCommand(new UnLockCommand("unlock"));
        $this->registerSubCommand(new PermissionsCommand("permissions"));
        $this->registerSubCommand(new SettingsCommand("settings"));
        $this->registerSubCommand(new MembersCommand("members"));
        $this->registerSubCommand(new MakeOwnerCommand("makeowner"));
        $this->registerSubCommand(new TrustedKickCommand("tkick"));
        $this->registerSubCommand(new TrustedAcceptCommand("taccept"));
        $this->registerSubCommand(new TrustedInviteCommand("tinvite"));
        $this->registerSubCommand(new TrustedCommand("trusted"));
        $this->registerSubCommand(new KickVisitorsCommand("kickvisitors"));
        $this->registerSubCommand(new AddPrestigePoints("addprestigepoints"));
        $this->registerSubCommand(new AddXpCommand("addxp"));
        $this->registerSubCommand(new ForcePrestige("forceprestige"));
        $this->registerSubCommand(new GivePrestigeShards("addprestigeshards"));
        $this->registerSubCommand(new RestartCommand("reset"));
        $this->registerSubCommand(new TopCommand("top"));
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