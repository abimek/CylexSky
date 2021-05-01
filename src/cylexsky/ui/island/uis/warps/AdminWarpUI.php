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
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class AdminWarpUI extends SimpleForm{

    private $cancel = false;
    private $wname = "";

    public function __construct(PlayerSession $session, string $name)
    {
        parent::__construct($this->getFormResultCallable());
        $this->wname = $name;
        $this->setTitle(Glyphs::OPEN_BOOK . TextFormat::BOLD_GOLD. $name . TextFormat::RED . " Warps" . Glyphs::OPEN_BOOK);
        if ($session->getIslandObject() === null){
            $session->sendNotification("You are not in an island!");
            $this->cancel = true;
            return;
        }
        if (!$session->getIslandObject()->getTeleportModule()->warpExists($name)){
            $this->setContent(Glyphs::GREEN_BOX_EXCLAMATION . TextFormat::GRAY . "The warp no longer exists!");
            $this->cancel = true;
            return;
        }
        $l = $session->getIslandObject()->getTeleportModule()->getWarp($name);
        $content = "";
        $content .= TextFormat::RED . "Warp Name: " .TextFormat::GRAY . $name . "\n";
        $content .= TextFormat::GOLD . "Location: " . "\n";
        $content .= TextFormat::RED . "  X: " . TextFormat::GRAY . floor($l->getX()) . "\n";
        $content .= TextFormat::RED . "  Y: " . TextFormat::GRAY . floor($l->getY()) . "\n";
        $content .= TextFormat::RED . "  Z: " . TextFormat::GRAY . floor($l->getZ());
        $this->setContent($content);
        $this->addButton(TextFormat::BOLD_GREEN . "Teleport");
        $this->addButton(TextFormat::RED . "Delete");
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
            if (!$session->getIslandObject()->getMembersModule()->isOperator($session->getXuid())){
                return;
            }
            if (!$session->getIslandObject()->getTeleportModule()->warpExists($this->wname)){
                $session->sendNotification("Warp does not exist!");
                return;
            }
            switch ($data){
                case 0:
                    $session->getIslandObject()->getTeleportModule()->teleport($this->wname, $session);
                    return;
                case 1:
                    $session->getIslandObject()->getTeleportModule()->deleteWarp($this->wname);
                    IslandUIHandler::sendWarpUI($session);
                    return;
                case 2:
                    IslandUIHandler::sendWarpUI($session);
                    return;
            }
        };
    }
}
