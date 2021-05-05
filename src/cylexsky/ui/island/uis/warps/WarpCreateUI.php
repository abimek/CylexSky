<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\warps;

use core\forms\formapi\CustomForm;
use core\main\text\TextFormat;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class WarpCreateUI extends CustomForm{

    private $session;
    private $cancel = false;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD_GREEN . "Warp Create");
        if ($session->getIslandObject() === null){
            $this->cancel = true;
            $this->addLabel(Glyphs::BOX_EXCLAMATION .  TextFormat::GRAY . " You are not in an island!");
            return;
        }
        if ($session->getPlayer()->getWorld()->getFolderName() !== $session->getIsland()){
            $this->cancel = true;
            $this->addLabel(Glyphs::BOX_EXCLAMATION .  TextFormat::GRAY . " You must be in your island to add a warp!");
            return;
        }
        if (!$session->getIslandObject()->getTeleportModule()->canAddWarp()){
            $this->cancel = true;
            $this->addLabel(Glyphs::BOX_EXCLAMATION .  TextFormat::GRAY . " Island Warps are maxed out!");
            return;
        }else{
            $l = $session->getPlayer()->getLocation();
            $content = Glyphs::BOX_EXCLAMATION . TextFormat::GOLD . " Create a warp" . "\n";
            $content .= TextFormat::GOLD . "Location: " . "\n";
            $content .= TextFormat::RED . "  X: " . TextFormat::GRAY . floor($l->getX()) . "\n";
            $content .= TextFormat::RED . "  Y: " . TextFormat::GRAY . floor($l->getY()) . "\n";
            $content .= TextFormat::RED . "  Z: " . TextFormat::GRAY . floor($l->getZ());
            $this->addLabel($content);
            $this->addInput(TextFormat::DARK_GRAY . "Warp Name");
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data = null){
            $session = SessionManager::getSession($player->getXuid());
            if ($data === null || $this->cancel){
                return;
            }
            if ($session->getIslandObject() === null){
                return;
            }
            if (!$session->getIslandObject()->getTeleportModule()->canAddWarp()){
                $session->sendNotification("Warp limit reached");
                return;
            }
            if ($data[1] === "" || $data[1] === null){
                $session->sendNotification("No data inputed!");
                return;
            }
            $session->getIslandObject()->getTeleportModule()->addWarp($data[1], $player->getLocation());
            $session->sendGoodNotification("Successfully added island warp " . TextFormat::GOLD . $data[1] . TextFormat::GREEN . "!");
        };
    }
}