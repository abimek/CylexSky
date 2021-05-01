<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\forms\formapi\ModalForm;
use core\main\text\TextFormat;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\island\IslandManager;
use cylexsky\session\SessionManager;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class RestartCommand extends BaseSubCommand{

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player){
            return;
        }
        $session = SessionManager::getSession($sender->getXuid());
        if ($session->getIsland() === null){
            $session->sendNotification("No island to restart!");
            return;
        }
        if ($session->getIslandObject()->getOwner() !== $sender->getXuid()){
            $session->sendNotification("Only island " . TextFormat::GOLD . "owners " . TextFormat::GRAY . "have permission to restart islands!");
            return;
        }
        $form = new ModalForm(function (Player $player, ?bool $value){
            $session = SessionManager::getSession($player->getXuid());
            if ($value === null || $value === false){
                $session->sendGoodNotification("Successfully " . TextFormat::RED . "aborted " . TextFormat::GREEN . "restarting your island!");
                return;
            }
            if ($session->getIslandObject() === null){
                $session->sendNotification("You are not in an island!");
                return;
            }
            IslandUIHandler::sendIslandResetUIForrm($session);
        });
        $form->setTitle(Glyphs::BOX_EXCLAMATION . TextFormat::BOLD_RED . "Reset Confirmation" . Glyphs::BOX_EXCLAMATION);
        $form->setContent(Glyphs::RIGHT_ARROW . TextFormat::GRAY . "Are you sure you want to " . TextFormat::BOLD_RED . "reset " . TextFormat::RESET_GRAY . "your island?");
        $form->setButton1(Glyphs::CHECK_MARK . "Yes");
        $form->setButton2(Glyphs::X_MARK . "No");
        $sender->sendForm($form);
    }
}