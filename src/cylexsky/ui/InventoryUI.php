<?php

namespace cylexsky\ui;

use core\main\text\TextFormat;
use customies\item\CustomiesItemFactory;
use cylexsky\custom\items\ItemIdentifiers;
use cylexsky\CylexSky;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\utils\Sounds;
use LogicException;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\metadata\SingleBlockMenuMetadata;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ToolTier;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

abstract class InventoryUI{

    public const MAPINV = "cylexsky:map_inv";


    protected static $nullItem = null;

    private static $backItem = null;

    /** @var null|InventoryUI */
    protected $nextUI = null;

    protected $menu;

    protected $session;

    public function __construct(Player $player, string $type){
        $this->menu = InvMenu::create($type);
        $this->session = SessionManager::getSession($player->getXuid());
        $this->prepareItems();
        $this->prepareActionListener();
        $this->prepareClosingListener();
    }

    public final static function init() : void{
        BlockFactory::getInstance()->register(new Opaque(new BlockIdentifier(BlockLegacyIds::SHULKER_BOX), "Shulker Box", new BlockBreakInfo(1.25, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 21.0)));
        $type = new SingleBlockMenuMetadata(
            self::MAPINV,
            27,
            WindowTypes::CONTAINER,
             BlockFactory::getInstance()->get(BlockLegacyIds::SHULKER_BOX, 0),
        );
        InvMenuHandler::registerMenuType($type);
        if(self::$nullItem !== null){
            throw new LogicException("The null item has already been initialized!");
        } else {
            self::$nullItem = self::getItemFromFactory(ItemIdentifiers::GRAY_GLASS_PANE, "", []);
            self::$nullItem->setCustomName(" ");
            $backItem = self::getItemFromFactory(ItemIdentifiers::BACK_ITEM, TextFormat::BOLD_RED . "Back", [TextFormat::GRAY . "Go back to the previous\nmenu!"]);
            $backItem->setNamedTag($backItem->getNamedTag()->setByte("back", (int)true));
            self::$backItem = $backItem;
        }
    }

    public final static function getNullItem() : Item{
        return self::$nullItem;
    }

    protected final static function getBackItem() : Item{
        return self::$backItem;
    }

    public final static function getItem(int $id, int $meta, string $name, array $lore) : Item{
        $item = ItemFactory::getInstance()->get($id, $meta);
        $item->setCustomName($name);
        $item->setLore($lore);

        return $item;
    }

    protected final static function getItemFromFactory(string $identifier, string $name, array $lore): Item
    {
        $item = CustomiesItemFactory::get($identifier, 1);
        $item->setCustomName($name);
        $item->setLore($lore);

        return $item;
    }

    abstract protected function prepareItems() : void;

    abstract protected function prepareActionListener() : void;

    public function sendDingSound(PlayerSession $session)
    {

        Sounds::sendSoundPlayer($session->getPlayer(), Sounds::DING_SOUND);
    }

    public function clearItemInCursor(PlayerSession $session)
    {
        $session->getPlayer()->getCursorInventory()->setItem(0, ItemFactory::getInstance()->get(0));
    }

    protected function prepareClosingListener() : void{
        $this->menu->setInventoryCloseListener(
            function(Player $player, Inventory $inventory) : void{
                if($this->nextUI !== null){
                    $this->nextUI->send();
                }
            }
        );
    }

    public function send(bool $instant = false){
        if(!$instant) {
            CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(
                new ClosureTask(function (): void {
                    $this->menu->send($this->session->getPlayer());
                }),
                15
            );
        } else {
            $this->menu->send($this->session->getPlayer());
        }
    }

}