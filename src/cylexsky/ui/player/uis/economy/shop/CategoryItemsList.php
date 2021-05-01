<?php
declare(strict_types=1);

namespace cylexsky\ui\player\uis\economy\shop;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\misc\shop\objects\Category;
use cylexsky\misc\shop\objects\ShopItem;
use cylexsky\session\PlayerSession;
use cylexsky\ui\player\PlayerUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class CategoryItemsList extends SimpleForm{

    private $session;
    private $category;
    private $items = [];

    public function __construct(PlayerSession $session, Category $category)
    {
        $this->session = $session;
        $this->category = $category;
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(Glyphs::GOLD_COIN . TextFormat::BOLD_GOLD . $category->getName() . Glyphs::GOLD_COIN);
        foreach ($category->getShopItems() as $item){
            assert($item instanceof ShopItem);
            $this->items[] = $item;
            if (is_string($item->getTexture())){
                $firstString = substr($item->getTexture(), 0, 4);
                if ($firstString === "text"){
                    $this->addButton(TextFormat::GRAY . $item->getButtonName(), self::IMAGE_TYPE_PATH, $item->getTexture());
                }else{
                    $this->addButton(TextFormat::GRAY . $item->getButtonName(), self::IMAGE_TYPE_URL, $item->getTexture());
                }
                continue;
            }
            $this->addButton(TextFormat::GRAY . $item->getButtonName());
        }
        $this->addButton(Glyphs::BOX_EXCLAMATION . " " . TextFormat::RED . "Back");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data=null){
            if ($data === null){
                return;
            }

            if ($data === count($this->category->getShopItems())){
                PlayerUIHandler::sendShopUI($this->session);
                return;
            }
            PlayerUIHandler::sendShopItemFOrm($this->session, $this->items[$data]);
        };
    }
}