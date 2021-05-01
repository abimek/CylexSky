<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\forms\formapi\ModalForm;
use core\main\text\TextFormat;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class LeaveCommand extends BaseSubCommand{

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
            $session->sendNotification("No island to leave!");
            return;
        }
        if ($session->getIslandObject()->getOwner() === $sender->getXuid()){
            $session->sendNotification("You must transfer ownership or delete the island!");
            return;
        }
        $form = new ModalForm(function (Player $player, ?bool $value){
            $session = SessionManager::getSession($player->getXuid());
            if ($value === null || $value === false){
                $session->sendGoodNotification("Successfully " . TextFormat::RED . "aborted " . TextFormat::GREEN . "leaving the island!");
                return;
            }
            if ($session->getIslandObject() !== null){
                $session->getIslandObject()->getMembersModule()->kick($player->getName(), true);
            }
        });
        $form->setTitle(Glyphs::BOX_EXCLAMATION . TextFormat::BOLD_RED . "Leave Confirmation" . Glyphs::BOX_EXCLAMATION);
        $form->setContent(Glyphs::RIGHT_ARROW . TextFormat::RED . "Are you sure you want to " . TextFormat::BOLD_RED . "leave" . TextFormat::RESET_GOLD . " " . $session->getIslandObject()->getOwnerName() . "'s " . TextFormat::RED . "island?");
        $form->setButton1(Glyphs::CHECK_MARK . "Yes");
        $form->setButton2(Glyphs::X_MARK . "No");
        $sender->sendForm($form);
    }
}