<?php
declare(strict_types=1);

namespace cylexsky\custom\items\items\base;

use cylexsky\custom\items\items\traits\CustomComponentTrait;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use customies\item\ItemComponentTrait;

class CustomItem extends Item {

    use CustomComponentTrait;

    public function __construct(ItemIdentifier $identifier, string $name = "Unknown")
    {
        parent::__construct($identifier, $name);
        $this->initPropertiesAndInitialData();
    }

}