<?php
declare(strict_types=1);

namespace cylexsky\custom;

use core\main\managers\Manager;
use cylexsky\custom\blocks\BlockHandler;
use cylexsky\custom\items\ItemHandler;
use pocketmine\item\Item;

class CustomManager extends Manager{

    private static $items = [];

    protected function init(): void
    {
        ItemHandler::init();
        BlockHandler::init();
    }

    public static function getItems(): array {
        return self::$items;
    }

    public static function addItem(Item $item){
        $name = $item->getName();
        self::$items = array_merge(self::$items, [$name => $item]);
    }

    protected function close(): void
    {
        // TODO: Implement close() method.
    }
}