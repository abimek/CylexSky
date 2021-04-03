<?php
declare(strict_types=1);

namespace cylexsky\island\commands\subcommands;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use CortexPE\Commando\BaseSubCommand;
use cylexsky\session\SessionManager;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MembersCommand extends BaseSubCommand{

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player){
            return;
        }
        self::sendMembers($sender);
    }

    public static function sendMembers(Player $player){
        $session = SessionManager::getSession($player->getXuid());
        if ($session->getIslandObject() === null){
            $session->sendNotification("You are not in an island!");
            return;
        }
         $is = $session->getIslandObject();
        $memebrs = implode(", ", $is->getMembersModule()->getOnlyMemebers());
        $officers = implode(", ", $is->getMembersModule()->getOnlyOfficers());
        $trusted = implode(", ", $is->getTrustedModule()->getTrustedNames());
        $coowners = implode(", ", $is->getMembersModule()->getOnlyCoOwners());
        $form = new SimpleForm(function (Player $player, ?int $data = null){
            return;
        });
        $form->setTitle(TextFormat::BOLD_RED . "Island Members");
        $content = Glyphs::RIGHT_ARROW . TextFormat::RED . "Owner: " . TextFormat::GRAY . $is->getOwnerName() . "\n";
        $content .= Glyphs::RIGHT_ARROW . TextFormat::RED . "CoOwners:" . TextFormat::GOLD . $coowners . "\n";
        $content .= Glyphs::RIGHT_ARROW . TextFormat::RED . "Officers: " . TextFormat::GOLD . $officers . "\n";
        $content .= Glyphs::RIGHT_ARROW . TextFormat::RED . "Members: " . TextFormat::GOLD . $memebrs . "\n";
        $content .= Glyphs::RIGHT_ARROW . TextFormat::RED . "Trusted: " . TextFormat::GOLD . $trusted;
        $form->setContent($content);
        $form->addButton(Glyphs::X_MARK . TextFormat::BOLD_RED . "Close");
        $player->sendForm($form);
    }

    public static function sendMembersInUI(Player $player){
        $session = SessionManager::getSession($player->getXuid());
        if ($session->getIslandObject() === null){
            $session->sendNotification("You are not in an island!");
            return;
        }
        $is = $session->getIslandObject();
        $memebrs = implode(", ", $is->getMembersModule()->getOnlyMemebers());
        $officers = implode(", ", $is->getMembersModule()->getOnlyOfficers());
        $trusted = implode(", ", $is->getTrustedModule()->getTrustedNames());
        $coowners = implode(", ", $is->getMembersModule()->getOnlyCoOwners());
        $form = new SimpleForm(function (Player $player, ?int $data = null){
            if ($data === 0){
                IslandUIHandler::sendIslandUI(SessionManager::getSession($player->getXuid()));
            }
        });
        $form->setTitle(TextFormat::BOLD_RED . "Island Members");
        $content = Glyphs::RIGHT_ARROW . TextFormat::RED . "Owner: " . TextFormat::GRAY . $is->getOwnerName() . "\n";
        $content .= Glyphs::RIGHT_ARROW . TextFormat::RED . "CoOwners:" . TextFormat::GOLD . $coowners . "\n";
        $content .= Glyphs::RIGHT_ARROW . TextFormat::RED . "Officers: " . TextFormat::GOLD . $officers . "\n";
        $content .= Glyphs::RIGHT_ARROW . TextFormat::RED . "Members: " . TextFormat::GOLD . $memebrs . "\n";
        $content .= Glyphs::RIGHT_ARROW . TextFormat::RED . "Trusted: " . TextFormat::GOLD . $trusted;
        $form->setContent($content);
        $form->addButton(Glyphs::BOX_EXCLAMATION . TextFormat::BOLD_RED . "Back");
        $player->sendForm($form);
    }
}