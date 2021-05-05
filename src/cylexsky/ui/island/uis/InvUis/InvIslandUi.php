<?php

namespace cylexsky\ui\island\uis\InvUis;

use core\main\text\TextFormat;
use cylexsky\custom\items\items\base\ItemI;
use cylexsky\CylexSky;
use cylexsky\island\commands\subcommands\MembersCommand;
use cylexsky\ui\InventoryUI;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class InvIslandUi extends InventoryUI{

    public const RESERVED_SLOTS = [4, 10, 11, 12, 13, 14, 15, 16, 22];

    public const GO = 4;
    public const UPGRADES = 10;
    public const WARPS = 11;
    public const INFO = 12;
    public const MEMBERS = 13;
    public const MANAGEMENT = 14;
    public const TOP_ISLANDS = 15;
    public const VISIT = 16;
    public const ISLAND_CHAT = 22;

    public function __construct(Player $player)
    {
        parent::__construct($player, InventoryUI::MAPINV);
        $this->menu->setName(Glyphs::ISLAND_ICON . TextFormat::BOLD_GOLD . "Island Menu");
    }

    protected function prepareItems(): void
    {
        $this->menu->setName(Glyphs::ISLAND_ICON . TextFormat::BOLD_GOLD . "Island Menu");
        $inv = $this->menu->getInventory();
        $session = $this->session;
        for($i = 0; $i < $inv->getSize(); $i++){
            if (!in_array($i, self::RESERVED_SLOTS)){
                $inv->setItem($i, self::getNullItem());
                continue;
            }
            switch ($i) {
                case self::GO:
                    $go = self::getItem(ItemIds::ENDER_EYE, 0, Glyphs::GREEN_BOX_EXCLAMATION . " " . TextFormat::GREEN . "Teleport To Island", []);
                    $inv->setItem($i, $go);
                    break;
                case self::UPGRADES:
                    $upgrade = self::getItem(ItemIds::ANVIL, 0, Glyphs::GREEN_BOX_EXCLAMATION . " " . TextFormat::RED . "Upgrade Island", []);
                    $inv->setItem($i, $upgrade);
                    break;
                case self::WARPS:
                    $warps = self::getItem(ItemIds::ENDER_PEARL, 0, Glyphs::GREEN_BOX_EXCLAMATION . " " . TextFormat::GOLD . "Island Warps", [TextFormat::YELLOW . "See the warps of your island!"]);
                    $inv->setItem($i, $warps);
                    break;
                case self::INFO:
                    $info = self::getItem(ItemIds::PAPER, 0, Glyphs::GREEN_BOX_EXCLAMATION . " " . TextFormat::AQUA . "Island Info", [TextFormat::RED . "See your island info!"]);
                    $inv->setItem($i, $info);
                    break;
                case self::MEMBERS:
                    $members = self::getItem(ItemIds::BELL, 0, Glyphs::GREEN_BOX_EXCLAMATION . " " .  TextFormat::DARK_RED . "Island Members", []);
                    $inv->setItem($i, $members);
                    break;
                case self::MANAGEMENT:
                    $management = self::getItem(ItemIds::COMPASS, 0,Glyphs::GREEN_BOX_EXCLAMATION . " " .  TextFormat::LIGHT_PURPLE . "Management", [TextFormat::AQUA . "Manage your island! (CoOwners+)"]);
                    $inv->setItem($i, $management);
                    break;
                case self::TOP_ISLANDS:
                    $topislands = self::getItem(ItemIds::BOAT, 0, Glyphs::GREEN_BOX_EXCLAMATION . " " .  TextFormat::DARK_PURPLE . "Top Islands", [TextFormat::LIGHT_PURPLE . "Look at the top islands!"]);
                    $inv->setItem($i, $topislands);
                    break;
                case self::VISIT:
                    $visit = self::getItem(ItemIds::DIAMOND, 0, Glyphs::GREEN_BOX_EXCLAMATION . " " .  TextFormat::BOLD_BLUE . "Visit Islands", [TextFormat::GREEN . "Go visit someone's island!"]);
                    $inv->setItem($i, $visit);
                    break;
                case self::ISLAND_CHAT:
                    $chat = self::getItem(ItemIds::LEVER, 0, Glyphs::GREEN_BOX_EXCLAMATION . " " .  TextFormat::BOLD_BLUE . "Island Chat", []);
                    if ($session->getMiscModule()->inIslandChat()){
                        $chat->setCustomName(Glyphs::BOX_EXCLAMATION . TextFormat::RED . "Leave Island Chat");
                    }else{
                        $chat->setCustomName(Glyphs::GREEN_BOX_EXCLAMATION . TextFormat::RED . "Enter Island Chat");
                    }
                    $inv->setItem($i, $chat);
                    break;
            }
        }
    }

    protected function prepareActionListener(): void
    {
        $this->menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult{
            $map = [4 => 0, 10 => 1, 11 => 2, 12 => 3, 13 => 4, 14 => 5, 15 => 6, 16 => 7, 22 => 8];
            if (isset($map[$transaction->getAction()->getSlot()])){
                $slot = $map[$transaction->getAction()->getSlot()];
                $session = $this->session;
                $player = $transaction->getPlayer();
                switch ($slot){
                    case 0:
                        if ($session->getTeleportModule()->canTeleport() === false){
                            $session->sendNotification("Unable to teleport!");
                            break;
                        }
                        $session->getIslandObject()->teleportPlayer($player);
                        break;
                    case 1:
                        $this->menu->onClose($player);
                        CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function (): void{
                            IslandUIHandler::sendIslandUpgradeForm($this->session);
                        }), 20);
                        break;
                    case 2:
                        $this->menu->onClose($player);
                        CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function (): void{
                            IslandUIHandler::sendWarpUI($this->session);
                        }), 20);
                        break;
                    case 3:
                        $this->menu->onClose($player);
                        CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function (): void{
                            IslandUIHandler::sendIslandInfoForm($this->session);
                        }), 20);
                        break;
                    case 4:
                        $this->menu->onClose($player);
                        CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function (): void{
                            IslandUIHandler::sendManagementUI($this->session);
                        }), 20);
                        MembersCommand::sendMembersInUI($player);
                        break;
                    case 5:
                        if ($player->getXuid() !== $session->getIslandObject()->getOwner() && !$session->getIslandObject()->getMembersModule()->isCoOwner($player->getXuid())){
                            $session->sendNotification("Only island " . TextFormat::GOLD . "owners and coowners" . TextFormat::GRAY . " can manage the island!");
                            break;
                        }
                        $this->menu->onClose($player);
                        CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function (): void{
                            IslandUIHandler::sendManagementUI($this->session);
                        }), 20);
                        break;
                    case 6:
                        $this->menu->onClose($player);
                        CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function (): void{
                            IslandUIHandler::sendVisitForm($this->session);
                        }), 20);
                        break;
                    case 7:
                        $this->menu->onClose($player);
                        CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function (): void{
                            IslandUIHandler::sendIslandTopForm($this->session);
                        }), 20);
                        break;
                    case 8:
                        $this->menu->onClose($player);
                        $session->getMiscModule()->toggleIslandChat();
                    default:

                }
            }
            return new InvMenuTransactionResult(true);
        });
    }
}