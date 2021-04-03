<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\trusted;

use core\forms\formapi\ModalForm;
use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\island\Island;
use cylexsky\island\IslandManager;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;
use pocketmine\Server;

class TrustedIslandForm extends SimpleForm{

    private $islandId;
    private $session;

    public function __construct(PlayerSession $session, Island $island)
    {
        parent::__construct($this->getFormResultCallable());
        $this->islandId = $island->getId();
        $this->session = $session;
        $this->setTitle(TextFormat::GOLD . $island->getOwnerName() . "s " . TextFormat::GRAY . "Island");
        $this->addButton(TextFormat::BOLD_GREEN . "Teleport");
        $this->addButton(TextFormat::BOLD_RED . "Leave");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data){
            if ($data === null){
                return;
            }
            $s = $this->session;
            $is = IslandManager::getIsland($this->islandId);
            if ($is === null){
                $s->sendNotification("Island Does Not Exist!");
                return;
            }
            if (!$s->getTrustedModule()->isTrustedIsland($is->getId())){
                $s->sendNotification("Seems like you are nolonger trusted!");
                return;
            }
            switch ($data){
                case 0:
                    if (!$s->getTeleportModule()->canTeleport()){
                        $s->sendNotification(TextFormat::RED . "Unable to teleport..");
                        return;
                    }
                    $t = $is->getTeleportModule()->getTrustedSpawn();
                    Server::getInstance()->getWorldManager()->loadWorld($t->getWorld()->getDisplayName());
                    $player->teleport($t, $t->yaw, $t->pitch);
                    return;
                case 1:
                    $form = new ModalForm(function (Player $player, ?bool $value)use($is){
                        $session = SessionManager::getSession($player->getXuid());
                        if ($value === null || $value === false){
                            $session->sendGoodNotification("Successfully " . TextFormat::RED . "aborted " . TextFormat::GREEN . "leaving the island!");
                            return;
                        }
                        $is->getTrustedModule()->kick($player->getName(), true);
                    });
                    $form->setTitle(Glyphs::BOX_EXCLAMATION . TextFormat::BOLD_RED . "Leave Trusted Confirmation" . Glyphs::BOX_EXCLAMATION);
                    $form->setContent(Glyphs::RIGHT_ARROW . TextFormat::RED . "Are you sure you want to " . TextFormat::BOLD_RED . "leave" . TextFormat::RESET_GOLD . " " . $is->getOwnerName() . "'s " . TextFormat::RED . "island?");
                    $form->setButton1(Glyphs::CHECK_MARK . "Yes");
                    $form->setButton2(Glyphs::X_MARK . "No");
                    $player->sendForm($form);
            }
        };
    }
}