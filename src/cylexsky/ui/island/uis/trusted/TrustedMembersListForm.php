<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\trusted;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class TrustedMembersListForm extends SimpleForm{

    private $cancel = false;

    private $trusted;
    private $trustedXuids;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        if ($session->getIslandObject() === null){
            return;
        }
        if (!$session->getIslandObject()->getOwner() === $session->getXuid() && !$session->getIslandObject()->getMembersModule()->isCoOwner($session->getXuid())){
            $this->cancel = true;
            $this->setTitle(Glyphs::RIGHT_ARROW . TextFormat::BOLD_AQUA . "Trusted Islands" . Glyphs::LEFT_ARROW);
            $this->setContent(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Only island " . TextFormat::RED . "owners and coowners " . TextFormat::GRAY . "can manage trusteds!");
            return;
        }
        $this->setTitle(Glyphs::RIGHT_ARROW . TextFormat::BOLD_AQUA . "Trusted Members" . Glyphs::LEFT_ARROW);
        if ($session->getIslandObject()->getTrustedModule()->getTrustedCount() === 0){
            $this->cancel = true;
            $this->setContent(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "No trusteds to manage.");
            return;
        }
        foreach ($session->getIslandObject()->getTrustedModule()->getTrustedPeople() as $xuid => $name){
            $this->setContent(Glyphs::GREEN_BOX_EXCLAMATION . "Manage the trusted people on your island.");
            $this->trusted[$xuid] = $name;
            $this->trustedXuids[] = $xuid;
            $this->addButton(TextFormat::RED . $name);
        }
        $this->addButton(TextFormat::RED . "Back");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data = null){
            if ($this->cancel || $data === null){
                return;
            }
            $session = SessionManager::getSession($player->getXuid());
            $island = $session->getIslandObject();
            if (!$session->getIslandObject()->getOwner() === $session->getXuid() && !$session->getIslandObject()->getMembersModule()->isCoOwner($session->getXuid())){
                $session->getPlayer()->sendMessage(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Only island " . TextFormat::RED . "owners and coowners " . TextFormat::GRAY . "can manage trusteds!");
                return;
            }
                if ($island === null){
                $session->sendNotification("Your island Seems to not exist! Weird :/");
                return;
            }
            if ($data === count($this->trusted)){
                IslandUIHandler::sendIslandUI($session);
                return;
            }
            $xuid = $this->trustedXuids[$data];
            $name = $this->trusted[$xuid];
            if (!$island->getTrustedModule()->isTrusted($xuid)){
                $session->sendNotification("Seems like " . TextFormat::GOLD . $name . TextFormat::GRAY . " is not trusted!");
                return;
            }
            IslandUIHandler::sendManageTrustedForm($session, $xuid);
        };
    }
}