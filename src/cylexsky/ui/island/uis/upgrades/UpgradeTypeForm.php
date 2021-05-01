<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\upgrades;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\island\modules\UpgradesModule as U;
use cylexsky\session\PlayerSession;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use cylexsky\utils\Utils;
use pocketmine\player\Player;

class UpgradeTypeForm extends SimpleForm{

    private $session;
    private $cancel = false;
    private $back = false;
    private $type;

    public function __construct(PlayerSession $session, int $type)
    {
        parent::__construct($this->getFormResultCallable());
        $this->session = $session;
        if ($session->getIslandObject() === null){
            $this->cancel = true;
            $this->setTitle(TextFormat::YELLOW . "Upgrade");
            $this->setContent(TextFormat::GRAY . "You are not in an island");
            return;
        }
        $this->type = $type;
        $is = $session->getIslandObject();
        $content = Glyphs::GOLD_COIN . TextFormat::GOLD . $is->getPrestigePoints() . TextFormat::GRAY . "Prestige Coins";
        $up = $is->getUpgradeModule();
        switch ($type){
            case U::MINION_LIMIT:
                $this->setTitle(TextFormat::BOLD_RED . "Minion Limit");
                $content = Glyphs::BOX_EXCLAMATION . TextFormat::AQUA . " Minion Limit:" . "\n". "\n";
                $content .= TextFormat::RED . "  Description" . TextFormat::GOLD. ": " . TextFormat::GRAY . "The amount of minions that you're allowed to have on your island! The more minions you have, the more forced, I mean volunteer work that your island gets!\n";
                $content .= TextFormat::BOLD_GREEN . "Data: " . TextFormat::RESET. "\n";
                $content .= TextFormat::YELLOW . "  Current Limit" . TextFormat::GOLD . ": " . TextFormat::GRAY . $up->getMinionLimit() . Glyphs::MINION. "\n";
                if ($up->isCurrentPrestigeMax(U::MINION_LIMIT)){
                    $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "Current Minion Limit Reached" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    if ($up->getNextPrestigeUpgrade($type) !== null){
                        $content .= TextFormat::BOLD_GREEN . "  (" . TextFormat::RESET_GRAY . "Available Upgrade at prestige: " . TextFormat::AQUA .  Utils::numberToRomanRepresentation($up->getNextPrestigeUpgrade($type)) . TextFormat::BOLD_GREEN . ")" . TextFormat::RESET. "\n";
                        $content .= TextFormat::AQUA . "  " . $up->getMinionLimit() . Glyphs::RIGHT_ARROW . TextFormat::GOLD . $up->getNextMinionLimit(). "\n";
                    }else{
                        $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "No available upgrades" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    }
                }
                break;
            case U::SPAWNER_LIMIT:
                $this->setTitle(TextFormat::BOLD_RED . "Spawner Limit");
                $content = Glyphs::BOX_EXCLAMATION . TextFormat::AQUA . " Spawner Limit:" . "\n". "\n";
                $content .= TextFormat::RED . "  Description" . TextFormat::GOLD. ": " . TextFormat::GRAY . "The amount of spawners that you're allowed to have on your island! The more spawners you have, the more money that you make!\n";
                $content .= TextFormat::BOLD_GREEN . "Data: " . TextFormat::RESET. "\n";
                $content .= TextFormat::YELLOW . "  Current Limit" . TextFormat::GOLD . ": " . TextFormat::GRAY . $up->getSpawnerLimit() . " Spawners". "\n";
                if ($up->isCurrentPrestigeMax(U::SPAWNER_LIMIT)){
                    $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "Current Spawner Limit Reached" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    if ($up->getNextPrestigeUpgrade($type) !== null){
                        $content .= TextFormat::BOLD_GREEN . "  (" . TextFormat::RESET_GRAY . "Available Upgrade at prestige: " . TextFormat::AQUA .  Utils::numberToRomanRepresentation($up->getNextPrestigeUpgrade($type)) . TextFormat::BOLD_GREEN . ")" . TextFormat::RESET. "\n";
                        $content .= TextFormat::AQUA . "  " . $up->getSpawnerLimit() . Glyphs::RIGHT_ARROW . TextFormat::GOLD . $up->getNextSpawnerLimit(). "\n";
                    }else{
                        $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "No available upgrades" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    }
                }
                break;
            case U::SPAWNER_TYPES:
                $this->setTitle(TextFormat::BOLD_RED . "Spawner Types");
                $content = Glyphs::BOX_EXCLAMATION . TextFormat::AQUA . " Spawner Type:" . "\n". "\n";
                $content .= TextFormat::RED . "  Description" . TextFormat::GOLD. ": " . TextFormat::GRAY . "The types of spawners that you're allowed to have on your island! Gradually increasing their profitability, you want to get the best type possible!\n";
                $content .= TextFormat::BOLD_GREEN . "Data: " . TextFormat::RESET. "\n";
                foreach (U::SPAWNER_TYPE_NAMES as $id => $name){
                    if ($id > $up->getSpawnerType()){
                        $content .= "  " . TextFormat::RED . $name . " " . TextFormat::YELLOW . "Prestige: " . TextFormat::AQUA . Utils::numberToRomanRepresentation($id+1) . "\n";
                    }else{
                        $content .= "  " . TextFormat::GREEN . $name . "\n";
                    }
                }
                if ($up->isCurrentPrestigeMax(U::SPAWNER_TYPES)){
                    $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "Current Spawner Type Reached" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    if ($up->getNextPrestigeUpgrade($type) !== null){
                        $content .= TextFormat::BOLD_GREEN . "  (" . TextFormat::RESET_GRAY . "Available Upgrade at prestige: " . TextFormat::AQUA .  Utils::numberToRomanRepresentation($up->getNextPrestigeUpgrade($type)) . TextFormat::BOLD_GREEN . ")" . TextFormat::RESET. "\n";
                        $content .= TextFormat::AQUA . "  " . U::SPAWNER_TYPE_NAMES[$up->getSpawnerType()] . Glyphs::RIGHT_ARROW . TextFormat::GOLD . U::SPAWNER_TYPE_NAMES[$up->getSpawnerType() + 1]. "\n";
                    }else{
                        $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "No available upgrades" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    }
                }
                break;
            case U::RESOURCE_NODE_LIMIT:
                $this->setTitle(TextFormat::BOLD_RED . "Resource Node Limit");
                $content = Glyphs::BOX_EXCLAMATION . TextFormat::AQUA . " Resource Node Limit:" . "\n". "\n";
                $content .= TextFormat::RED . "  Description" . TextFormat::GOLD. ": " . TextFormat::GRAY . "The amount of resource nodes that you're allowed to have on your island! The more resource nodes you have, the more money that you make, but dont forget to actually mine them!\n\n";
                $content .= TextFormat::BOLD_GREEN . "Data: " . TextFormat::RESET. "\n";
                $content .= TextFormat::YELLOW . "  Current Limit" . TextFormat::GOLD . ": " . TextFormat::GRAY . $up->getResourceNodeLimit() . " Resource Nodes";
                if ($up->isCurrentPrestigeMax(U::RESOURCE_NODE_LIMIT)){
                    $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "Current Resource Node Limit Reached" . TextFormat::BOLD_RED . ")" . TextFormat::RESET;
                    if ($up->getNextPrestigeUpgrade($type) !== null){
                        $content .= TextFormat::BOLD_GREEN . "  (" . TextFormat::RESET_GRAY . "Available Upgrade at prestige: " . TextFormat::AQUA .  Utils::numberToRomanRepresentation($up->getNextPrestigeUpgrade($type)) . TextFormat::BOLD_GREEN . ")" . TextFormat::RESET. "\n";
                        $content .= TextFormat::AQUA . "  " . $up->getResourceNodeLimit() . Glyphs::RIGHT_ARROW . TextFormat::GOLD . $up->getNextResourceNodeLimit(). "\n";
                    }else{
                        $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "No available upgrades" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    }
                }
                break;
            case U::RESOURCE_NODE_TYPE:
                $this->setTitle(TextFormat::BOLD_RED . "Resource Node Types");
                $content = Glyphs::BOX_EXCLAMATION . TextFormat::AQUA . " Resource Node Type:" . "\n". "\n";
                $content .= TextFormat::RED . "  Description" . TextFormat::GOLD. ": " . TextFormat::GRAY . "The types of resource nodes that you're allowed to have on your island! Gradually increasing their profitability, you want to get the best resource node type possible to make the most money!\n\n";
                $content .= TextFormat::BOLD_GREEN . "Data: " . TextFormat::RESET. "\n";
                foreach (U::RESOURCE_NODE_NAMES as $id => $name){
                    if ($id > $up->getResourceNodeType()){
                        $content .= "  " . TextFormat::RED . $name . " " . TextFormat::YELLOW . "Prestige: " . TextFormat::AQUA . Utils::numberToRomanRepresentation($id+1) . "\n";
                    }else{
                        $content .= "  " . TextFormat::GREEN . $name . "\n";
                    }
                }
                if ($up->isCurrentPrestigeMax(U::RESOURCE_NODE_TYPE)){
                    $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "Current Resource Node Type Reached" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    if ($up->getNextPrestigeUpgrade($type) !== null){
                        $content .= TextFormat::BOLD_GREEN . "  (" . TextFormat::RESET_GRAY . "Available Upgrade at prestige: " . TextFormat::AQUA .  Utils::numberToRomanRepresentation($up->getNextPrestigeUpgrade($type)) . TextFormat::BOLD_GREEN . ")" . TextFormat::RESET. "\n";
                        $content .= TextFormat::AQUA . "  " . U::RESOURCE_NODE_NAMES[$up->getResourceNodeType()] . Glyphs::RIGHT_ARROW . TextFormat::GOLD . U::RESOURCE_NODE_NAMES[$up->getResourceNodeType() + 1]. "\n";
                    }else{
                        $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "No available upgrades" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    }
                }
                break;
            case U::HOPPER_LIMIT:
                $this->setTitle(TextFormat::BOLD_RED . "Hopper Limit");
                $content = Glyphs::BOX_EXCLAMATION . TextFormat::AQUA . " Hopper Limit:" . "\n". "\n";
                $content .= TextFormat::RED . "  Description" . TextFormat::GOLD. ": " . TextFormat::GRAY . "The amount of hopper(industrial or not) that you are allowed to have on your island, make sure to upgrade them well to have the most efficent island and be at the top!\n\n";
                $content .= TextFormat::BOLD_GREEN . "Data: " . TextFormat::RESET. "\n";
                $content .= TextFormat::YELLOW . "  Current Limit" . TextFormat::GOLD . ": " . TextFormat::GRAY . $up->getHopperLimit() . " Hoppers";
                if ($up->isCurrentPrestigeMax(U::HOPPER_LIMIT)){
                    $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "Current Hopper Limit Reached" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    if ($up->getNextPrestigeUpgrade($type) !== null){
                        $content .= TextFormat::BOLD_GREEN . "  (" . TextFormat::RESET_GRAY . "Available Upgrade at prestige: " . TextFormat::AQUA .  Utils::numberToRomanRepresentation($up->getNextPrestigeUpgrade($type)) . TextFormat::BOLD_GREEN . ")" . TextFormat::RESET. "\n";
                        $content .= TextFormat::AQUA . "  " . $up->getHopperLimit() . Glyphs::RIGHT_ARROW . TextFormat::GOLD . $up->getNextHopperLimit(). "\n";
                    }else{
                        $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "No available upgrades" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    }
                }
                break;
            case U::WARPS_LIMIT:
                $this->setTitle(TextFormat::BOLD_RED . "Warp Limit");
                $content = Glyphs::BOX_EXCLAMATION . TextFormat::AQUA . " Warp Limit:" . "\n". "\n";
                $content .= TextFormat::RED . "  Description" . TextFormat::GOLD. ": " . TextFormat::GRAY . "Increase the amount of locations on your island that you can instantly teleport to, warps are a time-saver!\n\n";
                $content .= TextFormat::BOLD_GREEN . "Data: " . TextFormat::RESET. "\n";
                $content .= TextFormat::YELLOW . "  Current Limit" . TextFormat::GOLD . ": " . TextFormat::GRAY . $up->getWarpLimit() . " Warps";
                if ($up->isCurrentPrestigeMax(U::WARPS_LIMIT)){
                    $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "Current Warp Limit Reached" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    if ($up->getNextPrestigeUpgrade($type) !== null){
                        $content .= TextFormat::BOLD_GREEN . "  (" . TextFormat::RESET_GRAY . "Available Upgrade at prestige: " . TextFormat::AQUA .  Utils::numberToRomanRepresentation($up->getNextPrestigeUpgrade($type)) . TextFormat::BOLD_GREEN . ")" . TextFormat::RESET. "\n";
                        $content .= TextFormat::AQUA . "  " . $up->getWarpLimit() . Glyphs::RIGHT_ARROW . TextFormat::GOLD . $up->getNextWarpLimit(). "\n";
                    }else{
                        $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "No available upgrades" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    }
                }
                break;
            case U::ISLAND_BORDER:
                $this->setTitle(TextFormat::BOLD_RED . "Island Border Limit");
                $content = Glyphs::BOX_EXCLAMATION . TextFormat::AQUA . " Island Border Limit:" . "\n". "\n";
                $content .= TextFormat::RED . "  Description" . TextFormat::GOLD. ": " . TextFormat::GRAY . "Increase the border of your island, to expand, expand, expand!\n\n";
                $content .= TextFormat::BOLD_GREEN . "Data: " . TextFormat::RESET. "\n";
                $content .= TextFormat::YELLOW . "  Current Limit" . TextFormat::GOLD . ": " . TextFormat::AQUA . $up->getIslandBorderLimit() . TextFormat::GRAY . "x" . TextFormat::AQUA . $up->getIslandBorderLimit(). TextFormat::GRAY . " blocks";
                if ($up->isCurrentPrestigeMax(U::WARPS_LIMIT)){
                    $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "Current Border Limit Reached" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    if ($up->getNextPrestigeUpgrade($type) !== null){
                        $content .= TextFormat::BOLD_GREEN . "  (" . TextFormat::RESET_GRAY . "Available Upgrade at prestige: " . TextFormat::AQUA .  Utils::numberToRomanRepresentation($up->getNextPrestigeUpgrade($type)) . TextFormat::BOLD_GREEN . ")" . TextFormat::RESET. "\n";
                        $content .= TextFormat::AQUA . "  " .  TextFormat::AQUA . $up->getIslandBorderLimit() . TextFormat::GRAY . "x" . TextFormat::AQUA . $up->getIslandBorderLimit(). Glyphs::RIGHT_ARROW . TextFormat::GOLD  . $up->getNextBorderLimit() . TextFormat::GRAY . "x" . TextFormat::GOLD . $up->getNextBorderLimit(). TextFormat::GRAY. "\n";
                    }else{
                        $content .= TextFormat::BOLD_RED . "  (" . TextFormat::RESET_GRAY . "No available upgrades" . TextFormat::BOLD_RED . ")" . TextFormat::RESET. "\n";
                    }
                }
                break;
        }
        $this->setContent($content);
        if ($session->getIslandObject()->getMembersModule()->isOperator($session->getXuid())){
            if ($up->canUpgrade($type)){
                if ($up->hasEnoughPrestigeCoinsForNextUpgrade($type)){
                    $this->addButton(TextFormat::BOLD_GREEN . "Upgrade: " . TextFormat::RESET_GRAY . $up->getPrestigeCoinsForNextUpgrade($type));
                }else{
                    $this->addButton(TextFormat::BOLD_RED . "Upgrade: " . TextFormat::RESET_GRAY . $up->getPrestigeCoinsForNextUpgrade($type));
                }
            }else{
                $this->addButton(TextFormat::RED . "No Upgrade Available Currently!");
            }
        }
        $this->back = true;
        $this->addButton(Glyphs::BOX_EXCLAMATION . TextFormat::RED . "Back");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data = null){
            if ($data === null || $this->cancel){
                return;
            }
            if ($data === 1){
                IslandUIHandler::sendIslandUpgradeForm($this->session);
                return;
            }
            $session = $this->session;
            $type = $this->type;
            if ($session->getIslandObject() === null){
                return;
            }
            $up = $session->getIslandObject()->getUpgradeModule();
            if ($session->getIslandObject()->getMembersModule()->isOperator($session->getXuid())){
                if ($up->canUpgrade($type)){
                    if ($up->hasEnoughPrestigeCoinsForNextUpgrade($type)){
                        $up->upgrade($type);
                    }else{
                        $session->sendNotification("Island Does Not have Enough Prestige Tokens to upgrade");
                    }
                }else{
                    $session->sendNotification(TextFormat::RED . "No Island Upgrade Available!");
                }
            }
        };
    }
}
