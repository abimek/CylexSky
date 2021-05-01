<?php
declare(strict_types=1);

namespace cylexsky\custom\items\items;

use cylexsky\custom\items\items\traits\CustomComponentTrait;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ToolTier;

class Sword extends \pocketmine\item\Sword{

    use CustomComponentTrait;
    use ItemComponentsTrait;

    public function __construct(ItemIdentifier $identifier, string $name)
    {
        $toolTier = strtoupper(self::getParameters()[0]);
        parent::__construct($identifier, $name, call_user_func(ToolTier::class ."::" . $toolTier));
        $this->initPropertiesAndInitialData();
    }
}