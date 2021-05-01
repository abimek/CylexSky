<?php
declare(strict_types=1);

namespace cylexsky\custom\items\items;

use customies\item\ItemComponentsTrait;
use cylexsky\custom\items\items\traits\CustomComponentTrait;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;

class Armor extends \pocketmine\item\Armor {

    use CustomComponentTrait;
    use ItemComponentsTrait;

    public function __construct(ItemIdentifier $identifier, string $name)
    {
        $params = self::getParameters();
        parent::__construct($identifier, $name, new ArmorTypeInfo($params["defencePoints"], $params["maxDurability"], $params["armorSlot"]));
        $this->initPropertiesAndInitialData();
    }
}