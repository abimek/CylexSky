<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\warps;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use core\ranks\RankManager;
use cylexsky\island\creation\IslandCreationHandler;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\ui\island\uis\IslandUI;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class WarpsUI extends SimpleForm{

    private $warps = [];
    private $cancel = false;
    private $create = false;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(Glyphs::OPEN_BOOK . TextFormat::BOLD_RED . "Island Warps" . Glyphs::OPEN_BOOK);
        if ($session->getIslandObject() === null){
            $session->sendNotification("You are not in an island!");
            $this->cancel = true;
            return;
        }
        if ($session->getIslandObject()->getTeleportModule()->getWarpCount() === 0){
            $this->setContent(Glyphs::GREEN_BOX_EXCLAMATION . TextFormat::GRAY . "No island warps, create one(At the location you're at)!");
        }
        foreach ($session->getIslandObject()->getTeleportModule()->getWarps() as $name => $location){
            $this->warps[] = $name;
            $this->addButton(TextFormat::GOLD . $name);
        }
        if ($session->getIslandObject()->getTeleportModule()->canAddWarp()){
            $this->create = true;
            $this->addButton(TextFormat::GREEN . "Create");
        }
        $this->addButton(Glyphs::BOX_EXCLAMATION . TextFormat::RED . " Back");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data){
            $session = SessionManager::getSession($player->getXuid());
            if ($data === null || $this->cancel){
                return;
            }
            if ($session->getIslandObject() === null){
                return;
            }
            if (isset($this->warps[$data])){
                if (!$session->getIslandObject()->getMembersModule()->isOperator($session->getXuid())){
                    $session->getIslandObject()->getTeleportModule()->teleport($this->warps[$data], $session);
                    return;
                }
                IslandUIHandler::sendAdminWarpUI($session, $this->warps[$data]);
                return;
            }
            if ($this->create === true && $data === count($this->warps)){
                if ($session->getIslandObject()->getMembersModule()->isOperator($session->getXuid())){
                    IslandUIHandler::sendWarpCreateUI($session);
                    return;
                }
            }
            if ($this->create && $data === count($this->warps) + 1){
                IslandUIHandler::sendIslandUI($session);
                return;
            }
            if ($this->create === false && $data === count($this->warps)){
                IslandUIHandler::sendIslandUI($session);
                return;
            }
        };
    }
}