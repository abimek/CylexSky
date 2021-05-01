<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\island\modules\LevelModule;
use cylexsky\session\PlayerSession;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class IslandInfoUI extends SimpleForm{

    private $session;

    public function __construct(PlayerSession $session, bool $back = true)
    {
        parent::__construct($this->getFormResultCallable());
        $this->session = $session;
        $this->setTitle(TextFormat::BOLD_RED . "Island Info");
        if ($session->getIslandObject() === null){
            $this->setTitle(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "You are not a member of an island");
            return;
        }
        $is = $session->getIslandObject();
        $content = "";
        $content .= TextFormat::RED . "Owner: " . TextFormat::GRAY . $is->getOwnerName() . "\n";
        $content .= TextFormat::GOLD . "Wealth: " . TextFormat::GRAY . $is->getWealthModule()->getWealth(). "\n";
        $content .= TextFormat::GOLD . "Level: " . TextFormat::GRAY . $is->getLevelModule()->getLevel() . Glyphs::GOLD_MEDAL;
        $content .= TextFormat::GOLD . "Xp: " . TextFormat::YELLOW . TextFormat::GRAY . $is->getLevelModule()->getXp() . TextFormat::GRAY . "/" .TextFormat::RED . $is->getLevelModule()->getXpForNextLevel(). "\n";
        $content .= TextFormat::GOLD . "PrestigeShards: " . TextFormat::GRAY . $is->getPrestigeShards() . Glyphs::PRESTIGE_SHARDS. "\n";
        $content .= TextFormat::GOLD . "PrestigePoints: " . TextFormat::GRAY . $is->getPrestigePoints() . Glyphs::PRESTIGE_SHARDS. "\n";
        $content .= TextFormat::BOLD_GOLD . "Prestige: ". "\n" . TextFormat::RESET;
        $content .= TextFormat::RED . "  Current Prestige: " . TextFormat::GRAY . $is->getLevelModule()->getPrestige(). "/"  . TextFormat::RED . $is->getLevelModule()->getCurrentPrestigeMax() . "\n";
        if ($is->getLevelModule()->getLevel() < LevelModule::MAX_LEVEL){
            $content .= TextFormat::RED . "    Required Prestige Shards: " . TextFormat::GRAY . $is->getLevelModule()->getPrestigeShardsForNextPrestige(). "\n";
            $content .= TextFormat::RED . "    Required Island Level: " . TextFormat::GRAY . $is->getLevelModule()->getNextPrestigeLevel(). "\n";
        }else{
            if ($is->getLevelModule()->getPrestige() === LevelModule::PRESTIEGE_MAX){
                $content .= TextFormat::RED . "    Highest Prestige Achieved!". "\n";
            }
        }
        $content .= TextFormat::RED . "Warp Count: " . TextFormat::YELLOW . $is->getTeleportModule()->getWarpCount() . TextFormat::GRAY . "/" . TextFormat::RED . $is->getUpgradeModule()->getWarpLimit(). "\n";
        $content .= TextFormat::RED . "Member Count: " . TextFormat::YELLOW . $is->getMembersModule()->getMemberCount() . TextFormat::GRAY . "/" . TextFormat::GOLD . $is->getMembersModule()->getMemberLimit(). "\n";
        $content .= TextFormat::RED . "Trusted Count: " . TextFormat::YELLOW . $is->getTrustedModule()->getTrustedCount() . TextFormat::GRAY . "/" . TextFormat::GOLD . $is->getTrustedModule()->getTrustedLimit();
        $this->setContent($content);
        if ($back){
            $this->addButton(TextFormat::RED . "Back");
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data){
            if ($data === null){
                return;
            }
            if ($data === 0){
                IslandUIHandler::sendIslandUI($this->session);
            }
        };
    }
}