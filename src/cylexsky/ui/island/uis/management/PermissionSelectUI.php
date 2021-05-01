<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\management;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class PermissionSelectUI extends SimpleForm{

    private $session;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->session = $session;
        $this->setTitle(Glyphs::RIGHT_ARROW . TextFormat::BOLD_RED . "Island Permission" . Glyphs::LEFT_ARROW);
        $this->setContent(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Select which permissions you would like to manage!");
        $this->addButton(TextFormat::BOLD_RED . "Officers");
        $this->addButton(TextFormat::BOLD_RED . "Members");
        $this->addButton(TextFormat::BOLD_RED . "Visitors");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data){
            $session = SessionManager::getSession($player->getXuid());
            if ($this->session->getIslandObject() === null){
                $this->session->sendNotification("You are not in an island");
            }
            if ($this->session->getIslandObject()->getOwner() !== $player->getXuid()){
                $this->session->sendNotification("You are not the owner of the island!");
                return;
            }
            if ($data === null){
                $session->sendNotification("No option selected!");
                return;
            }
            switch ($data){
                case 0:
                    IslandUIHandler::sendPermissionsUI(0, $this->session);
                    return;
                case 1:
                    IslandUIHandler::sendPermissionsUI(1, $this->session);
                    return;
                case 2:
                    IslandUIHandler::sendPermissionsUI(2, $this->session);
                    return;
            }
        };
    }
}