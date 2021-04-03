<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\forms\formapi\ModalForm;
use core\main\text\TextFormat;
use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class MakeOwnerCommand extends BaseSubCommand{

    public const USAGE = "/is makeowner <name>";

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
        if ($session->getIsland() === null){
            $session->sendNotification("You're not in an island!");
            return;
        }
        if (!isset($args["name"])){
            $session->sendCommandParameters(self::USAGE);
            return;
        }
        $name = $args["name"];
        $island = $session->getIslandObject();
        if (!$island->getMembersModule()->isMemberUsername($args["name"])){
            $session->sendNotification(TextFormat::GOLD . $name . " is not a member of your island!");
            return;
        }
        if (Server::getInstance()->getPlayerExact($name) === null){
            $session->sendNotification(TextFormat::GOLD . $name . TextFormat::GRAY . " is not online!");
            return;
        }
        if ($name === $sender->getName()){
            $session->sendNotification("You can not transfer ownership to yourself!");
            return;
        }
        if ($island->getOwnerName() === $session->getObject()->getUsername()){
            $form = new ModalForm(function (Player $player, ?bool $value)use($name){
                $session = SessionManager::getSession($player->getXuid());
                if ($value === null || $value === false){
                    $session->sendGoodNotification("Successfully " . TextFormat::RED . "aborted " . TextFormat::GREEN . "transferring ownership!");
                    return;
                }
                if ($session->getIslandObject() === null){
                    $session->sendNotification("You are not in an island!");
                    return;
                }
                if (Server::getInstance()->getPlayerExact($name) === null){
                    $session->sendNotification(TextFormat::GOLD . $name . TextFormat::GRAY . " is not online!");
                    return;
                }
                $session->getIslandObject()->transferOwnership(Server::getInstance()->getPlayerExact($name));
            });
            $form->setTitle(Glyphs::BOX_EXCLAMATION . TextFormat::BOLD_RED . "Transfer Confirmation" . Glyphs::BOX_EXCLAMATION);
            $form->setContent(Glyphs::RIGHT_ARROW . TextFormat::GRAY . "Are you sure you want to " . TextFormat::RED . "transfer" . TextFormat::RESET_GRAY . " your island to " . TextFormat::GOLD . $name . "?");
            $form->setButton1(Glyphs::CHECK_MARK . "Yes");
            $form->setButton2(Glyphs::X_MARK . "No");
            $sender->sendForm($form);
            return;
        }else{
            $session->sendNotification("Only island " . TextFormat::GOLD . "owners " . TextFormat::GRAY . "can do this command!");
            return;
        }

    }
}