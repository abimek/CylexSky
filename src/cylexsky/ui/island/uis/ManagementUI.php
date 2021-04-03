<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class ManagementUI extends SimpleForm{

    private $session;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->session = $session;
        $this->setTitle(Glyphs::RIGHT_ARROW . TextFormat::BOLD_RED . "Island Management" . Glyphs::LEFT_ARROW);
        $this->setContent(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Select what you would like to manage!");
        $this->addButton(TextFormat::BOLD_RED . "Settings");
        $this->addButton(TextFormat::BOLD_RED . "Permissions");
        $this->addButton(TextFormat::BOLD_RED . "Trusted");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data){
            $session = SessionManager::getSession($player->getXuid());
            if ($this->session->getIslandObject() === null){
                $this->session->sendNotification(TextFormat::RED . "You are not in an island");
            }
            if ($this->session->getIslandObject()->getOwner() !== $player->getXuid() && !$this->session->getIslandObject()->getMembersModule()->isCoOwner($player->getXuid())){
                $this->session->sendNotification("You are not an " . TextFormat::GOLD . "owner or coowner" . TextFormat::GRAY ." of the island!");
                return;
            }
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    IslandUIHandler::sendSettingsUI($session);
                    return;
                case 1:
                    IslandUIHandler::sendPermissionSelectUI($session);
                    return;
                case 2:
                    IslandUIHandler::sendIslandTrustedSelectForm($session);
                    return;
            }
        };
    }
}